<?php

namespace Biz\Group\Service\Impl;

use Biz\Common\BaseService;
use Biz\Group\Service\ThreadPostService;

class ThreadPostServiceImpl extends BaseService implements ThreadPostService
{
    public function getThreadPost($threadPostId)
    {
        return $this->getThreadPostDao()->get($threadPostId);
    }

    public function searchThreadPosts($conditions, $orderBy, $start, $limit)
    {
        return $this->getThreadPostDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchThreadPostsCount($conditions)
    {
        return $this->getThreadPostDao()->count($conditions);
    }

    public function createThreadPost($fields)
    {
        return $this->getThreadPostDao()->create($fields);
    }

    public function updateThreadPost($threadPostId, $fields)
    {
        return $this->getThreadPostDao()->update($threadPostId, $fields);
    }

    public function deleteThreadPost($threadPostId)
    {
        return $this->getThreadPostDao()->delete($threadPostId);
    }

    protected function getThreadPostDao()
    {
        return $this->biz->dao('Group:ThreadPostDao');
    }
}
