<?php

use Phpmig\Migration\Migration;

class AddTableLog extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `log` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志ID',
              `username` varchar(64) NOT NULL DEFAULT '' COMMENT '操作用户名',
              `module` varchar(16) NOT NULL DEFAULT '' COMMENT '模块',
              `module_id` int(11) NOT NULL DEFAULT '0' COMMENT '模块ID',
              `operation` varchar(255) NOT NULL DEFAULT '' COMMENT '操作类型',
              `message` text NOT NULL COMMENT '日志内容',
              `old_data` text COMMENT '旧数据',
              `new_data` text COMMENT '新数据',
              `created_time` int(10) NOT NULL DEFAULT '0' COMMENT '操作时间',
              `level` varchar(10) NOT NULL DEFAULT '' COMMENT '日志等级',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='系统日志表';
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
        $db->exec("DROP TABLE IF EXISTS `log`");
    }
}
