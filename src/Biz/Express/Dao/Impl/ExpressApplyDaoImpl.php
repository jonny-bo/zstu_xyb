<?php

namespace Biz\Express\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Express\Dao\ExpressApplyDao;

class ExpressApplyDaoImpl extends GeneralDaoImpl implements ExpressApplyDao
{
    protected $table = 'express_apply';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array(),
            'orderbys' => array('created_time'),
            'conditions' => array(),
        );
    }

    public function findByExpressId($expressId)
    {
        return $this->findByFields(array('express_id' => $expressId));
    }

    public function getByExpressIdAndUserId($expressId, $userId)
    {
        return $this->getByFields(array(
            'express_id' => $expressId,
            'user_id'    => $userId
        ));
    }
}
