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

    public function closeGroup($groupId);

    public function openGroup($groupId);

    public function changeGroupImg($groupId, $field, $data);

    public function joinGroup($groupId, $userId);

    public function exitGroup($groupId, $userId);

    public function findGroupsByUserId($userId);

    public function searchMembers($conditions, $orderBy, $start, $limit);

    public function searchMembersCount($conditions);

    public function isOwner($groupId, $userId);

    public function isAdmin($groupId, $userId);

    public function isMember($groupId, $userId);

    public function updateMember($memberId, $fields);

    public function getMemberByGroupIdAndUserId($groupId, $userId);

    public function deleteMemberByGroupIdAndUserId($groupId, $userId);

    public function waveGroup(array $ids, array $diffs);

    public function waveMemberByGroupIdAndUserId($groupId, $userId, array $diffs);
}
