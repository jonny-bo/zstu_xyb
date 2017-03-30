<?php

use Phpmig\Migration\Migration;

class CreateTableComment extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS `comment` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `object_type` varchar(32) NOT NULL,
          `object_id` int(10) unsigned NOT NULL,
          `user_id` int(10) unsigned NOT NULL DEFAULT '0',
          `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
          `content` text NOT NULL,
          `created_time` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id`),
          KEY `object_type` (`object_type`,`object_id`)
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
        $db->exec("DROP TABLE IF EXISTS `comment`");
    }
}
