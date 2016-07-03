-- MySQL dump 10.13  Distrib 5.7.12, for Linux (x86_64)
--
-- Host: localhost    Database: web_plasztika
-- ------------------------------------------------------
-- Server version	5.7.12-0ubuntu1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `_changes`
--

DROP TABLE IF EXISTS `_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_changes` (
  `change_id` int(11) NOT NULL AUTO_INCREMENT,
  `session` int(11) DEFAULT NULL,
  `reference` int(11) DEFAULT NULL,
  `resource` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `changes` text COLLATE utf8_hungarian_ci,
  `state` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `applied` int(11) DEFAULT NULL,
  `applied_session` int(11) DEFAULT NULL,
  PRIMARY KEY (`change_id`),
  KEY `reference` (`reference`),
  KEY `session` (`session`),
  KEY `state` (`state`),
  KEY `date` (`date`),
  KEY `resource` (`resource`),
  KEY `applied` (`applied`),
  KEY `applied_session` (`applied_session`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_changes`
--

LOCK TABLES `_changes` WRITE;
/*!40000 ALTER TABLE `_changes` DISABLE KEYS */;
/*!40000 ALTER TABLE `_changes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_crypt`
--

DROP TABLE IF EXISTS `_crypt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_crypt` (
  `crypt_id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(127) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `crypted` varchar(127) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `secret` varchar(127) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `base` text COLLATE utf8_hungarian_ci,
  `reference` int(11) DEFAULT NULL,
  `keep` int(11) DEFAULT NULL,
  `renew` int(11) DEFAULT '0',
  PRIMARY KEY (`crypt_id`),
  KEY `keep` (`keep`),
  KEY `cypted` (`crypted`),
  KEY `secret` (`secret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_crypt`
--

LOCK TABLES `_crypt` WRITE;
/*!40000 ALTER TABLE `_crypt` DISABLE KEYS */;
/*!40000 ALTER TABLE `_crypt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_drafts`
--

DROP TABLE IF EXISTS `_drafts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_drafts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference` int(11) NOT NULL,
  `session` int(11) DEFAULT NULL,
  `changes` text COLLATE utf8_hungarian_ci,
  PRIMARY KEY (`id`),
  KEY `reference` (`reference`),
  KEY `session` (`session`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_drafts`
--

LOCK TABLES `_drafts` WRITE;
/*!40000 ALTER TABLE `_drafts` DISABLE KEYS */;
/*!40000 ALTER TABLE `_drafts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_history`
--

DROP TABLE IF EXISTS `_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference` int(11) NOT NULL,
  `session` int(11) NOT NULL,
  `changes` text COLLATE utf8_hungarian_ci,
  PRIMARY KEY (`id`),
  KEY `reference` (`reference`),
  KEY `session` (`session`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_history`
--

LOCK TABLES `_history` WRITE;
/*!40000 ALTER TABLE `_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_lang`
--

DROP TABLE IF EXISTS `_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_lang`
--

LOCK TABLES `_lang` WRITE;
/*!40000 ALTER TABLE `_lang` DISABLE KEYS */;
INSERT INTO `_lang` VALUES (1,'hun','hu'),(2,'eng','en'),(3,'deu','de');
/*!40000 ALTER TABLE `_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_log`
--

DROP TABLE IF EXISTS `_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `exception` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `message` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `file` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `line` int(11) DEFAULT NULL,
  `trace` text COLLATE utf8_hungarian_ci,
  `solved` int(11) DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `date` (`date`),
  KEY `exception` (`exception`),
  KEY `file_line` (`file`,`line`),
  KEY `solved` (`solved`),
  KEY `message` (`message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_log`
--

LOCK TABLES `_log` WRITE;
/*!40000 ALTER TABLE `_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_model_metas`
--

DROP TABLE IF EXISTS `_model_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_model_metas` (
  `model_meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference` int(11) NOT NULL,
  `lang` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `keywords` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `description` mediumtext COLLATE utf8_hungarian_ci,
  `meta` mediumtext COLLATE utf8_hungarian_ci,
  PRIMARY KEY (`model_meta_id`),
  UNIQUE KEY `reference_UNIQUE` (`reference`),
  KEY `reference` (`reference`),
  KEY `lang` (`lang`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_model_metas`
--

LOCK TABLES `_model_metas` WRITE;
/*!40000 ALTER TABLE `_model_metas` DISABLE KEYS */;
/*!40000 ALTER TABLE `_model_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_model_orders`
--

DROP TABLE IF EXISTS `_model_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_model_orders` (
  `model_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference` int(11) NOT NULL,
  `lang` int(11) DEFAULT NULL,
  `parent_ref` int(11) DEFAULT NULL,
  `order_no` int(11) DEFAULT NULL,
  PRIMARY KEY (`model_order_id`),
  KEY `reference` (`reference`),
  KEY `lang` (`lang`),
  KEY `parent_ref` (`parent_ref`),
  KEY `order_no` (`order_no`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_model_orders`
--

LOCK TABLES `_model_orders` WRITE;
/*!40000 ALTER TABLE `_model_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `_model_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_model_states`
--

DROP TABLE IF EXISTS `_model_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_model_states` (
  `model_state_id` int(11) NOT NULL AUTO_INCREMENT,
  `model` int(11) NOT NULL,
  `lang` int(11) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `begin` int(11) DEFAULT NULL,
  `end` int(11) DEFAULT NULL,
  `params` mediumtext COLLATE utf8_hungarian_ci,
  PRIMARY KEY (`model_state_id`),
  KEY `lang` (`lang`),
  KEY `begin` (`begin`),
  KEY `end` (`end`),
  KEY `active` (`active`),
  KEY `model` (`model`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_model_states`
--

LOCK TABLES `_model_states` WRITE;
/*!40000 ALTER TABLE `_model_states` DISABLE KEYS */;
/*!40000 ALTER TABLE `_model_states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_model_trees`
--

DROP TABLE IF EXISTS `_model_trees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_model_trees` (
  `model_tree_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference` int(11) NOT NULL,
  `lang` int(11) DEFAULT NULL,
  `parent_ref` int(11) DEFAULT NULL,
  `prev_ref` int(11) DEFAULT NULL,
  PRIMARY KEY (`model_tree_id`),
  KEY `reference` (`reference`),
  KEY `lang` (`lang`),
  KEY `parent_ref` (`parent_ref`),
  KEY `prev_ref` (`prev_ref`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_model_trees`
--

LOCK TABLES `_model_trees` WRITE;
/*!40000 ALTER TABLE `_model_trees` DISABLE KEYS */;
INSERT INTO `_model_trees` VALUES (1,1,1,0,0),(2,2,2,0,0),(3,3,3,0,0),(10,10,NULL,1,0),(11,11,NULL,1,10),(20,20,NULL,2,0),(21,21,NULL,2,20),(30,30,NULL,3,0),(31,31,NULL,3,30),(99,99,NULL,NULL,NULL);
/*!40000 ALTER TABLE `_model_trees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_models`
--

DROP TABLE IF EXISTS `_models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_models` (
  `reference` int(11) NOT NULL AUTO_INCREMENT,
  `model_class` varchar(255) CHARACTER SET utf8 NOT NULL,
  `table_from` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'if is null then model_class is in use',
  `table_id` int(11) DEFAULT NULL,
  `table_condition` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'not used yet, just an idea to work out later:\n- not a direct id to model, it is (or they are) defined  by a condition',
  PRIMARY KEY (`reference`),
  KEY `table_index` (`table_from`(50),`table_id`,`table_condition`(50)),
  KEY `class_index` (`model_class`(50),`table_id`,`table_condition`(50))
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_models`
--

LOCK TABLES `_models` WRITE;
/*!40000 ALTER TABLE `_models` DISABLE KEYS */;
INSERT INTO `_models` VALUES (1,'lang','_lang',1,NULL),(2,'lang','_lang',2,NULL),(3,'lang','_lang',3,NULL),(10,'tree','tree',1,NULL),(11,'tree','tree',4,NULL),(20,'tree','tree',2,NULL),(21,'tree','tree',5,NULL),(30,'tree','tree',3,NULL),(31,'tree','tree',6,NULL),(91,'member','member',1,NULL),(92,'member','member',2,NULL),(99,'empty','empty',0,NULL);
/*!40000 ALTER TABLE `_models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_permissions`
--

DROP TABLE IF EXISTS `_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `route` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `reference` int(11) DEFAULT NULL,
  `label` varchar(63) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `owner` int(11) DEFAULT NULL,
  `group` int(11) DEFAULT NULL,
  `other` tinyint(1) DEFAULT '0',
  `permission` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`permission_id`),
  KEY `group` (`reference`,`group`),
  KEY `other` (`reference`,`other`),
  KEY `owner` (`reference`,`owner`),
  KEY `route` (`route`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_permissions`
--

LOCK TABLES `_permissions` WRITE;
/*!40000 ALTER TABLE `_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_rewrites`
--

DROP TABLE IF EXISTS `_rewrites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_rewrites` (
  `rewrite_id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `resource_uri` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `reference` int(11) DEFAULT NULL,
  `params` mediumtext COLLATE utf8_hungarian_ci,
  `primary` tinyint(1) NOT NULL DEFAULT '0',
  `lang` int(11) DEFAULT NULL,
  PRIMARY KEY (`rewrite_id`),
  UNIQUE KEY `url_UNIQUE` (`url`),
  KEY `url` (`url`),
  KEY `reference` (`reference`,`primary`),
  KEY `lang` (`lang`),
  KEY `pri` (`primary`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_rewrites`
--

LOCK TABLES `_rewrites` WRITE;
/*!40000 ALTER TABLE `_rewrites` DISABLE KEYS */;
/*!40000 ALTER TABLE `_rewrites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_sessions`
--

DROP TABLE IF EXISTS `_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_sessions` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(1024) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `token` int(11) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `open` int(11) DEFAULT NULL,
  `applied` int(11) DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  KEY `user` (`user`),
  KEY `open` (`applied`,`open`),
  KEY `applied` (`applied`),
  KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_sessions`
--

LOCK TABLES `_sessions` WRITE;
/*!40000 ALTER TABLE `_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_token`
--

DROP TABLE IF EXISTS `_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_token` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(127) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `open` int(11) DEFAULT NULL,
  `expire` int(11) DEFAULT NULL,
  `user_data` text COLLATE utf8_hungarian_ci,
  PRIMARY KEY (`token_id`),
  KEY `token` (`token`),
  KEY `user` (`user`),
  KEY `open` (`open`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_token`
--

LOCK TABLES `_token` WRITE;
/*!40000 ALTER TABLE `_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_user`
--

DROP TABLE IF EXISTS `_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `usergroup` int(11) DEFAULT NULL,
  `unique_scope` varchar(1023) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `unique_auth` varchar(1023) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `custom_data` text COLLATE utf8_hungarian_ci,
  `alias` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `email` (`email`),
  KEY `usergroup` (`usergroup`),
  KEY `alias` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_user`
--

LOCK TABLES `_user` WRITE;
/*!40000 ALTER TABLE `_user` DISABLE KEYS */;
INSERT INTO `_user` VALUES (1,'tibor.bedekovits@retroscope.hu','Bedekovits Tibor',1,NULL,NULL,'{\"member_id\":\"1\",\"last_login\":null,\"last_ip\":null,\"licence\":null}',0),(2,'info@retroscope.hu','Retroscope',1,NULL,NULL,'{\"member_id\":\"2\",\"last_login\":\"1424273313\",\"last_ip\":\"192.168.0.10\",\"licence\":null}',0);
/*!40000 ALTER TABLE `_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_usergroup`
--

DROP TABLE IF EXISTS `_usergroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_usergroup` (
  `usergroup_id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(63) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `scope` text COLLATE utf8_hungarian_ci,
  `auth` text COLLATE utf8_hungarian_ci,
  PRIMARY KEY (`usergroup_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_usergroup`
--

LOCK TABLES `_usergroup` WRITE;
/*!40000 ALTER TABLE `_usergroup` DISABLE KEYS */;
INSERT INTO `_usergroup` VALUES (0,'visitor',NULL,NULL),(1,'admin',NULL,'{\"editor\": \"*\", \"user\":\"*\", \"newsletter\":\"*\"}'),(2,'editor',NULL,'{\"editor\": \"*\", \"newsletter\":[\"campaign\",\"subscriber\"]}');
/*!40000 ALTER TABLE `_usergroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_virtual_trees`
--

DROP TABLE IF EXISTS `_virtual_trees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_virtual_trees` (
  `virtual_tree_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `model_tree_id` int(11) DEFAULT NULL,
  `reference` int(11) NOT NULL,
  `lang` int(11) DEFAULT NULL,
  `parent_ref` int(11) DEFAULT NULL,
  `prev_ref` int(11) DEFAULT NULL,
  PRIMARY KEY (`virtual_tree_id`),
  KEY `session_id` (`session_id`),
  KEY `reference` (`reference`),
  KEY `lang` (`lang`),
  KEY `parent_ref` (`parent_ref`),
  KEY `prev_ref` (`prev_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_virtual_trees`
--

LOCK TABLES `_virtual_trees` WRITE;
/*!40000 ALTER TABLE `_virtual_trees` DISABLE KEYS */;
/*!40000 ALTER TABLE `_virtual_trees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking`
--

DROP TABLE IF EXISTS `booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_hungarian_ci NOT NULL,
  `date` int(11) DEFAULT NULL,
  `room` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `arrival` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `departure` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `adults` int(11) DEFAULT '0',
  `children` int(11) DEFAULT '0',
  `email` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `message` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `user_agent` varchar(1024) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `status` (`status`),
  KEY `ip_address` (`ip_address`),
  KEY `user_agent` (`user_agent`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking`
--

LOCK TABLES `booking` WRITE;
/*!40000 ALTER TABLE `booking` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `e_campaign`
--

DROP TABLE IF EXISTS `e_campaign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `e_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_hungarian_ci DEFAULT NULL,
  `category` varchar(1024) CHARACTER SET utf8 COLLATE utf8_hungarian_ci DEFAULT NULL,
  `mail_route` varchar(255) DEFAULT NULL,
  `nonce` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `mail_html` text,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `e_campaign`
--

LOCK TABLES `e_campaign` WRITE;
/*!40000 ALTER TABLE `e_campaign` DISABLE KEYS */;
/*!40000 ALTER TABLE `e_campaign` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `e_cron`
--

DROP TABLE IF EXISTS `e_cron`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `e_cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign` int(11) NOT NULL,
  `test_address` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `limit_per_period` int(11) DEFAULT NULL,
  `period` int(11) DEFAULT NULL,
  `start` int(11) DEFAULT NULL,
  `finish` int(11) DEFAULT NULL,
  `nonce` varchar(128) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign` (`campaign`),
  KEY `start` (`start`),
  KEY `finish` (`finish`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `e_cron`
--

LOCK TABLES `e_cron` WRITE;
/*!40000 ALTER TABLE `e_cron` DISABLE KEYS */;
/*!40000 ALTER TABLE `e_cron` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `e_delivered`
--

DROP TABLE IF EXISTS `e_delivered`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `e_delivered` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cron_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `uhash` varchar(32) DEFAULT NULL,
  `unsubscribe` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `address_id` (`address_id`),
  KEY `date` (`date`),
  KEY `unsubscribe` (`unsubscribe`),
  KEY `cron_id` (`cron_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `e_delivered`
--

LOCK TABLES `e_delivered` WRITE;
/*!40000 ALTER TABLE `e_delivered` DISABLE KEYS */;
/*!40000 ALTER TABLE `e_delivered` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `e_request`
--

DROP TABLE IF EXISTS `e_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `e_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request` varchar(512) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `e_request`
--

LOCK TABLES `e_request` WRITE;
/*!40000 ALTER TABLE `e_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `e_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `e_stat`
--

DROP TABLE IF EXISTS `e_stat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `e_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) DEFAULT NULL,
  `deliver_id` int(11) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `click` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `deliver_id` (`deliver_id`),
  KEY `method` (`method`),
  KEY `click` (`click`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `e_stat`
--

LOCK TABLES `e_stat` WRITE;
/*!40000 ALTER TABLE `e_stat` DISABLE KEYS */;
/*!40000 ALTER TABLE `e_stat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `e_subscriber`
--

DROP TABLE IF EXISTS `e_subscriber`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `e_subscriber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `nonce` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `unsubscribe` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `date` (`date`),
  KEY `unsubscribe` (`unsubscribe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `e_subscriber`
--

LOCK TABLES `e_subscriber` WRITE;
/*!40000 ALTER TABLE `e_subscriber` DISABLE KEYS */;
/*!40000 ALTER TABLE `e_subscriber` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `freecontent`
--

DROP TABLE IF EXISTS `freecontent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `freecontent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_hungarian_ci NOT NULL,
  `description` text COLLATE utf8_hungarian_ci,
  `link` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `addon_class` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `contentimage` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `html_code` text COLLATE utf8_hungarian_ci,
  `condition` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `condition` (`condition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `freecontent`
--

LOCK TABLES `freecontent` WRITE;
/*!40000 ALTER TABLE `freecontent` DISABLE KEYS */;
/*!40000 ALTER TABLE `freecontent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `description` varchar(2048) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `thumbimage` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `contentimage` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `videourl` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `downloadurl` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_hungarian_ci NOT NULL,
  `pwd` varchar(255) COLLATE utf8_hungarian_ci NOT NULL,
  `reg_date` int(11) DEFAULT NULL,
  `confirm_date` int(11) DEFAULT NULL,
  `last_login` int(11) DEFAULT NULL,
  `last_ip` varchar(31) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `last_user_agent` varchar(1024) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `fbconnect` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `gpconnect` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `twconnect` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `inconnect` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `licence` int(11) DEFAULT NULL,
  `scope` varchar(2048) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `licence` (`licence`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member`
--

LOCK TABLES `member` WRITE;
/*!40000 ALTER TABLE `member` DISABLE KEYS */;
INSERT INTO `member` VALUES (1,NULL,'tibor.bedekovits@retroscope.hu','1dd29a9557014a9e92d33235b1232a54',1424269857,1424269857,1443165267,'127.0.0.1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,NULL,'info@retroscope.hu','$2a$09$05270a29efae5343dbd8e.hsjTr9o2rlxHov9X5bqQiLBQR370rhy',1424270657,1424270657,1463686554,'127.0.0.1',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_hungarian_ci NOT NULL,
  `date` int(11) NOT NULL,
  `contenttype` varchar(45) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `header_image` varchar(45) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_hungarian_ci NOT NULL,
  `date` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `message` varchar(2048) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `user_agent` varchar(1024) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `status` (`status`),
  KEY `ip_address` (`ip_address`),
  KEY `user_agent` (`user_agent`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post`
--

DROP TABLE IF EXISTS `post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_hungarian_ci NOT NULL,
  `date` int(11) NOT NULL,
  `lead` text COLLATE utf8_hungarian_ci,
  `content` longtext CHARACTER SET utf8,
  `contentimage` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `thumbimage` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `home_order` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `home_order` (`home_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post`
--

LOCK TABLES `post` WRITE;
/*!40000 ALTER TABLE `post` DISABLE KEYS */;
/*!40000 ALTER TABLE `post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pricing`
--

DROP TABLE IF EXISTS `pricing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pricing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `color` varchar(32) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `content` text COLLATE utf8_hungarian_ci,
  `price` varchar(16) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `currency` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `button` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pricing`
--

LOCK TABLES `pricing` WRITE;
/*!40000 ALTER TABLE `pricing` DISABLE KEYS */;
/*!40000 ALTER TABLE `pricing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_hungarian_ci NOT NULL,
  `date` int(11) NOT NULL,
  `brief` text COLLATE utf8_hungarian_ci,
  `content` text COLLATE utf8_hungarian_ci,
  `size_m` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `data_m` varchar(512) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `size_l` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `data_l` varchar(512) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `tag` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referencia`
--

DROP TABLE IF EXISTS `referencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `description` varchar(512) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `size_m` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `data_m` varchar(512) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `size_l` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `data_l` varchar(512) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referencia`
--

LOCK TABLES `referencia` WRITE;
/*!40000 ALTER TABLE `referencia` DISABLE KEYS */;
/*!40000 ALTER TABLE `referencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room`
--

DROP TABLE IF EXISTS `room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_hungarian_ci NOT NULL,
  `date` int(11) NOT NULL,
  `lead` text COLLATE utf8_hungarian_ci,
  `content` longtext CHARACTER SET utf8,
  `sidebar` text COLLATE utf8_hungarian_ci,
  `contentimage` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `thumbimage` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `price` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room`
--

LOCK TABLES `room` WRITE;
/*!40000 ALTER TABLE `room` DISABLE KEYS */;
/*!40000 ALTER TABLE `room` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slide`
--

DROP TABLE IF EXISTS `slide`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `slide` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `content_image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `sublabel` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `link` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slide`
--

LOCK TABLES `slide` WRITE;
/*!40000 ALTER TABLE `slide` DISABLE KEYS */;
/*!40000 ALTER TABLE `slide` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `public` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `public` (`public`),
  KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonial`
--

DROP TABLE IF EXISTS `testimonial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testimonial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_hungarian_ci NOT NULL,
  `company` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `brief` text COLLATE utf8_hungarian_ci,
  `size_photo` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `data_photo` varchar(512) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `size_logo` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `data_logo` varchar(512) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonial`
--

LOCK TABLES `testimonial` WRITE;
/*!40000 ALTER TABLE `testimonial` DISABLE KEYS */;
/*!40000 ALTER TABLE `testimonial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tree`
--

DROP TABLE IF EXISTS `tree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tree` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tree`
--

LOCK TABLES `tree` WRITE;
/*!40000 ALTER TABLE `tree` DISABLE KEYS */;
INSERT INTO `tree` VALUES (1,'hu'),(2,'en'),(3,'de'),(4,'hu_slides'),(5,'en_slides'),(6,'de_slides');
/*!40000 ALTER TABLE `tree` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'web_plasztika'
--
/*!50003 DROP FUNCTION IF EXISTS `_get_lang` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE FUNCTION `_get_lang`(_reference INT) RETURNS int(11)
BEGIN
	DECLARE _parent INT;
    DECLARE _lang INT;
    DECLARE lvl INT;
    SET _parent = _reference;
    SET lvl = 0;
    
    WHILE lvl < 100 AND EXISTS(SELECT reference FROM _models WHERE reference = _parent) AND COALESCE(_lang, 0) = 0 DO
		SELECT lang, parent_ref INTO _lang, _parent FROM _model_trees WHERE reference = _parent;
		SET lvl = lvl + 1;
    END WHILE;
    
    RETURN _lang;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `_get_parent` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE FUNCTION `_get_parent`(_reference INT, _session INT) RETURNS int(11)
BEGIN
	DECLARE _parent INT DEFAULT NULL;
    
    IF _session IS NOT NULL AND EXISTS(SELECT virtual_tree_id FROM _virtual_trees WHERE session_id = _session AND reference = _reference) THEN
		SELECT parent_ref INTO _parent FROM _virtual_trees WHERE session_id = _session AND reference = _reference ORDER BY virtual_tree_id DESC LIMIT 1;
	ELSEIF _session IS NULL AND EXISTS(SELECT virtual_tree_id FROM _virtual_trees WHERE reference = _reference) THEN
		SELECT parent_ref INTO _parent FROM _virtual_trees WHERE reference = _reference ORDER BY virtual_tree_id DESC LIMIT 1;
	ELSE
		SELECT parent_ref INTO _parent FROM _model_trees WHERE reference = _reference LIMIT 1;
    END IF;

	RETURN _parent;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_change_apply` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_change_apply`(IN _change_id INT, IN _session INT)
BEGIN
    IF _session IS NULL THEN
		SELECT `session` INTO _session FROM _changes WHERE change_id = _change_id;
    END IF;
    
	IF EXISTS(SELECT `change_id` FROM `_changes` WHERE `change_id` = _change_id AND `applied` IS NULL) THEN
		UPDATE `_changes` SET `applied` = UNIX_TIMESTAMP(), `applied_session` = _session WHERE `change_id` = _change_id;
        SELECT `change_id`, `reference`, `resource`, `state`, `session`, `applied_session`, `date`, `applied` FROM _changes WHERE change_id = _change_id;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_change_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_change_delete`(IN _change_id INT)
BEGIN
	DELETE FROM `_changes` WHERE change_id = _change_id;
    
    SELECT 1;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_change_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_change_get`(IN _change_id INT, IN _session INT, IN _draft BOOL, IN _applied BOOL)
BEGIN
	SELECT * FROM _changes 
    WHERE 
		(_change_id IS NULL OR `change_id` = _change_id) AND 
		(_session IS NULL OR `session` = _session) AND 
        (_draft IS NULL OR (_draft IS NOT NULL AND _draft = 0) OR (_draft IS NOT NULL AND _draft = 1 AND `applied` IS NULL)) AND
        (_applied IS NULL OR (_applied IS NOT NULL AND _applied = 0) OR (_applied IS NOT NULL AND _applied = 1 AND `applied` IS NOT NULL)) 
    ORDER BY change_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_change_log` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `_change_log`(IN _change_id INT, IN _session INT, IN _reference INT, IN _resource VARCHAR(255), IN _changes TEXT, IN _state VARCHAR(255))
BEGIN
    IF COALESCE(_state, '') = '' THEN
		SET _state = 'update';
    END IF;
    
	IF _change_id IS NOT NULL AND _change_id > 0 THEN
		UPDATE `_changes` SET `resource` = _resource, `changes` = _changes, `date` = UNIX_TIMESTAMP() WHERE change_id = _change_id;
	ELSE
		INSERT INTO `_changes` (`session`, `reference`, `resource`, `changes`, `state`, `date`) SELECT _session, CASE _reference WHEN 0 THEN NULL ELSE _reference END, _resource, _changes, _state, UNIX_TIMESTAMP();
        SELECT LAST_INSERT_ID() INTO _change_id;
	END IF;
    SELECT _change_id AS change_id, `date`, `state`, `applied`, `applied_session` FROM `_changes` WHERE change_id = _change_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_metas_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_metas_get`(IN _reference INT)
BEGIN
	SELECT metas.`title`, metas.`keywords`, metas.`description`, metas.`meta`
    FROM `_model_metas` AS metas
    WHERE metas.reference = _reference
    ORDER BY metas.model_meta_id DESC
    LIMIT 1;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_model_reference` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_model_reference`(IN _model_class VARCHAR(64), IN _table_from VARCHAR(64), IN _table_id INT)
BEGIN
	DECLARE _reference INT DEFAULT -1;
	
    IF _table_from = '' OR _model_class = _table_from THEN
		SELECT COALESCE(reference, -1) INTO _reference FROM `_models` WHERE model_class = _model_class AND table_id = _table_id;
	ELSE
		SELECT COALESCE(reference, -1) INTO _reference FROM `_models` WHERE model_class = _model_class AND table_from = _table_from AND table_id = _table_id;
	END IF;
    
    IF _reference = -1 THEN
		IF _table_from = '' THEN 
			SET _table_from = _model_class;
		END IF;
        
		INSERT INTO `_models` (`model_class`, `table_from`, `table_id`) VALUES (_model_class, _table_from, _table_id);
        SELECT LAST_INSERT_ID() INTO _reference;
    END IF;
    
	SELECT _reference AS reference;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_order_insert` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_order_insert`(IN _reference INT, IN _parent_ref INT, IN _order_no INT, IN _lang INT)
BEGIN
	DECLARE moved_back INT DEFAULT NULL;
    
    IF EXISTS(SELECT `reference` FROM `_model_orders` WHERE `order_no` = _order_no) THEN
		UPDATE `_model_orders` SET order_no = order_no+1 WHERE parent_ref = _parent_ref AND lang = _lang AND order_no >= _order_no;
    END IF;
    INSERT INTO `_model_orders` (reference, lang, parent_ref, order_no) VALUES (_reference, _lang, _parent_ref, _order_no);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_order_moveto` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_order_moveto`(IN _reference INT, IN _parent_ref INT, IN _order_no INT, IN _lang INT)
BEGIN
	CALL `_order_remove`(_reference);
    CALL `_order_insert`(_reference, _parent_ref, _order_no, _lang);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_order_remove` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_order_remove`(IN _reference INT)
BEGIN
	DECLARE moved_parent_ref INT DEFAULT NULL;
	DECLARE moved_order_no INT DEFAULT NULL;
	DECLARE moved_lang INT DEFAULT NULL;
    
    SELECT parent_ref, order_no, lang INTO moved_parent_ref, moved_order_no, moved_lang FROM `_metas` WHERE reference = _reference;
    
	IF EXISTS(SELECT reference FROM `_model_orders` WHERE lang = moved_lang AND parent_ref = moved_parent_ref) THEN
		UPDATE `_model_orders` SET order_no = order_no-1 WHERE lang = moved_lang AND parent_ref = moved_parent_ref AND order_no >= moved_order_no;
    END IF;
    DELETE FROM `_model_orders` WHERE reference = _reference;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_rewrite_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_rewrite_get`(IN _uri VARCHAR(512))
BEGIN
	DECLARE _reference INT DEFAULT NULL;
	DECLARE _resource_uri VARCHAR(512) DEFAULT NULL;
	DECLARE _params TEXT DEFAULT '';
    
    DECLARE _model_class VARCHAR(64);
    DECLARE _table_id INT;
    DECLARE _lang INT;
    
	SELECT rewrites.`resource_uri`, rewrites.`reference`, rewrites.`params` INTO _resource_uri, _reference, _params
	FROM `_rewrites` AS rewrites
	WHERE TRIM(BOTH '/' FROM TRIM(rewrites.`url`)) = _uri OR TRIM(BOTH '/' FROM TRIM(rewrites.`resource_uri`)) = _uri
	ORDER BY rewrites.`primary` DESC, rewrites.`rewrite_id`
    LIMIT 1;

    IF _reference IS NULL AND _resource_uri IS NULL AND _uri IS NOT NULL AND INSTR(_uri, '/') > 0 THEN
		SET _resource_uri = _uri;
    END IF;

    IF _reference IS NULL AND _resource_uri IS NOT NULL THEN
		SELECT SUBSTRING(_resource_uri, 1, INSTR(_resource_uri, '/')-1), 
        SUBSTRING(_resource_uri, INSTR(_resource_uri, '/')+1) 
        INTO _model_class, _table_id;
        
		SELECT (SELECT reference FROM `_models` WHERE model_class = _model_class AND table_id = _table_id ORDER BY reference DESC LIMIT 1) INTO _reference ;
        IF _reference IS NULL THEN
			SELECT (SELECT reference FROM `_models` WHERE table_from = _model_class AND table_id = _table_id ORDER BY reference DESC LIMIT 1) INTO _reference;
        END IF;
    END IF;

    IF _resource_uri IS NULL AND _reference IS NOT NULL THEN
		SELECT CONCAT(model_class, '/', table_id) INTO _resource_uri FROM `_models` AS models
        WHERE models.reference = _reference;
    END IF;
    IF _reference IS NOT NULL THEN
		SELECT lang INTO _lang FROM `_model_trees` AS trees WHERE reference = _reference;
    END IF;

	/*
    SELECT 'debug', _resource_uri, _reference, _params;
    */
    IF _resource_uri IS NOT NULL AND _reference IS NOT NULL THEN
		IF EXISTS(SELECT rewrites.rewrite_id FROM `_rewrites` AS rewrites
		WHERE 
			rewrites.`resource_uri` IS NOT NULL AND rewrites.`resource_uri` = _resource_uri AND
            rewrites.`reference` IS NOT NULL AND rewrites.`reference` = _reference AND
			COALESCE(rewrites.`params`, '') = COALESCE(_params, '')
		) THEN
			SELECT rewrites.`url`, rewrites.`params`, rewrites.`primary`, 
			COALESCE(rewrites.`resource_uri`, _resource_uri) AS resource_uri, 
			COALESCE(rewrites.`reference`, _reference) AS reference,
			COALESCE(rewrites.lang, _lang) AS lang
			FROM `_rewrites` AS rewrites
			WHERE 
				rewrites.`resource_uri` IS NOT NULL AND rewrites.`resource_uri` = _resource_uri AND
				rewrites.`reference` IS NOT NULL AND rewrites.`reference` = _reference AND
				COALESCE(rewrites.`params`, '') = COALESCE(_params, '')
			ORDER BY rewrites.`primary` DESC, rewrites.rewrite_id;
        ELSE
			SELECT _resource_uri AS `url`, NULL AS `params`, 1 AS `primary`, _resource_uri AS `resource_uri`, _reference AS `reference`, _lang AS lang;        
        END IF;
    END IF;
    IF _resource_uri IS NOT NULL AND _reference IS NULL THEN
		IF EXISTS(SELECT rewrites.rewrite_id FROM `_rewrites` AS rewrites
		WHERE 
			rewrites.`resource_uri` IS NOT NULL AND rewrites.`resource_uri` = _resource_uri AND
            rewrites.`reference` IS NULL AND
			COALESCE(rewrites.`params`, '') = COALESCE(_params, '')
		) THEN
			SELECT rewrites.`url`, rewrites.`params`, rewrites.`primary`, 
			COALESCE(rewrites.`resource_uri`, _resource_uri) AS resource_uri, 
			COALESCE(rewrites.`reference`, _reference) AS reference,
			COALESCE(rewrites.lang, _lang) AS lang
			FROM `_rewrites` AS rewrites
			WHERE 
				rewrites.`resource_uri` IS NOT NULL AND rewrites.`resource_uri` = _resource_uri AND
				rewrites.`reference` IS NULL AND 
				COALESCE(rewrites.`params`, '') = COALESCE(_params, '')
			ORDER BY rewrites.`primary` DESC, rewrites.rewrite_id;
        ELSE
			SELECT _resource_uri AS `url`, NULL AS `params`, 1 AS `primary`, _resource_uri AS `resource_uri`, _reference AS `reference`, _lang AS lang;        
        END IF;
    END IF;
    IF _resource_uri IS NULL AND _reference IS NOT NULL THEN
		IF EXISTS(SELECT rewrites.rewrite_id FROM `_rewrites` AS rewrites
		WHERE 
			rewrites.`resource_uri` IS NULL AND
            rewrites.`reference` IS NOT NULL AND rewrites.`reference` = _reference AND
			COALESCE(rewrites.`params`, '') = COALESCE(_params, '')
		) THEN
			SELECT rewrites.`url`, rewrites.`params`, rewrites.`primary`, 
			COALESCE(rewrites.`resource_uri`, _resource_uri) AS resource_uri, 
			COALESCE(rewrites.`reference`, _reference) AS reference,
			COALESCE(rewrites.lang, _lang) AS lang
			FROM `_rewrites` AS rewrites
			WHERE 
				rewrites.`resource_uri` IS NULL AND
				rewrites.`reference` IS NOT NULL AND rewrites.`reference` = _reference AND
				COALESCE(rewrites.`params`, '') = COALESCE(_params, '')
			ORDER BY rewrites.`primary` DESC, rewrites.rewrite_id;
        ELSE
			SELECT _resource_uri AS `url`, NULL AS `params`, 1 AS `primary`, _resource_uri AS `resource_uri`, _reference AS `reference`, _lang AS lang;        
        END IF;
    END IF;
    IF _resource_uri IS NULL AND _reference IS NULL THEN
		SELECT rewrites.`url`, rewrites.`params`, rewrites.`primary`, rewrites.`resource_uri`, rewrites.`reference`, COALESCE(rewrites.lang, _lang) AS lang
		FROM `_rewrites` AS rewrites
		WHERE rewrites.rewrite_id = 0;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_session_open` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_session_open`(IN _session_id INT, IN _label VARCHAR(1024), IN _token VARCHAR(127))
BEGIN
	DECLARE session_opened INT;
    
	IF _session_id = 0 THEN
		INSERT INTO _sessions (`label`, `token`, `user`, `open`)
        SELECT _label, token.token_id, token.user, UNIX_TIMESTAMP() 
			FROM _token AS token WHERE token.token = _token;
		SELECT LAST_INSERT_ID() INTO session_opened;
	ELSEIF EXISTS(SELECT * FROM _sessions WHERE session_id = _session_id 
				AND token = (SELECT token_id FROM _token WHERE token = _token)) THEN
		UPDATE _sessions SET `label` = _label WHERE session_id = _session_id 
			AND token = (SELECT token_id FROM _token WHERE token = _token);
		SET session_opened = _session_id;
    END IF;
    
    SELECT session_opened;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_set_pageprops` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_set_pageprops`(IN _session INT, IN _resource VARCHAR(255), IN _changes TEXT)
BEGIN
	DECLARE _change_id INT;
    SELECT change_id INTO _change_id FROM _changes WHERE `session` = _session AND resource = _resource AND state = 'pageproperties';
    
	IF _change_id IS NOT NULL THEN
		UPDATE `_changes` SET `changes` = _changes, `date` = UNIX_TIMESTAMP() WHERE change_id = _change_id;
	ELSE
		INSERT INTO `_changes` (`session`, `resource`, `changes`, `state`, `date`) 
        SELECT _session, _resource, _changes, 'pageproperties', UNIX_TIMESTAMP();
        SELECT LAST_INSERT_ID() INTO _change_id;
	END IF;
    SELECT _change_id AS change_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_states_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_states_get`(IN _reference INT)
BEGIN
	SELECT state.`active`, state.`begin`, state.`end`, state.`params`
    FROM `_model_states` AS state
    WHERE state.model = _reference
    ORDER BY state.model_state_id DESC
    LIMIT 1;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_tree_children` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_tree_children`(IN _reference INT, IN _lang INT, IN _prev_ref INT, IN _limit INT, IN _session INT)
BEGIN
	DECLARE _prev INT;
    DECLARE _next INT;
    DECLARE _tree_id INT;
    DECLARE _ind INT;
    DECLARE _heap INT;
    
    DROP TEMPORARY TABLE  IF EXISTS return_tree_children;

	IF _prev_ref IS NOT NULL THEN
		CALL _tree_get(NULL, _reference, _prev_ref, _lang, _session);

        IF _lang IS NOT NULL THEN
			SELECT return_tree_get.prev_ref, models.reference, models.model_class, models.table_id FROM return_tree_get
			LEFT JOIN _models AS models ON models.reference = return_tree_get.reference
			WHERE return_tree_get.parent_ref = _reference AND COALESCE(return_tree_get.lang, 0) = COALESCE(_lang, 0)
			ORDER BY return_tree_get.prev_ref;
		ELSE
			SELECT return_tree_get.prev_ref, models.reference, models.model_class, models.table_id FROM return_tree_get
			LEFT JOIN _models AS models ON models.reference = return_tree_get.reference
			WHERE return_tree_get.parent_ref = _reference
			ORDER BY return_tree_get.prev_ref;
        END IF;
	ELSE
		CALL _tree_get(NULL, _reference, _prev_ref, _lang, _session);

		SELECT COALESCE(_limit, COUNT(reference)) INTO _limit FROM return_tree_get WHERE parent_ref = _reference AND COALESCE(lang, 0) = COALESCE(_lang, 0);
        
		SET _prev = COALESCE(_prev_ref, 0);

		CREATE temporary table return_tree_children (
			tree_id int,
			ind int,
			reference int,
			model_class varchar(255),
			table_id int,
		PRIMARY KEY (`reference`)
		) ENGINE=MEMORY DEFAULT CHARSET=utf8;
        
        SET _ind = 0;
        SET _heap = 0;
        
        CALL _tree_get(NULL, _reference, _prev, _lang, _session);
        IF EXISTS(SELECT * FROM return_tree_get) THEN
			SELECT COALESCE(reference, 0), COALESCE(tree_id, 0) INTO _next, _tree_id FROM return_tree_get ORDER BY tree_id DESC LIMIT 1;
        ELSE
			SET _next = 0;
        END IF;
        WHILE _next > 0 AND _ind < _limit AND _heap < _limit DO
			IF NOT EXISTS(SELECT reference FROM return_tree_children WHERE reference = _next) THEN
				INSERT INTO return_tree_children (tree_id, ind, reference, model_class, table_id)
					SELECT _tree_id, _ind, reference, model_class, table_id FROM _models WHERE reference = _next;
				SET _ind = _ind + 1;
                SET _heap = 0;
			ELSE
				SET _heap = _heap + 1;
			END IF;
            SET _prev = _next;

			CALL _tree_get(NULL, _reference, _prev, _lang, _session);
			IF EXISTS(SELECT * FROM return_tree_get) THEN
				SELECT COALESCE(reference, 0), COALESCE(tree_id, 0) INTO _next, _tree_id FROM return_tree_get ORDER BY tree_id DESC LIMIT 1;
			ELSE
				SET _next = 0;
			END IF;
        END WHILE;
        
        SELECT * FROM return_tree_children ORDER BY ind;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_tree_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_tree_get`(IN _reference INT, IN _parent_ref INT, IN _prev_ref INT, IN _lang INT, IN _session INT)
BEGIN
	DROP TEMPORARY TABLE IF EXISTS return_tree_get;
    
    CREATE TEMPORARY TABLE `return_tree_get` (
		`tree_id` int(11) NOT NULL,
		`reference` int(11) NOT NULL,
		`lang` int(11) DEFAULT NULL,
		`parent_ref` int(11) DEFAULT NULL,
		`prev_ref` int(11) DEFAULT NULL,
		`session_id` int(11) DEFAULT NULL,
	  PRIMARY KEY (`tree_id`),
	  KEY `reference` (`reference`),
	  KEY `lang` (`lang`),
	  KEY `parent_ref` (`parent_ref`),
	  KEY `prev_ref` (`prev_ref`)
	) ENGINE=MEMORY DEFAULT CHARSET=utf8;
    
	INSERT INTO `return_tree_get` (tree_id, reference, lang, parent_ref, prev_ref, session_id)
	SELECT tree.model_tree_id AS tree_id, reference, lang, parent_ref, prev_ref, NULL AS session_id
	FROM _model_trees AS tree
	WHERE 
		COALESCE(_reference, tree.reference) = tree.reference AND
		COALESCE(_parent_ref, tree.parent_ref) = tree.parent_ref AND
		COALESCE(_prev_ref, tree.prev_ref) = tree.prev_ref AND
		COALESCE(_lang, 0) = COALESCE(tree.lang, 0)
	ORDER BY tree.model_tree_id DESC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_tree_insert` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_tree_insert`(IN _reference INT, IN _parent_ref INT, IN _prev_ref INT, IN _lang INT)
BEGIN
	DECLARE moved_back INT DEFAULT NULL;
    
    SELECT `reference` INTO moved_back 
		FROM `_model_trees` 
        WHERE `parent_ref` = _parent_ref AND `prev_ref` = _prev_ref AND COALESCE(`lang`, 0) = COALESCE(_lang, 0)
        ORDER BY model_tree_id DESC
        LIMIT 1;
    IF moved_back IS NOT NULL THEN
		UPDATE `_model_trees` SET prev_ref = _reference WHERE reference = moved_back;
    END IF;
    INSERT INTO `_model_trees` (reference, lang, parent_ref, prev_ref) 
		VALUES (_reference, _lang, _parent_ref, _prev_ref);
        
	SELECT _reference AS `inserted`, _lang AS `lang`, _parent_ref AS `parent`, _prev_ref AS `prev`;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_tree_lang` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_tree_lang`(IN _reference INT)
BEGIN
    SELECT _get_lang(_reference) AS lang;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_tree_moveto` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_tree_moveto`(IN _reference INT, IN _parent_ref INT, IN _prev_ref INT, IN _lang INT)
BEGIN
	CALL `_tree_remove`(_reference);
    CALL `_tree_insert`(_reference, _parent_ref, _prev_ref, _lang);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_tree_parent` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_tree_parent`(IN _reference INT, IN _session INT)
BEGIN
	DECLARE _ref INT;
    DECLARE _lvl INT DEFAULT 1;
    DECLARE _parent INT DEFAULT NULL;
    DECLARE _max INT DEFAULT 0;

    DROP TEMPORARY TABLE  IF EXISTS __parents;
    
    CREATE temporary table __parents (
		lvl int,
		reference int,
        model_class varchar(255),
        table_id int,
	PRIMARY KEY (`reference`)
	) ENGINE=MEMORY DEFAULT CHARSET=utf8;
    
    SELECT reference INTO _ref FROM _models WHERE reference = _reference;
    SELECT _get_parent(_ref, _session) INTO _parent;
    
    WHILE _lvl < 100 AND EXISTS(SELECT reference FROM _models WHERE reference = COALESCE(_parent, 0)) DO
		SET _ref = _parent;
		INSERT INTO __parents (lvl, reference, model_class, table_id) 
			SELECT _lvl, reference, model_class, table_id FROM _models WHERE reference = _ref ORDER BY reference DESC LIMIT 1;
        SET _lvl = _lvl + 1;
        
		SELECT _get_parent(_ref, _session) INTO _parent;
    END WHILE;
    
    SELECT * FROM __parents ORDER BY lvl DESC;
    DROP TEMPORARY TABLE  IF EXISTS __parents;
    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_tree_remove` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_tree_remove`(IN _reference INT)
BEGIN
	DECLARE moved_parent_ref INT DEFAULT NULL;
	DECLARE moved_prev_ref INT DEFAULT NULL;
    DECLARE moved_lang INT DEFAULT NULL;
    
    SELECT parent_ref, prev_ref, COALESCE(lang, 0) INTO moved_parent_ref, moved_prev_ref, moved_lang FROM `_model_trees` WHERE reference = _reference;
    
	IF EXISTS(SELECT reference FROM `_model_trees` WHERE parent_ref = moved_parent_ref AND prev_ref = _reference AND COALESCE(lang, 0) = moved_lang) THEN
		UPDATE `_model_trees` SET prev_ref = moved_prev_ref WHERE parent_ref = moved_parent_ref AND prev_ref = _reference AND COALESCE(lang, 0) = moved_lang;
    END IF;
    DELETE FROM `_model_trees` WHERE reference = _reference;
    
    SELECT _reference AS removed;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_tree_siblings` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_tree_siblings`(IN _reference INT, IN _lang INT, IN _prev_ref INT, IN _limit INT, IN _session INT)
BEGIN
	DECLARE _parent INT;
	SELECT _get_parent(_reference, _session) INTO _parent;
    IF _parent IS NOT NULL THEN
		CALL _tree_children(_parent, _lang, _prev_ref, _limit, _session);
	END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `_update_virtual_tree` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE PROCEDURE `_update_virtual_tree`(IN _session INT)
BEGIN

	DECLARE affected_model_tree_id INT;
	DECLARE affected_reference INT;
	DECLARE affected_lang INT;
	DECLARE affected_parent_ref INT;
	DECLARE affected_prev_ref INT;
    DECLARE vid INT;

    DECLARE cursor_done INT DEFAULT FALSE;

	-- declare cursor
	DEClARE affected_cursor CURSOR FOR 
	SELECT model_tree_id, reference, lang, parent_ref, prev_ref FROM affected 
		GROUP BY reference, lang, parent_ref, prev_ref
		ORDER BY reference DESC, lang, parent_ref, prev_ref;

    -- cursor not found handler workaround cause its not worked
    
	-- declare NOT FOUND handler
	/*DECLARE CONTINUE HANDLER FOR NOT FOUND SET cursor_done = TRUE;*/
    SELECT COUNT(*) INTO cursor_done FROM affected;

	OPEN affected_cursor;

	WHILE cursor_done > 0 DO
	/*cursor_loop: LOOP*/

		FETCH affected_cursor INTO affected_model_tree_id, affected_reference, affected_lang, affected_parent_ref, affected_prev_ref;
		/*
		IF cursor_done THEN 
			LEAVE cursor_loop;
		END IF;
        */
        
		-- do action
        SET vid = NULL;
		SELECT virtual_tree_id INTO vid FROM _virtual_trees WHERE session_id = _session AND reference = affected_reference;
        
		IF vid IS NOT NULL THEN
			UPDATE _virtual_trees SET model_tree_id = affected_model_tree_id, lang = affected_lang, 
				parent_ref = affected_parent_ref, prev_ref = affected_prev_ref WHERE virtual_tree_id = vid;
        ELSEIF affected_model_tree_id = 0 THEN
			INSERT INTO _virtual_trees (session_id, reference, lang, parent_ref, prev_ref)
				VALUES (_session, affected_reference, affected_lang, affected_parent_ref, affected_prev_ref);
        ELSE
			INSERT INTO _virtual_trees (session_id, model_tree_id, reference, lang, parent_ref, prev_ref)
				VALUES (_session, affected_model_tree_id, affected_reference, affected_lang, affected_parent_ref, affected_prev_ref);
        END IF;
        
		SET cursor_done = cursor_done - 1;
	/*END LOOP cursor_loop;*/
    END WHILE;

	CLOSE affected_cursor;
    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-06-12 14:17:51
