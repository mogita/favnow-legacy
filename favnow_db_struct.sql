-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- 主机: localhost:3306
-- 生成日期: 2015-10-21 12:52:55
-- 服务器版本: 5.5.45-cll
-- PHP 版本: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+08:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `favnow`
--
CREATE DATABASE IF NOT EXISTS `favnow` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `favnow`;

-- --------------------------------------------------------

--
-- 表的结构 `cat_relation`
--

DROP TABLE IF EXISTS `cat_relation`;
CREATE TABLE IF NOT EXISTS `cat_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `obj_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=50 ;

-- --------------------------------------------------------

--
-- 表的结构 `cat_terms`
--

DROP TABLE IF EXISTS `cat_terms`;
CREATE TABLE IF NOT EXISTS `cat_terms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catname` tinytext NOT NULL,
  `userid` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- 表的结构 `favs`
--

DROP TABLE IF EXISTS `favs`;
CREATE TABLE IF NOT EXISTS `favs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(32) CHARACTER SET utf8 NOT NULL,
  `userid` int(11) NOT NULL,
  `url` varchar(2083) NOT NULL,
  `title` varchar(200) NOT NULL,
  `timepoint` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=59 ;

-- --------------------------------------------------------

--
-- 表的结构 `preregister`
--

DROP TABLE IF EXISTS `preregister`;
CREATE TABLE IF NOT EXISTS `preregister` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(254) NOT NULL,
  `preregtime` int(11) NOT NULL,
  `notified` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(32) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(254) NOT NULL,
  `jointime` int(11) NOT NULL,
  `resetcode` varchar(100) CHARACTER SET utf8 NOT NULL,
  `resetcodetime` int(11) NOT NULL,
  `pubcode` varchar(100) DEFAULT NULL,
  `authcode` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=5 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
