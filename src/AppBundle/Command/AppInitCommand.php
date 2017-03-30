<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppInitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:init')
            ->setDescription('Init application.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>开始初始化系统</info>');
        $user = array(
            'username' => 'admin',
            'nickname' => 'admin',
            'password' => 'kaifazhe',
            'email'     => 'test@admin.com',
            'roles' => array('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_USER'),
        );

        $this->getUserService()->register($user);

        $output->writeln([
            "管理员 用户名: {$user['username']}",
            "管理员 密码: {$user['password']}",
        ]);
        $output->writeln('<info>初始化系统完毕</info>');
    }

    protected function getUserService()
    {
        return $this->getApplication()->getKernel()->getContainer()->get('biz')->service('User:UserService');
    }
}
