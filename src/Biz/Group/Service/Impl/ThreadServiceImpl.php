<?php

namespace Biz\Group\Service\Impl;

use Biz\Common\BaseService;
use Biz\Group\Service\ThreadService;
use Biz\Common\ArrayToolkit;
use Biz\Common\SimpleValidator;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;

class ThreadServiceImpl extends BaseService implements ThreadService
{
    public function getThread($threadId)
    {
        return $this->getThreadDao()->get($threadId);
    }

    public function searchThreads($conditions, $orderBy, $start, $limit)
    {
        if (isset($conditions['title']) && !empty($conditions['title'])) {
            $conditions['title'] = '%'.$conditions['title'].'%';
        }
        return $this->getThreadDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchThreadsCount($conditions)
    {
        return $this->getThreadDao()->count($conditions);
    }

    public function createThread($fields)
    {
        $fields = $this->checkFields($fields);

        // $event = $this->dispatchEvent('group.thread.before_create', $thread);

        // if ($event->isPropagationStopped()) {
        //     throw $this->createServiceException($this->getKernel()->trans('发帖次数过多，请稍候尝试。'));
        // }

        // $thread['title']   = $this->sensitiveFilter($thread['title'], 'group-thread-create');
        // $thread['content'] = $this->sensitiveFilter($thread['content'], 'group-thread-create');

        $fields['title']   = $this->purifyHtml($fields['title']);
        $fields['content'] = $this->purifyHtml($fields['content']);
        $fields['user_id'] = $this->getCurrentUser()['id'];

        $fields['imgs']    = json_encode($fields['imgs']);

        $thread            = $this->getThreadDao()->create($fields);

        $this->getGroupService()->waveGroup(array($fields['group_id']), array('thread_num' => 1));

        $this->getGroupService()->waveMemberByGroupIdAndUserId($thread['group_id'], $thread['user_id'], array('thread_num' => 1));

        return $thread;
    }

    public function updateThread($threadId, $fields)
    {
        $this->checkFields($fields);

        // $fields['title']   = $this->sensitiveFilter($fields['title'], 'group-thread-update');
        // $fields['content'] = $this->sensitiveFilter($fields['content'], 'group-thread-update');

        // $this->getThreadGoodsDao()->deleteGoodsByThreadId($id, 'content');
        // $this->hideThings($fields['content'], $id);

        $fields['title']   = $this->purifyHtml($fields['title']);
        $fields['content'] = $this->purifyHtml($fields['content']);

        $thread = $this->getThreadDao()->updateThread($threadId, $fields);
        // $this->dispatchEvent('group.thread.update', $thread);
        return $thread;
    }

    public function deleteThread($threadId)
    {
        return $this->getThreadDao()->delete($threadId);
    }

    public function isCollected($userId, $threadId)
    {
        $collect = $this->getThreadCollectDao()->getByUserIdAndThreadId($userId, $threadId);

        return empty($collect) ? false : true;
    }

    public function threadCollect($userId, $threadId)
    {
        $thread = $this->getThread($threadId);

        if (empty($thread)) {
            throw new ResourceNotFoundException('话题', $threadId);
        }

        if ($userId == $thread['userId']) {
            throw new RuntimeException('不能收藏自己的话题！');
        }

        $collectThread = $this->getThreadCollectDao()->getByUserIdAndThreadId($userId, $threadId);

        if (!empty($collectThread)) {
            throw new RuntimeException('不允许重复收藏!');
        }

        return $this->getThreadCollectDao()->create(array(
            'user_id' => $userId,
            'thread_id' => $threadId
        ));
    }
    
    public function unThreadCollect($userId, $threadId)
    {
        $thread = $this->getThread($threadId);

        if (empty($thread)) {
            throw new ResourceNotFoundException('话题', $threadId);
        }

        $collectThread = $this->getThreadCollectDao()->getByUserIdAndThreadId($userId, $threadId);

        if (empty($collectThread)) {
            throw new RuntimeException('不存在此收藏关系，取消收藏失败！');
        }

        return $this->getThreadCollectDao()->delete($collectThread['id']);
    }

    public function findThreadsByIds($ids)
    {
        if (empty($ids)) {
            return array();
        }
        return $this->getThreadDao()->findByIds($ids);
    }

    public function closeThread($threadId)
    {
        $thread = $this->getThreadDao()->update($threadId, array('status' => 'close'));

        return $thread;
    }

    public function openThread($threadId)
    {
        $thread = $this->getThreadDao()->update($threadId, array('status' => 'open'));

        return $thread;
    }

    public function hitThread($threadId)
    {
        $this->getThreadDao()->wave(array($threadId), array('hit_num' => 1));
    }

    public function postThread($groupId, $threadId, $fields)
    {
        $thread = $this->getThread($threadId);

        if (empty($thread)) {
            throw new ResourceNotFoundException('话题', $threadId);
        }

        if (!isset($fields['content']) || empty($fields['content'])) {
            throw new InvalidArgumentException('回复内容不能为空！');
        }

        $fields['content']      = $this->purifyHtml($fields['content']);
        $user                   =  $this->getCurrentUser();
        $fields['user_id']      = $user['id'];
        $fields['from_user_id'] = isset($fields['from_user_id']) ? $fields['from_user_id'] : 0;
        $fields['post_id']      = isset($fields['post_id']) ? $fields['post_id'] : 0;
        $fields['thread_id']    = $thread['id'];
        $post                   = $this->getThreadPostDao()->create($fields);
        $this->getThreadDao()->update($threadId, array('last_post_member_id' => $user['id'], 'last_post_time' => time()));
        $this->getGroupService()->waveGroup(array($groupId), array('post_num' => 1));
        $this->getGroupService()->waveMemberByGroupIdAndUserId($groupId, $user['id'], array('post_num' => 1));

        if ($fields['post_id'] == 0) {
            $this->getThreadDao()->wave(array($threadId), array('post_num' => 1));
        }

        return $post;
    }

    protected function checkFields($fields)
    {
        if (!ArrayToolkit::requireds($fields, array('title', 'content', 'group_id'), true)) {
            throw new InvalidArgumentException('缺少必填字段');
        }

        if (isset($fields['imgs']) && count($fields['imgs']) >= 10) {
            throw new RuntimeException('上传数量超过限制。');
        }
        
        $group = $this->getGroupService()->getGroup($fields['group_id']);
        
        if (empty($group)) {
            throw new ResourceNotFoundException('小组', $fields['group_id']);
        }

        $fields['imgs'] = isset($fields['imgs']) ? $fields['imgs'] : array();

        return $fields;
    }

    public function searchThreadPosts($conditions, $orderBy, $start, $limit)
    {
        return $this->getThreadPostDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchThreadPostsCount($conditions)
    {
        return $this->getThreadPostDao()->count($conditions);
    }

    protected function getThreadDao()
    {
        return $this->biz->dao('Group:ThreadDao');
    }

    protected function getThreadCollectDao()
    {
        return $this->biz->dao('Group:ThreadCollectDao');
    }

    protected function getThreadPostDao()
    {
        return $this->biz->dao('Group:ThreadPostDao');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getGroupService()
    {
        return $this->biz->service('Group:GroupService');
    }
}
