<?php

use Phpmig\Migration\Migration;

class AddTableCollection extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `collection` (
             `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
             `object_type` varchar(32) NOT NULL DEFAULT '' COMMENT '收藏对象',
             `object_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏对象id',
             `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏人',
             `created_time` int(10) NOT NULL DEFAULT '0' COMMENT '收藏时间',
             PRIMARY KEY (`id`),
             KEY `object_type` (`object_type`,`object_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
        $db->exec("DROP TABLE IF EXISTS `collection`");
    }
}
