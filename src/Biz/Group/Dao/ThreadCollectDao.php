<?php

namespace Biz\Group\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface ThreadCollectDao extends GeneralDaoInterface
{
    public function getByUserIdAndThreadId($userId, $threadId);
}
