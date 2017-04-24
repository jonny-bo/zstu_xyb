<?php

namespace Topxia\Api\Resource\Group;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use Biz\Common\ArrayToolkit;

class Group extends BaseResource
{
    protected $requireFiles = array(
        'title'
    );
    public function search(Request $request)
    {
        $conditions = $request->query->all();

        $start   = $request->query->get('start', 0);
        $limit   = $request->query->get('limit', 10);
        $conditions['status'] = isset($conditions['status']) ? $conditions['status'] : 'open';

        $ordeyBy = $this->getOrderBy($conditions, array('created_time' => 'DESC'));
        $groups = $this->getGroupService()->searchGroups($conditions, $ordeyBy, $start, $limit);
        $total = $this->getGroupService()->searchGroupsCount($conditions);

        return $this->wrap($this->multiSimplify($groups), $total);
    }

    public function get($groupId)
    {
        $group = $this->getGroupService()->getGroup($groupId);

        return $this->filter($group);
    }

    public function post(Request $request)
    {
        $group = $request->request->all();

        $this->checkRequiredFields($this->requireFiles, $group);

        $this->getGroupService()->createGroup($group);

        return array('success' => 'true');
    }

    public function update(Request $request, $groupId)
    {
        $fields = $request->request->all();

        $this->checkRequiredFields($this->requireFiles, $fields);

        $this->getGroupService()->updateGroup($groupId, $fields);

        return array('success' => 'true');
    }

    public function filter($res)
    {
        $res['created_time'] = date('c', $res['created_time']);
        $res['logo']         = $this->getFileUrl($res['logo']);
        $res['owner']        = $this->callSimplify('User/User', $this->getUserService()->getUser($res['owner_id']));

        unset($res['background_logo']);
        unset($res['owner_id']);

        return $res;
    }

    public function simplify($res)
    {
        unset($res['about']);
        unset($res['background_logo']);
        unset($res['status']);
        unset($res['owner_id']);

        $res['created_time'] = date('c', $res['created_time']);

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
