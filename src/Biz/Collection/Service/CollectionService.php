<?php

namespace Biz\Collection\Service;

interface CollectionService
{
    public function getCollection($collectionId);

    public function searchCollection($conditions, $orderBy, $start, $limit);

    public function searchCollectionCount($conditions);

    public function createCollection($fields);

    public function updateCollection($collectionId, $fields);

    public function deleteCollection($collectionId);
}
