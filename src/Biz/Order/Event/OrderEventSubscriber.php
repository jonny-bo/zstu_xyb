<?php

namespace Biz\Order\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Codeages\Biz\Framework\Context\Biz;

class OrderEventSubscriber implements EventSubscriberInterface
{
    public function __construct(Biz $biz)
    {
        $this->biz = $biz;
    }

    public static function getSubscribedEvents()
    {
        return array(
            OrderEvents::CREATED => 'onCreate'
        );
    }
}
