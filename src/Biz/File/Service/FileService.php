<?php

namespace Biz\File\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileService
{
    public function getFile($fileId);

    public function findFilesByIds(array $fileIds);

    public function searchFiles($conditions, $orderBy, $start, $limit);

    public function searchFilesCount($conditions);

    public function createFile($fields);

    public function updateFile($fileId, $fields);

    public function deleteFile($fileId);

    public function uploadFile($group, UploadedFile $file);

    public function parseFileUri($uri);
}
