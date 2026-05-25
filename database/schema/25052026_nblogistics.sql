/*
SQLyog Community
MySQL - 8.0.30 : Database - nblogistics
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `cache` */

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache` */

insert  into `cache`(`key`,`value`,`expiration`) values 
('laravel-cache-356a192b7913b04c54574d18c28d46e6395428ab','i:1;',1779607378),
('laravel-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer','i:1779607378;',1779607378);

/*Table structure for table `cache_locks` */

DROP TABLE IF EXISTS `cache_locks`;

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache_locks` */

/*Table structure for table `driver_attendances` */

DROP TABLE IF EXISTS `driver_attendances`;

CREATE TABLE `driver_attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` bigint unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','half_day','absent','holiday') NOT NULL DEFAULT 'present',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `driver_attendance_unique` (`driver_id`,`attendance_date`),
  KEY `driver_attendances_attendance_date_index` (`attendance_date`),
  KEY `driver_attendances_status_index` (`status`),
  CONSTRAINT `driver_attendances_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `driver_attendances` */

insert  into `driver_attendances`(`id`,`driver_id`,`attendance_date`,`status`,`created_at`,`updated_at`) values 
(1,2,'2026-05-01','present','2026-05-22 18:55:34','2026-05-22 18:55:34'),
(7,2,'2026-05-23','present','2026-05-23 00:42:09','2026-05-23 00:42:09'),
(8,2,'2026-05-22','present','2026-05-23 00:42:13','2026-05-23 00:42:13'),
(9,2,'2026-05-21','present','2026-05-23 00:42:14','2026-05-23 00:42:14'),
(10,2,'2026-05-10','present','2026-05-23 00:56:37','2026-05-23 00:56:37'),
(11,2,'2026-05-07','present','2026-05-23 00:56:40','2026-05-23 00:56:40'),
(12,3,'2026-05-01','present','2026-05-23 01:20:14','2026-05-23 01:20:14'),
(13,3,'2026-05-02','present','2026-05-23 01:20:15','2026-05-23 01:20:15'),
(14,3,'2026-05-03','present','2026-05-23 01:20:15','2026-05-23 01:20:15'),
(15,3,'2026-05-04','present','2026-05-23 01:20:17','2026-05-23 01:20:17'),
(16,3,'2026-05-05','present','2026-05-23 01:20:17','2026-05-23 01:20:17'),
(17,3,'2026-05-06','present','2026-05-23 01:20:18','2026-05-23 01:20:18'),
(18,3,'2026-05-07','present','2026-05-23 01:20:20','2026-05-23 01:20:20'),
(19,3,'2026-05-08','present','2026-05-23 01:20:21','2026-05-23 01:20:21'),
(20,3,'2026-05-09','present','2026-05-23 01:20:22','2026-05-23 01:20:22'),
(21,3,'2026-05-10','present','2026-05-23 01:20:22','2026-05-23 01:20:22'),
(22,3,'2026-05-11','present','2026-05-23 01:20:23','2026-05-23 01:20:23'),
(23,3,'2026-05-12','present','2026-05-23 01:20:24','2026-05-23 01:20:24'),
(24,3,'2026-05-13','present','2026-05-23 01:20:24','2026-05-23 01:20:24'),
(25,3,'2026-05-14','present','2026-05-23 01:20:25','2026-05-23 01:20:25'),
(26,3,'2026-05-15','present','2026-05-23 01:20:26','2026-05-23 01:20:26'),
(27,3,'2026-05-16','present','2026-05-23 01:20:26','2026-05-23 01:20:26'),
(28,3,'2026-05-17','present','2026-05-23 01:20:27','2026-05-23 01:20:27'),
(29,3,'2026-05-18','present','2026-05-23 01:20:29','2026-05-23 01:20:29'),
(35,2,'2026-05-02','present','2026-05-23 15:30:24','2026-05-23 15:30:24'),
(36,2,'2026-05-04','present','2026-05-23 15:30:25','2026-05-23 15:30:25'),
(37,2,'2026-05-05','present','2026-05-23 15:30:26','2026-05-23 15:30:26'),
(38,2,'2026-05-14','present','2026-05-23 15:30:28','2026-05-23 15:30:28'),
(39,2,'2026-05-15','present','2026-05-23 15:30:29','2026-05-23 15:30:29'),
(40,2,'2026-05-18','present','2026-05-23 15:30:30','2026-05-23 15:30:30'),
(41,3,'2026-05-21','present','2026-05-23 15:30:32','2026-05-23 15:30:32'),
(42,3,'2026-05-23','present','2026-05-23 15:30:33','2026-05-23 15:30:33'),
(43,4,'2026-05-01','present','2026-05-23 16:55:18','2026-05-23 16:55:18'),
(44,4,'2026-05-02','present','2026-05-23 16:55:19','2026-05-23 16:55:19'),
(45,4,'2026-05-03','present','2026-05-23 16:55:19','2026-05-23 16:55:19'),
(46,4,'2026-05-04','present','2026-05-23 16:55:20','2026-05-23 16:55:20'),
(47,4,'2026-05-05','present','2026-05-23 16:55:20','2026-05-23 16:55:20'),
(48,4,'2026-05-06','present','2026-05-23 16:55:21','2026-05-23 16:55:21'),
(49,4,'2026-05-07','present','2026-05-23 16:55:21','2026-05-23 16:55:21'),
(50,4,'2026-05-13','present','2026-05-23 16:55:22','2026-05-23 16:55:22'),
(51,4,'2026-05-15','present','2026-05-23 16:55:22','2026-05-23 16:55:22'),
(52,4,'2026-05-16','present','2026-05-23 16:55:23','2026-05-23 16:55:23'),
(53,4,'2026-05-17','present','2026-05-23 16:55:23','2026-05-23 16:55:23'),
(54,4,'2026-05-23','present','2026-05-23 16:55:24','2026-05-23 16:55:24'),
(55,4,'2026-05-21','present','2026-05-23 16:55:28','2026-05-23 16:55:28'),
(56,4,'2026-05-20','present','2026-05-23 16:55:28','2026-05-23 16:55:28');

/*Table structure for table `driver_salary_records` */

DROP TABLE IF EXISTS `driver_salary_records`;

CREATE TABLE `driver_salary_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` bigint unsigned NOT NULL,
  `month` char(7) NOT NULL,
  `total_days` int NOT NULL DEFAULT '0',
  `present_days` int NOT NULL DEFAULT '0',
  `absent_days` int NOT NULL DEFAULT '0',
  `half_days` int NOT NULL DEFAULT '0',
  `gross_salary` decimal(15,2) NOT NULL DEFAULT '0.00',
  `advance_deduction` decimal(15,2) NOT NULL DEFAULT '0.00',
  `net_salary` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('PENDING','PAID') NOT NULL DEFAULT 'PENDING',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `driver_salary_records_driver_id_month_unique` (`driver_id`,`month`),
  KEY `driver_salary_records_month_index` (`month`),
  CONSTRAINT `driver_salary_records_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `driver_salary_records` */

insert  into `driver_salary_records`(`id`,`driver_id`,`month`,`total_days`,`present_days`,`absent_days`,`half_days`,`gross_salary`,`advance_deduction`,`net_salary`,`status`,`created_at`,`updated_at`) values 
(1,2,'2026-05',31,6,25,0,0.00,0.00,0.00,'PAID','2026-05-23 01:14:05','2026-05-23 01:14:39'),
(2,3,'2026-05',31,20,11,0,9677.42,0.00,9677.42,'PENDING','2026-05-23 01:24:43','2026-05-23 15:33:30'),
(3,1,'2026-05',31,0,31,0,0.00,0.00,0.00,'PENDING','2026-05-23 15:31:20','2026-05-23 15:31:23'),
(4,4,'2026-05',31,14,17,0,11290.32,0.00,11290.32,'PENDING','2026-05-23 16:55:35','2026-05-23 16:55:35');

/*Table structure for table `drivers` */

DROP TABLE IF EXISTS `drivers`;

CREATE TABLE `drivers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'available',
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `base_salary` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drivers_email_unique` (`email`),
  KEY `drivers_name_index` (`name`),
  KEY `drivers_email_index` (`email`),
  KEY `drivers_mobile_index` (`mobile`),
  KEY `drivers_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `drivers` */

insert  into `drivers`(`id`,`name`,`email`,`mobile`,`status`,`opening_balance`,`base_salary`,`created_at`,`updated_at`) values 
(1,'Hardik Thanki',NULL,'5454545451','available',0.00,0.00,'2026-03-31 17:46:05','2026-05-24 13:06:41'),
(2,'Arshad pappu',NULL,'9876544321','not_available',15000.00,20000.00,'2026-04-20 18:58:05','2026-05-23 15:30:58'),
(3,'Chaim Jennings',NULL,'9898982424','available',5000.00,15000.00,'2026-05-23 01:19:49','2026-05-23 01:19:49'),
(4,'Test Last',NULL,'9824228575','available',0.00,25000.00,'2026-05-23 16:55:09','2026-05-23 16:55:09');

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

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

/*Data for the table `failed_jobs` */

/*Table structure for table `job_batches` */

DROP TABLE IF EXISTS `job_batches`;

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `job_batches` */

/*Table structure for table `jobs` */

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `jobs` */

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'2026_04_14_183403_create_driver_salary_records_table',1),
(2,'2026_04_21_120000_add_manual_entry_columns_to_trips_table',2),
(3,'2026_04_22_120000_make_trip_foreign_keys_nullable',3),
(4,'2026_04_30_173346_add_truck_and_category_to_trip_expenses_table',4),
(5,'2026_05_22_183522_create_driver_attendances_table',5),
(6,'2026_05_23_005232_add_base_salary_to_drivers_table',6);

/*Table structure for table `parties` */

DROP TABLE IF EXISTS `parties`;

CREATE TABLE `parties` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `opening_balance` float(15,2) DEFAULT '0.00',
  `opening_balance_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `parties` */

insert  into `parties`(`id`,`name`,`email`,`mobile`,`address`,`status`,`opening_balance`,`opening_balance_date`,`created_at`,`created_by`,`updated_at`,`updated_by`,`deleted_at`,`deleted_by`) values 
(1,'Abhishek Rajput',NULL,'5454545454',NULL,'active',500.00,'2026-01-16 00:00:00','2026-01-18 18:04:06',NULL,'2026-01-18 18:30:02',NULL,NULL,NULL),
(2,'Jamnadas Muswala',NULL,'9876543211',NULL,'active',20.00,'2026-04-21 00:00:00','2026-04-20 19:01:16',NULL,'2026-04-20 19:01:16',NULL,NULL,NULL),
(3,'Narbheram',NULL,'0987654321',NULL,'active',NULL,NULL,'2026-04-20 19:08:36',NULL,'2026-04-20 19:08:36',NULL,NULL,NULL),
(4,'Harihar ind',NULL,'9876543212',NULL,'active',NULL,NULL,'2026-04-20 19:09:12',NULL,'2026-04-20 19:09:12',NULL,NULL,NULL),
(5,'Haley Sanders',NULL,'9988776655',NULL,'active',NULL,NULL,'2026-04-25 04:59:17',NULL,'2026-04-25 04:59:17',NULL,NULL,NULL);

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_reset_tokens` */

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sessions` */

insert  into `sessions`(`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) values 
('JIq8aGpnVcUfdaoo4TZGirrABX1BjJBsSSbAbCMe',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiajBSVDV4a0hTMVp4RERuQ1V1TUpxYVdja2JyUUVLY2d5OEU4Tm1HcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sYy5uYmxvZ2lzdGljcy5jb20vdHJpcHMiO3M6NToicm91dGUiO3M6MTE6InRyaXBzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1779608433),
('kl035myd27tAZgScqcSG4Z6U2KobYXsnvnxgNivn',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoibTFnYXVUYWNFUFEwZHRBVlBGMmJDeTYwVWRJZWpsQ2xVQmc0Znp3ciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly9sYy5uYmxvZ2lzdGljcy5jb20iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1779694511),
('XlE0kQpizhr9oSmjupaJcmdI81Ea3y23d1HJiHe5',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoidlU3TmludGh4T0NkemtjczBKM3VQc2NLMENoeDNUMUt5SzVwcTYwdSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly9sYy5uYmxvZ2lzdGljcy5jb20vYnVpbHR5LzUiO3M6NToicm91dGUiO3M6MTE6ImJ1aWx0eS5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1779728225);

/*Table structure for table `trip_advances` */

DROP TABLE IF EXISTS `trip_advances`;

CREATE TABLE `trip_advances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `trip_id` bigint unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','cheque','upi','bank_transfer','fuel','others') NOT NULL,
  `payment_date` date NOT NULL,
  `received_by_driver` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `trip_advances` */

insert  into `trip_advances`(`id`,`trip_id`,`amount`,`payment_method`,`payment_date`,`received_by_driver`,`notes`,`created_at`,`updated_at`) values 
(3,1,100.00,'cash','2026-03-31',0,'','2026-04-01 16:50:56','2026-04-01 16:53:13');

/*Table structure for table `trip_charges` */

DROP TABLE IF EXISTS `trip_charges`;

CREATE TABLE `trip_charges` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `trip_id` int DEFAULT NULL,
  `charge_direction` enum('add_to_bill','reduce_from_bill') DEFAULT NULL,
  `charge_type` varchar(250) DEFAULT NULL,
  `amount` float(10,2) DEFAULT '0.00',
  `date` datetime DEFAULT NULL,
  `notes` text,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `trip_charges` */

insert  into `trip_charges`(`id`,`trip_id`,`charge_direction`,`charge_type`,`amount`,`date`,`notes`,`created_at`,`created_by`,`updated_at`,`updated_by`,`deleted_at`,`deleted_by`) values 
(2,1,'add_to_bill','loading_unloading',3000.00,'2026-03-31 00:00:00','','2026-03-31 17:38:40',NULL,'2026-03-31 17:38:40',NULL,NULL,NULL),
(3,1,'reduce_from_bill','loading_unloading',2000.00,'2026-03-31 00:00:00','','2026-04-01 16:21:31',NULL,'2026-04-01 16:38:19',NULL,NULL,NULL),
(4,3,'add_to_bill','parking',500.00,'2026-04-22 00:00:00','haksjdhaskjd','2026-04-22 17:07:30',NULL,'2026-04-22 17:07:30',NULL,NULL,NULL),
(5,5,'add_to_bill','others',1000.00,'2026-05-23 00:00:00','Diesel','2026-05-23 17:02:14',NULL,'2026-05-23 17:02:14',NULL,NULL,NULL);

/*Table structure for table `trip_expenses` */

DROP TABLE IF EXISTS `trip_expenses`;

CREATE TABLE `trip_expenses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `expense_category` enum('trip','truck','office') NOT NULL DEFAULT 'trip',
  `truck_id` bigint unsigned DEFAULT NULL,
  `trip_id` int unsigned DEFAULT NULL,
  `payment_mode` enum('cash','credit','paid_by_driver','online') DEFAULT NULL,
  `expense_type` varchar(250) DEFAULT NULL,
  `amount` float(10,2) DEFAULT '0.00',
  `expense_date` datetime DEFAULT NULL,
  `add_to_party_bill` tinyint(1) DEFAULT '0',
  `notes` text,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `trip_expenses` */

insert  into `trip_expenses`(`id`,`expense_category`,`truck_id`,`trip_id`,`payment_mode`,`expense_type`,`amount`,`expense_date`,`add_to_party_bill`,`notes`,`created_at`,`created_by`,`updated_at`,`updated_by`,`deleted_at`,`deleted_by`) values 
(3,'trip',NULL,1,'cash','toll',1000.00,'2026-03-31 00:00:00',1,'','2026-04-01 16:05:20',NULL,'2026-04-01 16:05:20',NULL,NULL,NULL),
(4,'trip',NULL,4,'cash','parking',200.00,'2026-04-08 00:00:00',0,'','2026-04-30 17:38:52',NULL,'2026-04-30 17:38:52',NULL,NULL,NULL),
(5,'truck',1,NULL,'paid_by_driver','fuel',2000.00,'2026-04-23 00:00:00',0,'','2026-04-30 17:43:06',NULL,'2026-04-30 17:43:06',NULL,NULL,NULL),
(6,'office',NULL,NULL,'cash','others',900.00,'2026-04-30 00:00:00',0,'Tea','2026-04-30 17:47:12',NULL,'2026-04-30 17:48:03',NULL,NULL,NULL),
(7,'office',NULL,NULL,'online','Telephone Expenses',500.00,'2026-04-30 00:00:00',0,'','2026-04-30 18:24:10',NULL,'2026-04-30 19:09:32',NULL,NULL,NULL),
(8,'trip',NULL,5,'cash','fuel',1000.00,'2026-05-23 00:00:00',1,'','2026-05-23 17:01:07',NULL,'2026-05-23 17:01:07',NULL,NULL,NULL);

/*Table structure for table `trip_payments` */

DROP TABLE IF EXISTS `trip_payments`;

CREATE TABLE `trip_payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `trip_id` int DEFAULT NULL,
  `payment_method` enum('cash','cheque','upi','bank_transfer','fuel','others') DEFAULT NULL,
  `received_by_driver` tinyint(1) DEFAULT '0',
  `amount` float(10,2) DEFAULT '0.00',
  `payment_date` datetime DEFAULT NULL,
  `notes` text,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `trip_payments` */

insert  into `trip_payments`(`id`,`trip_id`,`payment_method`,`received_by_driver`,`amount`,`payment_date`,`notes`,`created_at`,`created_by`,`updated_at`,`updated_by`,`deleted_at`,`deleted_by`) values 
(2,1,'cash',1,1000.00,'2026-03-31 00:00:00','','2026-04-01 17:00:17',NULL,'2026-04-01 17:00:17',NULL,NULL,NULL),
(3,5,'cash',1,500.00,'2026-05-23 00:00:00','sdfsdfdsfsd','2026-05-23 17:03:05',NULL,'2026-05-23 17:03:05',NULL,NULL,NULL);

/*Table structure for table `trips` */

DROP TABLE IF EXISTS `trips`;

CREATE TABLE `trips` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `party_id` int unsigned DEFAULT NULL,
  `party_name` varchar(255) DEFAULT NULL,
  `party_manual_entry` tinyint(1) NOT NULL DEFAULT '0',
  `truck_id` int unsigned DEFAULT NULL,
  `truck_name` varchar(255) DEFAULT NULL,
  `truck_manual_entry` tinyint(1) NOT NULL DEFAULT '0',
  `driver_id` int unsigned DEFAULT NULL,
  `driver_name` varchar(255) DEFAULT NULL,
  `driver_manual_entry` tinyint(1) NOT NULL DEFAULT '0',
  `origin` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `billing_type` enum('fixed','per_tonne','per_kg','per_km','per_trip','per_day','per_hour','per_litre','per_bag') NOT NULL DEFAULT 'fixed',
  `per_unit_amount` float(10,2) DEFAULT '0.00',
  `unit` int DEFAULT NULL,
  `freight_amount` float(10,2) NOT NULL DEFAULT '0.00',
  `pending_freight_amount` float(10,2) DEFAULT '0.00',
  `profit` float(10,2) DEFAULT '0.00',
  `start_date` datetime NOT NULL,
  `start_km` int unsigned NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `end_km` int unsigned DEFAULT NULL,
  `lr_number` varchar(50) DEFAULT NULL,
  `material_name` varchar(250) DEFAULT NULL,
  `note` text,
  `completed_date` datetime DEFAULT NULL,
  `pod_receipt` varchar(500) DEFAULT NULL,
  `pod_received_date` datetime DEFAULT NULL,
  `pod_submitted_date` datetime DEFAULT NULL,
  `settled_date` datetime DEFAULT NULL,
  `status` enum('pending','start','completed','pod_received','pod_submitted','settled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `deleted_by` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `trips` */

insert  into `trips`(`id`,`party_id`,`party_name`,`party_manual_entry`,`truck_id`,`truck_name`,`truck_manual_entry`,`driver_id`,`driver_name`,`driver_manual_entry`,`origin`,`destination`,`billing_type`,`per_unit_amount`,`unit`,`freight_amount`,`pending_freight_amount`,`profit`,`start_date`,`start_km`,`end_date`,`end_km`,`lr_number`,`material_name`,`note`,`completed_date`,`pod_receipt`,`pod_received_date`,`pod_submitted_date`,`settled_date`,`status`,`created_at`,`updated_at`,`created_by`,`updated_by`,`deleted_by`,`deleted_at`) values 
(1,1,NULL,0,1,NULL,0,1,NULL,0,'Alwar','Jaipur','fixed',0.00,NULL,10000.00,9900.00,8900.00,'2026-03-31 16:49:51',76565,'2026-03-31 00:00:00',774533,NULL,NULL,NULL,'2026-03-31 17:36:14',NULL,'2026-03-31 17:36:30','2026-03-31 17:36:34','2026-04-01 16:59:15','settled','2026-03-31 16:10:53','2026-04-01 17:01:25',1,1,NULL,NULL),
(2,2,NULL,0,2,NULL,0,2,NULL,0,'Jamnagar','Rajkot','per_tonne',1000.00,200,200000.00,200000.00,0.00,'2026-04-20 19:02:14',25025,'2026-04-21 00:00:00',27000,'LR0987','Iron rods','this is testing','2026-04-20 19:02:38',NULL,'2026-04-20 19:02:48','2026-04-20 19:02:53','2026-04-20 19:03:14','settled','2026-04-20 18:59:45','2026-04-20 19:03:14',1,1,NULL,NULL),
(3,NULL,'Chandreshbhai',1,1,NULL,0,NULL,'Mohan',1,'Jamnagar','Surat','per_tonne',200.00,200,40000.00,40000.00,0.00,'2026-04-24 18:27:20',12000,'2026-05-24 00:00:00',12500,'ghhhdkf','Iron rod','alsdfj lfjdslfjsf',NULL,NULL,NULL,NULL,NULL,'start','2026-04-22 16:54:36','2026-05-24 11:17:50',1,1,NULL,NULL),
(4,5,NULL,0,2,NULL,0,1,NULL,0,'Jamnagar','Rajkot','per_tonne',1200.00,2000,2400000.00,2400000.00,0.00,'2026-04-25 16:55:00',12333,'2026-04-26 00:00:00',123000,'ieieu','Clothes','lsf safl sdjlfjsd','2026-04-26 14:56:48','POCReceipt/Trip4/1779607321_6435_d-rendering-modern-light-blue-wall-white-random-cube-size-shape-pattern-background-176179305.jpg','2026-05-24 12:52:01',NULL,NULL,'pod_received','2026-04-25 11:25:54','2026-05-24 12:52:01',1,1,NULL,NULL),
(5,NULL,'Shivam brass',1,NULL,'GJ101234',1,1,NULL,0,'Jamnagar','Surat','per_kg',200.00,1000,200000.00,0.00,199000.00,'2026-05-23 16:59:00',29000,'2026-05-24 00:00:00',29500,'1233454','Brass','fffffffff','2026-05-24 12:27:44',NULL,'2026-05-24 12:50:18','2026-05-24 12:55:44','2026-05-24 13:06:41','settled','2026-05-23 16:59:52','2026-05-24 13:06:41',1,1,NULL,NULL);

/*Table structure for table `trucks` */

DROP TABLE IF EXISTS `trucks`;

CREATE TABLE `trucks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` int DEFAULT NULL,
  `truck_number` varchar(100) NOT NULL,
  `truck_type` varchar(150) DEFAULT NULL,
  `ownership` enum('market','self') DEFAULT 'self',
  `status` enum('available','not_available','hold') DEFAULT 'available',
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `trucks` */

insert  into `trucks`(`id`,`driver_id`,`truck_number`,`truck_type`,`ownership`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`deleted_at`,`deleted_by`) values 
(1,1,'GJ10TY4757','other','market','available','2026-01-18 19:34:43',NULL,'2026-05-24 11:17:50',NULL,NULL,NULL),
(2,NULL,'REG123456','open-truck','self','not_available','2026-04-20 18:57:37',NULL,'2026-05-24 12:52:01',NULL,NULL,NULL);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Y' COMMENT 'Y = Active, N = Inactive',
  `logins` int unsigned DEFAULT '0',
  `last_login_ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_mobile_index` (`mobile`),
  KEY `users_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`mobile`,`email_verified_at`,`password`,`is_active`,`logins`,`last_login_ip`,`last_login_at`,`remember_token`,`created_at`,`updated_at`) values 
(1,'Admin','nbadmin@mailinator.com','1111111111',NULL,'$2y$10$0wJrQYpK.VEY5a0F587E0utzC2rhBkiVpgeklhep28zPRNAySYNR2','Y',26,NULL,NULL,NULL,NULL,'2026-05-25 22:17:56');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
