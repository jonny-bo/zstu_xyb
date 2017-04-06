<?php

use Phpmig\Migration\Migration;

class AlertGoodsDropThumb extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            ALTER TABLE `old_goods` DROP `thumb`;
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
            ALTER TABLE  `old_goods` ADD  `thumb` varchar(128) NOT NULL DEFAULT  '' COMMENT  '缩略图' AFTER  `title` ;
        ");
    }
}
