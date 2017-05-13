<?php

namespace Biz\User\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UserInfoService
{
    public function changeAvatar(UploadedFile $file);

    public function recordCredit($userId, $message, $credit);
}
