<?php

namespace Biz\Collection\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Collection\Dao\CollectionDao;

class CollectionDaoImpl extends GeneralDaoImpl implements CollectionDao
{
    protected $table = 'collection';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array(),
            'orderbys' => array('created_time'),
            'conditions' => array(
                'object_type = :object_type'
            ),
        );
    }
}
