<?php

namespace Biz\User\Dao\Impl;

use Biz\User\Dao\UserTokenDao;
use Codeages\Biz\Framework\Dao\GeneralDaoImpl;

class UserTokenDaoImpl extends GeneralDaoImpl implements UserTokenDao
{
    protected $table = 'user_token';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array(),
            'orderbys' => array(),
            'conditions' => array(
            ),
        );
    }

    public function getByToken($token)
    {
        return $this->getByFields(array('token' => $token));
    }
}
