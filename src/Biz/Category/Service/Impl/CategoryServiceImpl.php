<?php

namespace Biz\Category\Service\Impl;

use Biz\Common\BaseService;
use Biz\Category\Service\CategoryService;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\TreeToolkit;

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

    public function findCategoryByGroupCode($code)
    {
        $group = $this->getCategoryGroupService()->getCategoryGroupByCode($code);
        return $this->getCategoryDao()->findByGroupId($group['id']);
    }

    public function getCategoryByCode($code)
    {
        return $this->getCategoryDao()->getByCode($code);
    }

    public function getCategoryTree($code)
    {
        $group = $this->getCategoryGroupService()->getCategoryGroupByCode($code);

        if (empty($group)) {
            throw new ResourceNotFoundException('分类', $code, '分类不存在');
        }

        $prepare = function ($categories) {
            $prepared = array();

            foreach ($categories as $category) {
                if (!isset($prepared[$category['parent_id']])) {
                    $prepared[$category['parent_id']] = array();
                }

                $prepared[$category['parent_id']][] = $category;
            }

            return $prepared;
        };

        $categories = $prepare($this->findCategoryByGroupCode($code));

        $tree = array();
        $this->makeCategoryTree($tree, $categories, 0);

        return $tree;
    }

    public function getCategoryStructureTree($code)
    {
        return TreeToolkit::makeTree($this->getCategoryTree($code), 'weight');
    }

    protected function makeCategoryTree(&$tree, &$categories, $parentId)
    {
        static $depth = 0;

        if (isset($categories[$parentId]) && is_array($categories[$parentId])) {
            foreach ($categories[$parentId] as $category) {
                $depth++;
                $category['depth'] = $depth;
                $tree[]            = $category;
                $this->makeCategoryTree($tree, $categories, $category['id']);
                $depth--;
            }
        }

        return $tree;
    }

    protected function getCategoryDao()
    {
        return $this->biz->dao('Category:CategoryDao');
    }

    protected function getCategoryGroupService()
    {
        return $this->biz->service('Category:CategoryGroupService');
    }
}
