<?php

namespace Biz\Category\Service;

interface CategoryService
{
    public function getCategory($categoryId);

    public function searchCategorys($conditions, $orderBy, $start, $limit);

    public function searchCategorysCount($conditions);

    public function createCategory($fields);

    public function updateCategory($categoryId, $fields);

    public function deleteCategory($categoryId);

    public function findCategoryByGroupCode($code);
}
