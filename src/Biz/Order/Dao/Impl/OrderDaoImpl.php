<?php

namespace Biz\Order\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Order\Dao\OrderDao;

class OrderDaoImpl extends GeneralDaoImpl implements OrderDao
{
    protected $table = 'orders';

    public function declares()
    {
        return array(
            'timestamps' => array('createdTime', 'updatedTime'),
            'serializes' => array(),
            'orderbys' => array(),
            'conditions' => array(),
        );
    }

    public function getBySn($orderSn)
    {
        return $this->getByFields(array(
            'sn' => $orderSn
        ));
    }

    public function getByTargetTypeAndTargetIdAndUserId($targetType, $targetId, $userId)
    {
        return $this->getByFields(array(
            'target_type' => $targetType,
            'target_id' => $targetId,
            'user_id' => $userId
        ));
    }
}
