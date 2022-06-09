-- noinspection SqlDialectInspectionForFile
-- noinspection SqlNoDataSourceInspectionForFile
CREATE TABLE IF NOT EXISTS `#__rsticketspro_configuration` (
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `name` (`name`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_custom_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `values` text NOT NULL,
  `additional` text NOT NULL,
  `validation` text NOT NULL,
  `required` tinyint(1) NOT NULL,
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `department_id` (`department_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_custom_fields_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custom_field_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_field_id` (`custom_field_id`),
  KEY `ticket_id` (`ticket_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `prefix` varchar(255) NOT NULL,
  `assignment_type` tinyint(1) NOT NULL,
  `generation_rule` tinyint(1) NOT NULL,
  `next_number` int(11) NOT NULL DEFAULT '1',
  `email_address` varchar(255) NOT NULL,
  `email_address_fullname` varchar(255) NOT NULL,
  `email_use_global` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `customer_send_email` tinyint(1) NOT NULL,
  `customer_send_copy_email` tinyint(1) NOT NULL DEFAULT '1',
  `customer_attach_email` tinyint(1) NOT NULL DEFAULT '1',
  `staff_send_email` tinyint(1) NOT NULL,
  `staff_attach_email` tinyint(1) NOT NULL DEFAULT '1',
  `upload` tinyint(1) NOT NULL,
  `upload_ticket_required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `upload_extensions` text NOT NULL,
  `upload_size` decimal(10,2) unsigned NOT NULL,
  `upload_files` int(11) NOT NULL,
  `download_type` varchar(255) NOT NULL DEFAULT 'attachment',
  `notify_new_tickets_to` text NOT NULL,
  `notify_assign` tinyint(1) NOT NULL,
  `priority_id` int(11) NOT NULL,
  `cc` text NOT NULL,
  `bcc` text NOT NULL,
  `predefined_subjects` text NOT NULL,
  `jgroups` mediumtext NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `customer_send_email` (`customer_send_email`),
  KEY `staff_send_email` (`staff_send_email`),
  KEY `upload` (`upload`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(64) NOT NULL,
  `type` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang` (`lang`,`type`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `add_ticket` tinyint(1) NOT NULL,
  `add_ticket_customers` tinyint(1) NOT NULL,
  `add_ticket_staff` tinyint(1) NOT NULL,
  `update_ticket` tinyint(1) NOT NULL,
  `update_ticket_custom_fields` tinyint(1) NOT NULL,
  `delete_ticket` tinyint(1) NOT NULL,
  `answer_ticket` tinyint(1) NOT NULL,
  `update_ticket_replies` tinyint(1) NOT NULL,
  `update_ticket_replies_customers` tinyint(1) NOT NULL,
  `update_ticket_replies_staff` tinyint(1) NOT NULL,
  `delete_ticket_replies_customers` tinyint(1) NOT NULL,
  `delete_ticket_replies_staff` tinyint(1) NOT NULL,
  `delete_ticket_replies` tinyint(1) NOT NULL,
  `assign_tickets` tinyint(1) NOT NULL,
  `change_ticket_status` tinyint(1) NOT NULL,
  `see_unassigned_tickets` tinyint(1) NOT NULL,
  `see_other_tickets` tinyint(1) NOT NULL,
  `move_ticket` tinyint(1) NOT NULL,
  `view_notes` tinyint(1) NOT NULL,
  `add_note` tinyint(1) NOT NULL,
  `update_note` tinyint(1) NOT NULL,
  `update_note_staff` tinyint(1) NOT NULL,
  `delete_note` tinyint(1) NOT NULL,
  `delete_note_staff` tinyint(1) NOT NULL,
  `export_tickets` tinyint(1) NOT NULL,
  UNIQUE KEY `GroupId` (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_kb_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `thumb` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `private` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_kb_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `private` tinyint(1) NOT NULL,
  `from_ticket_id` int(11) NOT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_kb_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `conditions` text NOT NULL,
  `publish_article` tinyint(1) NOT NULL,
  `private` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_priorities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `bg_color` varchar(7) NOT NULL,
  `fg_color` varchar(7) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_searches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `params` text NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `user_id` (`user_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `priority_id` int(11) NOT NULL,
  `signature` text NOT NULL,
  `exclude_auto_assign` tinyint(1) NOT NULL DEFAULT '0',
  `can_delete_time_history` tinyint(1) NOT NULL DEFAULT '0',
  `can_delete_own_time_history` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`),
  KEY `group_id` (`group_id`,`user_id`),
  KEY `priority_id` (`priority_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_staff_to_department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `department_id` (`department_id`),
  KEY `user_id` (`user_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status_id` int(11) NOT NULL,
  `priority_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `alternative_email` varchar(255) NOT NULL,
  `last_reply` datetime NOT NULL,
  `last_reply_customer` tinyint(1) NOT NULL,
  `replies` int(11) NOT NULL,
  `autoclose_sent` int(11) NOT NULL DEFAULT '0',
  `closed` datetime NOT NULL,
  `flagged` tinyint(1) NOT NULL DEFAULT '0',
  `agent` text NOT NULL,
  `referer` text NOT NULL,
  `ip` varchar(16) NOT NULL,
  `logged` tinyint(1) NOT NULL,
  `feedback` tinyint(1) NOT NULL,
  `followup_sent` tinyint(1) NOT NULL DEFAULT '0',
  `has_files` tinyint(1) unsigned NOT NULL,
  `time_spent` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`),
  KEY `staff_id` (`staff_id`),
  KEY `customer_id` (`customer_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_ticket_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `ticket_message_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `downloads` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_ticket_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `date` datetime NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_ticket_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` datetime NOT NULL,
  `html` tinyint(1) NOT NULL,
  `submitted_by_staff` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `user_id` (`user_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_timespent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
   PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_ticket_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `user_id` (`user_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rsticketspro_tokens` (
  `user_id` int(11) NOT NULL,
  `token` varchar(100) NOT NULL,
  PRIMARY KEY  (`user_id`)
) DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__rsticketspro_configuration` (`name`, `value`) VALUES
('global_register_code', ''),
('date_format', 'd.m.Y H:i:s'),
('date_format_notime', 'd.m.Y'),
('rsticketspro_link', '1'),
('allow_rich_editor', '1'),
('allow_rich_editor_buttons', '1'),
('global_message', ''),
('submit_message', ''),
('ticket_view', 'accordion'),
('rsticketspro_add_tickets', '1'),
('show_ticket_info', '1'),
('show_user_info', 'name'),
('show_ticket_voting', '1'),
('allow_ticket_closing', '1'),
('allow_ticket_reopening', '1'),
('ticket_viewing_history', '1'),
('avatars', ''),
('captcha_enabled', '1'),
('captcha_enabled_for', 'unregistered,customers,staff'),
('captcha_characters', '5'),
('captcha_case_sensitive', '0'),
('email_use_global', '1'),
('email_address', 'your@email.com'),
('email_address_fullname', 'Customer Support'),
('reply_above', 'Please reply above this line'),
('use_reply_above', '1'),
('autoclose_enabled', '1'),
('autoclose_automatically', '0'),
('autoclose_cron_lastcheck', '0'),
('autoclose_cron_interval', '10'),
('autoclose_email_interval', '1'),
('autoclose_interval', '1'),
('followup_interval', '1'),
('enable_followup', '0'),
('followup_enabled_time', '0'),
('followup_cron_lastcheck', '0'),
('followup_cron_interval', '10'),
('show_email_link', '1'),
('messages_direction', 'DESC'),
('color_whole_ticket', '0'),
('submit_redirect', ''),
('staff_force_departments', '0'),
('kb_template_body', '<div>\r\n<h2>{ticket_subject}</h2>\r\n<p><strong>Department:</strong> {ticket_department}</p>\r\n<p><strong>Date:</strong> {ticket_date}</p>\r\n{ticket_messages}</div>'),
('kb_template_ticket_body', '<div class="ticket_message">\r\n<p><strong>{message_date}</strong></p>\r\n<p><strong class="message_user">{message_user}:</strong></p>\r\n<div class="message_text">{message_text}</div>\r\n</div>'),
('kb_hot_hits', '200'),
('notice_email_address', ''),
('notice_max_replies_nr', '0'),
('notice_not_allowed_keywords', ''),
('notice_replies_with_no_response_nr', '0'),
('kb_comments', '0'),
('show_kb_search', '1'),
('show_signature', '1'),
('allow_predefined_subjects', '0'),
('customer_itemid', ''),
('staff_itemid', ''),
('enable_time_spent', '1'),
('time_spent_unit', 'h'),
('calculate_itemids', '1'),
('allow_password_change', '0'),
('emails_as_usernames', '0'),
('user_type', '2'),
('admin_groups', '6,7,8'),
('kb_load_plugin', '0'),
('bootstrap', '1'),
('jquery', '1'),
('use_magnific_popup', '0'),
('use_btn_group_radio', '1'),
('recaptcha_new_site_key', ''),
('recaptcha_new_secret_key', ''),
('recaptcha_new_theme', 'light'),
('recaptcha_new_type', 'image'),
('store_ip', '1'),
('store_user_agent', '1'),
('allow_self_anonymisation', '0'),
('anonymise_joomla_data', '1'),
('forms_consent', '1'),
('show_alternative_email', '0'),
('show_reply_as_customer', '1'),
('time_spent_type', 'input'),
('export_limit', '100'),
('blocklist', '');

INSERT IGNORE INTO `#__rsticketspro_emails` (`lang`, `type`, `subject`, `message`) VALUES
('en-GB', 'add_ticket_customer', '', '<p>Hello {customer_name},</p>\r\n<p>Thank you for contacting us. One of our staff members will attend to your problem as soon as possible.<br />You can view your ticket here:<br /><a href="{ticket}">{code}</a></p>'),
('en-GB', 'add_ticket_staff', '', '<p>Hello,</p>\r\n<p>A new ticket requires your attention:</p>\r\n<p><a href="{ticket}">{code}</a></p>\r\n<p>{customer_email} wrote:</p>\r\n<p>{message}</p>\r\n<p>{custom_fields}</p>'),
('en-GB', 'add_ticket_reply_customer', '', '<p>Hello {customer_name}.</p>\r\n<p>You have a new message from {staff_name}.<br />Re: {subject}<br />Message: {message}<br /><br />You can view your ticket here:<br /><a href="{ticket}">{code}</a></p>'),
('en-GB', 'add_ticket_reply_staff', '', '<p>Hello {staff_name}.</p>\r\n<p>You have a new message from  {customer_name}.<br /> Re: {subject}<br /> Message: {message}<br /> <br /> You can view the ticket here:<br /> <a href="{ticket}">{code}</a></p>'),
('en-GB', 'notification_email', 'Your ticket will be closed', '<p>Your ticket with subject "{subject}" had no activity for {inactive_interval} days.</p>\r\n<p>It will be automatically closed in {close_interval} days if no additional action is performed.</p>\r\n<p>Please log in to <br /><br /> <a href="{live_site}index.php?option=com_rsticketspro">Our Support Center</a> <br /><br /> and go to <a href="{live_site}index.php?option=com_rsticketspro">My Tickets</a> in order to view the status of your support request.</p>'),
('en-GB', 'reject_email', 'Re: {subject}', '<p>Hello {customer_name},<br /><br />Unfortunately your email for department {department} could not be processed. Only registered users can submit tickets by email.<br />We are sorry for the inconvenience. You can visit <a href="{live_site}">our website</a> instead.</p>'),
('en-GB', 'add_ticket_notify', '', '<p>Hello,</p>\r\n<p>A new ticket has been added:</p>\r\n<p><a href="{ticket}">{code}</a></p>\r\n<p>{customer_email} wrote:</p>\r\n<p>{message}</p>\r\n<p>{custom_fields}</p>'),
('en-GB', 'new_user_email', 'New user details', '<p>Here are your login details:</p>\r\n<p>Username: <strong>{username}</strong></p>\r\n<p>Password: <strong>{password}</strong></p>\r\n<p>Please note that this is your temporary password. You can login and change it at any time.</p>\r\n<p> Please log in to <br/><br/>\r\n  <a href="{live_site}index.php?option=com_rsticketspro">Our Support Center</a> <br/><br/>\r\n  and go to <a href="{live_site}">My Tickets</a> in order to view the status of your support request.</p>'),
('en-GB', 'notification_max_replies_nr', '{code} This unassigned ticket has received too many replies', 'The ticket <a href="{ticket}">{code}</a> has received {replies} replies without a staff member being assigned to it.\r\n<p><u>Customer Information</u></p>\r\n<p>Name: {customer_name}</p>\r\n<p>Username: {customer_username}</p>\r\n<p>Email: {customer_email}</p>\r\n\r\n<p><u>Staff Information</u></p>\r\n<p>Unassigned</p>\r\n\r\n<p><u>Ticket Information</u></p>\r\n<p>Subject: {subject}</p>\r\n<p>Message:<br />{message}</p>'),
('en-GB', 'notification_replies_with_no_response_nr', '{code} This ticket has received too many replies', 'The ticket <a href="{ticket}">{code}</a> has received {replies} replies without any response from the designated staff member.\r\n<p><u>Customer Information</u></p>\r\n<p>Name: {customer_name}</p>\r\n<p>Username: {customer_username}</p>\r\n<p>Email: {customer_email}</p>\r\n\r\n<p><u>Staff Information</u></p>\r\n<p>Name: {staff_name}</p>\r\n<p>Username: {staff_username}</p>\r\n<p>Email: {staff_email}</p>\r\n\r\n<p><u>Ticket Information</u></p>\r\n<p>Subject: {subject}</p>\r\n<p>Message:<br />{message}</p>'),
('en-GB', 'notification_not_allowed_keywords', 'This ticket contains a keyword', 'The ticket <a href="{ticket}">{code}</a> contains a keyword.\r\n<p><u>Customer Information</u></p>\r\n<p>Name: {customer_name}</p>\r\n<p>Username: {customer_username}</p>\r\n<p>Email: {customer_email}</p>\r\n\r\n<p><u>Staff Information</u></p>\r\n<p>Name: {staff_name}</p>\r\n<p>Username: {staff_username}</p>\r\n<p>Email: {staff_email}</p>\r\n\r\n<p><u>Ticket Information</u></p>\r\n<p>Subject: {subject}</p>\r\n<p>Message:<br />{message}</p>'),
('en-GB', 'notification_department_change', 'Department changed', 'The ticket <a href="{ticket}">{code}</a> had the department changed from {department_from} to {department_to}'),
('en-GB', 'feedback_followup_email', 'How would you rate the help you received?', '<p>The ticket <a href="{ticket}">{code}</a> regarding "{subject}" has been closed.</p>\r\n<p>Did we help you solve your problem?</p>\r\n<p><a href="{yes}">Yes, my problem has been solved.</a></p>\r\n<p><a href="{no}">No.</a></p>\r\n<p>Your feedback helps us improve our services!</p>');

INSERT IGNORE INTO `#__rsticketspro_priorities` (`id`, `name`, `bg_color`, `fg_color`, `published`, `ordering`) VALUES
(1, 'low', '', '', 1, 1),
(2, 'normal', '', '', 1, 2),
(3, 'high', '', '', 1, 3);

INSERT IGNORE INTO `#__rsticketspro_statuses` (`id`, `name`, `published`, `ordering`) VALUES
(1, 'open', 1, 1),
(2, 'closed', 1, 3),
(3, 'on-hold', 1, 2);