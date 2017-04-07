<?php

namespace Biz\File\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\File\Dao\FileDao;

class FileDaoImpl extends GeneralDaoImpl implements FileDao
{
    protected $table = 'file';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time'),
            'serializes' => array(),
            'orderbys' => array(),
            'conditions' => array(),
        );
    }

    public function deleteFileByUri($uri)
    {
        return $this->db()->delete($this->table, array('uri' => $uri));
    }
}
