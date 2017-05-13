<?php

namespace Biz\Express\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface ExpressReviewDao extends GeneralDaoInterface
{
    public function getByExpressAndUserId($expressId, $userId);
}
