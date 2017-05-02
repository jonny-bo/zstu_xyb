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

class MyExpress extends BaseResource
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

        return $this->wrap($this->multicallFilter('Express/Express', $expresses), $total);
    }

    public function auth(Request $request, $expressId)
    {
        $this->checkRequiredFields(array('user_id'), $request->request->all());
        $userId = $request->request->get('user_id');
        $this->getExpressApplyService()->auth($expressId, $userId);

        return array('success' => 'true');
    }

    public function myPublish($expressId)
    {
        $express = $this->getExpressService()->getExpress($expressId);

        return $this->filter($express);
    }

    public function publishedConfirm($expressId)
    {
        $this->getExpressService()->confirmMyPublishExpress($expressId);

        return array('success' => 'true');
    }

    public function recivedConfirm($expressId)
    {
        $this->getExpressService()->confirmMyReceiveExpress($expressId);

        return array('success' => 'true');
    }

    public function filter($res)
    {
        $res = $this->callFilter('Express/Express', $res);

        if ($res['status'] == 1) {
            $res['applys'] = $this->findApplyUsers($res[id]);
        }

        return $res;
    }

    public function simplify($res)
    {
        return $res;
    }

    protected function findApplyUsers($expressId)
    {
        $applys = $this->getExpressApplyService()->findExpressApplyByExpressId($expressId);

        $userIds = ArrayToolkit::column($applys, 'user_id');
        $users   = $this->getUserService()->findUsersByIds($userIds);

        return  $this->multicallSimplify('User/User', $users);
    }

    protected function getExpressService()
    {
        return $this->biz->service('Express:ExpressService');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getExpressApplyService()
    {
        return $this->biz->service('Express:ExpressApplyService');
    }
}
