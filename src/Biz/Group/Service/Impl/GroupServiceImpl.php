<?php

namespace Biz\Group\Service\Impl;

use Biz\Common\BaseService;
use Biz\Group\Service\GroupService;

class GroupServiceImpl extends BaseService implements GroupService
{
    public function getGroup($groupId)
    {
        return $this->getGroupDao()->get($groupId);
    }

    public function searchGroups($conditions, $orderBy, $start, $limit)
    {
        return $this->getGroupDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchGroupsCount($conditions)
    {
        return $this->getGroupDao()->count($conditions);
    }

    public function createGroup($fields)
    {
        return $this->getGroupDao()->create($fields);
    }

    public function updateGroup($groupId, $fields)
    {
        return $this->getGroupDao()->update($groupId, $fields);
    }

    public function deleteGroup($groupId)
    {
        return $this->getGroupDao()->delete($groupId);
    }

    protected function getGroupDao()
    {
        return $this->biz->dao('Group:GroupDao');
    }
}
