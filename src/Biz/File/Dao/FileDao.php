<?php

namespace Biz\File\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface FileDao extends GeneralDaoInterface
{
    public function deleteFileByUri($uri);
}
