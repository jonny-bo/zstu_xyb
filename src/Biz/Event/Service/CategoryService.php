<?php

namespace Biz\Event\Service;

interface CategoryService
{
    public function getCategory($categoryId);

    public function searchCategory($conditions, $orderBy, $start, $limit);

    public function searchCategoryCount($conditions);

    public function createCategory($fields);

    public function updateCategory($categoryId, $fields);

    public function deleteCategory($categoryId);
}
