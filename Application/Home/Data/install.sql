/*
Navicat MySQL Data Transfer
Date: 2015-09-23 10:24:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for day_users
-- ----------------------------
DROP TABLE IF EXISTS `day_sys`;
CREATE TABLE `day_sys` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `user_type` int(2) unsigned NOT NULL DEFAULT '2' COMMENT '用户类型，1：超级管理员，2：普通管理员',
  `user_account` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '用户密码',
  `user_name` varchar(255) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `china_id` varchar(255) DEFAULT NULL COMMENT '身份证号码',
  `head_image` varchar(255) DEFAULT NULL COMMENT '用户头像',
  `office_phone` varchar(32) DEFAULT NULL COMMENT '办公号码',
  `mobile_phone` varchar(255) DEFAULT NULL COMMENT '手机号码',
  `qq_number` varchar(255) DEFAULT NULL COMMENT 'QQ号码',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `register_time` varchar(20) DEFAULT NULL COMMENT '注册时间',
  `last_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后登录时间',
  `login_totals` int(11) DEFAULT '0' COMMENT '累计登录次数',
  `page_number` int(3) DEFAULT '10' COMMENT '分页数量',
  `user_status` int(11) NOT NULL DEFAULT '0' COMMENT '用户状态,0:正常,1:锁定',
  `remarks` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
-- Records of day_sys
-- ----------------------------
INSERT INTO `day_sys` VALUES ('1', '1', '38okkCl81AANV4irKw', 'e10adc3949ba59abbe56e057f20f883e', '38okkCl81AANV4irKw', '3s91kCl/hQJYAdf6dVE8siN3a3VEycTLuG8', 'img/face.jpg', '0718-85695632', 'jpkhxXso1w5dC9P1d1A+vi9zaQ', 'jphzxX0p01FeC9HycFk4vCw', '3p8kkXt+ggMNV4irKyBs73YpNWkWn5o', '1443087083', '2015-10-08 15:04:21', '1', '10', '0','');

-- ----------------------------
-- Table structure for day_members
-- ----------------------------
DROP TABLE IF EXISTS `day_members`;
CREATE TABLE `day_members` (
  `client_number` varchar(12) DEFAULT NULL COMMENT '体检单号',
  `client_name` varchar(60) DEFAULT NULL COMMENT '用户名',
  `password` varchar(64) DEFAULT NULL COMMENT '查询密码', 
  `china_id` varchar(255) DEFAULT NULL COMMENT '身份证号码',
  `mobile_phone` varchar(255) DEFAULT NULL COMMENT '手机号码',
  `frist_data` datetime DEFAULT NULL COMMENT '体检时间',
  `report_data` datetime DEFAULT NULL COMMENT '报告时间',
  `views` int(2) DEFAULT 0 COMMENT '查询次数',
  PRIMARY KEY (`client_number`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='体检用户表';

-- ----------------------------
-- Table structure for day_report
-- ----------------------------
DROP TABLE IF EXISTS `day_report`;
CREATE TABLE `day_report` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `client_number` varchar(12) DEFAULT NULL COMMENT '体检单号',
  `item` varchar(64) DEFAULT NULL COMMENT '体检项目',
  `result` longtext COMMENT '项目结果',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='体检结果';

-- ----------------------------
-- Table structure for day_setup
-- ----------------------------
DROP TABLE IF EXISTS `day_setup`;
CREATE TABLE `day_setup` (
  `subject` varchar(255) NOT NULL COMMENT '设置项目',
  `parameter` varchar(255) DEFAULT NULL COMMENT '设置参数',
  `illustrate` varchar(255) DEFAULT NULL COMMENT '设置说明',
  PRIMARY KEY (`subject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统设置';

-- ----------------------------
-- Records of day_setup
-- ----------------------------
INSERT INTO `day_setup` VALUES ('updata_time', '0','数据库更新时间');
INSERT INTO `day_setup` VALUES ('totle_num', '0','自系统建立以来共出具报告数');
INSERT INTO `day_setup` VALUES ('totle_view', '0','自系统建立以来查询次数');
INSERT INTO `day_setup` VALUES ('totle_pv', '0','首页访问量');
INSERT INTO `day_setup` VALUES ('view_pv', '0','查询结果页面pv');
INSERT INTO `day_setup` VALUES ('search_num', '3','查询次数限制');
INSERT INTO `day_setup` VALUES ('reserve', '120','数据保留天数');

-- ----------------------------
-- Table structure for day_sys_log
-- ----------------------------
DROP TABLE IF EXISTS `day_sys_log`;
CREATE TABLE `day_sys_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `user_account` varchar(255) NOT NULL DEFAULT '0' COMMENT '用户账号',
  `user_name` varchar(255) NOT NULL DEFAULT '0' COMMENT '真实姓名',
  `event` varchar(64) NOT NULL COMMENT '事件',
  `explicit` varchar(255) NOT NULL COMMENT '说明',
  `event_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '时间',
  `user_ip` varchar(20) NOT NULL COMMENT 'ip',
  `browser` varchar(64) DEFAULT NULL COMMENT '浏览器',
  `operating_system` varchar(64) DEFAULT NULL COMMENT '系统',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='安全日志表';

-- ----------------------------
-- Table structure for day_client_log
-- ----------------------------
DROP TABLE IF EXISTS `day_client_log`;
CREATE TABLE `day_client_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `client_number` varchar(12) NOT NULL DEFAULT '0' COMMENT '执行用户体检单号',
  `client_name` varchar(255) NOT NULL DEFAULT '0' COMMENT '执行用户名',
  `query_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '查询时间',
  `query_views` int(2) NOT NULL DEFAULT '0' COMMENT '查询次数',
  `client_ip` varchar(20) NOT NULL COMMENT '查询ip',
  `browser` varchar(64) DEFAULT NULL COMMENT '浏览器',
  `operating_system` varchar(64) DEFAULT NULL COMMENT '操作系统',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客户查询log表';
