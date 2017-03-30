<?php

namespace Biz\Groups\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Groups\Dao\GroupsDao;

class GroupsDaoImpl extends GeneralDaoImpl implements GroupsDao
{
    protected $table = 'groups';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array(),
            'orderbys' => array('created_time', 'member_num', 'thread_num', 'post_num'),
            'conditions' => array(),
        );
    }
}
