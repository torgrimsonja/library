-- phpMyAdmin SQL Dump
-- version 3.5.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 22, 2013 at 10:58 AM
-- Server version: 5.1.66-community
-- PHP Version: 5.4.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `local_library`
--

-- --------------------------------------------------------

--
-- Table structure for table `alternate_email_address`
--

CREATE TABLE IF NOT EXISTS `alternate_email_address` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `intersect_schedule_time_block`
--

CREATE TABLE IF NOT EXISTS `intersect_schedule_time_block` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) unsigned NOT NULL,
  `time_block_id` int(11) unsigned NOT NULL,
  `time_end` time NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `schedule_id` (`schedule_id`),
  KEY `time_block_id` (`time_block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `studentId` int(11) unsigned NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `teacherName` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `timeIn` time NOT NULL,
  `timeOut` time DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`studentId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=126 ;

--
-- Dumping data for table `log`
--

INSERT INTO `log` (`id`, `studentId`, `firstName`, `lastName`, `teacherName`, `date`, `timeIn`, `timeOut`, `timestamp`) VALUES
(15, 123456, 'John', 'Doe', NULL, '2013-03-20', '11:11:29', '11:12:35', '2013-03-20 17:12:35');

-- --------------------------------------------------------

--
-- Table structure for table `log_option`
--

CREATE TABLE IF NOT EXISTS `log_option` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `log_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `log_id` (`log_id`),
  KEY `option_id` (`option_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=157 ;

--
-- Dumping data for table `log_option`
--

INSERT INTO `log_option` (`id`, `log_id`, `option_id`, `timestamp`) VALUES
(15, 15, 26, '2013-03-20 17:11:29');

-- --------------------------------------------------------

--
-- Table structure for table `option`
--

CREATE TABLE IF NOT EXISTS `option` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `option`
--

INSERT INTO `option` (`id`, `name`, `timestamp`) VALUES
(4, 'Reading', '2013-03-15 21:12:26'),
(25, 'Books', '2013-03-18 18:37:04'),
(26, 'Computer', '2013-03-18 18:37:12'),
(29, 'Tutoring', '2013-03-18 18:38:37'),
(31, 'Homework', '2013-03-18 20:54:25');

-- --------------------------------------------------------

--
-- Table structure for table `organization`
--

CREATE TABLE IF NOT EXISTS `organization` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `startTime` time DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Organization Settings Table' AUTO_INCREMENT=29 ;

--
-- Dumping data for table `organization`
--

INSERT INTO `organization` (`id`, `name`, `startTime`, `timestamp`) VALUES
(28, 'Twin Falls High School', NULL, '2013-03-13 01:53:30');

-- --------------------------------------------------------

--
-- Table structure for table `organization_timeblock`
--

CREATE TABLE IF NOT EXISTS `organization_timeblock` (
  `id` int(11) unsigned NOT NULL,
  `organization_id` int(11) unsigned NOT NULL,
  `order` tinyint(4) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `schedule_id` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `organization_timeblock`
--

INSERT INTO `organization_timeblock` (`id`, `organization_id`, `order`, `name`, `timestamp`) VALUES
(1, 28, 1, 'Period 1', '2013-03-13 01:53:30'),
(2, 28, 2, 'Period 2', '2013-03-13 01:53:30'),
(3, 28, 3, 'Period 3', '2013-03-13 01:53:30'),
(4, 28, 4, 'Period 4', '2013-03-13 01:53:30'),
(5, 28, 5, 'Advisory', '2013-03-13 01:53:30'),
(6, 28, 6, 'Period 6', '2013-03-13 01:53:30'),
(7, 28, 7, 'Period 7', '2013-03-13 01:53:30'),
(8, 28, 8, 'Period 8', '2013-03-13 01:53:30');

-- --------------------------------------------------------

--
-- Table structure for table `privilege`
--

CREATE TABLE IF NOT EXISTS `privilege` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `privilege`
--

INSERT INTO `privilege` (`id`, `name`, `timestamp`) VALUES
(1, 'admin', '2013-03-11 20:41:37'),
(2, 'reports', '2013-03-11 20:41:37'),
(3, 'manager', '2013-03-11 20:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE IF NOT EXISTS `schedule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `endTime` time NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=87 ;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `name`, `endTime`, `timestamp`) VALUES
(86, 'Normal Schedule', '15:15:00', '2013-03-21 20:58:33');

-- --------------------------------------------------------

--
-- Table structure for table `schedule_block`
--

CREATE TABLE IF NOT EXISTS `schedule_block` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) unsigned NOT NULL,
  `organization_timeBlock_id` int(11) unsigned NOT NULL,
  `timeStart` time NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `schedule_id` (`schedule_id`),
  KEY `organization_timeBlock_id` (`organization_timeBlock_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=136 ;

--
-- Dumping data for table `schedule_block`
--

INSERT INTO `schedule_block` (`id`, `schedule_id`, `organization_timeBlock_id`, `timeStart`, `timestamp`) VALUES
(128, 86, 1, '08:00:00', '2013-03-21 20:58:33'),
(129, 86, 2, '08:55:00', '2013-03-21 20:58:33'),
(130, 86, 3, '09:50:00', '2013-03-21 20:58:33'),
(131, 86, 4, '10:45:00', '2013-03-21 20:58:33'),
(132, 86, 5, '11:35:00', '2013-03-21 20:58:33'),
(133, 86, 6, '12:35:00', '2013-03-21 20:58:33'),
(134, 86, 7, '13:30:00', '2013-03-21 20:58:33'),
(135, 86, 8, '14:25:00', '2013-03-21 20:58:33');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`, `timestamp`) VALUES
(1, 'sendEmail', '1', '2013-03-20 15:07:11'),
(2, 'currentSchedule', '86', '2013-03-21 20:58:42'),
(3, 'systemStatus', '1', '2013-03-14 21:11:42');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE IF NOT EXISTS `student` (
  `id` int(11) unsigned NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `gender` enum('M','F') NOT NULL,
  `gradeLevel` int(2) NOT NULL,
  `p1` varchar(255) NOT NULL,
  `p2` varchar(255) NOT NULL,
  `p3` varchar(255) NOT NULL,
  `p4` varchar(255) NOT NULL,
  `p5` varchar(255) NOT NULL,
  `p6` varchar(255) NOT NULL,
  `p7` varchar(255) NOT NULL,
  `p8` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `firstName`, `lastName`, `gender`, `gradeLevel`, `p1`, `p2`, `p3`, `p4`, `p5`, `p6`, `p7`, `p8`) VALUES
(123456, 'Joe', 'Schmoe', 'M', 11, 'Connor, Jo Marie', 'Harr, Matthew L.', 'Showers, Gary', 'Fonnesbeck, Brett F', 'Torgrimson, Jason', 'Mathes, Jessica', 'Hartley, William M', 'McCullough, Royce')
);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(40) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=89 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `firstName`, `lastName`, `username`, `password`, `timestamp`) VALUES
(63, 'Jason', 'Torgrimson', 't', '83e4a96aed96436c621b9809e258b309', '2013-04-16 18:43:05'),
(87, 'Thor', 'Lund', 'thorlund', 'e10adc3949ba59abbe56e057f20f883e', '2013-04-25 18:18:36'),
(88, 'Swade', 'Vance', 'swade', 'f0e3c272c78d2e112c1973eed1af0354', '2013-04-25 18:22:34');

-- --------------------------------------------------------

--
-- Table structure for table `user_privilege`
--

CREATE TABLE IF NOT EXISTS `user_privilege` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `privilege_id` int(11) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `privilege_id` (`privilege_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `user_privilege`
--

INSERT INTO `user_privilege` (`id`, `user_id`, `privilege_id`, `timestamp`) VALUES
(3, 63, 1, '2013-04-16 18:47:35'),
(12, 87, 3, '2013-04-25 18:18:47'),
(13, 88, 1, '2013-04-25 18:22:34');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `intersect_schedule_time_block`
--
ALTER TABLE `intersect_schedule_time_block`
  ADD CONSTRAINT `intersect_schedule_time_block_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `intersect_schedule_time_block_ibfk_2` FOREIGN KEY (`time_block_id`) REFERENCES `organization_timeblock` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `log_option`
--
ALTER TABLE `log_option`
  ADD CONSTRAINT `log_option_ibfk_3` FOREIGN KEY (`log_id`) REFERENCES `log` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `log_option_ibfk_6` FOREIGN KEY (`option_id`) REFERENCES `option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `organization_timeblock`
--
ALTER TABLE `organization_timeblock`
  ADD CONSTRAINT `organization_timeblock_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `schedule_block`
--
ALTER TABLE `schedule_block`
  ADD CONSTRAINT `schedule_block_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schedule_block_ibfk_2` FOREIGN KEY (`organization_timeBlock_id`) REFERENCES `organization_timeblock` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_privilege`
--
ALTER TABLE `user_privilege`
  ADD CONSTRAINT `user_privilege_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_privilege_ibfk_2` FOREIGN KEY (`privilege_id`) REFERENCES `privilege` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
