<?php

namespace Biz\Common;

use Codeages\Biz\Framework\Service\BaseService as ParentService;
use Symfony\Component\EventDispatcher\GenericEvent;

class BaseService extends ParentService
{
    protected $lock;

    protected function getDispatcher()
    {
        return $this->biz['dispatcher'];
    }

    protected function dispatchEvent($eventName, $subject, $arguments = array())
    {
        $event = new GenericEvent($subject, $arguments);

        return $this->getDispatcher()->dispatch($eventName, $event);
    }

    protected function getCurrentUser()
    {
        return $this->biz['user'];
    }

    protected function getLock()
    {
        if (!$this->lock) {
            $this->lock = new Lock($this->biz);
        }

        return $this->lock;
    }
}
