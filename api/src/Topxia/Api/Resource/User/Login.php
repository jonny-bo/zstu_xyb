<?php

namespace Topxia\Api\Resource\User;

use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\ArrayToolkit;

class Login extends BaseResource
{
    public function post(Request $request)
    {
        $fields = $request->request->all();

        if (!ArrayToolkit::requireds($fields, array('username', 'password'))) {
            return $this->error('required field', '缺少必填字段');
        }

        $user = $this->getUserService()->getUserByUsername($fields['username']);

        if (empty($user)) {
            throw new ResourceNotFoundException('用户名', $fields['username'], '用户名不存在');
        }

        if (!$this->getUserService()->verifyPassword($user['id'], $fields['password'])) {
            throw new RuntimeException('密码错误');
        }
        
        $token = $this->getUserService()->makeToken('mobile_login', $user['id']);

        $user['login_ip']  = $request->getClientIp();
        $this->biz['user'] = $user;

        $user  = $this->getUserService()->markLoginInfo();

        return array(
            'user'  => $this->callFilter('User/User', $user),
            'token' => $token
        );
    }

    public function filter($res)
    {
        return $res;
    }

    protected function getTokenService()
    {
        return $this->biz->service('User.TokenService');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}
