<?php

use Phpmig\Migration\Migration;

class AddTableOrders extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `orders` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单ID',
              `sn` varchar(32) NOT NULL COMMENT '订单编号',
              `status` enum('created','paid','refunding','refunded','cancelled') NOT NULL COMMENT '订单状态',
              `title` varchar(255) NOT NULL COMMENT '订单标题',
              `target_type` varchar(64) NOT NULL DEFAULT '' COMMENT '订单所属对象类型',
              `target_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单所属对象ID',
              `amount` float(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单实付金额',
              `total_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单总价',
              `token` varchar(50) DEFAULT NULL COMMENT '令牌',
              `refund_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次退款操作记录的ID',
              `user_id` int(10) unsigned NOT NULL COMMENT '订单创建人',
              `payment` varchar(32) NOT NULL DEFAULT 'none' COMMENT '订单支付方式',
              `coin_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '流币支付额',
              `coin_rate` float(10,2) NOT NULL DEFAULT '1.00' COMMENT '流币汇率',
              `paid_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
              `cash_sn` bigint(20) DEFAULT NULL COMMENT '支付流水号',
              `note` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
              `createdTime` int(10) unsigned NOT NULL COMMENT '订单创建时间',
              `updatedTime` int(10) NOT NULL  COMMENT '订单更新时间',
              PRIMARY KEY (`id`),
              UNIQUE KEY `sn` (`sn`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='订单表';

            CREATE TABLE IF NOT EXISTS `order_refund` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单退款记录ID',
              `order_id` int(10) unsigned NOT NULL COMMENT '退款订单ID',
              `user_id` int(10) unsigned NOT NULL COMMENT '退款人ID',
              `target_type` varchar(64) NOT NULL DEFAULT '' COMMENT '订单退款记录所属对象类型',
              `target_id` int(10) unsigned NOT NULL COMMENT '订单退款记录所属对象ID',
              `status` enum('created','success','failed','cancelled') NOT NULL DEFAULT 'created' COMMENT '退款状态',
              `expected_amount` float(10,2) unsigned DEFAULT '0.00' COMMENT '期望退款的金额，NULL代表未知，0代表不需要退款',
              `actual_amount` float(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实际退款金额，0代表无退款',
              `reason_type` varchar(64) NOT NULL DEFAULT '' COMMENT '退款理由类型',
              `reason_note` varchar(1024) NOT NULL DEFAULT '' COMMENT '退款理由',
              `updated_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单退款记录最后更新时间',
              `created_time` int(10) unsigned NOT NULL COMMENT '订单退款记录创建时间',
              `operator` int(11) NOT NULL COMMENT '操作人',
              UNIQUE KEY `id` (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='订单退款表';

            CREATE TABLE IF NOT EXISTS `order_log` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单日志ID',
              `order_id` int(10) unsigned NOT NULL COMMENT '订单ID',
              `type` varchar(32) NOT NULL COMMENT '订单日志类型',
              `message` text COMMENT '订单日志内容',
              `data` text COMMENT '订单日志数据',
              `user_id` int(10) unsigned NOT NULL COMMENT '订单操作人',
              `ip` varchar(255) NOT NULL COMMENT '订单操作IP',
              `created_time` int(10) unsigned NOT NULL COMMENT '订单日志记录时间',
              PRIMARY KEY (`id`),
              KEY `order_id` (`order_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单日志表';
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
            DROP TABLE IF EXISTS `orders`;
            DROP TABLE IF EXISTS `order_refund`;
            DROP TABLE IF EXISTS `order_log`;
        ");
    }
}
