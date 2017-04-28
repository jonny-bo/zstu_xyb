<?php

namespace Biz\User\Dao\Impl;

use Biz\User\Dao\UserDao;
use Codeages\Biz\Framework\Dao\GeneralDaoImpl;

class UserDaoImpl extends GeneralDaoImpl implements UserDao
{
    protected $table = 'user';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time', 'updated_time'),
            'serializes' => array('roles' => 'delimiter'),
            'orderbys' => array('created_time', 'updated_time'),
            'conditions' => array(
                'UPPER(nickname) LIKE :nickname',
                'UPPER(username) LIKE :username',
                'updated_time >= :updated_time_GE',
                'login_time >= :loginStartTime',
                'login_time <= :loginEndTime',
                'created_time >= :startTime',
                'created_time <= :endTime',
                'roles = :role',
                'roles LIKE :roles',
                'login_ip = :loginIp',
                'UPPER(email) LIKE :email',
            ),
        );
    }

    public function getByUsername($username)
    {
        return $this->getByFields(array('username' => $username));
    }

    public function getByEmail($email)
    {
        return $this->getByFields(array('email' => $email));
    }

    public function getByMobile($mobile)
    {
        return $this->getByFields(array('mobile' => $mobile));
    }

    public function findByIds($ids)
    {
        return $this->findInField('id', $ids);
    }
}
