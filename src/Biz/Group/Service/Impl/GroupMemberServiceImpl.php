<?php

namespace Biz\Group\Service\Impl;

use Biz\Common\BaseService;
use Biz\Group\Service\GroupMemberService;

class GroupMemberServiceImpl extends BaseService implements GroupMemberService
{
    public function getGroupMember($groupMemberId)
    {
        return $this->getGroupMemberDao()->get($groupMemberId);
    }

    public function searchGroupMembers($conditions, $orderBy, $start, $limit)
    {
        return $this->getGroupMemberDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchGroupMembersCount($conditions)
    {
        return $this->getGroupMemberDao()->count($conditions);
    }

    public function createGroupMember($fields)
    {
        return $this->getGroupMemberDao()->create($fields);
    }

    public function updateGroupMember($groupMemberId, $fields)
    {
        return $this->getGroupMemberDao()->update($groupMemberId, $fields);
    }

    public function deleteGroupMember($groupMemberId)
    {
        return $this->getGroupMemberDao()->delete($groupMemberId);
    }

    protected function getGroupMemberDao()
    {
        return $this->biz->dao('Group:GroupMemberDao');
    }
}
