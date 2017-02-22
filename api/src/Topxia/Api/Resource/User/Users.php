<?php

namespace Topxia\Api\Resource\User;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;

class Users extends BaseResource
{
    public function get(Request $request)
    {
        $conditions = $request->query->all();

        if (empty($conditions)) {
            return array();
        }

        $start = $request->query->get('start', 0);
        $limit = $request->query->get('limit', 100);

        if (isset($conditions['cursor'])) {
            $conditions['updatedTime_GE'] = $conditions['cursor'];
            $users = $this->getUserService()->searchUsers($conditions, array('updated_time', 'ASC'), $start, $limit);
            $next = $this->nextCursorPaging($conditions['cursor'], $start, $limit, $users);

            return $this->wrap($this->filter($users), $next);
        } else {
            $users = $this->getUserService()->searchUsers($conditions, array('created_time','DESC'), $start, $limit);
            $total = $this->getUserService()->searchUserCount($conditions);

            return $this->wrap($this->filter($users), $total);
        }
    }
    
    protected function getTokenService()
    {
        return $this->biz->service('User:TokenService');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}
