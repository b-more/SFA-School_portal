-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: u970673179_assisi
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.24.04.1

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
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_years` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `number_of_terms` int NOT NULL DEFAULT '3',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
INSERT INTO `academic_years` VALUES (1,'2024','2024-01-08','2024-12-20',NULL,0,0,3,'2025-10-15 03:45:16','2025-10-16 04:03:25'),(2,'2025','2025-01-06','2025-12-19',NULL,1,1,3,'2025-10-15 03:45:16','2025-10-17 03:44:20'),(3,'2023','2023-01-09','2023-12-15',NULL,0,0,3,'2025-10-15 03:45:16','2025-10-15 03:45:16');
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `albums`
--

DROP TABLE IF EXISTS `albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `albums` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int DEFAULT '0',
  `status` enum('published','draft') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `albums_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `albums`
--

LOCK TABLES `albums` WRITE;
/*!40000 ALTER TABLE `albums` DISABLE KEYS */;
/*!40000 ALTER TABLE `albums` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `class_section_id` bigint unsigned DEFAULT NULL,
  `grade_id` bigint unsigned DEFAULT NULL,
  `academic_year_id` bigint unsigned DEFAULT NULL,
  `term_id` bigint unsigned DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','late','excused') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `marked_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendances_student_id_attendance_date_unique` (`student_id`,`attendance_date`),
  KEY `attendances_term_id_foreign` (`term_id`),
  KEY `attendances_marked_by_foreign` (`marked_by`),
  KEY `attendances_student_id_attendance_date_index` (`student_id`,`attendance_date`),
  KEY `attendances_class_section_id_attendance_date_index` (`class_section_id`,`attendance_date`),
  KEY `attendances_grade_id_attendance_date_index` (`grade_id`,`attendance_date`),
  KEY `attendances_academic_year_id_term_id_index` (`academic_year_id`,`term_id`),
  KEY `attendances_status_index` (`status`),
  CONSTRAINT `attendances_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendances_class_section_id_foreign` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendances_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendances_marked_by_foreign` FOREIGN KEY (`marked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `terms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
INSERT INTO `attendances` VALUES (1,19,6,5,NULL,NULL,'2025-10-17','present','08:37:00',NULL,NULL,52,'2025-10-17 08:39:11','2025-10-17 08:39:11'),(2,2,6,5,NULL,NULL,'2025-10-17','present','08:37:00',NULL,NULL,52,'2025-10-17 08:39:11','2025-10-17 08:39:11'),(3,1,6,5,NULL,NULL,'2025-10-17','present','08:37:00',NULL,NULL,52,'2025-10-17 08:39:11','2025-10-17 08:39:11'),(4,17,6,5,NULL,NULL,'2025-10-17','present','08:37:00',NULL,NULL,52,'2025-10-17 08:39:11','2025-10-17 08:39:11'),(5,4,6,5,NULL,NULL,'2025-10-17','absent',NULL,NULL,NULL,52,'2025-10-17 08:39:11','2025-10-17 08:39:11'),(6,18,6,5,NULL,NULL,'2025-10-17','absent',NULL,NULL,NULL,52,'2025-10-17 08:39:11','2025-10-17 08:39:11');
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_id` bigint unsigned NOT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  KEY `audit_logs_user_id_index` (`user_id`),
  KEY `audit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_loans`
--

DROP TABLE IF EXISTS `book_loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `book_loans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `book_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `lent_by` bigint unsigned DEFAULT NULL,
  `returned_to` bigint unsigned DEFAULT NULL,
  `lent_date` date NOT NULL,
  `due_date` date NOT NULL,
  `returned_at` date DEFAULT NULL,
  `status` enum('active','returned','overdue','lost') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `fine_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fine_paid` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `condition_on_loan` text COLLATE utf8mb4_unicode_ci,
  `condition_on_return` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `book_loans_lent_by_foreign` (`lent_by`),
  KEY `book_loans_returned_to_foreign` (`returned_to`),
  KEY `book_loans_student_id_index` (`student_id`),
  KEY `book_loans_book_id_index` (`book_id`),
  KEY `book_loans_status_index` (`status`),
  KEY `book_loans_due_date_index` (`due_date`),
  KEY `book_loans_lent_date_index` (`lent_date`),
  KEY `book_loans_returned_at_index` (`returned_at`),
  KEY `book_loans_fine_paid_fine_amount_index` (`fine_paid`,`fine_amount`),
  KEY `book_loans_status_due_date_index` (`status`,`due_date`),
  KEY `book_loans_student_id_status_index` (`student_id`,`status`),
  KEY `idx_book_loans_academic_year` (`academic_year_id`),
  KEY `idx_book_loans_year_status` (`academic_year_id`,`status`),
  KEY `idx_book_loans_year_lent_date` (`academic_year_id`,`lent_date`),
  CONSTRAINT `book_loans_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `book_loans_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  CONSTRAINT `book_loans_lent_by_foreign` FOREIGN KEY (`lent_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `book_loans_returned_to_foreign` FOREIGN KEY (`returned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `book_loans_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_loans`
--

LOCK TABLES `book_loans` WRITE;
/*!40000 ALTER TABLE `book_loans` DISABLE KEYS */;
/*!40000 ALTER TABLE `book_loans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `books` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `isbn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publisher` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publication_year` year DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `total_copies` int NOT NULL DEFAULT '1',
  `available_copies` int NOT NULL DEFAULT '1',
  `shelf_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'English',
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `books_isbn_unique` (`isbn`),
  KEY `books_is_active_index` (`is_active`),
  KEY `books_available_copies_index` (`available_copies`),
  KEY `books_category_index` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bus_fare_structures`
--

DROP TABLE IF EXISTS `bus_fare_structures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bus_fare_structures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `route_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_plan` enum('monthly','per_term') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'per_term',
  `monthly_amount` decimal(10,2) DEFAULT NULL,
  `term_amount` decimal(10,2) DEFAULT NULL,
  `academic_year_id` bigint unsigned DEFAULT NULL,
  `term_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bus_fare_structures_route_name_index` (`route_name`),
  KEY `bus_fare_structures_payment_plan_index` (`payment_plan`),
  KEY `bus_fare_structures_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bus_fare_structures`
--

LOCK TABLES `bus_fare_structures` WRITE;
/*!40000 ALTER TABLE `bus_fare_structures` DISABLE KEYS */;
INSERT INTO `bus_fare_structures` VALUES (1,'Kakoso var Town Roots ','monthly',500.00,NULL,NULL,NULL,1,NULL,'2025-10-16 13:42:39','2025-10-16 13:42:39');
/*!40000 ALTER TABLE `bus_fare_structures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bus_payments`
--

DROP TABLE IF EXISTS `bus_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bus_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `bus_fare_structure_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned DEFAULT NULL,
  `term_id` bigint unsigned DEFAULT NULL,
  `month` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('unpaid','partial','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `due_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bus_payments_student_id_index` (`student_id`),
  KEY `bus_payments_bus_fare_structure_id_index` (`bus_fare_structure_id`),
  KEY `bus_payments_payment_status_index` (`payment_status`),
  KEY `bus_payments_student_id_year_month_index` (`student_id`,`year`,`month`),
  CONSTRAINT `bus_payments_bus_fare_structure_id_foreign` FOREIGN KEY (`bus_fare_structure_id`) REFERENCES `bus_fare_structures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bus_payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bus_payments`
--

LOCK TABLES `bus_payments` WRITE;
/*!40000 ALTER TABLE `bus_payments` DISABLE KEYS */;
INSERT INTO `bus_payments` VALUES (1,1,1,NULL,NULL,'October',2025,500.00,500.00,0.00,'paid','2025-11-16',NULL,'2025-10-16 13:45:35','2025-10-16 13:45:35');
/*!40000 ALTER TABLE `bus_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('356a192b7913b04c54574d18c28d46e6395428ab','i:1;',1770661090),('356a192b7913b04c54574d18c28d46e6395428ab:timer','i:1770661090;',1770661090),('livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3','i:1;',1770697647),('livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer','i:1770697647;',1770697647),('school_settings','O:25:\"App\\Models\\SchoolSettings\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:15:\"school_settings\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:1;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:11:\"school_name\";s:11:\"School Name\";s:13:\"currency_code\";s:3:\"ZMW\";s:8:\"timezone\";s:13:\"Africa/Lusaka\";s:11:\"school_days\";s:11:\"[1,2,3,4,5]\";s:15:\"payment_methods\";s:39:\"[\"cash\",\"bank_transfer\",\"mobile_money\"]\";s:10:\"updated_at\";s:19:\"2026-01-22 10:16:44\";s:10:\"created_at\";s:19:\"2026-01-22 10:16:44\";s:2:\"id\";i:1;}s:11:\"\0*\0original\";a:8:{s:11:\"school_name\";s:11:\"School Name\";s:13:\"currency_code\";s:3:\"ZMW\";s:8:\"timezone\";s:13:\"Africa/Lusaka\";s:11:\"school_days\";s:11:\"[1,2,3,4,5]\";s:15:\"payment_methods\";s:39:\"[\"cash\",\"bank_transfer\",\"mobile_money\"]\";s:10:\"updated_at\";s:19:\"2026-01-22 10:16:44\";s:10:\"created_at\";s:19:\"2026-01-22 10:16:44\";s:2:\"id\";i:1;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:62:{s:18:\"social_media_links\";s:5:\"array\";s:11:\"school_days\";s:5:\"array\";s:15:\"payment_methods\";s:5:\"array\";s:12:\"bank_details\";s:5:\"array\";s:20:\"mobile_money_details\";s:5:\"array\";s:15:\"custom_settings\";s:5:\"array\";s:22:\"show_position_in_class\";s:7:\"boolean\";s:22:\"show_position_in_grade\";s:7:\"boolean\";s:18:\"show_grade_average\";s:7:\"boolean\";s:28:\"enable_continuous_assessment\";s:7:\"boolean\";s:24:\"notify_parent_on_absence\";s:7:\"boolean\";s:21:\"notify_parent_on_late\";s:7:\"boolean\";s:22:\"enable_online_payments\";s:7:\"boolean\";s:23:\"enable_partial_payments\";s:7:\"boolean\";s:16:\"enable_late_fees\";s:7:\"boolean\";s:24:\"enable_sms_notifications\";s:7:\"boolean\";s:26:\"enable_email_notifications\";s:7:\"boolean\";s:29:\"enable_whatsapp_notifications\";s:7:\"boolean\";s:18:\"sms_on_fee_payment\";s:7:\"boolean\";s:21:\"sms_on_result_release\";s:7:\"boolean\";s:17:\"sms_on_attendance\";s:7:\"boolean\";s:15:\"sms_on_homework\";s:7:\"boolean\";s:21:\"show_teacher_comments\";s:7:\"boolean\";s:25:\"show_headteacher_comments\";s:7:\"boolean\";s:24:\"show_principal_signature\";s:7:\"boolean\";s:28:\"show_class_teacher_signature\";s:7:\"boolean\";s:26:\"show_parent_signature_line\";s:7:\"boolean\";s:23:\"show_attendance_summary\";s:7:\"boolean\";s:18:\"show_conduct_grade\";s:7:\"boolean\";s:23:\"enable_maintenance_mode\";s:7:\"boolean\";s:21:\"enable_student_portal\";s:7:\"boolean\";s:20:\"enable_parent_portal\";s:7:\"boolean\";s:21:\"enable_teacher_portal\";s:7:\"boolean\";s:38:\"require_password_change_on_first_login\";s:7:\"boolean\";s:18:\"enable_auto_backup\";s:7:\"boolean\";s:17:\"school_start_time\";s:12:\"datetime:H:i\";s:15:\"school_end_time\";s:12:\"datetime:H:i\";s:11:\"backup_time\";s:12:\"datetime:H:i\";s:16:\"next_term_starts\";s:4:\"date\";s:14:\"next_term_ends\";s:4:\"date\";s:24:\"settings_last_updated_at\";s:8:\"datetime\";s:14:\"terms_per_year\";s:7:\"integer\";s:12:\"passing_mark\";s:7:\"integer\";s:8:\"max_mark\";s:7:\"integer\";s:20:\"ca_weight_percentage\";s:7:\"integer\";s:22:\"exam_weight_percentage\";s:7:\"integer\";s:20:\"late_arrival_minutes\";s:7:\"integer\";s:30:\"absence_notification_threshold\";s:7:\"integer\";s:17:\"grace_period_days\";s:7:\"integer\";s:27:\"sms_balance_alert_threshold\";s:7:\"integer\";s:23:\"session_timeout_minutes\";s:7:\"integer\";s:20:\"password_expiry_days\";s:7:\"integer\";s:18:\"max_login_attempts\";s:7:\"integer\";s:24:\"lockout_duration_minutes\";s:7:\"integer\";s:21:\"backup_retention_days\";s:7:\"integer\";s:23:\"minimum_partial_payment\";s:9:\"decimal:2\";s:19:\"late_fee_percentage\";s:9:\"decimal:2\";s:11:\"grade_a_min\";s:7:\"integer\";s:11:\"grade_b_min\";s:7:\"integer\";s:11:\"grade_c_min\";s:7:\"integer\";s:11:\"grade_d_min\";s:7:\"integer\";s:11:\"grade_e_min\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:118:{i:0;s:11:\"school_name\";i:1;s:11:\"school_code\";i:2;s:19:\"registration_number\";i:3;s:7:\"tax_pin\";i:4;s:12:\"school_motto\";i:5;s:13:\"school_vision\";i:6;s:14:\"school_mission\";i:7;s:11:\"school_logo\";i:8;s:7:\"favicon\";i:9;s:11:\"header_logo\";i:10;s:11:\"footer_logo\";i:11;s:16:\"report_card_logo\";i:12;s:13:\"primary_color\";i:13;s:15:\"secondary_color\";i:14;s:12:\"accent_color\";i:15;s:7:\"address\";i:16;s:4:\"city\";i:17;s:14:\"state_province\";i:18;s:7:\"country\";i:19;s:11:\"postal_code\";i:20;s:5:\"phone\";i:21;s:15:\"alternate_phone\";i:22;s:5:\"email\";i:23;s:7:\"website\";i:24;s:18:\"social_media_links\";i:25;s:20:\"academic_year_format\";i:26;s:14:\"terms_per_year\";i:27;s:14:\"grading_system\";i:28;s:12:\"passing_mark\";i:29;s:8:\"max_mark\";i:30;s:22:\"show_position_in_class\";i:31;s:22:\"show_position_in_grade\";i:32;s:18:\"show_grade_average\";i:33;s:28:\"enable_continuous_assessment\";i:34;s:20:\"ca_weight_percentage\";i:35;s:22:\"exam_weight_percentage\";i:36;s:11:\"grade_a_min\";i:37;s:11:\"grade_b_min\";i:38;s:11:\"grade_c_min\";i:39;s:11:\"grade_d_min\";i:40;s:11:\"grade_e_min\";i:41;s:14:\"grade_a_remark\";i:42;s:14:\"grade_b_remark\";i:43;s:14:\"grade_c_remark\";i:44;s:14:\"grade_d_remark\";i:45;s:14:\"grade_e_remark\";i:46;s:17:\"school_start_time\";i:47;s:15:\"school_end_time\";i:48;s:20:\"late_arrival_minutes\";i:49;s:24:\"notify_parent_on_absence\";i:50;s:21:\"notify_parent_on_late\";i:51;s:30:\"absence_notification_threshold\";i:52;s:11:\"school_days\";i:53;s:22:\"enable_online_payments\";i:54;s:23:\"enable_partial_payments\";i:55;s:23:\"minimum_partial_payment\";i:56;s:16:\"enable_late_fees\";i:57;s:19:\"late_fee_percentage\";i:58;s:17:\"grace_period_days\";i:59;s:14:\"invoice_prefix\";i:60;s:14:\"receipt_prefix\";i:61;s:20:\"payment_instructions\";i:62;s:15:\"payment_methods\";i:63;s:12:\"bank_details\";i:64;s:20:\"mobile_money_details\";i:65;s:13:\"sms_sender_id\";i:66;s:24:\"enable_sms_notifications\";i:67;s:26:\"enable_email_notifications\";i:68;s:29:\"enable_whatsapp_notifications\";i:69;s:18:\"sms_on_fee_payment\";i:70;s:21:\"sms_on_result_release\";i:71;s:17:\"sms_on_attendance\";i:72;s:15:\"sms_on_homework\";i:73;s:27:\"sms_balance_alert_threshold\";i:74;s:18:\"report_card_format\";i:75;s:21:\"show_teacher_comments\";i:76;s:25:\"show_headteacher_comments\";i:77;s:24:\"show_principal_signature\";i:78;s:28:\"show_class_teacher_signature\";i:79;s:26:\"show_parent_signature_line\";i:80;s:23:\"show_attendance_summary\";i:81;s:18:\"show_conduct_grade\";i:82;s:14:\"principal_name\";i:83;s:15:\"principal_title\";i:84;s:19:\"principal_signature\";i:85;s:23:\"report_card_footer_text\";i:86;s:16:\"next_term_starts\";i:87;s:14:\"next_term_ends\";i:88;s:13:\"currency_code\";i:89;s:8:\"timezone\";i:90;s:11:\"date_format\";i:91;s:11:\"time_format\";i:92;s:15:\"datetime_format\";i:93;s:23:\"session_timeout_minutes\";i:94;s:23:\"enable_maintenance_mode\";i:95;s:19:\"maintenance_message\";i:96;s:21:\"enable_student_portal\";i:97;s:20:\"enable_parent_portal\";i:98;s:21:\"enable_teacher_portal\";i:99;s:38:\"require_password_change_on_first_login\";i:100;s:20:\"password_expiry_days\";i:101;s:18:\"max_login_attempts\";i:102;s:24:\"lockout_duration_minutes\";i:103;s:18:\"enable_auto_backup\";i:104;s:16:\"backup_frequency\";i:105;s:11:\"backup_time\";i:106;s:21:\"backup_retention_days\";i:107;s:16:\"school_head_name\";i:108;s:17:\"school_head_title\";i:109;s:17:\"primary_head_name\";i:110;s:18:\"primary_head_title\";i:111;s:22:\"primary_head_signature\";i:112;s:19:\"secondary_head_name\";i:113;s:20:\"secondary_head_title\";i:114;s:24:\"secondary_head_signature\";i:115;s:15:\"custom_settings\";i:116;s:24:\"settings_last_updated_at\";i:117;s:19:\"settings_updated_by\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}',1769080604);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_sections`
--

DROP TABLE IF EXISTS `class_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grade_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` int NOT NULL DEFAULT '40',
  `class_teacher_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_class_sections_grade_active` (`grade_id`,`is_active`),
  KEY `idx_class_sections_year` (`academic_year_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_sections`
--

LOCK TABLES `class_sections` WRITE;
/*!40000 ALTER TABLE `class_sections` DISABLE KEYS */;
INSERT INTO `class_sections` VALUES (1,1,2,'A','Baby Class Section A',NULL,25,14,1,'2025-10-15 03:45:17','2025-10-17 02:27:58'),(2,2,2,'A','Middle Class Section A',NULL,25,15,1,'2025-10-15 03:45:17','2025-10-17 02:30:01'),(3,3,2,'A','Reception Section A',NULL,25,16,1,'2025-10-15 03:45:17','2025-10-17 02:34:53'),(4,4,2,'A','Grade 1 Section A',NULL,35,17,1,'2025-10-15 03:45:17','2025-10-17 02:36:26'),(6,5,2,'A','Grade 2 Section A',NULL,35,18,1,'2025-10-15 03:45:17','2025-10-17 02:39:57'),(8,6,2,'A','Grade 3 Section A',NULL,35,19,1,'2025-10-15 03:45:17','2025-10-17 02:41:56'),(10,7,2,'A','Grade 4 Section A',NULL,35,20,1,'2025-10-15 03:45:17','2025-10-17 02:44:10'),(11,7,2,'B','Grade 4 Section B',NULL,35,38,1,'2025-10-15 03:45:17','2025-10-17 07:04:16'),(12,8,2,'A','Grade 5 Section A',NULL,40,21,1,'2025-10-15 03:45:17','2025-10-17 02:45:41'),(14,9,2,'A','Grade 6 Section A',NULL,40,22,1,'2025-10-15 03:45:17','2025-10-17 02:47:45'),(16,10,2,'A','Grade 7 Section A',NULL,40,23,1,'2025-10-15 03:45:17','2025-10-17 02:50:06'),(18,11,2,'Form 1 Ss','Grade 8 Section A',NULL,35,NULL,1,'2025-10-15 03:45:17','2025-10-16 06:01:07'),(20,12,2,'A','Grade 9 Section A',NULL,45,NULL,1,'2025-10-15 03:45:17','2025-10-15 18:48:15'),(22,13,1,'A','Grade 10 Section A',NULL,45,NULL,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(24,14,1,'A','Grade 11 Section A',NULL,45,NULL,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(26,15,2,'Grade 12',NULL,NULL,35,NULL,1,'2025-10-17 02:55:11','2025-10-17 02:55:11');
/*!40000 ALTER TABLE `class_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_subject_teacher`
--

DROP TABLE IF EXISTS `class_subject_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_subject_teacher` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `class_subject_teacher_class_id_subject_id_teacher_id_unique` (`class_id`,`subject_id`,`teacher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_subject_teacher`
--

LOCK TABLES `class_subject_teacher` WRITE;
/*!40000 ALTER TABLE `class_subject_teacher` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_subject_teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_teacher`
--

DROP TABLE IF EXISTS `class_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_teacher` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned DEFAULT NULL,
  `teacher_id` bigint unsigned DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'class_teacher',
  `is_primary` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_teacher`
--

LOCK TABLES `class_teacher` WRITE;
/*!40000 ALTER TABLE `class_teacher` DISABLE KEYS */;
INSERT INTO `class_teacher` VALUES (1,1,1,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(2,2,2,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(3,3,3,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(4,4,4,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(5,5,1,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(6,6,2,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(7,7,3,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(8,8,4,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(9,9,1,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(10,10,2,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(11,11,3,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(12,12,4,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(13,13,1,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(14,14,2,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(15,15,3,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(16,16,4,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(17,17,1,'class_teacher',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(18,1,8,'assistant_teacher',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(19,2,6,'assistant_teacher',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(20,3,5,'assistant_teacher',0,'2025-10-15 03:45:19','2025-10-15 03:45:19');
/*!40000 ALTER TABLE `class_teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `section` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES (1,'Baby Class','ECL','Baby Class',NULL,'A',1,'active','2025-10-15 03:45:17','2025-10-17 02:18:11'),(2,'Middle Class A','ECL','Middle Class',NULL,'A',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(3,'Reception A','ECL','Reception',NULL,'A',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(4,'Grade 1 A','Primary','Grade 1',NULL,'A',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(5,'Grade 1 B','Primary','Grade 1',NULL,'B',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(6,'Grade 2 A','Primary','Grade 2',NULL,'A',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(7,'Grade 2 B','Primary','Grade 2',NULL,'B',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(8,'Grade 3 A','Primary','Grade 3',NULL,'A',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(9,'Grade 3 B','Primary','Grade 3',NULL,'B',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(10,'Grade 4 A','Primary','Grade 4',NULL,'A',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(11,'Grade 4 B','Primary','Grade 4',NULL,'B',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(12,'Grade 5 A','Primary','Grade 5',NULL,'A',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(13,'Grade 5 B','Primary','Grade 5',NULL,'B',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(14,'Grade 6 A','Primary','Grade 6',NULL,'A',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(15,'Grade 6 B','Primary','Grade 6',NULL,'B',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(16,'Grade 7 A','Primary','Grade 7',NULL,'A',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(17,'Grade 7 B','Primary','Grade 7',NULL,'B',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(18,'Grade 8 A','Secondary','Grade 8',NULL,'A',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(19,'Grade 8 B','Secondary','Grade 8',NULL,'B',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(20,'Grade 9 A','Secondary','Grade 9',NULL,'A',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(21,'Grade 9 B','Secondary','Grade 9',NULL,'B',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(22,'Grade 10 A','Secondary','Grade 10',NULL,'A',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(23,'Grade 10 B','Secondary','Grade 10',NULL,'B',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(24,'Grade 11 A','Secondary','Grade 11',NULL,'A',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17'),(25,'Grade 11 B','Secondary','Grade 11',NULL,'B',1,'active','2025-10-15 03:45:17','2025-10-15 03:45:17');
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_subject`
--

DROP TABLE IF EXISTS `employee_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_subject` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_subject`
--

LOCK TABLES `employee_subject` WRITE;
/*!40000 ALTER TABLE `employee_subject` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `basic_salary` decimal(10,2) DEFAULT NULL,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_email_unique` (`email`),
  UNIQUE KEY `employees_employee_number_unique` (`employee_number`),
  UNIQUE KEY `employees_employee_id_unique` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'System Administrator','admin@stfrancisofassisi.tech','EMP001','+260971234567',1,'Administration','System Administrator','2025-10-15','active',NULL,'EMP001',NULL,1,'2025-10-15 03:45:07','2025-10-15 03:45:16'),(2,'Chungu','chungu@stfrancisofassisi.tech','TEA001','+260978006764',2,'Primary',NULL,NULL,'active',NULL,NULL,NULL,2,'2025-10-15 03:45:08','2025-10-15 03:45:08'),(3,'Zunda','zunda@stfrancisofassisi.tech','TEA002','+260976982552',2,'Secondary',NULL,NULL,'active',NULL,NULL,NULL,3,'2025-10-15 03:45:08','2025-10-15 03:45:08'),(4,'Constance','constance@stfrancisofassisi.tech','TEA003','+260976552012',2,'ECL',NULL,NULL,'active',NULL,NULL,NULL,4,'2025-10-15 03:45:08','2025-10-15 03:45:08'),(5,'Musa Doris','musa.doris@stfrancisofassisi.tech','TEA004','+260975645652',2,'Secondary',NULL,NULL,'active',NULL,NULL,NULL,5,'2025-10-15 03:45:08','2025-10-15 03:45:08'),(6,'Musakanya Mutale','musakanya.mutale@stfrancisofassisi.tech','TEA005','+260977353718',2,'Secondary',NULL,NULL,'active',NULL,NULL,NULL,6,'2025-10-15 03:45:08','2025-10-15 03:45:08'),(7,'Eunice Kansa','eunice.kansa@stfrancisofassisi.tech','TEA006','+260975502777',2,'Secondary',NULL,NULL,'active',NULL,NULL,NULL,7,'2025-10-15 03:45:09','2025-10-15 03:45:09'),(8,'Euelle Sinyangwe','euelle.sinyangwe@stfrancisofassisi.tech','TEA007','+260973271709',2,'ECL',NULL,NULL,'active',NULL,NULL,NULL,8,'2025-10-15 03:45:09','2025-10-15 03:45:09'),(9,'Mukupa Agness','mukupa.agness@stfrancisofassisi.tech','TEA008','+260978587119',2,'Primary',NULL,NULL,'active',NULL,NULL,NULL,9,'2025-10-15 03:45:09','2025-10-15 03:45:09'),(10,'Mubisa Martin','mubisa.martin@stfrancisofassisi.tech','TEA009','+260979318499',2,'Secondary',NULL,NULL,'active',NULL,NULL,NULL,10,'2025-10-15 03:45:09','2025-10-15 03:45:09'),(11,'Kopakopa Leonard','kopakopa.leonard@stfrancisofassisi.tech','TEA010','+260974998915',2,'ECL',NULL,NULL,'active',NULL,NULL,NULL,11,'2025-10-15 03:45:09','2025-10-15 03:45:09'),(12,'Kaposhi','kaposhi@stfrancisofassisi.tech','TEA011','+260971492688',2,'Secondary',NULL,NULL,'active',NULL,NULL,NULL,12,'2025-10-15 03:45:10','2025-10-15 03:45:10'),(13,'Muonda Bwalya','muonda.bwalya@stfrancisofassisi.tech','TEA012','+260977271647',2,'Secondary',NULL,NULL,'active',NULL,NULL,NULL,13,'2025-10-15 03:45:10','2025-10-15 03:45:10'),(14,'Chibwe Quintino','chibwe.quintino@stfrancisofassisi.tech','TEA013','+260973416178',2,'ECL',NULL,NULL,'active',NULL,NULL,NULL,14,'2025-10-15 03:45:10','2025-10-15 03:45:10'),(15,'Mwaba Breven','mwaba.breven@stfrancisofassisi.tech','TEA014','+260975922802',2,'ECL',NULL,NULL,'active',NULL,NULL,NULL,15,'2025-10-15 03:45:10','2025-10-15 03:45:10'),(16,'Sintomba Freddy','sintomba.freddy@stfrancisofassisi.tech','TEA015','+260976148498',2,'Primary',NULL,NULL,'active',NULL,NULL,NULL,16,'2025-10-15 03:45:10','2025-10-15 03:45:10'),(17,'Mulenga Vincent','mulenga.vincent@stfrancisofassisi.tech','TEA016','+260974709149',2,'Secondary',NULL,NULL,'active',NULL,NULL,NULL,17,'2025-10-15 03:45:11','2025-10-15 03:45:11'),(18,'Bwalya Sylvester','bwalya.sylvester@stfrancisofassisi.tech','TEA017','+260974335361',2,'ECL',NULL,NULL,'active',NULL,NULL,NULL,18,'2025-10-15 03:45:11','2025-10-15 03:45:11'),(19,'Singongo Bruce','singongo.bruce@stfrancisofassisi.tech','TEA018','+260979416886',2,'Secondary',NULL,NULL,'active',NULL,NULL,NULL,19,'2025-10-15 03:45:11','2025-10-15 03:45:11'),(20,'Mercy Kapelenga','mercy.kapelenga@stfrancisofassisi.tech','TEA019','+260975634436',2,'Primary',NULL,NULL,'active',NULL,NULL,NULL,20,'2025-10-15 03:45:11','2025-10-15 03:45:11'),(21,'Sylvester Lupando','sylvester.lupando@stfrancisofassisi.tech','TEA020','+260972865891',2,'Secondary',NULL,NULL,'active',NULL,NULL,NULL,21,'2025-10-15 03:45:11','2025-10-15 03:45:11'),(22,'Tiza Nkhomo','tiza.nkhomo@stfrancisofassisi.tech','TEA021','+260977728071',2,'ECL',NULL,NULL,'active',NULL,NULL,NULL,22,'2025-10-15 03:45:12','2025-10-15 03:45:12'),(23,'Mary Banda','mary.banda@stfrancis.tech',NULL,'0975111001',2,'Teaching','Teacher','2024-12-15','active',14527.00,'T001',NULL,24,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(24,'John Mwale','john.mwale@stfrancis.tech',NULL,'0975111002',2,'Teaching','Teacher','2025-03-15','active',12177.00,'T002',NULL,25,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(25,'Grace Phiri','grace.phiri@stfrancis.tech',NULL,'0975111003',2,'Teaching','Teacher','2025-07-15','active',13002.00,'T003',NULL,26,'2025-10-15 03:45:18','2025-10-15 03:45:18'),(26,'Peter Zulu','peter.zulu@stfrancis.tech',NULL,'0975111004',2,'Teaching','Teacher','2025-07-15','active',8355.00,'T004',NULL,27,'2025-10-15 03:45:18','2025-10-15 03:45:18'),(27,'Dr. Sarah Tembo','sarah.tembo@stfrancis.tech',NULL,'0975111005',2,'Teaching','Teacher','2024-11-15','active',8522.00,'T005',NULL,28,'2025-10-15 03:45:18','2025-10-15 03:45:18'),(28,'Prof. Michael Chanda','michael.chanda@stfrancis.tech',NULL,'0975111006',2,'Teaching','Teacher','2025-03-15','active',11717.00,'T006',NULL,29,'2025-10-15 03:45:18','2025-10-15 03:45:18'),(29,'Ms. Janet Kasonde','janet.kasonde@stfrancis.tech',NULL,'0975111007',2,'Teaching','Teacher','2024-10-15','active',12496.00,'T007',NULL,30,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(30,'Mr. Robert Simwanza','robert.simwanza@stfrancis.tech',NULL,'0975111008',2,'Teaching','Teacher','2024-11-15','active',9122.00,'T008',NULL,31,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(31,'Blessmore Mutale','mulengablessmore@gmail.com',NULL,'260975020473',2,'ecl','Grade Teacher','2025-05-01','active',5000.00,'454543242','employee-photos/01K7MGJMGNRQY7PSTQFRAWAATE.jpg',32,'2025-10-15 18:02:06','2025-10-15 18:02:06'),(32,'Kabamba Handson','kabambahandson@gmail.com',NULL,'0965102620',15,'secondary','Mathematics Teacher ','2024-01-09','active',2000.00,'ST.F/S-005/25',NULL,39,'2025-10-16 09:16:28','2025-10-16 09:16:28'),(33,'Ludinda Godwin','ludindagodwin@gmail.com',NULL,'0967308940',2,'secondary','ICT Teacher','2024-01-08','active',2000.00,'ST.F/S-027/25',NULL,40,'2025-10-16 09:26:30','2025-10-16 09:26:30'),(34,'Kapelanga L Mercy','kapelangamercy@gmail.com',NULL,'0968562371',12,'primary','Primary Teacher ','2023-01-09','active',2000.00,'ST.F/P-006/25',NULL,41,'2025-10-16 09:38:19','2025-10-16 09:38:19');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `applicable_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('upcoming','ongoing','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'upcoming',
  `organizer_id` bigint unsigned DEFAULT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `notify_parents` tinyint(1) DEFAULT '0',
  `target_grades` json DEFAULT NULL,
  `sms_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `events_slug_unique` (`slug`),
  KEY `events_status_start_date_index` (`status`,`start_date`),
  KEY `events_category_index` (`category`),
  KEY `idx_events_academic_year` (`academic_year_id`),
  KEY `idx_events_year_start_date` (`academic_year_id`,`start_date`),
  KEY `idx_events_year_status` (`academic_year_id`,`status`),
  CONSTRAINT `events_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'Test','<p>Testing the Graduation event</p>',NULL,'test','2025-10-17 05:56:46','2025-10-17 17:00:54','School hall','event-images/01K7NJQ74XDWE55CN5V2YACTKZ.jpg','academic','upcoming',8,2,1,'[]',NULL,'2025-10-16 03:58:47','2025-10-16 03:58:47'),(2,'Rerum molestias accusamus autem cupidatat aute quo dolores et','<p>Cumque quaerat quia .</p>',NULL,'rerum-molestias-accusamus-autem-cupidatat-aute-quo-dolores-et','2019-06-16 05:17:00','1971-09-21 05:22:00','Quaerat Nam ut in voluptas quae minima vel eaque possimus harum reprehenderit dolor ut numquam sit atque ea','event-images/01K7NJSNCGS55KKYVX5JJS8A5J.jpg','sports','completed',14,2,1,'[]',NULL,'2025-10-16 04:00:08','2025-10-16 04:00:08'),(3,'Parent-Teacher Conference 2025','<p>Dear Parents and Guardians,</p><p>We invite you to attend our Parent-Teacher Conference where you can discuss your child\'s academic progress, behavior, and overall development with their teachers.</p><p><strong>Schedule:</strong></p><ul><li>9:00 AM - 11:00 AM: Grades 1-6</li><li>11:30 AM - 1:00 PM: Grades 7-9</li><li>2:00 PM - 3:00 PM: Forms 1-4</li></ul><p>Please bring your child\'s report book for reference.</p>',NULL,'parent-teacher-conference-2025','2025-11-15 09:00:00','2025-11-15 15:00:00','School Main Hall',NULL,'academic','upcoming',1,2,0,NULL,NULL,'2025-10-17 06:44:56','2025-10-17 06:44:56'),(4,'Inter-House Sports Day','<p>Join us for our annual Inter-House Sports Day! Students will compete in various track and field events representing their respective houses.</p><p><strong>Events include:</strong></p><ul><li>100m, 200m, and 400m races</li><li>Long jump and high jump</li><li>Relay races</li><li>Tug of war</li><li>Netball and football matches</li></ul><p>Parents are welcome to attend and cheer for their children. Refreshments will be available.</p><p><em>Students should wear their house colors and bring water bottles.</em></p>',NULL,'inter-house-sports-day','2025-11-25 08:00:00','2025-11-25 16:00:00','School Sports Ground',NULL,'sports','upcoming',1,2,0,NULL,NULL,'2025-10-17 06:44:56','2025-10-17 06:44:56'),(5,'Christmas Carol Service','<p>We cordially invite all students, parents, and staff to our annual Christmas Carol Service.</p><p>Join us as we celebrate the birth of Jesus Christ through:</p><ul><li>Traditional Christmas carols and hymns</li><li>Scripture readings by students</li><li>Nativity play performed by Grade 3 students</li><li>Choir performances</li><li>Message from the Chaplain</li></ul><p>This is a wonderful opportunity to come together as a school community and reflect on the true meaning of Christmas.</p><p><strong>Dress Code:</strong> Smart casual or traditional attire</p>',NULL,'christmas-carol-service','2025-12-18 17:00:00','2025-12-18 19:00:00','St Francis Chapel',NULL,'religious','upcoming',1,2,0,NULL,NULL,'2025-10-17 06:44:56','2025-10-17 06:44:56'),(6,'Science Exhibition 2025','<p>Students from Grades 5-12 will showcase their innovative science projects at our annual Science Exhibition.</p><p><strong>Exhibition Categories:</strong></p><ul><li>Physics and Engineering</li><li>Chemistry and Materials Science</li><li>Biology and Environmental Science</li><li>Computer Science and Technology</li></ul><p>Winners will represent St Francis of Assisi at the District Science Fair in December.</p><p><strong>Judging Criteria:</strong></p><ul><li>Scientific method and research</li><li>Innovation and creativity</li><li>Presentation and communication</li></ul><p>Parents and guardians are invited to view the projects from 1:00 PM - 2:00 PM.</p>',NULL,'science-exhibition-2025','2025-11-08 10:00:00','2025-11-08 14:00:00','School Science Laboratory',NULL,'academic','upcoming',1,2,0,NULL,NULL,'2025-10-17 06:44:56','2025-10-17 06:44:56'),(7,'Cultural Day Celebration','<p>Celebrate the rich diversity of Zambian culture at our Cultural Day!</p><p><strong>Activities include:</strong></p><ul><li>Traditional dance performances from all 73 tribes</li><li>Cultural fashion show</li><li>Traditional food tasting</li><li>Art and craft exhibitions</li><li>Storytelling sessions with local elders</li><li>Traditional games and activities</li></ul><p>Students are encouraged to wear traditional attire representing their cultural heritage.</p><p><strong>Special Guests:</strong> Local cultural leaders and traditional dancers</p><p>Parents are welcome to participate and share cultural artifacts or performances.</p>',NULL,'cultural-day-celebration','2025-12-05 09:00:00','2025-12-05 15:00:00','School Assembly Ground',NULL,'cultural','upcoming',1,2,0,NULL,NULL,'2025-10-17 06:44:56','2025-10-17 06:44:56'),(8,'End of Term 3 Prize Giving Ceremony','<p>Join us for our End of Term Prize Giving Ceremony where we celebrate academic excellence and outstanding achievement.</p><p><strong>Awards to be presented:</strong></p><ul><li>Top 3 students in each grade</li><li>Subject excellence awards</li><li>Most improved student awards</li><li>Perfect attendance awards</li><li>Sports and extracurricular achievements</li><li>Character and leadership awards</li></ul><p><strong>Programme:</strong></p><ul><li>10:00 AM - Welcome and opening prayer</li><li>10:15 AM - Headteacher\'s report</li><li>10:45 AM - Guest speaker</li><li>11:15 AM - Prize presentations</li><li>12:30 PM - Closing remarks</li></ul><p>Refreshments will be served after the ceremony.</p>',NULL,'end-of-term-3-prize-giving-ceremony','2025-12-20 10:00:00','2025-12-20 13:00:00','School Main Hall',NULL,'academic','upcoming',1,2,0,NULL,NULL,'2025-10-17 06:44:56','2025-10-17 06:44:56');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exports`
--

DROP TABLE IF EXISTS `exports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `completed_at` timestamp NULL DEFAULT NULL,
  `file_disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exporter` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed_rows` int unsigned NOT NULL DEFAULT '0',
  `total_rows` int unsigned NOT NULL,
  `successful_rows` int unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exports_user_id_foreign` (`user_id`),
  CONSTRAINT `exports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exports`
--

LOCK TABLES `exports` WRITE;
/*!40000 ALTER TABLE `exports` DISABLE KEYS */;
INSERT INTO `exports` VALUES (1,'2026-02-10 04:27:42','local','export-1-teachers','App\\Filament\\Exports\\TeacherExporter',21,21,21,1,'2026-02-10 04:27:42','2026-02-10 04:27:42');
/*!40000 ALTER TABLE `exports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_import_rows`
--

DROP TABLE IF EXISTS `failed_import_rows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_import_rows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `data` json NOT NULL,
  `import_id` bigint unsigned NOT NULL,
  `validation_error` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `failed_import_rows_import_id_foreign` (`import_id`),
  CONSTRAINT `failed_import_rows_import_id_foreign` FOREIGN KEY (`import_id`) REFERENCES `imports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_import_rows`
--

LOCK TABLES `failed_import_rows` WRITE;
/*!40000 ALTER TABLE `failed_import_rows` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_import_rows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
INSERT INTO `failed_jobs` VALUES (1,'ab9e7bc6-013a-4770-9ad8-9df80cab1e3f','database','default','{\"uuid\":\"ab9e7bc6-013a-4770-9ad8-9df80cab1e3f\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:2:{i:0;a:21:{s:4:\\\"name\\\";s:12:\\\"download_csv\\\";s:5:\\\"color\\\";N;s:5:\\\"event\\\";N;s:9:\\\"eventData\\\";a:0:{}s:17:\\\"dispatchDirection\\\";b:0;s:19:\\\"dispatchToComponent\\\";N;s:15:\\\"extraAttributes\\\";a:0:{}s:4:\\\"icon\\\";N;s:12:\\\"iconPosition\\\";E:42:\\\"Filament\\\\Support\\\\Enums\\\\IconPosition:Before\\\";s:8:\\\"iconSize\\\";N;s:10:\\\"isOutlined\\\";b:0;s:10:\\\"isDisabled\\\";b:0;s:5:\\\"label\\\";s:13:\\\"Download .csv\\\";s:11:\\\"shouldClose\\\";b:0;s:16:\\\"shouldMarkAsRead\\\";b:1;s:18:\\\"shouldMarkAsUnread\\\";b:0;s:21:\\\"shouldOpenUrlInNewTab\\\";b:1;s:4:\\\"size\\\";E:39:\\\"Filament\\\\Support\\\\Enums\\\\ActionSize:Small\\\";s:7:\\\"tooltip\\\";N;s:3:\\\"url\\\";s:39:\\\"\\/filament\\/exports\\/1\\/download?format=csv\\\";s:4:\\\"view\\\";s:29:\\\"filament-actions::link-action\\\";}i:1;a:21:{s:4:\\\"name\\\";s:13:\\\"download_xlsx\\\";s:5:\\\"color\\\";N;s:5:\\\"event\\\";N;s:9:\\\"eventData\\\";a:0:{}s:17:\\\"dispatchDirection\\\";b:0;s:19:\\\"dispatchToComponent\\\";N;s:15:\\\"extraAttributes\\\";a:0:{}s:4:\\\"icon\\\";N;s:12:\\\"iconPosition\\\";r:21;s:8:\\\"iconSize\\\";N;s:10:\\\"isOutlined\\\";b:0;s:10:\\\"isDisabled\\\";b:0;s:5:\\\"label\\\";s:14:\\\"Download .xlsx\\\";s:11:\\\"shouldClose\\\";b:0;s:16:\\\"shouldMarkAsRead\\\";b:1;s:18:\\\"shouldMarkAsUnread\\\";b:0;s:21:\\\"shouldOpenUrlInNewTab\\\";b:1;s:4:\\\"size\\\";r:30;s:7:\\\"tooltip\\\";N;s:3:\\\"url\\\";s:40:\\\"\\/filament\\/exports\\/1\\/download?format=xlsx\\\";s:4:\\\"view\\\";s:29:\\\"filament-actions::link-action\\\";}}s:4:\\\"body\\\";s:55:\\\"Your teacher export has completed and 21 rows exported.\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";s:23:\\\"heroicon-o-check-circle\\\";s:9:\\\"iconColor\\\";s:7:\\\"success\\\";s:6:\\\"status\\\";s:7:\\\"success\\\";s:5:\\\"title\\\";s:16:\\\"Export completed\\\";s:4:\\\"view\\\";s:36:\\\"filament-notifications::notification\\\";s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"b060d1ae-5d11-413d-9365-c515e0067f9c\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}','PDOException: SQLSTATE[42S02]: Base table or view not found: 1146 Table \'u970673179_assisi.notifications\' doesn\'t exist in /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/MySqlConnection.php:47\nStack trace:\n#0 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/MySqlConnection.php(47): PDO->prepare()\n#1 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Connection.php(810): Illuminate\\Database\\MySqlConnection->Illuminate\\Database\\{closure}()\n#2 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Connection.php(777): Illuminate\\Database\\Connection->runQueryCallback()\n#3 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/MySqlConnection.php(42): Illuminate\\Database\\Connection->run()\n#4 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3718): Illuminate\\Database\\MySqlConnection->insert()\n#5 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(2141): Illuminate\\Database\\Query\\Builder->insert()\n#6 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1336): Illuminate\\Database\\Eloquent\\Builder->__call()\n#7 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1164): Illuminate\\Database\\Eloquent\\Model->performInsert()\n#8 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Relations/HasOneOrMany.php(371): Illuminate\\Database\\Eloquent\\Model->save()\n#9 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Support/helpers.php(399): Illuminate\\Database\\Eloquent\\Relations\\HasOneOrMany->Illuminate\\Database\\Eloquent\\Relations\\{closure}()\n#10 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Relations/HasOneOrMany.php(368): tap()\n#11 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Notifications/Channels/DatabaseChannel.php(19): Illuminate\\Database\\Eloquent\\Relations\\HasOneOrMany->create()\n#12 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Notifications/NotificationSender.php(148): Illuminate\\Notifications\\Channels\\DatabaseChannel->send()\n#13 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Notifications/NotificationSender.php(106): Illuminate\\Notifications\\NotificationSender->sendToNotifiable()\n#14 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Support/Traits/Localizable.php(19): Illuminate\\Notifications\\NotificationSender->Illuminate\\Notifications\\{closure}()\n#15 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Notifications/NotificationSender.php(101): Illuminate\\Notifications\\NotificationSender->withLocale()\n#16 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Notifications/ChannelManager.php(54): Illuminate\\Notifications\\NotificationSender->sendNow()\n#17 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Notifications/SendQueuedNotifications.php(119): Illuminate\\Notifications\\ChannelManager->sendNow()\n#18 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Notifications\\SendQueuedNotifications->handle()\n#19 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#20 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#21 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#22 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/Container.php(754): Illuminate\\Container\\BoundMethod::call()\n#23 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(126): Illuminate\\Container\\Container->call()\n#24 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(170): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#25 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(127): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#26 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(130): Illuminate\\Pipeline\\Pipeline->then()\n#27 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(126): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#28 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(170): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#29 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(127): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#30 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(121): Illuminate\\Pipeline\\Pipeline->then()\n#31 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(69): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#32 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#33 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(442): Illuminate\\Queue\\Jobs\\Job->fire()\n#34 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(392): Illuminate\\Queue\\Worker->process()\n#35 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(178): Illuminate\\Queue\\Worker->runJob()\n#36 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(149): Illuminate\\Queue\\Worker->daemon()\n#37 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(132): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#38 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#39 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#40 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#41 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#42 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/Container.php(754): Illuminate\\Container\\BoundMethod::call()\n#43 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Console/Command.php(213): Illuminate\\Container\\Container->call()\n#44 /var/www/stfrancisofassisi.tech/projects/portal/vendor/symfony/console/Command/Command.php(279): Illuminate\\Console\\Command->execute()\n#45 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Console/Command.php(182): Symfony\\Component\\Console\\Command\\Command->run()\n#46 /var/www/stfrancisofassisi.tech/projects/portal/vendor/symfony/console/Application.php(1094): Illuminate\\Console\\Command->run()\n#47 /var/www/stfrancisofassisi.tech/projects/portal/vendor/symfony/console/Application.php(342): Symfony\\Component\\Console\\Application->doRunCommand()\n#48 /var/www/stfrancisofassisi.tech/projects/portal/vendor/symfony/console/Application.php(193): Symfony\\Component\\Console\\Application->doRun()\n#49 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(198): Symfony\\Component\\Console\\Application->run()\n#50 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#51 /var/www/stfrancisofassisi.tech/projects/portal/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#52 {main}\n\nNext Illuminate\\Database\\QueryException: SQLSTATE[42S02]: Base table or view not found: 1146 Table \'u970673179_assisi.notifications\' doesn\'t exist (Connection: mysql, SQL: insert into `notifications` (`id`, `type`, `data`, `read_at`, `notifiable_id`, `notifiable_type`, `updated_at`, `created_at`) values (b060d1ae-5d11-413d-9365-c515e0067f9c, Filament\\Notifications\\DatabaseNotification, {\"actions\":[{\"name\":\"download_csv\",\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Download .csv\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":true,\"size\":\"sm\",\"tooltip\":null,\"url\":\"\\/filament\\/exports\\/1\\/download?format=csv\",\"view\":\"filament-actions::link-action\"},{\"name\":\"download_xlsx\",\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Download .xlsx\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":true,\"size\":\"sm\",\"tooltip\":null,\"url\":\"\\/filament\\/exports\\/1\\/download?format=xlsx\",\"view\":\"filament-actions::link-action\"}],\"body\":\"Your teacher export has completed and 21 rows exported.\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-check-circle\",\"iconColor\":\"success\",\"status\":\"success\",\"title\":\"Export completed\",\"view\":\"filament-notifications::notification\",\"viewData\":[],\"format\":\"filament\"}, ?, 1, App\\Models\\User, 2026-02-10 04:27:42, 2026-02-10 04:27:42)) in /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Connection.php:823\nStack trace:\n#0 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Connection.php(777): Illuminate\\Database\\Connection->runQueryCallback()\n#1 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/MySqlConnection.php(42): Illuminate\\Database\\Connection->run()\n#2 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3718): Illuminate\\Database\\MySqlConnection->insert()\n#3 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(2141): Illuminate\\Database\\Query\\Builder->insert()\n#4 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1336): Illuminate\\Database\\Eloquent\\Builder->__call()\n#5 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1164): Illuminate\\Database\\Eloquent\\Model->performInsert()\n#6 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Relations/HasOneOrMany.php(371): Illuminate\\Database\\Eloquent\\Model->save()\n#7 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Support/helpers.php(399): Illuminate\\Database\\Eloquent\\Relations\\HasOneOrMany->Illuminate\\Database\\Eloquent\\Relations\\{closure}()\n#8 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Relations/HasOneOrMany.php(368): tap()\n#9 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Notifications/Channels/DatabaseChannel.php(19): Illuminate\\Database\\Eloquent\\Relations\\HasOneOrMany->create()\n#10 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Notifications/NotificationSender.php(148): Illuminate\\Notifications\\Channels\\DatabaseChannel->send()\n#11 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Notifications/NotificationSender.php(106): Illuminate\\Notifications\\NotificationSender->sendToNotifiable()\n#12 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Support/Traits/Localizable.php(19): Illuminate\\Notifications\\NotificationSender->Illuminate\\Notifications\\{closure}()\n#13 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Notifications/NotificationSender.php(101): Illuminate\\Notifications\\NotificationSender->withLocale()\n#14 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Notifications/ChannelManager.php(54): Illuminate\\Notifications\\NotificationSender->sendNow()\n#15 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Notifications/SendQueuedNotifications.php(119): Illuminate\\Notifications\\ChannelManager->sendNow()\n#16 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Notifications\\SendQueuedNotifications->handle()\n#17 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#18 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#19 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#20 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/Container.php(754): Illuminate\\Container\\BoundMethod::call()\n#21 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(126): Illuminate\\Container\\Container->call()\n#22 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(170): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#23 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(127): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#24 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(130): Illuminate\\Pipeline\\Pipeline->then()\n#25 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(126): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#26 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(170): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#27 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(127): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#28 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(121): Illuminate\\Pipeline\\Pipeline->then()\n#29 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(69): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#30 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#31 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(442): Illuminate\\Queue\\Jobs\\Job->fire()\n#32 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(392): Illuminate\\Queue\\Worker->process()\n#33 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(178): Illuminate\\Queue\\Worker->runJob()\n#34 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(149): Illuminate\\Queue\\Worker->daemon()\n#35 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(132): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#36 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#37 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#38 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#39 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#40 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Container/Container.php(754): Illuminate\\Container\\BoundMethod::call()\n#41 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Console/Command.php(213): Illuminate\\Container\\Container->call()\n#42 /var/www/stfrancisofassisi.tech/projects/portal/vendor/symfony/console/Command/Command.php(279): Illuminate\\Console\\Command->execute()\n#43 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Console/Command.php(182): Symfony\\Component\\Console\\Command\\Command->run()\n#44 /var/www/stfrancisofassisi.tech/projects/portal/vendor/symfony/console/Application.php(1094): Illuminate\\Console\\Command->run()\n#45 /var/www/stfrancisofassisi.tech/projects/portal/vendor/symfony/console/Application.php(342): Symfony\\Component\\Console\\Application->doRunCommand()\n#46 /var/www/stfrancisofassisi.tech/projects/portal/vendor/symfony/console/Application.php(193): Symfony\\Component\\Console\\Application->doRun()\n#47 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(198): Symfony\\Component\\Console\\Application->run()\n#48 /var/www/stfrancisofassisi.tech/projects/portal/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#49 /var/www/stfrancisofassisi.tech/projects/portal/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#50 {main}','2026-02-10 04:27:42');
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_structures`
--

DROP TABLE IF EXISTS `fee_structures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_structures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grade_id` bigint unsigned NOT NULL,
  `term_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `basic_fee` decimal(10,2) NOT NULL,
  `additional_charges` json DEFAULT NULL,
  `total_fee` decimal(10,2) NOT NULL,
  `late_fee_amount` decimal(10,2) DEFAULT NULL,
  `late_fee_percentage` decimal(5,2) DEFAULT NULL,
  `payment_deadline` date DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `name` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fee_structures_grade_id_term_id_academic_year_id_unique` (`grade_id`,`term_id`,`academic_year_id`),
  KEY `idx_fee_structures_lookup` (`grade_id`,`academic_year_id`,`term_id`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_structures`
--

LOCK TABLES `fee_structures` WRITE;
/*!40000 ALTER TABLE `fee_structures` DISABLE KEYS */;
INSERT INTO `fee_structures` VALUES (1,1,1,1,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Baby Class during Term 1 (2024)','Baby Class - Term 1 (2024)',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(2,2,1,1,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Middle Class during Term 1 (2024)','Middle Class - Term 1 (2024)',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(3,3,1,1,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Reception during Term 1 (2024)','Reception - Term 1 (2024)',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(4,4,1,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 1 during Term 1 (2024)','Grade 1 - Term 1 (2024)',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(5,5,1,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 2 during Term 1 (2024)','Grade 2 - Term 1 (2024)',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(6,6,1,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 3 during Term 1 (2024)','Grade 3 - Term 1 (2024)',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(7,7,1,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 4 during Term 1 (2024)','Grade 4 - Term 1 (2024)',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(8,8,1,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 5 during Term 1 (2024)','Grade 5 - Term 1 (2024)',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(9,9,1,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 6 during Term 1 (2024)','Grade 6 - Term 1 (2024)',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(10,10,1,1,2450.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2450.00,NULL,NULL,NULL,'Fee structure for Grade 7 during Term 1 (2024)','Grade 7 - Term 1 (2024)',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(11,11,1,1,2800.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',2800.00,NULL,NULL,NULL,'Fee structure for Grade 8 during Term 1 (2024)','Grade 8 - Term 1 (2024)',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(12,12,1,1,2800.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',2800.00,NULL,NULL,NULL,'Fee structure for Grade 9 during Term 1 (2024)','Grade 9 - Term 1 (2024)',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(13,13,1,1,3150.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',3150.00,NULL,NULL,NULL,'Fee structure for Grade 10 during Term 1 (2024)','Grade 10 - Term 1 (2024)',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(14,14,1,1,3150.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',3150.00,NULL,NULL,NULL,'Fee structure for Grade 11 during Term 1 (2024)','Grade 11 - Term 1 (2024)',1,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(15,1,2,1,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Baby Class during Term 2 (2024)','Baby Class - Term 2 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(16,2,2,1,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Middle Class during Term 2 (2024)','Middle Class - Term 2 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(17,3,2,1,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Reception during Term 2 (2024)','Reception - Term 2 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(18,4,2,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 1 during Term 2 (2024)','Grade 1 - Term 2 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(19,5,2,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 2 during Term 2 (2024)','Grade 2 - Term 2 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(20,6,2,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 3 during Term 2 (2024)','Grade 3 - Term 2 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(21,7,2,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 4 during Term 2 (2024)','Grade 4 - Term 2 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(22,8,2,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 5 during Term 2 (2024)','Grade 5 - Term 2 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(23,9,2,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 6 during Term 2 (2024)','Grade 6 - Term 2 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(24,10,2,1,2450.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2450.00,NULL,NULL,NULL,'Fee structure for Grade 7 during Term 2 (2024)','Grade 7 - Term 2 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(25,11,2,1,2800.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',2800.00,NULL,NULL,NULL,'Fee structure for Grade 8 during Term 2 (2024)','Grade 8 - Term 2 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(26,12,2,1,2800.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',2800.00,NULL,NULL,NULL,'Fee structure for Grade 9 during Term 2 (2024)','Grade 9 - Term 2 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(27,13,2,1,3150.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',3150.00,NULL,NULL,NULL,'Fee structure for Grade 10 during Term 2 (2024)','Grade 10 - Term 2 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(28,14,2,1,3150.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',3150.00,NULL,NULL,NULL,'Fee structure for Grade 11 during Term 2 (2024)','Grade 11 - Term 2 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(29,1,3,1,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Baby Class during Term 3 (2024)','Baby Class - Term 3 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(30,2,3,1,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Middle Class during Term 3 (2024)','Middle Class - Term 3 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(31,3,3,1,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Reception during Term 3 (2024)','Reception - Term 3 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(32,4,3,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 1 during Term 3 (2024)','Grade 1 - Term 3 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(33,5,3,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 2 during Term 3 (2024)','Grade 2 - Term 3 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(34,6,3,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 3 during Term 3 (2024)','Grade 3 - Term 3 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(35,7,3,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 4 during Term 3 (2024)','Grade 4 - Term 3 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(36,8,3,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 5 during Term 3 (2024)','Grade 5 - Term 3 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(37,9,3,1,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 6 during Term 3 (2024)','Grade 6 - Term 3 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(38,10,3,1,2450.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2450.00,NULL,NULL,NULL,'Fee structure for Grade 7 during Term 3 (2024)','Grade 7 - Term 3 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(39,11,3,1,2800.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',2800.00,NULL,NULL,NULL,'Fee structure for Grade 8 during Term 3 (2024)','Grade 8 - Term 3 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(40,12,3,1,2800.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',2800.00,NULL,NULL,NULL,'Fee structure for Grade 9 during Term 3 (2024)','Grade 9 - Term 3 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(41,13,3,1,3150.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',3150.00,NULL,NULL,NULL,'Fee structure for Grade 10 during Term 3 (2024)','Grade 10 - Term 3 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(42,14,3,1,3150.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',3150.00,NULL,NULL,NULL,'Fee structure for Grade 11 during Term 3 (2024)','Grade 11 - Term 3 (2024)',0,'2025-10-15 03:45:19','2025-10-15 03:45:19'),(43,1,4,2,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Baby Class during Term 1 (2025)','Baby Class - Term 1 (2025)',1,'2025-10-16 06:27:41','2025-10-16 06:27:41'),(44,2,4,2,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Middle Class during Term 1 (2025)','Middle Class - Term 1 (2025)',1,'2025-10-16 06:27:41','2025-10-16 06:27:41'),(45,3,4,2,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Reception during Term 1 (2025)','Reception - Term 1 (2025)',1,'2025-10-16 06:27:41','2025-10-16 06:27:41'),(46,4,4,2,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 1 during Term 1 (2025)','Grade 1 - Term 1 (2025)',1,'2025-10-16 06:27:41','2025-10-16 06:27:41'),(47,5,4,2,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 2 during Term 1 (2025)','Grade 2 - Term 1 (2025)',1,'2025-10-16 06:27:41','2025-10-16 06:27:41'),(48,6,4,2,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 3 during Term 1 (2025)','Grade 3 - Term 1 (2025)',1,'2025-10-16 06:27:41','2025-10-16 06:27:41'),(49,7,4,2,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 4 during Term 1 (2025)','Grade 4 - Term 1 (2025)',1,'2025-10-16 06:27:41','2025-10-16 06:27:41'),(50,8,4,2,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 5 during Term 1 (2025)','Grade 5 - Term 1 (2025)',1,'2025-10-16 06:27:41','2025-10-16 06:27:41'),(51,9,4,2,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 6 during Term 1 (2025)','Grade 6 - Term 1 (2025)',1,'2025-10-16 06:27:41','2025-10-16 06:27:41'),(52,10,4,2,2450.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2450.00,NULL,NULL,NULL,'Fee structure for Grade 7 during Term 1 (2025)','Grade 7 - Term 1 (2025)',1,'2025-10-16 06:27:41','2025-10-16 06:27:41'),(53,11,4,2,2800.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',2800.00,NULL,NULL,NULL,'Fee structure for Form 1 during Term 1 (2025)','Form 1 - Term 1 (2025)',1,'2025-10-16 06:27:41','2025-10-16 06:27:41'),(54,12,4,2,2800.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',2800.00,NULL,NULL,NULL,'Fee structure for Grade 9 during Term 1 (2025)','Grade 9 - Term 1 (2025)',1,'2025-10-16 06:27:41','2025-10-16 06:27:41'),(55,13,4,2,3150.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',3150.00,NULL,NULL,NULL,'Fee structure for Grade 10 during Term 1 (2025)','Grade 10 - Term 1 (2025)',1,'2025-10-16 06:27:41','2025-10-16 06:27:41'),(56,14,4,2,3150.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',3150.00,NULL,NULL,NULL,'Fee structure for Grade 11 during Term 1 (2025)','Grade 11 - Term 1 (2025)',1,'2025-10-16 06:27:41','2025-10-16 06:27:41'),(57,1,6,2,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Baby Class during Term 3 (2025)','Baby Class - Term 3 (2025)',1,'2025-10-16 06:47:06','2025-10-16 06:47:06'),(58,2,6,2,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Middle Class during Term 3 (2025)','Middle Class - Term 3 (2025)',1,'2025-10-16 06:47:06','2025-10-16 06:47:06'),(59,3,6,2,1750.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',1750.00,NULL,NULL,NULL,'Fee structure for Reception during Term 3 (2025)','Reception - Term 3 (2025)',1,'2025-10-16 06:47:06','2025-10-16 06:47:06'),(60,4,6,2,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 1 during Term 3 (2025)','Grade 1 - Term 3 (2025)',1,'2025-10-16 06:47:06','2025-10-16 06:47:06'),(61,5,6,2,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 2 during Term 3 (2025)','Grade 2 - Term 3 (2025)',1,'2025-10-16 06:47:06','2025-10-16 06:47:06'),(62,6,6,2,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 3 during Term 3 (2025)','Grade 3 - Term 3 (2025)',1,'2025-10-16 06:47:06','2025-10-16 06:47:06'),(63,7,6,2,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 4 during Term 3 (2025)','Grade 4 - Term 3 (2025)',1,'2025-10-16 06:47:06','2025-10-16 06:47:06'),(64,8,6,2,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 5 during Term 3 (2025)','Grade 5 - Term 3 (2025)',1,'2025-10-16 06:47:06','2025-10-16 06:47:06'),(65,9,6,2,2100.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2100.00,NULL,NULL,NULL,'Fee structure for Grade 6 during Term 3 (2025)','Grade 6 - Term 3 (2025)',1,'2025-10-16 06:47:06','2025-10-16 06:47:06'),(66,10,6,2,2450.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 0}',2450.00,NULL,NULL,NULL,'Fee structure for Grade 7 during Term 3 (2025)','Grade 7 - Term 3 (2025)',1,'2025-10-16 06:47:06','2025-10-16 06:47:06'),(67,11,6,2,2800.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',2800.00,NULL,NULL,NULL,'Fee structure for Form 1 during Term 3 (2025)','Form 1 - Term 3 (2025)',1,'2025-10-16 06:47:06','2025-10-16 06:47:06'),(68,12,6,2,2800.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',2800.00,NULL,NULL,NULL,'Fee structure for Grade 9 during Term 3 (2025)','Grade 9 - Term 3 (2025)',1,'2025-10-16 06:47:06','2025-10-16 06:47:06'),(69,13,6,2,3150.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',3150.00,NULL,NULL,NULL,'Fee structure for Grade 10 during Term 3 (2025)','Grade 10 - Term 3 (2025)',1,'2025-10-16 06:47:06','2025-10-16 06:47:06'),(70,14,6,2,3150.00,'{\"Sports Fee\": 150, \"Library Fee\": 100, \"Computer Fee\": 200, \"Laboratory Fee\": 300}',3150.00,NULL,NULL,NULL,'Fee structure for Grade 11 during Term 3 (2025)','Grade 11 - Term 3 (2025)',1,'2025-10-16 06:47:06','2025-10-16 06:47:06');
/*!40000 ALTER TABLE `fee_structures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery_images`
--

DROP TABLE IF EXISTS `gallery_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gallery_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery_images`
--

LOCK TABLES `gallery_images` WRITE;
/*!40000 ALTER TABLE `gallery_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `gallery_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `get_in_touches`
--

DROP TABLE IF EXISTS `get_in_touches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `get_in_touches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `is_read` tinyint(1) DEFAULT '0',
  `inquiry_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('new','read','responded') COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `response` text COLLATE utf8mb4_unicode_ci,
  `responded_by` bigint unsigned DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `get_in_touches`
--

LOCK TABLES `get_in_touches` WRITE;
/*!40000 ALTER TABLE `get_in_touches` DISABLE KEYS */;
/*!40000 ALTER TABLE `get_in_touches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grade_subject`
--

DROP TABLE IF EXISTS `grade_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grade_subject` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grade_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grade_subject_grade_id_subject_id_unique` (`grade_id`,`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=210 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grade_subject`
--

LOCK TABLES `grade_subject` WRITE;
/*!40000 ALTER TABLE `grade_subject` DISABLE KEYS */;
INSERT INTO `grade_subject` VALUES (1,1,32,1,'2025-10-15 03:45:17','2025-10-16 04:36:14'),(5,1,5,1,'2025-10-15 03:45:17','2025-10-16 04:41:30'),(11,2,33,1,'2025-10-15 03:45:17','2025-10-16 04:46:54'),(12,2,32,1,'2025-10-15 03:45:17','2025-10-16 04:47:24'),(21,3,33,1,'2025-10-15 03:45:17','2025-10-16 04:50:06'),(22,3,32,1,'2025-10-15 03:45:17','2025-10-16 04:50:32'),(25,3,5,1,'2025-10-15 03:45:17','2025-10-16 04:51:44'),(32,4,34,1,'2025-10-15 03:45:17','2025-10-16 05:40:43'),(33,4,11,1,'2025-10-15 03:45:17','2025-10-16 05:41:34'),(35,4,5,1,'2025-10-15 03:45:17','2025-10-16 05:46:52'),(36,4,6,1,'2025-10-15 03:45:17','2025-10-16 05:47:23'),(41,5,1,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(42,5,2,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(43,5,3,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(44,5,4,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(45,5,5,1,'2025-10-15 03:45:17','2025-10-16 05:50:59'),(46,5,6,1,'2025-10-15 03:45:17','2025-10-16 05:51:54'),(51,6,1,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(52,6,2,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(53,6,3,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(54,6,4,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(55,6,5,1,'2025-10-15 03:45:17','2025-10-16 05:54:44'),(56,6,6,1,'2025-10-15 03:45:17','2025-10-16 05:55:43'),(61,7,1,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(62,7,2,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(63,7,3,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(64,7,4,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(65,7,5,1,'2025-10-15 03:45:17','2025-10-16 05:57:41'),(66,7,6,1,'2025-10-15 03:45:17','2025-10-16 05:58:26'),(71,8,1,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(72,8,2,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(74,8,4,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(75,8,5,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(76,8,6,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(81,9,1,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(82,9,2,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(84,9,4,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(85,9,5,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(86,9,6,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(91,10,1,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(92,10,2,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(94,10,4,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(95,10,5,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(96,10,6,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(101,11,11,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(102,11,12,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(103,11,13,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(104,11,14,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(108,11,18,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(109,11,19,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(110,11,20,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(111,11,21,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(112,11,22,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(113,11,23,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(114,11,24,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(115,11,25,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(116,11,26,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(117,11,27,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(118,11,28,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(119,11,29,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(120,11,30,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(121,11,31,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(122,12,11,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(123,12,12,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(124,12,13,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(125,12,14,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(126,12,15,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(127,12,16,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(128,12,17,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(129,12,18,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(130,12,19,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(131,12,20,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(132,12,21,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(133,12,22,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(134,12,23,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(135,12,24,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(136,12,25,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(137,12,26,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(138,12,27,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(139,12,28,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(140,12,29,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(141,12,30,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(142,12,31,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(143,13,11,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(144,13,12,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(145,13,13,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(146,13,14,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(147,13,15,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(148,13,16,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(149,13,17,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(150,13,18,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(151,13,19,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(152,13,20,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(153,13,21,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(154,13,22,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(155,13,23,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(156,13,24,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(157,13,25,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(158,13,26,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(159,13,27,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(160,13,28,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(161,13,29,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(162,13,30,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(163,13,31,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(164,14,11,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(165,14,12,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(166,14,13,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(167,14,14,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(168,14,15,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(169,14,16,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(170,14,17,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(171,14,18,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(172,14,19,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(173,14,20,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(174,14,21,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(175,14,22,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(176,14,23,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(177,14,24,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(178,14,25,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(179,14,26,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(180,14,27,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(181,14,28,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(182,14,29,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(183,14,30,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(184,14,31,0,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(185,1,33,1,'2025-10-16 04:42:34','2025-10-16 04:42:34'),(187,2,5,1,'2025-10-16 04:48:11','2025-10-16 04:48:11'),(189,15,11,1,'2025-10-17 02:07:45','2025-10-17 02:12:15'),(190,15,12,1,'2025-10-17 02:07:45','2025-10-17 02:12:15'),(191,15,13,0,'2025-10-17 02:07:45','2025-10-17 02:07:45'),(192,15,14,0,'2025-10-17 02:07:45','2025-10-17 02:07:45'),(193,15,15,1,'2025-10-17 02:07:45','2025-10-17 02:12:15'),(194,15,16,1,'2025-10-17 02:07:45','2025-10-17 02:12:15'),(195,15,17,1,'2025-10-17 02:07:45','2025-10-17 02:12:15'),(196,15,18,1,'2025-10-17 02:07:45','2025-10-17 02:12:15'),(197,15,19,1,'2025-10-17 02:07:45','2025-10-17 02:12:15'),(198,15,20,0,'2025-10-17 02:07:45','2025-10-17 02:07:45'),(199,15,21,0,'2025-10-17 02:07:45','2025-10-17 02:07:45'),(200,15,22,0,'2025-10-17 02:07:45','2025-10-17 02:07:45'),(201,15,23,0,'2025-10-17 02:07:45','2025-10-17 02:07:45'),(202,15,24,0,'2025-10-17 02:07:45','2025-10-17 02:07:45'),(203,15,25,0,'2025-10-17 02:07:45','2025-10-17 02:07:45'),(204,15,26,0,'2025-10-17 02:07:45','2025-10-17 02:07:45'),(205,15,27,0,'2025-10-17 02:07:45','2025-10-17 02:07:45'),(206,15,28,0,'2025-10-17 02:07:45','2025-10-17 02:07:45'),(207,15,29,0,'2025-10-17 02:07:45','2025-10-17 02:07:45'),(208,15,30,0,'2025-10-17 02:07:45','2025-10-17 02:07:45'),(209,15,31,0,'2025-10-17 02:07:45','2025-10-17 02:07:45');
/*!40000 ALTER TABLE `grade_subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grades`
--

DROP TABLE IF EXISTS `grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_section_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `capacity` int NOT NULL DEFAULT '40',
  `breakeven_number` int NOT NULL DEFAULT '30',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grades_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grades`
--

LOCK TABLES `grades` WRITE;
/*!40000 ALTER TABLE `grades` DISABLE KEYS */;
INSERT INTO `grades` VALUES (1,1,'Baby Class','BC',1,'ECE - Baby Class',35,25,1,'2025-10-15 03:45:16','2025-10-16 04:10:25'),(2,1,'Middle Class','MC',2,'ECE - Middle Class',35,25,1,'2025-10-15 03:45:16','2025-10-16 04:11:27'),(3,1,'Reception','RC',3,'ECL - Reception',35,25,1,'2025-10-15 03:45:16','2025-10-16 04:12:32'),(4,2,'Grade 1','G1',4,'Primary - Grade 1',35,25,1,'2025-10-15 03:45:16','2025-10-16 04:13:26'),(5,2,'Grade 2','G2',5,'Primary - Grade 2',35,25,1,'2025-10-15 03:45:16','2025-10-16 04:14:05'),(6,2,'Grade 3','G3',6,'Primary - Grade 3',35,25,1,'2025-10-15 03:45:16','2025-10-16 04:14:49'),(7,2,'Grade 4','G4',7,'Primary - Grade 4',35,25,1,'2025-10-15 03:45:16','2025-10-16 04:15:31'),(8,3,'Grade 5','G5',8,'Primary - Grade 5',35,25,1,'2025-10-15 03:45:16','2025-10-16 04:16:12'),(9,3,'Grade 6','G6',9,'Primary - Grade 6',35,25,1,'2025-10-15 03:45:16','2025-10-16 04:17:03'),(10,3,'Grade 7','G7',10,'Primary - Grade 7',35,25,1,'2025-10-15 03:45:16','2025-10-16 04:17:52'),(11,4,'Form 1','F1',11,'Secondary - Grade 8',35,25,1,'2025-10-15 03:45:16','2025-10-16 04:18:54'),(12,4,'Grade 9','G9',12,'Secondary - Grade 9',35,25,1,'2025-10-15 03:45:16','2025-10-16 04:19:28'),(13,5,'Grade 10','G10',13,'Secondary - Grade 10',35,25,1,'2025-10-15 03:45:16','2025-10-16 04:20:32'),(14,5,'Grade 11','G11',14,'Secondary - Grade 11',35,25,1,'2025-10-15 03:45:16','2025-10-16 04:21:23'),(15,5,'Grade 12','G12',15,'Senior Secondary - Grade 12',35,8,1,'2025-10-16 04:22:44','2025-10-16 04:22:44');
/*!40000 ALTER TABLE `grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grading_scale_items`
--

DROP TABLE IF EXISTS `grading_scale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grading_scale_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grading_scale_id` bigint unsigned NOT NULL,
  `grade` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_marks` decimal(5,2) NOT NULL,
  `max_marks` decimal(5,2) NOT NULL,
  `grade_points` decimal(3,1) NOT NULL DEFAULT '0.0',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grading_scale_items_grading_scale_id_min_marks_max_marks_index` (`grading_scale_id`,`min_marks`,`max_marks`),
  CONSTRAINT `grading_scale_items_grading_scale_id_foreign` FOREIGN KEY (`grading_scale_id`) REFERENCES `grading_scales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grading_scale_items`
--

LOCK TABLES `grading_scale_items` WRITE;
/*!40000 ALTER TABLE `grading_scale_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `grading_scale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grading_scales`
--

DROP TABLE IF EXISTS `grading_scales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grading_scales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade_level` enum('primary','secondary','all') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grading_scales_grade_level_is_active_index` (`grade_level`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grading_scales`
--

LOCK TABLES `grading_scales` WRITE;
/*!40000 ALTER TABLE `grading_scales` DISABLE KEYS */;
/*!40000 ALTER TABLE `grading_scales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `homework`
--

DROP TABLE IF EXISTS `homework`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `homework` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `file_attachment` json DEFAULT NULL,
  `homework_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_by` bigint unsigned DEFAULT NULL,
  `grade_id` bigint unsigned DEFAULT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `submission_start` datetime DEFAULT NULL,
  `submission_end` datetime DEFAULT NULL,
  `allow_late_submission` tinyint(1) NOT NULL DEFAULT '0',
  `late_submission_deadline` datetime DEFAULT NULL,
  `max_score` int NOT NULL DEFAULT '100',
  `submission_instructions` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `notify_parents` tinyint(1) DEFAULT '1',
  `sms_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_homework_grade` (`grade_id`),
  KEY `idx_homework_subject` (`subject_id`),
  KEY `idx_homework_due_date` (`due_date`),
  KEY `idx_homework_academic_year` (`academic_year_id`),
  KEY `idx_homework_year_grade_subject` (`academic_year_id`,`grade_id`,`subject_id`),
  KEY `idx_homework_year_due_date` (`academic_year_id`,`due_date`),
  CONSTRAINT `homework_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homework`
--

LOCK TABLES `homework` WRITE;
/*!40000 ALTER TABLE `homework` DISABLE KEYS */;
INSERT INTO `homework` VALUES (1,'Test',NULL,NULL,'homework-files/01K7P1MXH5H7KA8JACKYF8G45X.docx',12,12,2,11,'2025-10-22',NULL,NULL,0,NULL,100,NULL,'active',1,NULL,'2025-10-16 08:19:41','2025-10-16 08:19:41'),(2,'HM','Answer all ',NULL,'homework-files/01K7PDS5N43KS6102SYK15XB16.jpg',9,5,2,1,'2025-10-27',NULL,NULL,0,NULL,100,NULL,'active',1,NULL,'2025-10-16 11:51:43','2025-10-16 11:51:43'),(3,'Week 1 homw work','Answer all questions',NULL,'homework-files/01K7R86EF7Y74R8CVEXNNVX59J.pdf',30,13,2,13,'2025-10-27',NULL,NULL,0,NULL,100,NULL,'active',1,NULL,'2025-10-17 04:52:35','2025-10-17 04:52:35');
/*!40000 ALTER TABLE `homework` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `homework_submissions`
--

DROP TABLE IF EXISTS `homework_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `homework_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `homework_id` bigint unsigned DEFAULT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `file_attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `marks` decimal(5,2) DEFAULT NULL,
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `status` enum('submitted','graded','returned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `is_late` tinyint(1) NOT NULL DEFAULT '0',
  `teacher_notes` text COLLATE utf8mb4_unicode_ci,
  `graded_by` bigint unsigned DEFAULT NULL,
  `graded_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_homework_submissions_lookup` (`homework_id`,`student_id`),
  KEY `idx_homework_submissions_status` (`status`),
  KEY `idx_homework_submissions_academic_year` (`academic_year_id`),
  CONSTRAINT `homework_submissions_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homework_submissions`
--

LOCK TABLES `homework_submissions` WRITE;
/*!40000 ALTER TABLE `homework_submissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `homework_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imports`
--

DROP TABLE IF EXISTS `imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `completed_at` timestamp NULL DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `importer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed_rows` int unsigned NOT NULL DEFAULT '0',
  `total_rows` int unsigned NOT NULL,
  `successful_rows` int unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `imports_user_id_foreign` (`user_id`),
  CONSTRAINT `imports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imports`
--

LOCK TABLES `imports` WRITE;
/*!40000 ALTER TABLE `imports` DISABLE KEYS */;
/*!40000 ALTER TABLE `imports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
INSERT INTO `job_batches` VALUES ('a10b4484-95f1-4b82-b7b8-c45ff60b6146','',2,0,0,'[]','a:2:{s:13:\"allowFailures\";b:1;s:7:\"finally\";a:1:{i:0;O:47:\"Laravel\\SerializableClosure\\SerializableClosure\":1:{s:12:\"serializable\";O:46:\"Laravel\\SerializableClosure\\Serializers\\Signed\":2:{s:12:\"serializable\";s:6308:\"O:46:\"Laravel\\SerializableClosure\\Serializers\\Native\":5:{s:3:\"use\";a:1:{s:4:\"next\";O:46:\"Filament\\Actions\\Exports\\Jobs\\ExportCompletion\":7:{s:11:\"\0*\0exporter\";O:36:\"App\\Filament\\Exports\\TeacherExporter\":3:{s:9:\"\0*\0export\";O:38:\"Filament\\Actions\\Exports\\Models\\Export\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";N;s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:1;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:7:\"user_id\";i:1;s:8:\"exporter\";s:36:\"App\\Filament\\Exports\\TeacherExporter\";s:10:\"total_rows\";i:21;s:9:\"file_disk\";s:5:\"local\";s:10:\"updated_at\";s:19:\"2026-02-10 04:27:42\";s:10:\"created_at\";s:19:\"2026-02-10 04:27:42\";s:2:\"id\";i:1;s:9:\"file_name\";s:17:\"export-1-teachers\";}s:11:\"\0*\0original\";a:8:{s:7:\"user_id\";i:1;s:8:\"exporter\";s:36:\"App\\Filament\\Exports\\TeacherExporter\";s:10:\"total_rows\";i:21;s:9:\"file_disk\";s:5:\"local\";s:10:\"updated_at\";s:19:\"2026-02-10 04:27:42\";s:10:\"created_at\";s:19:\"2026-02-10 04:27:42\";s:2:\"id\";i:1;s:9:\"file_name\";s:17:\"export-1-teachers\";}s:10:\"\0*\0changes\";a:1:{s:9:\"file_name\";s:17:\"export-1-teachers\";}s:8:\"\0*\0casts\";a:4:{s:12:\"completed_at\";s:9:\"timestamp\";s:14:\"processed_rows\";s:7:\"integer\";s:10:\"total_rows\";s:7:\"integer\";s:15:\"successful_rows\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:0:{}s:10:\"\0*\0guarded\";a:0:{}}s:12:\"\0*\0columnMap\";a:12:{s:4:\"name\";s:4:\"Name\";s:11:\"employee_id\";s:11:\"Employee ID\";s:5:\"phone\";s:5:\"Phone\";s:5:\"email\";s:5:\"Email\";s:3:\"nrc\";s:10:\"NRC Number\";s:13:\"qualification\";s:13:\"Qualification\";s:14:\"specialization\";s:14:\"Specialization\";s:9:\"join_date\";s:9:\"Join date\";s:7:\"address\";s:7:\"Address\";s:9:\"is_active\";s:9:\"Is active\";s:8:\"grade_id\";s:8:\"Grade ID\";s:16:\"class_section_id\";s:16:\"Class Section ID\";}s:10:\"\0*\0options\";a:0:{}}s:9:\"\0*\0export\";O:45:\"Illuminate\\Contracts\\Database\\ModelIdentifier\":5:{s:5:\"class\";s:38:\"Filament\\Actions\\Exports\\Models\\Export\";s:2:\"id\";i:1;s:9:\"relations\";a:0:{}s:10:\"connection\";s:5:\"mysql\";s:15:\"collectionClass\";N;}s:12:\"\0*\0columnMap\";a:12:{s:4:\"name\";s:4:\"Name\";s:11:\"employee_id\";s:11:\"Employee ID\";s:5:\"phone\";s:5:\"Phone\";s:5:\"email\";s:5:\"Email\";s:3:\"nrc\";s:10:\"NRC Number\";s:13:\"qualification\";s:13:\"Qualification\";s:14:\"specialization\";s:14:\"Specialization\";s:9:\"join_date\";s:9:\"Join date\";s:7:\"address\";s:7:\"Address\";s:9:\"is_active\";s:9:\"Is active\";s:8:\"grade_id\";s:8:\"Grade ID\";s:16:\"class_section_id\";s:16:\"Class Section ID\";}s:10:\"\0*\0formats\";a:2:{i:0;E:47:\"Filament\\Actions\\Exports\\Enums\\ExportFormat:Csv\";i:1;E:48:\"Filament\\Actions\\Exports\\Enums\\ExportFormat:Xlsx\";}s:10:\"\0*\0options\";a:0:{}s:7:\"chained\";a:1:{i:0;s:2805:\"O:44:\"Filament\\Actions\\Exports\\Jobs\\CreateXlsxFile\":4:{s:11:\"\0*\0exporter\";O:36:\"App\\Filament\\Exports\\TeacherExporter\":3:{s:9:\"\0*\0export\";O:38:\"Filament\\Actions\\Exports\\Models\\Export\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";N;s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:1;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:7:\"user_id\";i:1;s:8:\"exporter\";s:36:\"App\\Filament\\Exports\\TeacherExporter\";s:10:\"total_rows\";i:21;s:9:\"file_disk\";s:5:\"local\";s:10:\"updated_at\";s:19:\"2026-02-10 04:27:42\";s:10:\"created_at\";s:19:\"2026-02-10 04:27:42\";s:2:\"id\";i:1;s:9:\"file_name\";s:17:\"export-1-teachers\";}s:11:\"\0*\0original\";a:8:{s:7:\"user_id\";i:1;s:8:\"exporter\";s:36:\"App\\Filament\\Exports\\TeacherExporter\";s:10:\"total_rows\";i:21;s:9:\"file_disk\";s:5:\"local\";s:10:\"updated_at\";s:19:\"2026-02-10 04:27:42\";s:10:\"created_at\";s:19:\"2026-02-10 04:27:42\";s:2:\"id\";i:1;s:9:\"file_name\";s:17:\"export-1-teachers\";}s:10:\"\0*\0changes\";a:1:{s:9:\"file_name\";s:17:\"export-1-teachers\";}s:8:\"\0*\0casts\";a:4:{s:12:\"completed_at\";s:9:\"timestamp\";s:14:\"processed_rows\";s:7:\"integer\";s:10:\"total_rows\";s:7:\"integer\";s:15:\"successful_rows\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:0:{}s:10:\"\0*\0guarded\";a:0:{}}s:12:\"\0*\0columnMap\";a:12:{s:4:\"name\";s:4:\"Name\";s:11:\"employee_id\";s:11:\"Employee ID\";s:5:\"phone\";s:5:\"Phone\";s:5:\"email\";s:5:\"Email\";s:3:\"nrc\";s:10:\"NRC Number\";s:13:\"qualification\";s:13:\"Qualification\";s:14:\"specialization\";s:14:\"Specialization\";s:9:\"join_date\";s:9:\"Join date\";s:7:\"address\";s:7:\"Address\";s:9:\"is_active\";s:9:\"Is active\";s:8:\"grade_id\";s:8:\"Grade ID\";s:16:\"class_section_id\";s:16:\"Class Section ID\";}s:10:\"\0*\0options\";a:0:{}}s:9:\"\0*\0export\";O:45:\"Illuminate\\Contracts\\Database\\ModelIdentifier\":5:{s:5:\"class\";s:38:\"Filament\\Actions\\Exports\\Models\\Export\";s:2:\"id\";i:1;s:9:\"relations\";a:0:{}s:10:\"connection\";s:5:\"mysql\";s:15:\"collectionClass\";N;}s:12:\"\0*\0columnMap\";a:12:{s:4:\"name\";s:4:\"Name\";s:11:\"employee_id\";s:11:\"Employee ID\";s:5:\"phone\";s:5:\"Phone\";s:5:\"email\";s:5:\"Email\";s:3:\"nrc\";s:10:\"NRC Number\";s:13:\"qualification\";s:13:\"Qualification\";s:14:\"specialization\";s:14:\"Specialization\";s:9:\"join_date\";s:9:\"Join date\";s:7:\"address\";s:7:\"Address\";s:9:\"is_active\";s:9:\"Is active\";s:8:\"grade_id\";s:8:\"Grade ID\";s:16:\"class_section_id\";s:16:\"Class Section ID\";}s:10:\"\0*\0options\";a:0:{}}\";}s:19:\"chainCatchCallbacks\";a:0:{}}}s:8:\"function\";s:266:\"function (\\Illuminate\\Bus\\Batch $batch) use ($next) {\n                if (! $batch->cancelled()) {\n                    \\Illuminate\\Container\\Container::getInstance()->make(\\Illuminate\\Contracts\\Bus\\Dispatcher::class)->dispatch($next);\n                }\n            }\";s:5:\"scope\";s:27:\"Illuminate\\Bus\\ChainedBatch\";s:4:\"this\";N;s:4:\"self\";s:32:\"00000000000009900000000000000000\";}\";s:4:\"hash\";s:44:\"AfHYXwrD5gwSEJp6HVCO4EYguG+0+G1ktW2gvEbkHEw=\";}}}}',NULL,1770697662,1770697662);
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_broadcasts`
--

DROP TABLE IF EXISTS `message_broadcasts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_broadcasts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `filters` json DEFAULT NULL,
  `total_recipients` int unsigned NOT NULL DEFAULT '0',
  `sent_count` int unsigned NOT NULL DEFAULT '0',
  `failed_count` int unsigned NOT NULL DEFAULT '0',
  `total_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `message_broadcasts_created_by_foreign` (`created_by`),
  CONSTRAINT `message_broadcasts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_broadcasts`
--

LOCK TABLES `message_broadcasts` WRITE;
/*!40000 ALTER TABLE `message_broadcasts` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_broadcasts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_templates`
--

DROP TABLE IF EXISTS `message_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `message_templates_created_by_foreign` (`created_by`),
  CONSTRAINT `message_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_templates`
--

LOCK TABLES `message_templates` WRITE;
/*!40000 ALTER TABLE `message_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2023_11_07_144936_create_permissions_table',1),(5,'2024_12_07_221327_create_imports_table',1),(6,'2024_12_07_221328_create_exports_table',1),(7,'2024_12_07_221329_create_failed_import_rows_table',1),(8,'2025_02_02_004253_create_get_in_touches_table',1),(9,'2025_03_17_025038_create_students_table',1),(10,'2025_03_17_025049_create_parent_guardians_table',1),(11,'2025_03_17_025057_create_fee_structures_table',1),(12,'2025_03_17_025100_create_academic_years_table',1),(13,'2025_03_17_025101_create_terms_table',1),(14,'2025_03_17_025102_create_school_sections_table',1),(15,'2025_03_17_025103_create_grades_table',1),(16,'2025_03_17_025108_create_student_fees_table',1),(17,'2025_03_17_025120_create_employees_table',1),(18,'2025_03_17_025128_create_payrolls_table',1),(19,'2025_03_17_025149_create_subjects_table',1),(20,'2025_03_17_025158_create_homework_table',1),(21,'2025_03_17_025208_create_homework_submissions_table',1),(22,'2025_03_17_025221_create_results_table',1),(23,'2025_03_17_025229_create_news_table',1),(24,'2025_03_17_025238_create_events_table',1),(25,'2025_03_17_025250_create_gallery_images_table',1),(26,'2025_03_17_031702_create_employee_subject_table',1),(27,'2025_03_17_195026_create_sms_logs_table',1),(28,'2025_03_19_065249_create_albums_table',1),(29,'2025_03_19_065259_create_photos_table',1),(30,'2025_03_19_201413_create_roles_table',1),(31,'2025_03_19_212610_create_user_credentials_table',1),(32,'2025_04_10_204338_create_school_classes_table',1),(33,'2025_04_10_205141_create_class_teacher_table',1),(34,'2025_04_10_210123_create_class_subject_teacher_table',1),(35,'2025_04_10_213128_create_classes_table',1),(36,'2025_05_02_205802_create_school_settings_table',1),(37,'2025_05_02_221738_create_class_sections_table',1),(38,'2025_05_02_222538_create_grade_subjects_table',1),(39,'2025_05_11_052735_create_teachers_table',1),(40,'2025_05_11_053252_create_subject_teachings_table',1),(41,'2025_05_11_061421_create_user_activities_table',1),(42,'2025_05_11_185820_add_applicable_to_to_events_table',1),(43,'2025_05_17_210113_create_message_broadcasts_table',1),(44,'2025_05_17_210127_create_message_templates_table',1),(45,'2025_05_25_063012_create_payment_transactions_table',1),(46,'2025_10_05_103248_add_payment_fields_to_student_fees_table',1),(47,'2025_10_05_104019_create_qr_payments_table',1),(48,'2025_10_10_123049_add_performance_indexes_to_tables',1),(49,'2025_10_10_130601_create_attendances_table',1),(50,'2025_10_11_123629_create_books_table',1),(51,'2025_10_11_123728_create_book_loans_table',1),(52,'2025_10_11_133951_add_performance_indexes_to_tables',1),(53,'2025_10_12_055229_add_indexes_to_library_tables',1),(54,'2025_10_14_185723_create_bus_fare_structures_table',1),(55,'2025_10_14_185730_create_bus_payments_table',1),(56,'2025_10_15_211214_add_additional_fields_to_teachers_table',2),(57,'2025_10_16_061723_add_enrollment_term_id_to_students_table',3),(58,'2025_10_16_085944_add_new_roles_to_roles_table',4),(59,'2025_10_16_121601_add_credential_types_to_sms_logs_message_type',5),(60,'2025_03_17_025050_create_academic_years_table',1),(61,'2025_03_17_025051_create_terms_table',1),(62,'2025_03_17_025052_create_grades_table',1),(63,'2025_03_17_025053_create_class_sections_table',1),(64,'2025_12_10_222558_create_school_sections_table',1),(65,'2025_10_02_044440_add_performance_indexes',1),(66,'2025_10_05_061209_add_overpaid_status_to_student_fees_table',6),(67,'2025_10_05_061514_add_payment_deadline_and_late_fees_to_fee_tables',6),(68,'2025_10_05_061748_add_discount_and_scholarship_support_to_student_fees',6),(69,'2025_10_05_061912_create_audit_logs_table',6),(70,'2025_10_30_215351_add_administrative_roles_to_teachers_table',6),(71,'2025_10_30_231802_add_end_of_term_test_to_exam_type_enum',6),(72,'2025_11_01_040012_add_academic_year_to_students_table',6),(73,'2025_11_01_040018_add_academic_year_to_homework_table',6),(74,'2025_11_01_040019_add_academic_year_to_homework_submissions_table',6),(75,'2025_11_01_040019_add_academic_year_to_payment_transactions_table',6),(76,'2025_11_01_040019_add_academic_year_to_results_table',6),(77,'2025_11_01_040020_add_academic_year_to_book_loans_table',6),(78,'2025_11_01_040020_add_academic_year_to_events_table',6),(79,'2025_11_01_040020_add_academic_year_to_payrolls_table',7),(80,'2025_12_12_015440_add_document_fields_to_teachers_table',7),(81,'2025_12_12_051405_create_sms_credits_table',7),(82,'2025_12_12_120000_convert_sms_credits_to_integer',7),(83,'2025_12_12_130000_add_broadcast_to_sms_logs_message_type',7),(84,'2025_12_12_140000_create_grading_scales_table',7),(85,'2025_12_12_150000_create_report_card_comments_table',7),(86,'2025_12_14_000001_create_staff_designations_table',7),(87,'2025_12_14_000002_create_teacher_designations_table',7),(88,'2025_12_14_000003_add_head_teacher_roles',7),(89,'2025_12_14_000004_add_section_to_teachers_table',7),(90,'2025_12_14_100000_add_robust_settings_to_school_settings',7),(91,'2025_12_14_110000_add_grading_scale_to_school_settings',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('published','draft') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `author_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `news_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parent_guardians`
--

DROP TABLE IF EXISTS `parent_guardians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parent_guardians` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nrc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alternate_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `relationship` enum('father','mother','guardian','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `role_id` bigint unsigned NOT NULL DEFAULT '4',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parent_guardians`
--

LOCK TABLES `parent_guardians` WRITE;
/*!40000 ALTER TABLE `parent_guardians` DISABLE KEYS */;
INSERT INTO `parent_guardians` VALUES (1,'Ben Mwaba','mulengablessmore@gmail.com','260975020473','456534/12/1','Zambian',NULL,'father','Officer','chililaz',37,4,'2025-10-16 06:06:58','2025-10-17 05:11:49'),(2,'Lydia Bwalya Grace','lydia@gmail.com','0978654321',NULL,NULL,NULL,'mother',NULL,'Kamenza',113,4,'2025-10-16 06:23:13','2025-10-17 03:45:16'),(3,'Teddson Lung\'eenda',NULL,'0964443502','252589/63/1','Zambian',NULL,'father','Contructor ','370 Kamenza East',43,4,'2025-10-16 10:38:31','2025-10-16 10:38:31'),(4,'Patrick Mulenga','johnbanda1@parent.com','0977000001',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',114,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(5,'Ruth Chanda','gracephiri2@parent.com','0977000002',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',115,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(6,'Moses Zulu','sarahmwanza3@parent.com','0977000003',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',116,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(7,'Catherine Namukoko','johnbanda4@parent.com','0977000004',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',117,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(8,'Joseph Tembo','gracephiri5@parent.com','0977000005',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',118,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(9,'Mary Mutale','sarahmwanza6@parent.com','0977000006',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',119,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(10,'David Chongo','johnbanda7@parent.com','0977000007',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',120,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(11,'Esther Mwape','gracephiri8@parent.com','0977000008',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',121,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(12,'Peter Sakala','sarahmwanza9@parent.com','0977000009',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',122,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(13,'Joyce Banda','johnbanda10@parent.com','0977000010',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',123,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(14,'Emmanuel Lungu','gracephiri11@parent.com','0977000011',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',124,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(15,'Beatrice Phiri','sarahmwanza12@parent.com','0977000012',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',125,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(16,'Daniel Moyo','johnbanda13@parent.com','0977000013',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',126,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(17,'Margaret Ng\'andu','gracephiri14@parent.com','0977000014',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',127,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(18,'Isaac Mwansa','sarahmwanza15@parent.com','0977000015',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',128,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(19,'Rachel Chisenga','johnbanda16@parent.com','0977000016',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',129,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(20,'George Sichone','gracephiri17@parent.com','0977000017',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',130,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(21,'Alice Mwamba','sarahmwanza18@parent.com','0977000018',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',131,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(22,'Charles Mbewe','johnbanda19@parent.com','0977000019',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',132,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(23,'Charity Chola','gracephiri20@parent.com','0977000020',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',133,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(24,'Francis Kabwe','sarahmwanza21@parent.com','0977000021',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',134,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(25,'Patience Mumba','johnbanda22@parent.com','0977000022',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',135,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(26,'James Musonda','gracephiri23@parent.com','0977000023',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',136,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(27,'Miriam Malama','sarahmwanza24@parent.com','0977000024',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',137,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(28,'Robert Nyirenda','johnbanda25@parent.com','0977000025',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',138,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(29,'Christine Kunda','gracephiri26@parent.com','0977000026',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',139,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(30,'Simon Mwila','sarahmwanza27@parent.com','0977000027',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',140,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(31,'Florence Siame','johnbanda28@parent.com','0977000028',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',141,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(32,'Andrew Mwewa','gracephiri29@parent.com','0977000029',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',142,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(33,'Martha Chileshe','sarahmwanza30@parent.com','0977000030',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',143,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(34,'Thomas Simukoko','johnbanda31@parent.com','0977000031',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',144,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(35,'Elizabeth Bwalya','gracephiri32@parent.com','0977000032',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',145,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(36,'Richard Kasonde','sarahmwanza33@parent.com','0977000033',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',146,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(37,'Nancy Muleya','johnbanda34@parent.com','0977000034',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',147,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(38,'Kenneth Chanda','gracephiri35@parent.com','0977000035',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',148,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(39,'Priscilla Kapembwa','sarahmwanza36@parent.com','0977000036',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',149,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(40,'Vincent Sikazwe','johnbanda37@parent.com','0977000037',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',150,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(41,'Janet Mwale','gracephiri38@parent.com','0977000038',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',151,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(42,'Michael Lubinda','sarahmwanza39@parent.com','0977000039',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',152,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(43,'Agnes Mwanza','johnbanda40@parent.com','0977000040',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',153,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(44,'Lawrence Chishimba','gracephiri41@parent.com','0977000041',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',154,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(45,'Doreen Phiri','sarahmwanza42@parent.com','0977000042',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',155,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(46,'Stephen Zimba','johnbanda43@parent.com','0977000043',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',156,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(47,'Brenda Banda','gracephiri44@parent.com','0977000044',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',157,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(48,'Christopher Mwale','sarahmwanza45@parent.com','0977000045',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',158,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(49,'Hannah Chilufya','johnbanda46@parent.com','0977000046',NULL,NULL,NULL,'father',NULL,'Lusaka, Zambia',159,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(50,'Paul Mulenga','gracephiri47@parent.com','0977000047',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',160,4,'2025-10-17 03:44:55','2025-10-17 03:45:17'),(51,'Grace Siwale','sarahmwanza48@parent.com','0977000048',NULL,NULL,NULL,'mother',NULL,'Lusaka, Zambia',161,4,'2025-10-17 03:44:55','2025-10-17 03:45:17');
/*!40000 ALTER TABLE `parent_guardians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_transactions`
--

DROP TABLE IF EXISTS `payment_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_fee_id` bigint unsigned DEFAULT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `type` enum('payment','refund','adjustment','balance_forward','overpayment','credit_applied') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` enum('cash','bank_transfer','mobile_money','cheque','credit_card','online_payment','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','completed','failed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `processed_by` bigint unsigned DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT '2025-10-15 03:39:56',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_transactions_reference_number_unique` (`reference_number`),
  KEY `payment_transactions_student_fee_id_type_index` (`student_fee_id`,`type`),
  KEY `payment_transactions_reference_number_index` (`reference_number`),
  KEY `payment_transactions_transaction_date_index` (`transaction_date`),
  KEY `payment_transactions_status_index` (`status`),
  KEY `idx_payment_transactions_fee` (`student_fee_id`),
  KEY `idx_payment_transactions_type` (`type`),
  KEY `idx_payment_transactions_date` (`transaction_date`),
  KEY `idx_payment_transactions_fee_type` (`student_fee_id`,`type`),
  KEY `idx_payment_transactions_academic_year` (`academic_year_id`),
  KEY `idx_payment_trans_year_date` (`academic_year_id`,`transaction_date`),
  KEY `idx_payment_trans_year_method` (`academic_year_id`,`payment_method`),
  KEY `idx_payment_trans_year_status` (`academic_year_id`,`status`),
  CONSTRAINT `payment_transactions_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_transactions`
--

LOCK TABLES `payment_transactions` WRITE;
/*!40000 ALTER TABLE `payment_transactions` DISABLE KEYS */;
INSERT INTO `payment_transactions` VALUES (1,3,2,2000.00,'payment','RCP-2025-046459',NULL,'bank_transfer',NULL,'Payment for school fee','completed',1,'2025-10-16 00:00:00','2025-10-16 13:09:46','2025-10-16 13:09:46');
/*!40000 ALTER TABLE `payment_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payrolls`
--

DROP TABLE IF EXISTS `payrolls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payrolls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned DEFAULT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `month` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` year DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL,
  `allowances` json DEFAULT NULL,
  `deductions` json DEFAULT NULL,
  `gross_salary` decimal(10,2) DEFAULT NULL,
  `net_salary` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('pending','paid') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_payrolls_academic_year` (`academic_year_id`),
  KEY `idx_payrolls_year_month` (`academic_year_id`,`month`,`year`),
  KEY `idx_payrolls_year_employee` (`academic_year_id`,`employee_id`),
  CONSTRAINT `payrolls_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payrolls`
--

LOCK TABLES `payrolls` WRITE;
/*!40000 ALTER TABLE `payrolls` DISABLE KEYS */;
INSERT INTO `payrolls` VALUES (1,32,2,'October',NULL,2025,2000.00,'[{\"type\": \"Housing Allowance\", \"amount\": \"250\", \"net_salary\": 0, \"gross_salary\": 0}, {\"type\": \"Transport Allowance\", \"amount\": \"50\", \"net_salary\": 0, \"gross_salary\": 0}]','[{\"type\": \"NAPSA\", \"amount\": 100}, {\"type\": \"NHIMA\", \"amount\": 23}, {\"type\": \"PAYE\", \"amount\": 0}]',2300.00,2177.00,'paid','2025-10-22','Testing payslip','2025-10-16 10:18:11','2025-10-16 10:18:11');
/*!40000 ALTER TABLE `payrolls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned DEFAULT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create` tinyint(1) NOT NULL DEFAULT '0',
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `update` tinyint(1) NOT NULL DEFAULT '0',
  `delete` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `photos`
--

DROP TABLE IF EXISTS `photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `photos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `album_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caption` text COLLATE utf8mb4_unicode_ci,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int DEFAULT '0',
  `featured` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photos`
--

LOCK TABLES `photos` WRITE;
/*!40000 ALTER TABLE `photos` DISABLE KEYS */;
/*!40000 ALTER TABLE `photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qr_payments`
--

DROP TABLE IF EXISTS `qr_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qr_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `customer_mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_id` bigint unsigned DEFAULT NULL,
  `student_fee_id` bigint unsigned DEFAULT NULL,
  `status` enum('pending','processing','completed','failed','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `cgrate_payment_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_message` text COLLATE utf8mb4_unicode_ci,
  `response_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `initiated_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qr_payments_qr_code_unique` (`qr_code`),
  UNIQUE KEY `qr_payments_payment_reference_unique` (`payment_reference`),
  KEY `qr_payments_student_fee_id_foreign` (`student_fee_id`),
  KEY `qr_payments_status_index` (`status`),
  KEY `qr_payments_payment_reference_index` (`payment_reference`),
  KEY `qr_payments_student_id_index` (`student_id`),
  CONSTRAINT `qr_payments_student_fee_id_foreign` FOREIGN KEY (`student_fee_id`) REFERENCES `student_fees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `qr_payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qr_payments`
--

LOCK TABLES `qr_payments` WRITE;
/*!40000 ALTER TABLE `qr_payments` DISABLE KEYS */;
INSERT INTO `qr_payments` VALUES (1,'UVItSkRSSVMyUFlTTnwxMDB8MjYwOTY5ODkzMTgy','QR-JDRIS2PYSN',100.00,'260969893182',4,NULL,'pending',NULL,NULL,NULL,'2025-10-16 13:21:48',NULL,'2025-10-17 13:21:05','2025-10-16 13:21:48','2025-10-16 13:21:48'),(2,'UVItWEJMM0ZUU1JQVXwxMDB8MjYwOTY5NjgzMzcwODE=','QR-XBL3FTSRPU',100.00,'26096968337081',1,NULL,'failed',NULL,'The payment service is currently experiencing delays. Please check your phone for the payment prompt and complete the transaction. If the prompt does not appear within 2 minutes, please try again.',NULL,'2025-10-16 13:34:29',NULL,'2025-10-17 13:34:29','2025-10-16 13:34:29','2025-10-16 13:35:00'),(3,'UVItSzlSTllESFI2THwxMDB8MjYwOTY4MzM3MDgx','QR-K9RNYDHR6L',100.00,'260968337081',1,NULL,'failed',NULL,'The payment service is currently experiencing delays. Please check your phone for the payment prompt and complete the transaction. If the prompt does not appear within 2 minutes, please try again.',NULL,'2025-10-16 13:35:32',NULL,'2025-10-17 13:35:32','2025-10-16 13:35:32','2025-10-16 13:36:02'),(4,'UVItSVJOU1JYS1RYTnwxODB8MjYwOTc0MDQ1NTU4','QR-IRNSRXKTXN',180.00,'260974045558',1,NULL,'failed',NULL,'The payment service is currently experiencing delays. Please check your phone for the payment prompt and complete the transaction. If the prompt does not appear within 2 minutes, please try again.',NULL,'2025-10-16 14:01:12',NULL,'2025-10-17 14:01:12','2025-10-16 14:01:12','2025-10-16 14:01:42');
/*!40000 ALTER TABLE `qr_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_card_comments`
--

DROP TABLE IF EXISTS `report_card_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_card_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `term_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `class_teacher_comment` text COLLATE utf8mb4_unicode_ci,
  `class_teacher_id` bigint unsigned DEFAULT NULL,
  `class_teacher_commented_at` timestamp NULL DEFAULT NULL,
  `head_teacher_comment` text COLLATE utf8mb4_unicode_ci,
  `head_teacher_id` bigint unsigned DEFAULT NULL,
  `head_teacher_commented_at` timestamp NULL DEFAULT NULL,
  `last_generated_at` timestamp NULL DEFAULT NULL,
  `generation_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_term_year_comment` (`student_id`,`term_id`,`academic_year_id`),
  KEY `report_card_comments_academic_year_id_foreign` (`academic_year_id`),
  KEY `report_card_comments_class_teacher_id_foreign` (`class_teacher_id`),
  KEY `report_card_comments_head_teacher_id_foreign` (`head_teacher_id`),
  KEY `report_card_comments_term_id_academic_year_id_index` (`term_id`,`academic_year_id`),
  CONSTRAINT `report_card_comments_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `report_card_comments_class_teacher_id_foreign` FOREIGN KEY (`class_teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `report_card_comments_head_teacher_id_foreign` FOREIGN KEY (`head_teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `report_card_comments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `report_card_comments_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `terms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_card_comments`
--

LOCK TABLES `report_card_comments` WRITE;
/*!40000 ALTER TABLE `report_card_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_card_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `results`
--

DROP TABLE IF EXISTS `results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `homework_id` bigint unsigned DEFAULT NULL,
  `exam_type` enum('mid-term','final','quiz','assignment','end-of-term') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marks` decimal(5,2) DEFAULT NULL,
  `grade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `term` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` year DEFAULT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `notify_parent` tinyint(1) DEFAULT '1',
  `sms_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `results_student_id_subject_id_homework_id_term_year_index` (`student_id`,`subject_id`,`homework_id`,`term`,`year`),
  KEY `results_exam_type_index` (`exam_type`),
  KEY `idx_results_academic_year` (`academic_year_id`),
  KEY `idx_results_year_student` (`academic_year_id`,`student_id`),
  KEY `idx_results_year_term` (`academic_year_id`,`term`),
  KEY `idx_results_year_subject` (`academic_year_id`,`subject_id`),
  CONSTRAINT `results_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `results`
--

LOCK TABLES `results` WRITE;
/*!40000 ALTER TABLE `results` DISABLE KEYS */;
/*!40000 ALTER TABLE `results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `custom_permissions` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','Full access to all system features',1,NULL,'2025-10-15 03:45:03','2025-10-15 03:45:03'),(2,'Teacher','Access to teaching and student management',1,NULL,'2025-10-15 03:45:03','2025-10-15 03:45:03'),(3,'Student','Access to student portal',1,NULL,'2025-10-15 03:45:03','2025-10-15 03:45:03'),(4,'Parent','Access to parent portal',1,NULL,'2025-10-15 03:45:03','2025-10-15 03:45:03'),(5,'Accountant','Access to financial features',1,NULL,'2025-10-15 03:45:03','2025-10-15 03:45:16'),(7,'Librarian','Access to library management',1,NULL,'2025-10-15 03:45:03','2025-10-15 03:45:16'),(8,'Security','Access to security features',1,NULL,'2025-10-15 03:45:03','2025-10-15 03:45:16'),(9,'Support','Access to support features',1,NULL,'2025-10-15 03:45:03','2025-10-15 03:45:16'),(10,'Clinician','Medical staff responsible for student health care',1,NULL,'2025-10-16 09:00:41','2025-10-16 09:00:41'),(11,'Director','School director with administrative oversight',1,NULL,'2025-10-16 09:00:41','2025-10-16 09:00:41'),(12,'Dean of Primary','Dean of primary school teachers',1,NULL,'2025-10-16 09:00:41','2025-10-16 09:00:41'),(13,'Dean of Secondary','Dean of secondary school teachers',1,NULL,'2025-10-16 09:00:41','2025-10-16 09:00:41'),(14,'Driver','School bus/transport driver',1,NULL,'2025-10-16 09:00:41','2025-10-16 09:00:41'),(15,'Head of Department','Head of Department',1,NULL,'2025-10-16 09:08:17','2025-10-16 09:08:17'),(16,'Head Teacher Secondary',NULL,1,NULL,'2025-12-15 03:51:32','2025-12-15 03:51:32'),(17,'Deputy Head Primary',NULL,1,NULL,'2025-12-15 03:51:32','2025-12-15 03:51:32'),(18,'Deputy Head Secondary',NULL,1,NULL,'2025-12-15 03:51:32','2025-12-15 03:51:32');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_classes`
--

DROP TABLE IF EXISTS `school_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_classes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `section` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_classes`
--

LOCK TABLES `school_classes` WRITE;
/*!40000 ALTER TABLE `school_classes` DISABLE KEYS */;
/*!40000 ALTER TABLE `school_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_sections`
--

DROP TABLE IF EXISTS `school_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `head_of_section_id` bigint unsigned DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_sections`
--

LOCK TABLES `school_sections` WRITE;
/*!40000 ALTER TABLE `school_sections` DISABLE KEYS */;
INSERT INTO `school_sections` VALUES (1,'Early Childhood Education','ECE','Early Childhood Education',20,0,1,'2025-10-16 04:04:51','2025-10-16 04:04:51'),(2,'Lower Primary Section','LPS','Lower Primary Section',20,1,1,'2025-10-16 04:05:55','2025-10-16 04:05:55'),(3,'Upper Primary Section','UPS','Upper Primary Section',20,2,1,'2025-10-16 04:06:45','2025-10-16 04:06:45'),(4,'Junior Secondary School','JSS','Junior Secondary School',28,3,1,'2025-10-16 04:08:13','2025-10-16 04:08:13'),(5,'Senior Secondary School','SSC','Senior Secondary School',30,4,1,'2025-10-16 04:08:51','2025-10-16 04:08:51');
/*!40000 ALTER TABLE `school_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_settings`
--

DROP TABLE IF EXISTS `school_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_pin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_motto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_vision` text COLLATE utf8mb4_unicode_ci,
  `school_mission` text COLLATE utf8mb4_unicode_ci,
  `school_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `header_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `footer_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_card_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#1e40af',
  `secondary_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#64748b',
  `accent_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#f59e0b',
  `academic_year_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'YYYY',
  `terms_per_year` int NOT NULL DEFAULT '3',
  `grading_system` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `passing_mark` int NOT NULL DEFAULT '40',
  `max_mark` int NOT NULL DEFAULT '100',
  `show_position_in_class` tinyint(1) NOT NULL DEFAULT '1',
  `show_position_in_grade` tinyint(1) NOT NULL DEFAULT '1',
  `show_grade_average` tinyint(1) NOT NULL DEFAULT '1',
  `enable_continuous_assessment` tinyint(1) NOT NULL DEFAULT '1',
  `ca_weight_percentage` int NOT NULL DEFAULT '40',
  `exam_weight_percentage` int NOT NULL DEFAULT '60',
  `grade_a_min` int NOT NULL DEFAULT '80',
  `grade_b_min` int NOT NULL DEFAULT '65',
  `grade_c_min` int NOT NULL DEFAULT '50',
  `grade_d_min` int NOT NULL DEFAULT '40',
  `grade_e_min` int NOT NULL DEFAULT '0',
  `grade_a_remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Distinction',
  `grade_b_remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Merit',
  `grade_c_remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Credit',
  `grade_d_remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pass',
  `grade_e_remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Fail',
  `school_start_time` time NOT NULL DEFAULT '07:30:00',
  `school_end_time` time NOT NULL DEFAULT '13:00:00',
  `late_arrival_minutes` int NOT NULL DEFAULT '15',
  `notify_parent_on_absence` tinyint(1) NOT NULL DEFAULT '1',
  `notify_parent_on_late` tinyint(1) NOT NULL DEFAULT '0',
  `absence_notification_threshold` int NOT NULL DEFAULT '3',
  `school_days` json DEFAULT NULL,
  `enable_online_payments` tinyint(1) NOT NULL DEFAULT '0',
  `enable_partial_payments` tinyint(1) NOT NULL DEFAULT '1',
  `minimum_partial_payment` decimal(10,2) NOT NULL DEFAULT '100.00',
  `enable_late_fees` tinyint(1) NOT NULL DEFAULT '1',
  `late_fee_percentage` decimal(5,2) NOT NULL DEFAULT '5.00',
  `grace_period_days` int NOT NULL DEFAULT '7',
  `invoice_prefix` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INV',
  `receipt_prefix` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'RCP',
  `payment_instructions` text COLLATE utf8mb4_unicode_ci,
  `payment_methods` json DEFAULT NULL,
  `bank_details` json DEFAULT NULL,
  `mobile_money_details` json DEFAULT NULL,
  `sms_sender_id` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enable_sms_notifications` tinyint(1) NOT NULL DEFAULT '1',
  `enable_email_notifications` tinyint(1) NOT NULL DEFAULT '1',
  `enable_whatsapp_notifications` tinyint(1) NOT NULL DEFAULT '0',
  `sms_on_fee_payment` tinyint(1) NOT NULL DEFAULT '1',
  `sms_on_result_release` tinyint(1) NOT NULL DEFAULT '1',
  `sms_on_attendance` tinyint(1) NOT NULL DEFAULT '0',
  `sms_on_homework` tinyint(1) NOT NULL DEFAULT '0',
  `sms_balance_alert_threshold` int NOT NULL DEFAULT '100',
  `report_card_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'standard',
  `show_teacher_comments` tinyint(1) NOT NULL DEFAULT '1',
  `show_headteacher_comments` tinyint(1) NOT NULL DEFAULT '1',
  `show_principal_signature` tinyint(1) NOT NULL DEFAULT '1',
  `show_class_teacher_signature` tinyint(1) NOT NULL DEFAULT '1',
  `show_parent_signature_line` tinyint(1) NOT NULL DEFAULT '1',
  `show_attendance_summary` tinyint(1) NOT NULL DEFAULT '1',
  `show_conduct_grade` tinyint(1) NOT NULL DEFAULT '1',
  `principal_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Executive Director',
  `principal_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_card_footer_text` text COLLATE utf8mb4_unicode_ci,
  `next_term_starts` date DEFAULT NULL,
  `next_term_ends` date DEFAULT NULL,
  `date_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'd/m/Y',
  `time_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'H:i',
  `datetime_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'd/m/Y H:i',
  `session_timeout_minutes` int NOT NULL DEFAULT '120',
  `enable_maintenance_mode` tinyint(1) NOT NULL DEFAULT '0',
  `maintenance_message` text COLLATE utf8mb4_unicode_ci,
  `enable_student_portal` tinyint(1) NOT NULL DEFAULT '1',
  `enable_parent_portal` tinyint(1) NOT NULL DEFAULT '1',
  `enable_teacher_portal` tinyint(1) NOT NULL DEFAULT '1',
  `require_password_change_on_first_login` tinyint(1) NOT NULL DEFAULT '1',
  `password_expiry_days` int NOT NULL DEFAULT '90',
  `max_login_attempts` int NOT NULL DEFAULT '5',
  `lockout_duration_minutes` int NOT NULL DEFAULT '30',
  `enable_auto_backup` tinyint(1) NOT NULL DEFAULT '0',
  `backup_frequency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'daily',
  `backup_time` time NOT NULL DEFAULT '02:00:00',
  `backup_retention_days` int NOT NULL DEFAULT '30',
  `primary_head_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_head_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Head Teacher Primary',
  `primary_head_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_head_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_head_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Head Teacher Secondary',
  `secondary_head_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_settings` json DEFAULT NULL,
  `settings_last_updated_at` timestamp NULL DEFAULT NULL,
  `settings_updated_by` bigint unsigned DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_province` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alternate_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ZMW',
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Africa/Lusaka',
  `school_head_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_head_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media_links` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_settings`
--

LOCK TABLES `school_settings` WRITE;
/*!40000 ALTER TABLE `school_settings` DISABLE KEYS */;
INSERT INTO `school_settings` VALUES (1,'School Name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'#1e40af','#64748b','#f59e0b','YYYY',3,'percentage',40,100,1,1,1,1,40,60,80,65,50,40,0,'Distinction','Merit','Credit','Pass','Fail','07:30:00','13:00:00',15,1,0,3,'[1, 2, 3, 4, 5]',0,1,100.00,1,5.00,7,'INV','RCP',NULL,'[\"cash\", \"bank_transfer\", \"mobile_money\"]',NULL,NULL,NULL,1,1,0,1,1,0,0,100,'standard',1,1,1,1,1,1,1,NULL,'Executive Director',NULL,NULL,NULL,NULL,'d/m/Y','H:i','d/m/Y H:i',120,0,NULL,1,1,1,1,90,5,30,0,'daily','02:00:00',30,NULL,'Head Teacher Primary',NULL,NULL,'Head Teacher Secondary',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ZMW','Africa/Lusaka',NULL,NULL,NULL,'2026-01-22 10:16:44','2026-01-22 10:16:44');
/*!40000 ALTER TABLE `school_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('2Cg1ZCkq2dVXu3PgYDkZMg1jdsSYOJhvslgjnDGr',NULL,'127.0.0.1','Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTG9vRHJuWmtBNmc1TVczZVM0MlhaWEpCVFZZZmJOTGlwZWhCT25nMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly9wb3J0YWwuc3RmcmFuY2lzb2Zhc3Npc2kudGVjaCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1770663370),('9wAboW1weLzLYmnFBEPYR4HYMRvJ2yIazZwHdc04',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVFY0dlBXZTZNSk1lTjU5a1dCTzhXZHVFNGF1aTFiNGNLMUlPUDBsNiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9wb3J0YWwuc3RmcmFuY2lzb2Zhc3Npc2kudGVjaC9hZG1pbi9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1770660284),('gmjFGMVfInMEn5HHnwuhEvlZzptFO8BWTlgVyiyl',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiRmtnMFhQajlnRUhidXpvR3JqckxtNlRackZPV0hwbzJoa0xPUEplTSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjcwOiJodHRwOi8vcG9ydGFsLnN0ZnJhbmNpc29mYXNzaXNpLnRlY2gvYWRtaW4vaG9tZXdvcmstc3VibWlzc2lvbnMvY3JlYXRlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjg6ImZpbGFtZW50IjthOjA6e319',1770661031),('oXexWrLYnEUm5rUklQXDuHm5BXMX0EbFfSAwuNig',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZjU4WHRhNTNKUHZNWXRVQ1k4Wm82RVZLSzZYSnI4UGR0NjZWdVlLbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly9wb3J0YWwuc3RmcmFuY2lzb2Zhc3Npc2kudGVjaC9hZG1pbi90ZWFjaGVycyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo4OiJmaWxhbWVudCI7YTowOnt9fQ==',1770697675),('Qfw6yf44hTA02obYbcEHa15gVhF3u7vLk67EPKTp',NULL,'127.0.0.1','Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWnRqY1VsRzRadDR1bmNkT3JqZnVwMUVmZUxxc3dCcFJ0ZUtvVkg0aCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly9wb3J0YWwuc3RmcmFuY2lzb2Zhc3Npc2kudGVjaC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1770663374),('sHowwxZGLQ16Zg0Ng50FoCtNhMOsUAEStgRSCdvx',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 12; SAMSUNG SM-A415F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/23.0 Chrome/115.0.0.0 Mobile Safari/537.3','YTozOntzOjY6Il90b2tlbiI7czo0MDoibndlTm5CQ05HbEZnS1pnOG83VWNnVFBSU3ZjYjM4U2dnMnkwNWZpWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly9wb3J0YWwuc3RmcmFuY2lzb2Zhc3Npc2kudGVjaC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1770670594),('uDPOLs7akE85bUyMTfQxkYToaQ7AxGV9UZ2S0Vyl',NULL,'127.0.0.1','Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/6.0)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWlJOZ0haQ0lDUFVuMTRXWE1lTW9rMldzWUd2RkFER3c3bGd6bG56SCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly9wb3J0YWwuc3RmcmFuY2lzb2Zhc3Npc2kudGVjaC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1770679084);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_credit_transactions`
--

DROP TABLE IF EXISTS `sms_credit_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_credit_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('credit','debit','adjustment','refund') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Transaction type',
  `amount` int NOT NULL,
  `balance_before` int NOT NULL,
  `balance_after` int NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Transaction description',
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'External reference (receipt, invoice, etc.)',
  `sms_log_id` bigint unsigned DEFAULT NULL COMMENT 'Related SMS log for debits',
  `performed_by` bigint unsigned DEFAULT NULL COMMENT 'User who performed the transaction',
  `metadata` json DEFAULT NULL COMMENT 'Additional transaction data',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sms_credit_transactions_sms_log_id_foreign` (`sms_log_id`),
  KEY `sms_credit_transactions_performed_by_foreign` (`performed_by`),
  KEY `sms_credit_transactions_type_created_at_index` (`type`,`created_at`),
  KEY `sms_credit_transactions_reference_index` (`reference`),
  CONSTRAINT `sms_credit_transactions_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sms_credit_transactions_sms_log_id_foreign` FOREIGN KEY (`sms_log_id`) REFERENCES `sms_logs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_credit_transactions`
--

LOCK TABLES `sms_credit_transactions` WRITE;
/*!40000 ALTER TABLE `sms_credit_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_credit_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_credits`
--

DROP TABLE IF EXISTS `sms_credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_credits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `balance` int NOT NULL DEFAULT '0',
  `cost_per_sms` int NOT NULL DEFAULT '1',
  `low_balance_threshold` int NOT NULL DEFAULT '50',
  `allow_negative_balance` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Allow sending when credits are insufficient',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Enable/disable SMS sending',
  `last_topped_up_at` timestamp NULL DEFAULT NULL,
  `last_topped_up_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sms_credits_last_topped_up_by_foreign` (`last_topped_up_by`),
  CONSTRAINT `sms_credits_last_topped_up_by_foreign` FOREIGN KEY (`last_topped_up_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_credits`
--

LOCK TABLES `sms_credits` WRITE;
/*!40000 ALTER TABLE `sms_credits` DISABLE KEYS */;
INSERT INTO `sms_credits` VALUES (1,0,1,50,0,1,NULL,NULL,'2025-12-15 03:51:31','2025-12-15 03:51:31');
/*!40000 ALTER TABLE `sms_credits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_logs`
--

DROP TABLE IF EXISTS `sms_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `recipient` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` enum('sent','delivered','failed','pending') COLLATE utf8mb4_unicode_ci DEFAULT 'sent',
  `message_type` enum('homework_notification','result_notification','fee_reminder','event_notification','general','other','student_credentials','staff_credentials','broadcast') COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `reference_id` bigint unsigned DEFAULT NULL COMMENT 'ID of related record (homework, result, etc.)',
  `cost` decimal(10,2) DEFAULT NULL,
  `provider_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Message ID from SMS provider',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `sent_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sms_logs_status_message_type_reference_id_index` (`status`,`message_type`,`reference_id`),
  KEY `sms_logs_recipient_index` (`recipient`),
  KEY `sms_logs_sent_by_index` (`sent_by`),
  KEY `idx_sms_logs_status` (`status`),
  KEY `idx_sms_logs_type` (`message_type`),
  KEY `idx_sms_logs_created` (`created_at`),
  KEY `idx_sms_logs_reference` (`reference_id`,`message_type`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_logs`
--

LOCK TABLES `sms_logs` WRITE;
/*!40000 ALTER TABLE `sms_logs` DISABLE KEYS */;
INSERT INTO `sms_logs` VALUES (1,'260969893182','Hello Ben Mwaba, your child Charles Mwaba has been registered at St Francis of Assisi School.\n\nYou can view their information on your parent portal: https://staff.stfrancisofassisi.tech/\nYour username: ben.mwaba(at)stfrancisofassisi.tech','sent','general',1,1.00,NULL,NULL,1,'2025-10-16 06:09:30','2025-10-16 06:09:31'),(2,'260978654321','Hello Lydia Bwalya Grace, your child Lydia Bwalya has been registered at St Francis of Assisi School.\n\nPlease contact the school office for more information.','sent','general',2,0.50,NULL,NULL,1,'2025-10-16 06:23:21','2025-10-16 06:23:22'),(3,'260964443502','Dear Teddson Lung\'eenda, Fees for Grabrial Lung\'enda have been set for Term 3 (2025). Grade: Grade 7, Amount: ZMW 2,450.00. Please visit the school office for payment. Thank you.','sent','general',2,1.00,NULL,NULL,1,'2025-10-16 10:45:48','2025-10-16 10:45:48'),(4,'260964443502','Hello Teddson Lung\'eenda, your child Grabrial Lung\'enda has been registered at St Francis of Assisi School.\n\nYou can view their information on your parent portal: https://staff.stfrancisofassisi.tech/\nYour username: teddson.lung\'eenda(at)stfrancisofassisi.tech','sent','general',3,1.00,NULL,NULL,1,'2025-10-16 10:45:48','2025-10-16 10:45:49'),(5,'260969893182','Dear Ben Mwaba, Fees for Euell Kunda have been set for Term 3 (2025). Grade: Grade 2, Amount: ZMW 2,100.00. Please visit the school office for payment. Thank you.','sent','general',3,1.00,NULL,NULL,1,'2025-10-16 10:50:09','2025-10-16 10:50:10'),(6,'260969893182','Hello Ben Mwaba, your child Euell Kunda has been registered at St Francis of Assisi School.\n\nYou can view their information on your parent portal: https://staff.stfrancisofassisi.tech/\nYour username: ben.mwaba(at)stfrancisofassisi.tech','sent','general',4,1.00,NULL,NULL,1,'2025-10-16 10:50:10','2025-10-16 10:50:10'),(7,'260968963214','Welcome Monica Mpoya! Your St Francis Portal account is ready.\nEmail: monicampoya772@gmail.com\nPass: _/i3F,\\,}/0V\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',48,1.50,NULL,NULL,1,'2025-10-17 02:27:57','2025-10-17 02:27:57'),(8,'260978652310','Welcome Gift Zunda! Your St Francis Portal account is ready.\nEmail: giftnzunda@gmail.com\nPass: 3(KB!86hpCpE\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',49,1.50,NULL,NULL,1,'2025-10-17 02:30:00','2025-10-17 02:30:01'),(9,'260969857412','Welcome Memory Chomba! Your St Francis Portal account is ready.\nEmail: memorychomba483@gmail.com\nPass: \\G!/Bs7n9z-5\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',50,1.50,NULL,NULL,1,'2025-10-17 02:34:52','2025-10-17 02:34:53'),(10,'260774568921','Welcome Musa Doris! Your St Francis Portal account is ready.\nEmail: mulengamusa429@gmail.com\nPass: 4T%kZV&x>}\\N\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',51,1.50,NULL,NULL,1,'2025-10-17 02:36:26','2025-10-17 02:36:26'),(11,'260975421579','Welcome Musakanya Mutale! Your St Francis Portal account is ready.\nEmail: mutalemusakanya944@gmail.com\nPass: I^yu^*5S>*9<\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',52,1.50,NULL,NULL,1,'2025-10-17 02:39:56','2025-10-17 02:39:57'),(12,'260754125478','Welcome Eunice Kansa! Your St Francis Portal account is ready.\nEmail: eunicekansa@gmail.com\nPass: n*yjI\\UIlK0O\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',53,1.50,NULL,NULL,1,'2025-10-17 02:41:56','2025-10-17 02:41:56'),(13,'260955648695','Welcome Sinyangwe Euell! Your St Francis Portal account is ready.\nEmail: sinyangweeuell@gmail.com\nPass: 3g#ydlpm9X$]\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',54,1.50,NULL,NULL,1,'2025-10-17 02:44:09','2025-10-17 02:44:10'),(14,'260977451278','Welcome Agness Mukupa! Your St Francis Portal account is ready.\nEmail: agnessmukupa2@gmail.com\nPass: s3lm0%r>Y0(H\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',55,1.50,NULL,NULL,1,'2025-10-17 02:45:40','2025-10-17 02:45:41'),(15,'260966524178','Welcome Mubisa Micheal! Your St Francis Portal account is ready.\nEmail: mubisamicheal@gamil.com\nPass: qr6Oz1V52c?<\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',56,1.50,NULL,NULL,1,'2025-10-17 02:47:44','2025-10-17 02:47:45'),(16,'260954781269','Welcome Leonard Kopakopa! Your St Francis Portal account is ready.\nEmail: leonardkopakopa@gmail.com\nPass: P[,ep1xXz/5_\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',57,1.50,NULL,NULL,1,'2025-10-17 02:50:05','2025-10-17 02:50:06'),(17,'260768953147','Welcome Nkandu Richard! Your St Francis Portal account is ready.\nEmail: richienkandu@gmail.com\nPass: ,MMjC!A28&Al\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',58,1.50,NULL,NULL,1,'2025-10-17 02:53:11','2025-10-17 02:53:11'),(18,'260963582143','Welcome Quintinoh Chibwe! Your St Francis Portal account is ready.\nEmail: quintinohchibwe89@gmail.com\nPass: QtEOF{*Lw(\\2\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',59,1.50,NULL,NULL,1,'2025-10-17 03:00:38','2025-10-17 03:00:39'),(19,'2609553214','Welcome Silwamba Bruno! Your St Francis Portal account is ready.\nEmail: silwambabruno88@gmail.com\nPass: ?nl!v0_s{0)d\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',60,1.50,NULL,NULL,1,'2025-10-17 03:05:03','2025-10-17 03:05:03'),(20,'260966548972','Welcome Bwalya Mulenga! Your St Francis Portal account is ready.\nEmail: bwalyamuele1501@gmail.com\nPass: 1ZjI.Y59LL{7\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',61,1.50,NULL,NULL,1,'2025-10-17 03:10:14','2025-10-17 03:10:14'),(21,'260974862456','Welcome Evidence Mulenga! Your St Francis Portal account is ready.\nEmail: evidencem9@gmail.com\nPass: uV0;ZQ-$1M&$\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',62,1.50,NULL,NULL,1,'2025-10-17 03:13:36','2025-10-17 03:13:37'),(22,'260966857412','Welcome Kabanda Handson! Your St Francis Portal account is ready.\nEmail: kabambahandson7@gmail.com\nPass: !blUb~YS7gj|\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',63,1.50,NULL,NULL,1,'2025-10-17 03:16:17','2025-10-17 03:16:17'),(23,'260978564231','Welcome Vincent Mulenga! Your St Francis Portal account is ready.\nEmail: vincentmulenga1987@gmail.com\nPass: 8#X\\G4$[ukqs\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',64,1.50,NULL,NULL,1,'2025-10-17 03:20:40','2025-10-17 03:20:41'),(24,'260961629637','Welcome LUBINDA GODWIN! Your St Francis Portal account is ready.\nEmail: llubindagodwin@gmail.com\nPass: DOTgqd3((3\\W\nLogin: http://102.23.120.249:11022/admin','failed','staff_credentials',162,1.50,NULL,'Connection timeout: cURL error 28: Failed to connect to www.cloudservicezm.com port 443 after 10002 ms: Timeout was reached (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for https://www.cloudservicezm.com/smsservice/httpapi',1,'2025-10-17 06:41:22','2025-10-17 06:41:32'),(25,'260974944898','Welcome MWABA BRAVINE! Your St Francis Portal account is ready.\nEmail: bravine.mwaba312019@gmail.com\nPass: ]0:j$3Ds6JjN\nLogin: http://102.23.120.249:11022/admin','failed','staff_credentials',163,1.50,NULL,'Connection timeout: cURL error 28: Failed to connect to www.cloudservicezm.com port 443 after 10002 ms: Timeout was reached (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for https://www.cloudservicezm.com/smsservice/httpapi',1,'2025-10-17 06:48:16','2025-10-17 06:48:26'),(26,'260979176346','Welcome FREDDIE SIMPEMBA! Your St Francis Portal account is ready.\nEmail: fredsimpemba@gmail.com\nPass: 0_\\dHfw;uX3#\nLogin: http://102.23.120.249:11022/admin','sent','staff_credentials',165,1.50,NULL,NULL,1,'2025-10-17 07:23:40','2025-10-17 07:23:40'),(27,'260964443502','Dear Teddson Lung\'eenda, thank you for your payment of ZMW 1450 for Ruth Ng\'oma\'s fees. Grade: Grade 7, Term: Term 3. Total fee: ZMW 2450.00, Balance: ZMW 1000.00. Status: PARTIALLY PAID. Receipt No: RCP-2026-9814.','failed','fee_reminder',2,1.00,NULL,'Insufficient SMS credit balance.',1,'2026-02-09 18:11:46','2026-02-09 18:11:46');
/*!40000 ALTER TABLE `sms_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_designations`
--

DROP TABLE IF EXISTS `staff_designations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_designations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `section` enum('primary','secondary','both') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'both',
  `hierarchy_level` int NOT NULL DEFAULT '5',
  `permissions` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_designations_code_unique` (`code`),
  KEY `staff_designations_section_is_active_index` (`section`,`is_active`),
  KEY `staff_designations_hierarchy_level_index` (`hierarchy_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_designations`
--

LOCK TABLES `staff_designations` WRITE;
/*!40000 ALTER TABLE `staff_designations` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_designations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_fees`
--

DROP TABLE IF EXISTS `student_fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_fees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `fee_structure_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned DEFAULT NULL,
  `term_id` bigint unsigned DEFAULT NULL,
  `grade_id` bigint unsigned DEFAULT NULL,
  `payment_status` enum('unpaid','partial','paid','overpaid') COLLATE utf8mb4_unicode_ci DEFAULT 'unpaid',
  `amount_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_deadline` date DEFAULT NULL,
  `late_fee_applied` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `discount_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint unsigned DEFAULT NULL,
  `is_overdue` tinyint(1) NOT NULL DEFAULT '0',
  `overdue_since` date DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `receipt_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_of_payment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` enum('cash','bank_transfer','mobile_money','cheque','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `send_sms_notification` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_fee_structure` (`student_id`,`fee_structure_id`),
  KEY `student_fees_academic_year_id_foreign` (`academic_year_id`),
  KEY `student_fees_grade_id_foreign` (`grade_id`),
  KEY `student_fees_student_id_academic_year_id_index` (`student_id`,`academic_year_id`),
  KEY `student_fees_payment_status_index` (`payment_status`),
  KEY `student_fees_payment_date_index` (`payment_date`),
  KEY `student_fees_fee_structure_id_index` (`fee_structure_id`),
  KEY `idx_student_fees_lookup` (`student_id`,`academic_year_id`,`term_id`),
  KEY `idx_student_fees_payment_status` (`payment_status`),
  KEY `idx_student_fees_term_student` (`term_id`,`student_id`,`payment_status`),
  KEY `idx_student_fees_structure` (`fee_structure_id`),
  CONSTRAINT `student_fees_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  CONSTRAINT `student_fees_fee_structure_id_foreign` FOREIGN KEY (`fee_structure_id`) REFERENCES `fee_structures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_fees_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE SET NULL,
  CONSTRAINT `student_fees_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_fees_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `terms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_fees`
--

LOCK TABLES `student_fees` WRITE;
/*!40000 ALTER TABLE `student_fees` DISABLE KEYS */;
INSERT INTO `student_fees` VALUES (1,1,5,1,1,5,'partial',1500.00,600.00,NULL,0.00,0.00,NULL,NULL,NULL,NULL,0,NULL,'2025-10-16','RCP-2025-000001',NULL,'payment-proofs/01K7NTBTEG53C19PX1JMJT8YVA.jpg','bank_transfer',1,NULL,'2025-10-16 06:12:23','2025-10-16 06:12:23'),(2,3,66,2,6,10,'partial',1450.00,1000.00,NULL,0.00,0.00,NULL,NULL,NULL,NULL,0,NULL,'2026-02-09','RCP-2026-9814',NULL,NULL,'cash',0,'cash','2025-10-16 10:45:48','2026-02-09 18:11:46'),(3,4,61,2,6,5,'partial',2000.00,100.00,NULL,0.00,0.00,NULL,NULL,NULL,NULL,0,NULL,'2025-10-16','RCP-2025-046459',NULL,NULL,'bank_transfer',0,'Automatically created for new student registration','2025-10-16 10:50:09','2025-10-16 13:09:46');
/*!40000 ALTER TABLE `student_fees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `class_section_id` bigint unsigned DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `religious_denomination` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `standard_of_education` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smallpox_vaccination` enum('Yes','No','Not Sure') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_vaccinated` date DEFAULT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_id_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_guardian_id` bigint unsigned DEFAULT NULL,
  `grade_id` bigint unsigned DEFAULT NULL,
  `school_class_id` bigint unsigned DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `enrollment_term_id` bigint unsigned DEFAULT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `enrollment_status` enum('active','inactive','graduated','transferred') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `previous_school` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_information` text COLLATE utf8mb4_unicode_ci,
  `role_id` bigint unsigned NOT NULL DEFAULT '3',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_student_id_number_unique` (`student_id_number`),
  KEY `idx_students_enrollment_status` (`enrollment_status`),
  KEY `idx_students_grade_section` (`grade_id`,`class_section_id`),
  KEY `idx_students_parent_guardian` (`parent_guardian_id`),
  KEY `students_enrollment_term_id_foreign` (`enrollment_term_id`),
  KEY `idx_students_grade_id` (`grade_id`),
  KEY `idx_students_class_section_id` (`class_section_id`),
  KEY `idx_students_parent_guardian_id` (`parent_guardian_id`),
  KEY `idx_students_academic_year` (`academic_year_id`),
  KEY `idx_students_year_grade` (`academic_year_id`,`grade_id`),
  KEY `idx_students_year_status` (`academic_year_id`,`enrollment_status`),
  KEY `idx_students_id_year` (`student_id_number`,`academic_year_id`),
  CONSTRAINT `students_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `students_enrollment_term_id_foreign` FOREIGN KEY (`enrollment_term_id`) REFERENCES `terms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,'Michael Sichone',44,6,'2020-03-16','Chingola','Catholic','Primary','Yes','2025-03-13','male','Kamenza East','STD0001/2025',1,5,NULL,'2025-10-16',NULL,2,'active','Kakoso Primary School','student-photos/01K7NT6J9AD1BYYYT0Z5PHEEW6.png',NULL,3,NULL,'2025-10-16 06:09:30','2025-10-17 03:50:13'),(2,'Mary Phiri',45,6,'2020-01-01','Ndola','Catholic','Primary','No',NULL,'female','Kakoso','STD0002/2025',2,5,NULL,'2025-10-16',3,2,'active','Lilanda Primary school','student-photos/01K7NTZXC6JPCSYT2N4DC6YT8S.png',NULL,3,NULL,'2025-10-16 06:23:21','2025-10-17 03:50:13'),(3,'Ruth Ng\'oma',46,16,'2018-10-10',NULL,'Christian','Primary','Not Sure',NULL,'female',NULL,'STD0003/2025',3,10,NULL,'2025-10-16',6,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-16 10:45:48','2025-10-17 03:50:13'),(4,'Ruth Mumba',47,6,'2019-10-09',NULL,NULL,'Primary','Not Sure',NULL,'female',NULL,'STD0004/2025',1,5,NULL,'2025-10-16',6,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-16 10:50:09','2025-10-17 03:50:13'),(5,'Daniel Mwale',65,1,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0005/2025',4,1,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(6,'Benjamin Mumba',66,1,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0006/2025',5,1,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(7,'Elizabeth Chiluba',67,1,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0007/2025',6,1,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(8,'Ruth Sichone',68,2,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0008/2025',7,2,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(9,'Benjamin Sakala',69,2,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0009/2025',8,2,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(10,'Joseph Daka',70,2,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0010/2025',9,2,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(11,'Esther Mumba',71,3,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0011/2025',10,3,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(12,'Elizabeth Banda',72,3,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0012/2025',11,3,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(13,'Ruth Ng\'oma',73,3,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0013/2025',12,3,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(14,'Benjamin Banda',74,4,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0014/2025',13,4,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(15,'Hannah Ng\'oma',75,4,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0015/2025',14,4,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(16,'Michael Chiluba',76,4,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0016/2025',15,4,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(17,'Rebecca Sakala',77,6,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0017/2025',16,5,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(18,'Sarah Zulu',78,6,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0018/2025',17,5,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(19,'Isaac Tembo',79,6,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0019/2025',18,5,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(20,'Rebecca Kasonde',80,8,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0020/2025',19,6,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(21,'Joseph Sakala',81,8,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0021/2025',20,6,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(22,'Rebecca Chiluba',82,8,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0022/2025',21,6,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(23,'Rebecca Ng\'oma',83,10,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0023/2025',22,7,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(24,'Hannah Mulenga',84,10,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0024/2025',23,7,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(25,'Grace Kasonde',85,10,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0025/2025',24,7,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:13'),(26,'John Bwalya',86,11,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0026/2025',25,7,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(27,'Elizabeth Mumba',87,11,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0027/2025',26,7,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(28,'Rachel Kunda',88,11,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0028/2025',27,7,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(29,'John Mwale',89,12,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0029/2025',28,8,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(30,'James Sichone',90,12,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0030/2025',29,8,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(31,'Ruth Chiluba',91,12,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0031/2025',30,8,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(32,'Ruth Kunda',92,14,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0032/2025',31,9,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(33,'Rachel Zulu',93,14,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0033/2025',32,9,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(34,'Esther Sichone',94,14,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0034/2025',33,9,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(35,'Ruth Mutale',95,16,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0035/2025',34,10,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(36,'Esther Mutale',96,16,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0036/2025',35,10,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(37,'Rebecca Banda',97,16,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0037/2025',36,10,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(38,'Samuel Zulu',98,18,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0038/2025',37,11,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(39,'David Mutale',99,18,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0039/2025',38,11,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(40,'John Lungu',100,18,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0040/2025',39,11,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(41,'Michael Ng\'oma',101,20,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0041/2025',40,12,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(42,'James Mwanza',102,20,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0042/2025',41,12,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(43,'Rebecca Mumba',103,20,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0043/2025',42,12,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(44,'Joseph Mulenga',104,22,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0044/2025',43,13,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(45,'Rachel Nyirenda',105,22,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0045/2025',44,13,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(46,'John Mulenga',106,22,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0046/2025',45,13,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(47,'Hannah Phiri',107,24,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0047/2025',46,14,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(48,'Ruth Bwalya',108,24,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0048/2025',47,14,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(49,'Grace Mwale',109,24,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0049/2025',48,14,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(50,'Mary Bwalya',110,26,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'female',NULL,'STD0050/2025',49,15,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(51,'David Sichone',111,26,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0051/2025',50,15,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14'),(52,'James Mwale',112,26,'2015-10-17',NULL,NULL,NULL,NULL,NULL,'male',NULL,'STD0052/2025',51,15,NULL,NULL,1,2,'active',NULL,NULL,NULL,3,NULL,'2025-10-17 03:44:55','2025-10-17 03:50:14');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject_teachings`
--

DROP TABLE IF EXISTS `subject_teachings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject_teachings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `class_section_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_teaching_assignment` (`teacher_id`,`subject_id`,`class_section_id`,`academic_year_id`),
  KEY `subject_teachings_class_section_id_foreign` (`class_section_id`),
  KEY `idx_subject_teaching_teacher` (`teacher_id`),
  KEY `idx_subject_teaching_subject_class` (`subject_id`,`class_section_id`),
  KEY `idx_subject_teaching_year` (`academic_year_id`),
  CONSTRAINT `subject_teachings_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_teachings_class_section_id_foreign` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_teachings_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_teachings_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject_teachings`
--

LOCK TABLES `subject_teachings` WRITE;
/*!40000 ALTER TABLE `subject_teachings` DISABLE KEYS */;
INSERT INTO `subject_teachings` VALUES (30,14,5,1,2,'2025-10-17 02:27:58','2025-10-17 02:27:58'),(31,14,32,1,2,'2025-10-17 02:27:58','2025-10-17 02:27:58'),(32,14,33,1,2,'2025-10-17 02:27:58','2025-10-17 02:27:58'),(33,15,5,2,2,'2025-10-17 02:30:01','2025-10-17 02:30:01'),(34,15,32,2,2,'2025-10-17 02:30:01','2025-10-17 02:30:01'),(35,15,33,2,2,'2025-10-17 02:30:01','2025-10-17 02:30:01'),(36,16,5,3,2,'2025-10-17 02:34:53','2025-10-17 02:34:53'),(37,16,32,3,2,'2025-10-17 02:34:53','2025-10-17 02:34:53'),(38,16,33,3,2,'2025-10-17 02:34:53','2025-10-17 02:34:53'),(39,17,5,4,2,'2025-10-17 02:36:26','2025-10-17 02:36:26'),(40,17,6,4,2,'2025-10-17 02:36:26','2025-10-17 02:36:26'),(41,17,11,4,2,'2025-10-17 02:36:26','2025-10-17 02:36:26'),(42,17,34,4,2,'2025-10-17 02:36:26','2025-10-17 02:36:26'),(43,18,1,6,2,'2025-10-17 02:39:57','2025-10-17 02:39:57'),(44,18,2,6,2,'2025-10-17 02:39:57','2025-10-17 02:39:57'),(45,18,3,6,2,'2025-10-17 02:39:57','2025-10-17 02:39:57'),(46,18,4,6,2,'2025-10-17 02:39:57','2025-10-17 02:39:57'),(47,18,5,6,2,'2025-10-17 02:39:57','2025-10-17 02:39:57'),(48,18,6,6,2,'2025-10-17 02:39:57','2025-10-17 02:39:57'),(49,19,1,8,2,'2025-10-17 02:41:56','2025-10-17 02:41:56'),(50,19,2,8,2,'2025-10-17 02:41:56','2025-10-17 02:41:56'),(51,19,3,8,2,'2025-10-17 02:41:56','2025-10-17 02:41:56'),(52,19,4,8,2,'2025-10-17 02:41:56','2025-10-17 02:41:56'),(53,19,5,8,2,'2025-10-17 02:41:56','2025-10-17 02:41:56'),(54,19,6,8,2,'2025-10-17 02:41:56','2025-10-17 02:41:56'),(55,20,1,10,2,'2025-10-17 02:44:10','2025-10-17 02:44:10'),(56,20,2,10,2,'2025-10-17 02:44:10','2025-10-17 02:44:10'),(57,20,3,10,2,'2025-10-17 02:44:10','2025-10-17 02:44:10'),(58,20,4,10,2,'2025-10-17 02:44:10','2025-10-17 02:44:10'),(59,20,5,10,2,'2025-10-17 02:44:10','2025-10-17 02:44:10'),(60,20,6,10,2,'2025-10-17 02:44:10','2025-10-17 02:44:10'),(61,21,1,12,2,'2025-10-17 02:45:41','2025-10-17 02:45:41'),(62,21,2,12,2,'2025-10-17 02:45:41','2025-10-17 02:45:41'),(63,21,4,12,2,'2025-10-17 02:45:41','2025-10-17 02:45:41'),(64,21,5,12,2,'2025-10-17 02:45:41','2025-10-17 02:45:41'),(65,21,6,12,2,'2025-10-17 02:45:41','2025-10-17 02:45:41'),(66,22,1,14,2,'2025-10-17 02:47:45','2025-10-17 02:47:45'),(67,22,2,14,2,'2025-10-17 02:47:45','2025-10-17 02:47:45'),(68,22,4,14,2,'2025-10-17 02:47:45','2025-10-17 02:47:45'),(69,22,5,14,2,'2025-10-17 02:47:45','2025-10-17 02:47:45'),(70,22,6,14,2,'2025-10-17 02:47:45','2025-10-17 02:47:45'),(71,23,1,16,2,'2025-10-17 02:50:06','2025-10-17 02:50:06'),(72,23,2,16,2,'2025-10-17 02:50:06','2025-10-17 02:50:06'),(73,23,4,16,2,'2025-10-17 02:50:06','2025-10-17 02:50:06'),(74,23,5,16,2,'2025-10-17 02:50:06','2025-10-17 02:50:06'),(75,23,6,16,2,'2025-10-17 02:50:06','2025-10-17 02:50:06'),(76,24,17,24,2,'2025-10-17 02:53:11','2025-10-17 02:53:11'),(77,24,17,22,2,'2025-10-17 02:53:11','2025-10-17 02:53:11'),(78,25,11,20,2,'2025-10-17 03:00:39','2025-10-17 03:00:39'),(79,25,28,22,2,'2025-10-17 03:00:39','2025-10-17 03:00:39'),(80,25,11,24,2,'2025-10-17 03:00:39','2025-10-17 03:00:39'),(81,25,28,24,2,'2025-10-17 03:00:39','2025-10-17 03:00:39'),(86,27,11,20,2,'2025-10-17 03:10:14','2025-10-17 03:10:14'),(87,27,21,22,2,'2025-10-17 03:10:14','2025-10-17 03:10:14'),(88,28,24,22,2,'2025-10-17 03:13:37','2025-10-17 03:13:37'),(89,28,24,24,2,'2025-10-17 03:13:37','2025-10-17 03:13:37'),(90,28,24,26,2,'2025-10-17 03:13:37','2025-10-17 03:13:37'),(91,29,12,26,2,'2025-10-17 03:16:17','2025-10-17 03:16:17'),(92,29,12,24,2,'2025-10-17 03:16:17','2025-10-17 03:16:17'),(93,30,13,24,2,'2025-10-17 03:20:41','2025-10-17 03:20:41'),(94,30,13,26,2,'2025-10-17 03:20:41','2025-10-17 03:20:41'),(95,30,13,22,2,'2025-10-17 03:20:41','2025-10-17 03:20:41'),(96,31,11,22,2,'2025-10-17 06:41:32','2025-10-17 06:41:32'),(97,32,12,20,2,'2025-10-17 06:48:26','2025-10-17 06:48:26'),(98,38,1,11,2,'2025-10-17 07:04:16','2025-10-17 07:04:16'),(99,38,2,11,2,'2025-10-17 07:04:16','2025-10-17 07:04:16'),(100,38,3,11,2,'2025-10-17 07:04:16','2025-10-17 07:04:16'),(101,38,4,11,2,'2025-10-17 07:04:16','2025-10-17 07:04:16'),(102,38,5,11,2,'2025-10-17 07:04:16','2025-10-17 07:04:16'),(103,38,6,11,2,'2025-10-17 07:04:16','2025-10-17 07:04:16'),(104,26,11,22,2,'2025-10-17 07:09:52','2025-10-17 07:09:52'),(105,26,11,24,2,'2025-10-17 07:09:52','2025-10-17 07:09:52'),(106,26,21,24,2,'2025-10-17 07:09:52','2025-10-17 07:09:52'),(107,26,21,26,2,'2025-10-17 07:09:52','2025-10-17 07:09:52'),(108,39,30,22,2,'2025-10-17 07:23:40','2025-10-17 07:23:40');
/*!40000 ALTER TABLE `subject_teachings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `grade_level` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `academic_year_id` bigint unsigned DEFAULT NULL,
  `is_core` tinyint(1) NOT NULL DEFAULT '1',
  `credit_hours` int NOT NULL DEFAULT '1',
  `weight` decimal(5,2) NOT NULL DEFAULT '1.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subjects_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'English Language','ENGP','English Language for Primary level','Primary',1,1,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(2,'Mathematics','MATP','Mathematics for Primary level','Primary',1,1,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(3,'Science','SCIP','Integrated Science for Primary level','Primary',1,1,1,1.00,1,'2025-10-15 03:45:17','2025-10-17 01:56:31'),(4,'Social Studies','SOCP','Social Studies for Primary level','Primary',1,1,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(5,'Creative and Technology Studies (CTS)','CTSP','Creative and Technology Studies (CTS) for Primary level','Primary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(6,'Icibemba','ZL','Zambian Languages for Primary level','Primary',2,1,1,1.00,1,'2025-10-15 03:45:17','2025-10-16 05:45:11'),(7,'Physical Education','PHEP','Physical Education for Primary level','Primary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(8,'Religious Education','RELP','Religious Education for Primary level','Primary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(9,'Art','ARTP','Art for Primary level','Primary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(10,'Music','MUSP','Music for Primary level','Primary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(11,'English','ENGS','English for Secondary level','Secondary',1,1,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(12,'Mathematics','MATS','Mathematics for Secondary level','Secondary',1,1,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(13,'Science','SCIS','Science for Secondary level','Secondary',1,1,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(14,'Social Studies','SOCS','Social Studies for Secondary level','Secondary',1,1,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(15,'Physics','PHYS','Physics for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(16,'Chemistry','CHMS','Chemistry for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(17,'Biology','BIOS','Biology for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(18,'Geography','GEOS','Geography for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(19,'History','HISS','History for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(20,'Civic Education','CIVS','Civic Education for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(21,'Religious Education','RELS','Religious Education for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(22,'Physical Education','PHES','Physical Education for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(23,'Computer Studies','COMS','Computer Studies for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(24,'Business Studies','BUSS','Business Studies for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(25,'Accounting','ACCS','Accounting for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(26,'Home Economics','HOMS','Home Economics for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(27,'Art','ARTS','Art for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(28,'Music','MUSS','Music for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(29,'French','FRNS','French for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(30,'Technical Drawing','TEDS','Technical Drawing for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(31,'Agriculture','AGRS','Agriculture for Secondary level','Secondary',1,0,1,1.00,1,'2025-10-15 03:45:17','2025-10-15 03:45:17'),(32,'Pre-mathematics and Science','PMS',NULL,NULL,NULL,1,1,1.00,1,'2025-10-16 04:36:08','2025-10-16 04:36:08'),(33,'Pre-literacy and Language','PLL',NULL,NULL,NULL,1,1,1.00,1,'2025-10-16 04:37:39','2025-10-16 04:37:39'),(34,'Science and Mathematics','SM','Science and Mathematics','Primary',2,1,1,1.00,1,'2025-10-16 05:39:08','2025-10-16 05:39:08');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher_designations`
--

DROP TABLE IF EXISTS `teacher_designations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teacher_designations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `staff_designation_id` bigint unsigned NOT NULL,
  `school_section_id` bigint unsigned DEFAULT NULL,
  `assigned_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teacher_designation_section_unique` (`teacher_id`,`staff_designation_id`,`school_section_id`),
  KEY `teacher_designations_school_section_id_foreign` (`school_section_id`),
  KEY `teacher_designations_teacher_id_is_active_index` (`teacher_id`,`is_active`),
  KEY `teacher_designations_staff_designation_id_is_active_index` (`staff_designation_id`,`is_active`),
  CONSTRAINT `teacher_designations_school_section_id_foreign` FOREIGN KEY (`school_section_id`) REFERENCES `school_sections` (`id`) ON DELETE SET NULL,
  CONSTRAINT `teacher_designations_staff_designation_id_foreign` FOREIGN KEY (`staff_designation_id`) REFERENCES `staff_designations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_designations_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_designations`
--

LOCK TABLES `teacher_designations` WRITE;
/*!40000 ALTER TABLE `teacher_designations` DISABLE KEYS */;
/*!40000 ALTER TABLE `teacher_designations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teachers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `grade_id` bigint unsigned DEFAULT NULL,
  `class_section_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_grade_teacher` tinyint(1) NOT NULL DEFAULT '0',
  `role_id` bigint unsigned NOT NULL DEFAULT '2',
  `school_section_id` bigint unsigned DEFAULT NULL,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nrc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tpin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_branch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qualification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_class_teacher` tinyint(1) NOT NULL DEFAULT '0',
  `administrative_role` enum('none','director','head_teacher','deputy_head_teacher','dean_of_students') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `section_scope` enum('none','primary','secondary','hybrid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `requires_class_assignment` tinyint(1) NOT NULL DEFAULT '1',
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cv_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `police_clearance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `teaching_license` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nrc_copy` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `application_letter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_contract` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biography` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teachers_employee_id_unique` (`employee_id`),
  UNIQUE KEY `teachers_email_unique` (`email`),
  KEY `teachers_school_section_id_index` (`school_section_id`),
  CONSTRAINT `teachers_school_section_id_foreign` FOREIGN KEY (`school_section_id`) REFERENCES `school_sections` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES (14,48,1,1,'Monica Mpoya',1,2,NULL,'TCH-251000001','125478/12/1','889895554',NULL,NULL,NULL,'Advanced Diploma',NULL,'2024-01-08','0968963214','monicampoya772@gmail.com','Chililabombwe',1,1,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:27:54','2025-10-17 07:03:52'),(15,49,2,2,'Gift Zunda',1,2,NULL,'TCH-251000002',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma',NULL,'2024-01-08','0978652310','giftnzunda@gmail.com',NULL,1,1,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:29:58','2025-10-17 07:03:44'),(16,50,3,3,'Memory Chomba',1,2,NULL,'TCH-251000003',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma',NULL,'2024-01-08','0969857412','memorychomba483@gmail.com',NULL,1,1,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:34:48','2025-10-17 07:03:40'),(17,51,4,4,'Musa Doris',1,2,NULL,'TCH-251000004',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma',NULL,'2024-01-08','0774568921','mulengamusa429@gmail.com',NULL,1,1,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:36:24','2025-10-17 07:03:58'),(18,52,5,6,'Musakanya Mutale',1,2,NULL,'TCH-251000005',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma',NULL,'2024-01-08','0975421579','mutalemusakanya944@gmail.com',NULL,1,1,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:39:54','2025-10-17 07:03:19'),(19,53,6,8,'Eunice Kansa',1,2,NULL,'TCH-251000006',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma',NULL,'2024-01-08','0754125478','eunicekansa@gmail.com',NULL,1,1,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:41:54','2025-10-17 07:03:08'),(20,54,7,10,'Sinyangwe Euell',1,2,NULL,'TCH-251000007',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma',NULL,'2024-01-08','0955648695','sinyangweeuell@gmail.com',NULL,1,1,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:44:08','2025-10-17 07:02:34'),(21,55,8,12,'Agness Mukupa',1,2,NULL,'TCH-251000008',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma',NULL,'2021-01-06','0977451278','agnessmukupa2@gmail.com',NULL,1,1,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:45:39','2025-10-17 07:02:59'),(22,56,9,14,'Mubisa Micheal',1,2,NULL,'TCH-251000009',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma',NULL,'2025-06-06','0966524178','mubisamicheal@gamil.com',NULL,1,1,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:47:43','2025-10-17 07:02:55'),(23,57,10,16,'Leonard Kopakopa',1,2,NULL,'TCH-251000010',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma',NULL,'2024-01-01','0954781269','leonardkopakopa@gmail.com',NULL,1,0,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:50:02','2025-10-17 07:02:45'),(24,58,NULL,NULL,'Nkandu Richard',0,2,NULL,'TCH-251000011',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma','Biology','2025-01-01','0768953147','richienkandu@gmail.com',NULL,1,0,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:53:09','2025-10-17 02:53:09'),(25,59,12,NULL,'Quintinoh Chibwe',1,2,NULL,'TCH-251000012',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma','English and Music','2025-10-17','0963582143','quintinohchibwe89@gmail.com',NULL,1,1,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:00:35','2025-10-17 07:04:33'),(26,60,13,NULL,'Silwamba Bruno',1,2,NULL,'TCH-251000013',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma','English and Religous Education','2025-10-17','09553214','silwambabruno88@gmail.com',NULL,1,0,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:05:00','2025-10-17 03:05:00'),(27,61,NULL,NULL,'Bwalya Mulenga',0,2,NULL,'TCH-251000014',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma','English and Religous Education','2024-01-01','0966548972','bwalyamuele1501@gmail.com',NULL,1,0,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:10:12','2025-10-17 03:10:12'),(28,62,NULL,NULL,'Evidence Mulenga',0,2,NULL,'TCH-251000015',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma','Business Studies','2024-01-06','0974862456','evidencem9@gmail.com',NULL,1,0,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:13:34','2025-10-17 03:13:34'),(29,63,15,NULL,'Kabanda Handson',1,2,NULL,'TCH-251000016',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma','Mathematics','2022-01-06','0966857412','kabambahandson7@gmail.com',NULL,1,0,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:16:14','2025-10-17 03:16:15'),(30,64,14,NULL,'Vincent Mulenga',1,2,NULL,'TCH-251000017',NULL,NULL,NULL,NULL,NULL,'Advanced Diploma','Science','2023-01-08','0978564231','vincentmulenga1987@gmail.com',NULL,1,0,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:20:38','2025-10-17 03:20:38'),(31,162,NULL,NULL,'LUBINDA GODWIN',0,2,NULL,'TCH-251000018','188944/63/1',NULL,NULL,NULL,NULL,'Diploma','ENGLSH/BUSINESS STUDIES','2024-01-14','0961629637','llubindagodwin@gmail.com','H1/37 \nKapijimpanga Road Mine Township Area',1,0,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 06:41:21','2025-10-17 06:41:21'),(32,163,12,NULL,'MWABA BRAVINE',1,2,NULL,'TCH-251000019','312019/68/1',NULL,NULL,NULL,NULL,'Degree','MATHEMATICS/CHEMISTRY','2024-01-14','0974944898','bravine.mwaba312019@gmail.com','130 KASAPA ROAD \nKAKOSO SITE AND SERVICE\nCHILILABOMBWE',1,0,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 06:48:15','2025-10-17 06:48:15'),(38,NULL,7,11,'kapelanga mercy',1,2,NULL,'TCH-251000020',NULL,NULL,NULL,NULL,NULL,'Diploma',NULL,'2025-10-17','0979790488','kapelangamercy@gmail.com','Plot 4122 Kakoso south ',1,1,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 07:04:16','2025-10-17 07:05:02'),(39,165,NULL,NULL,'FREDDIE SIMPEMBA',0,2,NULL,'TCH-251000021','169919/63/1',NULL,NULL,'INDO ZAMBIA BANK','CHILILABOMBWE','Advanced Diploma','DESIGN AND TECHNOLOGY','2025-10-17','0979176346','fredsimpemba@gmail.com','W 709 LUBENGELE\nCHILILABOMBWE',1,0,'none','none',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 07:23:37','2025-10-17 07:23:37');
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `terms`
--

DROP TABLE IF EXISTS `terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `terms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `terms`
--

LOCK TABLES `terms` WRITE;
/*!40000 ALTER TABLE `terms` DISABLE KEYS */;
INSERT INTO `terms` VALUES (1,1,'Term 1','2024-01-08','2024-04-05',NULL,0,1,'2025-10-15 03:45:16','2025-10-16 06:47:13'),(2,1,'Term 2','2024-05-06','2024-08-09',NULL,0,0,'2025-10-15 03:45:16','2025-10-16 06:47:13'),(3,1,'Term 3','2024-09-09','2024-12-20',NULL,0,0,'2025-10-15 03:45:16','2025-10-16 06:47:13'),(4,2,'Term 1','2025-01-15','2025-04-30',NULL,0,0,'2025-10-16 06:15:44','2025-10-16 06:47:13'),(5,2,'Term 2','2025-05-01','2025-08-31',NULL,0,0,'2025-10-16 06:46:47','2025-10-16 06:47:13'),(6,2,'Term 3','2025-09-01','2025-12-15',NULL,1,0,'2025-10-16 06:46:47','2025-10-16 06:47:13');
/*!40000 ALTER TABLE `terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_activities`
--

DROP TABLE IF EXISTS `user_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_activities_user_id_foreign` (`user_id`),
  CONSTRAINT `user_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_activities`
--

LOCK TABLES `user_activities` WRITE;
/*!40000 ALTER TABLE `user_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_credentials`
--

DROP TABLE IF EXISTS `user_credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_credentials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `sent_at` datetime DEFAULT NULL,
  `delivery_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sms',
  `is_retrieved` tinyint(1) NOT NULL DEFAULT '0',
  `retrieved_at` datetime DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_credentials_user_id_foreign` (`user_id`),
  CONSTRAINT `user_credentials_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_credentials`
--

LOCK TABLES `user_credentials` WRITE;
/*!40000 ALTER TABLE `user_credentials` DISABLE KEYS */;
INSERT INTO `user_credentials` VALUES (1,32,'mulengablessmore@gmail.com','[312&9khSj$#',1,'2025-10-15 18:02:09','email',0,NULL,'2025-10-22 18:02:06','2025-10-15 18:02:06','2025-10-15 18:02:09',NULL),(3,37,'ben.mwaba@stfrancisofassisi.tech','u|IE1V]$~!',1,'2025-10-16 06:06:59','sms',0,NULL,'2025-10-23 06:06:59','2025-10-16 06:06:59','2025-10-16 06:06:59',NULL),(5,39,'kabambahandson@gmail.com','T9z5%9>Kn>_-',1,'2025-10-16 09:16:29','email',0,NULL,'2025-10-23 09:16:28','2025-10-16 09:16:28','2025-10-16 09:16:29',NULL),(6,40,'ludindagodwin@gmail.com',';Ekk.jD1G{!B',1,'2025-10-16 09:26:32','email',0,NULL,'2025-10-23 09:26:30','2025-10-16 09:26:30','2025-10-16 09:26:32',NULL),(7,41,'kapelangamercy@gmail.com','g<]e0&^\\b7N6',1,'2025-10-16 09:38:21','email',0,NULL,'2025-10-23 09:38:19','2025-10-16 09:38:19','2025-10-16 09:38:21',NULL),(9,43,'teddson.lung\'eenda@stfrancisofassisi.tech','US61iSjv$]',1,'2025-10-16 10:38:32','sms',0,NULL,'2025-10-23 10:38:32','2025-10-16 10:38:32','2025-10-16 10:38:32',NULL),(10,44,'charles.mwaba@student.stfrancisofassisi.tech','[>M{Mc21.8,I',0,NULL,'email_and_sms',0,NULL,'2025-10-23 12:15:48','2025-10-16 12:15:48','2025-10-16 12:15:48',NULL),(11,45,'lydia.bwalya@student.stfrancisofassisi.tech','iOc!m%<4%^b[',0,NULL,'email_and_sms',0,NULL,'2025-10-23 12:15:48','2025-10-16 12:15:48','2025-10-16 12:15:48',NULL),(12,46,'grabrial.lungenda@student.stfrancisofassisi.tech','G1}t:M<S8mf%',0,NULL,'email_and_sms',0,NULL,'2025-10-23 12:15:48','2025-10-16 12:15:48','2025-10-16 12:15:48',NULL),(13,47,'euell.kunda@student.stfrancisofassisi.tech','q}6Qg,Q;Whj3',0,NULL,'email_and_sms',0,NULL,'2025-10-23 12:15:48','2025-10-16 12:15:48','2025-10-16 12:15:48',NULL),(14,48,'monicampoya772@gmail.com','_/i3F,\\,}/0V',1,'2025-10-17 02:27:57','email_and_sms',0,NULL,'2025-10-24 02:27:55','2025-10-17 02:27:55','2025-10-17 02:27:57',NULL),(15,49,'giftnzunda@gmail.com','3(KB!86hpCpE',1,'2025-10-17 02:30:01','email_and_sms',0,NULL,'2025-10-24 02:29:58','2025-10-17 02:29:58','2025-10-17 02:30:01',NULL),(16,50,'memorychomba483@gmail.com','\\G!/Bs7n9z-5',1,'2025-10-17 02:34:53','email_and_sms',0,NULL,'2025-10-24 02:34:48','2025-10-17 02:34:48','2025-10-17 02:34:53',NULL),(17,51,'mulengamusa429@gmail.com','4T%kZV&x>}\\N',1,'2025-10-17 02:36:26','email_and_sms',0,NULL,'2025-10-24 02:36:24','2025-10-17 02:36:24','2025-10-17 02:36:26',NULL),(18,52,'mutalemusakanya944@gmail.com','I^yu^*5S>*9<',1,'2025-10-17 02:39:57','email_and_sms',0,NULL,'2025-10-24 02:39:54','2025-10-17 02:39:54','2025-10-17 02:39:57',NULL),(19,53,'eunicekansa@gmail.com','n*yjI\\UIlK0O',1,'2025-10-17 02:41:56','email_and_sms',0,NULL,'2025-10-24 02:41:54','2025-10-17 02:41:54','2025-10-17 02:41:56',NULL),(20,54,'sinyangweeuell@gmail.com','3g#ydlpm9X$]',1,'2025-10-17 02:44:10','email_and_sms',0,NULL,'2025-10-24 02:44:08','2025-10-17 02:44:08','2025-10-17 02:44:10',NULL),(21,55,'agnessmukupa2@gmail.com','s3lm0%r>Y0(H',1,'2025-10-17 02:45:41','email_and_sms',0,NULL,'2025-10-24 02:45:39','2025-10-17 02:45:39','2025-10-17 02:45:41',NULL),(22,56,'mubisamicheal@gamil.com','qr6Oz1V52c?<',1,'2025-10-17 02:47:45','email_and_sms',0,NULL,'2025-10-24 02:47:43','2025-10-17 02:47:43','2025-10-17 02:47:45',NULL),(23,57,'leonardkopakopa@gmail.com','P[,ep1xXz/5_',1,'2025-10-17 02:50:06','email_and_sms',0,NULL,'2025-10-24 02:50:03','2025-10-17 02:50:03','2025-10-17 02:50:06',NULL),(24,58,'richienkandu@gmail.com',',MMjC!A28&Al',1,'2025-10-17 02:53:11','email_and_sms',0,NULL,'2025-10-24 02:53:09','2025-10-17 02:53:09','2025-10-17 02:53:11',NULL),(25,59,'quintinohchibwe89@gmail.com','QtEOF{*Lw(\\2',1,'2025-10-17 03:00:39','email_and_sms',0,NULL,'2025-10-24 03:00:35','2025-10-17 03:00:35','2025-10-17 03:00:39',NULL),(26,60,'silwambabruno88@gmail.com','?nl!v0_s{0)d',1,'2025-10-17 03:05:03','email_and_sms',0,NULL,'2025-10-24 03:05:00','2025-10-17 03:05:00','2025-10-17 03:05:03',NULL),(27,61,'bwalyamuele1501@gmail.com','1ZjI.Y59LL{7',1,'2025-10-17 03:10:14','email_and_sms',0,NULL,'2025-10-24 03:10:12','2025-10-17 03:10:12','2025-10-17 03:10:14',NULL),(28,62,'evidencem9@gmail.com','uV0;ZQ-$1M&$',1,'2025-10-17 03:13:37','email_and_sms',0,NULL,'2025-10-24 03:13:34','2025-10-17 03:13:34','2025-10-17 03:13:37',NULL),(29,63,'kabambahandson7@gmail.com','!blUb~YS7gj|',1,'2025-10-17 03:16:17','email_and_sms',0,NULL,'2025-10-24 03:16:15','2025-10-17 03:16:15','2025-10-17 03:16:17',NULL),(30,64,'vincentmulenga1987@gmail.com','8#X\\G4$[ukqs',1,'2025-10-17 03:20:41','email_and_sms',0,NULL,'2025-10-24 03:20:38','2025-10-17 03:20:38','2025-10-17 03:20:41',NULL),(31,162,'llubindagodwin@gmail.com','DOTgqd3((3\\W',1,'2025-10-17 06:41:32','email',0,NULL,'2025-10-24 06:41:21','2025-10-17 06:41:21','2025-10-17 06:41:32',NULL),(32,163,'bravine.mwaba312019@gmail.com',']0:j$3Ds6JjN',1,'2025-10-17 06:48:26','email',0,NULL,'2025-10-24 06:48:15','2025-10-17 06:48:15','2025-10-17 06:48:26',NULL),(33,165,'fredsimpemba@gmail.com','0_\\dHfw;uX3#',1,'2025-10-17 07:23:40','email_and_sms',0,NULL,'2025-10-24 07:23:37','2025-10-17 07:23:37','2025-10-17 07:23:40',NULL);
/*!40000 ALTER TABLE `user_credentials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `profile_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`),
  KEY `users_email_phone_username_index` (`email`,`phone`,`username`),
  KEY `users_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'System Administrator','admin@stfrancisofassisi.tech','+260971234567','admin','$2y$12$jg/MQ.TRX8eqKcgfq.PleeyQGhMZI/dPpLBN486qpg.Ss60KSBaM6','active',NULL,NULL,'2025-10-15 03:45:16','2025-10-15 03:45:16',NULL,NULL,NULL,'b0ufwXZ115Oa1yj5MAM9uLjzhD2ga6bqULUIklxUEBQ3lXcrhhNhjCfyPRWT','2025-10-15 03:45:07','2025-10-15 03:45:16',NULL),(2,2,'Chungu','chungu@stfrancisofassisi.tech','+260978006764','chungu','$2y$12$TzSJARX0cRhPcgnVCX7MAuvuWFiYU/Lo7ShxOM9tmQcOJbLELZM.6','active',NULL,NULL,'2025-10-15 03:45:07','2025-10-15 03:45:08',NULL,NULL,NULL,'5baenWLsDi','2025-10-15 03:45:08','2025-10-15 03:45:08',NULL),(3,2,'Zunda','zunda@stfrancisofassisi.tech','+260976982552','zunda','$2y$12$KLVf6Uuooqb05GzXZBNgzeEqYdnW6dL/PZs4OIzI/Derm60NfD0/2','active',NULL,NULL,'2025-10-15 03:45:08','2025-10-15 03:45:08',NULL,NULL,NULL,'yHkTn9eBnB','2025-10-15 03:45:08','2025-10-15 03:45:08',NULL),(4,2,'Constance','constance@stfrancisofassisi.tech','+260976552012','constance','$2y$12$FFL/4JF9nRR0XSaRZiy03ux4Kh7hFySCOvrZp1iDXUkbfDAlDTF3S','active',NULL,NULL,'2025-10-15 03:45:08','2025-10-15 03:45:08',NULL,NULL,NULL,'wKKQcgRxX6','2025-10-15 03:45:08','2025-10-15 03:45:08',NULL),(5,2,'Musa Doris','musa.doris@stfrancisofassisi.tech','+260975645652','musa.doris','$2y$12$uHQdqeTCnyOnhAMTRxqVn.bC84Zj1pervdaPNAQWYBlkRyJqt8kuq','active',NULL,NULL,'2025-10-15 03:45:08','2025-10-15 03:45:08',NULL,NULL,NULL,'xQhvaQNgKu','2025-10-15 03:45:08','2025-10-15 03:45:08',NULL),(7,2,'Eunice Kansa','eunice.kansa@stfrancisofassisi.tech','+260975502777','eunice.kansa','$2y$12$rG12GpxtEyQVIiXSUcdaMOTX3nm/DXjvL1dXEroEN/FjETUDAXPDa','active',NULL,NULL,'2025-10-15 03:45:08','2025-10-15 03:45:09',NULL,NULL,NULL,'qNjFAoYbR2','2025-10-15 03:45:09','2025-10-15 03:45:09',NULL),(8,2,'Euelle Sinyangwe','euelle.sinyangwe@stfrancisofassisi.tech','+260973271709','euelle.sinyangwe','$2y$12$j0S/nXSxVV2n9S9tpN6zj.sFYoRkS76l78SVw794sze7zyucFhFK6','active',NULL,NULL,'2025-10-15 03:45:09','2025-10-15 03:45:09',NULL,NULL,NULL,'wSXmuwuA5z','2025-10-15 03:45:09','2025-10-15 03:45:09',NULL),(9,2,'Mukupa Agness','mukupa.agness@stfrancisofassisi.tech','+260978587119','mukupa.agness','$2y$12$drQzXD24OAfntG1keMWELup3McFYhSHVxe/UPLVDvXD88oqYKSJUS','active',NULL,NULL,'2025-10-15 03:45:09','2025-10-15 03:45:09',NULL,NULL,NULL,'Nj01KVIrII','2025-10-15 03:45:09','2025-10-15 03:45:09',NULL),(10,2,'Mubisa Martin','mubisa.martin@stfrancisofassisi.tech','+260979318499','mubisa.martin','$2y$12$KHXAe/RYnDD3vY30BFC52u/8h.NO8FGqh834Vt7Q3j1uSvrthIE7S','active',NULL,NULL,'2025-10-15 03:45:09','2025-10-15 03:45:09',NULL,NULL,NULL,'HiSBO7ws6m','2025-10-15 03:45:09','2025-10-15 03:45:09',NULL),(11,2,'Kopakopa Leonard','kopakopa.leonard@stfrancisofassisi.tech','+260974998915','kopakopa.leonard','$2y$12$ztimVRF5jPq43mf5T0ty7eIMjpSsHoKkfbzrBCeeTjcOgDdAICsZm','active',NULL,NULL,'2025-10-15 03:45:09','2025-10-15 03:45:09',NULL,NULL,NULL,'g9jjYtfY3n','2025-10-15 03:45:09','2025-10-15 03:45:09',NULL),(12,2,'Kaposhi','kaposhi@stfrancisofassisi.tech','+260971492688','kaposhi','$2y$12$nY54S6ZONhGKbqsSES4qUuQ.i9H6FrfXuOW8wHH1/nidfTFZQQYMW','active',NULL,NULL,'2025-10-15 03:45:09','2025-10-15 03:45:10',NULL,NULL,NULL,'J23m8Pfxpl','2025-10-15 03:45:10','2025-10-15 03:45:10',NULL),(13,2,'Muonda Bwalya','muonda.bwalya@stfrancisofassisi.tech','+260977271647','muonda.bwalya','$2y$12$dMEYLhqjGi3ieuAl5ZBJ8uBLYVhNWK7KuOkLyPlQrddddJ0Uq9d1K','active',NULL,NULL,'2025-10-15 03:45:10','2025-10-15 03:45:10',NULL,NULL,NULL,'S7urVCouFh','2025-10-15 03:45:10','2025-10-15 03:45:10',NULL),(14,2,'Chibwe Quintino','chibwe.quintino@stfrancisofassisi.tech','+260973416178','chibwe.quintino','$2y$12$L.fuOn6ZnHsHYyQ.BX0uou4R4p/CQJ5UZccajpG9ltZptyxByh2RW','active',NULL,NULL,'2025-10-15 03:45:10','2025-10-15 03:45:10',NULL,NULL,NULL,'YjEF9pNqaF','2025-10-15 03:45:10','2025-10-15 03:45:10',NULL),(15,2,'Mwaba Breven','mwaba.breven@stfrancisofassisi.tech','+260975922802','mwaba.breven','$2y$12$qJ.cF1R5j/.tVMzEiANHROzlpfGHB7HdDJZ2qkPdeZT.pA2/R/ZBy','active',NULL,NULL,'2025-10-15 03:45:10','2025-10-15 03:45:10',NULL,NULL,NULL,'zWuKig5mor','2025-10-15 03:45:10','2025-10-15 03:45:10',NULL),(16,2,'Sintomba Freddy','sintomba.freddy@stfrancisofassisi.tech','+260976148498','sintomba.freddy','$2y$12$DaoXf4/ycSYfbwd/lYDPpuKccVT8qGMPjHA7vZzoq8ngheZ4nGI.m','active',NULL,NULL,'2025-10-15 03:45:10','2025-10-15 03:45:10',NULL,NULL,NULL,'GiDHUIQkKP','2025-10-15 03:45:10','2025-10-15 03:45:10',NULL),(17,2,'Mulenga Vincent','mulenga.vincent@stfrancisofassisi.tech','+260974709149','mulenga.vincent','$2y$12$uXrXDj.i5o3Yo1EcgXkaW.dS9dR9Jc9fuyAg.cmME0LVZ07Fmqa1i','active',NULL,NULL,'2025-10-15 03:45:10','2025-10-15 03:45:11',NULL,NULL,NULL,'5hw3UTQobp','2025-10-15 03:45:11','2025-10-15 03:45:11',NULL),(18,2,'Bwalya Sylvester','bwalya.sylvester@stfrancisofassisi.tech','+260974335361','bwalya.sylvester','$2y$12$9njTlPK6YZgga7egPHGPzet9LDorVBsg1ayo1hCrYgxxAZjt6MEpG','active',NULL,NULL,'2025-10-15 03:45:11','2025-10-15 03:45:11',NULL,NULL,NULL,'13V5ETiBQz','2025-10-15 03:45:11','2025-10-15 03:45:11',NULL),(19,2,'Singongo Bruce','singongo.bruce@stfrancisofassisi.tech','+260979416886','singongo.bruce','$2y$12$lJdNSyuBn9IUMgB8FxNh7uUU0HLaPAy03q9QBWChq7oRU7lG8g59W','active',NULL,NULL,'2025-10-15 03:45:11','2025-10-15 03:45:11',NULL,NULL,NULL,'W3Ep4TkaAz','2025-10-15 03:45:11','2025-10-15 03:45:11',NULL),(20,2,'Mercy Kapelenga','mercy.kapelenga@stfrancisofassisi.tech','+260975634436','mercy.kapelenga','$2y$12$rKx3L9TgQa/a7f2vcWvqaewj/h5xyIgwwJfCBp.bSm88Rr7HYXApe','active',NULL,NULL,'2025-10-15 03:45:11','2025-10-15 03:45:11',NULL,NULL,NULL,'PBdf1CgHLA','2025-10-15 03:45:11','2025-10-15 03:45:11',NULL),(21,2,'Sylvester Lupando','sylvester.lupando@stfrancisofassisi.tech','+260972865891','sylvester.lupando','$2y$12$2TVezFue./38jGYaBmZjXeIc62uPBQwFyoV58.1XutyRwu4nV9wam','active',NULL,NULL,'2025-10-15 03:45:11','2025-10-15 03:45:11',NULL,NULL,NULL,'iUu0J2YgMt','2025-10-15 03:45:11','2025-10-15 03:45:11',NULL),(22,2,'Tiza Nkhomo','tiza.nkhomo@stfrancisofassisi.tech','+260977728071','tiza.nkhomo','$2y$12$0JN7FYAoYCRsVtQ9SdRq6upXc9MFP58sLCXwyMCpy3HopyFxpeMrS','active',NULL,NULL,'2025-10-15 03:45:11','2025-10-15 03:45:12',NULL,NULL,NULL,'W5VCMsNOOR','2025-10-15 03:45:12','2025-10-15 03:45:12',NULL),(23,3,'Banda Daka','banda.daka.student@stfrancisofassisi.tech',NULL,'banda.daka','$2y$12$J07aXk6.NT5RB/nNC9SOieogRsIPLH0W88XLWsPh/k4L5nGFJQ0fS','active',NULL,NULL,'2025-10-15 03:45:12',NULL,NULL,NULL,NULL,'dBxvO0vBfi','2025-10-15 03:45:12','2025-10-15 03:45:12',NULL),(32,2,'Blessmore Mutale','mulengablessmore@gmail.com','260975020473',NULL,'$2y$12$ZOEV1rAkX/TkE06hC7ACr.AWqdAPS5rb1dOpXoaNFRDdf48jqIWs6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'8YcFGxQD274Ulm2eXwhCcpYgNesLtVC380aH5YolYuJ7COOqwgd6x9Xcxaj2','2025-10-15 18:02:06','2025-10-15 18:02:06',NULL),(37,4,'Ben Mwaba','ben@gmail.com','260969893182','ben.mwaba@stfrancisofassisi.tech','$2y$12$VIUSq1CbW1oSfAiHz6L3MOM3S7e9FN8vYkpWns5lpxoyUy4.HmUcS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'TbSeMIc53zMJlW6mrzBW3ywTBQUbCzrMA7SE1HTGlChDIdIukiYnoHQYEJdD','2025-10-16 06:06:58','2025-10-16 11:16:35',NULL),(39,15,'Kabamba Handson','kabambahandson@gmail.com','0965102620',NULL,'$2y$12$JFJTiN64qTkUp6gBaViX8ubaq19X5fCFU7MjTndGoHi9YRbp9QB8e','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-16 09:16:28','2025-10-16 09:16:28',NULL),(40,2,'Ludinda Godwin','ludindagodwin@gmail.com','0967308940',NULL,'$2y$12$8RxAYw4FpkrLEWRmn3LLYeiTzy6nt22J3vR3z7BYUmqO0/ibsezdu','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-16 09:26:30','2025-10-16 09:26:30',NULL),(41,12,'Kapelanga L Mercy','kapelangamercy@gmail.com','0968562371',NULL,'$2y$12$s7IVB7koVezjeNLJicSNJuYyy9Y/nAIgVFn5oKABrIDzTkv2ZJB5O','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-16 09:38:19','2025-10-16 09:38:19',NULL),(43,4,'Teddson Lung\'eenda',NULL,'0964443502','teddson.lung\'eenda@stfrancisofassisi.tech','$2y$12$UVunoWN1NvDEZn.5b.wp6.PedvjtwAe1yvfW3qxQDEw1SGGRlMNv.','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-16 10:38:31','2025-10-16 11:16:58',NULL),(44,3,'Michael Sichone','charles.mwaba@student.stfrancisofassisi.tech',NULL,NULL,'$2y$12$TBQtv4VVhOFjoQ2qB.xW2OznAcKSHPzDnngoSvwS7/wA4CXFwgcYe','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-16 12:15:48','2025-10-17 03:50:13',NULL),(45,3,'Mary Phiri','lydia.bwalya@student.stfrancisofassisi.tech',NULL,NULL,'$2y$12$w9zzbL2Wx66VH/Tw8XemB.bntduAm2SDDbPmlfM9iGr3UzQUHhV5y','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-16 12:15:48','2025-10-17 03:50:13',NULL),(46,3,'Ruth Ng\'oma','grabrial.lungenda@student.stfrancisofassisi.tech',NULL,NULL,'$2y$12$oOHochr1CPhP.D.AeWyHEeGlI6FY4w1M2qdCqa7whf66oL7w6cvQi','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-16 12:15:48','2025-10-17 03:50:13',NULL),(47,3,'Ruth Mumba','euell.kunda@student.stfrancisofassisi.tech',NULL,NULL,'$2y$12$DkGBKWHEFmEu/fGdPnlTwOWtjYwh6yfxhxKvkCIoQg6X67YnojyYS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-16 12:15:48','2025-10-17 03:50:13',NULL),(48,2,'Monica Mpoya','monicampoya772@gmail.com',NULL,NULL,'$2y$12$R44na96zE8aOSSNKOUMqQ.up52zcB2DcA3v58CSoMiFlk7z8CwIuS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:27:55','2025-10-17 06:54:38',NULL),(49,2,'Gift Zunda','giftnzunda@gmail.com',NULL,NULL,'$2y$12$9uCj7.zDqL/a5faXqmhjWO/R2vufamb0nctjxGEGaWsoEu/PVstxK','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'en2U5hBNsrkZRFhR1wXhvkoTIWelqidLSvkHTMmxKk7W7gEn65e4gJ2Uy0cG','2025-10-17 02:29:58','2025-10-17 06:54:38',NULL),(50,2,'Memory Chomba','memorychomba483@gmail.com',NULL,NULL,'$2y$12$XcCkaMckDsO0pYWLxLznMOOUW9IoNros5.XtHL4QljUs/dEAXs6GC','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:34:48','2025-10-17 06:54:38',NULL),(51,2,'Musa Doris','mulengamusa429@gmail.com',NULL,NULL,'$2y$12$f/m9f6Yc1SF/eWd4RKRuVOCgblpvciCLBBoYiGshF0KwBlCK0S1gC','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:36:24','2025-10-17 06:54:39',NULL),(52,2,'Musakanya Mutale','mutalemusakanya944@gmail.com',NULL,NULL,'$2y$12$qV0yoeyyxSUWjgAWv4oNZOyN6pcAjfhf.ZrsBor/lzNuY8THKCCgy','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'T3HyeiGBUNUQNLYEaQApFYztRL6xmkhMB8JGwUxYwEg4sux6M2FjLUDkRh72','2025-10-17 02:39:54','2025-10-17 07:28:31',NULL),(53,2,'Eunice Kansa','eunicekansa@gmail.com',NULL,NULL,'$2y$12$amr.OKj17AQGhSlrjW0I2upM3bxdx15DD6W6VsqHZ0rq6PYyLV3dq','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:41:54','2025-10-17 06:54:39',NULL),(54,2,'Sinyangwe Euell','sinyangweeuell@gmail.com',NULL,NULL,'$2y$12$QiJOWJDh2zqQEDJN2HOjJeu01cXeypur0xRfx7DBhja.1LdZfJLmC','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:44:08','2025-10-17 06:54:39',NULL),(55,2,'Agness Mukupa','agnessmukupa2@gmail.com',NULL,NULL,'$2y$12$7BZvZpWq7W3urz2Peuw1bO.ssrkH2nkNBkhK4monsDz4ooON.drT.','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:45:39','2025-10-17 06:54:40',NULL),(56,2,'Mubisa Micheal','mubisamicheal@gamil.com',NULL,NULL,'$2y$12$c.vY26qsTTOMYGa5qFN0SO5wUZ7vBVY2oCJbS335iKBtRx/e1CMfu','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:47:43','2025-10-17 06:54:40',NULL),(57,2,'Leonard Kopakopa','leonardkopakopa@gmail.com',NULL,NULL,'$2y$12$1D5IBX7IpMrVzoLtUqUBwuD6cp2kaNeixiJ450LQnE2Zehz8GzrJm','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'R3RS66sO0ZdNJSGBSbZHNaWr2pb61KmHk0jB2Mz4ccso5eljZgdkJBNua3jN','2025-10-17 02:50:03','2025-10-17 06:54:40',NULL),(58,2,'Nkandu Richard','richienkandu@gmail.com',NULL,NULL,'$2y$12$vGNJLeqrxD5JyiNl6Fu./ubGB5YitigtgTWAfzCw5L5QOXfTIOlX6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 02:53:09','2025-10-17 06:54:40',NULL),(59,2,'Quintinoh Chibwe','quintinohchibwe89@gmail.com',NULL,NULL,'$2y$12$x1GrqhZ9hfaWqHmTGH0FJultqyWmiJ79lG9yckjjzoDHtMIamatam','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:00:35','2025-10-17 06:54:40',NULL),(60,2,'Silwamba Bruno','silwambabruno88@gmail.com',NULL,NULL,'$2y$12$IPYmk45NbxNZ8XHUq2abSezGjlzAbGTxMZU5UowOwJ0I9QJp5ehzy','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:05:00','2025-10-17 06:54:41',NULL),(61,2,'Bwalya Mulenga','bwalyamuele1501@gmail.com',NULL,NULL,'$2y$12$bjsEq.7Z6uikCqUcnUxlROr/9FJzdOhFwnoBnBqvR9gXi14gho/p2','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:10:12','2025-10-17 06:54:41',NULL),(62,2,'Evidence Mulenga','evidencem9@gmail.com',NULL,NULL,'$2y$12$T1bjQ2B3nTsSnOnjCxAkhO0D6/1uKZ7tX72I4ORJxJEweaG603.0q','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:13:34','2025-10-17 06:54:41',NULL),(63,2,'Kabanda Handson','kabambahandson7@gmail.com',NULL,NULL,'$2y$12$LwZM8bq5qWNAujXn.om9nurj/iu.pBmNvzvztQG.U1ethSFByxaB2','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:16:15','2025-10-17 06:54:41',NULL),(64,2,'Vincent Mulenga','vincentmulenga1987@gmail.com',NULL,NULL,'$2y$12$Y1JuEopPG9q7W3f2RWvruebVG72UGV3cFOEEItA0UcKfALsV2OSkq','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'HLcNgeBSlfexh71c73yD7ImGQOxSNJC0PvEf33yOCqZBuo3tdmtJ74rumjMf','2025-10-17 03:20:38','2025-10-17 06:54:41',NULL),(65,3,'Daniel Mwale','5@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:07','2025-10-17 03:50:13',NULL),(66,3,'Benjamin Mumba','6@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(67,3,'Elizabeth Chiluba','7@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(68,3,'Ruth Sichone','8@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(69,3,'Benjamin Sakala','9@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(70,3,'Joseph Daka','10@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(71,3,'Esther Mumba','11@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(72,3,'Elizabeth Banda','12@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(73,3,'Ruth Ng\'oma','13@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(74,3,'Benjamin Banda','14@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(75,3,'Hannah Ng\'oma','15@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(76,3,'Michael Chiluba','16@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(77,3,'Rebecca Sakala','17@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(78,3,'Sarah Zulu','18@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(79,3,'Isaac Tembo','19@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(80,3,'Rebecca Kasonde','20@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(81,3,'Joseph Sakala','21@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(82,3,'Rebecca Chiluba','22@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(83,3,'Rebecca Ng\'oma','23@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(84,3,'Hannah Mulenga','24@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:13',NULL),(85,3,'Grace Kasonde','25@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(86,3,'John Bwalya','26@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(87,3,'Elizabeth Mumba','27@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(88,3,'Rachel Kunda','28@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(89,3,'John Mwale','29@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(90,3,'James Sichone','30@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(91,3,'Ruth Chiluba','31@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(92,3,'Ruth Kunda','32@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(93,3,'Rachel Zulu','33@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(94,3,'Esther Sichone','34@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(95,3,'Ruth Mutale','35@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(96,3,'Esther Mutale','36@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(97,3,'Rebecca Banda','37@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(98,3,'Samuel Zulu','38@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(99,3,'David Mutale','39@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(100,3,'John Lungu','40@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(101,3,'Michael Ng\'oma','41@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(102,3,'James Mwanza','42@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(103,3,'Rebecca Mumba','43@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(104,3,'Joseph Mulenga','44@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(105,3,'Rachel Nyirenda','45@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(106,3,'John Mulenga','46@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(107,3,'Hannah Phiri','47@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(108,3,'Ruth Bwalya','48@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(109,3,'Grace Mwale','49@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(110,3,'Mary Bwalya','50@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(111,3,'David Sichone','51@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(112,3,'James Mwale','52@student.com',NULL,NULL,'$2y$12$lHEXoaKlox4r31q.Wff8Deqj6PW7eTYf9lhUA3jvcrsUvAmI2IBkS','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:08','2025-10-17 03:50:14',NULL),(113,4,'Lydia Bwalya Grace','lydia@gmail.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:16','2025-10-17 03:45:16',NULL),(114,4,'John Banda','johnbanda1@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(115,4,'Grace Phiri','gracephiri2@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(116,4,'Sarah Mwanza','sarahmwanza3@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(117,4,'John Banda','johnbanda4@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(118,4,'Grace Phiri','gracephiri5@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(119,4,'Sarah Mwanza','sarahmwanza6@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(120,4,'John Banda','johnbanda7@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(121,4,'Grace Phiri','gracephiri8@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(122,4,'Sarah Mwanza','sarahmwanza9@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(123,4,'John Banda','johnbanda10@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(124,4,'Grace Phiri','gracephiri11@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(125,4,'Sarah Mwanza','sarahmwanza12@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(126,4,'John Banda','johnbanda13@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(127,4,'Grace Phiri','gracephiri14@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(128,4,'Sarah Mwanza','sarahmwanza15@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(129,4,'John Banda','johnbanda16@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(130,4,'Grace Phiri','gracephiri17@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(131,4,'Sarah Mwanza','sarahmwanza18@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(132,4,'John Banda','johnbanda19@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(133,4,'Grace Phiri','gracephiri20@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(134,4,'Sarah Mwanza','sarahmwanza21@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(135,4,'John Banda','johnbanda22@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(136,4,'Grace Phiri','gracephiri23@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(137,4,'Sarah Mwanza','sarahmwanza24@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(138,4,'John Banda','johnbanda25@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(139,4,'Grace Phiri','gracephiri26@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(140,4,'Sarah Mwanza','sarahmwanza27@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(141,4,'John Banda','johnbanda28@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(142,4,'Grace Phiri','gracephiri29@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(143,4,'Sarah Mwanza','sarahmwanza30@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(144,4,'John Banda','johnbanda31@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(145,4,'Grace Phiri','gracephiri32@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(146,4,'Sarah Mwanza','sarahmwanza33@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(147,4,'John Banda','johnbanda34@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(148,4,'Grace Phiri','gracephiri35@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(149,4,'Sarah Mwanza','sarahmwanza36@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(150,4,'John Banda','johnbanda37@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(151,4,'Grace Phiri','gracephiri38@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(152,4,'Sarah Mwanza','sarahmwanza39@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(153,4,'John Banda','johnbanda40@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(154,4,'Grace Phiri','gracephiri41@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(155,4,'Sarah Mwanza','sarahmwanza42@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(156,4,'John Banda','johnbanda43@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(157,4,'Grace Phiri','gracephiri44@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(158,4,'Sarah Mwanza','sarahmwanza45@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(159,4,'John Banda','johnbanda46@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(160,4,'Grace Phiri','gracephiri47@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(161,4,'Sarah Mwanza','sarahmwanza48@parent.com',NULL,NULL,'$2y$12$6wc/eKDpEuXGd24R3ITnfuy6DZzfaFmlucoM4YNRNYeBsEM.4UCV6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 03:45:17','2025-10-17 03:45:17',NULL),(162,2,'LUBINDA GODWIN','llubindagodwin@gmail.com',NULL,NULL,'$2y$12$gyzdBDcqrx1G0wIo5PugFOQ.6K1MuD/AO5sv0610TdEHgTLLEC0H6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 06:41:21','2025-10-17 06:54:42',NULL),(163,2,'MWABA BRAVINE','bravine.mwaba312019@gmail.com',NULL,NULL,'$2y$12$zSFdZV5bw3chiitEjH/YvebvBJk6vHB.ZV8o9WZZHhzR0tMYhkoH6','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 06:48:15','2025-10-17 06:54:42',NULL),(165,2,'FREDDIE SIMPEMBA','fredsimpemba@gmail.com',NULL,NULL,'$2y$12$Qy7gocLtCK7/YMEA.1OohORI838FJxKQGHricPTY7/DcRMajGdc46','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-17 07:23:37','2025-10-17 07:23:37',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-10  4:30:39
