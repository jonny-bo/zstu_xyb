<?php

namespace Biz;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Biz\User\Event\UserEventSubscriber;
use Biz\Order\Event\OrderEventSubscriber;

class EventSubscribersProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['dispatcher']->addSubscriber(new UserEventSubscriber($app));
        $app['dispatcher']->addSubscriber(new OrderEventSubscriber($app));
    }
}
