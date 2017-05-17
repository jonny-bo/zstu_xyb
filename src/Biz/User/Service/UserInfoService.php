<?php

namespace Biz\User\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UserInfoService
{
    public function changeAvatar(UploadedFile $file);

    public function recordCredit($userId, $message, $credit);

    public function recordBill($userId, $message, $coin);

    public function setTagId($userId, $tagId);

    public function setBaseInfo($userId, $fields);

    public function findCreditsByUserId($userId);
}
