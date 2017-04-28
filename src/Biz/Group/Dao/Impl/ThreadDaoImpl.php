<?php

namespace Biz\Group\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Group\Dao\ThreadDao;

class ThreadDaoImpl extends GeneralDaoImpl implements ThreadDao
{
    protected $table = 'groups_thread';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time', 'updated_time'),
            'serializes' => array('imgs' => 'json'),
            'orderbys' => array('created_time', 'updated_time', 'post_num', 'hit_num'),
            'conditions' => array(
                'group_id = :group_id',
                'title LIKE :title',
                'status = :status',
                'is_stick = :isStick',
                'is_elite = :isElite',
                'group_id IN (:groupIds)',
            ),
        );
    }

    public function findByIds($ids)
    {
        return $this->findInField(array(
            'id' => $ids
        ));
    }
}
