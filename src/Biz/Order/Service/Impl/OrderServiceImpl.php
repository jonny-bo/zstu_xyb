<?php

namespace Biz\Order\Service\Impl;

use Biz\Common\BaseService;
use Biz\Order\Service\OrderService;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\ArrayToolkit;
use Biz\Order\Event\OrderEvents;
use Biz\User\Event\UserEvents;

class OrderServiceImpl extends BaseService implements OrderService
{
    public function getOrder($orderId)
    {
        return $this->getOrderDao()->get($orderId);
    }

    public function searchOrder($conditions, $orderBy, $start, $limit)
    {
        return $this->getOrderDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchOrderCount($conditions)
    {
        return $this->getOrderDao()->count($conditions);
    }

    public function createOrder($fields)
    {
        if (!ArrayToolkit::requireds($fields, array('user_id', 'title', 'target_type', 'target_id', 'payment'))) {
            throw new InvalidArgumentException('创建订单失败：缺少参数！');
        }
        $fields['amount'] = isset($fields['amount']) ? $fields['amount'] : 0.00;
        $fields['coin_amount'] = isset($fields['coin_amount']) ? $fields['coin_amount'] : 0;
        $order = ArrayToolkit::parts($fields, array(
            'user_id',
            'title',
            'amount',
            'target_type',
            'target_id',
            'payment',
            'note',
            'coin_amount',
            'coin_rate',
            'price_type',
            'total_price',
            'sn_prefix'
        ));

        try {
            $this->getOrderDao()->db()->beginTransaction();

            $orderUser = $this->getUserService()->getUser($order['user_id']);

            if (empty($orderUser)) {
                throw new ResourceNotFoundException('user', $order['id'], '订单用户不存在，不能创建订单。');
            }

            $payment = array (
                'none' => '--',
                'alipay' => '支付宝',
                'wxpay' => '微信支付',
                'coin' => '流币',
                'outside' => '线下支付',
            );
            $payment = array_keys($payment);

            if (!in_array($order['payment'], $payment)) {
                throw new InvalidArgumentException('创建订单失败：支付方式值不正确。');
            }

            if ($order['payment'] == 'coin') {
                if ($orderUser['coin'] < $order['coin_amount']) {
                    throw new RuntimeException('创建订单失败，虚拟币余额不足！');
                }

                $this->getUserService()->updateCoin($orderUser['id'], -$order['coin_amount']);
                $this->dispatchEvent(UserEvents::COIN, $orderUser['id'], array(
                    'coin' => -$order['coin_amount'],
                    'message' => "流币出帐"
                ));

            }

            $order['sn'] = $this->generateOrderSn($order);
            unset($order['sn_prefix']);

            $order['amount'] = number_format($order['amount'], 2, '.', '');

            $order['status']      = 'created';
            $order['token'] = $this->makeToken($order['sn']);

            $order = $this->getOrderDao()->create($order);

            $this->createOrderLog($order['id'], 'created', '创建订单');

            $this->getOrderDao()->db()->commit();
        } catch (Exception $e) {
            $this->getOrderDao()->db()->rollBack();
            throw $e;
        }

        return $order;
    }

    public function payOrder($payData)
    {
        $success = false;
        try {
            $this->getOrderDao()->db()->beginTransaction();
            $order   = $this->getOrderDao()->getBySn($payData['sn']);

            if (empty($order)) {
                throw new ResourceNotFoundException('order', $order['id'], "订单({$payData['sn']})已被删除，支付失败。");
                $this->getUserService()->updateCoin($payData['to_user_id'], $order['coin_amount']);
            }

            if ($payData['status'] == 'success' || $order['payment'] == 'coin') {
                if ($order['payment'] == 'coin') {
                    if ($payData['coin_amount'] != $order['coin_amount']) {
                        $message = "订单{$order['sn']}流币支付额度{$payData['coin_amount']}与实际支付的流币额度{$order['coin_amount']} 不符，支付失败！";
                        $this->createOrderLog($order['id'], 'pay_error', $message, $payData);
                        throw new RuntimeException($message);
                    }
                    $this->getUserService()->updateCoin($payData['to_user_id'], $order['coin_amount']);
                    $this->dispatchEvent(UserEvents::COIN, $payData['to_user_id'], array(
                        'coin' => $order['coin_amount'],
                        'message' => "流币到帐"
                    ));
                } else if (intval($payData['amount'] * 100) !== intval($order['amount'] * 100)) {
                    $message = "订单{$order['sn']}支付额度{$payData['amount']}与实际支付的额度{$order['amount']} 不符，支付失败！";
                    $this->createOrderLog($order['id'], 'pay_error', $message, $payData);
                    throw new RuntimeException($message);
                }

                if ($this->canOrderPay($order)) {
                    $payFields = array(
                        'status'   => 'paid',
                        'paid_time' => $payData['paid_time']
                    );

                    !empty($payData['payment']) ? $payFields['payment'] = $payData['payment'] : '';

                    $this->getOrderDao()->update($order['id'], $payFields);
                    $this->createOrderLog($order['id'], 'pay_success', '付款成功', $payData);
                    $success = true;
                } else {
                    $this->createOrderLog($order['id'], 'pay_ignore', '订单已处理', $payData);
                }
            } else {
                $this->createOrderLog($order['id'], 'pay_unknown', '', $payData);
            }

            $this->getOrderDao()->db()->commit();
        } catch (\Exception $e) {
            $this->getOrderDao()->db()->rollBack();
            throw $e;
        }

        $order = $this->getOrder($order['id']);

        // if ($success) {
        //     $this->getDispatcher()->dispatch('order.service.paid', new ServiceEvent($order));
        // }

        return array($success, $order);
    }

    public function findOrderLogsByorderId($orderId)
    {
        return $this->getOrderLogDao()->findByOrderId($orderId);
    }

    public function getOrderByTargetTypeAndTargetIdAndUserId($targetType, $targetId, $userId)
    {
        return $this->getOrderDao()->getByTargetTypeAndTargetIdAndUserId($targetType, $targetId, $userId);
    }

    public function updateOrder($orderId, $fields)
    {
        return $this->getOrderDao()->update($orderId, $fields);
    }

    public function deleteOrder($orderId)
    {
        return $this->getOrderDao()->delete($orderId);
    }

    private function makeToken($sn)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $value = '';
        for ($i = 0; $i < 5; $i++) {
            $value .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $sn.$value;
    }

    public function canOrderPay($order)
    {
        if (empty($order['status'])) {
            throw new InvalidArgumentException('订单状态异常！');
        }

        return in_array($order['status'], array('created', 'cancelled'));
    }

    protected function generateOrderSn($order)
    {
        $prefix = empty($order['sn_prefix']) ? 'E' : (string)$order['sn_prefix'];
        return $prefix.date('YmdHis', time()).mt_rand(10000, 99999);
    }

    protected function createOrderLog($orderId, $type, $message = '', array $data = array())
    {
        $user = $this->getCurrentUser();

        $log = array(
            'order_id'     => $orderId,
            'type'        => $type,
            'message'     => $message,
            'data'        => $data,
            'user_id'      => $user->id,
            'ip'          => $user->login_ip,
        );

        return $this->getOrderLogDao()->create($log);
    }

    protected function getOrderDao()
    {
        return $this->biz->dao('Order:OrderDao');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getSettingService()
    {
        return $this->biz->service('System:SettingService');
    }

    protected function getOrderLogDao()
    {
        return $this->biz->dao('Order:OrderLogDao');
    }
}
