<?php

namespace Biz\File\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface FileGroupDao extends GeneralDaoInterface
{
    public function getByCode($code);
}
