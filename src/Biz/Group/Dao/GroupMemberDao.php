<?php

namespace Biz\Group\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface GroupMemberDao extends GeneralDaoInterface
{
    public function getByGroupIdAndUserId($groupId, $userId);

    public function findByUserId($userId);
}
