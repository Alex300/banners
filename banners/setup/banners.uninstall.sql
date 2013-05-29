/**
 * Completely removes banners data and tables
 */

DROP TABLE IF EXISTS `cot_banners`, `cot_banner_clients`, `cot_banner_tracks`;

DELETE FROM `cot_structure` WHERE `structure_area` = 'banners';