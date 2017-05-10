<?php

use Phpmig\Migration\Migration;

class AddTableSettings extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `settings` (
             `id` int(11) NOT NULL AUTO_INCREMENT,
             `name` varchar(64) NOT NULL DEFAULT '',
             `value` varchar(1024) DEFAULT '',
             PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;
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
        $db->exec("DROP TABLE IF EXISTS `settings`");
    }
}
