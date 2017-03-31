<?php

namespace Biz\Category\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Category\Dao\CategoryGroupDao;

class CategoryGroupDaoImpl extends GeneralDaoImpl implements CategoryGroupDao
{
    protected $table = 'category_group';

    public function declares()
    {
        return array(
            'timestamps' => array(),
            'serializes' => array(),
            'orderbys' => array(),
            'conditions' => array(),
        );
    }

    public function getByCode($code)
    {
        return $this->getByFields(array('code' => $code));
    }
}
