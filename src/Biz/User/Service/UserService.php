<?php

namespace Biz\User\Service;

interface UserService
{
    public function getUser($id);

    public function getUserByUsername($username);

    public function createUser($user);

    public function makeToken($type, $userId = null, $expiredTime = null, $data = null);
    
    public function getToken($type, $token);

    public function searchTokenCount($conditions);

    public function deleteToken($type, $token);

    public function verifyPassword($id, $password);

    public function verifyInSaltOut($password, $salt, $out);

    public function markLoginInfo();
}
