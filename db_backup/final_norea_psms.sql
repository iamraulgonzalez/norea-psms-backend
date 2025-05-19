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
) ENGINE=InnoDB AUTO_INCREMENT=416 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `classroom_subject_monthly_score` */

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
) ENGINE=InnoDB AUTO_INCREMENT=242 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_assign_subject_grade` */

insert  into `tbl_assign_subject_grade`(`assign_subject_grade_id`,`grade_id`,`subject_code`,`create_date`,`isDeleted`) values (232,1,1,'2025-05-19 16:49:28',0),(233,1,2,'2025-05-19 16:49:28',0),(234,1,3,'2025-05-19 16:49:28',0),(235,1,4,'2025-05-19 16:49:28',0),(236,2,1,'2025-05-19 16:49:55',0),(237,2,2,'2025-05-19 16:49:55',0),(238,2,3,'2025-05-19 16:49:55',0),(239,2,4,'2025-05-19 16:49:55',0),(240,2,5,'2025-05-19 16:49:55',0),(241,1,1,'2025-05-19 16:50:16',1);

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
  `status` varchar(255) DEFAULT 'active',
  `num_students_in_class` int(11) NOT NULL DEFAULT 45 COMMENT 'Maximum number of students allowed in this class',
  PRIMARY KEY (`class_id`,`class_name`),
  KEY `fk_classroom_session` (`session_id`),
  KEY `fk_classroom_grade` (`grade_id`),
  KEY `fk_classroom_teacher` (`teacher_id`),
  CONSTRAINT `fk_classroom_grade` FOREIGN KEY (`grade_id`) REFERENCES `tbl_grade` (`grade_id`),
  CONSTRAINT `fk_classroom_session` FOREIGN KEY (`session_id`) REFERENCES `tbl_school_session` (`session_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_classroom_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `tbl_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_classroom` */

insert  into `tbl_classroom`(`class_id`,`class_name`,`grade_id`,`session_id`,`teacher_id`,`create_date`,`isDeleted`,`status`,`num_students_in_class`) values (10,'១ក',1,1,6,'2025-04-05 17:18:45',0,'active',45),(11,'២ក',2,1,NULL,'2025-04-05 17:24:22',0,'active',45),(12,'១ខ',1,1,NULL,'2025-04-12 08:26:25',0,'active',45),(13,'១គ',1,1,NULL,'2025-04-19 08:42:27',0,'active',45),(14,'៤ក',4,1,4,'2025-05-01 09:47:46',0,'active',45),(15,'៤ខ',4,1,7,'2025-05-01 20:41:12',0,'active',9),(16,'៥ក',5,1,NULL,'2025-05-06 15:31:52',0,'active',11),(17,'៥ខ',5,1,8,'2025-05-06 15:38:08',0,'active',9),(18,'៥គ',5,1,NULL,'2025-05-06 15:39:30',0,'active',9),(19,'៣ក',3,1,NULL,'2025-05-18 12:48:36',0,'active',9);

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_semester_exam_subjects` */

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

insert  into `tbl_student_info`(`student_id`,`student_name`,`gender`,`dob`,`pob_address`,`current_address`,`father_name`,`father_job`,`father_phone`,`mother_name`,`mother_job`,`mother_phone`,`family_status`,`status`,`isDeleted`) values (1001,'សុខ វិសាល','male','2016-05-15','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','សុខ សុភា','កសិករ','០៩៧៨៨៨៧៧៧','យិន សុគន្ធា','មេផ្ទះ','០៩៧៨៨៨៧៧៦','level1','active',0),(1002,'ម៉ៅ សុគន្ធា','female','2016-06-20','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ម៉ៅ វិបុល','អ្នកលក់ដូរ','០៩៧៨៨៨៧៧៨','សែម សុជាតា','អ្នកលក់ដូរ','០៩៧៨៨៨៧៧៩','level2','active',0),(1003,'គង់ រតនា','male','2016-07-10','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','គង់ សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៧៨០','ស៊ុន សុភាព','គ្រូបង្រៀន','០៩៧៨៨៨៧៨១','level1','active',0),(1004,'លី សុវណ្ណារិទ្ធ','female','2016-07-15','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','លី សុវណ្ណ','អ្នកជំនួញ','០៩៧៨៨៨៧៨២','ជា សុខា','អ្នកជំនួញ','០៩៧៨៨៨៧៨៣','level2','active',0),(1005,'ហេង វិរៈ','male','2016-08-01','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ហេង វិចិត្រ','កសិករ','០៩៧៨៨៨៧៨៤','គង់ សុខនី','កសិករ','០៩៧៨៨៨៧៨៥','level1','active',0),(1006,'ណុប សុភ័ក្រ','female','2016-08-15','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ណុប វាសនា','គ្រូបង្រៀន','០៩៧៨៨៨៧៨៦','លឹម សុខា','គ្រូបង្រៀន','០៩៧៨៨៨៧៨៧','level2','active',0),(1007,'ឡុង វិសិដ្ឋ','male','2016-09-01','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ឡុង សំអាត','អ្នកជំនួញ','០៩៧៨៨៨៧៨៨','សុខ សុភា','អ្នកជំនួញ','០៩៧៨៨៨៧៨៩','level1','active',0),(1008,'គឹម សុវណ្ណារ៉ា','female','2016-09-15','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','គឹម វិសាល','កសិករ','០៩៧៨៨៨៧៩០','ម៉ម សុគន្ធា','កសិករ','០៩៧៨៨៨៧៩១','level2','active',0),(1009,'ជា វិរៈបុត្រ','male','2016-10-01','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ជា សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៧៩២','លី សុជាតា','គ្រូបង្រៀន','០៩៧៨៨៨៧៩៣','level1','active',0),(1010,'ស៊ាន សុវណ្ណារិទ្ធ','female','2016-10-15','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ស៊ាន វិបុល','អ្នកជំនួញ','០៩៧៨៨៨៧៩៤','ហុង សុខា','អ្នកជំនួញ','០៩៧៨៨៨៧៩៥','level2','active',0),(1011,'ហុង វិរៈធី','male','2016-11-01','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ហុង សំណាង','កសិករ','០៩៧៨៨៨៧៩៦','គឹម សុភា','កសិករ','០៩៧៨៨៨៧៩៧','level1','active',0),(1012,'គឹម សុគន្ធា','female','2016-11-15','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','គឹម វិបុល','អ្នកជំនួញ','០៩៧៨៨៨៧៩៨','ហេង សុជាតា','អ្នកជំនួញ','០៩៧៨៨៨៧៩៩','level2','active',0),(1013,'ឡុង វិរៈបុត្រ','male','2016-12-01','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ឡុង សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨០០','ស៊ាន សុភាព','គ្រូបង្រៀន','០៩៧៨៨៨៨០១','level1','active',0),(1014,'ស៊ុន សុវណ្ណារិទ្ធ','female','2016-12-15','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ស៊ុន វិបុល','កសិករ','០៩៧៨៨៨៨០២','ឡុង សុខា','កសិករ','០៩៧៨៨៨៨០៣','level2','active',0),(1015,'គង់ វិរៈធី','male','2017-01-01','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','គង់ សំណាង','អ្នកជំនួញ','០៩៧៨៨៨៨០៤','គឹម សុគន្ធា','អ្នកជំនួញ','០៩៧៨៨៨៨០៥','level1','active',0),(1016,'ហេង សុគន្ធា','female','2017-01-15','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ហេង វិបុល','គ្រូបង្រៀន','០៩៧៨៨៨៨០៦','ឡុង សុជាតា','គ្រូបង្រៀន','០៩៧៨៨៨៨០៧','level2','active',0),(1017,'ណុប វិរៈបុត្រ','male','2017-02-01','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ណុប សំណាង','កសិករ','០៩៧៨៨៨៨០៨','ស៊ុន សុភា','កសិករ','០៩៧៨៨៨៨០៩','level1','active',0),(1018,'លឹម សុវណ្ណារិទ្ធ','female','2017-02-15','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','លឹម វិបុល','អ្នកជំនួញ','០៩៧៨៨៨៨១០','គង់ សុខា','អ្នកជំនួញ','០៩៧៨៨៨៨១១','level2','active',0),(1019,'សុខ វិរៈធី','male','2017-03-01','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','សុខ សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨១២','ហេង សុភាព','គ្រូបង្រៀន','០៩៧៨៨៨៨១៣','level1','active',0),(1020,'ម៉ៅ សុគន្ធា','female','2017-03-15','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ម៉ៅ វិបុល','កសិករ','០៩៧៨៨៨៨១៤','ណុប សុខា','កសិករ','០៩៧៨៨៨៨១៥','level2','active',0),(1021,'ជា រតនា','male','2017-04-01','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ជា សុវណ្ណ','អ្នកលក់ដូរ','០៩៧៨៨៨៨១៦','លឹម សុគន្ធា','អ្នកលក់ដូរ','០៩៧៨៨៨៨១៧','level1','active',0),(1022,'យិន សុវណ្ណារិទ្ធ','female','2017-04-15','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','យិន វិបុល','គ្រូបង្រៀន','០៩៧៨៨៨៨១៨','សុខ សុជាតា','គ្រូបង្រៀន','០៩៧៨៨៨៨១៩','level2','active',0),(1023,'សែម វិរៈធី','male','2017-05-01','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','សែម សំណាង','កសិករ','០៩៧៨៨៨៨២០','ម៉ៅ សុភាព','កសិករ','០៩៧៨៨៨៨២១','level1','active',0),(1024,'ស៊ុន សុគន្ធា','female','2017-05-15','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ស៊ុន វិចិត្រ','អ្នកជំនួញ','០៩៧៨៨៨៨២២','ស៊ាន សុខា','អ្នកជំនួញ','០៩៧៨៨៨៨២៣','level2','active',0),(1025,'ជា វិសិដ្ឋ','male','2017-06-01','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ជា វាសនា','គ្រូបង្រៀន','០៩៧៨៨៨៨២៤','គង់ សុភា','គ្រូបង្រៀន','០៩៧៨៨៨៨២៥','level1','active',0),(1026,'លី សុភ័ក្រ','female','2017-06-15','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','លី សំអាត','កសិករ','០៩៧៨៨៨៨២៦','ណុប សុជាតា','កសិករ','០៩៧៨៨៨៨២៧','level2','active',0),(1027,'គង់ សុភ័ក្រ','male','2017-07-01','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','គង់ វិសាល','អ្នកជំនួញ','០៩៧៨៨៨៨២៨','លឹម សុភា','អ្នកជំនួញ','០៩៧៨៨៨៨២៩','level1','active',0),(1028,'ហេង សុវណ្ណារ៉ា','female','2017-07-15','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ហេង សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨៣០','ជា សុគន្ធា','គ្រូបង្រៀន','០៩៧៨៨៨៨៣១','level2','active',0),(1029,'គឹម វិរៈបុត្រ','male','2017-08-01','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','គឹម សំណាង','កសិករ','០៩៧៨៨៨៨៣២','យិន សុភាព','កសិករ','០៩៧៨៨៨៨៣៣','level1','active',0),(1030,'ណុប សុវណ្ណារិទ្ធ','female','2017-08-15','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ណុប វិបុល','អ្នកជំនួញ','០៩៧៨៨៨៨៣៤','សែម សុខា','អ្នកជំនួញ','០៩៧៨៨៨៨៣៥','level2','active',0),(1031,'ឡុង រតនា','male','2017-09-01','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ឡុង សុវណ្ណ','គ្រូបង្រៀន','០៩៧៨៨៨៨៣៦','ស៊ុន សុគន្ធា','គ្រូបង្រៀន','០៩៧៨៨៨៨៣៧','level1','active',0),(1032,'គឹម សុភ័ក្រ','female','2017-09-15','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','គឹម វិចិត្រ','កសិករ','០៩៧៨៨៨៨៣៨','ជា សុជាតា','កសិករ','០៩៧៨៨៨៨៣៩','level2','active',0),(1033,'សុខ វិសិដ្ឋ','male','2017-10-01','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','សុខ វាសនា','អ្នកជំនួញ','០៩៧៨៨៨៨៤០','លី សុភា','អ្នកជំនួញ','០៩៧៨៨៨៨៤១','level1','active',0),(1034,'ម៉ៅ សុវណ្ណារ៉ា','female','2017-10-15','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ម៉ៅ សំអាត','គ្រូបង្រៀន','០៩៧៨៨៨៨៤២','គង់ សុជាតា','គ្រូបង្រៀន','០៩៧៨៨៨៨៤៣','level2','active',0),(1035,'ស៊ាន វិរៈធី','male','2017-11-01','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ស៊ាន វិសាល','កសិករ','០៩៧៨៨៨៨៤៤','ហេង សុភាព','កសិករ','០៩៧៨៨៨៨៤៥','level1','active',0),(1036,'ហុង សុគន្ធា','female','2017-11-15','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ហុង សំណាង','អ្នកជំនួញ','០៩៧៨៨៨៨៤៦','គឹម សុខា','អ្នកជំនួញ','០៩៧៨៨៨៨៤៧','level2','active',0),(1037,'យិន វិរៈបុត្រ','male','2017-12-01','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','យិន សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨៤៨','ឡុង សុគន្ធា','គ្រូបង្រៀន','០៩៧៨៨៨៨៤៩','level1','active',0),(1038,'សែម សុវណ្ណារិទ្ធ','female','2017-12-15','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','សែម វិបុល','កសិករ','០៩៧៨៨៨៨៥០','ណុប សុភា','កសិករ','០៩៧៨៨៨៨៥១','level2','active',0),(1039,'ស៊ុន សុភ័ក្រ','male','2018-01-01','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ស៊ុន វិចិត្រ','អ្នកជំនួញ','០៩៧៨៨៨៨៥២','ស៊ុន សុជាតា','អ្នកជំនួញ','០៩៧៨៨៨៨៥៣','level1','active',0),(1040,'ស៊ាន សុភ័ក្រ','female','2018-01-15','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ស៊ាន សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨៥៤','ជា សុភាព','គ្រូបង្រៀន','០៩៧៨៨៨៨៥៥','level2','active',0),(1041,'ជា វិសាល','male','2018-02-01','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ជា សំណាង','កសិករ','០៩៧៨៨៨៨៥៦','លី សុគន្ធា','កសិករ','០៩៧៨៨៨៨៥៧','level1','active',0),(1042,'លី សុវណ្ណារ៉ា','female','2018-02-15','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','លី វិបុល','អ្នកជំនួញ','០៩៧៨៨៨៨៥៨','គង់ សុជាតា','អ្នកជំនួញ','០៩៧៨៨៨៨៥៩','level2','active',0),(1043,'គង់ វិសាល','male','2018-03-01','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','គង់ វាសនា','គ្រូបង្រៀន','០៩៧៨៨៨៨៦០','ហេង សុភា','គ្រូបង្រៀន','០៩៧៨៨៨៨៦១','level1','active',0),(1044,'ហេង សុភ័ក្រ','female','2018-03-15','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ហេង សំអាត','កសិករ','០៩៧៨៨៨៨៦២','គឹម សុខា','កសិករ','០៩៧៨៨៨៨៦៣','level2','active',0),(1045,'គឹម រតនា','male','2018-04-01','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','គឹម វិសាល','អ្នកជំនួញ','០៩៧៨៨៨៨៦៤','ណុប សុគន្ធា','អ្នកជំនួញ','០៩៧៨៨៨៨៦៥','level1','active',0),(1046,'ណុប សុវណ្ណារ៉ា','female','2018-04-15','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ណុប សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨៦៦','ឡុង សុជាតា','គ្រូបង្រៀន','០៩៧៨៨៨៨៦៧','level2','active',0),(1047,'ឡុង វិរៈធី','male','2018-05-01','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ឡុង សុវណ្ណ','កសិករ','០៩៧៨៨៨៨៦៨','ស៊ុន សុភា','កសិករ','០៩៧៨៨៨៨៦៩','level1','active',0),(1048,'ស៊ុន សុវណ្ណារ៉ា','female','2018-05-15','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ស៊ុន វិចិត្រ','អ្នកជំនួញ','០៩៧៨៨៨៨៧០','ស៊ាន សុខា','អ្នកជំនួញ','០៩៧៨៨៨៨៧១','level2','active',0),(1049,'ស៊ាន វិសិដ្ឋ','male','2018-06-01','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ស៊ាន វាសនា','គ្រូបង្រៀន','០៩៧៨៨៨៨៧២','ជា សុភាព','គ្រូបង្រៀន','០៩៧៨៨៨៨៧៣','level1','active',0),(1050,'ហុង សុវណ្ណារិទ្ធ','female','2018-06-15','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ហុង សំអាត','កសិករ','០៩៧៨៨៨៨៧៤','លី សុជាតា','កសិករ','០៩៧៨៨៨៨៧៥','level2','active',0),(1051,'យិន វិសាល','male','2018-07-01','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','យិន វិសាល','អ្នកជំនួញ','០៩៧៨៨៨៨៧៦','គង់ សុគន្ធា','អ្នកជំនួញ','០៩៧៨៨៨៨៧៧','level1','active',0),(1052,'សែម សុគន្ធា','female','2018-07-15','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','សែម សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨៧៨','ហេង សុជាតា','គ្រូបង្រៀន','០៩៧៨៨៨៨៧៩','level2','active',0),(1053,'ម៉ៅ វិរៈបុត្រ','male','2018-08-01','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ម៉ៅ វិចិត្រ','កសិករ','០៩៧៨៨៨៨៨០','គឹម សុភា','កសិករ','០៩៧៨៨៨៨៨១','level1','active',0),(1054,'សុខ សុភ័ក្រ','female','2018-08-15','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','សុខ វាសនា','អ្នកជំនួញ','០៩៧៨៨៨៨៨២','ណុប សុខា','អ្នកជំនួញ','០៩៧៨៨៨៨៨៣','level2','active',0),(1055,'ជា សុភ័ក្រ','male','2018-09-01','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ជា សំអាត','គ្រូបង្រៀន','០៩៧៨៨៨៨៨៤','ឡុង សុគន្ធា','គ្រូបង្រៀន','០៩៧៨៨៨៨៨៥','level1','active',0),(1056,'លី សុវណ្ណារិទ្ធ','female','2018-09-15','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','លី វិសាល','កសិករ','០៩៧៨៨៨៨៨៦','ស៊ុន សុជាតា','កសិករ','០៩៧៨៨៨៨៨៧','level2','active',0),(1057,'គង់ វិសិដ្ឋ','male','2018-10-01','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','គង់ វាសនា','អ្នកជំនួញ','០៩៧៨៨៨៨៨៨','ស៊ាន សុភា','អ្នកជំនួញ','០៩៧៨៨៨៨៨៩','level1','active',0),(1058,'ហេង សុវណ្ណារិទ្ធ','female','2018-10-15','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ហេង សំណាង','គ្រូបង្រៀន','០៩៧៨៨៨៨៩០','ជា សុជាតា','គ្រូបង្រៀន','០៩៧៨៨៨៨៩១','level2','active',0),(1059,'គឹម រតនា','male','2018-11-01','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','គឹម សំអាត','កសិករ','០៩៧៨៨៨៨៩២','លី សុភាព','កសិករ','០៩៧៨៨៨៨៩៣','level1','active',0),(1060,'ណុប សុគន្ធា','female','2018-11-15','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ណុប វិបុល','អ្នកជំនួញ','០៩៧៨៨៨៨៩៤','គង់ សុខា','អ្នកជំនួញ','០៩៧៨៨៨៨៩៥','level2','active',0),(1061,'ឡុង វិសាល','male','2018-12-01','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ភូមិរំចេក សង្កាត់រតនៈ ក្រុងបាត់ដំបង','ឡុង វាសនា','គ្រូបង្រៀន','០៩៧៨៨៨៨៩៦','ហេង សុគន្ធា','គ្រូបង្រៀន','០៩៧៨៨៨៨៩៧','level1','active',0),(1062,'ស៊ុន សុវណ្ណារ៉ា','female','2018-12-15','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ភូមិស្វាយប៉ោ សង្កាត់ស្វាយប៉ោ ក្រុងបាត់ដំបង','ស៊ុន សំណាង','កសិករ','០៩៧៨៨៨៨៩៨','គឹម សុជាតា','កសិករ','០៩៧៨៨៨៨៩៩','level2','active',0),(1063,'ស៊ាន វិរៈបុត្រ','male','2019-01-01','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ភូមិព្រែកព្រះស្តេច សង្កាត់ព្រែកព្រះស្តេច ក្រុងបាត់ដំបង','ស៊ាន វិចិត្រ','អ្នកជំនួញ','០៩៧៨៨៨៩០០','ណុប សុភា','អ្នកជំនួញ','០៩៧៨៨៨៩០១','level1','active',0),(1064,'ហុង សុភ័ក្រ','female','2019-01-15','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ភូមិទួលតាឯក សង្កាត់ទួលតាឯក ក្រុងបាត់ដំបង','ហុង សំអាត','គ្រូបង្រៀន','០៩៧៨៨៨៩០២','ឡុង សុខា','គ្រូបង្រៀន','០៩៧៨៨៨៩០៣','level2','active',0),(1065,'យិន វិសិដ្ឋ','male','2019-02-01','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','ភូមិឧត្តម សង្កាត់ឧត្តម ក្រុងបាត់ដំបង','យិន វិសាល','កសិករ','០៩៧៨៨៨៩០៤','ស៊ុន សុគន្ធា','កសិករ','០៩៧៨៨៨៩០៥','level1','active',0),(1066,'សែម សុវណ្ណារិទ្ធ','female','2019-02-15','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','ភូមិចំការសំរោង សង្កាត់ចំការសំរោង ក្រុងបាត់ដំបង','សែម វិបុល','អ្នកជំនួញ','០៩៧៨៨៨៩០៦','ស៊ាន សុជាតា','អ្នកជំនួញ','០៩៧៨៨៨៩០៧','level2','active',0),(1067,'ម៉ៅ វិសាល','male','2019-03-01','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ភូមិកំពង់ក្រឡាញ់ សង្កាត់កំពង់ក្រឡាញ់ ក្រុងបាត់ដំបង','ម៉ៅ វាសនា','គ្រូបង្រៀន','០៩៧៨៨៨៩០៨','ជា សុភាព','គ្រូបង្រៀន','០៩៧៨៨៨៩០៩','level1','active',0),(1068,'សុខ សុវណ្ណារ៉ា','female','2019-03-15','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','ភូមិស្លាកែត សង្កាត់ស្លាកែត ក្រុងបាត់ដំបង','សុខ សំណាង','កសិករ','០៩៧៨៨៨៩១០','លី សុជាតា','កសិករ','០៩៧៨៨៨៩១១','level2','active',0),(1069,'ជា វិរៈធី','male','2019-04-01','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ភូមិវត្តគរ សង្កាត់វត្តគរ ក្រុងបាត់ដំបង','ជា សំអាត','អ្នកជំនួញ','០៩៧៨៨៨៩១២','គង់ សុគន្ធា','អ្នកជំនួញ','០៩៧៨៨៨៩១៣','level1','active',0),(1070,'លី សុគន្ធា','female','2019-04-15','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','ភូមិអូរចារ សង្កាត់អូរចារ ក្រុងបាត់ដំបង','លី វិចិត្រ','គ្រូបង្រៀន','០៩៧៨៨៨៩១៤','ហេង សុខា','គ្រូបង្រៀន','០៩៧៨៨៨៩១៥','level2','active',0),(1071,'តេស្ត','male','2025-05-01',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'level1','active',0),(1072,'នួន សុផារី','female','2016-08-20','ភូមិស្វាយប៉ាក់ ឃុំសំបួរ ស្រុកមង្គលបូរី','ភូមិស្វាយប៉ាក់ ឃុំសំបួរ ស្រុកមង្គលបូរី','នួន ណារ៉ុង','ជាងឈើ','092345678','ទេព ផល្លី','អ្នកលក់','092345679','level2','active',0),(1073,'ជា សុជាតិ','male','2017-01-10','ភូមិបត់ត្រង់ ឃុំបត់ត្រង់ ស្រុកមោងឫស្សី','ភូមិបត់ត្រង់ ឃុំបត់ត្រង់ ស្រុកមោងឫស្សី','ជា វណ្ណឌី','គ្រូបង្រៀន','093888111','លី សុខុម','មេផ្ទះ','093888112','level1','active',0),(1074,'ហេង ម៉ាលីន','female','2015-12-05','ភូមិព្រៃខ្ពស់ ឃុំព្រៃខ្ពស់ ស្រុកសំពៅលូន','ភូមិព្រៃខ្ពស់ ឃុំព្រៃខ្ពស់ ស្រុកសំពៅលូន','ហេង សំអុល','កសិករ','095222333','អ៊ុង សុីថា','កម្មការិនី','095222334','level3','active',0),(1075,'សេង ដារ៉ា','male','2016-09-18','ភូមិអូរតាគី ឃុំអូរតាគី ស្រុកសង្កែ','ភូមិអូរតាគី ឃុំអូរតាគី ស្រុកសង្កែ','សេង តុលា','ជាងសំណង់','096777444','ផាត់ ចាន់ថា','អ្នកលក់','096777445','level2','active',0),(1076,'លី សុផានី','female','2017-03-22','ភូមិឈើខ្មៅ ឃុំឈើខ្មៅ ស្រុកកំរៀង','ភូមិឈើខ្មៅ ឃុំឈើខ្មៅ ស្រុកកំរៀង','លី ម៉េងហុង','មន្ត្រីរាជការ','097999555','ជា សុខលី','មេផ្ទះ','097999556','level1','active',0),(1077,'ម៉ៅ សុវណ្ណ','male','2015-11-01','ភូមិបឹងរុន ឃុំបឹងរុន ស្រុកថ្មគោល','ភូមិបឹងរុន ឃុំបឹងរុន ស្រុកថ្មគោល','ម៉ៅ ចំរើន','កសិករ','098111666','សឹម ស្រីនិច','កម្មការិនី','098111667','level3','active',0),(1078,'យ៉ន ស្រីល័ក្ខ','female','2016-07-08','ភូមិព្រៃទទឹង ឃុំព្រៃទទឹង ស្រុកបាណន់','ភូមិព្រៃទទឹង ឃុំព្រៃទទឹង ស្រុកបាណន់','យ៉ន សុភាព','ជាងសំណង់','099444777','ប៉ែន ច័ន្ទធីតា','អ្នកលក់','099444778','level2','active',0),(1079,'តាន់ គីមហុង','male','2017-02-14','ភូមិអន្លង់វិល ឃុំអន្លង់វិល ស្រុកសង្កែ','ភូមិអន្លង់វិល ឃុំអន្លង់វិល ស្រុកសង្កែ','តាន់ សុខុម','គ្រូបង្រៀន','012888999','ហេង ផល្លី','មេផ្ទះ','012888998','level1','active',0),(1080,'ប៉េង សុីណាត','female','2015-10-28','ភូមិស្វាយដូនកែវ ឃុំស្វាយដូនកែវ ស្រុកបាណន់','ភូមិស្វាយដូនកែវ ឃុំស្វាយដូនកែវ ស្រុកបាណន់','ប៉េង ណារ៉ុង','កសិករ','015222111','អ៊ុង សុខុម','កម្មការិនី','015222112','level3','active',0),(1081,'ជា លីហ្សា','female','2016-06-02','ភូមិ ក ក្បាលដំរី ឃុំ កំបោរ ស្រុក ភ្នំព្រឹក','ភូមិ ក ក្បាលដំរី ឃុំ កំបោរ ស្រុក ភ្នំព្រឹក','ជា វណ្ណឌី','មន្ត្រីរាជការ','016333444','លី សុខុម','អាជីវករ','016333445','Level2','Active',0),(1082,'ហេង ឧត្តម','male','2017-04-18','ភូមិ ខ ឃុំ ត្រែង ស្រុក រុក្ខគីរី','ភូមិ ខ ឃុំ ត្រែង ស្រុក រុក្ខគីរី','ហេង សំអុល','បុគ្គលិកក្រុមហ៊ុន','017555888','អ៊ុង សុីថា','បុគ្គលិកក្រុមហ៊ុន','017555889','Level1','Active',0),(1083,'សេង ផល្លា','female','2015-09-05','ភូមិ គ ឃុំ បន្ទាយចាស់ ស្រុក បាត់ដំបង','ភូមិ គ ឃុំ បន្ទាយចាស់ ស្រុក បាត់ដំបង','សេង តុលា','កសិករ','018222999','ផាត់ ចាន់ថា','មេផ្ទះ','018222998','Level3','Active',0),(1084,'លី រតនា','male','2016-12-29','ភូមិ ឃ ឃុំ ព្រះស្តី ស្រុក ភ្នំព្រឹក','ភូមិ ឃ ឃុំ ព្រះស្តី ស្រុក ភ្នំព្រឹក','លី ម៉េងហុង','ជាងសំណង់','011999888','ជា សុខលី','អាជីវករ','011999887','Level2','Active',0),(1085,'ម៉ៅ គឹមហុង','male','2017-07-12','ភូមិ ច ឃុំ សំរោង ស្រុក ឯកភ្នំ','ភូមិ ច ឃុំ សំរោង ស្រុក ឯកភ្នំ','ម៉ៅ ចំរើន','បុគ្គលិកក្រុមហ៊ុន','013444777','សឹម ស្រីនិច','បុគ្គលិកក្រុមហ៊ុន','013444776','Level1','Active',0),(1086,'យ៉ន ម៉ារី','female','2015-08-21','ភូមិ ឆ ឃុំ ព្រៃមាស ស្រុក រុក្ខគីរី','ភូមិ ឆ ឃុំ ព្រៃមាស ស្រុក រុក្ខគីរី','យ៉ន សុភាព','កសិករ','015666555','ប៉ែន ច័ន្ទធីតា','មេផ្ទះ','015666554','Level3','Active',0),(1087,'តាន់ សុជាតិ','male','2016-01-03','ភូមិ ជ ឃុំ ថ្មគោល ស្រុក ថ្មគោល','ភូមិ ជ ឃុំ ថ្មគោល ស្រុក ថ្មគោល','តាន់ សុខុម','ជាងសំណង់','017888333','ហេង ផល្លី','អាជីវករ','017888332','Level2','Active',0),(1088,'ប៉េង សុផានី','female','2017-05-09','ភូមិ ញ ឃុំ អន្លង់វិល ស្រុក សង្កែ','ភូមិ ញ ឃុំ អន្លង់វិល ស្រុក សង្កែ','ប៉េង ណារ៉ុង','បុគ្គលិកក្រុមហ៊ុន','011222111','អ៊ុង សុខុម','បុគ្គលិកក្រុមហ៊ុន','011222112','Level1','Active',0),(1089,'នួន សុខ','male','2016-09-22','ភូមិ ១ ឃុំ ចំរើនផល ស្រុក សំឡូត','ភូមិ ១ ឃុំ ចំរើនផល ស្រុក សំឡូត','នួន ណារ៉ុង','កសិករ','092345678','ទេព ផល្លី','មេផ្ទះ','092345679','level2','active',0),(1090,'ជា សុខុម','female','2017-03-15','ភូមិ ២ ឃុំ មានជ័យ ស្រុក ភ្នំព្រឹក','ភូមិ ២ ឃុំ មានជ័យ ស្រុក ភ្នំព្រឹក','ជា វណ្ណឌី','គ្រូបង្រៀន','093888111','លី សុខុម','អ្នកលក់','093888112','level1','active',0),(1091,'កែវ ច័ន្ទ្រា','female','2017-08-10','ភូមិថ្មី ឃុំកោះច្បាស់ ស្រុកមោងឫស្សី','ភូមិថ្មី ឃុំកោះច្បាស់ ស្រុកមោងឫស្សី','កែវ ឧត្តម','ជាងដែក','092111222','ស៊ឹម ផល្លី','អ្នកលក់','092111223','level1','active',0),(1092,'ខៀវ វិសាល','male','2016-02-25','ភូមិព្រៃទទឹង ឃុំព្រៃទទឹង ស្រុកបាណន់','ភូមិព្រៃទទឹង ឃុំព្រៃទទឹង ស្រុកបាណន់','ខៀវ ម៉េងហុង','គ្រូបង្រៀន','093444555','ហេង សុខុម','មេផ្ទះ','093444556','level2','active',0),(1093,'គង់ សុផារី','female','2015-12-12','ភូមិស្វាយប៉ាក់ ឃុំសំបួរ ស្រុកមង្គលបូរី','ភូមិស្វាយប៉ាក់ ឃុំសំបួរ ស្រុកមង្គលបូរី','គង់ ណារ៉ុង','កសិករ','095777888','ទេព ផល្លី','កម្មការិនី','095777889','level3','active',0),(1094,'ចាន់ សុជាតិ','male','2017-01-18','ភូមិបត់ត្រង់ ឃុំបត់ត្រង់ ស្រុកមោងឫស្សី','ភូមិបត់ត្រង់ ឃុំបត់ត្រង់ ស្រុកមោងឫស្សី','ចាន់ វណ្ណឌី','ជាងឈើ','096222999','លី សុខុម','អ្នកលក់','096222998','level1','active',0),(1095,'ជួន ម៉ាលីន','female','2016-03-01','ភូមិព្រៃខ្ពស់ ឃុំព្រៃខ្ពស់ ស្រុកសំពៅលូន','ភូមិព្រៃខ្ពស់ ឃុំព្រៃខ្ពស់ ស្រុកសំពៅលូន','ជួន សំអុល','មន្ត្រីរាជការ','097555111','អ៊ុង សុីថា','មេផ្ទះ','097555112','level2','active',0),(1096,'ឈុន ដារ៉ា','male','2015-11-08','ភូមិអូរតាគី ឃុំអូរតាគី ស្រុកសង្កែ','ភូមិអូរតាគី ឃុំអូរតាគី ស្រុកសង្កែ','ឈុន តុលា','កសិករ','098888222','ផាត់ ចាន់ថា','កម្មការិនី','098888223','level3','active',0),(1097,'ដួង សុផានី','female','2017-04-05','ភូមិឈើខ្មៅ ឃុំឈើខ្មៅ ស្រុកកំរៀង','ភូមិឈើខ្មៅ ឃុំឈើខ្មៅ ស្រុកកំរៀង','ដួង ម៉េងហុង','ជាងសំណង់','099111333','ជា សុខលី','អ្នកលក់','099111334','level1','active',0),(1098,'ថាន់ សុវណ្ណ','male','2016-05-20','ភូមិបឹងរុន ឃុំបឹងរុន ស្រុកថ្មគោល','ភូមិបឹងរុន ឃុំបឹងរុន ស្រុកថ្មគោល','ថាន់ ចំរើន','គ្រូបង្រៀន','012444666','សឹម ស្រីនិច','មេផ្ទះ','012444667','level2','active',0),(1099,'ធី សុីណាត','female','2015-10-15','ភូមិស្វាយដូនកែវ ឃុំស្វាយដូនកែវ ស្រុកបាណន់','ភូមិស្វាយដូនកែវ ឃុំស្វាយដូនកែវ ស្រុកបាណន់','ធី ណារ៉ុង','កសិករ','015777999','អ៊ុង សុខុម','កម្មការិនី','015777998','level3','active',0),(1100,'រ៉ា ស្រីល័ក្ខ','female','2017-02-01','ភូមិព្រៃទទឹង ឃុំព្រៃទទឹង ស្រុកបាណន់','ភូមិព្រៃទទឹង ឃុំព្រៃទទឹង ស្រុកបាណន់','រ៉ា សុភាព','ជាងសំណង់','016888111','ប៉ែន ច័ន្ទធីតា','អ្នកលក់','016888112','level2','active',0),(1101,'dfd','male','2025-04-28',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'level1','active',0),(1102,'តេស្ត','male','2025-04-28',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'level1','active',0),(1103,'jhdgfhjdbfh','male','2025-05-06',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'level1','active',0),(1104,'nhhh','male','2025-04-29',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'level1','active',0);

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
) ENGINE=InnoDB AUTO_INCREMENT=9287 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_student_monthly_score` */

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
) ENGINE=InnoDB AUTO_INCREMENT=826 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_student_semester_score` */

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
) ENGINE=InnoDB AUTO_INCREMENT=238 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_study` */

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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_subject` */

insert  into `tbl_subject`(`subject_code`,`subject_name`,`create_date`,`isDeleted`) values (1,'គណិតវិទ្យា','2025-02-24 11:59:00',0),(2,'ភាសាខ្មែរ','2025-02-24 11:59:08',0),(3,'វិទ្យាសាស្ត្រ','2025-02-24 17:58:35',0),(4,'សិក្សាសង្គម','2025-02-24 17:58:44',0),(5,'អប់រំកាយ-សុខភាពកីឡា','2025-02-24 17:59:07',0),(6,'អប់រំបំណិនជីវិត','2025-02-24 17:59:20',0),(7,'ភាសាបរទេស','2025-02-24 17:59:34',0),(8,'សម្ថភាពស្ដាប់','2025-02-27 23:58:11',0),(9,'សម្ថភាពសរសេរ','2025-02-27 23:58:20',0),(10,'សម្ថភាពអាន','2025-02-27 23:58:29',0),(11,'សម្ថភាពនិយាយ','2025-02-27 23:58:37',0),(12,'ចំនួន','2025-02-27 23:58:41',0),(13,'រង្វាស់រង្វាល់','2025-02-27 23:58:50',0),(14,'ធរណីមាត្រ','2025-02-27 23:58:57',0),(15,'ពីជគណិត','2025-02-27 23:59:04',0),(16,'ស្ថិតិ','2025-02-27 23:59:11',0),(17,'រូបវិទ្យា','2025-02-27 23:59:19',0),(18,'គីមីវិទ្យា','2025-02-27 23:59:26',0),(19,'ជីវវិទ្យា','2025-02-27 23:59:36',0),(20,'ផែនដី-បរិស្ថានវិទ្យា','2025-02-27 23:59:55',0),(21,'សីលធម៌-ពលរដ្ឋវិទ្យា','2025-02-28 00:00:11',0),(22,'ភូមិវិទ្យា','2025-02-28 00:00:20',0),(23,'ប្រវត្តិវិទ្យា','2025-02-28 00:00:32',0),(24,'គេហវិទ្យា-អប់រំសិល្បៈ','2025-02-28 00:00:55',0),(25,'អប់រំកាយ-កីឡា','2025-02-28 00:01:08',0),(26,'សុខភាព-អនាម័យ','2025-02-28 00:01:24',0),(31,'GTets','2025-05-18 09:33:38',1),(32,'d','2025-05-18 09:39:23',1),(33,'v','2025-05-18 09:40:46',1);

/*Table structure for table `tbl_user` */

DROP TABLE IF EXISTS `tbl_user`;

CREATE TABLE `tbl_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `user_type` varchar(255) NOT NULL,
  `status` int(2) DEFAULT 1,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_user` */

insert  into `tbl_user`(`user_id`,`full_name`,`user_name`,`password`,`phone`,`user_type`,`status`,`created_date`,`isDeleted`) values (1,'ពៅ សំ','khmersr','$2y$10$Q.VLgo/R..2KsmPCie8oMe1PwiQZoix5EQB6a8A3TyUINt4uPiOf2','098582828','admin',1,'2025-02-02 11:41:40',0),(2,'Norea PMS','admin_norea','$2y$10$0BlKYh8a5PqSZBtTVbAASOnBhEdrxj4gnQHPU1u.sD1HlVamaqCxq','0123456789','super_admin',1,'2025-02-02 11:41:44',0),(3,'នី ឡេនីន','lenin','$2y$10$2OCIICvBQ6bkOBklqc7ZXuE2hu.o441OMA.m5o8jzUPPVJYOyrBL6','054359273','user',1,'2025-02-02 11:42:01',0),(4,'ផល សុផាត','sophat','$2y$10$G7cJ98GTijEFlaTVCTOSGugAt6k5Oo3LtrbTqgjlmxY/3ocQbayay','0123456','user',1,'2025-02-11 23:15:10',0),(5,'ឃុត ទីណា','tina','$2y$10$yegGusbbXzg0EQocY4SoCeFVncn31a1WzcDGaPVZ4nbFEfw6KVohK','099887766','user',1,'2025-04-06 15:08:14',0),(6,'បោយ លីន','test','$2y$10$tkvDTFUzRI7ei3fFYEb3hOEJu2YMehkVoXemeTuRSMGVWJMl4r3Y2','0123456','user',1,'2025-04-30 21:58:17',0),(7,'អ៊ុំ វ៉ាន់ច័ន្ទ','hhhhh','$2y$10$p5IUQGhqaVG5bu8EEYH3ROdk03XREOBENoXc1/9SIjjifLZzFGqFi','092783456','user',1,'2025-05-01 10:32:24',0),(8,'ពៅ សំ','hhbb','$2y$10$sdWAXhwIr/eqZPPUQyEmX.ghXKQYbDhDTem3F41l898Qa/w4wNTg2','0123456','user',1,'2025-05-04 21:43:49',0),(9,'ដួង រតនា','ratana','$2y$10$vfJeG5jd4CRavbf8.uVJzuHzwHtz6nX3GxukMRjCfKZspo1fKRna.',NULL,'admin',1,'2025-05-18 08:48:23',0),(10,'tets','test1111111111','$2y$10$WT3Pl5gdc7Lj.2SHW9AfyukkwXZnUgyLHvkQWyqeEijbduD6IsPBO',NULL,'user',0,'2025-05-18 08:50:12',1),(11,'នី ឡេនីន','lenin1','$2y$10$D0vjcFqnCwGpDGlhiRxZRObS0Sq9ImgIm9KyvZiIOXUPSVxYOLC0q','0123456','user',0,'2025-05-18 19:35:36',1);

/*Table structure for table `tbl_year_study` */

DROP TABLE IF EXISTS `tbl_year_study`;

CREATE TABLE `tbl_year_study` (
  `year_study_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `year_study` varchar(255) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isDeleted` int(2) DEFAULT 0,
  PRIMARY KEY (`year_study_id`),
  UNIQUE KEY `year_study` (`year_study`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_year_study` */

insert  into `tbl_year_study`(`year_study_id`,`year_study`,`create_date`,`isDeleted`) values (1,'២០២៥-២០២៦','2025-02-02 09:49:37',0),(2,'២០២៦-២០២៧','2025-02-02 09:49:37',0),(3,'២០២៧-២០២៨','2025-02-02 09:49:37',0),(4,'២០២៨-២០២៩','2025-04-08 17:37:54',0),(5,'២០២៩-២០៣០','2025-04-08 17:43:17',0),(6,'2029-2030','2025-05-18 10:19:30',1);

/* Procedure structure for procedure `CalculateClassYearlyAverage` */

/*!50003 DROP PROCEDURE IF EXISTS  `CalculateClassYearlyAverage` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateClassYearlyAverage`(
    IN p_class_id INT, 
    IN p_year_study_id INT,
    IN p_semester1_monthly_ids VARCHAR(255),
    IN p_semester2_monthly_ids VARCHAR(255)
)
BEGIN
    SELECT 
        s.student_id,
        s.student_name,
        c.class_id,
        c.class_name,
        -- Semester 1 Monthly Average
        ROUND((
            SELECT AVG(sms.score)
            FROM tbl_student_monthly_score sms
            JOIN classroom_subject_monthly_score csms 
                ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
            WHERE sms.student_id = s.student_id
              AND csms.class_id = c.class_id
              AND FIND_IN_SET(csms.monthly_id, p_semester1_monthly_ids)
              AND sms.isDeleted = 0
        ), 2) AS semester1_monthly_avg,
        -- Semester 1 Exam Average
        ROUND((
            SELECT AVG(sss.score)
            FROM tbl_student_semester_score sss
            JOIN tbl_semester_exam_subjects ses 
                ON sss.semester_exam_subject_id = ses.id
            WHERE sss.student_id = s.student_id
              AND ses.class_id = c.class_id
              AND ses.semester_id = 1
              AND sss.isDeleted = 0
        ), 2) AS semester1_exam_avg,
        -- Semester 1 Final
        ROUND((
            IFNULL((
                SELECT AVG(sms.score)
                FROM tbl_student_monthly_score sms
                JOIN classroom_subject_monthly_score csms 
                    ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                WHERE sms.student_id = s.student_id
                  AND csms.class_id = c.class_id
                  AND FIND_IN_SET(csms.monthly_id, p_semester1_monthly_ids)
                  AND sms.isDeleted = 0
            ), 0)
            +
            IFNULL((
                SELECT AVG(sss.score)
                FROM tbl_student_semester_score sss
                JOIN tbl_semester_exam_subjects ses 
                    ON sss.semester_exam_subject_id = ses.id
                WHERE sss.student_id = s.student_id
                  AND ses.class_id = c.class_id
                  AND ses.semester_id = 1
                  AND sss.isDeleted = 0
            ), 0)
        ) / 2, 2) AS semester1_final_avg,
        -- Semester 2 Monthly Average
        ROUND((
            SELECT AVG(sms.score)
            FROM tbl_student_monthly_score sms
            JOIN classroom_subject_monthly_score csms 
                ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
            WHERE sms.student_id = s.student_id
              AND csms.class_id = c.class_id
              AND FIND_IN_SET(csms.monthly_id, p_semester2_monthly_ids)
              AND sms.isDeleted = 0
        ), 2) AS semester2_monthly_avg,
        -- Semester 2 Exam Average
        ROUND((
            SELECT AVG(sss.score)
            FROM tbl_student_semester_score sss
            JOIN tbl_semester_exam_subjects ses 
                ON sss.semester_exam_subject_id = ses.id
            WHERE sss.student_id = s.student_id
              AND ses.class_id = c.class_id
              AND ses.semester_id = 2
              AND sss.isDeleted = 0
        ), 2) AS semester2_exam_avg,
        -- Semester 2 Final
        ROUND((
            IFNULL((
                SELECT AVG(sms.score)
                FROM tbl_student_monthly_score sms
                JOIN classroom_subject_monthly_score csms 
                    ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                WHERE sms.student_id = s.student_id
                  AND csms.class_id = c.class_id
                  AND FIND_IN_SET(csms.monthly_id, p_semester2_monthly_ids)
                  AND sms.isDeleted = 0
            ), 0)
            +
            IFNULL((
                SELECT AVG(sss.score)
                FROM tbl_student_semester_score sss
                JOIN tbl_semester_exam_subjects ses 
                    ON sss.semester_exam_subject_id = ses.id
                WHERE sss.student_id = s.student_id
                  AND ses.class_id = c.class_id
                  AND ses.semester_id = 2
                  AND sss.isDeleted = 0
            ), 0)
        ) / 2, 2) AS semester2_final_avg,
        -- Yearly Average
        ROUND((
            (
                IFNULL((
                    IFNULL((
                        SELECT AVG(sms.score)
                        FROM tbl_student_monthly_score sms
                        JOIN classroom_subject_monthly_score csms 
                            ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                        WHERE sms.student_id = s.student_id
                          AND csms.class_id = c.class_id
                          AND FIND_IN_SET(csms.monthly_id, p_semester1_monthly_ids)
                          AND sms.isDeleted = 0
                    ), 0)
                    +
                    IFNULL((
                        SELECT AVG(sss.score)
                        FROM tbl_student_semester_score sss
                        JOIN tbl_semester_exam_subjects ses 
                            ON sss.semester_exam_subject_id = ses.id
                        WHERE sss.student_id = s.student_id
                          AND ses.class_id = c.class_id
                          AND ses.semester_id = 1
                          AND sss.isDeleted = 0
                    ), 0)
                ) / 2, 0)
            )
            +
            (
                IFNULL((
                    IFNULL((
                        SELECT AVG(sms.score)
                        FROM tbl_student_monthly_score sms
                        JOIN classroom_subject_monthly_score csms 
                            ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                        WHERE sms.student_id = s.student_id
                          AND csms.class_id = c.class_id
                          AND FIND_IN_SET(csms.monthly_id, p_semester2_monthly_ids)
                          AND sms.isDeleted = 0
                    ), 0)
                    +
                    IFNULL((
                        SELECT AVG(sss.score)
                        FROM tbl_student_semester_score sss
                        JOIN tbl_semester_exam_subjects ses 
                            ON sss.semester_exam_subject_id = ses.id
                        WHERE sss.student_id = s.student_id
                          AND ses.class_id = c.class_id
                          AND ses.semester_id = 2
                          AND sss.isDeleted = 0
                    ), 0)
                ) / 2, 0)
            )
        ) / 2, 2) AS yearly_avg
    FROM tbl_student_info s
    INNER JOIN tbl_study st ON s.student_id = st.student_id
    INNER JOIN tbl_classroom c ON st.class_id = c.class_id
    WHERE c.class_id = p_class_id
      AND st.year_study_id = p_year_study_id
      AND s.isDeleted = 0
      AND st.isDeleted = 0
    GROUP BY s.student_id, s.student_name, c.class_id, c.class_name;
END */$$
DELIMITER ;

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

/* Procedure structure for procedure `CalculateYearlyAverage` */

/*!50003 DROP PROCEDURE IF EXISTS  `CalculateYearlyAverage` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateYearlyAverage`(
    IN p_student_id INT,
    IN p_class_id INT,
    IN p_semester1_monthly_ids VARCHAR(255),
    IN p_semester2_monthly_ids VARCHAR(255)
)
BEGIN
    DECLARE semester1_monthly_avg DECIMAL(5,2);
    DECLARE semester1_exam_avg DECIMAL(5,2);
    DECLARE semester2_monthly_avg DECIMAL(5,2);
    DECLARE semester2_exam_avg DECIMAL(5,2);
    DECLARE semester1_final DECIMAL(5,2);
    DECLARE semester2_final DECIMAL(5,2);
    DECLARE year_avg DECIMAL(5,2);
    -- Calculate Semester 1 Monthly Average
    SELECT AVG(sms.score) INTO semester1_monthly_avg
    FROM tbl_student_monthly_score sms
    JOIN classroom_subject_monthly_score csms 
        ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
    WHERE sms.student_id = p_student_id
      AND csms.class_id = p_class_id
      AND FIND_IN_SET(csms.monthly_id, p_semester1_monthly_ids)
      AND sms.isDeleted = 0;
    -- Calculate Semester 1 Exam Average
    SELECT AVG(sss.score) INTO semester1_exam_avg
    FROM tbl_student_semester_score sss
    JOIN tbl_semester_exam_subjects ses 
        ON sss.semester_exam_subject_id = ses.id
    WHERE sss.student_id = p_student_id
      AND ses.class_id = p_class_id
      AND ses.semester_id = 1
      AND sss.isDeleted = 0;
    -- Calculate Semester 2 Monthly Average
    SELECT AVG(sms.score) INTO semester2_monthly_avg
    FROM tbl_student_monthly_score sms
    JOIN classroom_subject_monthly_score csms 
        ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
    WHERE sms.student_id = p_student_id
      AND csms.class_id = p_class_id
      AND FIND_IN_SET(csms.monthly_id, p_semester2_monthly_ids)
      AND sms.isDeleted = 0;
    -- Calculate Semester 2 Exam Average
    SELECT AVG(sss.score) INTO semester2_exam_avg
    FROM tbl_student_semester_score sss
    JOIN tbl_semester_exam_subjects ses 
        ON sss.semester_exam_subject_id = ses.id
    WHERE sss.student_id = p_student_id
      AND ses.class_id = p_class_id
      AND ses.semester_id = 2
      AND sss.isDeleted = 0;
    -- Calculate final averages
    SET semester1_final = (IFNULL(semester1_monthly_avg, 0) + IFNULL(semester1_exam_avg, 0)) / 2;
    SET semester2_final = (IFNULL(semester2_monthly_avg, 0) + IFNULL(semester2_exam_avg, 0)) / 2;
    SET year_avg = (semester1_final + semester2_final) / 2;
    -- Return the results
    SELECT 
        p_student_id AS student_id,
        ROUND(semester1_monthly_avg, 2) AS semester1_monthly_average,
        ROUND(semester1_exam_avg, 2) AS semester1_exam_average,
        ROUND(semester1_final, 2) AS semester1_final_average,
        ROUND(semester2_monthly_avg, 2) AS semester2_monthly_average,
        ROUND(semester2_exam_avg, 2) AS semester2_exam_average,
        ROUND(semester2_final, 2) AS semester2_final_average,
        ROUND(year_avg, 2) AS yearly_average;
END */$$
DELIMITER ;

/* Procedure structure for procedure `CalculateYearlyAverageForClass` */

/*!50003 DROP PROCEDURE IF EXISTS  `CalculateYearlyAverageForClass` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateYearlyAverageForClass`(
    IN p_class_id INT
)
BEGIN
    -- Create temporary table to store results
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_yearly_averages (
        student_id INT,
        class_id INT,
        semester1_final_avg DECIMAL(5,2),
        semester2_final_avg DECIMAL(5,2),
        year_avg DECIMAL(5,2)
    );
    -- Get all active students in the class
    INSERT INTO temp_yearly_averages (student_id, class_id)
    SELECT s.student_id, p_class_id
    FROM tbl_student_info s
    JOIN tbl_study st ON s.student_id = st.student_id
    WHERE st.class_id = p_class_id 
      AND st.status = 'active'
      AND s.isDeleted = 0
      AND st.isDeleted = 0
      AND s.student_name IS NOT NULL
      AND s.student_name <> '';
    -- Update semester 1 monthly averages (semester 1 months)
    UPDATE temp_yearly_averages t
    SET t.semester1_final_avg = (
        SELECT AVG(sms.score)
        FROM tbl_student_monthly_score sms
        JOIN classroom_subject_monthly_score csms
          ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
        JOIN tbl_monthly m
          ON csms.monthly_id = m.monthly_id
        WHERE sms.student_id = t.student_id
          AND csms.class_id = p_class_id
          AND m.semester_id = 1
          AND sms.isDeleted = 0
    );
    -- Update semester 1 exam averages
    UPDATE temp_yearly_averages t
    SET t.semester1_final_avg = (
        (IFNULL(t.semester1_final_avg, 0) + 
        IFNULL((
            SELECT AVG(sss.score)
            FROM tbl_student_semester_score sss
            JOIN tbl_semester_exam_subjects ses
              ON sss.semester_exam_subject_id = ses.id
            WHERE sss.student_id = t.student_id
              AND ses.class_id = p_class_id
              AND ses.semester_id = 1
              AND sss.isDeleted = 0
        ), 0)) / 2
    );
    -- Update semester 2 monthly averages (semester 2 months)
    UPDATE temp_yearly_averages t
    SET t.semester2_final_avg = (
        SELECT AVG(sms.score)
        FROM tbl_student_monthly_score sms
        JOIN classroom_subject_monthly_score csms
          ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
        JOIN tbl_monthly m
          ON csms.monthly_id = m.monthly_id
        WHERE sms.student_id = t.student_id
          AND csms.class_id = p_class_id
          AND m.semester_id = 2
          AND sms.isDeleted = 0
    );
    -- Update semester 2 exam averages
    UPDATE temp_yearly_averages t
    SET t.semester2_final_avg = (
        (IFNULL(t.semester2_final_avg, 0) + 
        IFNULL((
            SELECT AVG(sss.score)
            FROM tbl_student_semester_score sss
            JOIN tbl_semester_exam_subjects ses
              ON sss.semester_exam_subject_id = ses.id
            WHERE sss.student_id = t.student_id
              AND ses.class_id = p_class_id
              AND ses.semester_id = 2
              AND sss.isDeleted = 0
        ), 0)) / 2
    );
    -- Calculate yearly average
    UPDATE temp_yearly_averages
    SET year_avg = (IFNULL(semester1_final_avg, 0) + IFNULL(semester2_final_avg, 0)) / 2;
    -- Return results with student names
    SELECT 
        t.student_id,
        s.student_name,
        t.class_id,
        t.semester1_final_avg,
        t.semester2_final_avg,
        t.year_avg
    FROM temp_yearly_averages t
    JOIN tbl_student_info s ON t.student_id = s.student_id
    ORDER BY s.student_name;
    -- Clean up
    DROP TEMPORARY TABLE IF EXISTS temp_yearly_averages;
END */$$
DELIMITER ;

/* Procedure structure for procedure `CalculateYearlyAverageForStudent` */

/*!50003 DROP PROCEDURE IF EXISTS  `CalculateYearlyAverageForStudent` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateYearlyAverageForStudent`(
    IN p_student_id INT,
    IN p_class_id INT
)
BEGIN
    DECLARE semester1_monthly_avg DECIMAL(5,2);
    DECLARE semester1_exam_avg DECIMAL(5,2);
    DECLARE semester2_monthly_avg DECIMAL(5,2);
    DECLARE semester2_exam_avg DECIMAL(5,2);
    DECLARE semester1_final DECIMAL(5,2);
    DECLARE semester2_final DECIMAL(5,2);
    DECLARE year_avg DECIMAL(5,2);
    -- Semester 1 monthly average
    SELECT AVG(sms.score) INTO semester1_monthly_avg
    FROM tbl_student_monthly_score sms
    JOIN classroom_subject_monthly_score csms
      ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
    WHERE sms.student_id = p_student_id
      AND csms.class_id = p_class_id
      AND csms.monthly_id IN (1) -- months for semester 1
      AND sms.isDeleted = 0;
    -- Semester 1 exam average
    SELECT AVG(sss.score) INTO semester1_exam_avg
    FROM tbl_student_semester_score sss
    JOIN tbl_semester_exam_subjects ses
      ON sss.semester_exam_subject_id = ses.id
    WHERE sss.student_id = p_student_id
      AND ses.class_id = p_class_id
      AND ses.semester_id = 1
      AND sss.isDeleted = 0;
    -- Semester 2 monthly average
    SELECT AVG(sms.score) INTO semester2_monthly_avg
    FROM tbl_student_monthly_score sms
    JOIN classroom_subject_monthly_score csms
      ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
    WHERE sms.student_id = p_student_id
      AND csms.class_id = p_class_id
      AND csms.monthly_id IN (3) -- months for semester 2
      AND sms.isDeleted = 0;
    -- Semester 2 exam average
    SELECT AVG(sss.score) INTO semester2_exam_avg
    FROM tbl_student_semester_score sss
    JOIN tbl_semester_exam_subjects ses
      ON sss.semester_exam_subject_id = ses.id
    WHERE sss.student_id = p_student_id
      AND ses.class_id = p_class_id
      AND ses.semester_id = 2
      AND sss.isDeleted = 0;
    -- Calculate final averages
    SET semester1_final = (IFNULL(semester1_monthly_avg,0) + IFNULL(semester1_exam_avg,0)) / 2;
    SET semester2_final = (IFNULL(semester2_monthly_avg,0) + IFNULL(semester2_exam_avg,0)) / 2;
    SET year_avg = (semester1_final + semester2_final) / 2;
    -- Return result
    SELECT 
        p_student_id AS student_id,
        p_class_id AS class_id,
        semester1_final AS semester1_final_avg,
        semester2_final AS semester2_final_avg,
        year_avg AS year_avg;
END */$$
DELIMITER ;

/* Procedure structure for procedure `CalculateYearlyAveragesForClass` */

/*!50003 DROP PROCEDURE IF EXISTS  `CalculateYearlyAveragesForClass` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateYearlyAveragesForClass`(IN p_class_id INT)
BEGIN
    SELECT 
        s.student_id,
        s.student_name,
        c.class_id,
        c.class_name,
        -- Semester 1 Monthly Average (dynamic)
        (SELECT AVG(sms1.score)
         FROM tbl_student_monthly_score sms1
         JOIN classroom_subject_monthly_score csms1 ON sms1.classroom_subject_monthly_score_id = csms1.classroom_subject_monthly_score_id
         JOIN tbl_monthly m1 ON csms1.monthly_id = m1.monthly_id
         WHERE sms1.student_id = s.student_id 
           AND csms1.class_id = c.class_id
           AND m1.semester_id = 1
           AND sms1.isDeleted = 0
        ) AS semester1_monthly_avg,
        -- Semester 1 Exam Average
        (SELECT AVG(sss1.score)
         FROM tbl_student_semester_score sss1
         JOIN tbl_semester_exam_subjects ses1 ON sss1.semester_exam_subject_id = ses1.id
         WHERE sss1.student_id = s.student_id
           AND ses1.class_id = c.class_id
           AND ses1.semester_id = 1
           AND sss1.isDeleted = 0
        ) AS semester1_exam_avg,
        -- Semester 2 Monthly Average (dynamic)
        (SELECT AVG(sms2.score)
         FROM tbl_student_monthly_score sms2
         JOIN classroom_subject_monthly_score csms2 ON sms2.classroom_subject_monthly_score_id = csms2.classroom_subject_monthly_score_id
         JOIN tbl_monthly m2 ON csms2.monthly_id = m2.monthly_id
         WHERE sms2.student_id = s.student_id
           AND csms2.class_id = c.class_id
           AND m2.semester_id = 2
           AND sms2.isDeleted = 0
        ) AS semester2_monthly_avg,
        -- Semester 2 Exam Average
        (SELECT AVG(sss2.score)
         FROM tbl_student_semester_score sss2
         JOIN tbl_semester_exam_subjects ses2 ON sss2.semester_exam_subject_id = ses2.id
         WHERE sss2.student_id = s.student_id
           AND ses2.class_id = c.class_id
           AND ses2.semester_id = 2
           AND sss2.isDeleted = 0
        ) AS semester2_exam_avg,
        -- Final Averages
        (
            IFNULL(
                (SELECT AVG(sms1.score)
                 FROM tbl_student_monthly_score sms1
                 JOIN classroom_subject_monthly_score csms1 ON sms1.classroom_subject_monthly_score_id = csms1.classroom_subject_monthly_score_id
                 JOIN tbl_monthly m1 ON csms1.monthly_id = m1.monthly_id
                 WHERE sms1.student_id = s.student_id 
                   AND csms1.class_id = c.class_id
                   AND m1.semester_id = 1
                   AND sms1.isDeleted = 0
                ), 0
            ) + 
            IFNULL(
                (SELECT AVG(sss1.score)
                 FROM tbl_student_semester_score sss1
                 JOIN tbl_semester_exam_subjects ses1 ON sss1.semester_exam_subject_id = ses1.id
                 WHERE sss1.student_id = s.student_id
                   AND ses1.class_id = c.class_id
                   AND ses1.semester_id = 1
                   AND sss1.isDeleted = 0
                ), 0
            )
        ) / 2 AS semester1_final_avg,
        (
            IFNULL(
                (SELECT AVG(sms2.score)
                 FROM tbl_student_monthly_score sms2
                 JOIN classroom_subject_monthly_score csms2 ON sms2.classroom_subject_monthly_score_id = csms2.classroom_subject_monthly_score_id
                 JOIN tbl_monthly m2 ON csms2.monthly_id = m2.monthly_id
                 WHERE sms2.student_id = s.student_id
                   AND csms2.class_id = c.class_id
                   AND m2.semester_id = 2
                   AND sms2.isDeleted = 0
                ), 0
            ) + 
            IFNULL(
                (SELECT AVG(sss2.score)
                 FROM tbl_student_semester_score sss2
                 JOIN tbl_semester_exam_subjects ses2 ON sss2.semester_exam_subject_id = ses2.id
                 WHERE sss2.student_id = s.student_id
                   AND ses2.class_id = c.class_id
                   AND ses2.semester_id = 2
                   AND sss2.isDeleted = 0
                ), 0
            )
        ) / 2 AS semester2_final_avg,
        -- Yearly Average
        (
            (
                IFNULL(
                    (SELECT AVG(sms1.score)
                     FROM tbl_student_monthly_score sms1
                     JOIN classroom_subject_monthly_score csms1 ON sms1.classroom_subject_monthly_score_id = csms1.classroom_subject_monthly_score_id
                     JOIN tbl_monthly m1 ON csms1.monthly_id = m1.monthly_id
                     WHERE sms1.student_id = s.student_id 
                       AND csms1.class_id = c.class_id
                       AND m1.semester_id = 1
                       AND sms1.isDeleted = 0
                    ), 0
                ) + 
                IFNULL(
                    (SELECT AVG(sss1.score)
                     FROM tbl_student_semester_score sss1
                     JOIN tbl_semester_exam_subjects ses1 ON sss1.semester_exam_subject_id = ses1.id
                     WHERE sss1.student_id = s.student_id
                       AND ses1.class_id = c.class_id
                       AND ses1.semester_id = 1
                       AND sss1.isDeleted = 0
                    ), 0
                )
            ) / 2
            +
            (
                IFNULL(
                    (SELECT AVG(sms2.score)
                     FROM tbl_student_monthly_score sms2
                     JOIN classroom_subject_monthly_score csms2 ON sms2.classroom_subject_monthly_score_id = csms2.classroom_subject_monthly_score_id
                     JOIN tbl_monthly m2 ON csms2.monthly_id = m2.monthly_id
                     WHERE sms2.student_id = s.student_id
                       AND csms2.class_id = c.class_id
                       AND m2.semester_id = 2
                       AND sms2.isDeleted = 0
                    ), 0
                ) + 
                IFNULL(
                    (SELECT AVG(sss2.score)
                     FROM tbl_student_semester_score sss2
                     JOIN tbl_semester_exam_subjects ses2 ON sss2.semester_exam_subject_id = ses2.id
                     WHERE sss2.student_id = s.student_id
                       AND ses2.class_id = c.class_id
                       AND ses2.semester_id = 2
                       AND sss2.isDeleted = 0
                    ), 0
                )
            ) / 2
        ) / 2 AS yearly_avg
    FROM tbl_student_info s
    JOIN tbl_study st ON s.student_id = st.student_id
    JOIN tbl_classroom c ON st.class_id = c.class_id
    WHERE c.class_id = p_class_id
      AND st.status = 'active'
      AND s.isDeleted = 0
      AND st.isDeleted = 0
    ORDER BY s.student_name;
END */$$
DELIMITER ;

/* Procedure structure for procedure `CalculateYearlyAverageWithMonthlyIds` */

/*!50003 DROP PROCEDURE IF EXISTS  `CalculateYearlyAverageWithMonthlyIds` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateYearlyAverageWithMonthlyIds`(
    IN p_student_id INT,
    IN p_class_id INT,
    IN p_semester1_monthly_ids VARCHAR(255),
    IN p_semester2_monthly_ids VARCHAR(255)
)
BEGIN
    DECLARE semester1_avg DECIMAL(5,2);
    DECLARE semester2_avg DECIMAL(5,2);
    -- Semester 1
    SELECT (
        IFNULL((
            SELECT AVG(sms.score)
            FROM tbl_student_monthly_score sms
            JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
            WHERE sms.student_id = p_student_id AND csms.class_id = p_class_id AND FIND_IN_SET(csms.monthly_id, p_semester1_monthly_ids) AND sms.isDeleted = 0
        ), 0)
        +
        IFNULL((
            SELECT AVG(sss2.score)
            FROM tbl_student_semester_score sss2
            JOIN tbl_semester_exam_subjects ses2 ON sss2.semester_exam_subject_id = ses2.id
            WHERE sss2.student_id = p_student_id AND ses2.semester_id = 1 AND ses2.class_id = p_class_id AND sss2.isDeleted = 0
        ), 0)
    ) / 2 INTO semester1_avg;
    -- Semester 2
    SELECT (
        IFNULL((
            SELECT AVG(sms.score)
            FROM tbl_student_monthly_score sms
            JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
            WHERE sms.student_id = p_student_id AND csms.class_id = p_class_id AND FIND_IN_SET(csms.monthly_id, p_semester2_monthly_ids) AND sms.isDeleted = 0
        ), 0)
        +
        IFNULL((
            SELECT AVG(sss2.score)
            FROM tbl_student_semester_score sss2
            JOIN tbl_semester_exam_subjects ses2 ON sss2.semester_exam_subject_id = ses2.id
            WHERE sss2.student_id = p_student_id AND ses2.semester_id = 2 AND ses2.class_id = p_class_id AND sss2.isDeleted = 0
        ), 0)
    ) / 2 INTO semester2_avg;
    -- Yearly average
    SELECT
        p_student_id AS student_id,
        semester1_avg AS semester_1_average,
        semester2_avg AS semester_2_average,
        ROUND((semester1_avg + semester2_avg) / 2, 2) AS yearly_average;
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

/* Procedure structure for procedure `GetStudentYearlyAveragesWithRanking` */

/*!50003 DROP PROCEDURE IF EXISTS  `GetStudentYearlyAveragesWithRanking` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `GetStudentYearlyAveragesWithRanking`(IN p_grade_id INT)
BEGIN
    SELECT 
        s.student_id,
        s.student_name,
        c.class_id,
        c.class_name,
        g.grade_id,
        g.grade_name,
        st.year_study_id,  -- Added year_study_id field
        -- Semester 1 Monthly Average
        ROUND(IFNULL(AVG(CASE 
            WHEN FIND_IN_SET(csms.monthly_id, ses1.monthly_ids) THEN sms.score 
            ELSE NULL 
        END), 0), 2) AS semester1_monthly_avg,
        -- Semester 1 Exam Average
        ROUND(IFNULL(AVG(CASE 
            WHEN ses.semester_id = 1 THEN sss.score 
            ELSE NULL 
        END), 0), 2) AS semester1_exam_avg,
        -- Semester 2 Monthly Average
        ROUND(IFNULL(AVG(CASE 
            WHEN FIND_IN_SET(csms.monthly_id, ses2.monthly_ids) THEN sms.score 
            ELSE NULL 
        END), 0), 2) AS semester2_monthly_avg,
        -- Semester 2 Exam Average
        ROUND(IFNULL(AVG(CASE 
            WHEN ses.semester_id = 2 THEN sss.score 
            ELSE NULL 
        END), 0), 2) AS semester2_exam_avg,
        -- Final Semester 1 Average
        ROUND((
            IFNULL(AVG(CASE 
                WHEN FIND_IN_SET(csms.monthly_id, ses1.monthly_ids) THEN sms.score 
                ELSE NULL 
            END), 0) +
            IFNULL(AVG(CASE 
                WHEN ses.semester_id = 1 THEN sss.score 
                ELSE NULL 
            END), 0)
        ) / 2, 2) AS semester1_final_avg,
        -- Final Semester 2 Average
        ROUND((
            IFNULL(AVG(CASE 
                WHEN FIND_IN_SET(csms.monthly_id, ses2.monthly_ids) THEN sms.score 
                ELSE NULL 
            END), 0) +
            IFNULL(AVG(CASE 
                WHEN ses.semester_id = 2 THEN sss.score 
                ELSE NULL 
            END), 0)
        ) / 2, 2) AS semester2_final_avg,
        -- Yearly Average
        ROUND((
            (
                IFNULL(AVG(CASE 
                    WHEN FIND_IN_SET(csms.monthly_id, ses1.monthly_ids) THEN sms.score 
                    ELSE NULL 
                END), 0) +
                IFNULL(AVG(CASE 
                    WHEN ses.semester_id = 1 THEN sss.score 
                    ELSE NULL 
                END), 0)
            ) / 2 +
            (
                IFNULL(AVG(CASE 
                    WHEN FIND_IN_SET(csms.monthly_id, ses2.monthly_ids) THEN sms.score 
                    ELSE NULL 
                END), 0) +
                IFNULL(AVG(CASE 
                    WHEN ses.semester_id = 2 THEN sss.score 
                    ELSE NULL 
                END), 0)
            ) / 2
        ) / 2, 2) AS yearly_avg,
        -- Rankings
        DENSE_RANK() OVER (
            PARTITION BY c.class_id 
            ORDER BY ROUND((
                IFNULL(AVG(CASE 
                    WHEN FIND_IN_SET(csms.monthly_id, ses1.monthly_ids) THEN sms.score 
                    ELSE NULL 
                END), 0) +
                IFNULL(AVG(CASE 
                    WHEN ses.semester_id = 1 THEN sss.score 
                    ELSE NULL 
                END), 0)
            ) / 2, 2) DESC
        ) AS semester1_rank,
        DENSE_RANK() OVER (
            PARTITION BY c.class_id 
            ORDER BY ROUND((
                IFNULL(AVG(CASE 
                    WHEN FIND_IN_SET(csms.monthly_id, ses2.monthly_ids) THEN sms.score 
                    ELSE NULL 
                END), 0) +
                IFNULL(AVG(CASE 
                    WHEN ses.semester_id = 2 THEN sss.score 
                    ELSE NULL 
                END), 0)
            ) / 2, 2) DESC
        ) AS semester2_rank,
        DENSE_RANK() OVER (
            PARTITION BY c.class_id 
            ORDER BY ROUND((
                (
                    IFNULL(AVG(CASE 
                        WHEN FIND_IN_SET(csms.monthly_id, ses1.monthly_ids) THEN sms.score 
                        ELSE NULL 
                    END), 0) +
                    IFNULL(AVG(CASE 
                        WHEN ses.semester_id = 1 THEN sss.score 
                        ELSE NULL 
                    END), 0)
                ) / 2 +
                (
                    IFNULL(AVG(CASE 
                        WHEN FIND_IN_SET(csms.monthly_id, ses2.monthly_ids) THEN sms.score 
                        ELSE NULL 
                    END), 0) +
                    IFNULL(AVG(CASE 
                        WHEN ses.semester_id = 2 THEN sss.score 
                        ELSE NULL 
                    END), 0)
                ) / 2
            ) / 2, 2) DESC
        ) AS yearly_rank
    FROM 
        tbl_student_info s
        JOIN tbl_study st ON s.student_id = st.student_id
        JOIN tbl_classroom c ON st.class_id = c.class_id
        JOIN tbl_grade g ON c.grade_id = g.grade_id
        LEFT JOIN tbl_student_monthly_score sms ON s.student_id = sms.student_id
        LEFT JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
        LEFT JOIN tbl_student_semester_score sss ON s.student_id = sss.student_id
        LEFT JOIN tbl_semester_exam_subjects ses ON sss.semester_exam_subject_id = ses.id
        LEFT JOIN tbl_semester_exam_subjects ses1 ON ses1.class_id = c.class_id AND ses1.semester_id = 1
        LEFT JOIN tbl_semester_exam_subjects ses2 ON ses2.class_id = c.class_id AND ses2.semester_id = 2
    WHERE 
        g.grade_id = p_grade_id
        AND st.status = 'active'
        AND s.isDeleted = 0
        AND st.isDeleted = 0
        AND (sms.isDeleted = 0 OR sms.isDeleted IS NULL)
        AND (csms.isDeleted = 0 OR csms.isDeleted IS NULL)
        AND (sss.isDeleted = 0 OR sss.isDeleted IS NULL)
    GROUP BY 
        s.student_id, s.student_name, c.class_id, c.class_name, g.grade_id, g.grade_name, st.year_study_id;  -- Added year_study_id to GROUP BY
END */$$
DELIMITER ;

/* Procedure structure for procedure `GetYearlyAverageByGrade` */

/*!50003 DROP PROCEDURE IF EXISTS  `GetYearlyAverageByGrade` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `GetYearlyAverageByGrade`(IN p_grade_id INT)
BEGIN
    SELECT 
        s.student_id,
        s.student_name,
        c.class_id,
        c.class_name,
        g.grade_id,
        g.grade_name,
        -- Semester 1 Monthly Average
        ROUND(IFNULL(AVG(CASE 
            WHEN FIND_IN_SET(csms.monthly_id, ses1.monthly_ids) THEN sms.score 
            ELSE NULL 
        END), 0), 2) AS semester1_monthly_avg,
        -- Semester 1 Exam Average
        ROUND(IFNULL(AVG(CASE 
            WHEN ses.semester_id = 1 THEN sss.score 
            ELSE NULL 
        END), 0), 2) AS semester1_exam_avg,
        -- Semester 2 Monthly Average
        ROUND(IFNULL(AVG(CASE 
            WHEN FIND_IN_SET(csms.monthly_id, ses2.monthly_ids) THEN sms.score 
            ELSE NULL 
        END), 0), 2) AS semester2_monthly_avg,
        -- Semester 2 Exam Average
        ROUND(IFNULL(AVG(CASE 
            WHEN ses.semester_id = 2 THEN sss.score 
            ELSE NULL 
        END), 0), 2) AS semester2_exam_avg,
        -- Final Semester 1 Average
        ROUND((
            IFNULL(AVG(CASE 
                WHEN FIND_IN_SET(csms.monthly_id, ses1.monthly_ids) THEN sms.score 
                ELSE NULL 
            END), 0) +
            IFNULL(AVG(CASE 
                WHEN ses.semester_id = 1 THEN sss.score 
                ELSE NULL 
            END), 0)
        ) / 2, 2) AS semester1_final_avg,
        -- Final Semester 2 Average
        ROUND((
            IFNULL(AVG(CASE 
                WHEN FIND_IN_SET(csms.monthly_id, ses2.monthly_ids) THEN sms.score 
                ELSE NULL 
            END), 0) +
            IFNULL(AVG(CASE 
                WHEN ses.semester_id = 2 THEN sss.score 
                ELSE NULL 
            END), 0)
        ) / 2, 2) AS semester2_final_avg,
        -- Yearly Average
        ROUND((
            (
                IFNULL(AVG(CASE 
                    WHEN FIND_IN_SET(csms.monthly_id, ses1.monthly_ids) THEN sms.score 
                    ELSE NULL 
                END), 0) +
                IFNULL(AVG(CASE 
                    WHEN ses.semester_id = 1 THEN sss.score 
                    ELSE NULL 
                END), 0)
            ) / 2 +
            (
                IFNULL(AVG(CASE 
                    WHEN FIND_IN_SET(csms.monthly_id, ses2.monthly_ids) THEN sms.score 
                    ELSE NULL 
                END), 0) +
                IFNULL(AVG(CASE 
                    WHEN ses.semester_id = 2 THEN sss.score 
                    ELSE NULL 
                END), 0)
            ) / 2
        ) / 2, 2) AS yearly_avg
    FROM 
        tbl_student_info s
        JOIN tbl_study st ON s.student_id = st.student_id
        JOIN tbl_classroom c ON st.class_id = c.class_id
        JOIN tbl_grade g ON c.grade_id = g.grade_id
        LEFT JOIN tbl_student_monthly_score sms ON s.student_id = sms.student_id
        LEFT JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
        LEFT JOIN tbl_student_semester_score sss ON s.student_id = sss.student_id
        LEFT JOIN tbl_semester_exam_subjects ses ON sss.semester_exam_subject_id = ses.id
        LEFT JOIN tbl_semester_exam_subjects ses1 ON ses1.class_id = c.class_id AND ses1.semester_id = 1
        LEFT JOIN tbl_semester_exam_subjects ses2 ON ses2.class_id = c.class_id AND ses2.semester_id = 2
    WHERE 
        g.grade_id = p_grade_id
        AND st.status = 'active'
        AND s.isDeleted = 0
        AND st.isDeleted = 0
        AND (sms.isDeleted = 0 OR sms.isDeleted IS NULL)
        AND (csms.isDeleted = 0 OR csms.isDeleted IS NULL)
        AND (sss.isDeleted = 0 OR sss.isDeleted IS NULL)
    GROUP BY 
        s.student_id, s.student_name, c.class_id, c.class_name, g.grade_id, g.grade_name
    ORDER BY 
        s.student_name;
END */$$
DELIMITER ;

/* Procedure structure for procedure `GetYearlyAverageForClass` */

/*!50003 DROP PROCEDURE IF EXISTS  `GetYearlyAverageForClass` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `GetYearlyAverageForClass`(
    IN p_class_id INT
)
BEGIN
    SELECT 
        s.student_id,
        s.student_name,
        c.class_id,
        c.class_name,
        -- Semester 1 Monthly Average
        ROUND(IFNULL(AVG(CASE 
            WHEN FIND_IN_SET(csms.monthly_id, ses1.monthly_ids) THEN sms.score 
            ELSE NULL 
        END), 0), 2) AS semester1_monthly_avg,
        -- Semester 1 Exam Average
        ROUND(IFNULL(AVG(CASE 
            WHEN ses.semester_id = 1 THEN sss.score 
            ELSE NULL 
        END), 0), 2) AS semester1_exam_avg,
        -- Semester 2 Monthly Average
        ROUND(IFNULL(AVG(CASE 
            WHEN FIND_IN_SET(csms.monthly_id, ses2.monthly_ids) THEN sms.score 
            ELSE NULL 
        END), 0), 2) AS semester2_monthly_avg,
        -- Semester 2 Exam Average
        ROUND(IFNULL(AVG(CASE 
            WHEN ses.semester_id = 2 THEN sss.score 
            ELSE NULL 
        END), 0), 2) AS semester2_exam_avg,
        -- Final Semester 1 Average
        ROUND((
            IFNULL(AVG(CASE 
                WHEN FIND_IN_SET(csms.monthly_id, ses1.monthly_ids) THEN sms.score 
                ELSE NULL 
            END), 0) +
            IFNULL(AVG(CASE 
                WHEN ses.semester_id = 1 THEN sss.score 
                ELSE NULL 
            END), 0)
        ) / 2, 2) AS semester1_final_avg,
        -- Final Semester 2 Average
        ROUND((
            IFNULL(AVG(CASE 
                WHEN FIND_IN_SET(csms.monthly_id, ses2.monthly_ids) THEN sms.score 
                ELSE NULL 
            END), 0) +
            IFNULL(AVG(CASE 
                WHEN ses.semester_id = 2 THEN sss.score 
                ELSE NULL 
            END), 0)
        ) / 2, 2) AS semester2_final_avg,
        -- Yearly Average
        ROUND((
            (
                IFNULL(AVG(CASE 
                    WHEN FIND_IN_SET(csms.monthly_id, ses1.monthly_ids) THEN sms.score 
                    ELSE NULL 
                END), 0) +
                IFNULL(AVG(CASE 
                    WHEN ses.semester_id = 1 THEN sss.score 
                    ELSE NULL 
                END), 0)
            ) / 2 +
            (
                IFNULL(AVG(CASE 
                    WHEN FIND_IN_SET(csms.monthly_id, ses2.monthly_ids) THEN sms.score 
                    ELSE NULL 
                END), 0) +
                IFNULL(AVG(CASE 
                    WHEN ses.semester_id = 2 THEN sss.score 
                    ELSE NULL 
                END), 0)
            ) / 2
        ) / 2, 2) AS yearly_avg
    FROM 
        tbl_student_info s
        JOIN tbl_study st ON s.student_id = st.student_id
        JOIN tbl_classroom c ON st.class_id = c.class_id
        LEFT JOIN tbl_student_monthly_score sms ON s.student_id = sms.student_id
        LEFT JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
        LEFT JOIN tbl_student_semester_score sss ON s.student_id = sss.student_id
        LEFT JOIN tbl_semester_exam_subjects ses ON sss.semester_exam_subject_id = ses.id
        LEFT JOIN tbl_semester_exam_subjects ses1 ON ses1.class_id = c.class_id AND ses1.semester_id = 1
        LEFT JOIN tbl_semester_exam_subjects ses2 ON ses2.class_id = c.class_id AND ses2.semester_id = 2
    WHERE 
        c.class_id = p_class_id
        AND st.status = 'active'
        AND s.isDeleted = 0
        AND st.isDeleted = 0
        AND (sms.isDeleted = 0 OR sms.isDeleted IS NULL)
        AND (csms.isDeleted = 0 OR csms.isDeleted IS NULL)
        AND (sss.isDeleted = 0 OR sss.isDeleted IS NULL)
    GROUP BY 
        s.student_id, s.student_name, c.class_id, c.class_name;
END */$$
DELIMITER ;

/*Table structure for table `view_all_students_by_year_study` */

DROP TABLE IF EXISTS `view_all_students_by_year_study`;

/*!50001 DROP VIEW IF EXISTS `view_all_students_by_year_study` */;
/*!50001 DROP TABLE IF EXISTS `view_all_students_by_year_study` */;

/*!50001 CREATE TABLE  `view_all_students_by_year_study`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `gender` varchar(255) ,
 `year_study_id` int(10) unsigned ,
 `year_study` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `grade_id` int(11) ,
 `grade_name` varchar(255) 
)*/;

/*Table structure for table `view_all_students_by_year_study_graduate` */

DROP TABLE IF EXISTS `view_all_students_by_year_study_graduate`;

/*!50001 DROP VIEW IF EXISTS `view_all_students_by_year_study_graduate` */;
/*!50001 DROP TABLE IF EXISTS `view_all_students_by_year_study_graduate` */;

/*!50001 CREATE TABLE  `view_all_students_by_year_study_graduate`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `gender` varchar(255) ,
 `year_study_id` int(10) unsigned ,
 `year_study` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `grade_id` int(11) ,
 `grade_name` varchar(255) 
)*/;

/*Table structure for table `view_final_semester_averages` */

DROP TABLE IF EXISTS `view_final_semester_averages`;

/*!50001 DROP VIEW IF EXISTS `view_final_semester_averages` */;
/*!50001 DROP TABLE IF EXISTS `view_final_semester_averages` */;

/*!50001 CREATE TABLE  `view_final_semester_averages`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `gender` varchar(255) ,
 `semester_id` int(10) unsigned ,
 `semester_name` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `grade_name` varchar(255) ,
 `subject_code` int(10) unsigned ,
 `subject_name` varchar(255) ,
 `subject_score` float ,
 `year_study_id` int(10) unsigned ,
 `year_study` varchar(255) ,
 `monthly_average` double ,
 `semester_exam_average` double ,
 `final_semester_average` double(19,2) ,
 `yearly_average` double(19,2) 
)*/;

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

/*Table structure for table `view_student_monthly_score_report` */

DROP TABLE IF EXISTS `view_student_monthly_score_report`;

/*!50001 DROP VIEW IF EXISTS `view_student_monthly_score_report` */;
/*!50001 DROP TABLE IF EXISTS `view_student_monthly_score_report` */;

/*!50001 CREATE TABLE  `view_student_monthly_score_report`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `gender` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `monthly_id` int(10) unsigned ,
 `month_name` varchar(255) ,
 `subject_code` int(10) unsigned ,
 `subject_name` varchar(255) ,
 `score` float ,
 `isDeleted` int(2) ,
 `year_study_id` int(10) unsigned ,
 `year_study` varchar(255) 
)*/;

/*Table structure for table `view_student_monthly_score_report_for_report_page` */

DROP TABLE IF EXISTS `view_student_monthly_score_report_for_report_page`;

/*!50001 DROP VIEW IF EXISTS `view_student_monthly_score_report_for_report_page` */;
/*!50001 DROP TABLE IF EXISTS `view_student_monthly_score_report_for_report_page` */;

/*!50001 CREATE TABLE  `view_student_monthly_score_report_for_report_page`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `gender` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `monthly_id` int(10) unsigned ,
 `month_name` varchar(255) ,
 `subject_code` int(10) unsigned ,
 `subject_name` varchar(255) ,
 `score` float ,
 `isDeleted` int(2) ,
 `year_study_id` int(10) unsigned ,
 `year_study` varchar(255) 
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

/*Table structure for table `view_student_semester_report` */

DROP TABLE IF EXISTS `view_student_semester_report`;

/*!50001 DROP VIEW IF EXISTS `view_student_semester_report` */;
/*!50001 DROP TABLE IF EXISTS `view_student_semester_report` */;

/*!50001 CREATE TABLE  `view_student_semester_report`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `gender` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `grade_id` int(11) ,
 `grade_name` varchar(255) ,
 `semester_id` int(10) unsigned ,
 `semester_name` varchar(255) ,
 `subject_code` int(10) unsigned ,
 `subject_name` varchar(255) ,
 `subject_score` float ,
 `year_study_id` int(10) unsigned ,
 `year_study` varchar(255) ,
 `monthly_average` double ,
 `semester_exam_average` double ,
 `final_semester_average` double 
)*/;

/*Table structure for table `view_student_semester_report_for_report_page` */

DROP TABLE IF EXISTS `view_student_semester_report_for_report_page`;

/*!50001 DROP VIEW IF EXISTS `view_student_semester_report_for_report_page` */;
/*!50001 DROP TABLE IF EXISTS `view_student_semester_report_for_report_page` */;

/*!50001 CREATE TABLE  `view_student_semester_report_for_report_page`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `gender` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `grade_id` int(11) ,
 `grade_name` varchar(255) ,
 `semester_id` int(10) unsigned ,
 `semester_name` varchar(255) ,
 `subject_code` int(10) unsigned ,
 `subject_name` varchar(255) ,
 `subject_score` float ,
 `year_study_id` int(10) unsigned ,
 `year_study` varchar(255) ,
 `monthly_average` double ,
 `semester_exam_average` double ,
 `final_semester_average` double 
)*/;

/*Table structure for table `view_student_semester_score_report` */

DROP TABLE IF EXISTS `view_student_semester_score_report`;

/*!50001 DROP VIEW IF EXISTS `view_student_semester_score_report` */;
/*!50001 DROP TABLE IF EXISTS `view_student_semester_score_report` */;

/*!50001 CREATE TABLE  `view_student_semester_score_report`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `gender` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `semester_id` int(10) unsigned ,
 `semester_name` varchar(255) ,
 `subject_code` int(10) unsigned ,
 `subject_name` varchar(255) ,
 `score` float ,
 `isDeleted` int(2) 
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

/*Table structure for table `vw_student_first_semester_final_avg` */

DROP TABLE IF EXISTS `vw_student_first_semester_final_avg`;

/*!50001 DROP VIEW IF EXISTS `vw_student_first_semester_final_avg` */;
/*!50001 DROP TABLE IF EXISTS `vw_student_first_semester_final_avg` */;

/*!50001 CREATE TABLE  `vw_student_first_semester_final_avg`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `grade_id` int(11) ,
 `grade_name` varchar(255) ,
 `monthly_average` double ,
 `semester_exam_average` double ,
 `final_semester_average` double 
)*/;

/*Table structure for table `vw_student_semester_scores_with_averages` */

DROP TABLE IF EXISTS `vw_student_semester_scores_with_averages`;

/*!50001 DROP VIEW IF EXISTS `vw_student_semester_scores_with_averages` */;
/*!50001 DROP TABLE IF EXISTS `vw_student_semester_scores_with_averages` */;

/*!50001 CREATE TABLE  `vw_student_semester_scores_with_averages`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `grade_name` varchar(255) ,
 `semester_id` int(10) unsigned ,
 `semester_name` varchar(255) ,
 `subject_name` varchar(255) ,
 `subject_code` int(10) unsigned ,
 `assign_subject_grade_id` int(10) unsigned ,
 `student_semester_score_id` decimal(10,0) ,
 `score` float ,
 `create_date` timestamp ,
 `semester1_avg` double ,
 `semester2_avg` double ,
 `yearly_avg` double(19,2) 
)*/;

/*Table structure for table `vw_top_monthly_rankings` */

DROP TABLE IF EXISTS `vw_top_monthly_rankings`;

/*!50001 DROP VIEW IF EXISTS `vw_top_monthly_rankings` */;
/*!50001 DROP TABLE IF EXISTS `vw_top_monthly_rankings` */;

/*!50001 CREATE TABLE  `vw_top_monthly_rankings`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `monthly_id` int(10) unsigned ,
 `month_name` varchar(255) ,
 `year_study_id` int(10) unsigned ,
 `year_study` varchar(255) ,
 `monthly_avg` double(19,2) ,
 `monthly_rank` bigint(21) 
)*/;

/*Table structure for table `vw_top_semester_rankings` */

DROP TABLE IF EXISTS `vw_top_semester_rankings`;

/*!50001 DROP VIEW IF EXISTS `vw_top_semester_rankings` */;
/*!50001 DROP TABLE IF EXISTS `vw_top_semester_rankings` */;

/*!50001 CREATE TABLE  `vw_top_semester_rankings`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `semester_id` int(10) unsigned ,
 `semester_name` varchar(255) ,
 `year_study_id` int(10) unsigned ,
 `year_study` varchar(255) ,
 `semester_avg` double(19,2) ,
 `semester_rank` bigint(21) 
)*/;

/*Table structure for table `vw_top_yearly_rankings` */

DROP TABLE IF EXISTS `vw_top_yearly_rankings`;

/*!50001 DROP VIEW IF EXISTS `vw_top_yearly_rankings` */;
/*!50001 DROP TABLE IF EXISTS `vw_top_yearly_rankings` */;

/*!50001 CREATE TABLE  `vw_top_yearly_rankings`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `gender` varchar(255) ,
 `dob` date ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `grade_id` int(11) ,
 `grade_name` varchar(255) ,
 `year_study_id` int(10) unsigned ,
 `year_study` varchar(255) ,
 `yearly_avg` double(19,2) ,
 `yearly_rank` bigint(21) 
)*/;

/*Table structure for table `v_getstudentbygrade` */

DROP TABLE IF EXISTS `v_getstudentbygrade`;

/*!50001 DROP VIEW IF EXISTS `v_getstudentbygrade` */;
/*!50001 DROP TABLE IF EXISTS `v_getstudentbygrade` */;

/*!50001 CREATE TABLE  `v_getstudentbygrade`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `gender` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `grade_id` int(11) ,
 `grade_name` varchar(255) 
)*/;

/*Table structure for table `v_getstudentbygradebygraduate` */

DROP TABLE IF EXISTS `v_getstudentbygradebygraduate`;

/*!50001 DROP VIEW IF EXISTS `v_getstudentbygradebygraduate` */;
/*!50001 DROP TABLE IF EXISTS `v_getstudentbygradebygraduate` */;

/*!50001 CREATE TABLE  `v_getstudentbygradebygraduate`(
 `student_id` int(10) unsigned ,
 `student_name` varchar(255) ,
 `gender` varchar(255) ,
 `class_id` int(10) unsigned ,
 `class_name` varchar(255) ,
 `grade_id` int(11) ,
 `grade_name` varchar(255) 
)*/;

/*View structure for view view_all_students_by_year_study */

/*!50001 DROP TABLE IF EXISTS `view_all_students_by_year_study` */;
/*!50001 DROP VIEW IF EXISTS `view_all_students_by_year_study` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_all_students_by_year_study` AS select `s`.`student_id` AS `student_id`,`s`.`student_name` AS `student_name`,`s`.`gender` AS `gender`,`st`.`year_study_id` AS `year_study_id`,`ys`.`year_study` AS `year_study`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`g`.`grade_id` AS `grade_id`,`g`.`grade_name` AS `grade_name` from ((((`tbl_student_info` `s` left join `tbl_study` `st` on(`s`.`student_id` = `st`.`student_id` and `st`.`status` = 'active' and `st`.`isDeleted` = 0)) left join `tbl_year_study` `ys` on(`st`.`year_study_id` = `ys`.`year_study_id`)) left join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) left join `tbl_grade` `g` on(`c`.`grade_id` = `g`.`grade_id`)) where `s`.`isDeleted` = 0 */;

/*View structure for view view_all_students_by_year_study_graduate */

/*!50001 DROP TABLE IF EXISTS `view_all_students_by_year_study_graduate` */;
/*!50001 DROP VIEW IF EXISTS `view_all_students_by_year_study_graduate` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_all_students_by_year_study_graduate` AS select `s`.`student_id` AS `student_id`,`s`.`student_name` AS `student_name`,`s`.`gender` AS `gender`,`st`.`year_study_id` AS `year_study_id`,`ys`.`year_study` AS `year_study`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`g`.`grade_id` AS `grade_id`,`g`.`grade_name` AS `grade_name` from ((((`tbl_student_info` `s` left join `tbl_study` `st` on(`s`.`student_id` = `st`.`student_id` and `st`.`status` = 'graduate' and `st`.`isDeleted` = 0)) left join `tbl_year_study` `ys` on(`st`.`year_study_id` = `ys`.`year_study_id`)) left join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) left join `tbl_grade` `g` on(`c`.`grade_id` = `g`.`grade_id`)) where `s`.`isDeleted` = 0 */;

/*View structure for view view_final_semester_averages */

/*!50001 DROP TABLE IF EXISTS `view_final_semester_averages` */;
/*!50001 DROP VIEW IF EXISTS `view_final_semester_averages` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_final_semester_averages` AS select `si`.`student_id` AS `student_id`,`si`.`student_name` AS `student_name`,`si`.`gender` AS `gender`,`sem`.`semester_id` AS `semester_id`,`sem`.`semester_name` AS `semester_name`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`g`.`grade_name` AS `grade_name`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,`sss`.`score` AS `subject_score`,`ys`.`year_study_id` AS `year_study_id`,`ys`.`year_study` AS `year_study`,(select avg(`sms`.`score`) from ((`tbl_student_monthly_score` `sms` join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) join `tbl_semester_exam_subjects` `ses2` on(`csms`.`class_id` = `ses2`.`class_id` and `ses2`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) where `sms`.`student_id` = `si`.`student_id` and find_in_set(`csms`.`monthly_id`,`ses2`.`monthly_ids`) and `ses2`.`semester_id` = `sem`.`semester_id` and `sms`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0) AS `monthly_average`,(select avg(`sss2`.`score`) from (`tbl_student_semester_score` `sss2` join `tbl_semester_exam_subjects` `ses2` on(`sss2`.`semester_exam_subject_id` = `ses2`.`id`)) where `sss2`.`student_id` = `si`.`student_id` and `ses2`.`semester_id` = `sem`.`semester_id` and `ses2`.`class_id` = `c`.`class_id` and `sss2`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0) AS `semester_exam_average`,round((ifnull((select avg(`sms`.`score`) from ((`tbl_student_monthly_score` `sms` join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) join `tbl_semester_exam_subjects` `ses2` on(`csms`.`class_id` = `ses2`.`class_id` and `ses2`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) where `sms`.`student_id` = `si`.`student_id` and find_in_set(`csms`.`monthly_id`,`ses2`.`monthly_ids`) and `ses2`.`semester_id` = `sem`.`semester_id` and `sms`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0),0) + ifnull((select avg(`sss2`.`score`) from (`tbl_student_semester_score` `sss2` join `tbl_semester_exam_subjects` `ses2` on(`sss2`.`semester_exam_subject_id` = `ses2`.`id`)) where `sss2`.`student_id` = `si`.`student_id` and `ses2`.`semester_id` = `sem`.`semester_id` and `ses2`.`class_id` = `c`.`class_id` and `sss2`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0),0)) / 2,2) AS `final_semester_average`,round(((ifnull((select avg(`sms`.`score`) from ((`tbl_student_monthly_score` `sms` join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) join `tbl_semester_exam_subjects` `ses2` on(`csms`.`class_id` = `ses2`.`class_id` and `ses2`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) where `sms`.`student_id` = `si`.`student_id` and find_in_set(`csms`.`monthly_id`,`ses2`.`monthly_ids`) and `ses2`.`semester_id` = 1 and `sms`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0),0) + ifnull((select avg(`sss2`.`score`) from (`tbl_student_semester_score` `sss2` join `tbl_semester_exam_subjects` `ses2` on(`sss2`.`semester_exam_subject_id` = `ses2`.`id`)) where `sss2`.`student_id` = `si`.`student_id` and `ses2`.`semester_id` = 1 and `ses2`.`class_id` = `c`.`class_id` and `sss2`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0),0)) / 2 + (ifnull((select avg(`sms`.`score`) from ((`tbl_student_monthly_score` `sms` join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) join `tbl_semester_exam_subjects` `ses2` on(`csms`.`class_id` = `ses2`.`class_id` and `ses2`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) where `sms`.`student_id` = `si`.`student_id` and find_in_set(`csms`.`monthly_id`,`ses2`.`monthly_ids`) and `ses2`.`semester_id` = 2 and `sms`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0),0) + ifnull((select avg(`sss2`.`score`) from (`tbl_student_semester_score` `sss2` join `tbl_semester_exam_subjects` `ses2` on(`sss2`.`semester_exam_subject_id` = `ses2`.`id`)) where `sss2`.`student_id` = `si`.`student_id` and `ses2`.`semester_id` = 2 and `ses2`.`class_id` = `c`.`class_id` and `sss2`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0),0)) / 2) / 2,2) AS `yearly_average` from (((((((((`tbl_student_info` `si` join `tbl_student_semester_score` `sss` on(`si`.`student_id` = `sss`.`student_id`)) join `tbl_semester_exam_subjects` `ses` on(`sss`.`semester_exam_subject_id` = `ses`.`id`)) join `tbl_classroom` `c` on(`ses`.`class_id` = `c`.`class_id`)) join `tbl_assign_subject_grade` `asg` on(`ses`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) join `tbl_subject` `sub` on(`asg`.`subject_code` = `sub`.`subject_code`)) join `tbl_grade` `g` on(`asg`.`grade_id` = `g`.`grade_id`)) join `tbl_semester` `sem` on(`ses`.`semester_id` = `sem`.`semester_id`)) join `tbl_study` `st` on(`si`.`student_id` = `st`.`student_id` and `st`.`status` = 'active' and `st`.`isDeleted` = 0)) join `tbl_year_study` `ys` on(`st`.`year_study_id` = `ys`.`year_study_id`)) where `si`.`isDeleted` = 0 and `c`.`isDeleted` = 0 and `sem`.`isDeleted` = 0 and `asg`.`isDeleted` = 0 and `sss`.`isDeleted` = 0 group by `si`.`student_id`,`sem`.`semester_id`,`c`.`class_id`,`sub`.`subject_code` */;

/*View structure for view view_student_monthly_rankings */

/*!50001 DROP TABLE IF EXISTS `view_student_monthly_rankings` */;
/*!50001 DROP VIEW IF EXISTS `view_student_monthly_rankings` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_monthly_rankings` AS select `s`.`student_id` AS `student_id`,`s`.`student_name` AS `student_name`,`s`.`class_id` AS `class_id`,`s`.`class_name` AS `class_name`,`s`.`monthly_id` AS `monthly_id`,`s`.`month_name` AS `month_name`,`s`.`subjects_count` AS `subjects_count`,`s`.`total_score` AS `total_score`,`s`.`average_score` AS `average_score`,rank() over ( partition by `s`.`class_id`,`s`.`monthly_id` order by `s`.`average_score` desc) AS `rank_in_class`,count(0) over ( partition by `s`.`class_id`,`s`.`monthly_id`) AS `class_size` from `view_student_monthly_summary` `s` */;

/*View structure for view view_student_monthly_score_report */

/*!50001 DROP TABLE IF EXISTS `view_student_monthly_score_report` */;
/*!50001 DROP VIEW IF EXISTS `view_student_monthly_score_report` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_monthly_score_report` AS select `s`.`student_id` AS `student_id`,`s`.`student_name` AS `student_name`,`s`.`gender` AS `gender`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`m`.`monthly_id` AS `monthly_id`,`m`.`month_name` AS `month_name`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,`sms`.`score` AS `score`,`sms`.`isDeleted` AS `isDeleted`,`ys`.`year_study_id` AS `year_study_id`,`ys`.`year_study` AS `year_study` from ((((((((`tbl_student_info` `s` join `tbl_study` `st` on(`s`.`student_id` = `st`.`student_id` and `st`.`status` = 'active' and `st`.`isDeleted` = 0)) join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) join `tbl_year_study` `ys` on(`st`.`year_study_id` = `ys`.`year_study_id`)) join `classroom_subject_monthly_score` `csms` on(`c`.`class_id` = `csms`.`class_id`)) join `tbl_assign_subject_grade` `asg` on(`csms`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) join `tbl_subject` `sub` on(`asg`.`subject_code` = `sub`.`subject_code`)) join `tbl_monthly` `m` on(`csms`.`monthly_id` = `m`.`monthly_id`)) left join `tbl_student_monthly_score` `sms` on(`s`.`student_id` = `sms`.`student_id` and `sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id` and `sms`.`isDeleted` = 0)) where `s`.`isDeleted` = 0 and `csms`.`isDeleted` = 0 */;

/*View structure for view view_student_monthly_score_report_for_report_page */

/*!50001 DROP TABLE IF EXISTS `view_student_monthly_score_report_for_report_page` */;
/*!50001 DROP VIEW IF EXISTS `view_student_monthly_score_report_for_report_page` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_monthly_score_report_for_report_page` AS select `s`.`student_id` AS `student_id`,`s`.`student_name` AS `student_name`,`s`.`gender` AS `gender`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`m`.`monthly_id` AS `monthly_id`,`m`.`month_name` AS `month_name`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,`sms`.`score` AS `score`,`sms`.`isDeleted` AS `isDeleted`,`ys`.`year_study_id` AS `year_study_id`,`ys`.`year_study` AS `year_study` from ((((((((`tbl_student_info` `s` join `tbl_study` `st` on(`s`.`student_id` = `st`.`student_id` and `st`.`isDeleted` = 0)) join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) join `tbl_year_study` `ys` on(`st`.`year_study_id` = `ys`.`year_study_id`)) join `classroom_subject_monthly_score` `csms` on(`c`.`class_id` = `csms`.`class_id`)) join `tbl_assign_subject_grade` `asg` on(`csms`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) join `tbl_subject` `sub` on(`asg`.`subject_code` = `sub`.`subject_code`)) join `tbl_monthly` `m` on(`csms`.`monthly_id` = `m`.`monthly_id`)) left join `tbl_student_monthly_score` `sms` on(`s`.`student_id` = `sms`.`student_id` and `sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id` and `sms`.`isDeleted` = 0)) where `s`.`isDeleted` = 0 and `csms`.`isDeleted` = 0 */;

/*View structure for view view_student_monthly_score_summary */

/*!50001 DROP TABLE IF EXISTS `view_student_monthly_score_summary` */;
/*!50001 DROP VIEW IF EXISTS `view_student_monthly_score_summary` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_monthly_score_summary` AS select `sms`.`student_id` AS `student_id`,`si`.`student_name` AS `student_name`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`m`.`monthly_id` AS `monthly_id`,`m`.`month_name` AS `month_name`,count(distinct `asg`.`assign_subject_grade_id`) AS `subjects_count`,sum(`sms`.`score`) AS `total_score`,avg(`sms`.`score`) AS `avg_score`,rank() over ( partition by `c`.`class_id`,`m`.`monthly_id` order by avg(`sms`.`score`) desc) AS `rank_in_class` from (((((`tbl_student_monthly_score` `sms` join `tbl_student_info` `si` on(`sms`.`student_id` = `si`.`student_id`)) join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) join `tbl_classroom` `c` on(`csms`.`class_id` = `c`.`class_id`)) join `tbl_monthly` `m` on(`csms`.`monthly_id` = `m`.`monthly_id`)) join `tbl_assign_subject_grade` `asg` on(`csms`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) where `sms`.`isDeleted` = 0 group by `sms`.`student_id`,`si`.`student_name`,`c`.`class_id`,`c`.`class_name`,`m`.`monthly_id`,`m`.`month_name` */;

/*View structure for view view_student_monthly_summary */

/*!50001 DROP TABLE IF EXISTS `view_student_monthly_summary` */;
/*!50001 DROP VIEW IF EXISTS `view_student_monthly_summary` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_monthly_summary` AS select `sms`.`student_id` AS `student_id`,`si`.`student_name` AS `student_name`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`csms`.`monthly_id` AS `monthly_id`,`m`.`month_name` AS `month_name`,count(distinct `asg`.`assign_subject_grade_id`) AS `subjects_count`,sum(`sms`.`score`) AS `total_score`,avg(`sms`.`score`) AS `average_score` from (((((`tbl_student_monthly_score` `sms` join `tbl_student_info` `si` on(`sms`.`student_id` = `si`.`student_id`)) join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) join `tbl_classroom` `c` on(`csms`.`class_id` = `c`.`class_id`)) join `tbl_monthly` `m` on(`csms`.`monthly_id` = `m`.`monthly_id`)) join `tbl_assign_subject_grade` `asg` on(`csms`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) where `sms`.`isDeleted` = 0 group by `sms`.`student_id`,`si`.`student_name`,`c`.`class_id`,`c`.`class_name`,`csms`.`monthly_id`,`m`.`month_name` */;

/*View structure for view view_student_semester_report */

/*!50001 DROP TABLE IF EXISTS `view_student_semester_report` */;
/*!50001 DROP VIEW IF EXISTS `view_student_semester_report` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_semester_report` AS select `si`.`student_id` AS `student_id`,`si`.`student_name` AS `student_name`,`si`.`gender` AS `gender`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`g`.`grade_id` AS `grade_id`,`g`.`grade_name` AS `grade_name`,`sem`.`semester_id` AS `semester_id`,`sem`.`semester_name` AS `semester_name`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,(select `sss2`.`score` from (`tbl_student_semester_score` `sss2` join `tbl_semester_exam_subjects` `ses2` on(`sss2`.`semester_exam_subject_id` = `ses2`.`id`)) where `sss2`.`student_id` = `si`.`student_id` and `ses2`.`semester_id` = `sem`.`semester_id` and `ses2`.`class_id` = `c`.`class_id` and `sss2`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0 and `ses2`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id` limit 1) AS `subject_score`,`ys`.`year_study_id` AS `year_study_id`,`ys`.`year_study` AS `year_study`,(select avg(`sms`.`score`) from ((`tbl_student_monthly_score` `sms` join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) join `tbl_semester_exam_subjects` `ses2` on(`csms`.`class_id` = `ses2`.`class_id` and `ses2`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) where `sms`.`student_id` = `si`.`student_id` and find_in_set(`csms`.`monthly_id`,`ses2`.`monthly_ids`) and `ses2`.`semester_id` = `sem`.`semester_id` and `sms`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0) AS `monthly_average`,(select avg(`sss2`.`score`) from (`tbl_student_semester_score` `sss2` join `tbl_semester_exam_subjects` `ses2` on(`sss2`.`semester_exam_subject_id` = `ses2`.`id`)) where `sss2`.`student_id` = `si`.`student_id` and `ses2`.`semester_id` = `sem`.`semester_id` and `ses2`.`class_id` = `c`.`class_id` and `sss2`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0) AS `semester_exam_average`,(ifnull((select avg(`sms`.`score`) from ((`tbl_student_monthly_score` `sms` join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) join `tbl_semester_exam_subjects` `ses2` on(`csms`.`class_id` = `ses2`.`class_id` and `ses2`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) where `sms`.`student_id` = `si`.`student_id` and find_in_set(`csms`.`monthly_id`,`ses2`.`monthly_ids`) and `ses2`.`semester_id` = `sem`.`semester_id` and `sms`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0),0) + ifnull((select avg(`sss2`.`score`) from (`tbl_student_semester_score` `sss2` join `tbl_semester_exam_subjects` `ses2` on(`sss2`.`semester_exam_subject_id` = `ses2`.`id`)) where `sss2`.`student_id` = `si`.`student_id` and `ses2`.`semester_id` = `sem`.`semester_id` and `ses2`.`class_id` = `c`.`class_id` and `sss2`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0),0)) / 2 AS `final_semester_average` from (((((((((`tbl_student_semester_score` `sss` join `tbl_student_info` `si` on(`sss`.`student_id` = `si`.`student_id`)) join `tbl_semester_exam_subjects` `ses` on(`sss`.`semester_exam_subject_id` = `ses`.`id`)) join `tbl_classroom` `c` on(`ses`.`class_id` = `c`.`class_id`)) join `tbl_assign_subject_grade` `asg` on(`ses`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) join `tbl_subject` `sub` on(`asg`.`subject_code` = `sub`.`subject_code`)) join `tbl_grade` `g` on(`asg`.`grade_id` = `g`.`grade_id`)) join `tbl_semester` `sem` on(`ses`.`semester_id` = `sem`.`semester_id`)) join `tbl_study` `st` on(`si`.`student_id` = `st`.`student_id` and `st`.`class_id` = `c`.`class_id` and `st`.`status` = 'active' and `st`.`isDeleted` = 0)) join `tbl_year_study` `ys` on(`st`.`year_study_id` = `ys`.`year_study_id`)) where `sss`.`isDeleted` = 0 and `si`.`isDeleted` = 0 and `c`.`isDeleted` = 0 and `sem`.`isDeleted` = 0 and `asg`.`isDeleted` = 0 group by `si`.`student_id`,`c`.`class_id`,`sem`.`semester_id`,`sub`.`subject_code`,`ys`.`year_study_id` */;

/*View structure for view view_student_semester_report_for_report_page */

/*!50001 DROP TABLE IF EXISTS `view_student_semester_report_for_report_page` */;
/*!50001 DROP VIEW IF EXISTS `view_student_semester_report_for_report_page` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_semester_report_for_report_page` AS select `si`.`student_id` AS `student_id`,`si`.`student_name` AS `student_name`,`si`.`gender` AS `gender`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`g`.`grade_id` AS `grade_id`,`g`.`grade_name` AS `grade_name`,`sem`.`semester_id` AS `semester_id`,`sem`.`semester_name` AS `semester_name`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,(select `sss2`.`score` from (`tbl_student_semester_score` `sss2` join `tbl_semester_exam_subjects` `ses2` on(`sss2`.`semester_exam_subject_id` = `ses2`.`id`)) where `sss2`.`student_id` = `si`.`student_id` and `ses2`.`semester_id` = `sem`.`semester_id` and `ses2`.`class_id` = `c`.`class_id` and `sss2`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0 and `ses2`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id` limit 1) AS `subject_score`,`ys`.`year_study_id` AS `year_study_id`,`ys`.`year_study` AS `year_study`,(select avg(`sms`.`score`) from ((`tbl_student_monthly_score` `sms` join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) join `tbl_semester_exam_subjects` `ses2` on(`csms`.`class_id` = `ses2`.`class_id` and `ses2`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) where `sms`.`student_id` = `si`.`student_id` and find_in_set(`csms`.`monthly_id`,`ses2`.`monthly_ids`) and `ses2`.`semester_id` = `sem`.`semester_id` and `sms`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0) AS `monthly_average`,(select avg(`sss2`.`score`) from (`tbl_student_semester_score` `sss2` join `tbl_semester_exam_subjects` `ses2` on(`sss2`.`semester_exam_subject_id` = `ses2`.`id`)) where `sss2`.`student_id` = `si`.`student_id` and `ses2`.`semester_id` = `sem`.`semester_id` and `ses2`.`class_id` = `c`.`class_id` and `sss2`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0) AS `semester_exam_average`,(ifnull((select avg(`sms`.`score`) from ((`tbl_student_monthly_score` `sms` join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) join `tbl_semester_exam_subjects` `ses2` on(`csms`.`class_id` = `ses2`.`class_id` and `ses2`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) where `sms`.`student_id` = `si`.`student_id` and find_in_set(`csms`.`monthly_id`,`ses2`.`monthly_ids`) and `ses2`.`semester_id` = `sem`.`semester_id` and `sms`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0),0) + ifnull((select avg(`sss2`.`score`) from (`tbl_student_semester_score` `sss2` join `tbl_semester_exam_subjects` `ses2` on(`sss2`.`semester_exam_subject_id` = `ses2`.`id`)) where `sss2`.`student_id` = `si`.`student_id` and `ses2`.`semester_id` = `sem`.`semester_id` and `ses2`.`class_id` = `c`.`class_id` and `sss2`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0),0)) / 2 AS `final_semester_average` from (((((((((`tbl_student_semester_score` `sss` join `tbl_student_info` `si` on(`sss`.`student_id` = `si`.`student_id`)) join `tbl_semester_exam_subjects` `ses` on(`sss`.`semester_exam_subject_id` = `ses`.`id`)) join `tbl_classroom` `c` on(`ses`.`class_id` = `c`.`class_id`)) join `tbl_assign_subject_grade` `asg` on(`ses`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) join `tbl_subject` `sub` on(`asg`.`subject_code` = `sub`.`subject_code`)) join `tbl_grade` `g` on(`asg`.`grade_id` = `g`.`grade_id`)) join `tbl_semester` `sem` on(`ses`.`semester_id` = `sem`.`semester_id`)) join `tbl_study` `st` on(`si`.`student_id` = `st`.`student_id` and `st`.`class_id` = `c`.`class_id` and `st`.`isDeleted` = 0)) join `tbl_year_study` `ys` on(`st`.`year_study_id` = `ys`.`year_study_id`)) where `sss`.`isDeleted` = 0 and `si`.`isDeleted` = 0 and `c`.`isDeleted` = 0 and `sem`.`isDeleted` = 0 and `asg`.`isDeleted` = 0 group by `si`.`student_id`,`c`.`class_id`,`sem`.`semester_id`,`sub`.`subject_code`,`ys`.`year_study_id` */;

/*View structure for view view_student_semester_score_report */

/*!50001 DROP TABLE IF EXISTS `view_student_semester_score_report` */;
/*!50001 DROP VIEW IF EXISTS `view_student_semester_score_report` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_semester_score_report` AS select `s`.`student_id` AS `student_id`,`s`.`student_name` AS `student_name`,`s`.`gender` AS `gender`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`se`.`semester_id` AS `semester_id`,`se`.`semester_name` AS `semester_name`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,`sss`.`score` AS `score`,`sss`.`isDeleted` AS `isDeleted` from (((((((`tbl_student_info` `s` join `tbl_study` `st` on(`s`.`student_id` = `st`.`student_id` and `st`.`status` = 'active' and `st`.`isDeleted` = 0)) join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) join `tbl_semester_exam_subjects` `ses` on(`c`.`class_id` = `ses`.`class_id`)) join `tbl_assign_subject_grade` `asg` on(`ses`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) join `tbl_subject` `sub` on(`asg`.`subject_code` = `sub`.`subject_code`)) join `tbl_semester` `se` on(`ses`.`semester_id` = `se`.`semester_id`)) left join `tbl_student_semester_score` `sss` on(`s`.`student_id` = `sss`.`student_id` and `sss`.`semester_exam_subject_id` = `ses`.`id` and `sss`.`isDeleted` = 0)) where `s`.`isDeleted` = 0 and `ses`.`isDeleted` = 0 */;

/*View structure for view vstudentmonthlyscorereport */

/*!50001 DROP TABLE IF EXISTS `vstudentmonthlyscorereport` */;
/*!50001 DROP VIEW IF EXISTS `vstudentmonthlyscorereport` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vstudentmonthlyscorereport` AS select `si`.`student_id` AS `student_id`,`si`.`student_name` AS `student_name`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`m`.`monthly_id` AS `monthly_id`,`m`.`month_name` AS `month_name`,group_concat(concat(`sub`.`subject_name`,': ',`sms`.`score`) order by `sub`.`subject_name` ASC separator ', ') AS `subject_scores` from (((((((`tbl_student_info` `si` join `tbl_study` `st` on(`si`.`student_id` = `st`.`student_id` and `st`.`status` = 'active' and `st`.`isDeleted` = 0)) join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) join `tbl_student_monthly_score` `sms` on(`si`.`student_id` = `sms`.`student_id`)) join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) join `tbl_monthly` `m` on(`csms`.`monthly_id` = `m`.`monthly_id`)) join `tbl_assign_subject_grade` `asg` on(`csms`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) join `tbl_subject` `sub` on(`asg`.`subject_code` = `sub`.`subject_code`)) where `si`.`isDeleted` = 0 and `sms`.`isDeleted` = 0 group by `si`.`student_id`,`si`.`student_name`,`c`.`class_id`,`c`.`class_name`,`m`.`monthly_id`,`m`.`month_name` order by `si`.`student_name`,`m`.`monthly_id` */;

/*View structure for view vstudentmonthlyscorereportv2 */

/*!50001 DROP TABLE IF EXISTS `vstudentmonthlyscorereportv2` */;
/*!50001 DROP VIEW IF EXISTS `vstudentmonthlyscorereportv2` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vstudentmonthlyscorereportv2` AS select `si`.`student_id` AS `student_id`,`si`.`student_name` AS `student_name`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`m`.`monthly_id` AS `monthly_id`,`m`.`month_name` AS `month_name`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,coalesce(`sms`.`score`,NULL) AS `score` from (((((((`tbl_student_info` `si` join `tbl_study` `st` on(`si`.`student_id` = `st`.`student_id` and `st`.`status` = 'active' and `st`.`isDeleted` = 0)) join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) join `classroom_subject_monthly_score` `csms` on(`c`.`class_id` = `csms`.`class_id`)) join `tbl_monthly` `m` on(`csms`.`monthly_id` = `m`.`monthly_id`)) join `tbl_assign_subject_grade` `asg` on(`csms`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) join `tbl_subject` `sub` on(`asg`.`subject_code` = `sub`.`subject_code`)) left join `tbl_student_monthly_score` `sms` on(`si`.`student_id` = `sms`.`student_id` and `sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) where `si`.`isDeleted` = 0 and `csms`.`isDeleted` = 0 order by `si`.`student_name`,`m`.`monthly_id`,`sub`.`subject_name` */;

/*View structure for view vw_student_first_semester_final_avg */

/*!50001 DROP TABLE IF EXISTS `vw_student_first_semester_final_avg` */;
/*!50001 DROP VIEW IF EXISTS `vw_student_first_semester_final_avg` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_student_first_semester_final_avg` AS select `view_student_semester_report`.`student_id` AS `student_id`,`view_student_semester_report`.`student_name` AS `student_name`,`view_student_semester_report`.`class_id` AS `class_id`,`view_student_semester_report`.`class_name` AS `class_name`,`view_student_semester_report`.`grade_id` AS `grade_id`,`view_student_semester_report`.`grade_name` AS `grade_name`,`view_student_semester_report`.`monthly_average` AS `monthly_average`,`view_student_semester_report`.`semester_exam_average` AS `semester_exam_average`,`view_student_semester_report`.`final_semester_average` AS `final_semester_average` from `view_student_semester_report` where `view_student_semester_report`.`semester_id` = 1 */;

/*View structure for view vw_student_semester_scores_with_averages */

/*!50001 DROP TABLE IF EXISTS `vw_student_semester_scores_with_averages` */;
/*!50001 DROP VIEW IF EXISTS `vw_student_semester_scores_with_averages` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_student_semester_scores_with_averages` AS select `s`.`student_id` AS `student_id`,`s`.`student_name` AS `student_name`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`g`.`grade_name` AS `grade_name`,`sem`.`semester_id` AS `semester_id`,`sem`.`semester_name` AS `semester_name`,`sub`.`subject_name` AS `subject_name`,`sub`.`subject_code` AS `subject_code`,`asg`.`assign_subject_grade_id` AS `assign_subject_grade_id`,coalesce(`sss`.`student_semester_score_id`,NULL) AS `student_semester_score_id`,coalesce(`sss`.`score`,NULL) AS `score`,`sss`.`create_date` AS `create_date`,(select avg(`sss1`.`score`) from (`tbl_student_semester_score` `sss1` join `tbl_semester_exam_subjects` `ses1` on(`sss1`.`semester_exam_subject_id` = `ses1`.`id`)) where `sss1`.`student_id` = `s`.`student_id` and `ses1`.`semester_id` = 1 and `ses1`.`class_id` = `c`.`class_id` and `sss1`.`isDeleted` = 0 and `ses1`.`isDeleted` = 0) AS `semester1_avg`,(select avg(`sss2`.`score`) from (`tbl_student_semester_score` `sss2` join `tbl_semester_exam_subjects` `ses2` on(`sss2`.`semester_exam_subject_id` = `ses2`.`id`)) where `sss2`.`student_id` = `s`.`student_id` and `ses2`.`semester_id` = 2 and `ses2`.`class_id` = `c`.`class_id` and `sss2`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0) AS `semester2_avg`,(select round((ifnull((select avg(`sss1`.`score`) from (`tbl_student_semester_score` `sss1` join `tbl_semester_exam_subjects` `ses1` on(`sss1`.`semester_exam_subject_id` = `ses1`.`id`)) where `sss1`.`student_id` = `s`.`student_id` and `ses1`.`semester_id` = 1 and `ses1`.`class_id` = `c`.`class_id` and `sss1`.`isDeleted` = 0 and `ses1`.`isDeleted` = 0),0) + ifnull((select avg(`sss2`.`score`) from (`tbl_student_semester_score` `sss2` join `tbl_semester_exam_subjects` `ses2` on(`sss2`.`semester_exam_subject_id` = `ses2`.`id`)) where `sss2`.`student_id` = `s`.`student_id` and `ses2`.`semester_id` = 2 and `ses2`.`class_id` = `c`.`class_id` and `sss2`.`isDeleted` = 0 and `ses2`.`isDeleted` = 0),0)) / 2,2)) AS `yearly_avg` from ((((((((`tbl_student_info` `s` join `tbl_study` `st` on(`s`.`student_id` = `st`.`student_id` and `st`.`status` = 'active' and `st`.`isDeleted` = 0)) join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) join `tbl_grade` `g` on(`c`.`grade_id` = `g`.`grade_id`)) join `tbl_semester_exam_subjects` `ses` on(`c`.`class_id` = `ses`.`class_id` and `ses`.`isDeleted` = 0)) join `tbl_assign_subject_grade` `asg` on(`ses`.`assign_subject_grade_id` = `asg`.`assign_subject_grade_id`)) join `tbl_subject` `sub` on(`asg`.`subject_code` = `sub`.`subject_code`)) join `tbl_semester` `sem` on(`ses`.`semester_id` = `sem`.`semester_id`)) left join `tbl_student_semester_score` `sss` on(`s`.`student_id` = `sss`.`student_id` and `sss`.`semester_exam_subject_id` = `ses`.`id` and `sss`.`isDeleted` = 0)) where `s`.`isDeleted` = 0 order by `s`.`student_name`,`sub`.`subject_name` */;

/*View structure for view vw_top_monthly_rankings */

/*!50001 DROP TABLE IF EXISTS `vw_top_monthly_rankings` */;
/*!50001 DROP VIEW IF EXISTS `vw_top_monthly_rankings` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_top_monthly_rankings` AS with MonthlyRankings as (select `s`.`student_id` AS `student_id`,`s`.`student_name` AS `student_name`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`csms`.`monthly_id` AS `monthly_id`,`m`.`month_name` AS `month_name`,`ys`.`year_study_id` AS `year_study_id`,`ys`.`year_study` AS `year_study`,round(avg(`sms`.`score`),2) AS `monthly_avg`,row_number() over ( partition by `c`.`class_id`,`csms`.`monthly_id`,`ys`.`year_study_id` order by avg(`sms`.`score`) desc) AS `monthly_rank` from ((((((`tbl_student_info` `s` join `tbl_study` `st` on(`s`.`student_id` = `st`.`student_id`)) join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) join `tbl_year_study` `ys` on(`st`.`year_study_id` = `ys`.`year_study_id`)) left join `tbl_student_monthly_score` `sms` on(`s`.`student_id` = `sms`.`student_id`)) left join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) left join `tbl_monthly` `m` on(`csms`.`monthly_id` = `m`.`monthly_id`)) where `st`.`status` = 'active' and `s`.`isDeleted` = 0 and `st`.`isDeleted` = 0 and (`sms`.`isDeleted` = 0 or `sms`.`isDeleted` is null) and (`csms`.`isDeleted` = 0 or `csms`.`isDeleted` is null) group by `s`.`student_id`,`s`.`student_name`,`c`.`class_id`,`c`.`class_name`,`csms`.`monthly_id`,`m`.`month_name`,`ys`.`year_study_id`,`ys`.`year_study`)select `monthlyrankings`.`student_id` AS `student_id`,`monthlyrankings`.`student_name` AS `student_name`,`monthlyrankings`.`class_id` AS `class_id`,`monthlyrankings`.`class_name` AS `class_name`,`monthlyrankings`.`monthly_id` AS `monthly_id`,`monthlyrankings`.`month_name` AS `month_name`,`monthlyrankings`.`year_study_id` AS `year_study_id`,`monthlyrankings`.`year_study` AS `year_study`,`monthlyrankings`.`monthly_avg` AS `monthly_avg`,`monthlyrankings`.`monthly_rank` AS `monthly_rank` from `monthlyrankings` where `monthlyrankings`.`monthly_rank` <= 5 */;

/*View structure for view vw_top_semester_rankings */

/*!50001 DROP TABLE IF EXISTS `vw_top_semester_rankings` */;
/*!50001 DROP VIEW IF EXISTS `vw_top_semester_rankings` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_top_semester_rankings` AS with SemesterRankings as (select `s`.`student_id` AS `student_id`,`s`.`student_name` AS `student_name`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`ses`.`semester_id` AS `semester_id`,`sem`.`semester_name` AS `semester_name`,`ys`.`year_study_id` AS `year_study_id`,`ys`.`year_study` AS `year_study`,round(avg(`sss`.`score`),2) AS `semester_avg`,row_number() over ( partition by `c`.`class_id`,`ses`.`semester_id`,`ys`.`year_study_id` order by avg(`sss`.`score`) desc) AS `semester_rank` from ((((((`tbl_student_info` `s` join `tbl_study` `st` on(`s`.`student_id` = `st`.`student_id`)) join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) join `tbl_year_study` `ys` on(`st`.`year_study_id` = `ys`.`year_study_id`)) left join `tbl_student_semester_score` `sss` on(`s`.`student_id` = `sss`.`student_id`)) left join `tbl_semester_exam_subjects` `ses` on(`sss`.`semester_exam_subject_id` = `ses`.`id`)) left join `tbl_semester` `sem` on(`ses`.`semester_id` = `sem`.`semester_id`)) where `st`.`status` = 'active' and `s`.`isDeleted` = 0 and `st`.`isDeleted` = 0 and (`sss`.`isDeleted` = 0 or `sss`.`isDeleted` is null) group by `s`.`student_id`,`s`.`student_name`,`c`.`class_id`,`c`.`class_name`,`ses`.`semester_id`,`sem`.`semester_name`,`ys`.`year_study_id`,`ys`.`year_study`)select `semesterrankings`.`student_id` AS `student_id`,`semesterrankings`.`student_name` AS `student_name`,`semesterrankings`.`class_id` AS `class_id`,`semesterrankings`.`class_name` AS `class_name`,`semesterrankings`.`semester_id` AS `semester_id`,`semesterrankings`.`semester_name` AS `semester_name`,`semesterrankings`.`year_study_id` AS `year_study_id`,`semesterrankings`.`year_study` AS `year_study`,`semesterrankings`.`semester_avg` AS `semester_avg`,`semesterrankings`.`semester_rank` AS `semester_rank` from `semesterrankings` where `semesterrankings`.`semester_rank` <= 5 */;

/*View structure for view vw_top_yearly_rankings */

/*!50001 DROP TABLE IF EXISTS `vw_top_yearly_rankings` */;
/*!50001 DROP VIEW IF EXISTS `vw_top_yearly_rankings` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_top_yearly_rankings` AS with StudentRankings as (select `s`.`student_id` AS `student_id`,`s`.`student_name` AS `student_name`,`s`.`gender` AS `gender`,`s`.`dob` AS `dob`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`g`.`grade_id` AS `grade_id`,`g`.`grade_name` AS `grade_name`,`ys`.`year_study_id` AS `year_study_id`,`ys`.`year_study` AS `year_study`,round(((ifnull(avg(case when find_in_set(`csms`.`monthly_id`,`ses1`.`monthly_ids`) then `sms`.`score` else NULL end),0) + ifnull(avg(case when `ses`.`semester_id` = 1 then `sss`.`score` else NULL end),0)) / 2 + (ifnull(avg(case when find_in_set(`csms`.`monthly_id`,`ses2`.`monthly_ids`) then `sms`.`score` else NULL end),0) + ifnull(avg(case when `ses`.`semester_id` = 2 then `sss`.`score` else NULL end),0)) / 2) / 2,2) AS `yearly_avg`,dense_rank() over ( partition by `c`.`class_id`,`ys`.`year_study_id` order by round(((ifnull(avg(case when find_in_set(`csms`.`monthly_id`,`ses1`.`monthly_ids`) then `sms`.`score` else NULL end),0) + ifnull(avg(case when `ses`.`semester_id` = 1 then `sss`.`score` else NULL end),0)) / 2 + (ifnull(avg(case when find_in_set(`csms`.`monthly_id`,`ses2`.`monthly_ids`) then `sms`.`score` else NULL end),0) + ifnull(avg(case when `ses`.`semester_id` = 2 then `sss`.`score` else NULL end),0)) / 2) / 2,2) desc) AS `yearly_rank` from ((((((((((`tbl_student_info` `s` join `tbl_study` `st` on(`s`.`student_id` = `st`.`student_id`)) join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) join `tbl_grade` `g` on(`c`.`grade_id` = `g`.`grade_id`)) join `tbl_year_study` `ys` on(`st`.`year_study_id` = `ys`.`year_study_id`)) left join `tbl_student_monthly_score` `sms` on(`s`.`student_id` = `sms`.`student_id`)) left join `classroom_subject_monthly_score` `csms` on(`sms`.`classroom_subject_monthly_score_id` = `csms`.`classroom_subject_monthly_score_id`)) left join `tbl_student_semester_score` `sss` on(`s`.`student_id` = `sss`.`student_id`)) left join `tbl_semester_exam_subjects` `ses` on(`sss`.`semester_exam_subject_id` = `ses`.`id`)) left join `tbl_semester_exam_subjects` `ses1` on(`ses1`.`class_id` = `c`.`class_id` and `ses1`.`semester_id` = 1)) left join `tbl_semester_exam_subjects` `ses2` on(`ses2`.`class_id` = `c`.`class_id` and `ses2`.`semester_id` = 2)) where `st`.`status` = 'active' and `s`.`isDeleted` = 0 and `st`.`isDeleted` = 0 and (`sms`.`isDeleted` = 0 or `sms`.`isDeleted` is null) and (`csms`.`isDeleted` = 0 or `csms`.`isDeleted` is null) and (`sss`.`isDeleted` = 0 or `sss`.`isDeleted` is null) group by `s`.`student_id`,`s`.`student_name`,`c`.`class_id`,`c`.`class_name`,`g`.`grade_id`,`g`.`grade_name`,`ys`.`year_study_id`,`ys`.`year_study`)select `studentrankings`.`student_id` AS `student_id`,`studentrankings`.`student_name` AS `student_name`,`studentrankings`.`gender` AS `gender`,`studentrankings`.`dob` AS `dob`,`studentrankings`.`class_id` AS `class_id`,`studentrankings`.`class_name` AS `class_name`,`studentrankings`.`grade_id` AS `grade_id`,`studentrankings`.`grade_name` AS `grade_name`,`studentrankings`.`year_study_id` AS `year_study_id`,`studentrankings`.`year_study` AS `year_study`,`studentrankings`.`yearly_avg` AS `yearly_avg`,`studentrankings`.`yearly_rank` AS `yearly_rank` from `studentrankings` where `studentrankings`.`yearly_rank` <= 5 order by `studentrankings`.`class_id`,`studentrankings`.`year_study_id`,`studentrankings`.`yearly_rank` */;

/*View structure for view v_getstudentbygrade */

/*!50001 DROP TABLE IF EXISTS `v_getstudentbygrade` */;
/*!50001 DROP VIEW IF EXISTS `v_getstudentbygrade` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_getstudentbygrade` AS select `s`.`student_id` AS `student_id`,`s`.`student_name` AS `student_name`,`s`.`gender` AS `gender`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`g`.`grade_id` AS `grade_id`,`g`.`grade_name` AS `grade_name` from (((`tbl_study` `st` join `tbl_student_info` `s` on(`st`.`student_id` = `s`.`student_id`)) join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) join `tbl_grade` `g` on(`c`.`grade_id` = `g`.`grade_id`)) where `st`.`isDeleted` = 0 and `s`.`isDeleted` = 0 and `c`.`isDeleted` = 0 and `st`.`status` = 'active' */;

/*View structure for view v_getstudentbygradebygraduate */

/*!50001 DROP TABLE IF EXISTS `v_getstudentbygradebygraduate` */;
/*!50001 DROP VIEW IF EXISTS `v_getstudentbygradebygraduate` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_getstudentbygradebygraduate` AS select `s`.`student_id` AS `student_id`,`s`.`student_name` AS `student_name`,`s`.`gender` AS `gender`,`c`.`class_id` AS `class_id`,`c`.`class_name` AS `class_name`,`g`.`grade_id` AS `grade_id`,`g`.`grade_name` AS `grade_name` from (((`tbl_study` `st` join `tbl_student_info` `s` on(`st`.`student_id` = `s`.`student_id`)) join `tbl_classroom` `c` on(`st`.`class_id` = `c`.`class_id`)) join `tbl_grade` `g` on(`c`.`grade_id` = `g`.`grade_id`)) where `st`.`isDeleted` = 0 and `s`.`isDeleted` = 0 and `c`.`isDeleted` = 0 and `st`.`status` = 'graduate' */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
