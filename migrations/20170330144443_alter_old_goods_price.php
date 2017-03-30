<?php

use Phpmig\Migration\Migration;

class AlterOldGoodsPrice extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            ALTER TABLE  `old_goods` ADD  `price` FLOAT( 10, 2 ) UNSIGNED NOT NULL DEFAULT  '0.00' COMMENT  '价格' AFTER  `body` ;
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
            ALTER TABLE `old_goods` DROP COLUMN `price`;
        ");
    }
}
