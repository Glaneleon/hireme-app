-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 22, 2024 at 07:22 AM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hireme`
--
CREATE DATABASE IF NOT EXISTS `hireme` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `hireme`;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--
-- Creation: May 12, 2024 at 11:33 AM
--

DROP TABLE IF EXISTS `companies`;
CREATE TABLE IF NOT EXISTS `companies` (
  `CompanyID` int NOT NULL AUTO_INCREMENT,
  `UserID` int NOT NULL,
  `CompanyName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `CompanyDescription` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `LegalDocuments` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'redundant, will remove later',
  `CompanyAddress` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `VerificationStatus` enum('Verified','Pending','Rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`CompanyID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companyapplication`
--
-- Creation: May 15, 2024 at 04:41 PM
--

DROP TABLE IF EXISTS `companyapplication`;
CREATE TABLE IF NOT EXISTS `companyapplication` (
  `CompanyApplicationID` int NOT NULL AUTO_INCREMENT,
  `CompanyID` int NOT NULL,
  `DocumentName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `DocumentFilePath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `DocumentType` enum('BIR Registration','SEC Registration','Business Permit','Mayor''s Permit','Certificate') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `VerificationStatus` enum('Pending','Verified','Rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `ReasonForRejection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `Date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CompanyApplicationID`),
  KEY `CompanyID` (`CompanyID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companydocuments`
--
-- Creation: May 13, 2024 at 10:36 AM
-- Last update: May 17, 2024 at 03:00 PM
--

DROP TABLE IF EXISTS `companydocuments`;
CREATE TABLE IF NOT EXISTS `companydocuments` (
  `listID` int NOT NULL AUTO_INCREMENT,
  `CompanyID` int NOT NULL,
  `sec` tinyint(1) NOT NULL,
  `businesspermit` tinyint(1) NOT NULL,
  `bir` tinyint(1) NOT NULL,
  `mayorpermit` tinyint(1) NOT NULL,
  `certificate` tinyint(1) NOT NULL,
  PRIMARY KEY (`listID`),
  UNIQUE KEY `CompanyID` (`CompanyID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companyprofile`
--
-- Creation: May 14, 2024 at 09:19 AM
-- Last update: May 16, 2024 at 02:34 PM
--

DROP TABLE IF EXISTS `companyprofile`;
CREATE TABLE IF NOT EXISTS `companyprofile` (
  `profileid` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `contact_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `rep_position` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `rep_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `rep_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `CompanyID` int NOT NULL,
  PRIMARY KEY (`profileid`),
  KEY `CompanyID` (`CompanyID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favoritejobs`
--
-- Creation: Apr 22, 2024 at 10:53 AM
-- Last update: Apr 22, 2024 at 10:53 AM
--

DROP TABLE IF EXISTS `favoritejobs`;
CREATE TABLE IF NOT EXISTS `favoritejobs` (
  `FavoriteID` int NOT NULL AUTO_INCREMENT,
  `JobSeekerID` int NOT NULL,
  `JobID` int NOT NULL,
  PRIMARY KEY (`FavoriteID`),
  KEY `JobSeekerID` (`JobSeekerID`),
  KEY `JobID` (`JobID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interviews`
--
-- Creation: Apr 22, 2024 at 10:53 AM
-- Last update: Apr 30, 2024 at 11:50 AM
--

DROP TABLE IF EXISTS `interviews`;
CREATE TABLE IF NOT EXISTS `interviews` (
  `InterviewID` int NOT NULL AUTO_INCREMENT,
  `JobID` int NOT NULL,
  `JobSeekerApplicationID` int NOT NULL,
  `InterviewDate` datetime NOT NULL,
  `DateMade` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Status` enum('Pending','No show','Done','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`InterviewID`),
  UNIQUE KEY `JobSeekerApplicationID` (`JobSeekerApplicationID`) USING BTREE,
  KEY `JobID` (`JobID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--
-- Creation: May 12, 2024 at 11:34 AM
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `JobID` int NOT NULL AUTO_INCREMENT,
  `CompanyID` int NOT NULL,
  `JobTitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `JobDescription` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `JobType` enum('Full-Time','Part-Time','Contract','Intern') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `SalaryMin` decimal(10,2) NOT NULL,
  `SalaryMax` decimal(10,2) NOT NULL,
  `WorkHours` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `JobLocation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `JobLocationType` enum('WFH','On Site') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `VerificationStatus` enum('Verified','Pending','Rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `JobIndustry` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `OtherIndustry` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`JobID`),
  KEY `CompanyID` (`CompanyID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobseekerapplication`
--
-- Creation: Apr 22, 2024 at 10:53 AM
-- Last update: Apr 30, 2024 at 11:36 AM
--

DROP TABLE IF EXISTS `jobseekerapplication`;
CREATE TABLE IF NOT EXISTS `jobseekerapplication` (
  `JobSeekerApplicationID` int NOT NULL AUTO_INCREMENT,
  `JobID` int DEFAULT NULL,
  `UserID` int DEFAULT NULL,
  `ResumeFilePath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ApplicationDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Status` enum('Pending','Rejected','Verified') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`JobSeekerApplicationID`),
  KEY `JobID` (`JobID`),
  KEY `UserID` (`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobseekers`
--
-- Creation: Apr 22, 2024 at 10:53 AM
-- Last update: Apr 22, 2024 at 12:52 PM
--

DROP TABLE IF EXISTS `jobseekers`;
CREATE TABLE IF NOT EXISTS `jobseekers` (
  `JobSeekerID` int NOT NULL AUTO_INCREMENT,
  `UserID` int NOT NULL,
  `FirstName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `LastName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `BirthDate` date NOT NULL,
  `Address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ContactNumber` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`JobSeekerID`),
  KEY `UserID` (`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--
-- Creation: Jun 21, 2024 at 06:10 PM
-- Last update: Jun 21, 2024 at 06:09 PM
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT 'zero means that all managers can see the notif',
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `type` enum('job_posted','job_verified') COLLATE utf8mb4_general_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
-- Creation: May 15, 2024 at 04:17 PM
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Role` enum('Admin','Company','Job Seeker','User','Manager') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'User',
  `Token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `unique_email` (`Email`),
  UNIQUE KEY `unique_username` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `companyapplication`
--
ALTER TABLE `companyapplication`
  ADD CONSTRAINT `companyapplication_ibfk_1` FOREIGN KEY (`CompanyID`) REFERENCES `companies` (`CompanyID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`CompanyID`) REFERENCES `companies` (`CompanyID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
