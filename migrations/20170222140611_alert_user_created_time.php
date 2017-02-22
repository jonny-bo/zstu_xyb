<?php

use Phpmig\Migration\Migration;

class AlertUserCreatedTime extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            ALTER TABLE  `user` ADD  `created_ip` VARCHAR( 64 ) NOT NULL DEFAULT  '' COMMENT  '注册IP' AFTER  `login_ip`;
            ALTER TABLE  `user` ADD  `mobile` VARCHAR( 32 ) NOT NULL DEFAULT  '' COMMENT  '手机号' AFTER  `username` ;
            ALTER TABLE  `user` ADD  `email_verified` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '邮箱是否验证' AFTER  `email` ;
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
            ALTER TABLE `user` DROP COLUMN `login_ip`;
            ALTER TABLE `user` DROP COLUMN `mobile`;
            ALTER TABLE `user` DROP COLUMN `email_verified`;
        ");
    }
}
