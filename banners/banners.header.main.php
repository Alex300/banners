<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
[END_COT_EXT]
==================== */
/**
 * Cotonti Plugin Banners
 * Banner rotation plugin with statistics
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */

defined('COT_CODE') or die('Wrong URL.');

if (!empty($cache_ext) && $usr['id'] == 0 && $cfg['cache_' . $cache_ext]){
    cot_rc_embed_footer(
        "var bannerx = '{$sys['xk']}'"
    );
    cot_rc_link_footer($cfg['plugins_dir']."/banners/js/banners.js");
}