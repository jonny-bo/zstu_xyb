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
            'serializes' => array(),
            'orderbys' => array(),
            'conditions' => array(),
        );
    }
}
