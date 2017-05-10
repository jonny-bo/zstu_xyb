<?php

namespace Biz\System\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\System\Dao\SettingDao;

class SettingDaoImpl extends GeneralDaoImpl implements SettingDao
{
    protected $table = 'settings';

    public function declares()
    {
        return array(
            'timestamps' => array(),
            'serializes' => array(),
            'orderbys' => array(),
            'conditions' => array(),
        );
    }

    public function getByName($name)
    {
        return $this->getByFields(array(
            'name' => $name
        ));
    }
}
