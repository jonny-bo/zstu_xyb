<?php

use Phpmig\Migration\Migration;

class AlertTableOldGoodsStatus extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $biz = $this->getContainer();
        $db  = $biz['db'];
        $db->exec("
            ALTER TABLE  `old_goods` CHANGE  `status`  `status` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' COMMENT '状态(1:未发布，2:已发布, 3:已关闭)';
            ALTER TABLE  `express` CHANGE  `status`  `status` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' COMMENT '1:待认领，2:被认领，3:已送达，4:已确认';
        ");
    }

    /**
     * Undo the migration
     */
    public function down()
    {

    }
}
