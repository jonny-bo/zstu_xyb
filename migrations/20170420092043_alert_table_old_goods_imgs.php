<?php

use Phpmig\Migration\Migration;

class AlertTableOldGoodsImgs extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $biz = $this->getContainer();
        $db  = $biz['db'];
        $db->exec("alter table old_goods  modify column imgs varchar(1024) NOT NULL DEFAULT  '' COMMENT  '图片集合';");
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $biz = $this->getContainer();
        $db  = $biz['db'];
        $db->exec("alter table old_goods  modify column imgs varchar(250);");
    }
}
