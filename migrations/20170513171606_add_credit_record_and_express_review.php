<?php

use Phpmig\Migration\Migration;

class AddCreditRecordAndExpressReview extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
                CREATE TABLE IF NOT EXISTS `user_credit_record` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                  `message` varchar(256) NOT NULL COMMENT '消息',
                  `up` tinyint(4) NOT NULL DEFAULT '0' COMMENT '信誉度浮动',
                  `created_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
                CREATE TABLE IF NOT EXISTS `express_review` (
                 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '课程评价ID',
                 `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评价人ID',
                 `express_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '被评价的快递ID',
                 `title` varchar(255) NOT NULL DEFAULT '' COMMENT '评价标题',
                 `content` text NOT NULL COMMENT '评论内容',
                 `rating` float(2,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '评分',
                 `private` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
                 `created_time` int(10) unsigned NOT NULL COMMENT '评价创建时间',
                 `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '回复ID',
                 `updated_time` int(10) DEFAULT NULL,
                 `meta` text COMMENT '评价元信息',
                 PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='评价表';
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
            DROP TABLE IF EXISTS `user_credit_record`;
            DROP TABLE IF EXISTS `express_review`;
        ");
    }
}
