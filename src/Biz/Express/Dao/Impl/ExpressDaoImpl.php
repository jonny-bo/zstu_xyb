<?php

namespace Biz\Express\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Express\Dao\ExpressDao;

class ExpressDaoImpl extends GeneralDaoImpl implements ExpressDao
{
    protected $table = 'express';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array(),
            'orderbys' => array('created_time'),
            'conditions' => array(
                'type         = :type',
                'status       = :status',
                'publisher_id = :publisher_id',
                'receiver_id  = :receiver_id',
                'publisher_id IN (:publishIds)',
                'created_time >= :startTime',
                'created_time <= :endTime',
                'title LIKE :title',
            ),
        );
    }
}
