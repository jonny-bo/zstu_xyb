<?php

namespace Biz\Groups\Service\Impl;

use Biz\Common\BaseService;
use Biz\Groups\Service\GroupsService;

class GroupsServiceImpl extends BaseService implements GroupsService
{
    public function getGroups($groupsId)
    {
        return $this->getGroupsDao()->get($groupsId);
    }

    public function searchGroupss($conditions, $orderBy, $start, $limit)
    {
        return $this->getGroupsDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchGroupssCount($conditions)
    {
        return $this->getGroupsDao()->count($conditions);
    }

    public function createGroups($fields)
    {
        return $this->getGroupsDao()->create($fields);
    }

    public function updateGroups($groupsId, $fields)
    {
        return $this->getGroupsDao()->update($groupsId, $fields);
    }

    public function deleteGroups($groupsId)
    {
        return $this->getGroupsDao()->delete($groupsId);
    }

    protected function getGroupsDao()
    {
        return $this->biz->dao('Groups:GroupsDao');
    }
}
