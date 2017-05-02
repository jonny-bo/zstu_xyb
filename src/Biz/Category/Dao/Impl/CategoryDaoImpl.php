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

    public function findByGroupId($groupId)
    {
        return $this->findByFields(array('group_id' => $groupId));
    }

    public function getByCode($code)
    {
        return $this->getByFields(array('code' => $code));
    }
}
