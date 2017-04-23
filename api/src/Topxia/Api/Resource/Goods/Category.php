<?php

namespace Topxia\Api\Resource\Goods;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\ArrayToolkit;

class Category extends BaseResource
{
    public function all()
    {
        $categorys = $this->getCategoryService()->findCategoryByGroupCode('goods');
        return $this->multiFilter($categorys);
    }

    public function filter($res)
    {
        $data = array();
        $data['id']   = $res['id'];
        $data['name'] = $res['name'];
        $data['icon'] = $this->getFileUrl($res['icon']);

        return $data;
    }

    public function simplify($res)
    {
        $data = array();
        $data['id'] = $res['id'];
        $data['name'] = $res['name'];

        return $data;
    }

    protected function getCategoryService()
    {
        return $this->biz->service('Category:CategoryService');
    }
}
