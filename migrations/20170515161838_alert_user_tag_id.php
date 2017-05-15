<?php

use Phpmig\Migration\Migration;

class AlertUserTagId extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $biz = $this->getContainer();
        $db  = $biz['db'];
        $db->exec("
            ALTER TABLE  `user` ADD  `tag_id` VARCHAR( 64 ) NOT NULL DEFAULT  '' COMMENT  'Jush的标签id';
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
            ALTER TABLE `user` DROP COLUMN `tag_id`;
        ");
    }
}
