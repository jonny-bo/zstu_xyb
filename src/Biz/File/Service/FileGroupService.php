<?php

namespace Biz\File\Service;

interface FileGroupService
{
    public function getFileGroup($fileGroupId);

    public function getFileGroupByCode($code);

    public function findAllFileGroups();

    public function searchFileGroups($conditions, $orderBy, $start, $limit);

    public function searchFileGroupsCount($conditions);

    public function createFileGroup($fields);

    public function updateFileGroup($fileGroupId, $fields);

    public function deleteFileGroup($fileGroupId);
}
