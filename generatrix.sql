-- phpMyAdmin SQL Dump
-- version 3.1.2deb1ubuntu0.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 07, 2010 at 03:05 PM
-- Server version: 5.0.75
-- PHP Version: 5.2.6-3ubuntu4.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `generatrix`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE IF NOT EXISTS `branches` (
  `id` int(11) NOT NULL auto_increment,
  `repo` varchar(128) NOT NULL,
  `branch` varchar(64) NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `branches`
--


-- --------------------------------------------------------

--
-- Table structure for table `commits`
--

CREATE TABLE IF NOT EXISTS `commits` (
  `id` int(11) NOT NULL auto_increment,
  `repo` varchar(128) NOT NULL,
  `commit` varchar(64) NOT NULL,
  `tree` varchar(64) NOT NULL,
  `parent` varchar(64) NOT NULL,
  `author_name` varchar(128) NOT NULL,
  `author_email` varchar(128) NOT NULL,
  `author_time` int(11) NOT NULL,
  `committer_name` varchar(128) NOT NULL,
  `committer_email` varchar(128) NOT NULL,
  `committer_time` int(11) NOT NULL,
  `message` varchar(1024) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=954 ;

--
-- Dumping data for table `commits`
--

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE IF NOT EXISTS `emails` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` int(11) NOT NULL,
  `from` varchar(128) NOT NULL,
  `subject` varchar(512) NOT NULL,
  `body` text NOT NULL,
  `budget` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `emails`
--


-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL auto_increment,
  `category` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `slug` varchar(128) NOT NULL,
  `details` varchar(1024) NOT NULL,
  `image1` varchar(256) NOT NULL,
  `image2` varchar(256) NOT NULL,
  `image3` varchar(256) NOT NULL,
  `image4` varchar(256) NOT NULL,
  `image5` varchar(256) NOT NULL,
  `image6` varchar(256) NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `projects`
--


-- --------------------------------------------------------

--
-- Table structure for table `repoaccess`
--

CREATE TABLE IF NOT EXISTS `repoaccess` (
  `id` int(11) NOT NULL auto_increment,
  `repo` varchar(128) NOT NULL,
  `user` varchar(128) NOT NULL,
  `active` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=376 ;

--
-- Dumping data for table `repoaccess`
--


-- --------------------------------------------------------

--
-- Table structure for table `repositories`
--

CREATE TABLE IF NOT EXISTS `repositories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(512) NOT NULL,
  `description` varchar(512) NOT NULL,
  `group` varchar(128) NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=79 ;

--
-- Dumping data for table `repositories`
--

--
-- Table structure for table `tree`
--

CREATE TABLE IF NOT EXISTS `tree` (
  `id` int(11) NOT NULL auto_increment,
  `repo` varchar(128) NOT NULL,
  `mode` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `commit` varchar(64) NOT NULL,
  `object_size` int(11) NOT NULL,
  `file_name` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `tree`
--

-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `email` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `permissions` int(11) NOT NULL,
  `email1` varchar(256) NOT NULL,
  `email2` varchar(256) NOT NULL,
  `email3` varchar(256) NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `users`
--


