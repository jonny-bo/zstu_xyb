<?php

namespace Topxia\Api\Resource\File;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\ArrayToolkit;

class File extends BaseResource
{
    public function upload(Request $request)
    {
        $file  = $request->files->get('file');
        $code  = $request->request->get('group');
        $group = $this->getFileGroupService()->getFileGroupByCode($code);
        if (empty($group)) {
            return $this->error('not_group', '上传文件组不能为空或者文件组不存在！');
        }

        if (empty($file)) {
            return $this->error('not_file', '上传文件不能为空！');
        }
        $res = $this->getFileService()->uploadFile($code, $file);

        return $this->filter($res);
    }

    public function filter($res)
    {
        $res['url'] = $this->getFileUrl($res['uri']);
        return $res;
    }

    public function simplify($res)
    {
        return $res;
    }

    protected function getFileService()
    {
        return $this->biz->service('File:FileService');
    }

    protected function getFileGroupService()
    {
        return $this->biz->service('File:FileGroupService');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}
