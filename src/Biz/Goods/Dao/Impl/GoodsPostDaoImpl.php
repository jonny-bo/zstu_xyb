<?php

namespace Biz\Goods\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Goods\Dao\GoodsPostDao;

class GoodsPostDaoImpl extends GeneralDaoImpl implements GoodsPostDao
{
    protected $table = 'old_goods_post';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array(),
            'orderbys' => array('created_time'),
            'conditions' => array(
                'old_goods_id = :old_goods_id'
            ),
        );
    }
}
