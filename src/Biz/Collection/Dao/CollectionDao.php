<?php

namespace Biz\Collection\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface CollectionDao extends GeneralDaoInterface
{
    public function getCollectionByFields($fields);
}
