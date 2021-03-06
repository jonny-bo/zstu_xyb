<?php

namespace Topxia\Api\Resource\Thread;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use Biz\Common\ArrayToolkit;
use Biz\Util\Jpush;

class Thread extends BaseResource
{
    protected $requireFiles = array(
        'title', 'content'
    );
    public function search(Request $request)
    {
        $conditions = $request->query->all();
        $conditions['status']   = isset($conditions['status']) ? $conditions['status'] : 'open';

        $ordeyBy = $this->getOrderBy($conditions, array('updated_time' => 'DESC'));

        $start   = $request->query->get('start', 0);
        $limit   = $request->query->get('limit', 10);

        $threds = $this->getThreadService()->searchThreads(
            $conditions,
            $ordeyBy,
            $start,
            $limit
        );
        $total = $this->getThreadService()->searchThreadsCount($conditions);

        return $this->wrap($this->multiFilter($threds), $total);
    }

    public function get(Request $request, $threadId)
    {
        $start  = $request->query->get('start', 0);
        $limit  = $request->query->get('limit', 10);
        $thread = $this->getThreadService()->getThread($threadId);

        $this->getThreadService()->hitThread($threadId);

        $comments   = $this->getThreadService()->searchThreadPosts(
            array('thread_id' => $threadId, 'post_id' => 0),
            array('created_time' => 'ASC'),
            $start,
            $limit
        );

        $thread['comments'] = $this->multicallFilter('Thread/Posts', $comments);

        return $this->filter($thread);
    }

    public function post(Request $request)
    {
        $fields             = $request->request->all();
        if (!isset($fields['group_id']) || empty($fields['group_id'])) {
            $group = $this->getGroupService()->getGroupByTitle('default');
            $fields['group_id'] = $group['id'];
        }
        if (isset($fields['imgs']) && !empty($fields['imgs'])) {
            $fields['imgs'] = json_decode($fields['imgs'], true);
            $fields['imgs'] = array_values($fields['imgs']);
        }

        $this->checkRequiredFields($this->requireFiles, $fields);
        $this->getThreadService()->createThread($fields);

        return array('success' => 'ture');
    }

    public function filter($res)
    {
        foreach ($res['imgs'] as $key => $uri) {
            $res['imgs'][$key] = $this->getFileUrl($uri);
        }
        $res['user'] = $this->callSimplify('User/User', $this->getUserService()->getUser($res['user_id']));
        if ($res['last_post_member_id']) {
            $res['last_post_member']    = $this->callSimplify('User/User', $this->getUserService()->getUser($res['last_post_member_id']));
        } else {
            $res['last_post_member'] = $res['user'];
        }

        $res['updated_time'] = date('c', $res['updated_time']);
        $res['created_time'] = date('c', $res['created_time']);
        $res['last_post_time'] = $res['last_post_time'] ? date('c', $res['last_post_time']) : $res['last_post_time'];

        unset($res['last_post_member_id']);
        unset($res['user_id']);
        unset($res['status']);

        return $res;
    }

    public function simplify($res)
    {
        return $res;
    }

    protected function getGroupService()
    {
        return $this->biz->service('Group:GroupService');
    }

    protected function getThreadService()
    {
        return $this->biz->service('Group:ThreadService');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}
