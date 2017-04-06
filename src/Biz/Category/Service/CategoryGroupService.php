<?php

namespace Biz\Category\Service;

interface CategoryGroupService
{
    public function getCategoryGroup($categoryGroupId);

    public function findAllCategoryGroups();

    public function searchCategoryGroups($conditions, $orderBy, $start, $limit);

    public function searchCategoryGroupsCount($conditions);

    public function createCategoryGroup($fields);

    public function updateCategoryGroup($categoryGroupId, $fields);

    public function deleteCategoryGroup($categoryGroupId);

    public function getCategoryGroupByCode($code);
}
