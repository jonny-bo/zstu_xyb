<?php

namespace Biz\Group\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface GroupDao extends GeneralDaoInterface
{
    public function findByUserId($userId);

    public function findByIds($ids);

    public function getByTitle($title);
}
