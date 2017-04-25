<?php

namespace Topxia\Api\Resource\Thread;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use Biz\Common\ArrayToolkit;

class Thread extends BaseResource
{
    protected $requireFiles = array(
        'title', 'content', 'group_id'
    );
    public function search(Request $request, $groupId)
    {
        $group = $this->getGroupService()->getGroup($groupId);
        if (!$group) {
            return $this->error('not_group', '小组不存在！');
        }
        $conditions = $request->query->all();
        $conditions['group_id'] = $groupId;
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

    public function post(Request $request, $groupId)
    {
        $group = $this->getGroupService()->getGroup($groupId);
        if (!$group) {
            return $this->error('not_group', '小组不存在！');
        }

        $fields             = $request->request->all();
        $fields['group_id'] = $groupId;

        $this->checkRequiredFields($this->requireFiles, $fields);
        $this->getThreadService()->createThread($fields);

        return array('success' => 'ture');
    }

    public function filter($res)
    {
        $res['imgs'] = json_decode($res['imgs'], true);
        foreach ($res['imgs'] as $key => $uri) {
            $res['imgs'][$key] = $this->getFileUrl($uri);
        }
        if ($res['last_post_member_id']) {
            $res['last_post_member']    = $this->callSimplify('User/User', $this->getUserService()->getUser($res['last_post_member_id']));
        } else {
            $res['last_post_member'] = array();
        }
        $res['user'] = $this->callSimplify('User/User', $this->getUserService()->getUser($res['user_id']));
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
