<?php

namespace Topxia\Api\Resource\User;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use AppBundle\Security\CurrentUser;

class User extends BaseResource
{
    public function get($userId)
    {
        $user = $this->getUserService()->getUser($userId);

        if (empty($user)) {
            return $this->error(404, "用户(#{$userId})不存在");
        }
        
        return $this->filter($user);
    }

    public function filter($res)
    {
        unset($res['password']);
        unset($res['salt']);

        $res['login_time'] = date('c', $res['login_time']);
        $res['updated_time'] = date('c', $res['updated_time']);
        $res['created_time'] = date('c', $res['created_time']);

        $user = $this->biz['user'];

        if (!$user->isLogin() || !$user->isAdmin() || ($user['id'] != $res['id'])) {
            unset($res['email']);
            unset($res['point']);
            unset($res['coin']);
            unset($res['login_ip']);
            unset($res['updated_time']);
            unset($res['login_time']);
            unset($res['created_time']);

            return $res;
        }

        return $res;
    }

    public function simplify($res)
    {
        $simple = array();

        $simple['id']       = $res['id'];
        $simple['nickname'] = $res['nickname'];
        $simple['avatar']   = $this->getFileUrl($res['avatar']);

        return $simple;
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
