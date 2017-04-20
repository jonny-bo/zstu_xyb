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
}
