<?php

namespace Biz\Category\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface CategoryGroupDao extends GeneralDaoInterface
{
    public function getByCode($code);

    public function findAllGroups();
}
