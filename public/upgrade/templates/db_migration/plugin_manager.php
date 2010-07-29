CREATE TABLE `<?php echo $table_prefix ?>plugins` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) <?php echo $default_collation ?> NOT NULL default '',
  `installed` tinyint NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB <?php echo $default_charset ?>;
