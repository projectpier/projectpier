CREATE TABLE `<?php echo $table_prefix ?>project_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(50) <?php echo $default_collation ?> NOT NULL default '',
  `description` varchar(255) <?php echo $default_collation ?> default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB <?php echo $default_charset ?>;

CREATE TABLE `<?php echo $table_prefix ?>project_tickets` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL default '0',
  `category_id` int(10) unsigned default NULL,
  `milestone_id` int(10) unsigned default NULL, 
  `assigned_to_company_id` smallint(5) unsigned default NULL,
  `assigned_to_user_id` int(10) unsigned default NULL,
  `summary` varchar(200) <?php echo $default_collation ?> NOT NULL default '',
  `status` enum('new', 'open', 'pending', 'closed') <?php echo $default_collation ?> NOT NULL default 'new',
  `type` enum('defect','enhancement','feature request') <?php echo $default_collation ?> NOT NULL default 'defect',
  `description` text <?php echo $default_collation ?>,
  `priority` enum('critical','major','minor','trivial') <?php echo $default_collation ?> NOT NULL default 'major',
  `is_private` tinyint(1) NOT NULL default '0',
  `closed_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `closed_by_id` int(10) default NULL,
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by_id` int(10) unsigned default NULL,
  `updated_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_by_id` int(10) default NULL,
  `updated` enum('settings','comment','attachment','open','closed') <?php echo $default_collation ?> default NULL,
  PRIMARY KEY  (`id`),
  KEY `created_on` (`created_on`),
  KEY `closed_on` (`closed_on`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB <?php echo $default_charset ?>;


CREATE TABLE `<?php echo $table_prefix ?>ticket_changes` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `ticket_id` int(11) unsigned NOT NULL default '0',
  `type` enum('status','priority','assigned to','summary','category','type','private','comment','attachment') <?php echo $default_collation ?> NOT NULL,
  `from_data` varchar(255) <?php echo $default_collation ?> NOT NULL default '',
  `to_data` varchar(255) <?php echo $default_collation ?> NOT NULL default '',
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by_id` int(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `created_on` (`created_on`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB <?php echo $default_charset ?>;

CREATE TABLE `<?php echo $table_prefix ?>ticket_subscriptions` (
  `ticket_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ticket_id`,`user_id`)
) ENGINE=InnoDB <?php echo $default_charset ?>;

ALTER TABLE `<?php echo $table_prefix ?>project_users` ADD COLUMN `can_manage_tickets` tinyint(1) unsigned default '0' AFTER `can_manage_files`;

INSERT INTO `<?php echo $table_prefix ?>config_options` (`category_name`, `name`, `value`, `config_handler_class`, `is_system`, `option_order`, `dev_comment`) VALUES ('system', 'tickets_per_page', '25', 'IntegerConfigHandler', 1, 0, NULL);

INSERT INTO `<?php echo $table_prefix ?>config_options` (`category_name`, `name`, `value`, `config_handler_class`, `is_system`, `option_order`, `dev_comment`) VALUES ('system', 'categories_per_page', '25', 'IntegerConfigHandler', 1, 0, NULL);

