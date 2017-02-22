<?php

namespace Topxia\Api\Resource\User;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\ArrayToolkit;

class Users extends BaseResource
{
    public function get(Request $request)
    {
        $conditions = $request->query->all();

        // if (empty($conditions)) {
        //     return array();
        // }

        $start = $request->query->get('start', 0);
        $limit = $request->query->get('limit', 100);

        if (isset($conditions['cursor'])) {
            $conditions['updated_time_GE'] = $conditions['cursor'];
            $users = $this->getUserService()->searchUsers($conditions, array('updated_time' => 'ASC'), $start, $limit);
            $next = $this->nextCursorPaging($conditions['cursor'], $start, $limit, $users);

            return $this->wrap($this->filter($users), $next);
        } else {
            $users = $this->getUserService()->searchUsers($conditions, array('created_time' =>'DESC'), $start, $limit);
            $total = $this->getUserService()->searchUsersCount($conditions);

            return $this->wrap($this->filter($users), $total);
        }
    }

    public function post(Request $request)
    {
        $fields = $request->request->all();

        if (!ArrayToolkit::requireds($fields, array('email', 'username', 'password', 'nickname'))) {
            throw new RuntimeException('缺少必填字段');
        }

        $loginIp = $request->getClientIp();
        $fields['created_ip'] = $loginIp;

        $user = $this->getUserService()->register($fields);

        return $this->callFilter('User/User', $user);
    }

    public function filter($res)
    {
        return $this->multicallFilter('User/User', $res);
    }

    protected function multicallFilter($name, $res)
    {
        foreach ($res as $key => $one) {
            $res[$key] = $this->callFilter($name, $one);
        }
        return $res;
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
