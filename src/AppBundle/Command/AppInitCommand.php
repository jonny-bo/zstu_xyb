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

        $output->write('  初始化文件组信息');
        $this->initFileGroup();
        $output->writeln(' ...<info>成功</info>');

        $output->write('  初始化分类信息');
        $this->initCategory();
        $output->writeln(' ...<info>成功</info>');

        $output->writeln('<info>初始化系统完毕</info>');
    }

    protected function initCategory()
    {
        $groups = $this->getCategoryGroupService()->findAllCategoryGroups();

        foreach ($groups as $group) {
            $this->getCategoryGroupService()->deleteCategoryGroup($group['id']);
        }

        $this->getCategoryGroupService()->createCategoryGroup(array(
            'name'   => '默认分类',
            'code'   => 'default',
            'depth' => 3
        ));

        $this->getCategoryGroupService()->createCategoryGroup(array(
            'name'   => '旧货分类',
            'code'   => 'goods',
            'depth' => 3
        ));

        $this->getCategoryGroupService()->createCategoryGroup(array(
            'name'   => '论坛分类',
            'code'   => 'blog',
            'depth' => 3
        ));

        $this->getCategoryGroupService()->createCategoryGroup(array(
            'name'   => '快递分类',
            'code'   => 'express',
            'depth' => 1
        ));
    }

    protected function initFileGroup()
    {
        $groups = $this->getFileGroupService()->findAllFileGroups();

        foreach ($groups as $group) {
            $this->getFileService()->deleteFileGroup($group['id']);
        }

        $this->getFileGroupService()->createFileGroup(array(
            'name'   => '默认文件组',
            'code'   => 'default',
            'public' => 1
        ));

        $this->getFileGroupService()->createFileGroup(array(
            'name'   => '缩略图',
            'code'   => 'thumb',
            'public' => 1
        ));

        $this->getFileGroupService()->createFileGroup(array(
            'name'   => '旧货交易',
            'code'   => 'course',
            'public' => 1
        ));

        $this->getFileGroupService()->createFileGroup(array(
            'name'   => '用户',
            'code'   => 'user',
            'public' => 1
        ));

        $this->getFileGroupService()->createFileGroup(array(
            'name'   => '用户私有文件',
            'code'   => 'user_private',
            'public' => 0
        ));

        $this->getFileGroupService()->createFileGroup(array(
            'name'   => '临时目录',
            'code'   => 'tmp',
            'public' => 1
        ));

        $this->getFileGroupService()->createFileGroup(array(
            'name'   => '全局设置文件',
            'code'   => 'system',
            'public' => 1
        ));

        $this->getFileGroupService()->createFileGroup(array(
            'name'   => '小组',
            'code'   => 'group',
            'public' => 1
        ));

        $this->getFileGroupService()->createFileGroup(array(
            'name'   => '话题',
            'code'   => 'thread',
            'public' => 1
        ));

        $this->getFileGroupService()->createFileGroup(array(
            'name'   => '分类',
            'code'   => 'category',
            'public' => 1
        ));
    }

    protected function getUserService()
    {
        return $this->getApplication()->getKernel()->getContainer()->get('biz')->service('User:UserService');
    }

    protected function getFileGroupService()
    {
        return $this->getApplication()->getKernel()->getContainer()->get('biz')->service('File:FileGroupService');
    }

    public function getCategoryGroupService()
    {
        return $this->getApplication()->getKernel()->getContainer()->get('biz')->service('Category:CategoryGroupService');
    }
}
