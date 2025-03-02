/*
SQLyog Enterprise - MySQL GUI v8.18 
MySQL - 8.3.0 : Database - librarydb
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`librarydb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `librarydb`;

/*Table structure for table `absences` */

DROP TABLE IF EXISTS `absences`;

CREATE TABLE `absences` (
  `id` int NOT NULL AUTO_INCREMENT,
  `studentId` int DEFAULT NULL,
  `classId` int DEFAULT NULL,
  `date` date NOT NULL,
  `absence` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `presence` varchar(50) DEFAULT NULL,
  `law` varchar(20) NOT NULL,
  `status` int NOT NULL,
  `createdAt` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `absences` */

insert  into `absences`(`id`,`studentId`,`classId`,`date`,`absence`,`presence`,`law`,`status`,`createdAt`) values (1,1,1,'2025-02-21','Absent','A','',0,'0000-00-00 00:00:00'),(2,3,1,'2025-02-21',NULL,'S','',0,'0000-00-00 00:00:00'),(3,2,1,'2025-02-21',NULL,NULL,'D',0,'0000-00-00 00:00:00'),(4,1,1,'2025-02-26','Absent','A','',0,'0000-00-00 00:00:00'),(5,2,1,'2025-02-26','Absent',NULL,'',0,'0000-00-00 00:00:00'),(6,6,1,'2025-02-26',NULL,NULL,'ឈឺក្បាល',0,'0000-00-00 00:00:00'),(7,2,1,'2025-02-27',NULL,'A','',0,'0000-00-00 00:00:00'),(8,1,1,'2025-02-27',NULL,NULL,'ឈឺក្បាល',0,'0000-00-00 00:00:00');

/*Table structure for table `attendance` */

DROP TABLE IF EXISTS `attendance`;

CREATE TABLE `attendance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `studentId` int DEFAULT NULL,
  `classId` int DEFAULT NULL,
  `subjectId` int DEFAULT NULL,
  `attendanceDate` date NOT NULL,
  `time_slot_id` int DEFAULT NULL,
  `status` enum('PS','P','AL','A') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'PS=មកទាន់ម៉ោង, P=មកយឺត, AL=សុំច្បាប់, A=អវត្តមាន',
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `attendance` */

/*Table structure for table `book` */

DROP TABLE IF EXISTS `book`;

CREATE TABLE `book` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bookCode` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `bookName` varchar(255) NOT NULL,
  `bookId` int DEFAULT NULL,
  `authorName` varchar(255) DEFAULT NULL,
  `printId` int DEFAULT NULL,
  `publishYear` varchar(100) DEFAULT NULL,
  `price` int NOT NULL,
  `qty` int NOT NULL,
  `categoryId` int DEFAULT NULL,
  `categoryShelf` int DEFAULT NULL,
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `createdAt` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `book` */

insert  into `book`(`id`,`bookCode`,`image`,`bookName`,`bookId`,`authorName`,`printId`,`publishYear`,`price`,`qty`,`categoryId`,`categoryShelf`,`status`,`createdAt`) values (1,'10001','8.png','Digital Kids Starter',NULL,'Binary Logic',NULL,'2018',15000,20,1,NULL,'in_use','0000-00-00 00:00:00'),(4,'10003','5.png','កុលាប',NULL,'Binary Logic SA',NULL,'1908',12000,20,1,NULL,'in_use','0000-00-00 00:00:00'),(5,'100004','5.png','ផ្កាស្រពោន',NULL,'ឆឺត ឆុង',NULL,'1911',12500,19,1,NULL,'1','0000-00-00 00:00:00');

/*Table structure for table `borrow` */

DROP TABLE IF EXISTS `borrow`;

CREATE TABLE `borrow` (
  `id` int NOT NULL AUTO_INCREMENT,
  `studentId` int NOT NULL,
  `bookId` int DEFAULT NULL,
  `qty` int NOT NULL,
  `borrowDate` varchar(255) NOT NULL,
  `returnDate` varchar(255) NOT NULL,
  `actual_return_date` varchar(100) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `studentpayId` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `createdAt` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `borrow` */

insert  into `borrow`(`id`,`studentId`,`bookId`,`qty`,`borrowDate`,`returnDate`,`actual_return_date`,`remark`,`studentpayId`,`status`,`createdAt`) values (16,5,4,1,'27-February-2025','06-March-2025','2025-02-27',NULL,NULL,0,'2025-02-27 16:29:55'),(17,6,1,1,'22-February-2025','06-March-2025','2025-02-27',NULL,NULL,0,'2025-02-27 16:30:20'),(15,4,1,1,'27-February-2025','06-March-2025','2025-02-27',NULL,NULL,0,'2025-02-27 16:28:03'),(14,6,1,1,'27-February-2025','06-March-2025','2025-02-27',NULL,NULL,0,'2025-02-27 16:25:25'),(18,6,1,1,'27-February-2025','06-March-2025','',NULL,NULL,1,'2025-02-27 16:50:59'),(19,4,1,1,'28-February-2025','07-March-2025','2025-02-28',NULL,NULL,0,'2025-02-28 01:13:40'),(20,4,1,1,'28-February-2025','07-March-2025','','',NULL,1,'2025-02-28 10:44:47'),(21,6,4,1,'28-February-2025','07-March-2025','','',NULL,1,'2025-02-28 10:45:24'),(22,6,4,1,'01-March-2025','08-March-2025','','',NULL,1,'2025-03-01 18:06:43'),(23,4,5,1,'01-March-2025','08-March-2025','2025-03-01','',NULL,0,'2025-03-01 18:07:24'),(24,5,5,1,'01-March-2025','08-March-2025','','',NULL,1,'2025-03-01 18:27:50');

/*Table structure for table `brand` */

DROP TABLE IF EXISTS `brand`;

CREATE TABLE `brand` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(225) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `createdAt` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `brand` */

insert  into `brand`(`id`,`image`,`name`,`address`,`facebook`,`phone`,`email`,`createdAt`) values (1,'1200px-MoEYS_(Cambodia).svg.png','សាលាវិទ្យា សខេង កន្ទឺ ២','W4GP+PP5, 154, Banan, Battambang, Cambodia','វិទ្យាល័យសខេងកន្ទឺ៧','095 505 454','','2024-12-20 10:20:14');

/*Table structure for table `category` */

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `categoryCode` varchar(225) NOT NULL,
  `categoryName` varchar(255) NOT NULL,
  `categoryShelf` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `category` */

insert  into `category`(`id`,`categoryCode`,`categoryName`,`categoryShelf`,`createdAt`) values (1,'10000001','សៀវវភៅប្រលោមលោក','F01','2024-11-15 14:59:35'),(2,'10000002','ភៅប្រវត្តិសាស្ត្រ និងជីវប្រវត្តិ','F02','2025-02-04 15:06:10'),(3,'10000003','សៀវភៅអប់រំ និងអនុស្សាវរីយ៍','F03','2025-02-27 21:55:49'),(4,'10000004','សៀវភៅវិទ្យាសាស្ត្រ និងបច្ចេកវិទ្យា','F05','2025-02-27 21:56:59'),(5,'1000006','សៀវភៅសាសនា និងទស្សនវិជ្ជា','F06','2025-02-27 21:58:16'),(6,'1000007','សៀវភៅបច្ចេកទេស និងអាជីព','F07','2025-02-27 21:59:51');

/*Table structure for table `class` */

DROP TABLE IF EXISTS `class`;

CREATE TABLE `class` (
  `id` int NOT NULL AUTO_INCREMENT,
  `className` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roomId` int NOT NULL,
  `status` tinyint DEFAULT NULL,
  `createdAt` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `className` (`className`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `class` */

insert  into `class`(`id`,`className`,`roomId`,`status`,`createdAt`) values (1,'7A',1,0,'0000-00-00 00:00:00'),(2,'8A',1,0,'0000-00-00 00:00:00'),(3,'7B',2,0,'0000-00-00 00:00:00'),(4,'8B',2,0,'0000-00-00 00:00:00'),(5,'7C',3,0,'0000-00-00 00:00:00');

/*Table structure for table `import` */

DROP TABLE IF EXISTS `import`;

CREATE TABLE `import` (
  `id` int NOT NULL AUTO_INCREMENT,
  `receivedDate` varchar(225) NOT NULL,
  `bookId` int NOT NULL,
  `categoryId` int NOT NULL,
  `createdAt` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `import` */

/*Table structure for table `library_visits` */

DROP TABLE IF EXISTS `library_visits`;

CREATE TABLE `library_visits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `studentId` int NOT NULL,
  `genderId` int NOT NULL,
  `bookId` int NOT NULL,
  `classId` int NOT NULL,
  `visitTime` datetime NOT NULL,
  `returnTime` datetime NOT NULL,
  `createdAt` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `library_visits` */

insert  into `library_visits`(`id`,`studentId`,`genderId`,`bookId`,`classId`,`visitTime`,`returnTime`,`createdAt`) values (1,1,1,1,0,'2025-02-21 03:45:30','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,1,1,4,1,'2025-02-26 00:51:27','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,2,1,4,1,'2025-02-26 01:02:59','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,1,0,1,0,'2025-02-27 22:11:22','0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,6,0,4,0,'2025-02-27 22:13:47','0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,5,0,4,0,'2025-02-27 22:15:44','0000-00-00 00:00:00','0000-00-00 00:00:00');

/*Table structure for table `print` */

DROP TABLE IF EXISTS `print`;

CREATE TABLE `print` (
  `id` int NOT NULL AUTO_INCREMENT,
  `publishingHouse` varchar(225) NOT NULL,
  `printingHouse` varchar(255) NOT NULL,
  `createdAt` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `print` */

insert  into `print`(`id`,`publishingHouse`,`printingHouse`,`createdAt`) values (1,'USA','UAS','0000-00-00 00:00:00');

/*Table structure for table `reader` */

DROP TABLE IF EXISTS `reader`;

CREATE TABLE `reader` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `studentId` int NOT NULL,
  `classId` int NOT NULL,
  `bookId` int NOT NULL,
  `createdAt` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `reader` */

/*Table structure for table `room_lists` */

DROP TABLE IF EXISTS `room_lists`;

CREATE TABLE `room_lists` (
  `id` int NOT NULL AUTO_INCREMENT,
  `roomName` varchar(50) NOT NULL,
  `status` int DEFAULT NULL,
  `createdAt` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `room_lists` */

insert  into `room_lists`(`id`,`roomName`,`status`,`createdAt`) values (1,'Room A',NULL,'0000-00-00 00:00:00'),(2,'Room B',NULL,'0000-00-00 00:00:00'),(3,'Room C',NULL,'0000-00-00 00:00:00');

/*Table structure for table `student` */

DROP TABLE IF EXISTS `student`;

CREATE TABLE `student` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(255) DEFAULT NULL,
  `startYear` varchar(255) NOT NULL,
  `endYear` varchar(255) NOT NULL,
  `idCard` varchar(100) NOT NULL,
  `studentName` varchar(255) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `birthday` varchar(225) DEFAULT NULL,
  `classId` int DEFAULT NULL,
  `studentpayId` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `deleted` int NOT NULL DEFAULT '1',
  `createdAt` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `student` */

insert  into `student`(`id`,`image`,`startYear`,`endYear`,`idCard`,`studentName`,`gender`,`birthday`,`classId`,`studentpayId`,`status`,`deleted`,`createdAt`) values (1,'ff.jpg','2024','2025','KT-10003','សុម៉ា​ វត្តី','ប្រុស','13-February-2025',1,0,0,1,'2025-02-26 15:40:08'),(2,'photo_2022-02-13_06-28-11.jpg','2024','2025','KT-10002','សុខ មិនា','ស្រី','15-February-2025',1,0,0,1,'2025-02-26 15:40:20'),(3,'ff.jpg','2024','2025','ff.jpg','KT-10002','ស្រី','ប្រុស',NULL,NULL,1,1,'2025-02-26 15:40:35'),(4,'ph.jpg','2024','2025','KT-10001','សេង ស្រីនីត','ប្រុស','26-February-2025',2,NULL,1,1,'0000-00-00 00:00:00'),(5,'ff.jpg','2024','2025','KT-10004','ឆឺត ដាំ','ប្រុស','28-February-1996',2,NULL,1,1,'0000-00-00 00:00:00'),(6,'kk.jpg','2024','2025','KT-10006','ឆឺត ឆុង','ស្រី','26-February-2025',1,NULL,1,1,'0000-00-00 00:00:00');

/*Table structure for table `student_pay` */

DROP TABLE IF EXISTS `student_pay`;

CREATE TABLE `student_pay` (
  `id` int NOT NULL AUTO_INCREMENT,
  `studentName` varchar(100) NOT NULL,
  `borrowDate` varchar(100) NOT NULL,
  `returnDate` date NOT NULL,
  `fee` decimal(10,2) NOT NULL,
  `description` text,
  `createdAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `studentId` (`studentName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `student_pay` */

/*Table structure for table `subjects` */

DROP TABLE IF EXISTS `subjects`;

CREATE TABLE `subjects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `subjects` */

/*Table structure for table `supplier` */

DROP TABLE IF EXISTS `supplier`;

CREATE TABLE `supplier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplierName` varchar(225) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `createdAt` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `supplier` */

insert  into `supplier`(`id`,`supplierName`,`phone`,`email`,`createdAt`) values (1,'ឆឺត ឆុង','060869317','chongcheut1490@gmail.com','0000-00-00 00:00:00'),(2,'ឆឺត ដាំ','090896387','dam1490@gmail.com','0000-00-00 00:00:00');

/*Table structure for table `teacher` */

DROP TABLE IF EXISTS `teacher`;

CREATE TABLE `teacher` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(225) DEFAULT NULL,
  `idCard` varchar(100) NOT NULL,
  `teacherName` varchar(255) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `deleted` int NOT NULL DEFAULT '1',
  `createdAt` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `teacher` */

insert  into `teacher`(`id`,`image`,`idCard`,`teacherName`,`gender`,`phone`,`status`,`deleted`,`createdAt`) values (1,'ff.jpg','KT-10001','ឆឹត ឆុង','ប្រុស','090869317',1,1,'2025-02-26 09:38:23'),(2,'photo_2022-02-13_06-28-11.jpg','KT-10002','ឆឹត ដាំ','ស្រី','090896369',1,1,'2025-02-26 09:38:44'),(5,'ph.jpg','KT-10005','សេង ស្រីនីត','ស្រី','015968642',1,1,'0000-00-00 00:00:00'),(3,'kk.jpg','KT-10003','ឆឺត សុខ','ប្រុស','090896531',1,1,'0000-00-00 00:00:00'),(4,'user.png','KT-10004','ឆឹត សុភ','ស្រី','096875392',1,1,'0000-00-00 00:00:00'),(7,'user.png','KT-10007','ឆឺត សុភា','ស្រី','015963487',1,1,'0000-00-00 00:00:00'),(6,'ff.jpg','KT-10006','សុខ មិនា','ស្រី','018967845',1,1,'0000-00-00 00:00:00');

/*Table structure for table `time_slots` */

DROP TABLE IF EXISTS `time_slots`;

CREATE TABLE `time_slots` (
  `id` int NOT NULL AUTO_INCREMENT,
  `startTime` time NOT NULL,
  `endTime` time NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `createdAtt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `time_slots` */

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(225) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `role` varchar(100) NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `deleted` int DEFAULT '1',
  `createdAt` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `user` */

insert  into `user`(`id`,`image`,`username`,`email`,`password`,`phone`,`role`,`status`,`deleted`,`createdAt`) values (1,'IMG_2100.JPG','admin','chhong@gmail.com','11111','090869314','admin',1,0,'2025-02-10 12:43:54'),(12,'photo_2022-02-13_06-28-11.jpg','srey nita','Oeun@gmail.com','$2y$10$mkIF8DU2gUBIqBM7AFrvteLGRDeatraprHaqsqkCvNej64BV2OZAe','00000','user',0,1,'2025-02-11 19:27:24'),(10,'ff.jpg','user','user@gmail.com','22222','00000','user',0,1,'2025-02-13 13:37:32'),(13,'photo_2022-02-13_06-28-11.jpg','srey','user@gmail.com','$2y$10$viXy5zzDRa42TNaooTYHZuJG9APRyE.2cBhER.JrEU2jg94UwSdxy','00000','user',1,0,'0000-00-00 00:00:00'),(14,'user.png','អឿន ហម','user@gmail.com','$2y$10$hMeE2axQPjnQYEZ85iVXQeN9pq.QtiZBWKai/vW7poWKQsi8zbd3y','444','user',1,0,'0000-00-00 00:00:00'),(15,'IMG_2100.JPG','nita','user@gmail.com','$2y$10$h9wi0ZCRiBYKBoUgQJOJnuGH.vq.UmtJdbqQ9pmFs618/FfAntn6C','00000','user',1,0,'0000-00-00 00:00:00');

/*Table structure for table `vbook` */

DROP TABLE IF EXISTS `vbook`;

/*!50001 DROP VIEW IF EXISTS `vbook` */;
/*!50001 DROP TABLE IF EXISTS `vbook` */;

/*!50001 CREATE TABLE  `vbook`(
 `id` int ,
 `bookCode` varchar(255) ,
 `image` varchar(255) ,
 `bookName` varchar(255) ,
 `authorName` varchar(255) ,
 `categoryName` varchar(255) ,
 `publishYear` varchar(100) ,
 `price` int ,
 `qty` int ,
 `status` varchar(100) ,
 `createdAt` timestamp 
)*/;

/*Table structure for table `vbookdetail` */

DROP TABLE IF EXISTS `vbookdetail`;

/*!50001 DROP VIEW IF EXISTS `vbookdetail` */;
/*!50001 DROP TABLE IF EXISTS `vbookdetail` */;

/*!50001 CREATE TABLE  `vbookdetail`(
 `id` int ,
 `image` varchar(255) ,
 `bookName` varchar(255) ,
 `categoryShelf` varchar(100) ,
 `authorName` varchar(255) ,
 `publishYear` varchar(100) ,
 `price` int ,
 `qty` int 
)*/;

/*Table structure for table `vborrow` */

DROP TABLE IF EXISTS `vborrow`;

/*!50001 DROP VIEW IF EXISTS `vborrow` */;
/*!50001 DROP TABLE IF EXISTS `vborrow` */;

/*!50001 CREATE TABLE  `vborrow`(
 `id` int ,
 `studentName` varchar(255) ,
 `bookName` varchar(255) ,
 `borrowDate` varchar(255) ,
 `returnDate` varchar(255) ,
 `qty` int ,
 `remark` varchar(255) ,
 `status` int ,
 `createdAt` timestamp 
)*/;

/*Table structure for table `vborrowdetail` */

DROP TABLE IF EXISTS `vborrowdetail`;

/*!50001 DROP VIEW IF EXISTS `vborrowdetail` */;
/*!50001 DROP TABLE IF EXISTS `vborrowdetail` */;

/*!50001 CREATE TABLE  `vborrowdetail`(
 `id` int ,
 `studentName` varchar(255) ,
 `className` varchar(10) ,
 `bookName` varchar(255) ,
 `qty` int ,
 `borrowDate` varchar(255) ,
 `returnDate` varchar(255) ,
 `remark` varchar(255) ,
 `createdAt` timestamp 
)*/;

/*Table structure for table `vimport` */

DROP TABLE IF EXISTS `vimport`;

/*!50001 DROP VIEW IF EXISTS `vimport` */;
/*!50001 DROP TABLE IF EXISTS `vimport` */;

/*!50001 CREATE TABLE  `vimport`(
 `id` int ,
 `receivedDate` varchar(225) ,
 `bookName` varchar(255) ,
 `createdAt` timestamp 
)*/;

/*Table structure for table `vreader` */

DROP TABLE IF EXISTS `vreader`;

/*!50001 DROP VIEW IF EXISTS `vreader` */;
/*!50001 DROP TABLE IF EXISTS `vreader` */;

/*!50001 CREATE TABLE  `vreader`(
 `id` int ,
 `date` datetime ,
 `studentName` varchar(255) ,
 `className` varchar(10) ,
 `bookName` varchar(255) ,
 `createdAt` timestamp 
)*/;

/*Table structure for table `vreaderdetail` */

DROP TABLE IF EXISTS `vreaderdetail`;

/*!50001 DROP VIEW IF EXISTS `vreaderdetail` */;
/*!50001 DROP TABLE IF EXISTS `vreaderdetail` */;

/*!50001 CREATE TABLE  `vreaderdetail`(
 `id` int ,
 `date` datetime ,
 `studentName` varchar(255) ,
 `gender` varchar(10) ,
 `className` varchar(10) ,
 `bookName` varchar(255) ,
 `createdAt` timestamp 
)*/;

/*Table structure for table `vstudent` */

DROP TABLE IF EXISTS `vstudent`;

/*!50001 DROP VIEW IF EXISTS `vstudent` */;
/*!50001 DROP TABLE IF EXISTS `vstudent` */;

/*!50001 CREATE TABLE  `vstudent`(
 `id` int ,
 `startYear` varchar(255) ,
 `endYear` varchar(255) ,
 `image` varchar(255) ,
 `idCard` varchar(100) ,
 `studentName` varchar(255) ,
 `gender` varchar(10) ,
 `birthday` varchar(225) ,
 `className` varchar(10) ,
 `status` int ,
 `createdAt` timestamp 
)*/;

/*Table structure for table `vstudentborrow` */

DROP TABLE IF EXISTS `vstudentborrow`;

/*!50001 DROP VIEW IF EXISTS `vstudentborrow` */;
/*!50001 DROP TABLE IF EXISTS `vstudentborrow` */;

/*!50001 CREATE TABLE  `vstudentborrow`(
 `id` int ,
 `studentName` varchar(255) ,
 `className` varchar(10) ,
 `bookName` varchar(255) ,
 `borrowDate` varchar(255) ,
 `returnDate` varchar(255) ,
 `status` int ,
 `createdAt` timestamp 
)*/;

/*Table structure for table `vstudentpay` */

DROP TABLE IF EXISTS `vstudentpay`;

/*!50001 DROP VIEW IF EXISTS `vstudentpay` */;
/*!50001 DROP TABLE IF EXISTS `vstudentpay` */;

/*!50001 CREATE TABLE  `vstudentpay`(
 `id` int ,
 `studentName` varchar(255) ,
 `borrowDate` varchar(255) ,
 `returnDate` date ,
 `fee` decimal(10,2) ,
 `description` text ,
 `createdAt` datetime 
)*/;

/*View structure for view vbook */

/*!50001 DROP TABLE IF EXISTS `vbook` */;
/*!50001 DROP VIEW IF EXISTS `vbook` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vbook` AS select `book`.`id` AS `id`,`book`.`bookCode` AS `bookCode`,`book`.`image` AS `image`,`book`.`bookName` AS `bookName`,`book`.`authorName` AS `authorName`,`category`.`categoryName` AS `categoryName`,`book`.`publishYear` AS `publishYear`,`book`.`price` AS `price`,`book`.`qty` AS `qty`,`book`.`status` AS `status`,`book`.`createdAt` AS `createdAt` from (`book` join `category` on((`book`.`categoryId` = `category`.`id`))) */;

/*View structure for view vbookdetail */

/*!50001 DROP TABLE IF EXISTS `vbookdetail` */;
/*!50001 DROP VIEW IF EXISTS `vbookdetail` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vbookdetail` AS select `book`.`id` AS `id`,`book`.`image` AS `image`,`book`.`bookName` AS `bookName`,`category`.`categoryShelf` AS `categoryShelf`,`book`.`authorName` AS `authorName`,`book`.`publishYear` AS `publishYear`,`book`.`price` AS `price`,`book`.`qty` AS `qty` from (`book` join `category` on((`book`.`categoryId` = `category`.`id`))) */;

/*View structure for view vborrow */

/*!50001 DROP TABLE IF EXISTS `vborrow` */;
/*!50001 DROP VIEW IF EXISTS `vborrow` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vborrow` AS select `borrow`.`id` AS `id`,`student`.`studentName` AS `studentName`,`book`.`bookName` AS `bookName`,`borrow`.`borrowDate` AS `borrowDate`,`borrow`.`returnDate` AS `returnDate`,`borrow`.`qty` AS `qty`,`borrow`.`remark` AS `remark`,`borrow`.`status` AS `status`,`borrow`.`createdAt` AS `createdAt` from ((`borrow` join `student` on((`borrow`.`studentId` = `student`.`id`))) join `book` on((`borrow`.`bookId` = `book`.`id`))) */;

/*View structure for view vborrowdetail */

/*!50001 DROP TABLE IF EXISTS `vborrowdetail` */;
/*!50001 DROP VIEW IF EXISTS `vborrowdetail` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vborrowdetail` AS select `borrow`.`id` AS `id`,`student`.`studentName` AS `studentName`,`class`.`className` AS `className`,`book`.`bookName` AS `bookName`,`borrow`.`qty` AS `qty`,`borrow`.`borrowDate` AS `borrowDate`,`borrow`.`returnDate` AS `returnDate`,`borrow`.`remark` AS `remark`,`borrow`.`createdAt` AS `createdAt` from (((`borrow` join `student` on((`borrow`.`studentId` = `student`.`id`))) join `book` on((`borrow`.`bookId` = `book`.`id`))) join `class` on((`student`.`classId` = `class`.`id`))) */;

/*View structure for view vimport */

/*!50001 DROP TABLE IF EXISTS `vimport` */;
/*!50001 DROP VIEW IF EXISTS `vimport` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vimport` AS select `import`.`id` AS `id`,`import`.`receivedDate` AS `receivedDate`,`book`.`bookName` AS `bookName`,`import`.`createdAt` AS `createdAt` from (`import` join `book` on((`import`.`bookId` = `book`.`id`))) */;

/*View structure for view vreader */

/*!50001 DROP TABLE IF EXISTS `vreader` */;
/*!50001 DROP VIEW IF EXISTS `vreader` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vreader` AS select `reader`.`id` AS `id`,`reader`.`date` AS `date`,`student`.`studentName` AS `studentName`,`class`.`className` AS `className`,`book`.`bookName` AS `bookName`,`reader`.`createdAt` AS `createdAt` from (((`reader` join `student` on((`reader`.`studentId` = `student`.`id`))) join `class` on((`student`.`classId` = `class`.`id`))) join `book` on((`reader`.`bookId` = `book`.`id`))) */;

/*View structure for view vreaderdetail */

/*!50001 DROP TABLE IF EXISTS `vreaderdetail` */;
/*!50001 DROP VIEW IF EXISTS `vreaderdetail` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vreaderdetail` AS select `reader`.`id` AS `id`,`reader`.`date` AS `date`,`student`.`studentName` AS `studentName`,`student`.`gender` AS `gender`,`class`.`className` AS `className`,`book`.`bookName` AS `bookName`,`reader`.`createdAt` AS `createdAt` from (((`reader` join `student` on((`reader`.`studentId` = `student`.`id`))) join `class` on((`student`.`classId` = `class`.`id`))) join `book` on((`reader`.`bookId` = `book`.`id`))) */;

/*View structure for view vstudent */

/*!50001 DROP TABLE IF EXISTS `vstudent` */;
/*!50001 DROP VIEW IF EXISTS `vstudent` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vstudent` AS select `student`.`id` AS `id`,`student`.`startYear` AS `startYear`,`student`.`endYear` AS `endYear`,`student`.`image` AS `image`,`student`.`idCard` AS `idCard`,`student`.`studentName` AS `studentName`,`student`.`gender` AS `gender`,`student`.`birthday` AS `birthday`,`class`.`className` AS `className`,`student`.`status` AS `status`,`student`.`createdAt` AS `createdAt` from (`student` join `class` on((`student`.`classId` = `class`.`id`))) */;

/*View structure for view vstudentborrow */

/*!50001 DROP TABLE IF EXISTS `vstudentborrow` */;
/*!50001 DROP VIEW IF EXISTS `vstudentborrow` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vstudentborrow` AS select `borrow`.`id` AS `id`,`student`.`studentName` AS `studentName`,`class`.`className` AS `className`,`book`.`bookName` AS `bookName`,`borrow`.`borrowDate` AS `borrowDate`,`borrow`.`returnDate` AS `returnDate`,`borrow`.`status` AS `status`,`borrow`.`createdAt` AS `createdAt` from (((`borrow` join `student` on((`borrow`.`studentId` = `student`.`id`))) join `class` on((`student`.`classId` = `class`.`id`))) join `book` on((`borrow`.`bookId` = `book`.`id`))) */;

/*View structure for view vstudentpay */

/*!50001 DROP TABLE IF EXISTS `vstudentpay` */;
/*!50001 DROP VIEW IF EXISTS `vstudentpay` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vstudentpay` AS select `student_pay`.`id` AS `id`,coalesce(`student`.`studentName`,'Unknown') AS `studentName`,coalesce(`borrow`.`borrowDate`,'1970-01-01') AS `borrowDate`,`student_pay`.`returnDate` AS `returnDate`,`student_pay`.`fee` AS `fee`,`student_pay`.`description` AS `description`,`student_pay`.`createdAt` AS `createdAt` from ((`student_pay` left join `borrow` on((`student_pay`.`id` = `borrow`.`id`))) left join `student` on((`borrow`.`studentId` = `student`.`id`))) */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
