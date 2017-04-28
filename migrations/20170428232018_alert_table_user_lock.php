<?php

use Phpmig\Migration\Migration;

class AlertTableUserLock extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $biz = $this->getContainer();
        $db  = $biz['db'];
        $db->exec("
            ALTER TABLE  `user` ADD  `locked` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否被禁止';
        ");
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $biz = $this->getContainer();
        $db  = $biz['db'];
        $db->exec("
            ALTER TABLE `user` DROP COLUMN `locked`;
        ");
    }
}
