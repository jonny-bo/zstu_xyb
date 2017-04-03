<?php

namespace Biz\Comment\Service\Impl;

use Biz\Common\BaseService;
use Biz\Comment\Service\CommentService;

class CommentServiceImpl extends BaseService implements CommentService
{
    public function getComment($commentId)
    {
        return $this->getCommentDao()->get($commentId);
    }

    public function searchComments($conditions, $orderBy, $start, $limit)
    {
        return $this->getCommentDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchCommentsCount($conditions)
    {
        return $this->getCommentDao()->count($conditions);
    }

    public function createComment($fields)
    {
        return $this->getCommentDao()->create($fields);
    }

    public function updateComment($commentId, $fields)
    {
        return $this->getCommentDao()->update($commentId, $fields);
    }

    public function deleteComment($commentId)
    {
        return $this->getCommentDao()->delete($commentId);
    }

    protected function getCommentDao()
    {
        return $this->biz->dao('Comment:CommentDao');
    }
}
