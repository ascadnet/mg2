-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 21, 2014 at 07:03 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mg2`
--

-- --------------------------------------------------------

--
-- Table structure for table `mg_activate`
--

CREATE TABLE IF NOT EXISTS `mg_activate` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `member_id` varchar(20) NOT NULL,
  `code` varchar(25) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_cache`
--

CREATE TABLE IF NOT EXISTS `mg_cache` (
  `id` varchar(30) NOT NULL,
  `data` longtext NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mg_content`
--

CREATE TABLE IF NOT EXISTS `mg_content` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `type` enum('Folder','Permalink','File') NOT NULL,
  `path` varchar(125) NOT NULL,
  `url` varchar(200) NOT NULL,
  `limit_downloads` int(7) NOT NULL,
  `category_tag` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_content_file_downloads`
--

CREATE TABLE IF NOT EXISTS `mg_content_file_downloads` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `content_id` int(9) NOT NULL,
  `member_id` varchar(20) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`,`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_data`
--

CREATE TABLE IF NOT EXISTS `mg_data` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `item_id` varchar(20) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_email_logs`
--

CREATE TABLE IF NOT EXISTS `mg_email_logs` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `created_by` varchar(20) NOT NULL,
  `body_html` mediumtext NOT NULL,
  `body_text` mediumtext NOT NULL,
  `subject` varchar(125) NOT NULL,
  `from` varchar(100) NOT NULL,
  `bcc` varchar(255) NOT NULL,
  `cc` varchar(255) NOT NULL,
  `track` tinyint(1) NOT NULL,
  `attachments` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_email_recipients`
--

CREATE TABLE IF NOT EXISTS `mg_email_recipients` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `email_id` int(9) NOT NULL,
  `member_id` varchar(20) NOT NULL,
  `sent` tinyint(1) NOT NULL,
  `sent_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email_id` (`email_id`,`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_email_tracking`
--

CREATE TABLE IF NOT EXISTS `mg_email_tracking` (
  `id` varchar(30) NOT NULL,
  `email_id` int(9) NOT NULL,
  `seen` mediumint(5) NOT NULL,
  `first_seen` datetime NOT NULL,
  `last_seen` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email_id` (`email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mg_forms_sets`
--

CREATE TABLE IF NOT EXISTS `mg_forms_sets` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `type_id` mediumint(5) NOT NULL,
  `name` varchar(85) NOT NULL,
  `description` mediumtext NOT NULL,
  `class` varchar(85) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `form_id` (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `mg_forms_sets`
--

INSERT INTO `mg_forms_sets` (`id`, `type_id`, `name`, `description`, `class`) VALUES
(1, 2, 'Desired Credentials', 'Please select your desired credentials.', ''),
(2, 2, 'Contact Information', 'Please provide us with some basic contact information.', '');

-- --------------------------------------------------------

--
-- Table structure for table `mg_forms_sets_fields`
--

CREATE TABLE IF NOT EXISTS `mg_forms_sets_fields` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `set_id` int(7) NOT NULL,
  `fixed_field_id` varchar(30) NOT NULL,
  `type` varchar(20) NOT NULL,
  `name` varchar(85) NOT NULL,
  `description` mediumtext NOT NULL,
  `options` mediumtext NOT NULL,
  `required` tinyint(1) NOT NULL,
  `row` mediumint(5) NOT NULL,
  `column` tinyint(1) NOT NULL COMMENT 'In descending order.',
  PRIMARY KEY (`id`),
  KEY `set_id` (`set_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `mg_forms_sets_fields`
--

INSERT INTO `mg_forms_sets_fields` (`id`, `set_id`, `fixed_field_id`, `type`, `name`, `description`, `options`, `required`, `row`, `column`) VALUES
(1, 1, 'username', 'text', 'Username', 'Select a username.', '', 1, 1, 1),
(2, 1, 'password', 'password', 'Password', 'Select a password.', '', 1, 2, 1),
(3, 1, 'password_repeat', 'password', 'Confirm Password', 'Please type your password in again to confirm its accuracy.', '', 1, 2, 2),
(4, 2, 'email', 'email', 'E-Mail', 'Please enter a valid email address.', '', 1, 3, 1),
(5, 2, 'first_name', 'text', 'First Name', 'Input your first name.', '', 1, 4, 1),
(6, 2, 'Last Name', 'text', 'last_name', 'Input your last name.', '', 1, 4, 2);

-- --------------------------------------------------------

--
-- Table structure for table `mg_form_sessions`
--

CREATE TABLE IF NOT EXISTS `mg_form_sessions` (
  `id` varchar(27) NOT NULL,
  `type_id` mediumint(5) NOT NULL,
  `started` datetime NOT NULL,
  `ended` datetime NOT NULL,
  `last_action` datetime NOT NULL,
  `member_id` varchar(20) NOT NULL,
  `form_data` longtext NOT NULL,
  `complete` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mg_gateway_cards`
--

CREATE TABLE IF NOT EXISTS `mg_gateway_cards` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `member_id` varchar(20) NOT NULL,
  `gateway_id` varchar(20) NOT NULL,
  `last_four` mediumint(4) NOT NULL,
  `exp_yy` smallint(2) NOT NULL,
  `exp_mm` smallint(2) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `address` varchar(150) NOT NULL,
  `city` varchar(65) NOT NULL,
  `state` varchar(3) NOT NULL,
  `country` varchar(2) NOT NULL,
  `type` varchar(12) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_hooks`
--

CREATE TABLE IF NOT EXISTS `mg_hooks` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `module` mediumint(5) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `path` varchar(125) NOT NULL,
  `name` varchar(85) NOT NULL,
  `trigger` varchar(25) NOT NULL,
  `trigger_when` enum('After','Before') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_logins`
--

CREATE TABLE IF NOT EXISTS `mg_logins` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `username_input` varchar(80) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_logs`
--

CREATE TABLE IF NOT EXISTS `mg_logs` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `member_id` varchar(20) NOT NULL,
  `action` varchar(30) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`,`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_members`
--

CREATE TABLE IF NOT EXISTS `mg_members` (
  `id` varchar(20) NOT NULL,
  `joined` datetime NOT NULL,
  `expires` datetime NOT NULL,
  `unlock` datetime NOT NULL,
  `status` enum('Active','Locked','Lapsed','Suspended','Pending Admin Activation','Pending Email Confirmation') NOT NULL DEFAULT 'Active',
  `type` mediumint(5) NOT NULL,
  `source` varchar(25) NOT NULL,
  `facebook_id` varchar(35) NOT NULL,
  `twitter_id` varchar(35) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mg_members`
--

INSERT INTO `mg_members` (`id`, `joined`, `expires`, `unlock`, `status`, `type`, `source`, `facebook_id`, `twitter_id`) VALUES
('PRIMARY_ADMIN', '2014-01-20 00:00:00', '2099-01-01 00:00:00', '0000-00-00 00:00:00', 'Active', 1, 'Setup', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `mg_members_fields`
--

CREATE TABLE IF NOT EXISTS `mg_members_fields` (
  `id` varchar(20) NOT NULL,
  `username` varchar(80) NOT NULL,
  `password` varchar(125) NOT NULL,
  `salt` varchar(8) NOT NULL,
  `email` varchar(125) NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `address1` varchar(125) NOT NULL,
  `city` varchar(65) NOT NULL,
  `state` varchar(3) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `country` varchar(3) NOT NULL,
  `phone` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mg_members_fields`
--

INSERT INTO `mg_members_fields` (`id`, `username`, `password`, `salt`, `email`, `first_name`, `last_name`, `address1`, `city`, `state`, `zip`, `country`, `phone`) VALUES
('PRIMARY_ADMIN', 'admin', 'pass123', 'ABC123', 'info@yoursite.com', 'Primary', 'Administrator', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `mg_member_types`
--

CREATE TABLE IF NOT EXISTS `mg_member_types` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` mediumtext NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `recur` varchar(5) NOT NULL,
  `trial` tinyint(1) NOT NULL,
  `trial_length` varchar(5) NOT NULL,
  `email_id` mediumint(5) NOT NULL,
  `visible_online` tinyint(1) NOT NULL,
  `initial_status` enum('Active','Pending Admin Activation','Pending Email Confirmation') NOT NULL,
  `login_redirection` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `mg_member_types`
--

INSERT INTO `mg_member_types` (`id`, `name`, `description`, `price`, `recur`, `trial`, `trial_length`, `email_id`, `visible_online`, `initial_status`, `login_redirection`) VALUES
(1, 'Administrator', 'Dashboard administrators with full privileges.', '0.00', '', 0, '', 0, 0, 'Active', ''),
(2, 'Free Membership', 'Free membership to our website.', '0.00', '', 0, '', 0, 1, 'Active', '');

-- --------------------------------------------------------

--
-- Table structure for table `mg_member_types_addons`
--

CREATE TABLE IF NOT EXISTS `mg_member_types_addons` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(85) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `description` mediumtext NOT NULL,
  `recur` varchar(5) NOT NULL,
  `type_id` mediumint(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_member_types_content`
--

CREATE TABLE IF NOT EXISTS `mg_member_types_content` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `content_id` mediumint(7) NOT NULL,
  `type_id` mediumint(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`,`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_modules`
--

CREATE TABLE IF NOT EXISTS `mg_modules` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL,
  `description` mediumtext NOT NULL,
  `author` varchar(125) NOT NULL,
  `url` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_password_reset`
--

CREATE TABLE IF NOT EXISTS `mg_password_reset` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `code` varchar(30) NOT NULL,
  `member_id` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`,`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_sessions`
--

CREATE TABLE IF NOT EXISTS `mg_sessions` (
  `id` varchar(30) NOT NULL,
  `started` datetime NOT NULL,
  `ended` datetime NOT NULL,
  `last_action` datetime NOT NULL,
  `member_id` varchar(20) NOT NULL,
  `salt` varchar(8) NOT NULL,
  `ip` varchar(25) NOT NULL,
  `browser` varchar(200) NOT NULL,
  `logged_in` tinyint(1) NOT NULL,
  `remember` tinyint(1) NOT NULL,
  `captcha` varchar(20) NOT NULL,
  `referrer` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mg_subscriptions`
--

CREATE TABLE IF NOT EXISTS `mg_subscriptions` (
  `id` varchar(15) NOT NULL,
  `member_id` varchar(20) NOT NULL,
  `member_type` mediumint(5) NOT NULL,
  `next_renew` datetime NOT NULL,
  `last_renew` datetime NOT NULL,
  `addons` mediumtext NOT NULL,
  `gateway_card_id` varchar(35) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mg_task_list`
--

CREATE TABLE IF NOT EXISTS `mg_task_list` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `scope` enum('add','edit','delete','list','other') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mg_templates`
--

CREATE TABLE IF NOT EXISTS `mg_templates` (
  `id` varchar(25) NOT NULL,
  `name` varchar(85) NOT NULL,
  `title` varchar(70) NOT NULL,
  `desc` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mg_templates_emails`
--

CREATE TABLE IF NOT EXISTS `mg_templates_emails` (
  `id` varchar(25) NOT NULL,
  `name` int(85) NOT NULL,
  `subject` int(125) NOT NULL,
  `cc` varchar(255) NOT NULL,
  `bcc` varchar(255) NOT NULL,
  `attach` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mg_transactions`
--

CREATE TABLE IF NOT EXISTS `mg_transactions` (
  `id` varchar(13) NOT NULL,
  `date` datetime NOT NULL,
  `gateway_id` varchar(20) NOT NULL,
  `status` enum('Approved','Rejected') NOT NULL,
  `reason` mediumtext NOT NULL,
  `member_id` varchar(20) NOT NULL,
  `card_id` int(7) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `member_type` mediumint(5) NOT NULL,
  `addons` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`,`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
