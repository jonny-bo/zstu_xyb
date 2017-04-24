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

        $start   = $request->query->get('start', 0);
        $limit   = $request->query->get('limit', 10);

        $threds = $this->getThreadService()->searchThreads(
            $conditions,
            array('created_time' => 'DESC'),
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
