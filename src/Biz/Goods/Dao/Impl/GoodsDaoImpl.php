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
            'serializes' => array('imgs' => 'json'),
            'orderbys' => array('created_time', 'updated_time', 'post_num', 'hits', 'price'),
            'conditions' => array(
                'id = :id',
                'category_id = :category_id',
                'status = :status',
                'publisher_id = :publisher_id',
                'created_time >= :startTime',
                'created_time <= :endTime',
                'title LIKE :title',
                'status = :status',
                'publish_id IN (publishIds)',
            ),
        );
    }
}
