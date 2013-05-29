<?php
/**
 * Cotonti Plugin Banners
 * Banner rotation plugin with statistics
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL.');

global $ba_files_dir;

require(cot_incfile('banners', 'plug'));

// Удалить категорию с баннерами
if(file_exists($ba_files_dir)){
    ba_removeDir($ba_files_dir);
}