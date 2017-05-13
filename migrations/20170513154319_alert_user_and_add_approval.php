<?php

use Phpmig\Migration\Migration;

class AlertUserAndAddApproval extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `user_approval` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
              `truename` varchar(32) NOT NULL DEFAULT '' COMMENT '真实姓名',
              `idcard` varchar(64) NOT NULL DEFAULT '' COMMENT '身份证号',
              `student_card_img` varchar(255) NOT NULL DEFAULT '' COMMENT '学生证照片',
              `note` text COMMENT '认证信息',
              `status` enum('unapprove','approving','approved','approve_fail') NOT NULL DEFAULT 'unapprove' COMMENT '认证状态',
              `operator_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核人',
              `created_time` int(11) NOT NULL DEFAULT '0' COMMENT '申请时间',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='实名认证' AUTO_INCREMENT=1 ;
            ALTER TABLE  `user` ADD  `pay_password` VARCHAR( 64 ) NOT NULL DEFAULT  '' COMMENT  '支付密码' AFTER  `coin` ,
            ADD  `pay_password_salt` VARCHAR( 64 ) NOT NULL DEFAULT  '' COMMENT  '支付密码salt' AFTER  `pay_password` ,
            ADD  `qq` VARCHAR( 32 ) NOT NULL DEFAULT  '' COMMENT  'qq' AFTER  `pay_password_salt` ,
            ADD  `birthday` DATE NULL DEFAULT NULL COMMENT  '生日'  AFTER  `qq` ,
            ADD  `credit` INT NOT NULL DEFAULT  '300' COMMENT  '信用度' AFTER  `birthday` ;
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
            DROP TABLE IF EXISTS `user_approval`;
            ALTER TABLE `user` DROP COLUMN `pay_password`;
            ALTER TABLE `user` DROP COLUMN `pay_password_salt`;
            ALTER TABLE `user` DROP COLUMN `qq`;
            ALTER TABLE `user` DROP COLUMN `birthday`;
            ALTER TABLE `user` DROP COLUMN `credit`;
        ");
    }
}
