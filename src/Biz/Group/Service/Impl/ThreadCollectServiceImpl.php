<?php

namespace Biz\Group\Service\Impl;

use Biz\Common\BaseService;
use Biz\Group\Service\ThreadCollectService;

class ThreadCollectServiceImpl extends BaseService implements ThreadCollectService
{
    public function getThreadCollect($threadCollectId)
    {
        return $this->getThreadCollectDao()->get($threadCollectId);
    }

    public function searchThreadCollects($conditions, $orderBy, $start, $limit)
    {
        return $this->getThreadCollectDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchThreadCollectsCount($conditions)
    {
        return $this->getThreadCollectDao()->count($conditions);
    }

    public function createThreadCollect($fields)
    {
        return $this->getThreadCollectDao()->create($fields);
    }

    public function updateThreadCollect($threadCollectId, $fields)
    {
        return $this->getThreadCollectDao()->update($threadCollectId, $fields);
    }

    public function deleteThreadCollect($threadCollectId)
    {
        return $this->getThreadCollectDao()->delete($threadCollectId);
    }

    protected function getThreadCollectDao()
    {
        return $this->biz->dao('Group:ThreadCollectDao');
    }
}
