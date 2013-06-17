CREATE TABLE IF NOT EXISTS `cot_banners` (
  `ba_id` INTEGER NOT NULL auto_increment,
  `ba_type` INTEGER DEFAULT '0',
  `ba_title` VARCHAR(255) NOT NULL DEFAULT '',
  `ba_cat` VARCHAR(255) NOT NULL DEFAULT '',
  `ba_file` VARCHAR(255) DEFAULT '',
  `ba_width` INTEGER DEFAULT '0',
  `ba_height` INTEGER DEFAULT '0',
  `ba_alt` VARCHAR(255) DEFAULT '',
  `ba_customcode` TEXT DEFAULT '',
  `ba_clickurl` VARCHAR(200) DEFAULT '',
  `ba_description` TEXT DEFAULT '',
  `ba_published` TINYINT(1) UNSIGNED DEFAULT 0,
  `ba_sticky` TINYINT(1) UNSIGNED DEFAULT 0,
  `ba_publish_up` DATETIME DEFAULT '0000-00-00 00:00:00',
  `ba_publish_down` DATETIME DEFAULT '0000-00-00 00:00:00',
  `ba_imptotal` INTEGER DEFAULT '0',
  `ba_impmade` INTEGER DEFAULT '0',
  `ba_lastimp` double DEFAULT '0',
  `ba_clicks` INTEGER DEFAULT '0',
  `bac_id` INTEGER DEFAULT '0',
  `ba_track_clicks` TINYINT DEFAULT '-1',
  `ba_track_impressions` TINYINT DEFAULT '-1',
  `ba_purchase_type` TINYINT DEFAULT '-1',
  `ba_ordering` INTEGER DEFAULT 0,
  `ba_created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ba_created_by` int(11) NOT NULL DEFAULT '0',
  `ba_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ba_updated_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`ba_id`),
  INDEX `idx_published` (`ba_published`),
  INDEX `idx_banner_cat`(`ba_cat`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT = 'Banners';

CREATE TABLE IF NOT EXISTS `cot_banner_clients` (
  `bac_id` INTEGER NOT NULL auto_increment,
  `bac_title` VARCHAR(255) NOT NULL DEFAULT '',
  `bac_email` VARCHAR(255) DEFAULT '',
  `bac_extrainfo` TEXT,
  `bac_published` TINYINT(1) UNSIGNED DEFAULT 0,
  `bac_purchase_type` TINYINT NOT NULL DEFAULT '-1',
  `bac_track_clicks` TINYINT NOT NULL DEFAULT '-1',
  `bac_track_impressions` TINYINT NOT NULL DEFAULT '-1',
  PRIMARY KEY  (`bac_id`)
)  ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `cot_banner_tracks` (
  `track_date` DATETIME NOT NULL,
  `track_type` INTEGER UNSIGNED NOT NULL,
  `ba_id` INTEGER UNSIGNED NOT NULL,
  `track_count` INTEGER UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`track_date`, `track_type`, `ba_id`),
  INDEX `idx_track_date` (`track_date`),
  INDEX `idx_track_type` (`track_type`),
  INDEX `idx_banner_id` (`ba_id`)
)  ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Default banners categories
-- INSERT INTO `cot_structure` (`structure_area`, `structure_code`, `structure_path`, `structure_tpl`, `structure_title`,
--                             `structure_desc`, `structure_icon`, `structure_locked`, `structure_count`) VALUES
-- ('banners', 'sample', '1', '', 'Sample', 'Sample category', '', 0, 0);
