<?php

namespace Biz\Category\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Category\Dao\CategoryDao;

class CategoryDaoImpl extends GeneralDaoImpl implements CategoryDao
{
    protected $table = 'category';

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
