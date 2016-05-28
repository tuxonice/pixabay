-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 28, 2016 at 11:48 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pixabay`
--

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pixabay_id` int(11) DEFAULT NULL,
  `page_url` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `type` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `tags` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `category` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `preview_url` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `preview_width` int(11) DEFAULT NULL,
  `preview_height` int(11) DEFAULT NULL,
  `normal_url` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `normal_width` int(11) DEFAULT NULL,
  `normal_height` int(11) DEFAULT NULL,
  `ratio` float DEFAULT NULL,
  `original_image_width` int(11) DEFAULT NULL,
  `original_image_height` int(11) DEFAULT NULL,
  `csv_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pixabay_id` (`pixabay_id`),
  KEY `csv_id` (`csv_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12049 ;

-- --------------------------------------------------------

--
-- Table structure for table `imgtags`
--

CREATE TABLE IF NOT EXISTS `imgtags` (
  `image_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`image_id`,`tag_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `imgtags`
--
ALTER TABLE `imgtags`
  ADD CONSTRAINT `imgtags_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`),
  ADD CONSTRAINT `imgtags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
