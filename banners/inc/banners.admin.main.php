<?php
(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

/**
 * Main Admin Controller class for the Banners
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */
class MainController{

    /**
     * Панель управления
     * Список баннеров
     * @todo фильтры
     */
    public function indexAction(){
        global $L, $adminpath, $cfg, $sys;

        $adminpath[] = '&nbsp;'.$L['ba_banners'];

        $sortFields = array(
            array('ba_id', 'ID'),
            array('ba_title', $L['Title'] ),
            array('ba_cat', $L['Category']),
            array('ba_published', $L['ba_published']),
            array('bac_id', $L['ba_client']),
            array('ba_impmade', $L['ba_impressions']),
            array('ba_clicks', $L['ba_clicks']),
            array('ba_publish_up', $L['ba_publish_up']),
            array('ba_publish_down', $L['ba_publish_down']),
        );

        $so = cot_import('so', 'G', 'ALP'); // order field name without 'ba_'
        $w = cot_import('w', 'G', 'ALP', 4); // order way (asc, desc)
        $fil = cot_import('fil', 'G', 'ARR');  // filters

        $maxrowsperpage = $cfg['maxrowsperpage'];
        list($pg, $d, $durl) = cot_import_pagenav('d', $maxrowsperpage); //page number for banners list

        $list_url_path = array('m' => 'other', 'p'=>'banners');
        if(empty($so)){
            $so = 'ba_title';
        }else{
            $list_url_path['so'] = $so;
        }
        if(empty($w)){
            $w = 'ASC';
        }else{
            $list_url_path['w'] = $w;
        }

        $cond = array();

        if (!empty($fil)){
            foreach($fil as $key => $val){
                $val = trim(cot_import($val, 'D', 'TXT'));
                if(empty($val) && $val !== '0') continue;
                if(in_array($key, array('title') )){
                    $cond[] = array($key, "*{$val}*");
                    $list_url_path["fil[{$key}]"] = $val;
                }elseif($key == 'bac_id'){
                    $cond[] = array($key, $val);
                    $list_url_path["fil[{$key}]"] = $val;
                }else{
                    $cond[] = array('ba_'.$key, $val);
                    $list_url_path["fil[{$key}]"] = $val;
                }
            }
        }else{
            $fil = array();
        }

        $list = BaBanner::find($cond, $maxrowsperpage, $d, $so.' '.$w);
        if(!$list) $list = array();
        $totallines = BaBanner::count($cond);

        $pagenav = cot_pagenav('admin', $list_url_path, $d, $totallines, $maxrowsperpage);

        $act = cot_import('act', 'G', 'ALP');
        if($act == 'delete'){
            $urlArr = $list_url_path;
            if($pagenav['current'] > 0) $urlArr['d'] = $pagenav['current'];
            $id = cot_import('id', 'G', 'INT');
            cot_check_xg();
            $item = BaBanner::getById($id);
            if (!$item){
                cot_error($L['No_items']." id# ".$id);
                cot_redirect(cot_url('admin', $urlArr, '', true));
            }
            $item->delete();
            cot_message($L['alreadydeletednewentry']." # $id - {$item->ba_title}");
            cot_redirect(cot_url('admin', $urlArr, '', true));

        }

        $tpl = new XTemplate(cot_tplfile('banners.admin.main', 'plug'));

        $i = $d+1;
        foreach ($list as $item){
            $tpl->assign(BaBanner::generateTags($item, 'LIST_ROW_'));
            $delUrlArr = $list_url_path;
            $delUrlArr['act'] = 'delete';
            $delUrlArr['id'] = $item->ba_id;
            if($pagenav['current'] > 0) $delUrlArr['d'] = $pagenav['current'];
            $delUrlArr['x'] = $sys['xk'];

            $tpl->assign(array(
                'LIST_ROW_NUM' => $i,
                'LIST_ROW_DELETE_URL' => cot_confirm_url(cot_url('admin', $delUrlArr), 'admin'),
            ));
            $i++;
            $tpl->parse('MAIN.LIST_ROW');
        }

        // Сортировка
        $sort = array();
        foreach ($sortFields as $fld){
            if (is_array($fld)){
                $sort[$fld[0]] = $fld[1];
            }else{
                $sort[$fld] = $fld;
            }
        }

        $clients = BaClient::getKeyValPairsList();
        if(!$clients) $clients = array();

        $tpl->assign(array(
            'LIST_PAGINATION' => $pagenav['main'],
            'LIST_PAGEPREV' => $pagenav['prev'],
            'LIST_PAGENEXT' => $pagenav['next'],
            'LIST_CURRENTPAGE' => $pagenav['current'],
            'LIST_TOTALLINES' => $totallines,
            'LIST_MAXPERPAGE' => $maxrowsperpage,
            'LIST_TOTALPAGES' => $pagenav['total'],
            'LIST_ITEMS_ON_PAGE' => $pagenav['onpage'],
            'LIST_URL' =>  cot_url('admin', $list_url_path, '', true),
            'PAGE_TITLE' => $L['ba_clients'],

            'SORT_BY' => cot_selectbox($so, 'so', array_keys($sort), array_values($sort), false),
            'SORT_WAY' => cot_selectbox($w, 'w', array('ASC', 'DESC'), array($L['Ascending'], $L['Descending']), false),
            'FILTER_PUBLISHED' => cot_selectbox($fil['published'], 'fil[published]',  array(0,1),
                array($L['No'], $L['Yes'])),
            'FILTER_CLIENT' => cot_selectbox($fil['bac_id'], 'fil[bac_id]',  array_keys($clients),
                array_values($clients)),
            'FILTER_CATEGORY' => ba_selectbox_structure('banners', $fil['cat'], 'fil[cat]', '', false, false, true),
            'FILTER_VALUES' => $fil

        ));

        $tpl->assign(array(

            'PAGE_TITLE' => $L['ba_banners'],

        ));
        $tpl->parse('MAIN');
        return $tpl->text();

    }

    /**
     * Создание / редактирование купона
     * @todo произвольный урл баннера
     * @return string
     */
    public function editAction(){
        global $adminpath, $structure, $cfg,  $L, $usr, $sys;

        $adminpath[] = array(cot_url('admin', array('m' => 'other', 'p'=>'banners')), $L['ba_banners']);

       if(empty($structure['banners'])) cot_error($L['ba_category_no']);

        $id = cot_import('id', 'G', 'INT');

        $act = cot_import('act', 'P', 'ALP');
        if(!$id){
            $id = 0;
            $adminpath[] = '&nbsp;'.$L['Add'];
            $banner = new BaBanner;
        }else{
            $banner = BaBanner::getById($id);

            $adminpath[] = $L['ba_banner_edit'].": ".htmlspecialchars($banner->ba_title);
        }

        if ($act == 'save'){
            $item = array();
//            $item['ba_id'] = cot_import('rid', 'P', 'INT');
            $item['ba_title'] = cot_import('rtitle', 'P', 'TXT');
            if(empty($item['ba_title'])){
                cot_error($L['ba_err_titleempty'], 'rtitle');
            }
            $item['ba_cat'] = cot_import('rcat', 'P', 'TXT');
            $file = ba_importFile('rfile', $banner->ba_file);
            $delFile = cot_import('rdel_rfile', 'P', 'BOL') ? 1 : 0;
            if($delFile) $item['ba_file'] = '';
            // @todo проверка mime типа и выставление $item['ba_type'] в зависимости от него
            $item['ba_type'] = cot_import('rtype', 'P', 'TXT');
            $item['ba_width'] = cot_import('rwidth', 'P', 'INT');
            $item['ba_height'] = cot_import('rheight', 'P', 'INT');

            if(!empty($file)){
                // Try to get image size
                @$gd = getimagesize($file);
                if (!$gd){
                    cot_error($L['ba_err_inv_file_type'], 'rfile');
                }else{
                    if(empty($item['ba_width'])){
                        $item['ba_width'] = $gd[0];
                    }
                    if(empty($item['ba_height'])){
                        $item['ba_height'] = $gd[1];
                    }
                    // Get image type
                    switch ($gd[2]) {
                        //case 1: // IMAGE
                        case IMAGETYPE_GIF:
                        case IMAGETYPE_JPEG:
                        case IMAGETYPE_PNG:
                        case IMAGETYPE_BMP:
                            if($item['ba_type'] != BaBanner::TYPE_CUSTOM){
                                $item['ba_type'] = BaBanner::TYPE_IMAGE;
                            }
                            break;
                        //case 4: // SWF ( Flash)
                        case IMAGETYPE_SWF:
                        case IMAGETYPE_SWC:
                            if($item['ba_type'] != BaBanner::TYPE_CUSTOM){
                                $item['ba_type'] = BaBanner::TYPE_FLASH;
                            }
                            break;
                        default:
                            cot_error($L['ba_err_inv_file_type'], 'rfile');
                    }
                }
            }elseif($item['ba_type'] != BaBanner::TYPE_CUSTOM){
                // Если файл не передан, тип не записываем
                if(!$delFile) unset($item['ba_type']);
            }

            $item['ba_alt'] = cot_import('ralt', 'P', 'TXT');
            $item['ba_customcode'] = cot_import('rcustomcode', 'P', 'HTM');
            $item['ba_clickurl'] = cot_import('rclickurl', 'P', 'TXT');
            $item['ba_description'] = cot_import('rdescription', 'P', 'TXT');
            $item['ba_rsticky'] = cot_import('rsticky', 'P', 'BOL');
            $item['ba_publish_up'] = cot_import_date('rpublish_up');
            $item['ba_publish_up'] = (!empty($item['ba_publish_up'])) ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00';
            $item['ba_publish_down'] = cot_import_date('rpublish_down');
            $item['ba_publish_down'] = (!empty($item['ba_publish_down'])) ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00';
            $item['ba_imptotal'] = cot_import('rimptotal', 'P', 'INT');
            $item['ba_impmade'] = cot_import('rimpmade', 'P', 'INT');
            $item['ba_clicks'] = cot_import('rclicks', 'P', 'INT');
            $item['bac_id'] = cot_import('rbac_id', 'P', 'INT');
            $item['ba_purchase_type'] = cot_import('rpurchase_type', 'P', 'INT');
            $item['ba_track_impressions'] = cot_import('rtrack_impressions', 'P', 'INT');
            $item['ba_track_clicks'] = cot_import('rtrack_clicks', 'P', 'INT');
            $item['ba_published'] = cot_import('rpublished', 'P', 'BOL');

            if(!cot_error_found()){
                if(!empty($file)){
                    $item['ba_file'] = $file;
                }
                $banner->setData($item);
                if ($id = $banner->save()){
                    cot_message($L['ba_saved']);
                }
                cot_redirect(cot_url('admin', array('m'=>'other', 'p'=>'banners','a'=>'edit', 'id'=>$id),
                        '', true));

            }else{
                // Удалим загруженный файл
                if(!empty($file) && file_exists($file)) unlink($file);
            }
        }

        $tpl = new XTemplate(cot_tplfile('banners.admin.main', 'plug'));

        $delUrl = '';
        if($banner->ba_id > 0){
            $delUrl = cot_confirm_url(cot_url('admin', 'm=other&p=banners&act=delete&id='.$banner->ba_id.'&'.cot_xg()), 'admin');
        }

        $types = array(
            '0' => $L['ba_type_file'],
            BaBanner::TYPE_CUSTOM => $L['ba_custom_code']
        );

        $clients = BaClient::getKeyValPairsList();
        if(!$clients) $clients = array();
        $purchase = array(
            BaClient::PURCHASE_DEFAULT => $L['ba_client_default'],
            BaClient::PURCHASE_UNLIMITED => $L['ba_unlimited'],
            BaClient::PURCHASE_YEARLY => $L['ba_pt_yearly'],
            BaClient::PURCHASE_MONTHLY => $L['ba_pt_monthly'],
            BaClient::PURCHASE_WEEKLY => $L['ba_pt_weekly'],
            BaClient::PURCHASE_DAILY => $L['ba_pt_daily']
        );

        $track = array(
            -1 => $L['ba_client_default'],
            0 => $L['No'],
            1 => $L['Yes']
        );

        $formFile = cot_inputbox('file', 'rfile', $banner->ba_file);
        if(!empty($banner->ba_file)) $formFile .= cot_checkbox(false, 'rdel_rfile', $L['Delete']);

        $tpl->assign(array(
            'FORM_ID' => $banner->ba_id,
            'FORM_TITLE' => cot_inputbox('text', 'rtitle', $banner->ba_title, array('size' => '20',
                                                                                        'maxlength' => '32')),
            'FORM_CATEGORY' => cot_selectbox_structure('banners', $banner->ba_cat, 'rcat', '', false, false),
            'FORM_TYPE' => cot_selectbox($banner->ba_type, 'rtype', array_keys($types), array_values($types), false),
            'FORM_FILE' => $formFile,
            'FORM_WIDTH' => cot_inputbox('text', 'rwidth', $banner->ba_width),
            'FORM_HEIGHT' => cot_inputbox('text', 'rheight', $banner->ba_height),
            'FORM_ALT' => cot_inputbox('text', 'ralt', $banner->alt),
            'FORM_CUSTOMCODE' => cot_textarea('rcustomcode', $banner->ba_customcode, 5, 60),
            'FORM_CLICKURL' => cot_inputbox('text', 'rclickurl', $banner->ba_clickurl),
            'FORM_DESCRIPTION' => cot_textarea('rdescription', $banner->ba_description, 5, 60),
            'FORM_STICKY' => cot_radiobox( $banner->ba_sticky, 'rsticky', array(1, 0), array($L['Yes'], $L['No'])),
            'FORM_PUBLISH_UP' => cot_selectbox_date(cot_date2stamp($banner->ba_publish_up, 'auto'), 'long', 'rpublish_up'),
            'FORM_PUBLISH_DOWN' => cot_selectbox_date(cot_date2stamp($banner->ba_publish_down, 'auto'), 'long', 'rpublish_down'),
            'FORM_IMPTOTAL' => cot_inputbox('text', 'rimptotal', $banner->ba_imptotal),
            'FORM_IMPMADE' => cot_inputbox('text', 'rimpmade', $banner->ba_impmade),
            'FORM_CLICKS' => cot_inputbox('text', 'rclicks', $banner->ba_clicks),
            'FORM_CLIENT_ID' => cot_selectbox($banner->bac_id, 'rbac_id', array_keys($clients), array_values($clients), true),
            'FORM_PURCHASE_TYPE' => cot_selectbox($banner->ba_purchase_type, 'rpurchase_type', array_keys($purchase),
                                                                                          array_values($purchase), false),
            'FORM_TRACK_IMP' => cot_selectbox($banner->ba_track_impressions, 'rtrack_impressions', array_keys($track),
                                                                                        array_values($track), false),
            'FORM_TRACK_CLICKS' => cot_selectbox($banner->ba_track_clicks, 'rtrack_clicks', array_keys($track),
                                                                                        array_values($track), false),

            'FORM_PUBLISHED' => cot_radiobox( isset($banner->ba_published) ? $banner->ba_published : 1,
                                                        'rpublished', array(1, 0), array($L['Yes'], $L['No'])),
            'FORM_DELETE_URL' => $delUrl,
        ));
        if(!empty($banner->ba_file)){
            if($banner->ba_type ==  BaBanner::TYPE_IMAGE){
                // расчитаем размеры картинки:
                $w = $banner->ba_width;
                $h = $banner->ba_height;
                if($h > 100){
                    $k = $w / $h;
                    $h = 100;
                    $w = intval($h * $k);
                }
                $image = cot_rc('banner_image_admin', array(
                    'file' => $banner->ba_file,
                    'alt' => $banner->ba_alt,
                    'width' => $w,
                    'height' => $h
                ));
                $tpl->assign(array(
                    'BANNER_IMAGE' => cot_rc('admin_banner', array(
                        'banner' => $image
                    ))
                ));
            }elseif($banner->ba_type ==  BaBanner::TYPE_FLASH){
                $w = $banner->ba_width;
                $h = $banner->ba_height;
                if($h > 100){
                    $k = $w / $h;
                    $h = 100;
                    $w = intval($h * $k);
                }
                $image = cot_rc('banner_flash_admin', array(
                    'file' => $banner->ba_file,
                    'width' => $w,
                    'height' => $h
                ));
                $tpl->assign(array(
                    'BANNER_IMAGE' => cot_rc('admin_banner', array(
                        'banner' => $image
                    ))
                ));
            }
        }


        if(!empty($structure['banners'])) $tpl->parse('EDIT.FORM');

        $tpl->assign(array(
            'PAGE_TITLE' => isset($banner->ba_id) ? $L['ba_banner_edit'].": ".htmlspecialchars($banner->ba_title) :
                $L['ba_banner_new'],
        ));
        $tpl->parse('EDIT');
        return $tpl->text('EDIT');
    }
}