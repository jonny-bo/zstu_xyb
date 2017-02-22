<?php

use Phpmig\Migration\Migration;

class CreateUserTokenTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS `user_token` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'TOKEN编号',
          `token` varchar(64) NOT NULL COMMENT 'TOKEN值',
          `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'TOKEN关联的用户ID',
          `type` varchar(255) NOT NULL COMMENT 'TOKEN类型',
          `data` text NOT NULL COMMENT 'TOKEN数据',
          `times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'TOKEN的校验次数限制(0表示不限制)',
          `remained_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'TOKE剩余校验次数',
          `expired_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'TOKEN过期时间',
          `created_time` int(10) unsigned NOT NULL COMMENT 'TOKEN创建时间',
          PRIMARY KEY (`id`),
          UNIQUE KEY `token` (`token`(60))
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
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
        $db->exec("DROP TABLE IF EXISTS `user_token`");
    }
}
