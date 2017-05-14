<?php

namespace Biz\Express\Service\Impl;

use Biz\Common\BaseService;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\ArrayToolkit;
use Biz\Express\Service\ExpressService;
use Biz\User\Event\UserEvents;

class ExpressServiceImpl extends BaseService implements ExpressService
{
    protected $requiredFields = array(
        'title', 'detail', 'offer'
    );

    public function getExpress($expressId)
    {
        return $this->getExpressDao()->get($expressId);
    }

    public function searchExpress($conditions, $orderBy, $start, $limit)
    {
        $conditions = $this->perConditions($conditions);

        return $this->getExpressDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchExpressCount($conditions)
    {
        $conditions = $this->perConditions($conditions);

        return $this->getExpressDao()->count($conditions);
    }

    public function createExpress($fields)
    {
        if (!ArrayToolkit::requireds($fields, $this->requiredFields)) {
            throw new InvalidArgumentException('缺少必要参数');
        }

        $user = $this->getCurrentUser();

        if (empty($user->id)) {
            throw new AccessDeniedException('未登录用户，无权操作！');
        }


        $fields['publisher_id'] = $user['id'];
        $fields['title']        = $this->purifyHtml($fields['title']);
        $fields['detail']       = $this->purifyHtml($fields['detail']);
        $fields['status']       = 1;

        try {
            $this->getExpressDao()->db()->beginTransaction();
            $express = $this->getExpressDao()->create($fields);

            $this->getOrderService()->createOrder(array(
                'user_id' => $user['id'],
                'target_type' => 'express',
                'target_id' => $express['id'],
                'title' => "发布快递代领{$express['title']}(#{$express['id']})",
                'payment' => 'coin',
                'coin_amount' => $express['offer']
            ));

            $this->getExpressDao()->db()->commit();
        } catch (\Exception $e) {
            $this->getExpressDao()->db()->rollBack();
            throw $e;
        }

        return $express;
    }

    public function orderExpress($expressId, $userId)
    {
        $express = $this->checkExpress($expressId);

        if ($express['publisher_id'] == $userId) {
            throw new UnexpectedValueException('自己不能接自己的订单');
        }

        $lockName = "express_{$expressId}";
        $lock     = $this->getLock();
        $lock->get($lockName, 10);

        $express = $this->updateExpress($expressId, array('status' => 2, 'receiver_id' => $userId));

        $lock->release($lockName);

        return $express;
    }

    public function updateExpress($expressId, $fields)
    {
        $this->checkExpress($expressId);
        if (isset($fields['title'])) {
            $fields['title']  = $this->purifyHtml($fields['title']);
        }

        if (isset($fields['detail'])) {
            $fields['detail'] = $this->purifyHtml($fields['detail']);
        }

        return $this->getExpressDao()->update($expressId, $fields);
    }

    public function deleteExpress($expressId)
    {
        $this->checkExpress($expressId);

        return $this->getExpressDao()->delete($expressId);
    }

    public function confirmMyPublishExpress($expressId)
    {
        $user    = $this->getCurrentUser();
        $express = $this->getExpress($expressId);

        if ($user['id'] != $express['publisher_id']) {
            throw new UnexpectedValueException('不是发布者，不能确认收货');
        }

        if ($express['status'] != 3) {
            throw new UnexpectedValueException('该订单未送达，不能确认收货');
        }

        try {
            $this->getExpressDao()->db()->beginTransaction();
            $order = $this->getOrderService()->getOrderByTargetTypeAndTargetIdAndUserId('express', $expressId, $user['id']);
            $this->getOrderService()->payOrder(array(
                'sn' => $order['sn'],
                'status' => false,
                'coin_amount' => $express['offer'],
                'to_user_id' => $express['receiver_id'],
                'paid_time' => time()
            ));
            $express = $this->getExpressDao()->update($expressId, array('status' => 4));
            $this->getExpressDao()->db()->commit();
        } catch (Exception $e) {
            $this->getExpressDao()->db()->rollBack();
            throw $e;
        }

        return $express;
    }

    public function confirmMyReceiveExpress($expressId)
    {
        $user    = $this->getCurrentUser();
        $express = $this->getExpress($expressId);

        if ($user['id'] != $express['receiver_id']) {
            throw new UnexpectedValueException('不是订单接收人，不能确认送到');
        }

        if ($express['status'] != 2) {
            throw new UnexpectedValueException('该订单不能确认送到');
        }

        return $this->getExpressDao()->update($expressId, array('status' => 3));
    }

    public function reviewExpress($expressId, $rating, $content)
    {
        $express = $this->getExpress($expressId);
        if (empty($express) || !floatval($rating) || !$content) {
            throw new InvalidArgumentException('订单不存在或参数错误！');
        }
        if ($express['status'] != 4) {
            throw new UnexpectedValueException('该订单未确认，不能评价！');
        }

        $user = $this->getCurrentUser();

        $review = $this->getExpressReviewDao()->getByExpressAndUserId($expressId, $user['id']);

        if ($review) {
            throw new UnexpectedValueException('该订单您已经评价过了！');
        }

        $review['express_id'] = $expressId;
        $review['rating']     = $rating;
        $review['content']    = $content;
        $review['user_id']    = $user['id'];

        $review = $this->getExpressReviewDao()->create($review);
        //触发信用度变化事件，订阅者模式
        $this->dispatchEvent(UserEvents::CREDIT, $express['receiver_id'], array(
            'rating' => $rating,
            'message' => "完成快递订单，获得{$rating}星级评价！"
        ));

        return $review;
    }

    protected function perConditions($conditions)
    {
        $conditions =  array_filter($conditions);

        if (isset($conditions['keywordType']) && isset($conditions['keyword'])) {
            if ($conditions['keywordType'] == 'nickname') {
                $users = $this->getUserService()->searchUsers(array('nickname' => $conditions['keyword']), array(), 0, PHP_INT_MAX);
                $conditions['publishIds'] = empty($users) ? array(0) : ArrayToolkit::column($users, 'id');
            } else {
                $conditions[$conditions['keywordType']] = "%{$conditions['keyword']}%";
            }

            unset($conditions['keywordType']);
            unset($conditions['keyword']);
        }

        if (isset($conditions['startDate'])) {
            $conditions['startTime'] = strtotime($conditions['startDate']);
        }

        if (isset($conditions['endDate'])) {
            $conditions['endTime'] = strtotime($conditions['endDate']);
        }

        return $conditions;
    }

    protected function checkExpress($expressId)
    {
        $express = $this->getExpress($expressId);

        if (!$express) {
            throw new ResourceNotFoundException('订单', $expressId);
        }

        if ($express['status'] != 1) {
            throw new UnexpectedValueException('该订单已经被别人接收');
        }

        return $express;
    }

    protected function getExpressDao()
    {
        return $this->biz->dao('Express:ExpressDao');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getExpressReviewDao()
    {
        return $this->biz->dao('Express:ExpressReviewDao');
    }

    protected function getOrderService()
    {
        return $this->biz->service('Order:OrderService');
    }
}
