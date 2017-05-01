<?php

namespace Biz\System\Dao\Impl;

use Biz\System\Dao\LogDao;
use Codeages\Biz\Framework\Dao\GeneralDaoImpl;

class LogDaoImpl extends GeneralDaoImpl implements LogDao
{
    protected $table = 'log';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array('roles' => 'delimiter'),
            'orderbys'   => array('created_time'),
            'conditions' => array(
                'module        =    :module',
                'module_id     =    :module_id',
                'username      LIKE :username',
                'level         =    :level',
                'created_time  >=   :start_time',
                'created_time  <=   :end_time'
            ),
        );
    }
}
