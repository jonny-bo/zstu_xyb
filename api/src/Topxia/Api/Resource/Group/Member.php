<?php

namespace Topxia\Api\Resource\Group;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use Biz\Common\ArrayToolkit;

class Member extends BaseResource
{
    public function search(Request $request)
    {
        $conditions = $request->query->all();

        $start   = $request->query->get('start', 0);
        $limit   = $request->query->get('limit', 10);

        $members = $this->getGroupService()->searchMembers(
            $conditions,
            array('created_time' => 'DESC'),
            $start,
            $limit
        );
        $total = $this->getGroupService()->searchMembersCount($conditions);

        return $this->wrap($this->multiFilter($members), $total);
    }

    public function filter($res)
    {
        $res['user']         = $this->callSimplify('User/User', $this->getUserService()->getUser($res['user_id']));

        $res['created_time'] = date('c', $res['created_time']);

        unset($res['user_id']);
        unset($res['group_id']);

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

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}
