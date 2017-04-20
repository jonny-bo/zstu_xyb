<?php

namespace Biz\Group\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Group\Dao\ThreadCollectDao;

class ThreadCollectDaoImpl extends GeneralDaoImpl implements ThreadCollectDao
{
    protected $table = 'groups_thread_collect';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array(),
            'orderbys' => array(),
            'conditions' => array(),
        );
    }
}
