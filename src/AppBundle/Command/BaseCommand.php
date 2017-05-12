<?php
namespace AppBundle\Command;

use AppBundle\Security\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

abstract class BaseCommand extends ContainerAwareCommand
{
    protected function getBiz()
    {
        return $this->getApplication()->getKernel()->getContainer()->get('biz');
    }

    protected function initBiz()
    {
        $biz = $this->getBiz();

        $user = $this->getUserService()->getUserByUsername('admin');
        $currentUser = new CurrentUser($user);

        $biz['user'] = $currentUser;
    }

    protected function getUserService()
    {
        return $this->getBiz()->service('User:UserService');
    }

    protected function getContainer()
    {
        return $this->getApplication()->getKernel()->getContainer();
    }
}
