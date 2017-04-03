<?php

namespace Biz\Comment\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\Comment\Dao\CommentDao;

class CommentDaoImpl extends GeneralDaoImpl implements CommentDao
{
    protected $table = 'comment';

    public function declares()
    {
        return array(
            'timestamps' => array(),
            'serializes' => array(),
            'orderbys' => array(),
            'conditions' => array(),
        );
    }
}
