<?php

namespace Topxia\Api\Resource\Express;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Api\Resource\BaseResource;
use AppBundle\Security\CurrentUser;

class Express extends BaseResource
{
    public function get($expressId)
    {
        $express = $this->getUserService()->getUser($expressId);

        if (empty($express)) {
            return $this->error(404, "快递(#{$expressId})不存在");
        }
        
        return $this->filter($express);
    }

    public function filter($res)
    {
        return $res;
    }

    public function simplify($res)
    {
        return $res;
    }

    protected function getExpressService()
    {
        return self::$biz->service('Express:ExpressService');
    }
}
