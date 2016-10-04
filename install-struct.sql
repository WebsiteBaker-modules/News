-- phpMyAdmin SQL Dump
-- Erstellungszeit: 20. Januar 2012 um 12:37
-- Server Version: 5.1.41
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
-- --------------------------------------------------------
-- Database structure for module 'news'
--
-- Replacements: {TABLE_PREFIX}, {TABLE_ENGINE}, {FIELD_COLLATION}
--
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `mod_news_comments`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_news_comments`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}mod_news_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL DEFAULT '0',
  `post_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `comment` text{FIELD_COLLATION} NOT NULL,
  `commented_when` int(11) NOT NULL DEFAULT '0',
  `commented_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`)
){TABLE_ENGINE};
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `mod_news_groups`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_news_groups`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}mod_news_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  PRIMARY KEY (`group_id`)
){TABLE_ENGINE};
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `mod_news_posts`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_news_posts`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}mod_news_posts` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `link` text{FIELD_COLLATION} NOT NULL,
  `content_short` text{FIELD_COLLATION} NOT NULL,
  `content_long` text{FIELD_COLLATION} NOT NULL,
  `commenting` varchar(7){FIELD_COLLATION} NOT NULL DEFAULT '',
  `created_when` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `published_when` int(11) NOT NULL DEFAULT '0',
  `published_until` int(11) NOT NULL DEFAULT '0',
  `posted_when` int(11) NOT NULL DEFAULT '0',
  `posted_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`post_id`)
){TABLE_ENGINE};
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `mod_news_settings`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_news_settings`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}mod_news_settings` (
  `section_id` int(11) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL DEFAULT '0',
  `header` text{FIELD_COLLATION} NOT NULL,
  `post_loop` text{FIELD_COLLATION} NOT NULL,
  `footer` text{FIELD_COLLATION} NOT NULL,
  `posts_per_page` int(11) NOT NULL DEFAULT '5',
  `post_header` text{FIELD_COLLATION} NOT NULL,
  `post_footer` text{FIELD_COLLATION} NOT NULL,
  `comments_header` text{FIELD_COLLATION} NOT NULL,
  `comments_loop` text{FIELD_COLLATION} NOT NULL,
  `comments_footer` text{FIELD_COLLATION} NOT NULL,
  `comments_page` text{FIELD_COLLATION} NOT NULL,
  `commenting` varchar(7){FIELD_COLLATION} NOT NULL DEFAULT '',
  `resize` int(11) NOT NULL DEFAULT '0',
  `use_captcha` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`section_id`)
){TABLE_ENGINE};
-- EndOfFile
