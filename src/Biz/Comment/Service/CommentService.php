<?php

namespace Biz\Comment\Service;

interface CommentService
{
    public function getComment($commentId);

    public function searchComments($conditions, $orderBy, $start, $limit);

    public function searchCommentsCount($conditions);

    public function createComment($fields);

    public function updateComment($commentId, $fields);

    public function deleteComment($commentId);
}
