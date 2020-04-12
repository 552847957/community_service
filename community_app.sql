/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 5.5.53 : Database - community_app
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`community_app` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `community_app`;

/*Table structure for table `u_activity` */

DROP TABLE IF EXISTS `u_activity`;

CREATE TABLE `u_activity` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_cover` varchar(200) DEFAULT NULL,
  `u_url` varchar(100) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_views` int(11) DEFAULT '0',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_activity` */


/*Table structure for table `u_calendar` */

DROP TABLE IF EXISTS `u_calendar`;

CREATE TABLE `u_calendar` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_take_date` varchar(11) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_calendar` */

/*Table structure for table `u_cate` */

DROP TABLE IF EXISTS `u_cate`;

CREATE TABLE `u_cate` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_name` varchar(30) DEFAULT NULL COMMENT '技工名字',
  `u_code` varchar(30) DEFAULT NULL COMMENT '技工code',
  `u_order` int(11) DEFAULT '0',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_cate` */


/*Table structure for table `u_contact_to_location` */

DROP TABLE IF EXISTS `u_contact_to_location`;

CREATE TABLE `u_contact_to_location` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_global_contact_id` int(11) DEFAULT NULL COMMENT '百宝箱的id',
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_house_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_contact_to_location` */


/*Table structure for table `u_global_cate` */

DROP TABLE IF EXISTS `u_global_cate`;

CREATE TABLE `u_global_cate` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_name` varchar(50) DEFAULT NULL,
  `u_code` varchar(50) DEFAULT NULL,
  `u_order` int(11) DEFAULT '0',
  `u_show` tinyint(1) DEFAULT '0' COMMENT '0,折叠；1，展开',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_global_cate` */


/*Table structure for table `u_global_contact` */

DROP TABLE IF EXISTS `u_global_contact`;

CREATE TABLE `u_global_contact` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_name` varchar(50) DEFAULT NULL,
  `u_phone` varchar(20) DEFAULT NULL,
  `u_cate_code` varchar(50) DEFAULT NULL,
  `u_service_time` varchar(100) DEFAULT NULL COMMENT '服务时间',
  `u_mark` varchar(100) DEFAULT NULL COMMENT '备注',
  `u_public` tinyint(1) DEFAULT '1' COMMENT '0,公共；1，私有',
  `u_create_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_global_contact` */


/*Table structure for table `u_global_contact_comment` */

DROP TABLE IF EXISTS `u_global_contact_comment`;

CREATE TABLE `u_global_contact_comment` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_global_contact_id` int(11) DEFAULT NULL,
  `u_content` varchar(400) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_user_id` int(11) DEFAULT NULL,
  `u_parent_id` int(11) DEFAULT '0',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_global_contact_comment` */

/*Table structure for table `u_global_contact_extra` */

DROP TABLE IF EXISTS `u_global_contact_extra`;

CREATE TABLE `u_global_contact_extra` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_global_contact_id` int(11) DEFAULT NULL,
  `u_icon` varchar(200) DEFAULT NULL,
  `u_lng` varchar(50) DEFAULT NULL,
  `u_lat` varchar(50) DEFAULT NULL,
  `u_pic` varchar(1000) DEFAULT NULL,
  `u_views` int(11) DEFAULT '0' COMMENT '浏览量',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_global_contact_extra` */


/*Table structure for table `u_house` */

DROP TABLE IF EXISTS `u_house`;

CREATE TABLE `u_house` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_name` varchar(50) DEFAULT NULL,
  `u_icon` varchar(200) DEFAULT NULL,
  `u_users` int(11) DEFAULT '0',
  `u_views` int(11) DEFAULT NULL,
  `u_admin_user_id` int(11) DEFAULT NULL COMMENT '创建人',
  `u_lat` varchar(50) DEFAULT NULL,
  `u_lng` varchar(50) DEFAULT NULL,
  `u_mark` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_house` */


/*Table structure for table `u_message` */

DROP TABLE IF EXISTS `u_message`;

CREATE TABLE `u_message` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_user_id` int(11) DEFAULT NULL,
  `u_content` varchar(1000) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_message` */


/*Table structure for table `u_notice` */

DROP TABLE IF EXISTS `u_notice`;

CREATE TABLE `u_notice` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_title` varchar(200) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_url` varchar(500) DEFAULT NULL,
  `u_house_id` int(11) DEFAULT NULL,
  `u_public` tinyint(1) DEFAULT '1' COMMENT '0,公开，1不公开',
  `u_content` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_notice` */


/*Table structure for table `u_order` */

DROP TABLE IF EXISTS `u_order`;

CREATE TABLE `u_order` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_code` varchar(32) DEFAULT NULL,
  `u_good_id` int(11) DEFAULT NULL COMMENT '0，标示VIP产品的',
  `u_user_id` int(11) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_state` tinyint(1) DEFAULT '0' COMMENT '0,创建，1，已支付，2，未支付，3，失败,4,支付中，5，已结单',
  `u_total_price` varchar(20) DEFAULT NULL COMMENT '实付金额',
  `u_pay_time` varchar(20) DEFAULT NULL COMMENT '支付时间',
  `u_mark` varchar(200) DEFAULT NULL,
  `u_number` int(11) DEFAULT '1' COMMENT '订单商品数量',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_order` */


/*Table structure for table `u_order_location` */

DROP TABLE IF EXISTS `u_order_location`;

CREATE TABLE `u_order_location` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_order_id` int(11) DEFAULT NULL,
  `u_user_order_location_id` int(11) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_order_location` */


/*Table structure for table `u_report` */

DROP TABLE IF EXISTS `u_report`;

CREATE TABLE `u_report` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_from_user_id` int(11) DEFAULT NULL,
  `u_to_user_id` int(11) DEFAULT NULL,
  `u_content` varchar(100) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_report` */


/*Table structure for table `u_role` */

DROP TABLE IF EXISTS `u_role`;

CREATE TABLE `u_role` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_name` varchar(20) DEFAULT NULL COMMENT '角色名',
  `u_code` varchar(20) DEFAULT NULL COMMENT '角色code',
  `u_icon` varchar(5) DEFAULT '0' COMMENT '角色icon，1，黄V（普通vip），2，红V（超级VIP），3，绿V（普通技工VIP），4，蓝V（普通机构VIP）',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_role` */


/*Table structure for table `u_shop_good` */

DROP TABLE IF EXISTS `u_shop_good`;

CREATE TABLE `u_shop_good` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_name` varchar(50) DEFAULT NULL COMMENT '商品名称',
  `u_now_price` varchar(20) DEFAULT NULL COMMENT '现价',
  `u_past_price` varchar(20) DEFAULT NULL COMMENT '划线价',
  `u_sales` int(11) DEFAULT '0' COMMENT '销售量',
  `u_stores` int(11) DEFAULT '0' COMMENT '存储量',
  `u_views` int(11) DEFAULT '0' COMMENT '浏览量',
  `u_content` text COMMENT '内容',
  `u_specs` varchar(50) DEFAULT NULL COMMENT '规格',
  `u_covers` varchar(1000) DEFAULT NULL,
  `u_create_time` timestamp NULL DEFAULT NULL,
  `u_ok` tinyint(1) DEFAULT '0' COMMENT '0,上架，1，撤回',
  `u_source` varchar(10) DEFAULT 'office' COMMENT '来源：user，个人，office，直营官方',
  `u_user_id` int(11) DEFAULT NULL COMMENT '当source为个人，此字段有效，即是发布人id',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_shop_good` */

/*Table structure for table `u_ticket` */

DROP TABLE IF EXISTS `u_ticket`;

CREATE TABLE `u_ticket` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_name` varchar(50) DEFAULT NULL COMMENT '卡券名称',
  `u_code` varchar(32) DEFAULT NULL COMMENT '券码',
  `u_num` varchar(20) DEFAULT NULL COMMENT '卡券抵用额度',
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `u_cate_code` varchar(20) DEFAULT NULL COMMENT '可用分类',
  `u_limit_num` varchar(20) DEFAULT NULL COMMENT '最低额度可用',
  `u_limit_time` varchar(50) DEFAULT NULL COMMENT '可用时间范围，如：2019-09-08~201-09-10',
  `u_good_id` varchar(20) DEFAULT NULL COMMENT '关联产品id,多个,分割',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_ticket` */


/*Table structure for table `u_topic` */

DROP TABLE IF EXISTS `u_topic`;

CREATE TABLE `u_topic` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_name` varchar(50) DEFAULT NULL COMMENT '话题名称',
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_content` varchar(1000) DEFAULT NULL COMMENT '话题介绍',
  `u_cover` varchar(200) DEFAULT NULL COMMENT '话题头像',
  `u_views` int(11) DEFAULT '0' COMMENT '阅读数',
  `u_comments` int(11) DEFAULT '0' COMMENT '评论数',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_topic` */


/*Table structure for table `u_trend` */

DROP TABLE IF EXISTS `u_trend`;

CREATE TABLE `u_trend` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_content` varchar(200) DEFAULT NULL,
  `u_imgs` varchar(1000) DEFAULT NULL,
  `u_user_id` int(11) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_views` int(11) DEFAULT '0',
  `u_stars` int(11) DEFAULT '0',
  `u_house_id` int(11) DEFAULT NULL,
  `u_topic_id` int(11) DEFAULT NULL COMMENT '关联话题id',
  `u_share_path` varchar(200) DEFAULT NULL COMMENT '分享图地址',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_trend` */


/*Table structure for table `u_trend_comment` */

DROP TABLE IF EXISTS `u_trend_comment`;

CREATE TABLE `u_trend_comment` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_trend_id` int(11) NOT NULL,
  `u_content` varchar(200) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_user_id` int(11) DEFAULT NULL,
  `u_parent_id` int(11) DEFAULT '0',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_trend_comment` */


/*Table structure for table `u_user` */

DROP TABLE IF EXISTS `u_user`;

CREATE TABLE `u_user` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_name` varchar(20) DEFAULT NULL,
  `u_nick_name` varchar(50) DEFAULT NULL,
  `u_gender` tinyint(1) DEFAULT '0',
  `u_icon` varchar(200) DEFAULT NULL,
  `u_country` varchar(20) DEFAULT NULL,
  `u_province` varchar(20) DEFAULT NULL,
  `u_city` varchar(20) DEFAULT NULL,
  `u_area` varchar(20) DEFAULT NULL,
  `u_phone` varchar(11) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_open_id` varchar(100) DEFAULT NULL,
  `u_mark` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`u_id`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8;

/*Data for the table `u_user` */


/*Table structure for table `u_user_house` */

DROP TABLE IF EXISTS `u_user_house`;

CREATE TABLE `u_user_house` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_user_id` int(11) DEFAULT NULL,
  `u_house_id` int(11) DEFAULT NULL,
  `u_create_time` timestamp NULL DEFAULT NULL,
  `u_building` int(11) DEFAULT NULL COMMENT '楼栋',
  `u_number` int(11) DEFAULT NULL COMMENT '房号',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_user_house` */


/*Table structure for table `u_user_level` */

DROP TABLE IF EXISTS `u_user_level`;

CREATE TABLE `u_user_level` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_user_id` int(11) DEFAULT NULL,
  `u_role_id` int(11) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_user_level` */


/*Table structure for table `u_user_order_location` */

DROP TABLE IF EXISTS `u_user_order_location`;

CREATE TABLE `u_user_order_location` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_phone` varchar(11) DEFAULT NULL,
  `u_address_name` varchar(100) DEFAULT NULL,
  `u_address` varchar(200) DEFAULT NULL,
  `u_area` varchar(50) DEFAULT NULL,
  `u_user_id` int(11) DEFAULT NULL,
  `u_default` tinyint(1) DEFAULT '0' COMMENT '0，非默认；1.默认地址',
  `u_name` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_user_order_location` */


/*Table structure for table `u_user_ticket` */

DROP TABLE IF EXISTS `u_user_ticket`;

CREATE TABLE `u_user_ticket` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_user_id` int(11) DEFAULT NULL,
  `u_ticket_id` int(11) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_used` tinyint(2) DEFAULT '0' COMMENT '0,可用，1，已用，2，过期',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_user_ticket` */

/*Table structure for table `u_user_wallet` */

DROP TABLE IF EXISTS `u_user_wallet`;

CREATE TABLE `u_user_wallet` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_user_id` int(11) DEFAULT NULL,
  `u_number` decimal(10,2) DEFAULT NULL COMMENT '金额',
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_mark` varchar(100) DEFAULT NULL COMMENT '备注',
  `u_type` tinyint(1) DEFAULT '0' COMMENT '0,新增，1，减少',
  `u_cate` tinyint(2) DEFAULT '0' COMMENT '0,自己生成；1，协助生成',
  `u_state` tinyint(1) DEFAULT '0' COMMENT '0，未审核，1，已审核，2，审核失败',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_user_wallet` */

/*Table structure for table `u_user_words` */

DROP TABLE IF EXISTS `u_user_words`;

CREATE TABLE `u_user_words` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_to_user_id` int(11) DEFAULT NULL,
  `u_from_user_id` int(11) DEFAULT NULL,
  `u_content` varchar(200) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_user_words` */

/*Table structure for table `u_worker` */

DROP TABLE IF EXISTS `u_worker`;

CREATE TABLE `u_worker` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_name` varchar(50) DEFAULT NULL,
  `u_gender` tinyint(1) DEFAULT '0',
  `u_cate_code` varchar(30) DEFAULT NULL COMMENT '技工分类code',
  `u_mark` varchar(200) DEFAULT NULL COMMENT '技工说明',
  `u_icon` varchar(200) DEFAULT NULL,
  `u_phone` varchar(11) DEFAULT NULL,
  `u_bind_house_id` int(11) DEFAULT NULL COMMENT '所属社区，根据分享人所在社区决定',
  `u_admin_user_id` int(11) DEFAULT NULL COMMENT '提交人id',
  `u_share_path` varchar(200) DEFAULT NULL COMMENT '分享名片地址',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_worker` */


/*Table structure for table `u_worker_comment` */

DROP TABLE IF EXISTS `u_worker_comment`;

CREATE TABLE `u_worker_comment` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_worker_id` int(11) NOT NULL,
  `u_content` varchar(200) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_user_id` int(11) DEFAULT NULL,
  `u_parent_id` int(11) DEFAULT '0',
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_worker_comment` */


/*Table structure for table `u_worker_level` */

DROP TABLE IF EXISTS `u_worker_level`;

CREATE TABLE `u_worker_level` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_worker_id` int(11) DEFAULT NULL,
  `u_role_id` int(11) DEFAULT NULL,
  `u_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_worker_level` */

/*Table structure for table `u_wx_msg` */

DROP TABLE IF EXISTS `u_wx_msg`;

CREATE TABLE `u_wx_msg` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_user_id` int(11) DEFAULT NULL,
  `u_from_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`u_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;

/*Data for the table `u_wx_msg` */


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
