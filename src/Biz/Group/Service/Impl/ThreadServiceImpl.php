<?php

namespace Biz\Group\Service\Impl;

use Biz\Common\BaseService;
use Biz\Group\Service\ThreadService;

class ThreadServiceImpl extends BaseService implements ThreadService
{
    public function getThread($threadId)
    {
        return $this->getThreadDao()->get($threadId);
    }

    public function searchThreads($conditions, $orderBy, $start, $limit)
    {
        return $this->getThreadDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchThreadsCount($conditions)
    {
        return $this->getThreadDao()->count($conditions);
    }

    public function createThread($fields)
    {
        return $this->getThreadDao()->create($fields);
    }

    public function updateThread($threadId, $fields)
    {
        return $this->getThreadDao()->update($threadId, $fields);
    }

    public function deleteThread($threadId)
    {
        return $this->getThreadDao()->delete($threadId);
    }

    protected function getThreadDao()
    {
        return $this->biz->dao('Group:ThreadDao');
    }
}
