-- TYPOlight Avatars :: Database setup file
--
-- Copyright (C) 2008 by Peter Koch, IBK Software AG.
-- For license see accompaning file LICENSE.txt
--
-- NOTE: this file was edited with tabs set to 4.
-- 
-- **********************************************************
-- *      ! ! !   I M P O R T A N T  N O T E   ! ! !        *
-- *                                                        *
-- * Do not import this file manually! Use the TYPOlight    *
-- * install tool to create and maintain database tables:   *
-- * - Point your browser to                                *
-- *   http://www.yourdomain.com/typolight/install.php      *
-- * - Enter the installation password and click "Login"    *
-- * - Scroll down and click button "Update Database"       *
-- **********************************************************

-- --------------------------------------------------------

-- 
-- Table `tl_member`
-- 

CREATE TABLE `tl_member` (
  `avatar` varchar(255) NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table `tl_user`
-- 

CREATE TABLE `tl_user` (
  `avatar` varchar(255) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
