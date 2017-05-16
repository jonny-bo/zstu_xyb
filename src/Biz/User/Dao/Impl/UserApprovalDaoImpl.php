<?php

namespace Biz\User\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\User\Dao\UserApprovalDao;

class UserApprovalDaoImpl extends GeneralDaoImpl implements UserApprovalDao
{
    protected $table = 'user_approval';

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
