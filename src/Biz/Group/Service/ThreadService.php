<?php

namespace Biz\Group\Service;

interface ThreadService
{
    public function getThread($threadId);

    public function searchThreads($conditions, $orderBy, $start, $limit);

    public function searchThreadsCount($conditions);

    public function createThread($fields);

    public function updateThread($threadId, $fields);

    public function deleteThread($threadId);

    public function isCollected($userId, $threadId);

    public function threadCollect($userId, $threadId);
    
    public function unThreadCollect($userId, $threadId);

    public function findThreadsByIds($ids);

    public function closeThread($threadId);

    public function openThread($threadId);

    public function hitThread($threadId);

    public function postThread($groupId, $threadId, $fields);

    public function searchThreadPosts($conditions, $orderBy, $start, $limit);

    public function searchThreadPostsCount($conditions);
}
