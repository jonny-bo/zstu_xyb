<?php

use AppBundle\Security\CurrentUser;
use Codeages\Biz\Framework\Service\Exception\AccessDeniedException;
use Codeages\Biz\Framework\Service\Exception\NotFoundException;

function filter($data, $type, $biz)
{
    $class = 'Topxia\\Api\\Filter\\' .  ucfirst($type) . 'Filter';
    $filter = new $class($biz);
    return $filter->filter($data);
}

function filters($datas, $type, $biz)
{
    $class = 'Topxia\\Api\\Filter\\' .  ucfirst($type) . 'Filter';
    $filter = new $class($biz);
    return $filter->filters($datas);
}

function convert($data, $type)
{
    $class = 'Topxia\\Api\\Convert\\' .  ucfirst($type) . 'Convert';
    $convert = new $class();
    return $convert->convert($data);
}

function createAccessDeniedException($message = 'Access Denied', $code = 0)
{
    return new AccessDeniedException($message, null, $code);
}

function createNotFoundException($message = 'Not Found', $code = 0)
{
    return new NotFoundException($message, $code);
}
