<?php

namespace Topxia\Api\Resource\User;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use AppBundle\Security\CurrentUser;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\ArrayToolkit;
use Biz\Common\FileToolkit;

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

    public function search(Request $request)
    {
        $conditions = $request->query->all();

        $start = $request->query->get('start', 0);
        $limit = $request->query->get('limit', 10);

        if (isset($conditions['cursor'])) {
            $conditions['updated_time_GE'] = $conditions['cursor'];
            $users = $this->getUserService()->searchUsers($conditions, array('updated_time' => 'ASC'), $start, $limit);
            $next = $this->nextCursorPaging($conditions['cursor'], $start, $limit, $users);

            return $this->wrap($this->multiFilter($users), $next);
        } else {
            $users = $this->getUserService()->searchUsers($conditions, array('created_time' =>'DESC'), $start, $limit);
            $total = $this->getUserService()->searchUsersCount($conditions);

            return $this->wrap($this->multiFilter($users), $total);
        }
    }

    public function login(Request $request)
    {
        $fields = $request->request->all();

        if (!ArrayToolkit::requireds($fields, array('username', 'password'))) {
            throw new RuntimeException('缺少必填字段');
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
        $this->biz['user'] = new CurrentUser($user);

        $user  = $this->getUserService()->markLoginInfo();

        return array(
            'user'  => $this->filter($user),
            'token' => $token
        );
    }

    public function setAvatar(Request $request)
    {
        $file = $request->files->get('avatar');

        if (empty($file)) {
            return $this->error('not_file', '请选择图片');
        }

        if (!FileToolkit::isImageFile($file)) {
            throw new \RuntimeException('您上传的不是图片文件，请重新上传。');
        }

        if (FileToolkit::getMaxFilesize() <= $file->getClientSize()) {
            throw new \RuntimeException('您上传的图片超过限制，请重新上传。');
        }

        $user = $this->getUserInfoService()->changeAvatar($file);

        return $this->filter($user);
    }

    public function register(Request $request)
    {
        $fields = $request->request->all();

        if (!ArrayToolkit::requireds($fields, array('email', 'username', 'password', 'nickname'))) {
            throw new RuntimeException('缺少必填字段');
        }

        $loginIp = $request->getClientIp();
        $fields['created_ip'] = $loginIp;

        $user = $this->getUserService()->register($fields);

        return $this->filter($user);
    }

    public function filter($res)
    {
        unset($res['password']);
        unset($res['salt']);

        $res['login_time']   = date('c', $res['login_time']);
        $res['updated_time'] = date('c', $res['updated_time']);
        $res['created_time'] = date('c', $res['created_time']);
        $res['avatar']       = $this->getFileUrl($res['avatar']);

        $user = $this->biz['user'];

        if (!$user->isLogin() || !$user->isAdmin() || ($user['id'] != $res['id'])) {
            unset($res['email']);
            unset($res['roles']);
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

    protected function getUserInfoService()
    {
        return $this->biz->service('User:UserInfoService');
    }
}
