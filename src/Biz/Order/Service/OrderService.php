<?php

namespace Biz\Order\Service;

interface OrderService
{
    public function getOrder($orderId);

    public function searchOrder($conditions, $orderBy, $start, $limit);

    public function searchOrderCount($conditions);

    public function createOrder($fields);

    public function updateOrder($orderId, $fields);

    public function deleteOrder($orderId);

    public function payOrder($payData);

    public function getOrderByTargetTypeAndTargetIdAndUserId($targetType, $targetId, $userId);
}
