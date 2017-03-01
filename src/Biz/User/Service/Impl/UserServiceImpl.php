<?php

namespace Biz\User\Service\Impl;

use Biz\Common\BaseService;
use Biz\User\Service\UserService;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Biz\Common\SimpleValidator;
use Biz\Common\ArrayToolkit;

class UserServiceImpl extends BaseService implements UserService
{
    protected $requiredFields = array(
        'username', 'password', 'nickname', 'email'
    );

    public function getUser($id)
    {
        return $this->getUserDao()->get($id);
    }

    public function getUserByUsername($username)
    {
        return $this->getUserDao()->getByUsername($username);
    }

    public function searchUsers($conditions, $orderbys, $start, $limit)
    {
        return $this->getUserDao()->search($conditions, $orderbys, $start, $limit);
    }

    public function searchUsersCount($conditions)
    {
        return $this->getUserDao()->count($conditions);
    }

    public function isUsernameAvaliable($username)
    {
        if (empty($username)) {
            return false;
        }

        $user = $this->getUserDao()->getByUsername($username);

        return empty($user) ? true : false;
    }

    public function isEmailAvaliable($email)
    {
        if (empty($email)) {
            return false;
        }

        $user = $this->getUserDao()->getByEmail($email);
        return empty($user) ? true : false;
    }

    public function isMobileAvaliable($mobile)
    {
        if (empty($mobile)) {
            return false;
        }

        $user = $this->getUserDao()->getByMobile($mobile);
        return empty($user) ? true : false;
    }

    public function register($registration)
    {
        $this->vilidateUser($registration);

        $user = array();

        $user['username']       = $registration['username'];

        $user['salt']           = md5(time().mt_rand(0, 1000));
        $user['password']       = $this->biz['user.password_encoder']->encodePassword($registration['password'], $user['salt']);
        $user['roles']          = empty($registration['roles']) ? array('ROLE_USER') : $registration['roles'];

        if (isset($registration['mobile'])) {
            $user['mobile']     = $registration['mobile'];
        } else {
            $user['mobile']     = '';
        }

        $user['email']          = $registration['email'];
        $user['email_verified'] = isset($registration['email_verified']) ? $registration['email_verified'] : 0;
        $user['nickname']       = $registration['nickname'];
        // $user['roles']          = array('ROLE_USER');
        $user['created_ip']     = empty($registration['created_ip']) ? '' : $registration['created_ip'];

        $user['created_time']   = time();

        return $this->getUserDao()->create($user);
    }

    protected function vilidateUser($registration)
    {
        if (!ArrayToolkit::requireds($registration, $this->requiredFields)) {
            throw new InvalidArgumentException('缺少必要参数');
        }

        if (!SimpleValidator::nickname($registration['nickname'])) {
            throw new UnexpectedValueException('昵称校验失败');
        }

        if (!$this->isUsernameAvaliable($registration['username'])) {
            throw new UnexpectedValueException('用户名已存在');
        }

        if (!SimpleValidator::email($registration['email'])) {
            throw new UnexpectedValueException('Email校验失败');
        }

        if (!$this->isEmailAvaliable($registration['email'])) {
            throw new UnexpectedValueException('Email已存在');
        }

        if (isset($registration['mobile']) && $registration['mobile'] != "" && !SimpleValidator::mobile($registration['mobile'])) {
            throw new UnexpectedValueException('手机号校验失败');
        }
    }

    public function verifyPassword($id, $password)
    {
        $user = $this->getUser($id);

        if (empty($user)) {
            throw new ResourceNotFoundException('User', $id);
        }

        return $this->verifyInSaltOut($password, $user['salt'], $user['password']);
    }

    public function verifyInSaltOut($password, $salt, $out)
    {
        return $out == $this->getPasswordEncoder()->encodePassword($password, $salt);
    }

    public function makeToken($type, $userId = null, $expiredTime = null, $data = null, $args = array())
    {
        $token                = array();
        $token['type']        = $type;
        $token['user_id']      = $userId ? (int)$userId : 0;
        $token['token']       = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $token['data']        = serialize($data);
        $token['times']       = empty($args['times']) ? 0 : intval($args['times']);
        $token['expired_time'] = $expiredTime ? (int)$expiredTime : 0;
        $token                = $this->getUserTokenDao()->create($token);
        return $token['token'];
    }

    public function getToken($type, $token)
    {
        if ($token) {
            $token = $this->getUserTokenDao()->getByToken($token);
        }
        
        if (empty($token) || $token['type'] != $type) {
            return null;
        }

        if ($token['expired_time'] > 0 && $token['expired_time'] < time()) {
            return null;
        }

        $token['data'] = unserialize($token['data']);
        return $token;
    }

    public function searchTokenCount($conditions)
    {
        return $this->getUserTokenDao()->count($conditions);
    }

    public function deleteToken($type, $token)
    {
        $token = $this->getUserTokenDao()->getByToken($token);

        if (empty($token) || $token['type'] != $type) {
            return false;
        }

        $this->getUserTokenDao()->delete($token['id']);
        return true;
    }

    public function markLoginInfo()
    {
        $user = $this->biz['user'];

        if (empty($user)) {
            return;
        }

        return $this->getUserDao()->update($user['id'], array(
            'login_ip'   => $user['login_ip'],
            'login_time' => time()
        ));
    }

    protected function getPasswordEncoder()
    {
        return new MessageDigestPasswordEncoder('sha256');
    }

    protected function getUserDao()
    {
        return $this->biz->dao('User:UserDao');
    }

    protected function getUserTokenDao()
    {
        return $this->biz->dao('User:UserTokenDao');
    }
}
