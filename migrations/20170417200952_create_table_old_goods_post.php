<?php

use Phpmig\Migration\Migration;

class CreateTableOldGoodsPost extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `old_goods_post` (
             `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
             `old_goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '旧货id',
             `content` text NOT NULL COMMENT '评论内容',
             `from_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论用户id',
             `to_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论目标用户id',
             `created_time` int(11) NOT NULL DEFAULT '0' COMMENT '评论时间',
             PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='旧货交易评论表';
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
        $db->exec("DROP TABLE IF EXISTS `old_goods_post`");
    }
}
