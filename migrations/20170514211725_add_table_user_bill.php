<?php

use Phpmig\Migration\Migration;

class AddTableUserBill extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
                CREATE TABLE IF NOT EXISTS `user_bill` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                  `message` varchar(256) NOT NULL COMMENT '消息',
                  `up` tinyint(4) NOT NULL DEFAULT '0' COMMENT '流币浮动数值',
                  `created_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
                  PRIMARY KEY (`id`)
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
        $db->exec("
            DROP TABLE IF EXISTS `user_bill`;
        ");
    }
}
