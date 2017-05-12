<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CrontabScheduleCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('crontab:schedule')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setDisableWebCrontab();
        $logger = $this->getContainer()->get('logger');
        $logger->info('Crontab:开始执行定时任务');
        $this->initBiz();
        putenv('IS_RUN_BY_COMMAND=true');
        $this->getCrontabService()->scheduleJobs();
        $logger->info('Crontab:定时任务执行完毕');
    }

    protected function setDisableWebCrontab()
    {
        $setting = $this->getSettingService()->get('magic');
        $setting = json_decode($setting, true);
        if (empty($setting['disable_web_crontab'])) {
            $setting['disable_web_crontab'] = 1;
            $this->getSettingService()->set('magic', json_encode($setting));
        }
    }

    public function getSettingService()
    {
        return $this->getBiz()->service('System:SettingService');
    }

    public function getCrontabService()
    {
        return $this->getBiz()->service('Crontab:CrontabService');
    }
}
