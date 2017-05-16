<?php

namespace Biz\User\Service;

interface UserApprovalService
{
    public function getUserApproval($userApprovalId);

    public function searchUserApproval($conditions, $orderBy, $start, $limit);

    public function searchUserApprovalCount($conditions);

    public function createUserApproval($fields);

    public function updateUserApproval($userApprovalId, $fields);

    public function deleteUserApproval($userApprovalId);
}
