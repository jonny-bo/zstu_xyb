<?php

use Phpmig\Migration\Migration;

class AddTableExpressApply extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `express_apply` (
             `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
             `express_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '快递id',
             `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申请人',
             `is_auth` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否授权',
             `created_time` int(11) NOT NULL DEFAULT '0' COMMENT '申请时间',
             PRIMARY KEY (`id`)
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
        $db->exec("DROP TABLE IF EXISTS `express_apply`");
    }
}
