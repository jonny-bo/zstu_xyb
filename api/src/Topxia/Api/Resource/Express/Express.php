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

class Express extends BaseResource
{
    public function get($expressId)
    {
        $express = $this->getExpressService()->getExpress($expressId);

        if (empty($express)) {
            return $this->error(404, "快递(#{$expressId})不存在");
        }
        
        return $this->filter($express);
    }

    public function search(Request $request)
    {
        $conditions = $request->query->all();

        $start = $request->query->get('start', 0);
        $limit = $request->query->get('limit', 10);

        $expresses = $this->getExpressService()->searchExpress($conditions, array('created_time' =>'DESC'), $start, $limit);
        $total = $this->getExpressService()->searchExpressCount($conditions);

        return $this->wrap($this->multiFilter($expresses), $total);
    }

    public function create(Request $request)
    {
        $fields = $request->request->all();

        $this->getExpressService()->createExpress($fields);

        return array('success' => 'true');
    }

    public function update($expressId, Request $request)
    {
        $fields  = $request->request->all();

        $this->getExpressService()->updateExpress($expressId, $fields);

        return array('success' => 'true');
    }

    public function order($expressId)
    {
        $this->getExpressService()->orderExpress($expressId);

        return array('success' => 'true');
    }

    public function cancel($expressId)
    {
        $this->getExpressService()->deleteExpress($expressId);

        return array('success' => 'true');
    }

    public function filter($res)
    {
        $res['publisher'] = $this->callSimplify('User/User', $this->getUserService()->getUser($res['publisher_id']));
        $res['created_time'] = date("m-d h:i", $res['created_time']);

        if ($res['receiver_id']) {
            $res['receiver'] = $this->callSimplify('User/User', $this->getUserService()->getUser($res['receiver_id']));
            
            unset($res['receiver_id']);
        }

        unset($res['publisher_id']);

        return $res;
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
