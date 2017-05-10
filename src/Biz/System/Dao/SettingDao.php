<?php

namespace Biz\System\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface SettingDao extends GeneralDaoInterface
{
    public function getByName($name);
}
