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

/*Table structure for table `classroom_subject_monthly_score` */

DROP TABLE IF EXISTS `classroom_subject_monthly_score`;

CREATE TABLE `classroom_subject_monthly_score` (
  `classroom_subject_monthly_score_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int(10) unsigned NOT NULL,
  `assign_subject_grade_id` int(10) unsigned NOT NULL,
  `monthly_id` int(10) unsigned NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`classroom_subject_monthly_score_id`),
  KEY `class_id` (`class_id`),
  KEY `assign_subject_grade_id` (`assign_subject_grade_id`),
  KEY `monthly_id` (`monthly_id`),
  CONSTRAINT `fk_classroom_subject_monthly_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classroom` (`class_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_classroom_subject_monthly_monthly` FOREIGN KEY (`monthly_id`) REFERENCES `tbl_monthly` (`monthly_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_classroom_subject_monthly_subject` FOREIGN KEY (`assign_subject_grade_id`) REFERENCES `tbl_assign_subject_grade` (`assign_subject_grade_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `classroom_subject_monthly_score` */

insert  into `classroom_subject_monthly_score`(`classroom_subject_monthly_score_id`,`class_id`,`assign_subject_grade_id`,`monthly_id`,`create_date`,`isDeleted`) values (60,10,91,1,'2025-04-05 17:29:45',0),(61,10,95,1,'2025-04-05 17:29:45',0),(62,10,100,1,'2025-04-05 17:29:45',0),(63,10,98,1,'2025-04-05 17:29:45',0),(64,10,103,1,'2025-04-05 17:29:45',0),(65,10,104,1,'2025-04-05 17:29:45',0),(66,10,91,2,'2025-04-08 17:11:27',0),(67,10,95,2,'2025-04-08 17:11:27',0),(68,10,100,2,'2025-04-08 17:11:27',0),(69,10,98,2,'2025-04-08 17:11:27',0),(70,10,101,2,'2025-04-08 17:11:27',0),(71,10,92,2,'2025-04-08 17:11:27',0),(72,10,99,2,'2025-04-08 17:11:28',0),(73,10,103,2,'2025-04-08 17:11:28',0),(74,10,100,3,'2025-04-23 15:02:05',0),(75,10,98,3,'2025-04-23 15:02:05',0),(76,10,98,4,'2025-04-23 19:14:28',0),(77,10,101,4,'2025-04-23 19:14:28',0);

/*Table structure for table `tbl_activity_log` */

DROP TABLE IF EXISTS `tbl_activity_log`;

CREATE TABLE `tbl_activity_log` (
  `activity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL COMMENT 'Type of activity (login, score_entry, etc.)',
  `description` text NOT NULL COMMENT 'Description of the activity',
  `user_id` int(10) unsigned NOT NULL COMMENT 'User who performed the activity',
  `category` varchar(50) NOT NULL COMMENT 'Category of activity (auth, student, class, etc.)',
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Additional details in JSON format' CHECK (json_valid(`details`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`activity_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_type` (`type`),
  KEY `idx_category` (`category`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_activity_log` */

insert  into `tbl_activity_log`(`activity_id`,`type`,`description`,`user_id`,`category`,`details`,`created_at`,`isDeleted`) values (1,'login','User logged in successfully',1,'authentication','{\"ip\": \"127.0.0.1\", \"browser\": \"Chrome\"}','2025-04-17 20:57:20',0),(2,'student_register','New student registered',1,'student_management','{\"student_id\": 1001, \"student_name\": \"សុខ សុវណ្ណា\"}','2025-04-17 20:57:20',0),(3,'score_entry','Monthly scores entered',1,'score_management','{\"class_id\": 1, \"monthly_id\": 1, \"subject_count\": 3}','2025-04-17 20:57:20',0),(4,'class_create','New class created',1,'class_management','{\"class_id\": 1, \"class_name\": \"១ក\", \"grade_id\": 1}','2025-04-17 20:57:20',0),(5,'report_generate','Monthly report generated',1,'report_management','{\"report_type\": \"monthly\", \"class_id\": 1, \"monthly_id\": 1}','2025-04-17 20:57:20',0);

/*Table structure for table `tbl_assign_subject_grade` */

DROP TABLE IF EXISTS `tbl_assign_subject_grade`;

CREATE TABLE `tbl_assign_subject_grade` (
  `assign_subject_grade_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grade_id` int(11) NOT NULL,
  `subject_code` int(10) unsigned NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`assign_subject_grade_id`),
  KEY `fk_assign_subject_grade_grade` (`grade_id`),
  KEY `fk_assign_subject_grade_subject` (`subject_code`),
  CONSTRAINT `fk_assign_subject_grade_grade` FOREIGN KEY (`grade_id`) REFERENCES `tbl_grade` (`grade_id`),
  CONSTRAINT `fk_assign_subject_grade_subject` FOREIGN KEY (`subject_code`) REFERENCES `tbl_subject` (`subject_code`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_assign_subject_grade` */

insert  into `tbl_assign_subject_grade`(`assign_subject_grade_id`,`grade_id`,`subject_code`,`create_date`,`isDeleted`) values (91,1,1,'2025-04-05 17:29:23',0),(92,1,2,'2025-04-05 17:29:23',0),(93,1,26,'2025-04-05 17:29:23',0),(94,1,25,'2025-04-05 17:29:23',0),(95,1,24,'2025-04-05 17:29:23',0),(96,1,21,'2025-04-05 17:29:23',0),(97,1,16,'2025-04-05 17:29:23',0),(98,1,14,'2025-04-05 17:29:23',0),(99,1,13,'2025-04-05 17:29:23',0),(100,1,12,'2025-04-05 17:29:23',0),(101,1,15,'2025-04-05 17:29:23',0),(102,1,10,'2025-04-05 17:29:23',0),(103,1,11,'2025-04-05 17:29:23',0),(104,1,9,'2025-04-05 17:29:23',0),(105,1,8,'2025-04-05 17:29:23',0),(106,1,6,'2025-04-05 17:29:23',0),(107,1,5,'2025-04-05 17:29:23',0);

/*Table structure for table `tbl_classroom` */

DROP TABLE IF EXISTS `tbl_classroom`;

CREATE TABLE `tbl_classroom` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_name` varchar(255) NOT NULL,
  `grade_id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `teacher_id` int(10) unsigned DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  `year_study_id` int(10) unsigned DEFAULT NULL,
  `status` varchar(255) DEFAULT 'active',
  `num_students_in_class` int(11) NOT NULL DEFAULT 45 COMMENT 'Maximum number of students allowed in this class',
  PRIMARY KEY (`class_id`,`class_name`),
  KEY `fk_classroom_session` (`session_id`),
  KEY `fk_classroom_year_study` (`year_study_id`),
  KEY `fk_classroom_grade` (`grade_id`),
  KEY `fk_classroom_teacher` (`teacher_id`),
  CONSTRAINT `fk_classroom_grade` FOREIGN KEY (`grade_id`) REFERENCES `tbl_grade` (`grade_id`),
  CONSTRAINT `fk_classroom_session` FOREIGN KEY (`session_id`) REFERENCES `tbl_school_session` (`session_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_classroom_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `tbl_user` (`user_id`),
  CONSTRAINT `fk_classroom_year_study` FOREIGN KEY (`year_study_id`) REFERENCES `tbl_year_study` (`year_study_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_classroom` */

insert  into `tbl_classroom`(`class_id`,`class_name`,`grade_id`,`session_id`,`teacher_id`,`create_date`,`isDeleted`,`year_study_id`,`status`,`num_students_in_class`) values (10,'១ក',1,1,4,'2025-04-05 17:18:45',0,1,'active',45),(11,'២ក',2,1,NULL,'2025-04-05 17:24:22',0,1,'active',45),(12,'១ខ',1,1,3,'2025-04-12 08:26:25',0,1,'active',9),(13,'១គ',1,1,5,'2025-04-19 08:42:27',0,1,'active',9);

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
  PRIMARY KEY (`semester_id`),
  UNIQUE KEY `semester_name` (`semester_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_semester` */

insert  into `tbl_semester`(`semester_id`,`semester_name`,`create_date`,`isDeleted`) values (1,'ឆមាសទី១','2025-02-02 09:51:24',0),(2,'ឆមាសទី២','2025-02-02 09:51:24',0);

/*Table structure for table `tbl_semester_exam_subjects` */

DROP TABLE IF EXISTS `tbl_semester_exam_subjects`;

CREATE TABLE `tbl_semester_exam_subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(10) unsigned NOT NULL,
  `semester_id` int(10) unsigned NOT NULL,
  `assign_subject_grade_id` int(10) unsigned NOT NULL,
  `monthly_ids` varchar(255) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `class_id` (`class_id`),
  KEY `semester_id` (`semester_id`),
  KEY `assign_subject_grade_id` (`assign_subject_grade_id`),
  CONSTRAINT `tbl_semester_exam_subjects_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `tbl_classroom` (`class_id`),
  CONSTRAINT `tbl_semester_exam_subjects_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `tbl_semester` (`semester_id`),
  CONSTRAINT `tbl_semester_exam_subjects_ibfk_3` FOREIGN KEY (`assign_subject_grade_id`) REFERENCES `tbl_assign_subject_grade` (`assign_subject_grade_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_semester_exam_subjects` */

insert  into `tbl_semester_exam_subjects`(`id`,`class_id`,`semester_id`,`assign_subject_grade_id`,`monthly_ids`,`create_date`,`isDeleted`) values (49,10,1,99,'1,2,3','2025-04-26 01:37:04',0),(50,10,1,103,'1,2,3','2025-04-26 01:37:05',0);

/*Table structure for table `tbl_student_info` */

DROP TABLE IF EXISTS `tbl_student_info`;

CREATE TABLE `tbl_student_info` (
  `student_id` int(10) unsigned NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `dob` date NOT NULL,
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

insert  into `tbl_student_info`(`student_id`,`student_name`,`gender`,`dob`,`pob_address`,`current_address`,`father_name`,`father_job`,`father_phone`,`mother_name`,`mother_job`,`mother_phone`,`family_status`,`status`,`isDeleted`) values (1001,'សុខ វិសាល','male','2016-05-15','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','សុខ សុភា','កសិករ','០៩៧៨៨៨៧៧៧','យិន សុគន្ធា','មេផ្ទះ','០៩៧៨៨៨៧៧៦','level1','active',0),(1002,'ម៉ៅ សុគន្ធា','female','2016-06-20','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ម៉ៅ វិបុល','អ្នកលក់ដូរ','០៩៧៨៨៨៧៧៨','សែម សុជាតា','អ្នកលក់ដូរ','០៩៧៨៨៨៧៧៩','level2','active',0),(1003,'គង់ រតនា','male','2016-07-10','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','គង់ សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៧៨០','ស៊ុន សុភាព','គ្រូបង្រៀន','០៩៧៨៨៨៧៨១','level1','active',0),(1004,'លី សុវណ្ណារិទ្ធ','female','2016-07-15','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','លី សុវណ្ណ','អ្នកជំនួញ','០៩៧៨៨៨៧៨២','ជា សុខា','អ្នកជំនួញ','០៩៧៨៨៨៧៨៣','level2','active',0),(1005,'ហេង វិរៈ','male','2016-08-01','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ហេង វិចិត្រ','កសិករ','០៩៧៨៨៨៧៨៤','គង់ សុខនី','កសិករ','០៩៧៨៨៨៧៨៥','level1','active',0),(1006,'ណុប សុភ័ក្រ','female','2016-08-15','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ណុប វាសនា','គ្រូបង្រៀន','០៩៧៨៨៨៧៨៦','លឹម សុខា','គ្រូបង្រៀន','០៩៧៨៨៨៧៨៧','level2','active',0),(1007,'ឡុង វិសិដ្ឋ','male','2016-09-01','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ឡុង សំអាត','អ្នកជំនួញ','០៩៧៨៨៨៧៨៨','សុខ សុភា','អ្នកជំនួញ','០៩៧៨៨៨៧៨៩','level1','active',0),(1008,'គឹម សុវណ្ណារ៉ា','female','2016-09-15','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','គឹម វិសាល','កសិករ','០៩៧៨៨៨៧៩០','ម៉ម សុគន្ធា','កសិករ','០៩៧៨៨៨៧៩១','level2','active',0),(1009,'ជា វិរៈបុត្រ','male','2016-10-01','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ជា សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៧៩២','លី សុជាតា','គ្រូបង្រៀន','០៩៧៨៨៨៧៩៣','level1','active',0),(1010,'ស៊ាន សុវណ្ណារិទ្ធ','female','2016-10-15','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ស៊ាន វិបុល','អ្នកជំនួញ','០៩៧៨៨៨៧៩៤','ហុង សុខា','អ្នកជំនួញ','០៩៧៨៨៨៧៩៥','level2','active',0),(1011,'ហុង វិរៈធី','male','2016-11-01','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ហុង សំណាង','កសិករ','០៩៧៨៨៨៧៩៦','គឹម សុភា','កសិករ','០៩៧៨៨៨៧៩៧','level1','active',0),(1012,'គឹម សុគន្ធា','female','2016-11-15','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','គឹម វិបុល','អ្នកជំនួញ','០៩៧៨៨៨៧៩៨','ហេង សុជាតា','អ្នកជំនួញ','០៩៧៨៨៨៧៩៩','level2','active',0),(1013,'ឡុង វិរៈបុត្រ','male','2016-12-01','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ឡុង សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨០០','ស៊ាន សុភាព','គ្រូបង្រៀន','០៩៧៨៨៨៨០១','level1','active',0),(1014,'ស៊ុន សុវណ្ណារិទ្ធ','female','2016-12-15','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ស៊ុន វិបុល','កសិករ','០៩៧៨៨៨៨០២','ឡុង សុខា','កសិករ','០៩៧៨៨៨៨០៣','level2','active',0),(1015,'គង់ វិរៈធី','male','2017-01-01','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','គង់ សំណាង','អ្នកជំនួញ','០៩៧៨៨៨៨០៤','គឹម សុគន្ធា','អ្នកជំនួញ','០៩៧៨៨៨៨០៥','level1','active',0),(1016,'ហេង សុគន្ធា','female','2017-01-15','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ហេង វិបុល','គ្រូបង្រៀន','០៩៧៨៨៨៨០៦','ឡុង សុជាតា','គ្រូបង្រៀន','០៩៧៨៨៨៨០៧','level2','active',0),(1017,'ណុប វិរៈបុត្រ','male','2017-02-01','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ណុប សំណាង','កសិករ','០៩៧៨៨៨៨០៨','ស៊ុន សុភា','កសិករ','០៩៧៨៨៨៨០៩','level1','active',0),(1018,'លឹម សុវណ្ណារិទ្ធ','female','2017-02-15','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','លឹម វិបុល','អ្នកជំនួញ','០៩៧៨៨៨៨១០','គង់ សុខា','អ្នកជំនួញ','០៩៧៨៨៨៨១១','level2','active',0),(1019,'សុខ វិរៈធី','male','2017-03-01','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','សុខ សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨១២','ហេង សុភាព','គ្រូបង្រៀន','០៩៧៨៨៨៨១៣','level1','active',0),(1020,'ម៉ៅ សុគន្ធា','female','2017-03-15','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ម៉ៅ វិបុល','កសិករ','០៩៧៨៨៨៨១៤','ណុប សុខា','កសិករ','០៩៧៨៨៨៨១៥','level2','active',0),(1021,'ជា រតនា','male','2017-04-01','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ជា សុវណ្ណ','អ្នកលក់ដូរ','០៩៧៨៨៨៨១៦','លឹម សុគន្ធា','អ្នកលក់ដូរ','០៩៧៨៨៨៨១៧','level1','active',0),(1022,'យិន សុវណ្ណារិទ្ធ','female','2017-04-15','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','យិន វិបុល','គ្រូបង្រៀន','០៩៧៨៨៨៨១៨','សុខ សុជាតា','គ្រូបង្រៀន','០៩៧៨៨៨៨១៩','level2','active',0),(1023,'សែម វិរៈធី','male','2017-05-01','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','សែម សំណាង','កសិករ','០៩៧៨៨៨៨២០','ម៉ៅ សុភាព','កសិករ','០៩៧៨៨៨៨២១','level1','active',0),(1024,'ស៊ុន សុគន្ធា','female','2017-05-15','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ស៊ុន វិចិត្រ','អ្នកជំនួញ','០៩៧៨៨៨៨២២','ស៊ាន សុខា','អ្នកជំនួញ','០៩៧៨៨៨៨២៣','level2','active',0),(1025,'ជា វិសិដ្ឋ','male','2017-06-01','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ជា វាសនា','គ្រូបង្រៀន','០៩៧៨៨៨៨២៤','គង់ សុភា','គ្រូបង្រៀន','០៩៧៨៨៨៨២៥','level1','active',0),(1026,'លី សុភ័ក្រ','female','2017-06-15','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','លី សំអាត','កសិករ','០៩៧៨៨៨៨២៦','ណុប សុជាតា','កសិករ','០៩៧៨៨៨៨២៧','level2','active',0),(1027,'គង់ សុភ័ក្រ','male','2017-07-01','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','គង់ វិសាល','អ្នកជំនួញ','០៩៧៨៨៨៨២៨','លឹម សុភា','អ្នកជំនួញ','០៩៧៨៨៨៨២៩','level1','active',0),(1028,'ហេង សុវណ្ណារ៉ា','female','2017-07-15','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ហេង សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨៣០','ជា សុគន្ធា','គ្រូបង្រៀន','០៩៧៨៨៨៨៣១','level2','active',0),(1029,'គឹម វិរៈបុត្រ','male','2017-08-01','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','គឹម សំណាង','កសិករ','០៩៧៨៨៨៨៣២','យិន សុភាព','កសិករ','០៩៧៨៨៨៨៣៣','level1','active',0),(1030,'ណុប សុវណ្ណារិទ្ធ','female','2017-08-15','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ណុប វិបុល','អ្នកជំនួញ','០៩៧៨៨៨៨៣៤','សែម សុខា','អ្នកជំនួញ','០៩៧៨៨៨៨៣៥','level2','active',0),(1031,'ឡុង រតនា','male','2017-09-01','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ឡុង សុវណ្ណ','គ្រូបង្រៀន','០៩៧៨៨៨៨៣៦','ស៊ុន សុគន្ធា','គ្រូបង្រៀន','០៩៧៨៨៨៨៣៧','level1','active',0),(1032,'គឹម សុភ័ក្រ','female','2017-09-15','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','គឹម វិចិត្រ','កសិករ','០៩៧៨៨៨៨៣៨','ជា សុជាតា','កសិករ','០៩៧៨៨៨៨៣៩','level2','active',0),(1033,'សុខ វិសិដ្ឋ','male','2017-10-01','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','សុខ វាសនា','អ្នកជំនួញ','០៩៧៨៨៨៨៤០','លី សុភា','អ្នកជំនួញ','០៩៧៨៨៨៨៤១','level1','active',0),(1034,'ម៉ៅ សុវណ្ណារ៉ា','female','2017-10-15','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ម៉ៅ សំអាត','គ្រូបង្រៀន','០៩៧៨៨៨៨៤២','គង់ សុជាតា','គ្រូបង្រៀន','០៩៧៨៨៨៨៤៣','level2','active',0),(1035,'ស៊ាន វិរៈធី','male','2017-11-01','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ស៊ាន វិសាល','កសិករ','០៩៧៨៨៨៨៤៤','ហេង សុភាព','កសិករ','០៩៧៨៨៨៨៤៥','level1','active',0),(1036,'ហុង សុគន្ធា','female','2017-11-15','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ហុង សំណាង','អ្នកជំនួញ','០៩៧៨៨៨៨៤៦','គឹម សុខា','អ្នកជំនួញ','០៩៧៨៨៨៨៤៧','level2','active',0),(1037,'យិន វិរៈបុត្រ','male','2017-12-01','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','យិន សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨៤៨','ឡុង សុគន្ធា','គ្រូបង្រៀន','០៩៧៨៨៨៨៤៩','level1','active',0),(1038,'សែម សុវណ្ណារិទ្ធ','female','2017-12-15','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','សែម វិបុល','កសិករ','០៩៧៨៨៨៨៥០','ណុប សុភា','កសិករ','០៩៧៨៨៨៨៥១','level2','active',0),(1039,'ស៊ុន សុភ័ក្រ','male','2018-01-01','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ស៊ុន វិចិត្រ','អ្នកជំនួញ','០៩៧៨៨៨៨៥២','ស៊ុន សុជាតា','អ្នកជំនួញ','០៩៧៨៨៨៨៥៣','level1','active',0),(1040,'ស៊ាន សុភ័ក្រ','female','2018-01-15','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ស៊ាន សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨៥៤','ជា សុភាព','គ្រូបង្រៀន','០៩៧៨៨៨៨៥៥','level2','active',0),(1041,'ជា វិសាល','male','2018-02-01','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ជា សំណាង','កសិករ','០៩៧៨៨៨៨៥៦','លី សុគន្ធា','កសិករ','០៩៧៨៨៨៨៥៧','level1','active',0),(1042,'លី សុវណ្ណារ៉ា','female','2018-02-15','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','លី វិបុល','អ្នកជំនួញ','០៩៧៨៨៨៨៥៨','គង់ សុជាតា','អ្នកជំនួញ','០៩៧៨៨៨៨៥៩','level2','active',0),(1043,'គង់ វិសាល','male','2018-03-01','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','គង់ វាសនា','គ្រូបង្រៀន','០៩៧៨៨៨៨៦០','ហេង សុភា','គ្រូបង្រៀន','០៩៧៨៨៨៨៦១','level1','active',0),(1044,'ហេង សុភ័ក្រ','female','2018-03-15','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ហេង សំអាត','កសិករ','០៩៧៨៨៨៨៦២','គឹម សុខា','កសិករ','០៩៧៨៨៨៨៦៣','level2','active',0),(1045,'គឹម រតនា','male','2018-04-01','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','គឹម វិសាល','អ្នកជំនួញ','០៩៧៨៨៨៨៦៤','ណុប សុគន្ធា','អ្នកជំនួញ','០៩៧៨៨៨៨៦៥','level1','graduate',0),(1046,'ណុប សុវណ្ណារ៉ា','female','2018-04-15','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ណុប សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨៦៦','ឡុង សុជាតា','គ្រូបង្រៀន','០៩៧៨៨៨៨៦៧','level2','graduate',0),(1047,'ឡុង វិរៈធី','male','2018-05-01','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ឡុង សុវណ្ណ','កសិករ','០៩៧៨៨៨៨៦៨','ស៊ុន សុភា','កសិករ','០៩៧៨៨៨៨៦៩','level1','graduate',0),(1048,'ស៊ុន សុវណ្ណារ៉ា','female','2018-05-15','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ស៊ុន វិចិត្រ','អ្នកជំនួញ','០៩៧៨៨៨៨៧០','ស៊ាន សុខា','អ្នកជំនួញ','០៩៧៨៨៨៨៧១','level2','graduate',0),(1049,'ស៊ាន វិសិដ្ឋ','male','2018-06-01','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ស៊ាន វាសនា','គ្រូបង្រៀន','០៩៧៨៨៨៨៧២','ជា សុភាព','គ្រូបង្រៀន','០៩៧៨៨៨៨៧៣','level1','suspend',0),(1050,'ហុង សុវណ្ណារិទ្ធ','female','2018-06-15','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ហុង សំអាត','កសិករ','០៩៧៨៨៨៨៧៤','លី សុជាតា','កសិករ','០៩៧៨៨៨៨៧៥','level2','suspend',0),(1051,'យិន វិសាល','male','2018-07-01','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','យិន វិសាល','អ្នកជំនួញ','០៩៧៨៨៨៨៧៦','គង់ សុគន្ធា','អ្នកជំនួញ','០៩៧៨៨៨៨៧៧','level1','suspend',0),(1052,'សែម សុគន្ធា','female','2018-07-15','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','សែម សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨៧៨','ហេង សុជាតា','គ្រូបង្រៀន','០៩៧៨៨៨៨៧៩','level2','suspend',0),(1053,'ម៉ៅ វិរៈបុត្រ','male','2018-08-01','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ម៉ៅ វិចិត្រ','កសិករ','០៩៧៨៨៨៨៨០','គឹម សុភា','កសិករ','០៩៧៨៨៨៨៨១','level1','inactive',0),(1054,'សុខ សុភ័ក្រ','female','2018-08-15','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','សុខ វាសនា','អ្នកជំនួញ','០៩៧៨៨៨៨៨២','ណុប សុខា','អ្នកជំនួញ','០៩៧៨៨៨៨៨៣','level2','inactive',0),(1055,'ជា សុភ័ក្រ','male','2018-09-01','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ជា សំអាត','គ្រូបង្រៀន','០៩៧៨៨៨៨៨៤','ឡុង សុគន្ធា','គ្រូបង្រៀន','០៩៧៨៨៨៨៨៥','level1','inactive',0),(1056,'លី សុវណ្ណារិទ្ធ','female','2018-09-15','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','លី វិសាល','កសិករ','០៩៧៨៨៨៨៨៦','ស៊ុន សុជាតា','កសិករ','០៩៧៨៨៨៨៨៧','level2','inactive',0),(1057,'គង់ វិសិដ្ឋ','male','2018-10-01','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','គង់ វាសនា','អ្នកជំនួញ','០៩៧៨៨៨៨៨៨','ស៊ាន សុភា','អ្នកជំនួញ','០៩៧៨៨៨៨៨៩','level1','active',0),(1058,'ហេង សុវណ្ណារិទ្ធ','female','2018-10-15','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ហេង សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨៩០','ជា សុជាតា','គ្រូបង្រៀន','០៩៧៨៨៨៨៩១','level2','active',0),(1059,'គឹម រតនា','male','2018-11-01','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','គឹម សំអាត','កសិករ','០៩៧៨៨៨៨៩២','លី សុភាព','កសិករ','០៩៧៨៨៨៨៩៣','level1','active',0),(1060,'ណុប សុគន្ធា','female','2018-11-15','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ណុប វិបុល','អ្នកជំនួញ','០៩៧៨៨៨៨៩៤','គង់ សុខា','អ្នកជំនួញ','០៩៧៨៨៨៨៩៥','level2','active',0),(1061,'ឡុង វិសាល','male','2018-12-01','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ឡុង វាសនា','គ្រូបង្រៀន','០៩៧៨៨៨៨៩៦','ហេង សុគន្ធា','គ្រូបង្រៀន','០៩៧៨៨៨៨៩៧','level1','active',0),(1062,'ស៊ុន សុវណ្ណារ៉ា','female','2018-12-15','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ស៊ុន សំណាង','កសិករ','០៩៧៨៨៨៨៩៨','គឹម សុជាតា','កសិករ','០៩៧៨៨៨៨៩៩','level2','graduate',0),(1063,'ស៊ាន វិរៈបុត្រ','male','2019-01-01','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ស៊ាន វិចិត្រ','អ្នកជំនួញ','០៩៧៨៨៨៩០០','ណុប សុភា','អ្នកជំនួញ','០៩៧៨៨៨៩០១','level1','graduate',0),(1064,'ហុង សុភ័ក្រ','female','2019-01-15','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ហុង សំអាត','គ្រូបង្រៀន','០៩៧៨៨៨៩០២','ឡុង សុខា','គ្រូបង្រៀន','០៩៧៨៨៨៩០៣','level2','graduate',0),(1065,'យិន វិសិដ្ឋ','male','2019-02-01','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','យិន វិសាល','កសិករ','០៩៧៨៨៨៩០៤','ស៊ុន សុគន្ធា','កសិករ','០៩៧៨៨៨៩០៥','level1','suspend',0),(1066,'សែម សុវណ្ណារិទ្ធ','female','2019-02-15','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','សែម វិបុល','អ្នកជំនួញ','០៩៧៨៨៨៩០៦','ស៊ាន សុជាតា','អ្នកជំនួញ','០៩៧៨៨៨៩០៧','level2','suspend',0),(1067,'ម៉ៅ វិសាល','male','2019-03-01','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ម៉ៅ វាសនា','គ្រូបង្រៀន','០៩៧៨៨៨៩០៨','ជា សុភាព','គ្រូបង្រៀន','០៩៧៨៨៨៩០៩','level1','suspend',0),(1068,'សុខ សុវណ្ណារ៉ា','female','2019-03-15','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','សុខ សំណាង','កសិករ','០៩៧៨៨៨៩១០','លី សុជាតា','កសិករ','០៩៧៨៨៨៩១១','level2','inactive',0),(1069,'ជា វិរៈធី','male','2019-04-01','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ជា សំអាត','អ្នកជំនួញ','០៩៧៨៨៨៩១២','គង់ សុគន្ធា','អ្នកជំនួញ','០៩៧៨៨៨៩១៣','level1','inactive',0),(1070,'លី សុគន្ធា','female','2019-04-15','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','លី វិចិត្រ','គ្រូបង្រៀន','០៩៧៨៨៨៩១៤','ហេង សុខា','គ្រូបង្រៀន','០៩៧៨៨៨៩១៥','level2','inactive',0);

/*Table structure for table `tbl_student_monthly_score` */

DROP TABLE IF EXISTS `tbl_student_monthly_score`;

CREATE TABLE `tbl_student_monthly_score` (
  `student_monthly_score_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `classroom_subject_monthly_score_id` int(10) unsigned NOT NULL,
  `score` float NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`student_monthly_score_id`),
  KEY `student_id` (`student_id`),
  KEY `classroom_subject_monthly_score_id` (`classroom_subject_monthly_score_id`),
  CONSTRAINT `fk_monthly_score_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_student_info` (`student_id`),
  CONSTRAINT `fk_student_monthly_classroom_subject` FOREIGN KEY (`classroom_subject_monthly_score_id`) REFERENCES `classroom_subject_monthly_score` (`classroom_subject_monthly_score_id`) ON DELETE CASCADE,
  CONSTRAINT `tbl_student_monthly_score_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `tbl_student_info` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1093 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_student_monthly_score` */

insert  into `tbl_student_monthly_score`(`student_monthly_score_id`,`student_id`,`classroom_subject_monthly_score_id`,`score`,`create_date`,`isDeleted`) values (1061,1001,60,10,'2025-04-08 16:45:59',0),(1062,1001,61,8,'2025-04-08 16:46:14',0),(1063,1001,62,9,'2025-04-08 16:46:16',0),(1064,1001,63,10,'2025-04-08 16:46:17',0),(1065,1001,64,8,'2025-04-08 16:46:22',0),(1066,1001,65,9,'2025-04-08 16:46:24',0),(1067,1003,60,8,'2025-04-17 20:54:02',0),(1068,1003,61,10,'2025-04-17 20:54:07',0),(1069,1003,62,9,'2025-04-17 20:54:09',0),(1070,1001,66,8,'2025-04-22 15:47:27',0),(1071,1001,67,8,'2025-04-22 15:47:28',0),(1072,1001,68,9,'2025-04-22 15:47:30',0),(1073,1001,69,7,'2025-04-22 15:47:31',0),(1074,1001,70,6,'2025-04-22 15:47:32',0),(1075,1001,71,7,'2025-04-22 15:47:37',0),(1076,1001,72,8,'2025-04-22 15:47:38',0),(1077,1001,73,6,'2025-04-22 15:47:39',0),(1078,1003,66,8,'2025-04-22 16:34:43',0),(1079,1003,67,7,'2025-04-22 16:34:45',0),(1080,1003,68,8,'2025-04-22 16:34:45',0),(1081,1003,69,9,'2025-04-22 16:34:46',0),(1082,1003,70,5,'2025-04-22 16:34:47',0),(1083,1003,71,6,'2025-04-22 16:34:49',0),(1084,1003,72,7,'2025-04-22 16:34:50',0),(1085,1003,73,6,'2025-04-22 16:34:52',0),(1086,1001,74,8,'2025-04-23 15:02:12',0),(1087,1001,75,5,'2025-04-23 15:02:14',0),(1088,1001,76,9,'2025-04-23 19:14:35',0),(1089,1001,77,8,'2025-04-23 19:14:36',0),(1090,1003,63,8,'2025-04-25 17:33:00',0),(1091,1003,64,7,'2025-04-25 17:33:02',0),(1092,1003,65,8,'2025-04-25 17:33:03',0);

/*Table structure for table `tbl_student_semester_score` */

DROP TABLE IF EXISTS `tbl_student_semester_score`;

CREATE TABLE `tbl_student_semester_score` (
  `student_semester_score_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `semester_exam_subject_id` int(11) NOT NULL,
  `score` float NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`student_semester_score_id`),
  KEY `student_id` (`student_id`),
  KEY `semester_exam_subject_id` (`semester_exam_subject_id`),
  CONSTRAINT `semester_exam_subject_id` FOREIGN KEY (`semester_exam_subject_id`) REFERENCES `tbl_semester_exam_subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tbl_student_semester_score_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `tbl_student_info` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_student_semester_score` */

insert  into `tbl_student_semester_score`(`student_semester_score_id`,`student_id`,`semester_exam_subject_id`,`score`,`create_date`,`isDeleted`) values (32,1001,49,7,'2025-04-26 01:37:41',0),(33,1001,50,8,'2025-04-26 01:37:42',0),(34,1003,49,9,'2025-04-26 01:37:47',0),(35,1003,50,10,'2025-04-26 01:37:48',0);

/*Table structure for table `tbl_study` */

DROP TABLE IF EXISTS `tbl_study`;

CREATE TABLE `tbl_study` (
  `study_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `year_study_id` int(10) unsigned NOT NULL,
  `enrollment_date` date NOT NULL DEFAULT curdate(),
  `status` varchar(50) DEFAULT 'active',
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`study_id`),
  KEY `student_id` (`student_id`),
  KEY `class_id` (`class_id`),
  KEY `year_study_id` (`year_study_id`),
  CONSTRAINT `fk_study_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classroom` (`class_id`),
  CONSTRAINT `fk_study_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_student_info` (`student_id`),
  CONSTRAINT `fk_study_year` FOREIGN KEY (`year_study_id`) REFERENCES `tbl_year_study` (`year_study_id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_study` */

insert  into `tbl_study`(`study_id`,`student_id`,`class_id`,`year_study_id`,`enrollment_date`,`status`,`create_date`,`isDeleted`) values (70,1001,10,1,'2025-04-08','active','2025-04-08 16:35:57',0),(74,1003,10,1,'2025-04-10','active','2025-04-10 18:52:57',0),(75,1015,10,1,'2025-04-10','active','2025-04-10 18:52:57',0),(76,1043,10,1,'2025-04-10','active','2025-04-10 18:52:57',0),(77,1057,10,1,'2025-04-17','active','2025-04-17 20:54:41',0),(78,1027,10,1,'2025-04-17','active','2025-04-17 20:54:41',0);

/*Table structure for table `tbl_subject` */

DROP TABLE IF EXISTS `tbl_subject`;

CREATE TABLE `tbl_subject` (
  `subject_code` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(255) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`subject_code`),
  UNIQUE KEY `unique_subject_name` (`subject_name`),
  UNIQUE KEY `subject_name` (`subject_name`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_subject` */

insert  into `tbl_subject`(`subject_code`,`subject_name`,`create_date`,`isDeleted`) values (1,'គណិតវិទ្យា','2025-02-24 11:59:00',0),(2,'ភាសាខ្មែរ','2025-02-24 11:59:08',0),(3,'វិទ្យាសាស្ត្រ','2025-02-24 17:58:35',0),(4,'សិក្សាសង្គម','2025-02-24 17:58:44',0),(5,'អប់រំកាយ-សុខភាពកីឡា','2025-02-24 17:59:07',0),(6,'អប់រំបំណិនជីវិត','2025-02-24 17:59:20',0),(7,'ភាសាបរទេស','2025-02-24 17:59:34',0),(8,'សម្ថភាពស្ដាប់','2025-02-27 23:58:11',0),(9,'សម្ថភាពសរសេរ','2025-02-27 23:58:20',0),(10,'សម្ថភាពអាន','2025-02-27 23:58:29',0),(11,'សម្ថភាពនិយាយ','2025-02-27 23:58:37',0),(12,'ចំនួន','2025-02-27 23:58:41',0),(13,'រង្វាស់រង្វាល់','2025-02-27 23:58:50',0),(14,'ធរណីមាត្រ','2025-02-27 23:58:57',0),(15,'ពីជគណិត','2025-02-27 23:59:04',0),(16,'ស្ថិតិ','2025-02-27 23:59:11',0),(17,'រូបវិទ្យា','2025-02-27 23:59:19',0),(18,'គីមីវិទ្យា','2025-02-27 23:59:26',0),(19,'ជីវវិទ្យា','2025-02-27 23:59:36',0),(20,'ផែនដី-បរិស្ថានវិទ្យា','2025-02-27 23:59:55',0),(21,'សីលធម៌-ពលរដ្ឋវិទ្យា','2025-02-28 00:00:11',0),(22,'ភូមិវិទ្យា','2025-02-28 00:00:20',0),(23,'ប្រវត្តិវិទ្យា','2025-02-28 00:00:32',0),(24,'គេហវិទ្យា-អប់រំសិល្បៈ','2025-02-28 00:00:55',0),(25,'អប់រំកាយ-កីឡា','2025-02-28 00:01:08',0),(26,'សុខភាព-អនាម័យ','2025-02-28 00:01:24',0);

/*Table structure for table `tbl_user` */

DROP TABLE IF EXISTS `tbl_user`;

CREATE TABLE `tbl_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `status` int(2) DEFAULT 1,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_user` */

insert  into `tbl_user`(`user_id`,`full_name`,`user_name`,`password`,`phone`,`user_type`,`status`,`created_date`,`isDeleted`) values (1,'ពៅ សំ','khmersr','$2y$10$LljfIoNdd4ua088RMl9NnOowB8a.ljbgQd6wD.hBX0jnNNfiGXE1q','098582828','admin',1,'0000-00-00 00:00:00',0),(2,'Norea PMS','admin_norea','$2y$10$1Ow1S23GKdkv1uR5ZS.seOO0w0.t4AMkZyOmJm6I3lmnMOsyVLaQa','0123456789','super_admin',1,'2025-02-02 11:41:44',0),(3,'នី ឡេនីន','lenin','$2y$10$F05Y.0ET3h7BBJHwmqPyuuu7nyhKLnmwtAEhyyIb7BlLibt.Bt04S','054359273','user',1,'2025-02-02 11:42:01',0),(4,'ផល សុផាត','sophat','$2y$10$y7fU6kwX40R/wYZUu9zrNe5fFQ1eQKNn4g0M/R8AKpwo6eiQpl/YO','0123456','user',1,'2025-02-11 23:15:10',0),(5,'ឃុត ទីណា','tina','$2y$10$yegGusbbXzg0EQocY4SoCeFVncn31a1WzcDGaPVZ4nbFEfw6KVohK','099887766','user',1,'2025-04-06 15:08:14',0);

/*Table structure for table `tbl_year_study` */

DROP TABLE IF EXISTS `tbl_year_study`;

CREATE TABLE `tbl_year_study` (
  `year_study_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `year_study` varchar(255) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`year_study_id`),
  UNIQUE KEY `year_study` (`year_study`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_year_study` */

insert  into `tbl_year_study`(`year_study_id`,`year_study`,`create_date`,`isDeleted`) values (1,'2025-2026','2025-02-02 09:49:37',0),(2,'2026-2027','2025-02-02 09:49:37',0),(3,'2027-2028','2025-02-02 09:49:37',0),(4,'2028-2029','2025-04-08 17:37:54',0),(5,'2029-2030','2025-04-08 17:43:17',0);

/* Procedure structure for procedure `CalculateFinalSemesterAverage` */

/*!50003 DROP PROCEDURE IF EXISTS  `CalculateFinalSemesterAverage` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateFinalSemesterAverage`(
    IN p_student_id INT,
    IN p_semester_id INT,
    IN p_class_id INT,
    IN p_monthly_ids VARCHAR(255)
)
BEGIN
    DECLARE v_monthly_avg DECIMAL(5,2);
    DECLARE v_semester_exam_avg DECIMAL(5,2);
    
    -- Calculate monthly average using the exact same method as CalculateStudentMonthlyAverage
    WITH monthly_scores AS (
        SELECT 
            sms.student_id,
            csms.monthly_id,
            AVG(sms.score) as monthly_average
        FROM tbl_student_monthly_score sms
        JOIN classroom_subject_monthly_score csms 
            ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
        WHERE sms.student_id = p_student_id
        AND csms.class_id = p_class_id
        AND FIND_IN_SET(csms.monthly_id, p_monthly_ids)
        AND sms.isDeleted = 0
        GROUP BY sms.student_id, csms.monthly_id
    )
    SELECT AVG(monthly_average) INTO v_monthly_avg
    FROM monthly_scores;
    
    -- Calculate semester exam average
    SELECT 
        AVG(sss.score) INTO v_semester_exam_avg
    FROM tbl_student_semester_score sss
    JOIN tbl_semester_exam_subjects ses 
        ON sss.semester_exam_subject_id = ses.id
    WHERE sss.student_id = p_student_id
    AND ses.semester_id = p_semester_id
    AND ses.class_id = p_class_id
    AND sss.isDeleted = 0;
    
    -- Calculate final average
    SELECT 
        p_student_id as student_id,
        v_monthly_avg as monthly_average,
        v_semester_exam_avg as semester_exam_average,
        (v_monthly_avg + v_semester_exam_avg) / 2 as final_semester_average;
END */$$
DELIMITER ;

/* Procedure structure for procedure `CalculateMonthlyAverage` */

/*!50003 DROP PROCEDURE IF EXISTS  `CalculateMonthlyAverage` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateMonthlyAverage`(
    IN p_student_id INT,
    IN p_monthly_ids VARCHAR(255)  -- Comma-separated list of selected month IDs
)
BEGIN
    -- First, calculate average for each month separately
    WITH monthly_averages AS (
        SELECT 
            csms.monthly_id,
            m.month_name,
            COUNT(DISTINCT csms.assign_subject_grade_id) as subjects_in_month,
            AVG(sms.score) as month_average,
            SUM(sms.score) as month_total
        FROM tbl_student_monthly_score sms
        JOIN classroom_subject_monthly_score csms 
            ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
        JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
        WHERE sms.student_id = p_student_id
        AND FIND_IN_SET(csms.monthly_id, p_monthly_ids)
        AND sms.isDeleted = 0
        AND csms.isDeleted = 0
        GROUP BY csms.monthly_id, m.month_name
    )
    -- Then calculate the overall average
    SELECT 
        AVG(month_average) as overall_average,
        COUNT(DISTINCT monthly_id) as months_counted,
        SUM(subjects_in_month) as total_subjects,
        GROUP_CONCAT(
            CONCAT(month_name, ' (', subjects_in_month, ' subjects, avg: ', ROUND(month_average, 2), ')')
            ORDER BY monthly_id
        ) as monthly_details,
        SUM(month_total) as total_score
    FROM monthly_averages;
END */$$
DELIMITER ;

/* Procedure structure for procedure `CalculateSemesterScore` */

/*!50003 DROP PROCEDURE IF EXISTS  `CalculateSemesterScore` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateSemesterScore`(
    IN p_student_id INT,
    IN p_class_id INT,
    IN p_semester_id INT,
    IN p_monthly_ids VARCHAR(255)
)
BEGIN
    SELECT 
        sms.student_id,
        si.student_name,
        AVG(sms.score) as monthly_average,
        COUNT(DISTINCT csms.monthly_id) as months_counted,
        (
            SELECT AVG(score) 
            FROM tbl_student_semester_exam_scores 
            WHERE student_id = sms.student_id 
            AND class_id = p_class_id 
            AND semester_id = p_semester_id 
            AND isDeleted = 0
        ) as semester_exam_score,
        (
            SELECT COUNT(DISTINCT semester_exam_subject_id) 
            FROM tbl_student_semester_exam_scores 
            WHERE student_id = sms.student_id 
            AND class_id = p_class_id 
            AND semester_id = p_semester_id 
            AND isDeleted = 0
        ) as exam_subjects_count,
        (
            (AVG(sms.score) + (
                SELECT AVG(score) 
                FROM tbl_student_semester_exam_scores 
                WHERE student_id = sms.student_id 
                AND class_id = p_class_id 
                AND semester_id = p_semester_id 
                AND isDeleted = 0
            )) / 2
        ) as final_score
    FROM tbl_student_monthly_score sms
    JOIN classroom_subject_monthly_score csms 
        ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
    JOIN tbl_student_info si 
        ON sms.student_id = si.student_id
    WHERE sms.student_id = p_student_id
    AND csms.class_id = p_class_id
    AND FIND_IN_SET(csms.monthly_id, p_monthly_ids)
    AND sms.isDeleted = 0
    GROUP BY sms.student_id, si.student_name;
END */$$
DELIMITER ;

/* Procedure structure for procedure `CalculateStudentMonthlyAverage` */

/*!50003 DROP PROCEDURE IF EXISTS  `CalculateStudentMonthlyAverage` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateStudentMonthlyAverage`(
    IN p_student_id INT,
    IN p_class_id INT,
    IN p_monthly_ids VARCHAR(255)
)
BEGIN
    WITH monthly_scores AS (
        SELECT 
            sms.student_id,
            csms.monthly_id,
            ROUND(AVG(sms.score), 2) as monthly_average
        FROM tbl_student_monthly_score sms
        JOIN classroom_subject_monthly_score csms 
            ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
        WHERE sms.student_id = p_student_id
        AND csms.class_id = p_class_id
        AND FIND_IN_SET(csms.monthly_id, p_monthly_ids)
        AND sms.isDeleted = 0
        GROUP BY sms.student_id, csms.monthly_id
    )
    SELECT 
        student_id,
        ROUND(AVG(monthly_average), 2) as final_monthly_average,
        COUNT(DISTINCT monthly_id) as months_counted
    FROM monthly_scores
    GROUP BY student_id;
END */$$
DELIMITER ;

/* Procedure structure for procedure `CalculateStudentSemesterExamAverage` */

/*!50003 DROP PROCEDURE IF EXISTS  `CalculateStudentSemesterExamAverage` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateStudentSemesterExamAverage`(
    IN p_student_id INT,
    IN p_semester_id INT,
    IN p_class_id INT
)
BEGIN
    SELECT 
        sss.student_id,
        AVG(sss.score) as semester_exam_average,
        COUNT(DISTINCT ses.id) as subjects_counted
    FROM tbl_student_semester_score sss
    JOIN tbl_semester_exam_subjects ses 
        ON sss.semester_exam_subject_id = ses.id
    WHERE sss.student_id = p_student_id
    AND ses.semester_id = p_semester_id
    AND ses.class_id = p_class_id
    AND sss.isDeleted = 0
    GROUP BY sss.student_id;
END */$$
DELIMITER ;

/* Procedure structure for procedure `GetAvailableMonthsForSemester` */

/*!50003 DROP PROCEDURE IF EXISTS  `GetAvailableMonthsForSemester` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAvailableMonthsForSemester`(
    IN p_class_id INT,
    IN p_semester_id INT,
    IN p_assign_subject_grade_id INT
)
BEGIN
    -- ជ្រើសខែដែលមានពិន្ទុសិស្សរួចហើយ
    SELECT DISTINCT 
        m.monthly_id,
        m.month_name,
        COUNT(DISTINCT sms.student_id) as student_count,
        COUNT(DISTINCT csms.assign_subject_grade_id) as subject_count
    FROM tbl_monthly m
    JOIN classroom_subject_monthly_score csms ON m.monthly_id = csms.monthly_id
    JOIN tbl_student_monthly_score sms ON csms.classroom_subject_monthly_score_id = sms.classroom_subject_monthly_score_id
    WHERE csms.class_id = p_class_id
    AND csms.assign_subject_grade_id = p_assign_subject_grade_id
    AND sms.isDeleted = 0
    AND csms.isDeleted = 0
    GROUP BY m.monthly_id, m.month_name
    HAVING student_count > 0 AND subject_count > 0
    ORDER BY m.monthly_id;
END */$$
DELIMITER ;

/*Table structure for table `view_student_monthly_rankings` */

DROP TABLE IF EXISTS `view_student_monthly_rankings`;

/*!50001 DROP VIEW IF EXISTS `view_student_monthly_rankings` */;
/*!50001 DROP TABLE IF EXISTS `view_student_monthly_rankings` */;

/*!50001 CREATE TABLE  `view_student_monthly_rankings`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `monthly_id` int(10) unsigned ,
 `month_name` varchar(255) ,
 `subjects_count` bigint(21) ,
 `total_score` double ,
 `average_score` double ,
 `rank_in_class` bigint(21) ,
 `class_size` bigint(21) 
)*/;

/*Table structure for table `view_student_monthly_score_summary` */

DROP TABLE IF EXISTS `view_student_monthly_score_summary`;

/*!50001 DROP VIEW IF EXISTS `view_student_monthly_score_summary` */;
/*!50001 DROP TABLE IF EXISTS `view_student_monthly_score_summary` */;

/*!50001 CREATE TABLE  `view_student_monthly_score_summary`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `monthly_id` int(10) unsigned ,
 `month_name` varchar(255) ,
 `subjects_count` bigint(21) ,
 `total_score` double ,
 `avg_score` double ,
 `rank_in_class` bigint(21) 
)*/;

/*Table structure for table `view_student_monthly_summary` */

DROP TABLE IF EXISTS `view_student_monthly_summary`;

/*!50001 DROP VIEW IF EXISTS `view_student_monthly_summary` */;
/*!50001 DROP TABLE IF EXISTS `view_student_monthly_summary` */;

/*!50001 CREATE TABLE  `view_student_monthly_summary`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `monthly_id` int(10) unsigned ,
 `month_name` varchar(255) ,
 `subjects_count` bigint(21) ,
 `total_score` double ,
 `average_score` double 
)*/;

/*Table structure for table `vstudentmonthlyscorereport` */

DROP TABLE IF EXISTS `vstudentmonthlyscorereport`;

/*!50001 DROP VIEW IF EXISTS `vstudentmonthlyscorereport` */;
/*!50001 DROP TABLE IF EXISTS `vstudentmonthlyscorereport` */;

/*!50001 CREATE TABLE  `vstudentmonthlyscorereport`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `monthly_id` int(10) unsigned ,
 `month_name` varchar(255) ,
 `subject_scores` mediumtext 
)*/;

/*Table structure for table `vstudentmonthlyscorereportv2` */

DROP TABLE IF EXISTS `vstudentmonthlyscorereportv2`;

/*!50001 DROP VIEW IF EXISTS `vstudentmonthlyscorereportv2` */;
/*!50001 DROP TABLE IF EXISTS `vstudentmonthlyscorereportv2` */;

/*!50001 CREATE TABLE  `vstudentmonthlyscorereportv2`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `monthly_id` int(10) unsigned ,
 `month_name` varchar(255) ,
 `subject_code` int(10) unsigned ,
 `subject_name` varchar(255) ,
 `score` float 
)*/;

/*View structure for view view_student_monthly_rankings */

/*!50001 DROP TABLE IF EXISTS `view_student_monthly_rankings` */;
/*!50001 DROP VIEW IF EXISTS `view_student_monthly_rankings` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_monthly_rankings` AS select `s`.`student_id` AS `student_id`,`s`.`student_name` AS `student_name`,`s`.`class_id` AS `class_id`,`s`.`class_name` AS `class_name`,`s`.`monthly_id` AS `monthly_id`,`s`.`month_name` AS `month_name`,`s`.`subjects_count` AS `subjects_count`,`s`.`total_score` AS `total_score`,`s`.`average_score` AS `average_score`,rank() over ( partition by `s`.`class_id`,`s`.`monthly_id` order by `s`.`average_score` desc) AS `rank_in_class`,count(0) over ( partition by `s`.`class_id`,`s`.`monthly_id`) AS `class_size` from `view_student_monthly_summary` `s` */;

/*View structure for view view_student_monthly_score_summary */

/*!50001 DROP TABLE IF EXISTS `view_student_monthly_score_summary` */;
/*!50001 DROP VIEW IF EXISTS `view_student_monthly_score_summary` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_monthly_score_summary` AS select `sms`.`student_id` AS `student_id`,`si`.`student_name` AS `student_name`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`m`.`monthly_id` AS `monthly_id`,`m`.`month_name` AS `month_name`,count(distinct `asg`.`assign_subject_grade_id`) AS `subjects_count`,sum(`sms`.`score`) AS `total_score`,avg(`sms`.`score`) AS `avg_score`,rank() over ( partition by `c`.`class_id`,`m`.`monthly_id` order by avg(`sms`.`score`) desc) AS `rank_in_class` from (((((`tbl_student_monthly_score` `sms` join `tbl_student_info` `si` on(`sms`.`student_id` = `si`.`student_id`)) join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) join `tbl_classroom` `c` on(`csms`.`class_id` = `c`.`class_id`)) join `tbl_monthly` `m` on(`csms`.`monthly_id` = `m`.`monthly_id`)) join `tbl_assign_subject_grade` `asg` on(`csms`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) where `sms`.`isDeleted` = 0 group by `sms`.`student_id`,`si`.`student_name`,`c`.`class_id`,`c`.`class_name`,`m`.`monthly_id`,`m`.`month_name` */;

/*View structure for view view_student_monthly_summary */

/*!50001 DROP TABLE IF EXISTS `view_student_monthly_summary` */;
/*!50001 DROP VIEW IF EXISTS `view_student_monthly_summary` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_monthly_summary` AS select `sms`.`student_id` AS `student_id`,`si`.`student_name` AS `student_name`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`csms`.`monthly_id` AS `monthly_id`,`m`.`month_name` AS `month_name`,count(distinct `asg`.`assign_subject_grade_id`) AS `subjects_count`,sum(`sms`.`score`) AS `total_score`,avg(`sms`.`score`) AS `average_score` from (((((`tbl_student_monthly_score` `sms` join `tbl_student_info` `si` on(`sms`.`student_id` = `si`.`student_id`)) join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) join `tbl_classroom` `c` on(`csms`.`class_id` = `c`.`class_id`)) join `tbl_monthly` `m` on(`csms`.`monthly_id` = `m`.`monthly_id`)) join `tbl_assign_subject_grade` `asg` on(`csms`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) where `sms`.`isDeleted` = 0 group by `sms`.`student_id`,`si`.`student_name`,`c`.`class_id`,`c`.`class_name`,`csms`.`monthly_id`,`m`.`month_name` */;

/*View structure for view vstudentmonthlyscorereport */

/*!50001 DROP TABLE IF EXISTS `vstudentmonthlyscorereport` */;
/*!50001 DROP VIEW IF EXISTS `vstudentmonthlyscorereport` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vstudentmonthlyscorereport` AS select `si`.`student_id` AS `student_id`,`si`.`student_name` AS `student_name`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`m`.`monthly_id` AS `monthly_id`,`m`.`month_name` AS `month_name`,group_concat(concat(`sub`.`subject_name`,': ',`sms`.`score`) order by `sub`.`subject_name` ASC separator ', ') AS `subject_scores` from (((((((`tbl_student_info` `si` join `tbl_study` `st` on(`si`.`student_id` = `st`.`student_id` and `st`.`status` = 'active' and `st`.`isDeleted` = 0)) join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) join `tbl_student_monthly_score` `sms` on(`si`.`student_id` = `sms`.`student_id`)) join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) join `tbl_monthly` `m` on(`csms`.`monthly_id` = `m`.`monthly_id`)) join `tbl_assign_subject_grade` `asg` on(`csms`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) join `tbl_subject` `sub` on(`asg`.`subject_code` = `sub`.`subject_code`)) where `si`.`isDeleted` = 0 and `sms`.`isDeleted` = 0 group by `si`.`student_id`,`si`.`student_name`,`c`.`class_id`,`c`.`class_name`,`m`.`monthly_id`,`m`.`month_name` order by `si`.`student_name`,`m`.`monthly_id` */;

/*View structure for view vstudentmonthlyscorereportv2 */

/*!50001 DROP TABLE IF EXISTS `vstudentmonthlyscorereportv2` */;
/*!50001 DROP VIEW IF EXISTS `vstudentmonthlyscorereportv2` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vstudentmonthlyscorereportv2` AS select `si`.`student_id` AS `student_id`,`si`.`student_name` AS `student_name`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`m`.`monthly_id` AS `monthly_id`,`m`.`month_name` AS `month_name`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,coalesce(`sms`.`score`,NULL) AS `score` from (((((((`tbl_student_info` `si` join `tbl_study` `st` on(`si`.`student_id` = `st`.`student_id` and `st`.`status` = 'active' and `st`.`isDeleted` = 0)) join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) join `classroom_subject_monthly_score` `csms` on(`c`.`class_id` = `csms`.`class_id`)) join `tbl_monthly` `m` on(`csms`.`monthly_id` = `m`.`monthly_id`)) join `tbl_assign_subject_grade` `asg` on(`csms`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) join `tbl_subject` `sub` on(`asg`.`subject_code` = `sub`.`subject_code`)) left join `tbl_student_monthly_score` `sms` on(`si`.`student_id` = `sms`.`student_id` and `sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) where `si`.`isDeleted` = 0 and `csms`.`isDeleted` = 0 order by `si`.`student_name`,`m`.`monthly_id`,`sub`.`subject_name` */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
