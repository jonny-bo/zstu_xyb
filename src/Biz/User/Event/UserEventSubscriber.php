<?php

namespace Biz\User\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Codeages\Biz\Framework\Context\Biz;

class UserEventSubscriber implements EventSubscriberInterface
{
    public function __construct(Biz $biz)
    {
        $this->biz = $biz;
    }

    public static function getSubscribedEvents()
    {
        return array(
            UserEvents::CREDIT => 'onCredit',
            UserEvents::COIN => 'onCoin',
        );
    }

    public function onCredit(GenericEvent $event)
    {
        $userId = $event->getSubject();
        $rating = $event->getArgument('rating');
        $message = $event->getArgument('message');

        $credit = ($rating-2.0)*6; //每0.5积分3信誉度,默认2型为0

        $this->getUserService()->updateCredit($userId, $credit);
        $this->getUserInfoService()->recordCredit($userId, $message, $credit);
    }

    public function onCoin(GenericEvent $event)
    {
        $userId = $event->getSubject();
        $coin = $event->getArgument('coin');
        $message = $event->getArgument('message');

        $this->getUserInfoService()->recordBill($userId, $message, $coin);
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }

    protected function getUserInfoService()
    {
        return $this->biz->service('User:UserInfoService');
    }
}
