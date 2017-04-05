<?php

namespace Biz\File\Service\Impl;

use Biz\Common\BaseService;
use Biz\File\Service\FileGroupService;

class FileGroupServiceImpl extends BaseService implements FileGroupService
{
    public function getFileGroup($fileGroupId)
    {
        return $this->getFileGroupDao()->get($fileGroupId);
    }

    public function getFileGroupByCode($code)
    {
        return $this->getFileGroupDao()->getByCode($code);
    }

    public function searchFileGroups($conditions, $orderBy, $start, $limit)
    {
        return $this->getFileGroupDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchFileGroupsCount($conditions)
    {
        return $this->getFileGroupDao()->count($conditions);
    }

    public function createFileGroup($fields)
    {
        return $this->getFileGroupDao()->create($fields);
    }

    public function updateFileGroup($fileGroupId, $fields)
    {
        return $this->getFileGroupDao()->update($fileGroupId, $fields);
    }

    public function deleteFileGroup($fileGroupId)
    {
        return $this->getFileGroupDao()->delete($fileGroupId);
    }

    protected function getFileGroupDao()
    {
        return $this->biz->dao('File:FileGroupDao');
    }
}
