<?php

namespace Topxia\Api\Resource\Collection;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\ArrayToolkit;

class Collection extends BaseResource
{
    public function post(Request $request)
    {
        $fields = $request->request->all();
        $fields['user_id'] = $this->getCurrentUser()['id'];
        $this->checkRequiredFields(array('object_type', 'object_id'), $fields);

        $this->getCollectionService()->createCollection($fields);

        return array('success' => 'true');
    }

    public function delete($id)
    {
        $this->getCollectionService()->deleteCollection($id);

        return array('success' => 'true');
    }

    public function get(Request $request)
    {
        $conditions = $request->query->all();

        if (!isset($conditions['object_type']) || empty($conditions['object_type'])) {
            return $this->error(500, '查询参数错误');
        }

        if (!isset($conditions['user_id']) || empty($conditions['user_id'])) {
            $conditions['user_id'] = $this->getCurrentUser()['id'];
        }

        $start = $request->query->get('start', 0);
        $limit = $request->query->get('limit', 10);

        $collections = $this->getCollectionService()->searchCollection($conditions, array('created_time' =>'DESC'), $start, $limit);
        $total = $this->getCollectionService()->searchCollectionCount($conditions);

        return $this->wrap($this->multiFilter($collections), $total);
    }

    public function filter($res)
    {
        $type = ucfirst($res['object_type']);
        $className = "get".$type."Service";
        $action     = "get".$type;
        $res = $this->$className()->$action($res['object_id']);

        return $this->callFilter($type."/".$type, $res);
    }

    public function simplify($res)
    {
        return $res;
    }

    protected function getCollectionService()
    {
        return $this->biz->service('Collection:CollectionService');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getExpressService()
    {
        return $this->biz->service('Express:ExpressService');
    }

    protected function getGoodsService()
    {
        return $this->biz->service('Goods:GoodsService');
    }
}
