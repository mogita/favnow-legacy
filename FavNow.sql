-- phpMyAdmin SQL Dump
-- version 4.1.9
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: 2015-01-19 04:17:02
-- 服务器版本： 5.5.34
-- PHP Version: 5.5.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `favnow`
--
CREATE DATABASE IF NOT EXISTS `favnow` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `favnow`;

-- --------------------------------------------------------

--
-- 表的结构 `Favs`
--

CREATE TABLE IF NOT EXISTS `Favs` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`userid` int(11) NOT NULL,
	`url` varchar(2083) NOT NULL,
	`title` varchar(200) NOT NULL,
	`timepoint` int(11) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- 表的结构 `PreRegister`
--

DROP TABLE IF EXISTS `PreRegister`;
CREATE TABLE IF NOT EXISTS `PreRegister` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`email` varchar(254) NOT NULL,
	`preregtime` int(11) NOT NULL,
	`notified` int(11) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- 表的结构 `Users`
--

DROP TABLE IF EXISTS `Users`;
CREATE TABLE IF NOT EXISTS `Users` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user` varchar(32) NOT NULL,
	`password` varchar(100) NOT NULL,
	`email` varchar(254) NOT NULL,
	`jointime` int(11) NOT NULL,
	`resetcode` varchar(100) NOT NULL,
	`resetcodetime` int(11) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
