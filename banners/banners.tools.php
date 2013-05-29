<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
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
(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');


list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('banners', 'any');
cot_block($usr['isadmin']);

require_once(cot_incfile('banners', 'plug'));
require_once cot_langfile('banners', 'plug');

//cot_rc_link_file($cfg['plugins_dir'].'/banners/tpl/admin.css');

// Роутер
// Only if the file exists...
if (!$n) $n = 'main';

if (file_exists(cot_incfile('banners', 'plug', 'admin.'.$n))) {
    require_once cot_incfile('banners', 'plug', 'admin.'.$n);
    /* Create the controller */
    $_class = ucfirst($n).'Controller';
    $controller = new $_class();

    // TODO кеширование
    /* Perform the Request task */
    $shop_action = $a.'Action';
    if (!$a && method_exists($controller, 'indexAction')){
        $content = $controller->indexAction();
    }elseif (method_exists($controller, $shop_action)){
        $content = $controller->$shop_action();
    }else{
        // Error page
        cot_die_message(404);
        exit;
    }

    //ob_clean();
    // todo дописать как вывод для плагинов
    if (isset($content)){
        $tpl = new XTemplate(cot_tplfile('banners.admin', 'plug'));

        // Error and message handling
        cot_display_messages($tpl);

        $tpl->assign('CONTENT', $content);
        $tpl->parse('MAIN');

        $plugin_body .=  $tpl->text();
    }

}else{
    // Error page
    cot_die_message(404);
    exit;
}