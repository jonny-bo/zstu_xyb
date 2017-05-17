<?php

namespace Biz\User\Service\Impl;

use Biz\Common\BaseService;
use Biz\User\Service\UserApprovalService;

class UserApprovalServiceImpl extends BaseService implements UserApprovalService
{
    public function getUserApproval($userApprovalId)
    {
        return $this->getUserApprovalDao()->get($userApprovalId);
    }

    public function searchUserApproval($conditions, $orderBy, $start, $limit)
    {
        return $this->getUserApprovalDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchUserApprovalCount($conditions)
    {
        return $this->getUserApprovalDao()->count($conditions);
    }

    public function createUserApproval($fields)
    {
        return $this->getUserApprovalDao()->create($fields);
    }

    public function updateUserApproval($userApprovalId, $fields)
    {
        return $this->getUserApprovalDao()->update($userApprovalId, $fields);
    }

    public function deleteUserApproval($userApprovalId)
    {
        return $this->getUserApprovalDao()->delete($userApprovalId);
    }

    public function getUserApprovalByUserId($userId)
    {
        return $this->getUserApprovalDao()->getByUserId($userId);
    }

    protected function getUserApprovalDao()
    {
        return $this->biz->dao('User:UserApprovalDao');
    }
}
