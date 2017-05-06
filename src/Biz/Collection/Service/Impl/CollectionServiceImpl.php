<?php

namespace Biz\Collection\Service\Impl;

use Biz\Common\BaseService;
use Biz\Collection\Service\CollectionService;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\AccessDeniedException;

class CollectionServiceImpl extends BaseService implements CollectionService
{
    public function getCollection($collectionId)
    {
        return $this->getCollectionDao()->get($collectionId);
    }

    public function searchCollection($conditions, $orderBy, $start, $limit)
    {
        return $this->getCollectionDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchCollectionCount($conditions)
    {
        return $this->getCollectionDao()->count($conditions);
    }

    public function createCollection($fields)
    {
        return $this->getCollectionDao()->create($fields);
    }

    public function updateCollection($collectionId, $fields)
    {
        return $this->getCollectionDao()->update($collectionId, $fields);
    }

    public function deleteCollection($collectionId)
    {
        $collection = $this->getCollection($collectionId);
        if (empty($collection)) {
            throw new ResourceNotFoundException('collection', $collectionId, '收藏对象不存在！');
        }
        return $this->getCollectionDao()->delete($collectionId);
    }

    protected function getCollectionDao()
    {
        return $this->biz->dao('Collection:CollectionDao');
    }
}
