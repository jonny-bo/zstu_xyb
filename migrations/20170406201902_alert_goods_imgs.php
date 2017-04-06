<?php

use Phpmig\Migration\Migration;

class AlertGoodsImgs extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            ALTER TABLE  `old_goods` ADD  `imgs` varchar(64) NOT NULL DEFAULT  '' COMMENT  '图片集合' AFTER  `body` ;
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
            ALTER TABLE `old_goods` DROP COLUMN `imgs`;
        ");
    }
}
