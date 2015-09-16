-- phpMyAdmin SQL Dump
-- version 4.2.12deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2015-09-16 14:31:15
-- 服务器版本： 5.5.43-0+deb8u1
-- PHP Version: 5.6.9-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `favnow`
--
CREATE DATABASE IF NOT EXISTS `favnow` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `favnow`;

-- --------------------------------------------------------

--
-- 表的结构 `cat_relation`
--

DROP TABLE IF EXISTS `cat_relation`;
CREATE TABLE IF NOT EXISTS `cat_relation` (
`id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `obj_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `cat_terms`
--

DROP TABLE IF EXISTS `cat_terms`;
CREATE TABLE IF NOT EXISTS `cat_terms` (
`id` int(11) NOT NULL,
  `catname` tinytext NOT NULL,
  `userid` int(11) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `favs`
--

DROP TABLE IF EXISTS `favs`;
CREATE TABLE IF NOT EXISTS `favs` (
`id` int(11) NOT NULL,
  `hash` varchar(32) CHARACTER SET utf8 NOT NULL,
  `userid` int(11) NOT NULL,
  `url` varchar(2083) NOT NULL,
  `title` varchar(200) NOT NULL,
  `timepoint` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `preregister`
--

DROP TABLE IF EXISTS `preregister`;
CREATE TABLE IF NOT EXISTS `preregister` (
`id` int(11) NOT NULL,
  `email` varchar(254) NOT NULL,
  `preregtime` int(11) NOT NULL,
  `notified` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
`id` int(11) NOT NULL,
  `user` varchar(32) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(254) NOT NULL,
  `jointime` int(11) NOT NULL,
  `resetcode` varchar(100) CHARACTER SET utf8 NOT NULL,
  `resetcodetime` int(11) NOT NULL,
  `authcode` varchar(100) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cat_relation`
--
ALTER TABLE `cat_relation`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cat_terms`
--
ALTER TABLE `cat_terms`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favs`
--
ALTER TABLE `favs`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `hash` (`hash`);

--
-- Indexes for table `preregister`
--
ALTER TABLE `preregister`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cat_relation`
--
ALTER TABLE `cat_relation`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cat_terms`
--
ALTER TABLE `cat_terms`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `favs`
--
ALTER TABLE `favs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `preregister`
--
ALTER TABLE `preregister`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
