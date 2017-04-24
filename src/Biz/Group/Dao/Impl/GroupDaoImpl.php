<?php

namespace Biz\Group\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Group\Dao\GroupDao;

class GroupDaoImpl extends GeneralDaoImpl implements GroupDao
{
    protected $table = 'groups';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array(),
            'orderbys' => array(
                'created_time',
                'member_num',
                'thread_num',
                'post_num'
            ),
            'conditions' => array(),
        );
    }

    public function findByUserId($userId)
    {
        return $this->findByFields(array('user_id' => $userId));
    }

    public function findByIds($ids)
    {
        return $this->findInField(array('id' => $ids));
    }
}
