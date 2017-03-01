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
                'status = :status'
            ),
        );
    }
}
