<?php

namespace Biz\Order\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Order\Dao\OrderLogDao;

class OrderLogDaoImpl extends GeneralDaoImpl implements OrderLogDao
{
    protected $table = 'order_log';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array('data' => 'json'),
            'orderbys' => array(),
            'conditions' => array(),
        );
    }

    public function findByOrderId($orderId)
    {
        return $this->findByFields(array(
            'order_id' => $orderId
        ));
    }
}
