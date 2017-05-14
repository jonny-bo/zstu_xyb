<?php

namespace Biz\User\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\User\Dao\UserBillDao;

class UserBillDaoImpl extends GeneralDaoImpl implements UserBillDao
{
    protected $table = 'user_bill';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array(),
            'orderbys' => array(),
            'conditions' => array(),
        );
    }
}
