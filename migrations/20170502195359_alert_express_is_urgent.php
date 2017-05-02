<?php

use Phpmig\Migration\Migration;

class AlertExpressIsUrgent extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $biz = $this->getContainer();
        $db  = $biz['db'];
        $db->exec("
            ALTER TABLE  `express` CHANGE  `is_urgent`  `is_urgent` TINYINT( 3 ) NOT NULL DEFAULT  '1' COMMENT  '是否加急0否1是';
        ");
    }

    /**
     * Undo the migration
     */
    public function down()
    {

    }
}
