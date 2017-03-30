<?php

namespace Biz\Category\Service\Impl;

use Biz\Common\BaseService;
use Biz\Category\Service\CategoryService;

class CategoryServiceImpl extends BaseService implements CategoryService
{
    public function getCategory($categoryId)
    {
        return $this->getCategoryDao()->get($categoryId);
    }

    public function searchCategorys($conditions, $orderBy, $start, $limit)
    {
        return $this->getCategoryDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchCategorysCount($conditions)
    {
        return $this->getCategoryDao()->count($conditions);
    }

    public function createCategory($fields)
    {
        return $this->getCategoryDao()->create($fields);
    }

    public function updateCategory($categoryId, $fields)
    {
        return $this->getCategoryDao()->update($categoryId, $fields);
    }

    public function deleteCategory($categoryId)
    {
        return $this->getCategoryDao()->delete($categoryId);
    }

    protected function getCategoryDao()
    {
        return $this->biz->dao('Category:CategoryDao');
    }
}
