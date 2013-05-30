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

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
$db_ba_banners    = (isset($db_ba_bannerse)) ? $db_ba_banners :$db_x.'banners';
$db_ba_clients    = (isset($db_ba_clients )) ? $db_ba_clients  :$db_x.'banner_clients';
$db_ba_tracks     = (isset($db_ba_tracks)) ? $db_ba_tracks :$db_x.'banner_tracks';

$ba_allowed_ext = array('bmp', 'gif', 'jpg', 'jpeg', 'swf');
$ba_files_dir = 'datas/banners/';

require_once cot_incfile('banners', 'plug', 'resources');

function baAutoLoader($class){
    global $cfg;
    $fName = $cfg['plugins_dir'].DS.'banners'.DS.'models'.DS.$class.'.php';

    if(file_exists($fName)){
        include($fName);
    }
    return false;
}

/**
 * Generates a banner widget.
 * Use it as CoTemplate callback.
 *
 * @param string $cat  Category, semicolon separated
 * @param int $cnt  Banner count
 * @param string $tpl
 * @param string $order  'order' OR 'rand'
 * @param int|bool $client
 * @param int|bool $subcats
 * @return string
 *
 */
function banner_widget($cat = '', $cnt = 1, $tpl = 'banners', $order = 'order', $client = false, $subcats = false){
    global $sys;

    $cats = array();
    $client = (int)$client;
    $cnt = (int)$cnt;

    if ($cat != ''){
        $categs = explode(';', $cat);
        if(is_array($categs)){
            foreach($categs as $tmp){
                $tmp = trim($tmp);
                if(empty($tmp)) continue;
                if($subcats){
                    // Specific cat
//                    var_dump(cot_structure_children('banners', $tmp));
                    $cats = array_merge($cats, cot_structure_children('banners', $tmp, true, true, false, false));
                }else{
                    $cats[] = $tmp;
                }
            }
        }
        $cats = array_unique($cats);
    }

    $cond = array(
        array('ba_published', 1),
        "ba_publish_up <='".date('Y-m-d H:i:s', $sys['now'])."'",
        array('RAW', "ba_publish_down >='".date('Y-m-d H:i:s', $sys['now'])."' OR ba_publish_down ='0000-00-00 00:00:00'"),
        array('RAW', "ba_imptotal = 0 OR ba_impmade < ba_imptotal"),
    );
    if(count($cats) > 0){
        $cond[] = array('ba_cat', $cats);
    }
    if($client){
        $cond[] = array('bac_id', $client);
    }
    $ord = "ba_lastimp ASC";
    if($order = 'rand') $ord = 'RAND()';

    $banners = BaBanner::find($cond, $cnt, 0, $ord);

    if(!$banners) return '';

    // Display the items
    $t = new XTemplate(cot_tplfile($tpl, 'plug'));

    foreach($banners as $banner){
        $banner->impress();
        $t->assign(BaBanner::generateTags($banner, 'ROW_'));
        $t->parse('MAIN.ROW');
    }

    $t->parse();
    return $t->text();
}

/**
 * Импортировать файл
 */
function ba_importFile($inputname, $oldvalue = ''){
    global $lang, $L, $cot_translit, $ba_allowed_ext, $ba_files_dir, $cfg;

    $import = !empty($_FILES[$inputname]) ? $_FILES[$inputname] : array();
    $import['delete'] = cot_import('rdel_' . $inputname, 'P', 'BOL') ? 1 : 0;

    // Если пришел файл или надо удалить существующий
    if (is_array($import) && !$import['error'] && !empty($import['name'])){
        $fname = mb_substr($import['name'], 0, mb_strrpos($import['name'], '.'));
        $ext = mb_strtolower(mb_substr($import['name'], mb_strrpos($import['name'], '.') + 1));

        if(!file_exists($ba_files_dir)) mkdir($ba_files_dir);

        //check extension
        if(empty($ba_allowed_ext) || in_array($ext, $ba_allowed_ext)){
            if ($lang != 'en'){
                require_once cot_langfile('translit', 'core');
                $fname = (is_array($cot_translit)) ? strtr($fname, $cot_translit) : '';
            }					$fname = str_replace(' ', '_', $fname);
            $fname = preg_replace('#[^a-zA-Z0-9\-_\.\ \+]#', '', $fname);
            $fname = str_replace('..', '.', $fname);
            $fname = (empty($fname)) ? cot_unique() : $fname;

            $fname .= (file_exists("{$ba_files_dir}/$fname.$ext") && $oldvalue != $fname . '.' . $ext) ? date("YmjGis") : '';
            $fname .= '.' . $ext;

            $file['old'] = (!empty($oldvalue) && ($import['delete'] || $import['tmp_name'])) ? $oldvalue : '';
            $file['tmp'] = (!$import['delete']) ? $import['tmp_name'] : '';
            $file['new'] = (!$import['delete']) ? $ba_files_dir.$fname : '';

            if (!empty($file['old']) && file_exists($file['old'])) unlink($file['old']);
            if (!empty($file['tmp']) && !empty($file['tmp'])) {
                move_uploaded_file($file['tmp'], $file['new']);
            }

            return $file['new'];

        }else{
            cot_error($L['ba_err_inv_file_type'], $inputname);
            return '';
        }
    }
}

/**
 * Recalculates banner category counters
 *
 * @param string $cat Cat code
 * @return int
 * @global CotDB $db
 */
function cot_banners_sync($cat){
    $cond = array(
        array('ba_cat',$cat)
    );

    return BaBanner::count($cond);
}

/**
 * Update banner category code
 *
 * @param string $oldcat Old Cat code
 * @param string $newcat New Cat code
 * @return bool
 * @global CotDB $db
 */
function cot_banners_updatecat($oldcat, $newcat){
    global $db, $db_ba_banners;
    return (bool) $db->update($db_ba_banners, array("ba_cat" => $newcat), "ba_cat='".$db->prep($oldcat)."'");
}


/**
 * Renders stucture dropdown
 *
 * @param string $extension Extension code
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @param string $subcat Show only subcats of selected category
 * @param bool $hideprivate Hide private categories
 * @param bool $is_module TRUE for modules, FALSE for plugins
 * @param bool $add_empty
 * @return string
 * @global CotDB $db
 */
function ba_selectbox_structure($extension, $check, $name, $subcat = '', $hideprivate = true, $is_module = true, $add_empty = false){
    global $structure;

    $structure[$extension] = (is_array($structure[$extension])) ? $structure[$extension] : array();

    $result_array = array();
    foreach ($structure[$extension] as $i => $x)
    {
        $display = ($hideprivate && $is_module) ? cot_auth($extension, $i, 'W') : true;
        if ($display && !empty($subcat) && isset($structure[$extension][$subcat]))
        {
            $mtch = $structure[$extension][$subcat]['path'].".";
            $mtchlen = mb_strlen($mtch);
            $display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i === $subcat);
        }

        if ((!$is_module || cot_auth($extension, $i, 'R')) && $i!='all' && $display)
        {
            $result_array[$i] = $x['tpath'];
        }
    }
    $result = cot_selectbox($check, $name, array_keys($result_array), array_values($result_array), $add_empty);

    return($result);
}

/**
 * Remove dir
 * @param $path
 */
function ba_removeDir($path)
{
    if(file_exists($path) && is_dir($path)){
        $dirHandle = opendir($path);
        while (false !== ($file = readdir($dirHandle))){
            if ($file!='.' && $file!='..') {// исключаем папки с назварием '.' и '..'
                $tmpPath=$path.'/'.$file;
                chmod($tmpPath, 0777);

                // если папка
                if (is_dir($tmpPath)){
                    RemoveDir($tmpPath);
                }else{
                    // удаляем файл
                    if(file_exists($tmpPath)) unlink($tmpPath);
                }
            }
        }
        closedir($dirHandle);

        // удаляем текущую папку
        if(file_exists($path))  rmdir($path);

    }else {
        echo "Deleting directory not exists or it's file!";
    }
}

/**
 * Files list in folder
 * @param $folder
 * @return array
 */
function ba_getFilesList($folder){
    $all_files = array();
    $fp=opendir($folder);
    while($cv_file=readdir($fp)) {
        if(is_file($folder."/".$cv_file)) {
            $all_files[]=$folder."/".$cv_file;
        }elseif($cv_file!="." && $cv_file!=".." && is_dir($folder."/".$cv_file)){
            GetListFiles($folder."/".$cv_file,$all_files);
        }
    }
    closedir($fp);
    return $all_files;
}

spl_autoload_register('baAutoLoader');
