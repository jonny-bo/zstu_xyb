<?php

use Phpmig\Migration\Migration;

class AlertGroupThreadImgsAndLastMember extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $biz = $this->getContainer();
        $db  = $biz['db'];
        $db->exec("
            ALTER TABLE  `groups_thread` CHANGE  `last_post_memberId`  `last_post_member_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT '最后评论人id';
            ALTER TABLE  `groups_thread` ADD  `imgs` VARCHAR( 1024 ) NOT NULL DEFAULT  '' COMMENT  '话题图片' AFTER  `content` ;
        ");
    }

    /**
     * Undo the migration
     */
    public function down()
    {
    }
}
