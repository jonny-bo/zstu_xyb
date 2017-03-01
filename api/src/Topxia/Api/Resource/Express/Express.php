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

    public function post($expressId)
    {
        $express = $this->getExpressService()->orderExpress($expressId);

        return $this->filter($express);
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
