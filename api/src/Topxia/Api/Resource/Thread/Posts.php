<?php

namespace Topxia\Api\Resource\Thread;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;

class Posts extends BaseResource
{
    public function post(Request $request, $threadId)
    {
        $fields = $request->request->all();

        $post = $this->getThreadService()->postThread($threadId, $fields);

        return $this->filter($post);
    }

    public function get(Request $request, $threadId)
    {
        $conditions = $request->query->all();

        $start = $request->query->get('start', 0);
        $limit = $request->query->get('limit', 10);

        $conditions['thread_id'] = $threadId;
        $conditions['post_id'] = isset($conditions['post_id']) ? $conditions['post_id'] : 0;

        $posts = $this->getThreadService()->searchThreadPosts(
            $conditions,
            array('created_time' => 'ASC'),
            $start,
            $limit
        );

        $total = $this->getThreadService()->searchThreadPostsCount($conditions);

        return $this->wrap($this->multiFilter($posts), $total);
    }

    public function filter($res)
    {
        $res['user'] = $this->callSimplify('User/User', $this->getUserService()->getUser($res['user_id']));
        $res['from_user'] = !empty($res['from_user_id']) ? $this->callSimplify('User/User', $this->getUserService()->getUser($res['from_user_id'])) : '';
        $res['created_time'] = date('c', $res['created_time']);

        if ($res['post_id'] == 0) {
            $res['post_num'] = $this->getThreadService()->searchThreadPostsCount(array(
                'post_id' => $res['id'],
                'thread_id' => $res['thread_id']
            ));
            $itemComments = $this->getThreadService()->searchThreadPosts(
                array('thread_id' => $res['thread_id'], 'post_id' => $res['id']),
                array('created_time' => 'ASC'),
                0,
                PHP_INI_MAX
            );
            $res['item_comments'] = $this->multiFilter($itemComments);
        }

        unset($res['user_id']);
        unset($res['from_user_id']);

        return $res;
    }

    public function simplify($res)
    {
        return $this->filter($res);
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
