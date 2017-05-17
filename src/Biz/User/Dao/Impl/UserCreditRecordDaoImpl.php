<?php

namespace Biz\User\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\User\Dao\UserCreditRecordDao;

class UserCreditRecordDaoImpl extends GeneralDaoImpl implements UserCreditRecordDao
{
    protected $table = 'user_credit_record';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array(),
            'orderbys' => array(),
            'conditions' => array(),
        );
    }

    public function findByUserId($userId)
    {
        return $this->findByFields(array(
            'user_id' => $userId
        ));
    }
}
