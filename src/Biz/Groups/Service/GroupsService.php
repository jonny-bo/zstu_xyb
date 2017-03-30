<?php

namespace Biz\Groups\Service;

interface GroupsService
{
    public function getGroups($groupsId);

    public function searchGroupss($conditions, $orderBy, $start, $limit);

    public function searchGroupssCount($conditions);

    public function createGroups($fields);

    public function updateGroups($groupsId, $fields);

    public function deleteGroups($groupsId);
}
