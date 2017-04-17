<?php

use Phpmig\Migration\Migration;

class CreateTableOldGoodsLike extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `old_goods_like` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞用户',
                `old_goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞旧货',
                `created_time` int(11) NOT NULL DEFAULT '0' COMMENT '点赞时间',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='旧货交易点赞表';
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
        $db->exec("DROP TABLE IF EXISTS `old_goods_like`");
    }
}
