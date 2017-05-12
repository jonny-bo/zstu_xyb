<?php

use Phpmig\Migration\Migration;

class AddTableCrontabJob extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `crontab_job` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
              `name` varchar(1024) NOT NULL COMMENT '任务名称',
              `cycle` enum('once','everyhour','everyday','everymonth') NOT NULL DEFAULT 'once' COMMENT '任务执行周期',
              `cycle_time` varchar(255) NOT NULL DEFAULT '0' COMMENT '任务执行时间',
              `job_class` varchar(1024) NOT NULL COMMENT '任务的Class名称',
              `job_params` text COMMENT '任务参数',
              `target_type` varchar(64) NOT NULL DEFAULT '',
              `target_id` int(10) unsigned NOT NULL DEFAULT '0',
              `executing` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '任务执行状态',
              `next_excuted_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '任务下次执行的时间',
              `latest_executed_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '任务最后执行的时间',
              `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '任务创建人',
              `created_time` int(10) unsigned NOT NULL COMMENT '任务创建时间',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
        ";
        $biz = $this->getContainer();
        $db  = $biz['db'];
        $db->exec($sql);
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $biz = $this->getContainer();
        $db  = $biz['db'];
        $db->exec("DROP TABLE IF EXISTS `crontab_job`");
    }
}
