-- HappiMynd database STRUCTURE ONLY (no user data), taken from production on 2026-11-23.
-- Includes the rows of the `migrations` table so `php artisan migrate` only runs newer migrations.
-- Import into an empty database:  mysql -u root -p happimynd < database/schema/happimynd_schema.sql
-- Needs MySQL 8 (one table uses utf8mb4_0900_ai_ci).

-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: localhost    Database: happimynd
-- ------------------------------------------------------
-- Server version	8.0.42-0ubuntu0.20.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_status` enum('active','blocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `application_rate_emojis`
--

DROP TABLE IF EXISTS `application_rate_emojis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `application_rate_emojis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assessment_answers`
--

DROP TABLE IF EXISTS `assessment_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assessment_answers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint unsigned NOT NULL,
  `option_question_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_answers_assessment_id_foreign` (`assessment_id`),
  KEY `assessment_answers_option_question_id_foreign` (`option_question_id`),
  CONSTRAINT `assessment_answers_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assessment_answers_option_question_id_foreign` FOREIGN KEY (`option_question_id`) REFERENCES `option_questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assessment_approves`
--

DROP TABLE IF EXISTS `assessment_approves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assessment_approves` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `available_date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slot` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assessment_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `call_option` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_approves_assessment_id_foreign` (`assessment_id`),
  CONSTRAINT `assessment_approves_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assessment_scores`
--

DROP TABLE IF EXISTS `assessment_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assessment_scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `attempts` int DEFAULT NULL,
  `scores` json NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_scores_assessment_id_foreign` (`assessment_id`),
  KEY `assessment_scores_user_id_foreign` (`user_id`),
  CONSTRAINT `assessment_scores_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assessment_scores_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assessments`
--

DROP TABLE IF EXISTS `assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assessments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `batch_id` bigint unsigned DEFAULT NULL,
  `started_at` datetime NOT NULL,
  `ended_at` datetime DEFAULT NULL,
  `report` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `platform` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'website',
  PRIMARY KEY (`id`),
  KEY `assessments_user_id_foreign` (`user_id`),
  KEY `assessments_batch_id_foreign` (`batch_id`),
  CONSTRAINT `assessments_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `assessments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assign_psy_to_org_for_talks`
--

DROP TABLE IF EXISTS `assign_psy_to_org_for_talks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assign_psy_to_org_for_talks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int NOT NULL,
  `psychologist_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assign_psy_to_plans`
--

DROP TABLE IF EXISTS `assign_psy_to_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assign_psy_to_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `plan_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `psychologist_id` int NOT NULL,
  `last_psy_assign_for_guide` int NOT NULL DEFAULT '0',
  `last_psy_assign_for_buddy` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `availabilities`
--

DROP TABLE IF EXISTS `availabilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `availabilities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `availability_dates`
--

DROP TABLE IF EXISTS `availability_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `availability_dates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `consultant` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `batch_category`
--

DROP TABLE IF EXISTS `batch_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `batch_category` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `batch_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `calculation_step_macro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` tinyint DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `batch_category_batch_id_foreign` (`batch_id`),
  KEY `batch_category_category_id_foreign` (`category_id`),
  CONSTRAINT `batch_category_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `batch_category_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `batches`
--

DROP TABLE IF EXISTS `batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `batches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_profile_id` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `batches_user_profile_id_foreign` (`user_profile_id`),
  CONSTRAINT `batches_user_profile_id_foreign` FOREIGN KEY (`user_profile_id`) REFERENCES `user_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bundle_statuses`
--

DROP TABLE IF EXISTS `bundle_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bundle_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `valid` tinyint(1) NOT NULL DEFAULT '1',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Snapshot of when this purchase expires. NULL = never expires',
  `percentage_covered` double(8,2) NOT NULL DEFAULT '0.00',
  `plan_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `receipt_id` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bundle_statuses_package_id_foreign` (`plan_id`),
  KEY `bundle_statuses_user_id_foreign` (`user_id`),
  KEY `bundle_statuses_receipt_id_foreign` (`receipt_id`),
  KEY `bundle_statuses_plan_id_foreign` (`plan_id`) USING BTREE,
  CONSTRAINT `bundle_statuses_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bundle_statuses_receipt_id_foreign` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bundle_statuses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calculation_steps`
--

DROP TABLE IF EXISTS `calculation_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calculation_steps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campaigns`
--

DROP TABLE IF EXISTS `campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_parameters` json NOT NULL,
  `plan_id` json NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1->Active 0->inActive',
  `meta_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `carousel_sections`
--

DROP TABLE IF EXISTS `carousel_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carousel_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `data_group_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carousel_sections_data_group_id_foreign` (`data_group_id`),
  CONSTRAINT `carousel_sections_data_group_id_foreign` FOREIGN KEY (`data_group_id`) REFERENCES `data_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `acronymn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_in_report` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `category_posts`
--

DROP TABLE IF EXISTS `category_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_posts_post_id_foreign` (`post_id`),
  KEY `category_posts_category_id_foreign` (`category_id`),
  CONSTRAINT `category_posts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `category_posts_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `category_tokens`
--

DROP TABLE IF EXISTS `category_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `token_id` bigint unsigned NOT NULL,
  `token_category_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_tokens_token_id_foreign` (`token_id`),
  KEY `category_tokens_token_category_id_foreign` (`token_category_id`),
  CONSTRAINT `category_tokens_token_category_id_foreign` FOREIGN KEY (`token_category_id`) REFERENCES `token_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `category_tokens_token_id_foreign` FOREIGN KEY (`token_id`) REFERENCES `tokens` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_bot_assessments`
--

DROP TABLE IF EXISTS `chat_bot_assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_bot_assessments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `chat_bot_category_id` bigint unsigned NOT NULL,
  `score` double(8,2) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_bot_assessments_user_id_foreign` (`user_id`),
  KEY `chat_bot_assessments_chat_bot_category_id_foreign` (`chat_bot_category_id`),
  CONSTRAINT `chat_bot_assessments_chat_bot_category_id_foreign` FOREIGN KEY (`chat_bot_category_id`) REFERENCES `chat_bot_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chat_bot_assessments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_bot_categories`
--

DROP TABLE IF EXISTS `chat_bot_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_bot_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `calculation_step_macro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_bot_options`
--

DROP TABLE IF EXISTS `chat_bot_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_bot_options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chat_bot_question_id` bigint unsigned NOT NULL,
  `option` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_bot_options_chat_bot_question_id_foreign` (`chat_bot_question_id`),
  CONSTRAINT `chat_bot_options_chat_bot_question_id_foreign` FOREIGN KEY (`chat_bot_question_id`) REFERENCES `chat_bot_questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_bot_questions`
--

DROP TABLE IF EXISTS `chat_bot_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_bot_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chat_bot_category_id` bigint unsigned NOT NULL,
  `language` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'english',
  `question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_bot_questions_chat_bot_category_id_foreign` (`chat_bot_category_id`),
  CONSTRAINT `chat_bot_questions_chat_bot_category_id_foreign` FOREIGN KEY (`chat_bot_category_id`) REFERENCES `chat_bot_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_bot_report_characteristics`
--

DROP TABLE IF EXISTS `chat_bot_report_characteristics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_bot_report_characteristics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chat_bot_category_id` bigint unsigned NOT NULL,
  `minimum` double(8,2) NOT NULL,
  `maximum` double(8,2) DEFAULT NULL,
  `interpretation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_bot_report_characteristics_chat_bot_category_id_foreign` (`chat_bot_category_id`),
  CONSTRAINT `chat_bot_report_characteristics_chat_bot_category_id_foreign` FOREIGN KEY (`chat_bot_category_id`) REFERENCES `chat_bot_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_channels`
--

DROP TABLE IF EXISTS `chat_channels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_channels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `psychologist_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `message` text,
  `referral` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coupon_plans`
--

DROP TABLE IF EXISTS `coupon_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` bigint unsigned NOT NULL,
  `coupon_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupon_plans_plan_id_foreign` (`plan_id`),
  KEY `coupon_plans_coupon_id_foreign` (`coupon_id`),
  CONSTRAINT `coupon_plans_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `coupon_plans_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coupon_receipts`
--

DROP TABLE IF EXISTS `coupon_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_receipts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` bigint unsigned NOT NULL,
  `receipt_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupon_receipts_coupon_id_foreign` (`coupon_id`),
  KEY `coupon_receipts_receipt_id_foreign` (`receipt_id`),
  KEY `coupon_receipts_user_id_foreign` (`user_id`),
  CONSTRAINT `coupon_receipts_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `coupon_receipts_receipt_id_foreign` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `coupon_receipts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_percent` tinyint NOT NULL,
  `max_uses` bigint DEFAULT NULL,
  `total_uses` bigint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '0',
  `expired_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `data_contents`
--

DROP TABLE IF EXISTS `data_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_contents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preference` int DEFAULT NULL,
  `data_group_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `carousel_section_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `data_contents_data_group_id_foreign` (`data_group_id`),
  KEY `data_contents_carousel_section_id_foreign` (`carousel_section_id`),
  CONSTRAINT `data_contents_carousel_section_id_foreign` FOREIGN KEY (`carousel_section_id`) REFERENCES `carousel_sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `data_contents_data_group_id_foreign` FOREIGN KEY (`data_group_id`) REFERENCES `data_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `data_groups`
--

DROP TABLE IF EXISTS `data_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `discussion_topics`
--

DROP TABLE IF EXISTS `discussion_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discussion_topics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `duration_types`
--

DROP TABLE IF EXISTS `duration_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `duration_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint NOT NULL COMMENT '1->onetime 2-> Session 3-> Year',
  `value` int NOT NULL DEFAULT '1' COMMENT '1)minutes for session 2)months for year',
  `frequency` tinyint NOT NULL DEFAULT '1' COMMENT 'example 2 sessions frequency is 2 ',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dynamic_bundle_plans`
--

DROP TABLE IF EXISTS `dynamic_bundle_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dynamic_bundle_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int NOT NULL,
  `plan_id` int NOT NULL,
  `sessions` int unsigned DEFAULT NULL COMMENT 'Number of sessions granted for session-based plans (e.g. HappiTALK). NULL = not applicable',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `edit_buttons`
--

DROP TABLE IF EXISTS `edit_buttons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `edit_buttons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `button_content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `button_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `education_service_authors`
--

DROP TABLE IF EXISTS `education_service_authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `education_service_authors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `expert_level_plans`
--

DROP TABLE IF EXISTS `expert_level_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expert_level_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `expert_level_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expert_level_plans_expert_level_id_foreign` (`expert_level_id`),
  KEY `expert_level_plans_plan_id_foreign` (`plan_id`),
  CONSTRAINT `expert_level_plans_expert_level_id_foreign` FOREIGN KEY (`expert_level_id`) REFERENCES `expert_levels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `expert_level_plans_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `expert_levels`
--

DROP TABLE IF EXISTS `expert_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expert_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feedbacks`
--

DROP TABLE IF EXISTS `feedbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedbacks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `application_rate_emoji_id` int NOT NULL,
  `feedback_message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `group_chats`
--

DROP TABLE IF EXISTS `group_chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `group_chats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `user_unread_message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `psychologist_id` int DEFAULT NULL,
  `psychologist_unread_message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `group_id` int DEFAULT NULL,
  `assigned_date_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_message_deliver_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_active_for_chat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happi_learn_contents`
--

DROP TABLE IF EXISTS `happi_learn_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happi_learn_contents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'english',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'free',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parameters` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` longtext COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` longtext COLLATE utf8mb4_unicode_ci,
  `date_of_upload` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_deleted` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happibuddy_monthy_reports`
--

DROP TABLE IF EXISTS `happibuddy_monthy_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happibuddy_monthy_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `psychologist_id` int NOT NULL,
  `session_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `presenting_complaints` longtext COLLATE utf8mb4_unicode_ci,
  `session_summary` longtext COLLATE utf8mb4_unicode_ci,
  `hardword_asigned` longtext COLLATE utf8mb4_unicode_ci,
  `plan_for_next_session` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiguide_notes_for_user_by_psys`
--

DROP TABLE IF EXISTS `happiguide_notes_for_user_by_psys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiguide_notes_for_user_by_psys` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `guide_session_id` int NOT NULL,
  `case_history` longtext COLLATE utf8mb4_unicode_ci,
  `username` longtext COLLATE utf8mb4_unicode_ci,
  `time` longtext COLLATE utf8mb4_unicode_ci,
  `duration` longtext COLLATE utf8mb4_unicode_ci,
  `name_of_therapist` longtext COLLATE utf8mb4_unicode_ci,
  `age` longtext COLLATE utf8mb4_unicode_ci,
  `gender` longtext COLLATE utf8mb4_unicode_ci,
  `occupation` longtext COLLATE utf8mb4_unicode_ci,
  `qualification` longtext COLLATE utf8mb4_unicode_ci,
  `presenting_complaints` longtext COLLATE utf8mb4_unicode_ci,
  `past_psychology_history` longtext COLLATE utf8mb4_unicode_ci,
  `medical_history` longtext COLLATE utf8mb4_unicode_ci,
  `family_psychological_histroy` longtext COLLATE utf8mb4_unicode_ci,
  `session_summary` longtext COLLATE utf8mb4_unicode_ci,
  `diagnosis` longtext COLLATE utf8mb4_unicode_ci,
  `plan_for_therpy_treatment` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiguide_session_compositions`
--

DROP TABLE IF EXISTS `happiguide_session_compositions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiguide_session_compositions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `happiguide_session_id` int NOT NULL,
  `twillio_composition_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiguide_session_opinion_psychologists`
--

DROP TABLE IF EXISTS `happiguide_session_opinion_psychologists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiguide_session_opinion_psychologists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `psychologist_id` int NOT NULL,
  `happiguide_session_id` int NOT NULL,
  `session_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `presenting_complaints` longtext COLLATE utf8mb4_unicode_ci,
  `session_summary` longtext COLLATE utf8mb4_unicode_ci,
  `hardword_asigned` longtext COLLATE utf8mb4_unicode_ci,
  `plan_for_next_session` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiguide_session_opinion_users`
--

DROP TABLE IF EXISTS `happiguide_session_opinion_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiguide_session_opinion_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `happiguide_session_id` int NOT NULL,
  `application_rate_emoji_id` int DEFAULT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `additional_comment` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiguide_sessions`
--

DROP TABLE IF EXISTS `happiguide_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiguide_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `psychologist_id` int NOT NULL,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `room_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_start` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_end` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_notification_emit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happilife_reminders`
--

DROP TABLE IF EXISTS `happilife_reminders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happilife_reminders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `assessment_id` bigint unsigned NOT NULL,
  `source` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stage` tinyint unsigned DEFAULT NULL,
  `channels` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answered` int unsigned NOT NULL DEFAULT '0',
  `total` int unsigned NOT NULL DEFAULT '0',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_by_admin_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `happilife_reminders_user_id_index` (`user_id`),
  KEY `happilife_reminders_assessment_id_index` (`assessment_id`),
  KEY `happilife_reminders_assessment_id_source_created_at_index` (`assessment_id`,`source`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiself_content_answers`
--

DROP TABLE IF EXISTS `happiself_content_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiself_content_answers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `happiself_content_id` int NOT NULL,
  `question_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiself_contents`
--

DROP TABLE IF EXISTS `happiself_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiself_contents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `happiself_course_id` int NOT NULL,
  `happiself_sub_course_id` int NOT NULL,
  `title` longtext COLLATE utf8mb4_unicode_ci,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `content_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `media` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_media_downloadable` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `correct_answer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiself_course_likes`
--

DROP TABLE IF EXISTS `happiself_course_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiself_course_likes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `happiself_course_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiself_courses`
--

DROP TABLE IF EXISTS `happiself_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiself_courses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiself_libraries`
--

DROP TABLE IF EXISTS `happiself_libraries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiself_libraries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `library_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiself_library_contents`
--

DROP TABLE IF EXISTS `happiself_library_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiself_library_contents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `happiself_library_id` int NOT NULL,
  `content_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiself_question_options`
--

DROP TABLE IF EXISTS `happiself_question_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiself_question_options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `happiself_content_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiself_sub_courses`
--

DROP TABLE IF EXISTS `happiself_sub_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiself_sub_courses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `happiself_course_id` int NOT NULL,
  `sub_course_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `count_for_sequence` int NOT NULL DEFAULT '0',
  `deleted_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiself_user_notes`
--

DROP TABLE IF EXISTS `happiself_user_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiself_user_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happiself_users_last_visit_sub_course_and_contents`
--

DROP TABLE IF EXISTS `happiself_users_last_visit_sub_course_and_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happiself_users_last_visit_sub_course_and_contents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `happiself_course_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `happiself_sub_course_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_complete_happiself_sub_course` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `happiself_content_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_complete_happiself_content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happitalk_bookings`
--

DROP TABLE IF EXISTS `happitalk_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happitalk_bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `psychologist_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount_after_deduction` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan_id` int DEFAULT NULL,
  `total_no_of_session` int NOT NULL,
  `remaining_session` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happitalk_notes_for_user_by_psys`
--

DROP TABLE IF EXISTS `happitalk_notes_for_user_by_psys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happitalk_notes_for_user_by_psys` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `case_history` longtext COLLATE utf8mb4_unicode_ci,
  `username` longtext COLLATE utf8mb4_unicode_ci,
  `time` longtext COLLATE utf8mb4_unicode_ci,
  `duration` longtext COLLATE utf8mb4_unicode_ci,
  `name_of_therapist` longtext COLLATE utf8mb4_unicode_ci,
  `age` longtext COLLATE utf8mb4_unicode_ci,
  `gender` longtext COLLATE utf8mb4_unicode_ci,
  `occupation` longtext COLLATE utf8mb4_unicode_ci,
  `qualification` longtext COLLATE utf8mb4_unicode_ci,
  `presenting_complaints` longtext COLLATE utf8mb4_unicode_ci,
  `past_psychology_history` longtext COLLATE utf8mb4_unicode_ci,
  `medical_history` longtext COLLATE utf8mb4_unicode_ci,
  `family_psychological_histroy` longtext COLLATE utf8mb4_unicode_ci,
  `session_summary` longtext COLLATE utf8mb4_unicode_ci,
  `diagnosis` longtext COLLATE utf8mb4_unicode_ci,
  `plan_for_therpy_treatment` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happitalk_penalty_clauses`
--

DROP TABLE IF EXISTS `happitalk_penalty_clauses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happitalk_penalty_clauses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `for_b2b_user_for_one_credit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2',
  `for_b2b_user_for_half_credit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2',
  `for_b2c_user_for_one_credit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `for_b2c_user_for_half_credit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happitalk_session_compositions`
--

DROP TABLE IF EXISTS `happitalk_session_compositions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happitalk_session_compositions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `happitalk_session_id` int NOT NULL,
  `twillio_composition_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happitalk_session_opinion_psychologists`
--

DROP TABLE IF EXISTS `happitalk_session_opinion_psychologists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happitalk_session_opinion_psychologists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `psychologist_id` int NOT NULL,
  `happitalk_session_id` int NOT NULL,
  `session_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `presenting_complaints` longtext COLLATE utf8mb4_unicode_ci,
  `session_summary` longtext COLLATE utf8mb4_unicode_ci,
  `hardword_asigned` longtext COLLATE utf8mb4_unicode_ci,
  `plan_for_next_session` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happitalk_session_opinion_users`
--

DROP TABLE IF EXISTS `happitalk_session_opinion_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happitalk_session_opinion_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `happitalk_session_id` int NOT NULL,
  `application_rate_emoji_id` int DEFAULT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `additional_comment` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happitalk_sessions`
--

DROP TABLE IF EXISTS `happitalk_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happitalk_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `happitalk_booking_id` int NOT NULL,
  `user_id` int NOT NULL,
  `user_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `psychologist_id` int NOT NULL,
  `amount_per_session_psy` int DEFAULT NULL,
  `date` date NOT NULL,
  `time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `room_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_start` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_end` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_req_accepted` int NOT NULL DEFAULT '0',
  `req_rejected_reason` longtext COLLATE utf8mb4_unicode_ci,
  `is_cancel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `cancel_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancel_reason` longtext COLLATE utf8mb4_unicode_ci,
  `is_notification_emit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_notification_emit_30_min_ago` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_notification_emit_24_hour_ago` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_user_join` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_psy_join` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `psy_joined_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `psy_leave_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_recording_permission` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `happitalk_taxes`
--

DROP TABLE IF EXISTS `happitalk_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `happitalk_taxes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tds_percentage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '10',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `languages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `like_happi_learn_contents`
--

DROP TABLE IF EXISTS `like_happi_learn_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `like_happi_learn_contents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `happi_learn_content_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mobile_otps`
--

DROP TABLE IF EXISTS `mobile_otps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mobile_otps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NOT NULL,
  `verified_token` text COLLATE utf8mb4_unicode_ci,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mobile_otps_mobile_index` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mood_meter_emojies`
--

DROP TABLE IF EXISTS `mood_meter_emojies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mood_meter_emojies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notification_lists`
--

DROP TABLE IF EXISTS `notification_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_lists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notification_messages`
--

DROP TABLE IF EXISTS `notification_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `english` longtext COLLATE utf8mb4_unicode_ci,
  `hindi` longtext COLLATE utf8mb4_unicode_ci,
  `punjabi` longtext COLLATE utf8mb4_unicode_ci,
  `marathi` longtext COLLATE utf8mb4_unicode_ci,
  `telugu` longtext COLLATE utf8mb4_unicode_ci,
  `bangali` longtext COLLATE utf8mb4_unicode_ci,
  `deleted_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offer_screen_contents`
--

DROP TABLE IF EXISTS `offer_screen_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offer_screen_contents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offers`
--

DROP TABLE IF EXISTS `offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount` tinyint NOT NULL DEFAULT '0',
  `price` double NOT NULL DEFAULT '0',
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `plan_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `offers_plan_id_foreign` (`plan_id`) USING BTREE,
  CONSTRAINT `offers_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `option_questions`
--

DROP TABLE IF EXISTS `option_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `option_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `question_id` bigint unsigned NOT NULL,
  `option_id` bigint unsigned NOT NULL,
  `weightage` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `option_questions_question_id_foreign` (`question_id`),
  KEY `option_questions_option_id_foreign` (`option_id`),
  CONSTRAINT `option_questions_option_id_foreign` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `option_questions_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `options`
--

DROP TABLE IF EXISTS `options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `option` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `organization_logos`
--

DROP TABLE IF EXISTS `organization_logos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organization_logos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `image_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `organization_page_data`
--

DROP TABLE IF EXISTS `organization_page_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organization_page_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `organizations`
--

DROP TABLE IF EXISTS `organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organizations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `organization_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_logo` int NOT NULL DEFAULT '0',
  `powered_by` int NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `other_service_subscribers`
--

DROP TABLE IF EXISTS `other_service_subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `other_service_subscribers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `other_service_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `other_service_subscribers_other_service_id_foreign` (`other_service_id`),
  CONSTRAINT `other_service_subscribers_other_service_id_foreign` FOREIGN KEY (`other_service_id`) REFERENCES `other_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `other_services`
--

DROP TABLE IF EXISTS `other_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `other_services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `discount` double(8,2) unsigned NOT NULL DEFAULT '0.00',
  `buy_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coupon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_type_group_id` bigint unsigned NOT NULL,
  `publish_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=>Draft, 1=>Published',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `other_services_service_type_group_id_foreign` (`service_type_group_id`),
  CONSTRAINT `other_services_service_type_group_id_foreign` FOREIGN KEY (`service_type_group_id`) REFERENCES `service_type_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `our_clients`
--

DROP TABLE IF EXISTS `our_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `our_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `preference` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `our_teams`
--

DROP TABLE IF EXISTS `our_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `our_teams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('Founders','Experts','Psychologists') COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `preference` int NOT NULL,
  `linkedin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bundle` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `validity` int unsigned DEFAULT NULL COMMENT 'Validity of the plan/bundle in days. NULL = lifetime',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `package_id` bigint unsigned NOT NULL,
  `duration_type_id` bigint unsigned DEFAULT NULL,
  `price` double(8,2) NOT NULL DEFAULT '0.00',
  `expert_level_id` bigint unsigned DEFAULT NULL,
  `active` tinyint NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plans_package_id_foreign` (`package_id`),
  KEY `plans_duration_type_id_foreign` (`duration_type_id`),
  KEY `plans_expert_level_id_foreign` (`expert_level_id`),
  CONSTRAINT `plans_duration_type_id_foreign` FOREIGN KEY (`duration_type_id`) REFERENCES `duration_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `plans_expert_level_id_foreign` FOREIGN KEY (`expert_level_id`) REFERENCES `expert_levels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `plans_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_categories`
--

DROP TABLE IF EXISTS `post_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `media` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_category_id` bigint unsigned NOT NULL,
  `publish_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=>Draft, 1=>Published',
  `restricted_content` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=>Free, 1=>Paid',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_post_category_id_foreign` (`post_category_id`),
  CONSTRAINT `posts_post_category_id_foreign` FOREIGN KEY (`post_category_id`) REFERENCES `post_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prompts`
--

DROP TABLE IF EXISTS `prompts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prompts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `psychologist_appointments`
--

DROP TABLE IF EXISTS `psychologist_appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `psychologist_appointments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `psychologist_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time_slot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `appointment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `dealId` bigint DEFAULT NULL,
  `sessions` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `psychologist_appointments_psychologist_id_foreign` (`psychologist_id`),
  KEY `psychologist_appointments_user_id_foreign` (`user_id`),
  CONSTRAINT `psychologist_appointments_psychologist_id_foreign` FOREIGN KEY (`psychologist_id`) REFERENCES `psychologists` (`id`) ON DELETE SET NULL,
  CONSTRAINT `psychologist_appointments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `psychologist_availabilities`
--

DROP TABLE IF EXISTS `psychologist_availabilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `psychologist_availabilities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `psychologist_id` bigint unsigned NOT NULL,
  `psychologist_slot_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `psychologist_availabilities_psychologist_id_foreign` (`psychologist_id`),
  KEY `psychologist_availabilities_psychologist_slot_id_foreign` (`psychologist_slot_id`),
  CONSTRAINT `psychologist_availabilities_psychologist_id_foreign` FOREIGN KEY (`psychologist_id`) REFERENCES `psychologists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `psychologist_availabilities_psychologist_slot_id_foreign` FOREIGN KEY (`psychologist_slot_id`) REFERENCES `psychologist_date_time_slots` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `psychologist_date_time_slots`
--

DROP TABLE IF EXISTS `psychologist_date_time_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `psychologist_date_time_slots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `psychologist_languages`
--

DROP TABLE IF EXISTS `psychologist_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `psychologist_languages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `psychologist_id` bigint unsigned NOT NULL,
  `language_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `psychologist_languages_psychologist_id_foreign` (`psychologist_id`),
  KEY `psychologist_languages_language_id_foreign` (`language_id`),
  CONSTRAINT `psychologist_languages_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `psychologist_languages_psychologist_id_foreign` FOREIGN KEY (`psychologist_id`) REFERENCES `psychologists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `psychologist_plan`
--

DROP TABLE IF EXISTS `psychologist_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `psychologist_plan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `psychologist_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned NOT NULL,
  `selling_price` double(8,2) NOT NULL DEFAULT '0.00',
  `discount` double(8,2) NOT NULL DEFAULT '0.00',
  `cost_price` double(8,2) NOT NULL DEFAULT '0.00',
  `day` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `psychologist_plan_psychologist_id_foreign` (`psychologist_id`),
  KEY `psychologist_plan_plan_id_foreign` (`plan_id`),
  CONSTRAINT `psychologist_plan_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `psychologist_plan_psychologist_id_foreign` FOREIGN KEY (`psychologist_id`) REFERENCES `psychologists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `psychologist_specializations`
--

DROP TABLE IF EXISTS `psychologist_specializations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `psychologist_specializations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `psychologist_id` bigint unsigned NOT NULL,
  `specialization_id` bigint unsigned NOT NULL,
  `price` double(8,2) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `psychologist_specializations_psychologist_id_foreign` (`psychologist_id`),
  KEY `psychologist_specializations_specialization_id_foreign` (`specialization_id`),
  CONSTRAINT `psychologist_specializations_psychologist_id_foreign` FOREIGN KEY (`psychologist_id`) REFERENCES `psychologists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `psychologist_specializations_specialization_id_foreign` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `psychologists`
--

DROP TABLE IF EXISTS `psychologists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `psychologists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meet_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `highest_qualification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `internation_cert` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_id` bigint unsigned DEFAULT NULL,
  `expert_level_id` bigint unsigned NOT NULL,
  `expert_category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'None',
  `slot1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slot2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commission_percentage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '60',
  `price_per_session` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '200',
  `total_user_for_chat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tds_percentage` double(8,2) NOT NULL DEFAULT '10.00',
  PRIMARY KEY (`id`),
  KEY `psychologists_city_id_foreign` (`city_id`),
  KEY `psychologists_expert_level_id_foreign` (`expert_level_id`),
  CONSTRAINT `psychologists_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `psychologists_expert_level_id_foreign` FOREIGN KEY (`expert_level_id`) REFERENCES `expert_levels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'english',
  `question` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `batch_category_id` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questions_category_id_foreign` (`category_id`),
  KEY `questions_batch_category_id_foreign` (`batch_category_id`),
  CONSTRAINT `questions_batch_category_id_foreign` FOREIGN KEY (`batch_category_id`) REFERENCES `batch_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `questions_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quotes`
--

DROP TABLE IF EXISTS `quotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quote` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `raise_queries`
--

DROP TABLE IF EXISTS `raise_queries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `raise_queries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `query` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `platform` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'website',
  PRIMARY KEY (`id`),
  KEY `raise_queries_user_id_foreign` (`user_id`),
  CONSTRAINT `raise_queries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rating_pictures`
--

DROP TABLE IF EXISTS `rating_pictures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rating_pictures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `receipt_packages`
--

DROP TABLE IF EXISTS `receipt_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `receipt_packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `receipt_id` bigint unsigned DEFAULT NULL,
  `plan_id` bigint unsigned NOT NULL,
  `amount` double(8,2) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `receipt_packages_receipt_id_foreign` (`receipt_id`),
  KEY `receipt_packages_package_id_foreign` (`plan_id`),
  KEY `receipt_packages_plan_id_foreign` (`plan_id`) USING BTREE,
  CONSTRAINT `receipt_packages_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `receipt_packages_receipt_id_foreign` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `receipts`
--

DROP TABLE IF EXISTS `receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `receipts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `marchant_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double(8,2) NOT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INR',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_id` longtext COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `receipts_user_id_foreign` (`user_id`),
  CONSTRAINT `receipts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recently_viewed_happi_learn_contents`
--

DROP TABLE IF EXISTS `recently_viewed_happi_learn_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recently_viewed_happi_learn_contents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `happi_learn_content_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `view_count` int unsigned NOT NULL DEFAULT '1' COMMENT 'How many times the user opened this HappiLEARN content',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recommendation_categories`
--

DROP TABLE IF EXISTS `recommendation_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recommendation_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recommendations`
--

DROP TABLE IF EXISTS `recommendations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recommendations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_profile_id` bigint unsigned NOT NULL,
  `recommendation_category_id` bigint unsigned NOT NULL,
  `title_1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_3` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_3` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_characteristics`
--

DROP TABLE IF EXISTS `report_characteristics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_characteristics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `minimum_score` double(8,2) NOT NULL,
  `maximum_score` double(8,2) DEFAULT NULL,
  `meter_scale_level_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint unsigned NOT NULL,
  `batch_category_id` bigint unsigned DEFAULT NULL,
  `summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `WOL_representation` enum('straight','inverse','none') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `WOL_fill_area` tinyint DEFAULT NULL,
  `included_in_report` tinyint(1) NOT NULL,
  `show_meter_scale` tinyint(1) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `rating_picture_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_characteristics_category_id_foreign` (`category_id`),
  KEY `report_characteristics_batch_category_id_foreign` (`batch_category_id`),
  KEY `report_characteristics_rating_picture_id_foreign` (`rating_picture_id`),
  CONSTRAINT `report_characteristics_batch_category_id_foreign` FOREIGN KEY (`batch_category_id`) REFERENCES `batch_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `report_characteristics_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `report_characteristics_rating_picture_id_foreign` FOREIGN KEY (`rating_picture_id`) REFERENCES `rating_pictures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reward_point_instances`
--

DROP TABLE IF EXISTS `reward_point_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reward_point_instances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `action_performed` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `points_to_be_given` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schedule_notifications`
--

DROP TABLE IF EXISTS `schedule_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_language` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scheduled_date_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_notification_delivered` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scores`
--

DROP TABLE IF EXISTS `scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `result` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` int NOT NULL,
  `smoothness` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `liveliness` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `control` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `energy_range` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clarity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `crispness` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `speech_rate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pause_duration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inferred_at` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `service_images`
--

DROP TABLE IF EXISTS `service_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `overview` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `service_meta_data`
--

DROP TABLE IF EXISTS `service_meta_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_meta_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `other_service_id` bigint unsigned NOT NULL,
  `education_service_author_id` bigint unsigned NOT NULL,
  `discounted_price` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `rating` int unsigned NOT NULL DEFAULT '0',
  `downloads` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_meta_data_other_service_id_foreign` (`other_service_id`),
  KEY `service_meta_data_education_service_author_id_foreign` (`education_service_author_id`),
  CONSTRAINT `service_meta_data_education_service_author_id_foreign` FOREIGN KEY (`education_service_author_id`) REFERENCES `education_service_authors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_meta_data_other_service_id_foreign` FOREIGN KEY (`other_service_id`) REFERENCES `other_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `service_type_groups`
--

DROP TABLE IF EXISTS `service_type_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_type_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_type_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_type_groups_service_type_id_foreign` (`service_type_id`),
  CONSTRAINT `service_type_groups_service_type_id_foreign` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `service_types`
--

DROP TABLE IF EXISTS `service_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `services_receipts`
--

DROP TABLE IF EXISTS `services_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services_receipts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `marchant_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double(8,2) NOT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INR',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_service_subscriber_id` bigint unsigned NOT NULL,
  `other_service_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `services_receipts_other_service_subscriber_id_foreign` (`other_service_subscriber_id`),
  KEY `services_receipts_other_service_id_foreign` (`other_service_id`),
  CONSTRAINT `services_receipts_other_service_id_foreign` FOREIGN KEY (`other_service_id`) REFERENCES `other_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `services_receipts_other_service_subscriber_id_foreign` FOREIGN KEY (`other_service_subscriber_id`) REFERENCES `other_service_subscribers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `specializations`
--

DROP TABLE IF EXISTS `specializations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `specializations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `static_sections`
--

DROP TABLE IF EXISTS `static_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `static_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `data_group_id` bigint unsigned NOT NULL,
  `section` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_content_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `static_sections_data_group_id_foreign` (`data_group_id`),
  KEY `static_sections_data_content_id_foreign` (`data_content_id`),
  CONSTRAINT `static_sections_data_content_id_foreign` FOREIGN KEY (`data_content_id`) REFERENCES `data_contents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `static_sections_data_group_id_foreign` FOREIGN KEY (`data_group_id`) REFERENCES `data_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suicidal_thoughts`
--

DROP TABLE IF EXISTS `suicidal_thoughts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suicidal_thoughts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `thrive_codes`
--

DROP TABLE IF EXISTS `thrive_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `thrive_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `organization_id` bigint unsigned DEFAULT NULL,
  `expired_at` datetime DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `thrive_codes_user_id_foreign` (`user_id`),
  KEY `thrive_codes_organization_id_foreign` (`organization_id`),
  CONSTRAINT `thrive_codes_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `thrive_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `token_categories`
--

DROP TABLE IF EXISTS `token_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `token_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `token_meta_data`
--

DROP TABLE IF EXISTS `token_meta_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `token_meta_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `meta_data` json NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `token_meta_data_organization_id_foreign` (`organization_id`),
  CONSTRAINT `token_meta_data_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `token_plans`
--

DROP TABLE IF EXISTS `token_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `token_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `token_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bundle_status_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `token_plans_token_id_foreign` (`token_id`),
  KEY `token_plans_plan_id_foreign` (`plan_id`),
  KEY `token_plans_bundle_status_id_foreign` (`bundle_status_id`),
  CONSTRAINT `token_plans_bundle_status_id_foreign` FOREIGN KEY (`bundle_status_id`) REFERENCES `bundle_statuses` (`id`),
  CONSTRAINT `token_plans_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`),
  CONSTRAINT `token_plans_token_id_foreign` FOREIGN KEY (`token_id`) REFERENCES `tokens` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `organization_id` bigint unsigned DEFAULT NULL,
  `expired_at` datetime DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `token_meta_data_id` bigint unsigned DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `use_limit` int NOT NULL DEFAULT '1',
  `use_count` int NOT NULL DEFAULT '0',
  `valid_till` date DEFAULT NULL COMMENT 'Last date (inclusive) the B2B code can be redeemed. NULL = no expiry',
  PRIMARY KEY (`id`),
  KEY `tokens_organization_id_foreign` (`organization_id`),
  CONSTRAINT `tokens_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_languages`
--

DROP TABLE IF EXISTS `user_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_languages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_moods`
--

DROP TABLE IF EXISTS `user_moods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_moods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `emoji_id` int NOT NULL,
  `text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> active 0->inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_reward_point_records`
--

DROP TABLE IF EXISTS `user_reward_point_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_reward_point_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `points_earned` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_performed` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_tokens`
--

DROP TABLE IF EXISTS `user_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `token_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_tokens_user_id_foreign` (`user_id`),
  KEY `user_tokens_token_id_foreign` (`token_id`),
  CONSTRAINT `user_tokens_token_id_foreign` FOREIGN KEY (`token_id`) REFERENCES `tokens` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_profile_id` bigint unsigned DEFAULT NULL,
  `country_id` bigint unsigned DEFAULT NULL,
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` int DEFAULT NULL,
  `gender` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_status` enum('active','blocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `lead_id` int DEFAULT NULL,
  `deal_id` int DEFAULT NULL,
  `happimynd_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'website',
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'english',
  `is_account_deleted` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `refer_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_refer_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_reward_noti_emit_number` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `users_user_profile_id_foreign` (`user_profile_id`),
  CONSTRAINT `users_user_profile_id_foreign` FOREIGN KEY (`user_profile_id`) REFERENCES `user_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_ratings`
--

DROP TABLE IF EXISTS `users_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_ratings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `application_rate_emoji_id` int DEFAULT NULL,
  `review` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verify_guardians`
--

DROP TABLE IF EXISTS `verify_guardians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `verify_guardians` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `session` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verify_psychologists`
--

DROP TABLE IF EXISTS `verify_psychologists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `verify_psychologists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email_otp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verify` tinyint(1) NOT NULL DEFAULT '0',
  `psychologist_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `verify_psychologists_psychologist_id_foreign` (`psychologist_id`),
  CONSTRAINT `verify_psychologists_psychologist_id_foreign` FOREIGN KEY (`psychologist_id`) REFERENCES `psychologists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verify_users`
--

DROP TABLE IF EXISTS `verify_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `verify_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mobile_otp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_otp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_verify` tinyint(1) NOT NULL DEFAULT '0',
  `email_verify` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `forget_email_permission` tinyint(1) NOT NULL DEFAULT '1',
  `forget_mobile_permission` tinyint(1) NOT NULL DEFAULT '1',
  `subscribe_newsletter_blog` tinyint(1) NOT NULL DEFAULT '1',
  `expires_at` datetime NOT NULL DEFAULT '2021-04-30 05:28:17',
  PRIMARY KEY (`id`),
  KEY `verify_users_user_id_foreign` (`user_id`),
  CONSTRAINT `verify_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'happimynd'
--

--
-- Dumping routines for database 'happimynd'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


-- Migrations already applied to this schema
SET FOREIGN_KEY_CHECKS=0;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` VALUES (2,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` VALUES (3,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` VALUES (4,'2020_12_16_052403_create_permission_tables',1);
INSERT INTO `migrations` VALUES (5,'2020_12_16_100904_create_categories_table',1);
INSERT INTO `migrations` VALUES (6,'2020_12_16_101129_create_questions_table',1);
INSERT INTO `migrations` VALUES (7,'2020_12_16_101206_create_batches_table',1);
INSERT INTO `migrations` VALUES (8,'2020_12_16_101743_create_batch_category_table',1);
INSERT INTO `migrations` VALUES (9,'2020_12_16_101816_create_posts_table',1);
INSERT INTO `migrations` VALUES (10,'2020_12_16_101852_create_category_posts_table',1);
INSERT INTO `migrations` VALUES (11,'2020_12_22_150944_create_assessments_table',1);
INSERT INTO `migrations` VALUES (12,'2020_12_22_151249_create_options_table',1);
INSERT INTO `migrations` VALUES (13,'2020_12_22_151805_create_organizations_table',1);
INSERT INTO `migrations` VALUES (14,'2020_12_22_151838_create_tokens_table',1);
INSERT INTO `migrations` VALUES (15,'2020_12_23_135104_create_option_questions_table',1);
INSERT INTO `migrations` VALUES (16,'2020_12_23_15105_create_assessment_answers_table',1);
INSERT INTO `migrations` VALUES (17,'2020_12_26_091145_create_admins_table',1);
INSERT INTO `migrations` VALUES (18,'2021_01_09_103731_create_data_groups_table',1);
INSERT INTO `migrations` VALUES (19,'2021_01_09_103748_create_data_contents_table',1);
INSERT INTO `migrations` VALUES (20,'2021_01_19_084118_add_dashboard_cover_pic_row_to_data_group_table',1);
INSERT INTO `migrations` VALUES (21,'2021_01_19_090742_create_notifications_table',1);
INSERT INTO `migrations` VALUES (22,'2021_01_19_092455_create_report_characteristics_table',1);
INSERT INTO `migrations` VALUES (23,'2021_01_24_101122_create_raise_queries_table',1);
INSERT INTO `migrations` VALUES (24,'2021_01_30_131215_create_durations_table',1);
INSERT INTO `migrations` VALUES (25,'2021_01_30_131216_create_packages_table',1);
INSERT INTO `migrations` VALUES (26,'2021_01_31_150859_create_receipts_table',1);
INSERT INTO `migrations` VALUES (27,'2021_01_31_154601_create_bundle_statuses_table',1);
INSERT INTO `migrations` VALUES (28,'2021_02_01_171020_add_landing_page_video_to_data_group_table',1);
INSERT INTO `migrations` VALUES (29,'2021_02_03_114131_create_verify_users_table',1);
INSERT INTO `migrations` VALUES (30,'2021_02_05_162159_create_rating_pictures_table',1);
INSERT INTO `migrations` VALUES (31,'2021_02_06_070504_create_assessment_approves_table',1);
INSERT INTO `migrations` VALUES (32,'2021_02_08_182644_create_offers_table',1);
INSERT INTO `migrations` VALUES (33,'2021_02_08_200332_create_receipt_packages_table',1);
INSERT INTO `migrations` VALUES (34,'2021_02_16_222458_add_wol_fill_area_column',2);
INSERT INTO `migrations` VALUES (35,'2021_02_16_230646_insert_w_o_l_fill_area_data',2);
INSERT INTO `migrations` VALUES (36,'2021_02_18_115304_create_duration_types_table',3);
INSERT INTO `migrations` VALUES (37,'2021_02_18_120313_create_plans_table',3);
INSERT INTO `migrations` VALUES (38,'2021_02_18_134738_change_fk_offer_table',3);
INSERT INTO `migrations` VALUES (39,'2021_02_18_150455_table_cleanup',3);
INSERT INTO `migrations` VALUES (40,'2021_02_18_173325_create_token_plans_table',3);
INSERT INTO `migrations` VALUES (41,'2021_02_18_190627_bundle_statuses_table_alter',3);
INSERT INTO `migrations` VALUES (42,'2021_02_18_192307_receipt_package_table_alter',3);
INSERT INTO `migrations` VALUES (43,'2021_02_26_003937_create_jobs_table',4);
INSERT INTO `migrations` VALUES (44,'2021_02_28_150200_create_token_categories_table',5);
INSERT INTO `migrations` VALUES (45,'2021_02_28_153945_create_category_tokens_table',5);
INSERT INTO `migrations` VALUES (46,'2021_02_28_164251_create_token_meta_data_table',5);
INSERT INTO `migrations` VALUES (47,'2021_03_02_010441_create_availabilities_table',6);
INSERT INTO `migrations` VALUES (48,'2021_03_02_211823_add_meta_data_id_to_tokens_table',6);
INSERT INTO `migrations` VALUES (49,'2021_02_26_202132_update_user',7);
INSERT INTO `migrations` VALUES (50,'2021_03_04_222754_create_thrive_codes_table',8);
INSERT INTO `migrations` VALUES (52,'2021_03_05_232502_add_email_and_mobile_permisson_for_forgetpass_otp',9);
INSERT INTO `migrations` VALUES (53,'2021_03_12_145956_update-organization',10);
INSERT INTO `migrations` VALUES (54,'2021_03_22_121450_create_assessment_scores_table',11);
INSERT INTO `migrations` VALUES (55,'2021_03_16_160225_create_post_categories_table',12);
INSERT INTO `migrations` VALUES (56,'2021_03_18_011132_add_file_and_featured_column_to_post_table',12);
INSERT INTO `migrations` VALUES (57,'2021_04_01_203615_create_availability_dates_table',12);
INSERT INTO `migrations` VALUES (58,'2021_04_17_205550_add_bundle_status_column_to_token_plans',13);
INSERT INTO `migrations` VALUES (59,'2021_04_18_125955_add_column_email_to_token',13);
INSERT INTO `migrations` VALUES (60,'2021_04_20_115339_campaign_changes',13);
INSERT INTO `migrations` VALUES (61,'2021_04_20_123257_create_campaigns_table',13);
INSERT INTO `migrations` VALUES (62,'2021_04_22_080108_add_expiry_column_to_verify_users',14);
INSERT INTO `migrations` VALUES (63,'2021_04_27_151303_make_all_users_column_null',15);
INSERT INTO `migrations` VALUES (64,'2021_04_29_123642_create_user_profiles_table',15);
INSERT INTO `migrations` VALUES (65,'2021_04_29_124149_change_profile_type_in_users_table',15);
INSERT INTO `migrations` VALUES (66,'2021_04_29_134926_add_columns_to_batches_table',15);
INSERT INTO `migrations` VALUES (67,'2021_04_29_191103_add_columns_to_batch_category_to_questions_table',15);
INSERT INTO `migrations` VALUES (68,'2021_04_29_191507_add_column_to_report_characteristics_table',15);
INSERT INTO `migrations` VALUES (69,'2021_05_05_171648_add_call_option_to_assessment_approves_table',15);
INSERT INTO `migrations` VALUES (70,'2021_05_07_050629_create_calculation_steps_table',15);
INSERT INTO `migrations` VALUES (71,'2021_05_07_134528_add_calculcation_step_to_batch_category_table',15);
INSERT INTO `migrations` VALUES (72,'2021_05_07_160753_create_verify_guardians_table',15);
INSERT INTO `migrations` VALUES (73,'2021_05_12_205739_add_rating_picture_column_to_report_characteristics_table',15);
INSERT INTO `migrations` VALUES (74,'2021_05_13_051555_make_scale_level_name_null_in_report_characteristics_table',15);
INSERT INTO `migrations` VALUES (75,'2021_05_19_031642_add_mobile_column_to_verify_guardian',15);
INSERT INTO `migrations` VALUES (76,'2021_05_21_161840_add_sort_column_to_batch_category_table',15);
INSERT INTO `migrations` VALUES (77,'2021_05_24_220406_add_color_column_to_categories_table',15);
INSERT INTO `migrations` VALUES (78,'2021_05_25_202014_use_json_for_score_in_scores_table',15);
INSERT INTO `migrations` VALUES (79,'2021_05_26_154301_add_batch_id_to_assessments_table',15);
INSERT INTO `migrations` VALUES (80,'2021_07_03_122134_add_soft_delete_column_to_user_profiles_table',16);
INSERT INTO `migrations` VALUES (81,'2021_05_19_134507_create_service_types_table',17);
INSERT INTO `migrations` VALUES (82,'2021_05_19_134508_create_service_type_groups_table',17);
INSERT INTO `migrations` VALUES (83,'2021_05_19_143724_create_education_service_authors_table',17);
INSERT INTO `migrations` VALUES (84,'2021_05_19_143725_create_other_services_table',17);
INSERT INTO `migrations` VALUES (85,'2021_05_19_143726_create_service_meta_data_table',17);
INSERT INTO `migrations` VALUES (86,'2021_05_26_120718_create_other_service_subscribers_table',17);
INSERT INTO `migrations` VALUES (87,'2021_06_05_222721_create_expert_levels_table',17);
INSERT INTO `migrations` VALUES (88,'2021_06_05_222729_create_specializations_table',17);
INSERT INTO `migrations` VALUES (89,'2021_06_05_222741_create_cities_table',17);
INSERT INTO `migrations` VALUES (90,'2021_06_05_222742_create_psychologists_table',17);
INSERT INTO `migrations` VALUES (91,'2021_06_05_222801_create_psychologist_specializations_table',17);
INSERT INTO `migrations` VALUES (92,'2021_06_05_222812_create_psychologist_date_time_slots_table',17);
INSERT INTO `migrations` VALUES (93,'2021_06_05_222813_create_psychologist_availabilities_table',17);
INSERT INTO `migrations` VALUES (94,'2021_06_05_225208_create_expert_level_plans_table',17);
INSERT INTO `migrations` VALUES (95,'2021_06_05_232343_create_languages_table',17);
INSERT INTO `migrations` VALUES (96,'2021_06_05_232400_create_psychologist_languages_table',17);
INSERT INTO `migrations` VALUES (97,'2021_06_12_170407_add_psychologist_to_plans_table',17);
INSERT INTO `migrations` VALUES (98,'2021_06_12_205850_create_psychologist_plan_table',17);
INSERT INTO `migrations` VALUES (99,'2021_06_19_154630_create_psychologist_appointments_table',17);
INSERT INTO `migrations` VALUES (100,'2021_06_21_165951_create_services_receipts_table',17);
INSERT INTO `migrations` VALUES (101,'2021_07_16_200202_add_email_group_row_i_n_data_group_table',17);
INSERT INTO `migrations` VALUES (102,'2021_07_18_124425_add_session_in_psychologist_appointment',17);
INSERT INTO `migrations` VALUES (103,'2021_07_22_135659_make_day_column_nullable_in_psychologist_plans_table',17);
INSERT INTO `migrations` VALUES (104,'2021_07_26_235321_add_deal_id_in_psychologist_appointment',17);
INSERT INTO `migrations` VALUES (105,'2021_07_29_113827_add_price_column_to_receipt_package_table',18);
INSERT INTO `migrations` VALUES (106,'2021_07_29_145101_create_coupons_table',18);
INSERT INTO `migrations` VALUES (107,'2021_07_29_145144_create_coupon_receipts_table',18);
INSERT INTO `migrations` VALUES (108,'2021_07_29_152249_create_coupon_plans_table',18);
INSERT INTO `migrations` VALUES (109,'2021_08_27_191402_create_our_teams_table',19);
INSERT INTO `migrations` VALUES (110,'2021_07_05_233636_create_user_tokens_table',20);
INSERT INTO `migrations` VALUES (111,'2021_07_16_135430_shifting_token_user',20);
INSERT INTO `migrations` VALUES (112,'2021_10_08_142913_create_quotes_table',20);
INSERT INTO `migrations` VALUES (113,'2021_10_09_114738_create_our_clients_table',20);
INSERT INTO `migrations` VALUES (114,'2021_10_12_193249_create_static_sections_table',20);
INSERT INTO `migrations` VALUES (115,'2021_10_13_120100_create_organization_page_data_table',20);
INSERT INTO `migrations` VALUES (116,'2021_10_13_223211_create_organization_logos_table',20);
INSERT INTO `migrations` VALUES (117,'2021_10_14_214516_create_carousel_sections_table',20);
INSERT INTO `migrations` VALUES (118,'2021_10_14_215155_add_preference_and_carousel_in_data_content',20);
INSERT INTO `migrations` VALUES (119,'2021_10_27_161618_create_edit_buttons_table',20);
INSERT INTO `migrations` VALUES (120,'2021_10_28_133527_create_service_images_table',20);
INSERT INTO `migrations` VALUES (121,'2022_02_24_152936_create_user_languages',21);
INSERT INTO `migrations` VALUES (122,'2022_03_01_155843_add_fields_to_psychologist_table',21);
INSERT INTO `migrations` VALUES (123,'2022_03_02_134338_create_verify_psychologists_table',21);
INSERT INTO `migrations` VALUES (124,'2022_03_08_151753_create_chat_channels',21);
INSERT INTO `migrations` VALUES (125,'2022_03_09_130014_add_field_to_question_table',21);
INSERT INTO `migrations` VALUES (126,'2022_03_09_130719_add_fields_to_users_table',21);
INSERT INTO `migrations` VALUES (127,'2022_03_14_163615_create_happi_learn_contents',21);
INSERT INTO `migrations` VALUES (128,'2022_03_15_105240_create_like_happi_learn_contents',21);
INSERT INTO `migrations` VALUES (129,'2022_03_25_180416_create_group_chats',21);
INSERT INTO `migrations` VALUES (130,'2022_03_29_152349_add_field_to_group_chats',21);
INSERT INTO `migrations` VALUES (131,'2022_04_01_114047_create_recently_viewed_happi_learn_contents',21);
INSERT INTO `migrations` VALUES (132,'2022_04_13_180309_create_schedule_notifications',21);
INSERT INTO `migrations` VALUES (133,'2022_04_15_112052_create_users_ratings',21);
INSERT INTO `migrations` VALUES (134,'2022_04_18_172826_create_notification_lists',21);
INSERT INTO `migrations` VALUES (135,'2022_04_21_160214_create_application_rate_emojis',21);
INSERT INTO `migrations` VALUES (136,'2022_04_25_123659_create_notification_messages',21);
INSERT INTO `migrations` VALUES (137,'2022_05_09_162529_add_fields_to_organizations_table',22);
INSERT INTO `migrations` VALUES (138,'2022_05_17_105210_create_happiself_courses',23);
INSERT INTO `migrations` VALUES (139,'2022_05_17_150514_create_happiself_sub_courses',23);
INSERT INTO `migrations` VALUES (140,'2022_05_17_164549_create_happiself_contents',23);
INSERT INTO `migrations` VALUES (141,'2022_05_17_183310_create_happiself_question_options',23);
INSERT INTO `migrations` VALUES (142,'2022_05_18_171006_create_happiself_users_last_visit_sub_course_and_contents',23);
INSERT INTO `migrations` VALUES (143,'2022_05_23_165709_create_happiself_course_likes',23);
INSERT INTO `migrations` VALUES (144,'2022_05_24_105740_create_happiself_libraries',23);
INSERT INTO `migrations` VALUES (145,'2022_05_24_112218_create_happiself_library_contents',23);
INSERT INTO `migrations` VALUES (146,'2022_06_03_133345_create_happiself_user_notes',23);
INSERT INTO `migrations` VALUES (147,'2022_07_04_121901_create_happiself_content_answers',23);
INSERT INTO `migrations` VALUES (148,'2022_07_06_161618_add_fields_to_happiself_contents',23);
INSERT INTO `migrations` VALUES (149,'2022_08_16_124131_add_field_to_users_table',23);
INSERT INTO `migrations` VALUES (150,'2022_09_06_124558_add_field_to_receipts_table',24);
INSERT INTO `migrations` VALUES (157,'2022_10_06_152219_create_assign_psy_to_plans',24);
INSERT INTO `migrations` VALUES (159,'2022_11_15_102748_add_pricing_in_psychologists',24);
INSERT INTO `migrations` VALUES (160,'2022_11_15_105528_create_happitalk_taxes',24);
INSERT INTO `migrations` VALUES (161,'2022_12_09_101555_add_fields_to_assessments',24);
INSERT INTO `migrations` VALUES (162,'2022_12_09_114319_add_field_to_raise_queries',24);
INSERT INTO `migrations` VALUES (163,'2022_12_12_095038_create_feedbacks',24);
INSERT INTO `migrations` VALUES (165,'2022_12_13_104728_create_user_moods',24);
INSERT INTO `migrations` VALUES (166,'2022_12_13_110135_create_mood_meter_emojies',24);
INSERT INTO `migrations` VALUES (167,'2022_12_13_120303_create_reward_point_instances',24);
INSERT INTO `migrations` VALUES (168,'2022_12_13_132157_create_user_reward_point_records',24);
INSERT INTO `migrations` VALUES (169,'2022_12_21_110359_add_refer_field_to_users',24);
INSERT INTO `migrations` VALUES (172,'2023_03_13_115805_create_offer_screen_contents',26);
INSERT INTO `migrations` VALUES (173,'2023_03_19_220456_add_last_reward_pt_noti_emit_to_users',27);
INSERT INTO `migrations` VALUES (175,'2022_09_20_125842_create_happitalk_bookings',28);
INSERT INTO `migrations` VALUES (176,'2022_09_20_125905_create_happitalk_sessions',28);
INSERT INTO `migrations` VALUES (177,'2022_09_21_163610_create_happitalk_session_opinion_users',28);
INSERT INTO `migrations` VALUES (178,'2022_09_27_135710_create_happitalk_session_opinion_psychologists',28);
INSERT INTO `migrations` VALUES (179,'2022_09_27_163535_create_assign_psy_to_org_for_talks',28);
INSERT INTO `migrations` VALUES (180,'2022_09_29_155427_create_happitalk_notes_for_user_by_psys',28);
INSERT INTO `migrations` VALUES (202,'2024_02_21_120001_create_discussion_topics_table',29);
INSERT INTO `migrations` VALUES (203,'2024_02_21_120002_create_suicidal_thoughts_table',30);
INSERT INTO `migrations` VALUES (204,'2024_02_21_120003_create_recommendation_categories_table',31);
INSERT INTO `migrations` VALUES (205,'2024_02_21_120004_create_recommendations_table',32);
INSERT INTO `migrations` VALUES (206,'2024_02_21_120005_create_chat_bot_categories_table',33);
INSERT INTO `migrations` VALUES (207,'2024_02_21_120006_create_chat_bot_questions_table',34);
INSERT INTO `migrations` VALUES (208,'2024_02_21_120007_create_chat_bot_options_table',35);
INSERT INTO `migrations` VALUES (209,'2024_02_21_120008_create_chat_bot_report_characteristics_table',36);
INSERT INTO `migrations` VALUES (210,'2024_02_21_120009_create_chat_bot_assessments_table',37);
INSERT INTO `migrations` VALUES (211,'2024_05_16_145407_create_countries_table',38);
INSERT INTO `migrations` VALUES (212,'2024_05_16_145408_add_country_id_to_users_table',39);
INSERT INTO `migrations` VALUES (213,'2019_12_14_000001_create_personal_access_tokens_table',40);
INSERT INTO `migrations` VALUES (214,'2026_08_13_000001_add_validity_to_packages_table',41);
INSERT INTO `migrations` VALUES (215,'2026_08_13_000002_add_expires_at_to_bundle_statuses_table',41);
INSERT INTO `migrations` VALUES (216,'2026_08_24_000001_add_sessions_to_dynamic_bundle_plans_table',41);
INSERT INTO `migrations` VALUES (217,'2026_11_13_000001_add_valid_till_to_tokens_table',42);
INSERT INTO `migrations` VALUES (218,'2026_11_13_000002_add_view_count_to_recently_viewed_happi_learn_contents_table',42);
INSERT INTO `migrations` VALUES (219,'2026_11_23_000001_create_happilife_reminders_table',43);
SET FOREIGN_KEY_CHECKS=1;
