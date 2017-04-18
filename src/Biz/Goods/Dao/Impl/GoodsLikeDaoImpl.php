<?php

namespace Biz\Goods\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Goods\Dao\GoodsLikeDao;

class GoodsLikeDaoImpl extends GeneralDaoImpl implements GoodsLikeDao
{
    protected $table = 'old_goods_like';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array(),
            'orderbys' => array(),
            'conditions' => array(),
        );
    }

    public function getByUserIdAndGoodsId($userId, $goodsId)
    {
        return $this->getByFields(array(
            'old_goods_id' => $goodsId,
            'user_id'      => $userId
        ));
    }
}
