<?php

namespace Topxia\Api\Resource\Express;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\RuntimeException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\ArrayToolkit;

class MyExpresses extends BaseResource
{
    /*
    ## 分页获取用户发起和接收的快递

    GET /my/expresses

     ** 参数 **

    | 名称  | 类型  | 必需   | 说明 |
    | ---- | ----- | ----- | ---- |
     type    string    yes   type = (publisher_id获取用户发起, receiver_id获取用户接收)
     ** 响应 **

    ```
    {
    'resources': [
    datalist
    ],
    "total": {total}
    }
    ```
    */
    public function get(Request $request)
    {
        $conditions = $request->query->all();
        $user       = $this->getCurrentUser();
        $start      = $request->query->get('start', 0);
        $limit      = $request->query->get('limit', 10);

        if (!isset($conditions['type']) || !in_array($conditions['type'], array('publisher_id', 'receiver_id'))) {
            return $this->error(500, '查询参数错误');
        }

        $conditions[$conditions['type']] = $user['id'];
        unset($conditions['type']);

        $expresses = $this->getExpressService()->searchExpress($conditions, array('created_time' =>'DESC'), $start, $limit);
        $total = $this->getExpressService()->searchExpressCount($conditions);

        return $this->wrap($this->filter($expresses), $total);
    }

    
    public function filter($res)
    {
        return $this->multicallFilter('Express/Express', $res);
    }

    public function simplify($res)
    {
        return $res;
    }

    protected function getExpressService()
    {
        return $this->biz->service('Express:ExpressService');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}
