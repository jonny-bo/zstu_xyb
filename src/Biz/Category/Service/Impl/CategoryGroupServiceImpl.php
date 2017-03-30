<?php

namespace Biz\Category\Service\Impl;

use Biz\Common\BaseService;
use Biz\Category\Service\CategoryGroupService;

class CategoryGroupServiceImpl extends BaseService implements CategoryGroupService
{
    public function getCategoryGroup($categoryGroupId)
    {
        return $this->getCategoryGroupDao()->get($categoryGroupId);
    }

    public function searchCategoryGroups($conditions, $orderBy, $start, $limit)
    {
        return $this->getCategoryGroupDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchCategoryGroupsCount($conditions)
    {
        return $this->getCategoryGroupDao()->count($conditions);
    }

    public function createCategoryGroup($fields)
    {
        return $this->getCategoryGroupDao()->create($fields);
    }

    public function updateCategoryGroup($categoryGroupId, $fields)
    {
        return $this->getCategoryGroupDao()->update($categoryGroupId, $fields);
    }

    public function deleteCategoryGroup($categoryGroupId)
    {
        return $this->getCategoryGroupDao()->delete($categoryGroupId);
    }

    protected function getCategoryGroupDao()
    {
        return $this->biz->dao('Category:CategoryGroupDao');
    }
}
