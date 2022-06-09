SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
 
CREATE TABLE `#__logregsms_confirm` (
  `id` int(11) NOT NULL,
  `created_on` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `from` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `is_confirmed` int(11) NOT NULL,
  `state` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `#__logregsms_smsarchives` (
  `id` int(11) NOT NULL,
  `created_on` varchar(20) NOT NULL,
  `time` varchar(20) NOT NULL,
  `to` varchar(20) NOT NULL,
  `from` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `text` text NOT NULL,
  `result` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `#__logregsms_confirm`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#__logregsms_smsarchives`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `#__logregsms_confirm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#__logregsms_smsarchives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
