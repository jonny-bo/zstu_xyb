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
        $conditions = $this->perConditions($conditions);
        return $this->getUserDao()->search($conditions, $orderbys, $start, $limit);
    }

    public function findTokensByUserIdAndType($userId, $type)
    {
        return $this->getUserTokenDao()->findByUserIdAndType($userId, $type);
    }

    public function searchUsersCount($conditions)
    {
        $conditions = $this->perConditions($conditions);
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

        $user['password']       = $this->passwordEncode($registration['password'], $user['salt']);
        $user['roles']          = empty($registration['roles']) ? array('ROLE_USER') : $registration['roles'];

        if (isset($registration['mobile'])) {
            $user['mobile']     = $registration['mobile'];
        } else {
            $user['mobile']     = '';
        }

        $user['email']          = $registration['email'];
        $user['email_verified'] = isset($registration['email_verified']) ? $registration['email_verified'] : 0;
        $user['nickname']       = $registration['nickname'];
        $user['created_ip']     = empty($registration['created_ip']) ? '' : $registration['created_ip'];

        $user['created_time']   = time();

        return $this->getUserDao()->create($user);
    }

    public function findUsersByIds($ids)
    {
        return $this->getUserDao()->findByIds($ids);
    }

    public function changeUserRoles($id, array $roles)
    {
        if (empty($roles)) {
            throw new InvalidArgumentException('用户角色不能为空');
        }

        $user = $this->getUser($id);

        if (empty($user)) {
            throw new ResourceNotFoundException('User', $id, '设置用户角色失败');
        }

        if (!in_array('ROLE_USER', $roles)) {
            throw new UnexpectedValueException('用户角色必须包含ROLE_USER');
        }
        $currentUser     = $this->getCurrentUser();
        $allowedRoles    = $currentUser['roles'];
        $notAllowedRoles = array_diff($roles, $allowedRoles);

        if (!empty($notAllowedRoles) && !in_array('ROLE_SUPER_ADMIN', $currentUser['roles'], true)) {
            throw new UnexpectedValueException('用户角色不正确，设置用户角色失败。');
        }

        return $this->getUserDao()->update($id, array('roles' => $roles));
    }

    public function lockUser($id)
    {
        $user = $this->getUser($id);

        if (empty($user)) {
            throw new ResourceNotFoundException('User', $id);
        }

        $this->getUserDao()->update($user['id'], array('locked' => 1));

        return true;
    }

    public function unlockUser($id)
    {
        $user = $this->getUser($id);

        if (empty($user)) {
            throw new ResourceNotFoundException('User', $id);
        }

        $this->getUserDao()->update($user['id'], array('locked' => 0));

        return true;
    }

    public function updateCredit($userId, $credit)
    {
        $user = $this->getUser($userId);

        return $this->getUserDao()->update($userId, array('credit' => $user['credit']+$credit));
    }

    public function updateCoin($userId, $coin)
    {
        $user = $this->getUser($userId);

        return $this->getUserDao()->update($userId, array('coin' => $user['coin']+$coin));
    }

    public function changePayPassword($userId, $newPayPassword)
    {
        if (empty($newPayPassword)) {
            throw new InvalidArgumentException('参数不正确，更改支付密码失败');
        }

        $user = $this->getUser($userId);

        if (empty($user)) {
            throw new ResourceNotFoundException('User', $userId, '更改支付密码失败');
        }

        if (!SimpleValidator::numbers($newPayPassword) || strlen($newPayPassword) != 6) {
            throw new InvalidArgumentException('支付密码格式不不正确！');
        }

        $payPasswordSalt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

        $fields = array(
            'pay_password_salt' => $payPasswordSalt,
            'pay_password'     => $this->passwordEncode($newPayPassword, $payPasswordSalt)
        );

        $this->getUserDao()->update($userId, $fields);

        $this->getLogService()->info('user', $userId, 'pay-password-changed', sprintf('用户%s(ID:%u)重置支付密码成功', $user['nickname'], $user['id']));

        return true;
    }

    public function verifyPayPassword($userId, $payPassword)
    {
        $user = $this->getUser($userId);

        if (empty($user)) {
            throw new ResourceNotFoundException('User', $userId, '用户不存在');
        }

        return $this->verifyInSaltOut($payPassword, $user['pay_password_salt'], $user['pay_password']);
    }

    protected function perConditions($conditions)
    {
        $conditions =  array_filter($conditions);

        if (isset($conditions['roles'])) {
            $conditions['roles'] = "%{$conditions['roles']}%";
        }

        if (isset($conditions['keywordType']) && isset($conditions['keyword'])) {
            if ($conditions['keywordType'] == 'loginIp') {
                $conditions[$conditions['keywordType']] = "{$conditions['keyword']}";
            } else {
                $conditions[$conditions['keywordType']] = "%{$conditions['keyword']}%";
            }

            unset($conditions['keywordType']);
            unset($conditions['keyword']);
        }

        if (isset($conditions['datePicker']) && $conditions['datePicker'] == 'longinDate') {
            if (isset($conditions['startDate'])) {
                $conditions['loginStartTime'] = strtotime($conditions['startDate']);
            }

            if (isset($conditions['endDate'])) {
                $conditions['loginEndTime'] = strtotime($conditions['endDate']);
            }
        }

        if (isset($conditions['datePicker']) && $conditions['datePicker'] == 'registerDate') {
            if (isset($conditions['startDate'])) {
                $conditions['startTime'] = strtotime($conditions['startDate']);
            }

            if (isset($conditions['endDate'])) {
                $conditions['endTime'] = strtotime($conditions['endDate']);
            }
        }

        return $conditions;
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
            throw new ResourceNotFoundException('用户', $id);
        }

        return $this->verifyInSaltOut($password, $user['salt'], $user['password']);
    }

    public function verifyInSaltOut($password, $salt, $out)
    {
        return $out == $this->passwordEncode($password, $salt);
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

        $oldTokens = $this->getUserTokenDao()->findByUserIdAndType($userId, $type);

        if (!empty($oldTokens)) {
            $this->deleteOldTokens($oldTokens);
        }

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
        $user = $this->getCurrentUser();

        if (empty($user)) {
            return;
        }

        return $this->getUserDao()->update($user['id'], array(
            'login_ip'   => $user['login_ip'],
            'login_time' => time()
        ));
    }

    protected function deleteOldTokens(array $oldTokens)
    {
        foreach ($oldTokens as $oldToken) {
            $this->getUserTokenDao()->delete($oldToken['id']);
        }
    }

    protected function passwordEncode($password, $salt)
    {
        return $this->biz['user.password_encoder']->encodePassword($password, $salt);
    }

    protected function getUserDao()
    {
        return $this->biz->dao('User:UserDao');
    }

    protected function getUserTokenDao()
    {
        return $this->biz->dao('User:UserTokenDao');
    }

    protected function getLogService()
    {
        return $this->biz->service('System:LogService');
    }
}
