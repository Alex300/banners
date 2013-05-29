<?php
(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

/**
 * Clients Admin Controller class for the Banners
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */
class ClientsController{
    /**
     * Список
     */
    public function indexAction(){
        global $L, $adminpath, $cfg, $a, $sys;

        $adminpath[] = '&nbsp;'.$L['ba_clients'];

        $so = cot_import('so', 'G', 'ALP'); // order field name without 'bac_'
        $w = cot_import('w', 'G', 'ALP', 4); // order way (asc, desc)

        $maxrowsperpage = $cfg['maxrowsperpage'];
        list($pg, $d, $durl) = cot_import_pagenav('d', $maxrowsperpage); //page number for clients list

        $list_url_path = array('m' => 'other', 'p'=>'banners', 'n' => 'clients');
        if(empty($so)){
            $so = 'bac_title';
        }else{
            $list_url_path['so'] = $so;
        }
        if(empty($w)){
            $w = 'ASC';
        }else{
            $list_url_path['w'] = $w;
        }

        $cond = array();

        $list = BaClient::find($cond, $maxrowsperpage, $d, $so.' '.$w);
        if(!$list) $list = array();
        $totallines = BaClient::count($cond);

        $pagenav = cot_pagenav('admin', $list_url_path, $d, $totallines, $maxrowsperpage);

        $act = cot_import('act', 'G', 'ALP');
        if($act == 'delete'){
            $urlArr = $list_url_path;
            if($pagenav['current'] > 0) $urlArr['d'] = $pagenav['current'];
            $id = cot_import('id', 'G', 'INT');
            cot_check_xg();
            $item = BaClient::getById($id);
            if (!$item){
                cot_error($L['No_items']." id# ".$id);
                cot_redirect(cot_url('admin', $urlArr, '', true));
            }
            $item->delete();
            cot_message($L['alreadydeletednewentry']." # $id - {$item->bac_title}");
            cot_redirect(cot_url('admin', $urlArr, '', true));

        }

        $tpl = new XTemplate(cot_tplfile('banners.admin.clients', 'plug'));

        $i = $d+1;
        foreach ($list as $item){
            $tpl->assign(BaClient::generateTags($item, 'LIST_ROW_'));
            $delUrlArr = $list_url_path;
            $delUrlArr['act'] = 'delete';
            $delUrlArr['id'] = $item->bac_id;
            if($pagenav['current'] > 0) $delUrlArr['d'] = $pagenav['current'];
            $delUrlArr['x'] = $sys['xk'];

            $tpl->assign(array(
                'LIST_ROW_NUM' => $i,
                'LIST_ROW_DELETE_URL' => cot_confirm_url(cot_url('admin', $delUrlArr), 'admin'),
            ));
            $i++;
            $tpl->parse('MAIN.LIST_ROW');
        }

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

        ));
        $tpl->parse('MAIN');
        return $tpl->text();

    }

    /**
     * Создание / редактирование клиента
     * @return string
     */
    public function editAction(){
        global $adminpath, $structure, $cfg,  $L, $usr, $sys;

        $adminpath[] = array(cot_url('admin', array('m' => 'other', 'p'=>'banners')), $L['ba_banners']);

        $id = cot_import('id', 'G', 'INT');

        $act = cot_import('act', 'P', 'ALP');
        if(!$id){
            $id = 0;
            $adminpath[] = '&nbsp;'.$L['Add'];
            $client = new BaClient;
        }else{
            $client = BaClient::getById($id);

            $adminpath[] = $L['ba_banner_edit'].": ".htmlspecialchars($client->bac_title);
        }

        if ($act == 'save'){
            $item = array();
            $item['bac_title'] = cot_import('rtitle', 'P', 'TXT');
            if(empty($item['bac_title'])){
                cot_error($L['ba_err_titleempty'], 'rtitle');
            }
            $item['bac_purchase_type'] = cot_import('rpurchase_type', 'P', 'INT');
            $item['bac_email'] = cot_import('remail', 'P', 'TXT');
            $item['bac_track_impressions'] = cot_import('rtrack_impressions', 'P', 'INT');
            $item['bac_track_clicks'] = cot_import('rtrack_clicks', 'P', 'INT');
            $item['bac_extrainfo'] = cot_import('rextrainfo', 'P', 'TXT');
            $item['bac_published'] = cot_import('rpublished', 'P', 'BOL');

            $client->setData($item);
            if(!cot_error_found()){
                if ($id = $client->save()){
                    cot_message($L['ba_saved']);
                }
                cot_redirect(cot_url('admin', array('m'=>'other', 'p'=>'banners', 'n'=>'clients', 'a'=>'edit', 'id'=>$id),
                        '', true));
            }
        }

        $tpl = new XTemplate(cot_tplfile('banners.admin.clients', 'plug'));

        $delUrl = '';
        if($client->bac_id > 0){
            $delUrl = cot_confirm_url(cot_url('admin', 'm=other&p=banners&n=clients&act=delete&id='.$client->bac_id.'&'.cot_xg()), 'admin');
        }

        $purchase = array(
            BaClient::PURCHASE_DEFAULT => $L['Default'],
            BaClient::PURCHASE_UNLIMITED => $L['ba_unlimited'],
            BaClient::PURCHASE_YEARLY => $L['ba_pt_yearly'],
            BaClient::PURCHASE_MONTHLY => $L['ba_pt_monthly'],
            BaClient::PURCHASE_WEEKLY => $L['ba_pt_weekly'],
            BaClient::PURCHASE_DAILY => $L['ba_pt_daily']
        );

        $track = array(
            -1 => $L['Default'],
            0 => $L['No'],
            1 => $L['Yes']
        );

        $tpl->assign(array(
            'FORM_ID' => $client->bac_id,
            'FORM_TITLE' => cot_inputbox('text', 'rtitle', $client->bac_title, array('size' => '20',
                                                                                        'maxlength' => '32')),
            'FORM_EMAIL' => cot_inputbox('text', 'remail', $client->bac_email),
            'FORM_PURCHASE_TYPE' => cot_selectbox($client->bac_purchase_type, 'rpurchase_type', array_keys($purchase),
                                                                                      array_values($purchase), false),
            'FORM_TRACK_IMP' => cot_selectbox($client->bac_track_impressions, 'rtrack_impressions', array_keys($track),
                                                    array_values($track), false),
            'FORM_TRACK_CLICKS' => cot_selectbox($client->bac_track_clicks, 'rtrack_clicks', array_keys($track),
                                                    array_values($track), false),
            'FORM_EXTRAINFO' => cot_textarea('rextrainfo', $client->bac_extrainfo, 5, 60),
            'FORM_PUBLISHED' => cot_radiobox( isset($client->published) ? $client->published : 1,
                                                        'rpublished', array(1, 0), array($L['Yes'], $L['No'])),
            'FORM_DELETE_URL' => $delUrl,
        ));

        $tpl->parse('EDIT.FORM');

        $tpl->assign(array(
            'PAGE_TITLE' => isset($client->bac_id) ? $L['ba_banner_edit'].": ".htmlspecialchars($client->bac_title) :
                $L['ba_client_new'],
        ));
        $tpl->parse('EDIT');
        return $tpl->text('EDIT');
    }
}