<?php

namespace Biz\Category\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface CategoryDao extends GeneralDaoInterface
{
    public function findByGroupId($groupId);

    public function getByCode($code);
}
