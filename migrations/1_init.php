<?php
use Phpmig\Migration\Migration;

class Init extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $container = $this->getContainer();

        $sql = "
         CREATE TABLE IF NOT EXISTS `batch_notification` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '群发通知id',
          `type` enum('text','image','video','audio') NOT NULL DEFAULT 'text' COMMENT '通知类型',
          `title` text NOT NULL COMMENT '通知标题',
          `from_id` int(10) unsigned NOT NULL COMMENT '发送人id',
          `content` text NOT NULL COMMENT '通知内容',
          `targetId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '通知发送对象ID',
          `createdTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送通知时间',
          `published` int(10) NOT NULL DEFAULT '0' COMMENT '是否已经发送',
          `sendedTime` int(10) NOT NULL DEFAULT '0' COMMENT '群发通知的发送时间',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='群发通知表' AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `category` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
          `code` varchar(64) NOT NULL DEFAULT '' COMMENT '分类编码',
          `name` varchar(255) NOT NULL COMMENT '分类名称',
          `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
          `path` varchar(255) NOT NULL DEFAULT '' COMMENT '分类完整路径',
          `weight` int(11) NOT NULL DEFAULT '0' COMMENT '分类权重',
          `group_id` int(10) unsigned NOT NULL COMMENT '分类组ID',
          `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父分类ID',
          `description` text,
          PRIMARY KEY (`id`),
          UNIQUE KEY `uri` (`code`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `category_group` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类组ID',
          `code` varchar(64) NOT NULL COMMENT '分类组编码',
          `name` varchar(255) NOT NULL COMMENT '分类组名称',
          `depth` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '该组下分类允许的最大层级数',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

        INSERT INTO `category_group` (`id`, `code`, `name`, `depth`) VALUES
        (1, 'goods', '旧货分类', 3);

        CREATE TABLE IF NOT EXISTS `express` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `publisher_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布用户id',
          `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '包裹规格（1:小包裹，2：中包裹，3：大包裹）',
          `address_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地址id',
          `title` varchar(256) NOT NULL DEFAULT '' COMMENT '标题',
          `detail` text NOT NULL COMMENT '包裹详情',
          `is_urgent` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否加急0否1是',
          `offer` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '悬赏',
          `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0:待认领，1:被认领，2:已送达，3:已确认',
          `receiver_id` int(10) NOT NULL DEFAULT '0' COMMENT '接受者id',
          `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `express_address` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(64) NOT NULL DEFAULT '' COMMENT '地址名',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='快递驿站地址' AUTO_INCREMENT=5 ;

        INSERT INTO `express_address` (`id`, `name`) VALUES
        (1, '生活一区'),
        (2, '生活二区'),
        (3, '创意市场'),
        (4, '二区报亭');

        CREATE TABLE IF NOT EXISTS `file` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '上传文件ID',
          `group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传文件组ID',
          `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传人ID',
          `uri` varchar(255) NOT NULL COMMENT '文件URI',
          `mime` varchar(255) NOT NULL COMMENT '文件MIME',
          `size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
          `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '文件状态',
          `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件上传时间',
          `upload_file_id` int(10) DEFAULT NULL COMMENT '上传文件id',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

        CREATE TABLE IF NOT EXISTS `file_group` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '上传文件组ID',
          `name` varchar(255) NOT NULL COMMENT '上传文件组名称',
          `code` varchar(255) NOT NULL COMMENT '上传文件组编码',
          `public` tinyint(4) NOT NULL DEFAULT '1' COMMENT '文件组文件是否公开',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

        CREATE TABLE IF NOT EXISTS `file_used` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `type` varchar(32) NOT NULL,
          `file_id` int(11) NOT NULL COMMENT 'upload_files id',
          `target_type` varchar(32) NOT NULL,
          `target_id` int(11) NOT NULL,
          `created_time` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `file_used_type_targetType_targetId_index` (`type`,`target_type`,`target_id`),
          KEY `file_used_type_targetType_targetId_fileId_index` (`type`,`target_type`,`target_id`,`file_id`),
          KEY `file_used_fileId_index` (`file_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `friend` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '关注ID',
          `from_id` int(10) unsigned NOT NULL COMMENT '关注人ID',
          `toId` int(10) unsigned NOT NULL COMMENT '被关注人ID',
          `pair` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为互加好友',
          `created_time` int(10) unsigned NOT NULL COMMENT '关注时间',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `groups` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '小组id',
          `title` varchar(100) NOT NULL COMMENT '小组名称',
          `about` text COMMENT '小组介绍',
          `logo` varchar(100) NOT NULL DEFAULT '' COMMENT 'logo',
          `background_logo` varchar(100) NOT NULL DEFAULT '',
          `status` enum('open','close') NOT NULL DEFAULT 'open',
          `member_num` int(10) unsigned NOT NULL DEFAULT '0',
          `thread_num` int(10) unsigned NOT NULL DEFAULT '0',
          `post_num` int(10) unsigned NOT NULL DEFAULT '0',
          `owner_id` int(10) unsigned NOT NULL COMMENT '小组组长id',
          `created_time` int(11) unsigned NOT NULL COMMENT '创建小组时间',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `groups_member` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '成员id主键',
          `group_id` int(10) unsigned NOT NULL COMMENT '小组id',
          `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
          `role` varchar(100) NOT NULL DEFAULT 'member',
          `post_num` int(10) unsigned NOT NULL DEFAULT '0',
          `thread_num` int(10) unsigned NOT NULL DEFAULT '0',
          `created_time` int(11) unsigned NOT NULL COMMENT '加入时间',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `groups_thread` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '话题id',
          `title` varchar(1024) NOT NULL COMMENT '话题标题',
          `content` text COMMENT '话题内容',
          `is_elite` int(11) unsigned NOT NULL DEFAULT '0',
          `is_stick` int(11) unsigned NOT NULL DEFAULT '0',
          `last_post_memberId` int(10) unsigned NOT NULL DEFAULT '0',
          `last_post_time` int(10) unsigned NOT NULL DEFAULT '0',
          `group_id` int(10) unsigned NOT NULL,
          `user_id` int(10) unsigned NOT NULL,
          `created_time` int(10) unsigned NOT NULL COMMENT '添加时间',
          `updated_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
          `post_num` int(10) unsigned NOT NULL DEFAULT '0',
          `status` enum('open','close') NOT NULL DEFAULT 'open',
          `hit_num` int(10) unsigned NOT NULL DEFAULT '0',
          `reward_coin` int(10) unsigned NOT NULL DEFAULT '0',
          `type` varchar(255) NOT NULL DEFAULT 'default',
          PRIMARY KEY (`id`),
          KEY `updatedTime` (`updated_time`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `groups_thread_collect` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id主键',
          `thread_id` int(11) unsigned NOT NULL COMMENT '收藏的话题id',
          `user_id` int(10) unsigned NOT NULL COMMENT '收藏人id',
          `created_time` int(10) unsigned NOT NULL COMMENT '收藏时间',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `groups_thread_goods` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `title` text NOT NULL,
          `description` text,
          `user_id` int(10) unsigned NOT NULL DEFAULT '0',
          `type` enum('content','attachment','postAttachment') NOT NULL,
          `thread_id` int(10) unsigned NOT NULL,
          `post_id` int(10) unsigned NOT NULL DEFAULT '0',
          `coin` int(10) unsigned NOT NULL,
          `file_id` int(10) unsigned NOT NULL DEFAULT '0',
          `hit_num` int(10) unsigned NOT NULL DEFAULT '0',
          `created_time` int(10) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `groups_thread_post` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id主键',
          `thread_id` int(11) unsigned NOT NULL COMMENT '话题id',
          `content` text NOT NULL COMMENT '回复内容',
          `user_id` int(10) unsigned NOT NULL COMMENT '回复人id',
          `from_user_id` int(10) unsigned NOT NULL DEFAULT '0',
          `post_id` int(10) unsigned DEFAULT '0',
          `created_time` int(10) unsigned NOT NULL COMMENT '回复时间',
          `adopt` int(10) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

        CREATE TABLE IF NOT EXISTS `groups_thread_trade` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `thread_id` int(10) unsigned DEFAULT '0',
          `goods_id` int(10) DEFAULT '0',
          `user_id` int(10) unsigned NOT NULL,
          `created_time` int(10) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `message` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '私信Id',
          `type` enum('text','image','video','audio') NOT NULL DEFAULT 'text' COMMENT '私信类型',
          `from_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发信人Id',
          `to_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收信人Id',
          `content` text NOT NULL COMMENT '私信内容',
          `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '私信发送时间',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `message_conversation` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '会话Id',
          `from_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发信人Id',
          `to_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收信人Id',
          `message_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '此对话的信息条数',
          `latest_message_user_Id` int(10) unsigned DEFAULT NULL COMMENT '最后发信人ID',
          `latest_message_time` int(10) unsigned NOT NULL COMMENT '最后发信时间',
          `latest_message_content` text NOT NULL COMMENT '最后发信内容',
          `latest_message_type` enum('text','image','video','audio') NOT NULL DEFAULT 'text' COMMENT '最后一条私信类型',
          `unread_num` int(10) unsigned NOT NULL COMMENT '未读数量',
          `created_time` int(10) unsigned NOT NULL COMMENT '会话创建时间',
          PRIMARY KEY (`id`),
          KEY `to_id_fromid` (`to_id`,`from_id`),
          KEY `to_id_latest_message_time` (`to_id`,`latest_message_time`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `message_relation` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息关联ID',
          `conversation_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联的会话ID',
          `message_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联的消息ID',
          `is_read` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否已读',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `notification` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '通知ID',
          `user_id` int(10) unsigned NOT NULL COMMENT '被通知的用户ID',
          `content` text COMMENT '通知内容',
          `batch_id` int(10) NOT NULL DEFAULT '0' COMMENT '群发通知表中的ID',
          `created_time` int(10) unsigned NOT NULL COMMENT '通知时间',
          `is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已读',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `second_hand_goods` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `title` varchar(128) NOT NULL DEFAULT '' COMMENT '旧货交易标题',
          `cover` varchar(128) NOT NULL DEFAULT '' COMMENT '旧货交易封面',
          `publisher_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '旧货交易发布者id',
          `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '栏目id',
          `body` text COMMENT '正文',
          `ups_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
          `post_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回复数',
          `hits` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点击数',
          `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
          `updated_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='旧货交易表' AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `upload_files` (
          `id` int(10) unsigned NOT NULL COMMENT '上传文件ID',
          `status` enum('uploading','ok') NOT NULL DEFAULT 'ok' COMMENT '文件上传状态',
          `hash_id` varchar(128) NOT NULL DEFAULT '' COMMENT '文件的HashID',
          `target_id` int(11) NOT NULL COMMENT '所存目标ID',
          `target_type` varchar(64) NOT NULL DEFAULT '' COMMENT '目标类型',
          `use_type` varchar(64) DEFAULT NULL COMMENT '文件使用的模块类型',
          `filename` varchar(1024) NOT NULL DEFAULT '' COMMENT '文件名',
          `ext` varchar(12) NOT NULL DEFAULT '' COMMENT '后缀',
          `file_size` bigint(20) NOT NULL DEFAULT '0' COMMENT '文件大小',
          `description` text,
          `is_public` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否公开文件',
          `can_download` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否可下载',
          `used_count` int(10) unsigned NOT NULL DEFAULT '0',
          `updated_time` int(10) unsigned DEFAULT '0' COMMENT '文件最后更新时间',
          `created_user_id` int(10) unsigned NOT NULL COMMENT '文件上传人',
          `created_time` int(10) unsigned NOT NULL COMMENT '文件上传时间',
          PRIMARY KEY (`id`),
          UNIQUE KEY `hashId` (`hash_id`(120))
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        CREATE TABLE IF NOT EXISTS `upload_files_collection` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `file_id` int(10) unsigned NOT NULL COMMENT '文件Id',
          `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏者',
          `created_time` int(10) unsigned NOT NULL,
          `updated_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文件收藏表' AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `upload_files_share` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `source_user_id` int(10) unsigned NOT NULL COMMENT '上传文件的用户ID',
          `target_user_id` int(10) unsigned NOT NULL COMMENT '文件分享目标用户ID',
          `is_active` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否有效',
          `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
          `updated_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `upload_files_share_history` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '系统ID',
          `source_user_id` int(10) NOT NULL COMMENT '分享用户的ID',
          `target_user_id` int(10) NOT NULL COMMENT '被分享的用户的ID',
          `is_active` tinyint(4) NOT NULL DEFAULT '0',
          `created_time` int(10) DEFAULT '0' COMMENT '创建时间',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

        CREATE TABLE IF NOT EXISTS `user` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
          `username` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
          `password` varchar(64) NOT NULL DEFAULT '' COMMENT '用户密码',
          `salt` varchar(32) NOT NULL DEFAULT '' COMMENT '密码SALT',
          `roles` varchar(1024) NOT NULL DEFAULT '' COMMENT '角色',
          `nickname` varchar(64) NOT NULL DEFAULT '' COMMENT '昵称',
          `email` varchar(128) NOT NULL DEFAULT '' COMMENT '邮箱',
          `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
          `point` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '积分',
          `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币',
          `login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
          `login_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '最后登录ip',
          `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
          `updated_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
          PRIMARY KEY (`id`),
          UNIQUE KEY `username` (`username`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
        ";

        $container['db']->exec($sql);
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $container = $this->getContainer();
        $container['db']->exec("DROP TABLE `user`");
        $container['db']->exec("DROP TABLE `batch_notification`");
        $container['db']->exec("DROP TABLE `category`");
        $container['db']->exec("DROP TABLE `category_group`");
        $container['db']->exec("DROP TABLE `express`");
        $container['db']->exec("DROP TABLE `express_address`");
        $container['db']->exec("DROP TABLE `file`");
        $container['db']->exec("DROP TABLE `file_group`");
        $container['db']->exec("DROP TABLE `file_used`");
        $container['db']->exec("DROP TABLE `friend`");
        $container['db']->exec("DROP TABLE `groups`");
        $container['db']->exec("DROP TABLE `groups_member`");
        $container['db']->exec("DROP TABLE `groups_thread`");
        $container['db']->exec("DROP TABLE `groups_thread_collect`");
        $container['db']->exec("DROP TABLE `groups_thread_goods`");
        $container['db']->exec("DROP TABLE `groups_thread_post`");
        $container['db']->exec("DROP TABLE `groups_thread_trade`");
        $container['db']->exec("DROP TABLE `message`");
        $container['db']->exec("DROP TABLE `message_relation`");
        $container['db']->exec("DROP TABLE `notification`");
        $container['db']->exec("DROP TABLE `second_hand_goods`");
        $container['db']->exec("DROP TABLE `upload_files`");
        $container['db']->exec("DROP TABLE `upload_files_collection`");
        $container['db']->exec("DROP TABLE `upload_files_share`");
        $container['db']->exec("DROP TABLE `upload_files_share_history`");
    }
}
