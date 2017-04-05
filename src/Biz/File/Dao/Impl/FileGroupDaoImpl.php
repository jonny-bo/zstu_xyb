<?php

namespace Biz\File\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\File\Dao\FileGroupDao;

class FileGroupDaoImpl extends GeneralDaoImpl implements FileGroupDao
{
    protected $table = 'file_group';

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
