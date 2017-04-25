<?php

namespace Biz\Group\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Group\Dao\ThreadPostDao;

class ThreadPostDaoImpl extends GeneralDaoImpl implements ThreadPostDao
{
    protected $table = 'groups_thread_post';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array(),
            'orderbys' => array('created_time', 'updated_time'),
            'conditions' => array(
                'id = :id',
                'thread_id = :thread_id',
                'post_id = :post_id'
            ),
        );
    }
}
