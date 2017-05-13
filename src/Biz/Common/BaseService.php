<?php

namespace Biz\Common;

use Codeages\Biz\Framework\Service\BaseService as ParentService;
use Symfony\Component\EventDispatcher\GenericEvent;
use Biz\Util\HTMLPurifierFactory;

class BaseService extends ParentService
{
    protected $lock;

    protected function getDispatcher()
    {
        return $this->biz['dispatcher'];
    }

    protected function dispatchEvent($eventName, $subject = null, array $arguments = array())
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

    protected function purifyHtml($html, $trusted = false)
    {
        if (empty($html)) {
            return '';
        }

        $config = array(
            'cacheDir' => $this->biz['cache_directory'].'/htmlpurifier'
        );

        $factory  = new HTMLPurifierFactory($config);
        $purifier = $factory->create($trusted);

        return $purifier->purify($html);
    }
}
