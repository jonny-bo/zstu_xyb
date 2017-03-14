<?php

namespace Biz\User\Service\Impl;

use Biz\Common\BaseService;
use Biz\User\Service\UserInfoService;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\SimpleValidator;
use Biz\Common\ArrayToolkit;
use Biz\Common\FileToolkit;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserInfoServiceImpl extends BaseService implements UserInfoService
{
    public function changeAvatar(UploadedFile $file)
    {
        $user = $this->getCurrentUser();

        if (!$user->isLogin()) {
            throw new \RuntimeException('未登录用户不能修改头像。');
        }

        if (!FileToolkit::isImageFile($file)) {
            throw new \RuntimeException('您上传的不是图片文件，请重新上传。');
        }

        if (FileToolkit::getMaxFilesize() <= $file->getClientSize()) {
            throw new \RuntimeException('您上传的图片超过限制，请重新上传。');
        }

        if (!empty($user['avatar'])) {
            FileToolkit::remove(__DIR__.'/../../../../../web/files/'.$user['avatar']);
        }

        $filename = FileToolkit::moveFile(__DIR__.'/../../../../../web/files', $file, 'user');

        return $this->getUserDao()->update($user['id'], array('avatar' => 'user/'.$filename));
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
