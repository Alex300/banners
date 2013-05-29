<?PHP
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
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

require_once cot_incfile('banners', 'plug');
require_once cot_langfile('banners');

// Роутер
// Only if the file exists...
if (!$m) $m = 'main';

if (file_exists(cot_incfile('banners', 'plug', $m))) {
    require_once cot_incfile('banners', 'plug', $m);
    /* Create the controller */
    $_class = ucfirst($m).'Controller';
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
//    require_once $cfg['system_dir'] . '/header.php';
//    if (isset($content)) echo $content;
//    require_once $cfg['system_dir'] . '/footer.php';
}else{
    // Error page
    cot_die_message(404);
    exit;
}
