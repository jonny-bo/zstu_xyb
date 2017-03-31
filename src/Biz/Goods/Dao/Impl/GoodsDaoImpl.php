<?php

namespace Biz\Goods\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Goods\Dao\GoodsDao;

class GoodsDaoImpl extends GeneralDaoImpl implements GoodsDao
{
    protected $table = 'old_goods';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time', 'updated_time'),
            'serializes' => array(),
            'orderbys' => array('created_time', 'updated_time', 'post_num', 'hits'),
            'conditions' => array(
                'id = :id',
                'category_id = :category_id',
                'updated_time >= :start_time',
                'updated_time <= :end_time',
                'title LIKE :title'
            ),
        );
    }
}
