<?php
namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\BaseController;
use AppBundle\Common\Paginator;
use Biz\Common\ArrayToolkit;

class ExpressController extends BaseController
{
    public function indexAction(Request $request)
    {
        $conditions = $request->query->all();
        $orderBy    = $this->getOrderBy($conditions, array('created_time' => 'DESC'));
        $expressCount  = $this->getExpressService()->searchExpressCount($conditions);
        $paginator  = new Paginator($request, $expressCount, parent::DEFAULT_PAGE_COUNT);

        $expresses = $this->getExpressService()->searchExpress(
            $conditions,
            $orderBy,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $userIds = ArrayToolkit::column($expresses, 'publisher_id');

        $users   = $this->getUserService()->findUsersByIds($userIds);
        $users   = ArrayToolkit::index($users, 'id');

        return $this->render('AppBundle:admin/express:index.html.twig', array(
            'expresses'     => $expresses,
            'expressCount'  => $expressCount,
            'users'         => $users,
            'paginator'     => $paginator
        ));
    }

    public function showAction($id)
    {
        $express   = $this->getExpressService()->getExpress($id);
        $publisher = $this->getUserService()->getUser($express['publisher_id']);
        $receiver  =  $this->getUserService()->getUser($express['receiver_id']);

        return $this->render('AppBundle:admin/express:show-modal.html.twig', array(
            'express'     => $express,
            'publisher'   => $publisher,
            'receiver'    => $receiver
        ));
    }

    public function ordersAction(Request $request)
    {
        $conditions = $request->query->all();
        $orderBy    = $this->getOrderBy($conditions, array('createdTime' => 'DESC'));
        $conditions['target_type'] = 'express';

        $ordersCount  = $this->getOrderService()->searchOrderCount($conditions);
        $paginator  = new Paginator($request, $ordersCount, parent::DEFAULT_PAGE_COUNT);

        $orders = $this->getOrderService()->searchOrder(
            $conditions,
            $orderBy,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $userIds = ArrayToolkit::column($orders, 'user_id');

        $users   = $this->getUserService()->findUsersByIds($userIds);
        $users   = ArrayToolkit::index($users, 'id');

        return $this->render('AppBundle:admin/express:orders.html.twig', array(
            'orders'        => $orders,
            'ordersCount'   => $ordersCount,
            'users'         => $users,
            'paginator'     => $paginator
        ));
    }

    public function showOrderAction($orderId)
    {
        $order = $this->getOrderService()->getOrder($orderId);
        $user = $this->getUserService()->getUser($order['user_id']);
        $orderLogs = $this->getOrderService()->findOrderLogsByorderId($orderId);

        return $this->render('AppBundle:admin/express:order-show-modal.html.twig', array(
            'order'   => $order,
            'user'     => $user,
            'orderLogs' => $orderLogs
        ));
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getExpressService()
    {
        return $this->biz->service('Express:ExpressService');
    }

    protected function getOrderService()
    {
        return $this->biz->service('Order:OrderService');
    }
}
