/*
SQLyog Enterprise - MySQL GUI v8.18 
MySQL - 5.5.5-10.4.32-MariaDB : Database - norea_psms
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`norea_psms` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `norea_psms`;

/*Table structure for table `tbl_assign_subject_grade` */

DROP TABLE IF EXISTS `tbl_assign_subject_grade`;

CREATE TABLE `tbl_assign_subject_grade` (
  `assign_subject_grade_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grade_id` int(11) NOT NULL,
  `subject_code` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`assign_subject_grade_id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_assign_subject_grade` */

insert  into `tbl_assign_subject_grade`(`assign_subject_grade_id`,`grade_id`,`subject_code`,`create_date`,`isDeleted`) values (1,1,1,'2025-02-27 23:09:27',0),(2,1,2,'2025-02-27 23:09:50',0),(3,1,3,'2025-02-27 23:10:00',0),(4,1,4,'2025-02-27 23:57:03',0),(5,1,5,'2025-02-27 23:57:03',0),(6,1,6,'2025-02-27 23:57:03',0),(7,1,7,'2025-02-27 23:57:03',0),(8,4,1,'2025-02-28 00:02:24',0),(9,4,2,'2025-02-28 00:02:24',0),(10,4,3,'2025-02-28 00:02:24',0),(11,4,4,'2025-02-28 00:02:24',0),(12,4,5,'2025-02-28 00:02:24',0),(13,4,6,'2025-02-28 00:02:24',0),(14,4,7,'2025-02-28 00:02:24',0),(15,4,8,'2025-02-28 00:02:24',0),(16,4,9,'2025-02-28 00:02:24',0),(17,4,10,'2025-02-28 00:02:24',0),(18,4,11,'2025-02-28 00:02:24',0),(19,4,12,'2025-02-28 00:02:24',0),(20,4,13,'2025-02-28 00:02:24',0),(21,4,14,'2025-02-28 00:02:24',0),(22,4,15,'2025-02-28 00:02:24',0),(23,4,16,'2025-02-28 00:02:24',0),(24,4,17,'2025-02-28 00:02:24',0),(25,4,18,'2025-02-28 00:02:24',0),(26,4,19,'2025-02-28 00:02:24',0),(27,4,20,'2025-02-28 00:02:24',0),(28,4,21,'2025-02-28 00:02:24',0),(29,4,22,'2025-02-28 00:02:24',0),(30,4,23,'2025-02-28 00:02:24',0),(31,4,24,'2025-02-28 00:02:24',0),(32,4,25,'2025-02-28 00:02:24',0),(33,4,26,'2025-02-28 00:02:24',0),(34,2,1,'2025-02-28 00:20:47',0),(35,2,2,'2025-02-28 00:20:47',0),(36,2,3,'2025-02-28 00:20:47',0),(37,2,4,'2025-02-28 00:20:47',0),(38,2,5,'2025-02-28 00:20:47',0),(39,2,6,'2025-02-28 00:20:47',0),(40,2,7,'2025-02-28 00:20:47',0),(41,2,8,'2025-02-28 00:20:47',0),(42,2,9,'2025-02-28 00:20:47',0),(43,2,10,'2025-02-28 00:20:47',0),(44,2,11,'2025-02-28 00:20:47',0),(45,2,12,'2025-02-28 00:20:47',0),(46,2,13,'2025-02-28 00:20:47',0),(47,2,14,'2025-02-28 00:20:47',0),(48,2,15,'2025-02-28 00:20:47',0),(49,2,16,'2025-02-28 00:20:47',0),(50,2,17,'2025-02-28 00:20:47',0),(51,2,18,'2025-02-28 00:20:47',0),(52,2,19,'2025-02-28 00:20:47',0),(53,2,20,'2025-02-28 00:20:47',0),(54,2,21,'2025-02-28 00:20:47',0),(55,2,22,'2025-02-28 00:20:47',0),(56,2,23,'2025-02-28 00:20:47',0),(57,2,24,'2025-02-28 00:20:47',0),(58,2,25,'2025-02-28 00:20:47',0),(59,2,26,'2025-02-28 00:20:47',0);

/*Table structure for table `tbl_classroom` */

DROP TABLE IF EXISTS `tbl_classroom`;

CREATE TABLE `tbl_classroom` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_name` varchar(255) NOT NULL,
  `grade_id` int(11) NOT NULL,
  `session_id` int(10) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `num_students_in_class` int(11) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`class_id`,`class_name`),
  KEY `fk_classroom_session` (`session_id`),
  CONSTRAINT `fk_classroom_session` FOREIGN KEY (`session_id`) REFERENCES `tbl_school_session` (`session_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_classroom` */

insert  into `tbl_classroom`(`class_id`,`class_name`,`grade_id`,`session_id`,`teacher_id`,`num_students_in_class`,`create_date`,`isDeleted`) values (1,'១ក',1,1,1001,45,'2025-02-08 17:07:59',0),(2,'២ក',2,1,1001,45,'2025-02-08 17:19:28',0),(3,'២ខ',2,2,1001,45,'2025-02-08 17:20:15',0),(4,'១ខ',1,1,1001,45,'2025-02-08 18:24:32',0),(5,'១គ',1,1,1002,45,'2025-02-09 17:13:01',0),(6,'៤ក',4,1,1003,45,'2025-02-11 19:14:21',0),(7,'៤​ខ',4,1,1004,45,'2025-02-11 19:27:08',0),(8,'3ក',3,1,1001,45,'2025-03-01 13:36:57',0);

/*Table structure for table `tbl_grade` */

DROP TABLE IF EXISTS `tbl_grade`;

CREATE TABLE `tbl_grade` (
  `grade_id` int(11) NOT NULL,
  `grade_name` varchar(255) NOT NULL,
  `level` int(50) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`grade_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_grade` */

insert  into `tbl_grade`(`grade_id`,`grade_name`,`level`,`create_date`,`isDeleted`) values (1,'ថ្នាក់ទី ១',1,'2025-02-02 09:52:57',0),(2,'ថ្នាក់ទី ២',2,'2025-02-02 09:52:57',0),(3,'ថ្នាក់ទី ៣',3,'2025-02-02 09:52:57',0),(4,'ថ្នាក់ទី ៤',4,'2025-02-02 09:52:57',0),(5,'ថ្នាក់ទី ៥',5,'2025-02-02 09:52:57',0),(6,'ថ្នាក់ទី ៦',6,'2025-02-02 09:52:57',0);

/*Table structure for table `tbl_monthly` */

DROP TABLE IF EXISTS `tbl_monthly`;

CREATE TABLE `tbl_monthly` (
  `monthly_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `month_name` varchar(255) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`monthly_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_monthly` */

insert  into `tbl_monthly`(`monthly_id`,`month_name`,`create_date`,`isDeleted`) values (1,'មករា','2025-02-02 09:52:34',0),(2,'កុម្ភះ','2025-02-02 09:52:34',0),(3,'មិនា','2025-02-02 09:52:34',0),(4,'មេសា','2025-02-02 09:52:34',0),(5,'ឧសភា','2025-02-02 09:52:34',0),(6,'មិថុនា','2025-02-02 09:52:34',0),(7,'កក្កដា','2025-02-02 09:52:34',0),(8,'សីហា','2025-02-02 09:52:34',0),(9,'កញ្ញា','2025-02-02 09:52:34',0),(10,'តុលា','2025-02-02 09:52:34',0),(11,'វិច្ឆិកា','2025-02-02 09:52:34',0),(12,'ធ្នូ','2025-02-02 09:52:34',0);

/*Table structure for table `tbl_school_session` */

DROP TABLE IF EXISTS `tbl_school_session`;

CREATE TABLE `tbl_school_session` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_name` varchar(50) NOT NULL,
  `isDeleted` int(2) DEFAULT 0,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_school_session` */

insert  into `tbl_school_session`(`session_id`,`session_name`,`isDeleted`,`create_date`) values (1,'វេនព្រឹក',0,'2025-02-08 16:29:07'),(2,'វេនរសៀល',0,'2025-02-08 16:29:13');

/*Table structure for table `tbl_semester` */

DROP TABLE IF EXISTS `tbl_semester`;

CREATE TABLE `tbl_semester` (
  `semester_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `semester_name` varchar(255) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`semester_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_semester` */

insert  into `tbl_semester`(`semester_id`,`semester_name`,`create_date`,`isDeleted`) values (1,'ឆមាសទី១','2025-02-02 09:51:24',0),(2,'ឆមាសទី២','2025-02-02 09:51:24',0);

/*Table structure for table `tbl_student_info` */

DROP TABLE IF EXISTS `tbl_student_info`;

CREATE TABLE `tbl_student_info` (
  `student_id` int(10) unsigned NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `class_id` int(11) NOT NULL,
  `year_study_id` int(11) DEFAULT NULL,
  `pob_address` varchar(255) DEFAULT NULL,
  `current_address` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `father_job` varchar(255) DEFAULT NULL,
  `father_phone` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `mother_job` varchar(255) DEFAULT NULL,
  `mother_phone` varchar(255) DEFAULT NULL,
  `family_status` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_student_info` */

insert  into `tbl_student_info`(`student_id`,`student_name`,`gender`,`dob`,`class_id`,`year_study_id`,`pob_address`,`current_address`,`father_name`,`father_job`,`father_phone`,`mother_name`,`mother_job`,`mother_phone`,`family_status`,`status`,`isDeleted`) values (1001,'សុខ សុវណ្ណា','female','2005-05-15',1,NULL,'ភ្នំពេញ','ភ្នំពេញ','សុខ សុភា','គ្រូ','012345678','សុខ សុភា','គ្រូ','012345678','level1','active',0),(1002,'ចាន់ សុភា','male','2006-08-20',1,NULL,'កណ្តាល','ភ្នំពេញ','ចាន់ សុភា','កសិករ','098765432','ចាន់ សុភា','គ្រូ','098765432','level2','active',0),(1003,'លី សុវណ្ណា','female','2007-12-10',1,NULL,'សៀមរាប','ភ្នំពេញ','លី សុភា','ពេទ្យ','011223344','លី សុភា','គ្រូ','011223344',NULL,'active',0),(1004,'ផល សុផាត','male','2005-06-15',1,NULL,'កំពង់ចាម','ភ្នំពេញ','សុខ សុភា','គ្រូ','012345679','សុខ សុភា','គ្រូ','012345679','level1','active',0),(1005,'ប៉ោយ ស្រីលីន','female','2006-09-20',1,NULL,'កណ្តាល','ភ្នំពេញ','ចាន់ សុភា','កសិករ','098765433','ចាន់ សុភា','គ្រូ','098765433','level2','active',0),(1006,'ពៅ សំ','male','2007-11-10',1,NULL,'សៀមរាប','ភ្នំពេញ','លី សុភា','ពេទ្យ','011223345','លី សុភា','គ្រូ','011223345',NULL,'active',0),(1007,'ឆេង ម៉េង','female','2005-07-15',1,NULL,'ភ្នំពេញ','ភ្នំពេញ','សុខ សុភា','គ្រូ','012345680','សុខ សុភា','គ្រូ','012345680','level1','active',0),(1008,'សួន ចាន់ដារ៉ូ','male','2006-10-20',1,NULL,'កណ្តាល','ភ្នំពេញ','ចាន់ សុភា','កសិករ','098765434','ចាន់ សុភា','គ្រូ','098765434','level2','active',0),(1009,'គួន ម៉ុម','female','2007-10-10',1,NULL,'សៀមរាប','ភ្នំពេញ','លី សុភា','ពេទ្យ','011223346','លី សុភា','គ្រូ','011223346',NULL,'active',0),(1010,'សុខ សុវណ្ណ','male','2005-08-15',1,NULL,'កំពង់ចាម','ភ្នំពេញ','សុខ សុភា','គ្រូ','012345681','សុខ សុភា','គ្រូ','012345681','level1','active',0),(1011,'ស៊ីថា ស្រីនីត','female','2006-11-20',2,NULL,'កណ្តាល','ភ្នំពេញ','ចាន់ សុភា','កសិករ','098765435','ចាន់ សុភា','គ្រូ','098765435','level2','active',0),(1012,'នី ឡេនីន','male','2007-09-10',3,NULL,'សៀមរាប','ភ្នំពេញ','លី សុភា','ពេទ្យ','011223347','លី សុភា','គ្រូ','011223347',NULL,'active',0),(1013,'វ៉ាន់ដា រ៉ូតឌី','female','2005-09-15',1,NULL,'ភ្នំពេញ','ភ្នំពេញ','សុខ សុភា','គ្រូ','012345682','សុខ សុភា','គ្រូ','012345682','level1','active',0),(1014,'ចាន់ មករា','male','2006-12-20',2,NULL,'កណ្តាល','ភ្នំពេញ','ចាន់ សុភា','កសិករ','098765436','ចាន់ សុភា','គ្រូ','098765436','level2','active',0),(1015,'លី កុម្ភៈ','female','2007-08-10',3,NULL,'សៀមរាប','ភ្នំពេញ','លី សុភា','ពេទ្យ','011223348','លី សុភា','គ្រូ','011223348',NULL,'active',0),(1016,'សុខ មិនា','male','2005-10-15',1,NULL,'កំពង់ចាម','ភ្នំពេញ','សុខ សុភា','គ្រូ','012345683','សុខ សុភា','គ្រូ','012345683','level1','active',0),(1017,'ចាន់ សុភាន់','female','2006-01-20',2,NULL,'កណ្តាល','ភ្នំពេញ','ចាន់ សុភា','កសិករ','098765437','ចាន់ សុភា','គ្រូ','098765437','level2','active',0),(1018,'លុយ សុវណ្ណា','male','2007-07-10',3,NULL,'សៀមរាប','ភ្នំពេញ','លី សុភា','ពេទ្យ','011223349','លី សុភា','គ្រូ','011223349',NULL,'active',0),(1019,'សុខ វិច្ឆិកា','female','2005-11-15',1,NULL,'ភ្នំពេញ','ភ្នំពេញ','សុខ សុភា','គ្រូ','012345684','សុខ សុភា','គ្រូ','012345684','level1','active',0),(1020,'ចាន់ សម័យ','male','2006-02-20',2,NULL,'កណ្តាល','ភ្នំពេញ','ចាន់ សុភា','កសិករ','098765438','ចាន់ សុភា','គ្រូ','098765438','level2','active',0),(1021,'លី សុវណ្ណេ','female','2007-06-10',3,NULL,'សៀមរាប','ភ្នំពេញ','លី សុភា','ពេទ្យ','011223350','លី សុភា','គ្រូ','011223350',NULL,'active',0),(1022,'សុខ ស្រីវី','male','2005-12-15',1,NULL,'កំពង់ចាម','ភ្នំពេញ','សុខ សុភា','គ្រូ','012345685','សុខ សុភា','គ្រូ','012345685','level1','active',0),(1023,'សុវណ្ណ លគុត','male','2006-03-20',2,NULL,'កណ្តាល','ភ្នំពេញ','ចាន់ សុភា','កសិករ','098765439','ចាន់ សុភា','គ្រូ','098765439','level2','active',0),(1024,'មុនី វិក្រាណ','female','2007-05-10',3,NULL,'សៀមរាប','ភ្នំពេញ','លី សុភា','ពេទ្យ','011223351','លី សុភា','គ្រូ','011223351',NULL,'active',0),(1025,'ហ៊ុល គឹមហ៊ុយ','male','2005-01-01',1,NULL,'ភ្នំពេញ','ភ្នំពេញ','ហ៊ុល សុភា','គ្រូ','012345678','ហ៊ុល សុភា','គ្រូ','012345678','level1','active',0),(1026,'វិរៈ ដារ៉ា','male','2005-02-01',2,NULL,'កណ្តាល','ភ្នំពេញ','វិរៈ សុភា','កសិករ','098765432','វិរៈ សុភា','គ្រូ','098765432','level2','active',0),(1027,'រ៉េត លីហេង','male','2005-03-01',3,NULL,'សៀមរាប','ភ្នំពេញ','រ៉េត សុភា','ពេទ្យ','011223344','រ៉េត សុភា','គ្រូ','011223344',NULL,'active',0),(1028,'អូសេ តាកាគី','male','2005-04-01',1,NULL,'កំពង់ចាម','ភ្នំពេញ','អូសេ សុភា','គ្រូ','012345679','អូសេ សុភា','គ្រូ','012345679','level1','active',0),(1029,'ខាន់ ហ្វាអេហ្ស','male','2005-05-01',2,NULL,'កណ្តាល','ភ្នំពេញ','ខាន់ សុភា','កសិករ','098765433','ខាន់ សុភា','គ្រូ','098765433','level2','active',0),(1030,'សឿយ វិសាល','male','2005-06-01',3,NULL,'សៀមរាប','ភ្នំពេញ','សឿយ សុភា','ពេទ្យ','011223345','សឿយ សុភា','គ្រូ','011223345',NULL,'active',0),(1031,'តាំង ប៊ុនឆៃ','male','2005-07-01',1,NULL,'ភ្នំពេញ','ភ្នំពេញ','តាំង សុភា','គ្រូ','012345680','តាំង សុភា','គ្រូ','012345680','level1','active',0),(1032,'សារ៉េត គ្រីយ៉ា','male','2005-08-01',2,NULL,'កណ្តាល','ភ្នំពេញ','សារ៉េត សុភា','កសិករ','098765434','សារ៉េត សុភា','គ្រូ','098765434','level2','active',0),(1033,'អ៊ន ចាន់ប៉ូលីន','male','2005-09-01',3,NULL,'សៀមរាប','ភ្នំពេញ','អ៊ន សុភា','ពេទ្យ','011223346','អ៊ន សុភា','គ្រូ','011223346',NULL,'active',0),(1034,'មីហ្សូណូ ហិការ៉ូ','male','2005-10-01',1,NULL,'កំពង់ចាម','ភ្នំពេញ','មីហ្សូណូ សុភា','គ្រូ','012345681','មីហ្សូណូ សុភា','គ្រូ','012345681','level1','active',0),(1035,'អូហ្គាវ៉ា យូដៃ','male','2005-11-01',2,NULL,'កណ្តាល','ភ្នំពេញ','អូហ្គាវ៉ា សុភា','កសិករ','098765435','អូហ្គាវ៉ា សុភា','គ្រូ','098765435','level2','active',0),(1036,'អ៊ិន សុដាវីត','male','2005-12-01',3,NULL,'សៀមរាប','ភ្នំពេញ','អ៊ិន សុភា','ពេទ្យ','011223347','អ៊ិន សុភា','គ្រូ','011223347',NULL,'active',0),(1037,'មិន រតនៈ','male','2006-01-01',1,NULL,'ភ្នំពេញ','ភ្នំពេញ','មិន សុភា','គ្រូ','012345682','មិន សុភា','គ្រូ','012345682','level1','active',0),(1038,'គីម សុគុយ','male','2006-02-01',2,NULL,'កណ្តាល','ភ្នំពេញ','គីម សុភា','កសិករ','098765436','គីម សុភា','គ្រូ','098765436','level2','active',0),(1039,'យឺ មូស្លីម','male','2006-03-01',3,NULL,'សៀមរាប','ភ្នំពេញ','យឺ សុភា','ពេទ្យ','011223348','យឺ សុភា','គ្រូ','011223348',NULL,'active',0),(1040,'ស៊ីន កាកដា','male','2006-04-01',1,NULL,'កំពង់ចាម','ភ្នំពេញ','ស៊ីន សុភា','គ្រូ','012345683','ស៊ីន សុភា','គ្រូ','012345683','level1','active',0),(1041,'ឡេង នូរ៉ា','male','2006-05-01',2,NULL,'កណ្តាល','ភ្នំពេញ','ឡេង សុភា','កសិករ','098765437','ឡេង សុភា','គ្រូ','098765437','level2','active',0),(1042,'សឺត បារ៉ាយ','male','2006-06-01',3,NULL,'សៀមរាប','ភ្នំពេញ','សឺត សុភា','ពេទ្យ','011223349','សឺត សុភា','គ្រូ','011223349',NULL,'active',0),(1043,'អាន់ដ្រេស នីអេតូ','male','2006-07-01',1,NULL,'ភ្នំពេញ','ភ្នំពេញ','អាន់ដ្រេស សុភា','គ្រូ','012345684','អាន់ដ្រេស សុភា','គ្រូ','012345684','level1','active',0),(1044,'សា ទី','male','2006-08-01',2,NULL,'កណ្តាល','ភ្នំពេញ','សា សុភា','កសិករ','098765438','សា សុភា','គ្រូ','098765438','level2','active',0),(1045,'លីម ពិសុទ្ធ','male','2006-09-01',3,NULL,'សៀមរាប','ភ្នំពេញ','លីម សុភា','ពេទ្យ','011223350','លីម សុភា','គ្រូ','011223350',NULL,'active',0),(1046,'សៀង ចន្ថា','male','2006-10-01',1,NULL,'កំពង់ចាម','ភ្នំពេញ','សៀង សុភា','គ្រូ','012345685','សៀង សុភា','គ្រូ','012345685','level1','active',0),(1047,'តេល័រ នីក','male','2006-11-01',2,NULL,'កណ្តាល','ភ្នំពេញ','តេល័រ សុភា','កសិករ','098765439','សៀង សុភា','គ្រូ','012345685','level1','active',0);

/*Table structure for table `tbl_student_monthly_score` */

DROP TABLE IF EXISTS `tbl_student_monthly_score`;

CREATE TABLE `tbl_student_monthly_score` (
  `student_monthly_score_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `assign_subject_grade_id` int(10) unsigned NOT NULL,
  `monthly_id` int(10) unsigned NOT NULL,
  `score` float NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`student_monthly_score_id`),
  KEY `student_id` (`student_id`),
  KEY `assign_subject_grade_id` (`assign_subject_grade_id`),
  KEY `monthly_id` (`monthly_id`),
  CONSTRAINT `tbl_student_monthly_score_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `tbl_student_info` (`student_id`) ON DELETE CASCADE,
  CONSTRAINT `tbl_student_monthly_score_ibfk_2` FOREIGN KEY (`assign_subject_grade_id`) REFERENCES `tbl_assign_subject_grade` (`assign_subject_grade_id`) ON DELETE CASCADE,
  CONSTRAINT `tbl_student_monthly_score_ibfk_3` FOREIGN KEY (`monthly_id`) REFERENCES `tbl_monthly` (`monthly_id`) ON DELETE CASCADE,
  CONSTRAINT `tbl_student_monthly_score_ibfk_4` FOREIGN KEY (`assign_subject_grade_id`) REFERENCES `tbl_assign_subject_grade` (`assign_subject_grade_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_student_monthly_score` */

/*Table structure for table `tbl_student_semester_score` */

DROP TABLE IF EXISTS `tbl_student_semester_score`;

CREATE TABLE `tbl_student_semester_score` (
  `student_semester_score_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `assign_subject_grade_id` int(10) unsigned NOT NULL,
  `semester_id` int(10) unsigned NOT NULL,
  `score` float NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`student_semester_score_id`),
  KEY `student_id` (`student_id`),
  KEY `classroom_subject_id` (`assign_subject_grade_id`),
  KEY `semester_id` (`semester_id`),
  CONSTRAINT `tbl_student_semester_score_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `tbl_student_info` (`student_id`) ON DELETE CASCADE,
  CONSTRAINT `tbl_student_semester_score_ibfk_3` FOREIGN KEY (`semester_id`) REFERENCES `tbl_semester` (`semester_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_student_semester_score` */

/*Table structure for table `tbl_subject` */

DROP TABLE IF EXISTS `tbl_subject`;

CREATE TABLE `tbl_subject` (
  `subject_code` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(255) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`subject_code`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_subject` */

insert  into `tbl_subject`(`subject_code`,`subject_name`,`create_date`,`isDeleted`) values (1,'គណិតវិទ្យា','2025-02-24 11:59:00',0),(2,'ភាសាខ្មែរ','2025-02-24 11:59:08',0),(3,'វិទ្យាសាស្ត្រ','2025-02-24 17:58:35',0),(4,'សិក្សាសង្គម','2025-02-24 17:58:44',0),(5,'អប់រំកាយ-សុខភាពកីឡា','2025-02-24 17:59:07',0),(6,'អប់រំបំណិនជីវិត','2025-02-24 17:59:20',0),(7,'ភាសាបរទេស','2025-02-24 17:59:34',0),(8,'សម្ថភាពស្ដាប់','2025-02-27 23:58:11',0),(9,'សម្ថភាពសរសេរ','2025-02-27 23:58:20',0),(10,'សម្ថភាពអាន','2025-02-27 23:58:29',0),(11,'សម្ថភាពនិយាយ','2025-02-27 23:58:37',0),(12,'ចំនួន','2025-02-27 23:58:41',0),(13,'រង្វាស់រង្វាល់','2025-02-27 23:58:50',0),(14,'ធរណីមាត្រ','2025-02-27 23:58:57',0),(15,'ពីជគណិត','2025-02-27 23:59:04',0),(16,'ស្ថិតិ','2025-02-27 23:59:11',0),(17,'រូបវិទ្យា','2025-02-27 23:59:19',0),(18,'គីមីវិទ្យា','2025-02-27 23:59:26',0),(19,'ជីវវិទ្យា','2025-02-27 23:59:36',0),(20,'ផែនដី-បរិស្ថានវិទ្យា','2025-02-27 23:59:55',0),(21,'សីលធម៌-ពលរដ្ឋវិទ្យា','2025-02-28 00:00:11',0),(22,'ភូមិវិទ្យា','2025-02-28 00:00:20',0),(23,'ប្រវត្តិវិទ្យា','2025-02-28 00:00:32',0),(24,'គេហវិទ្យា-អប់រំសិល្បៈ','2025-02-28 00:00:55',0),(25,'អប់រំកាយ-កីឡា','2025-02-28 00:01:08',0),(26,'សុខភាព-អនាម័យ','2025-02-28 00:01:24',0);

/*Table structure for table `tbl_teacher` */

DROP TABLE IF EXISTS `tbl_teacher`;

CREATE TABLE `tbl_teacher` (
  `teacher_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `teacher_name` varchar(255) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`teacher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1005 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_teacher` */

insert  into `tbl_teacher`(`teacher_id`,`teacher_name`,`create_date`,`isDeleted`) values (1001,'កែវ វាសនា','2025-02-02 09:50:02',0),(1002,'អ៊ុំ វ៉ាន់ច័ន្ទ','2025-02-02 09:50:02',0),(1003,'ដួង រតនា','2025-02-05 20:13:09',0),(1004,'កែវ ចាន់សម្ភស្ស','2025-02-05 20:38:29',0);

/*Table structure for table `tbl_user` */

DROP TABLE IF EXISTS `tbl_user`;

CREATE TABLE `tbl_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_user` */

insert  into `tbl_user`(`user_id`,`full_name`,`user_name`,`password`,`phone`,`user_type`,`created_date`,`isDeleted`) values (1,'Poeu Sam','khmersr','$2y$10$LljfIoNdd4ua088RMl9NnOowB8a.ljbgQd6wD.hBX0jnNNfiGXE1q','098582828','admin','0000-00-00 00:00:00',0),(2,'Norea PMS','admin_norea','$2y$10$1Ow1S23GKdkv1uR5ZS.seOO0w0.t4AMkZyOmJm6I3lmnMOsyVLaQa','0123456789','super_admin','2025-02-02 11:41:44',0),(3,'Ny lenin','lenin','$2y$10$F05Y.0ET3h7BBJHwmqPyuuu7nyhKLnmwtAEhyyIb7BlLibt.Bt04S','054359273','user','2025-02-02 11:42:01',0),(4,'Test','test','$2y$10$y7fU6kwX40R/wYZUu9zrNe5fFQ1eQKNn4g0M/R8AKpwo6eiQpl/YO','098583828','user','2025-02-11 23:15:10',0);

/*Table structure for table `tbl_year_study` */

DROP TABLE IF EXISTS `tbl_year_study`;

CREATE TABLE `tbl_year_study` (
  `year_study_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `year_study` varchar(255) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`year_study_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_year_study` */

insert  into `tbl_year_study`(`year_study_id`,`year_study`,`create_date`,`isDeleted`) values (1,'2025-2026','2025-02-02 09:49:37',0),(2,'2026-2027','2025-02-02 09:49:37',0),(3,'2027-2028','2025-02-02 09:49:37',0);

/*Table structure for table `view_monthly_rankings` */

DROP TABLE IF EXISTS `view_monthly_rankings`;

/*!50001 DROP VIEW IF EXISTS `view_monthly_rankings` */;
/*!50001 DROP TABLE IF EXISTS `view_monthly_rankings` */;

/*!50001 CREATE TABLE  `view_monthly_rankings`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `class_id` int(11) ,
 `class_name` varchar(255) ,
 `grade_id` int(11) ,
 `grade_name` varchar(255) ,
 `monthly_id` int(10) unsigned ,
 `month_name` varchar(255) ,
 `total_subjects` bigint(21) ,
 `total_score` double ,
 `average_score` double(19,2) ,
 `rank_in_class` bigint(21) 
)*/;

/*Table structure for table `view_monthly_subject_scores` */

DROP TABLE IF EXISTS `view_monthly_subject_scores`;

/*!50001 DROP VIEW IF EXISTS `view_monthly_subject_scores` */;
/*!50001 DROP TABLE IF EXISTS `view_monthly_subject_scores` */;

/*!50001 CREATE TABLE  `view_monthly_subject_scores`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `class_id` int(11) ,
 `class_name` varchar(255) ,
 `monthly_id` int(10) unsigned ,
 `month_name` varchar(255) ,
 `assign_subject_grade_id` int(10) unsigned ,
 `subject_name` varchar(255) ,
 `score` float ,
 `subject_rank_in_class` bigint(21) 
)*/;

/*View structure for view view_monthly_rankings */

/*!50001 DROP TABLE IF EXISTS `view_monthly_rankings` */;
/*!50001 DROP VIEW IF EXISTS `view_monthly_rankings` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_monthly_rankings` AS with student_monthly_averages as (select `sms`.`student_id` AS `student_id`,`sms`.`monthly_id` AS `monthly_id`,count(distinct `sms`.`assign_subject_grade_id`) AS `total_subjects`,sum(`sms`.`score`) AS `total_score`,sum(`sms`.`score`) / count(distinct `sms`.`assign_subject_grade_id`) AS `average_score` from `tbl_student_monthly_score` `sms` where `sms`.`isDeleted` = 0 group by `sms`.`student_id`,`sms`.`monthly_id`)select `si`.`student_id` AS `student_id`,`si`.`student_name` AS `student_name`,`si`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`g`.`grade_id` AS `grade_id`,`g`.`grade_name` AS `grade_name`,`m`.`monthly_id` AS `monthly_id`,`m`.`month_name` AS `month_name`,`sma`.`total_subjects` AS `total_subjects`,`sma`.`total_score` AS `total_score`,round(`sma`.`average_score`,2) AS `average_score`,dense_rank() over ( partition by `si`.`class_id`,`m`.`monthly_id` order by `sma`.`average_score` desc) AS `rank_in_class` from ((((`tbl_student_info` `si` join `tbl_classroom` `c` on(`si`.`class_id` = `c`.`class_id`)) join `tbl_grade` `g` on(`c`.`grade_id` = `g`.`grade_id`)) join `student_monthly_averages` `sma` on(`si`.`student_id` = `sma`.`student_id`)) join `tbl_monthly` `m` on(`sma`.`monthly_id` = `m`.`monthly_id`)) where `si`.`isDeleted` = 0 */;

/*View structure for view view_monthly_subject_scores */

/*!50001 DROP TABLE IF EXISTS `view_monthly_subject_scores` */;
/*!50001 DROP VIEW IF EXISTS `view_monthly_subject_scores` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_monthly_subject_scores` AS select `si`.`student_id` AS `student_id`,`si`.`student_name` AS `student_name`,`si`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`m`.`monthly_id` AS `monthly_id`,`m`.`month_name` AS `month_name`,`asg`.`assign_subject_grade_id` AS `assign_subject_grade_id`,`s`.`subject_name` AS `subject_name`,`sms`.`score` AS `score`,dense_rank() over ( partition by `si`.`class_id`,`m`.`monthly_id`,`asg`.`assign_subject_grade_id` order by `sms`.`score` desc) AS `subject_rank_in_class` from (((((`tbl_student_info` `si` join `tbl_classroom` `c` on(`si`.`class_id` = `c`.`class_id`)) join `tbl_student_monthly_score` `sms` on(`si`.`student_id` = `sms`.`student_id`)) join `tbl_monthly` `m` on(`sms`.`monthly_id` = `m`.`monthly_id`)) join `tbl_assign_subject_grade` `asg` on(`sms`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) join `tbl_subject` `s` on(`asg`.`subject_code` = `s`.`subject_code`)) where `si`.`isDeleted` = 0 and `sms`.`isDeleted` = 0 */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
