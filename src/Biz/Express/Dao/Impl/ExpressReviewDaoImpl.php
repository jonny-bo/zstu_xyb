<?php

namespace Biz\Express\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Express\Dao\ExpressReviewDao;

class ExpressReviewDaoImpl extends GeneralDaoImpl implements ExpressReviewDao
{
    protected $table = 'express_review';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time', 'updated_time'),
            'serializes' => array('meta'),
            'orderbys' => array(),
            'conditions' => array(),
        );
    }

    public function getByExpressAndUserId($expressId, $userId)
    {
        return $this->getByFields(array(
            'express_id' => $expressId,
            'user_id'    => $userId,
            'parent_id'  => 0
        ));
    }
}
