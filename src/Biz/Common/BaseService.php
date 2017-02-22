<?php

namespace Biz\Common;

use Codeages\Biz\Framework\Service\BaseService as ParentService;
use Symfony\Component\EventDispatcher\GenericEvent;

class BaseService extends ParentService
{
    protected function getDispatcher()
    {
        return $this->biz['dispatcher'];
    }

    protected function dispatchEvent($eventName, $subject, $arguments = array())
    {
        $event = new GenericEvent($subject, $arguments);

        return $this->getDispatcher()->dispatch($eventName, $event);
    }
}
