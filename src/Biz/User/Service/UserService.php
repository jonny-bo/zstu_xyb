<?php

namespace Biz\User\Service;

interface UserService
{
    public function getUser($id);

    public function getUserByUsername($username);

    public function searchUsersCount($conditions);

    public function searchUsers($conditions, $orderbys, $start, $limit);

    public function makeToken($type, $userId = null, $expiredTime = null, $data = null);
    
    public function getToken($type, $token);

    public function searchTokenCount($conditions);

    public function deleteToken($type, $token);

    public function findTokensByUserIdAndType($userId, $type);

    public function verifyPassword($id, $password);

    public function verifyInSaltOut($password, $salt, $out);

    public function markLoginInfo();

    public function isEmailAvaliable($email);

    public function isUsernameAvaliable($username);

    public function isMobileAvaliable($mobile);

    public function register($registration);

    public function findUsersByIds($ids);

    public function changeUserRoles($id, array $roles);

    public function lockUser($id);

    public function unlockUser($id);

    public function updateCredit($userId, $credit);

    public function updateCoin($userId, $coin);
}
