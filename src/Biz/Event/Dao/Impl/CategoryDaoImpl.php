<?php

namespace Biz\Event\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Event\Dao\CategoryDao;

class CategoryDaoImpl extends GeneralDaoImpl implements CategoryDao
{
    protected $table = '';

    public function declares()
    {
        return array(
            'timestamps' => array(),
            'serializes' => array(),
            'orderbys' => array(),
            'conditions' => array(),
        );
    }
}
