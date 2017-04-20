<?php

namespace Biz\Group\Service;

interface GroupService
{
    public function getGroup($groupId);

    public function searchGroups($conditions, $orderBy, $start, $limit);

    public function searchGroupsCount($conditions);

    public function createGroup($fields);

    public function updateGroup($groupId, $fields);

    public function deleteGroup($groupId);
}
