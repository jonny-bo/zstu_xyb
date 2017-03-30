<?php

use Phpmig\Migration\Migration;

class AlterOldGoodsStatus extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            ALTER TABLE  `old_goods` ADD  `status` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '状态(0:未发布，1:已发布, 2:已关闭)' AFTER `body` ;
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
            ALTER TABLE `old_goods` DROP COLUMN `status`;
        ");
    }
}
