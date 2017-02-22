<?php

namespace Biz\User\Service\Impl;

use Codeages\Biz\Framework\Service\BaseService;
use Biz\User\Service\UserService;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class UserServiceImpl extends BaseService implements UserService
{
    public function getUser($id)
    {
        return $this->getUserDao()->get($id);
    }

    public function getUserByUsername($username)
    {
        return $this->getUserDao()->getByUsername($username);
    }

    /**
     * 注意：此方法为示例方法，不能作为正式的用户注册的业务方法，请修改。
     */
    public function createUser($fields)
    {
        $user = [];
        $user['username'] = $fields['username'];

        $user['salt'] = md5(time().mt_rand(0, 1000));
        $user['password'] = $this->biz['user.password_encoder']->encodePassword($fields['password'], $user['salt']);
        $user['roles'] = empty($fields['roles']) ? array('ROLE_USER') : $fields['roles'];

        return $this->getUserDao()->create($user);
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
            $token = $this->getUserTokenDao()->getByFields(array('token' => $token));
        }
        if (empty($token) || $token['type'] != $type) {
            return null;
        }

        if ($token['expiredTime'] > 0 && $token['expiredTime'] < time()) {
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
        $token = $this->getUserTokenDao()->getByFields(array('token' => $token));

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
