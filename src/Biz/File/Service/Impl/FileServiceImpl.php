<?php

namespace Biz\File\Service\Impl;

use Biz\Common\BaseService;
use Biz\File\Service\FileService;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\FileToolkit;

class FileServiceImpl extends BaseService implements FileService
{
    public function getFile($fileId)
    {
        return $this->getFileDao()->get($fileId);
    }

    public function searchFiles($conditions, $orderBy, $start, $limit)
    {
        return $this->getFileDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchFilesCount($conditions)
    {
        return $this->getFileDao()->count($conditions);
    }

    public function uploadFile($group, UploadedFile $file)
    {
        $group = $this->getFileGroupService()->getFileGroupByCode($group);
        $user  = $this->getCurrentUser();

        if (!$user->isLogin()) {
            throw new RuntimeException("未登录用户不能上传");
        }
        $errors = FileToolkit::validateFileExtension($file);
        if ($errors) {
            @unlink($file->getRealPath());
            throw new RuntimeException("该文件格式不允许上传");
        }

        $record  = array(
            'user_id'   => $user['id'],
            'group_id'  => $group['id'],
            'uri'       => $this->generateUri($group, $file),
            'size'      => $file->getSize(),
            'mime'      => $file->getClientMimeType(),
            'status'    => 0
        );

        $this->saveFile($file, $record['uri']);

        return $this->createFile($record);
    }

    public function createFile($fields)
    {
        return $this->getFileDao()->create($fields);
    }

    public function updateFile($fileId, $fields)
    {
        return $this->getFileDao()->update($fileId, $fields);
    }

    public function deleteFile($fileId)
    {
        return $this->getFileDao()->delete($fileId);
    }

    public function parseFileUri($uri)
    {
        $parsed = array();
        $parts  = explode('://', $uri);
        if (empty($parts) || count($parts) != 2) {
            throw new RuntimeException("解析文件URI({$uri})失败！");
        }
        $parsed['access']    = $parts[0];
        $parsed['path']      = $parts[1];
        $parsed['directory'] = dirname($parsed['path']);
        $parsed['name']      = basename($parsed['path']);

        $directory = $this->biz['upload.public_directory'];

        if ($parsed['access'] == 'public') {
            $directory = $this->biz['upload.public_directory'];
        } else {
            $directory = $this->biz['upload.private_directory'];
        }
        $parsed['fullpath'] = $directory.'/'.$parsed['path'];

        return $parsed;
    }

    protected function saveFile($file, $uri)
    {
        $parsed = $this->parseFileUri($uri);
        if ($parsed['access'] == 'public') {
            $directory = $this->biz['upload.public_directory'];
        } else {
            $directory = $this->biz['upload.private_directory'];
        }

        if (!is_writable($directory)) {
            throw new RuntimeException("文件上传路径{$directory}不可写，文件上传失败");
        }
        $directory .= '/'.$parsed['directory'];

        $newFile = $file->move($directory, $parsed['name']);

        $newFilePath = FileToolkit::imagerotatecorrect($newFile->getRealPath());
        if ($newFilePath) {
            return new File($newFilePath);
        }

        return $newFile;
    }

    protected function generateUri($group, $file)
    {
        if ($file instanceof UploadedFile) {
            $filename = $file->getClientOriginalName();
        } else {
            $filename = $file->getFilename();
        }

        $filenameParts = explode('.', $filename);
        $ext           = array_pop($filenameParts);
        if (empty($ext)) {
            throw new RuntimeException('获取文件扩展名失败！');
        }

        $uri = ($group['public'] ? 'public://' : 'private://').$group['code'].'/';
        $uri .= date('Y').'/'.date('m-d').'/'.date('His');
        $uri .= substr(uniqid(), -6).substr(uniqid('', true), -6);
        $uri .= '.'.$ext;

        return $uri;
    }

    protected function getFileDao()
    {
        return $this->biz->dao('File:FileDao');
    }

    protected function getFileGroupService()
    {
        return $this->biz->service('File:FileGroupService');
    }
}
