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

class Expresses extends BaseResource
{
    public function get(Request $request)
    {
        $conditions = $request->query->all();

        $start = $request->query->get('start', 0);
        $limit = $request->query->get('limit', 10);

        $expresses = $this->getExpressService()->searchExpress($conditions, array('created_time' =>'DESC'), $start, $limit);
        $total = $this->getExpressService()->searchExpressCount($conditions);

        return $this->wrap($this->filter($expresses), $total);
    }

    public function post(Request $request)
    {
        $fields = $request->request->all();

        if (!ArrayToolkit::requireds($fields, array('title', 'detail', 'offer'))) {
            throw new RuntimeException('缺少必填字段');
        }

        $express = $this->getExpressService()->createExpress($fields);

        return $express;
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
