/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : test2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2019-02-15 18:38:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for collection
-- ----------------------------
DROP TABLE IF EXISTS `collection`;
CREATE TABLE `collection` (
  `collection_id` int(11) NOT NULL AUTO_INCREMENT,
  `gallery_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `delete_time` datetime NOT NULL,
  PRIMARY KEY (`collection_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for gallery
-- ----------------------------
DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `gallery_name` varchar(25) CHARACTER SET utf8 NOT NULL,
  `creator_id` int(200) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `gallery_id` int(11) NOT NULL AUTO_INCREMENT,
  `gallery_describe` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `head_photo_path` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `status` enum('0','1','','') COLLATE utf8_bin NOT NULL DEFAULT '0' COMMENT '0表示未发布，1表示发布',
  `collect` enum('0','1','','') COLLATE utf8_bin NOT NULL DEFAULT '0' COMMENT '表示当前用户是否收藏该相册 1表示收藏 0表示未收藏',
  PRIMARY KEY (`gallery_id`),
  KEY `creator_id` (`creator_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for photo
-- ----------------------------
DROP TABLE IF EXISTS `photo`;
CREATE TABLE `photo` (
  `photo_id` int(250) unsigned NOT NULL AUTO_INCREMENT,
  `photo_name` varchar(25) COLLATE utf8_bin NOT NULL,
  `creator_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `path` varchar(250) COLLATE utf8_bin NOT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`photo_id`),
  KEY `gallery_id` (`gallery_id`),
  CONSTRAINT `photo` FOREIGN KEY (`gallery_id`) REFERENCES `gallery` (`gallery_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(25) COLLATE utf8_bin NOT NULL,
  `password` varchar(64) COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `nickname` varchar(255) COLLATE utf8_bin NOT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `profile_photo` varchar(255) CHARACTER SET utf8 DEFAULT 'profile.png',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
