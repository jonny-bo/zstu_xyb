<?php

namespace Biz;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Biz\User\Event\UserEventSubscriber;

class EventSubscribersProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['dispatcher']->addSubscriber(new UserEventSubscriber($app));
    }
}
