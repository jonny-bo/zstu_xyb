<?php

namespace Biz\Express\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface ExpressApplyDao extends GeneralDaoInterface
{
    public function findByExpressId($expressId);

    public function getByExpressIdAndUserId($expressId, $userId);
}
