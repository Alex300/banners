<?php
(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

/**
 * Main Admin Controller class for the tracks
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */
class TrackController{

    /**
     * основная статистика кликов и показов
     */
    public function indexAction(){
        global $L, $adminpath, $cfg, $sys, $db_ba_banners, $db_ba_clients, $db_ba_tracks, $db, $structure;

        $adminpath[] = '&nbsp;'.$L['ba_tracks'];

        $sortFields = array(
            array('ba_title', $L['Title'] ),
            array('ba_cat', $L['Category']),
            array('cl.bac_id', $L['ba_client']),
            array('track_type', $L['Type']),
            array('track_count', $L['Count']),
            array('track_date', $L['Date']),
        );

        $so = cot_import('so', 'G', 'ALP'); // order field name without 'ba_'
        $w = cot_import('w', 'G', 'ALP', 4); // order way (asc, desc)
        $fil = cot_import('fil', 'G', 'ARR');  // filters
        $fil['date_from'] = cot_import_date('fil_df', true, false, 'G');
        $fil['date_to'] = cot_import_date('fil_dt', true, false, 'G');

        $maxrowsperpage = $cfg['maxrowsperpage'];
        list($pg, $d, $durl) = cot_import_pagenav('d', $maxrowsperpage); //page number for banners list

        $list_url_path = array('m' => 'other', 'p'=>'banners', 'n'=>'track');
        if(empty($so)){
            $so = 'track_date';
        }else{
            $list_url_path['so'] = $so;
        }
        if(empty($w)){
            $w = 'DESC';
        }else{
            $list_url_path['w'] = $w;
        }

        $where = array();
        $params = array();

        if (!empty($fil)){
            foreach($fil as $key => $val){
                $val = trim(cot_import($val, 'D', 'TXT'));
                if(empty($val) && $val !== '0') continue;
                if(in_array($key, array('title') )){
                    $params[$key] = "%{$val}%";
                    $where['filter'][] = "ba_title LIKE :$key";
                    $list_url_path["fil[{$key}]"] = $val;
                }elseif($key == 'date_from'){
                    if($fil[$key] == 0) continue;
                    $where['filter'][] = "a.track_date >= '".date('Y-m-d H:i:s', $fil[$key])."'";
                    $list_url_path["fil_df[year]"] = cot_date('Y', $fil[$key]);
                    $list_url_path["fil_df[month]"] = cot_date('m', $fil[$key]);
                    $list_url_path["fil_df[day]"] = cot_date('d', $fil[$key]);
                }elseif($key == 'date_to'){
                    if($fil[$key] == 0) continue;
                    $where['filter'][] = "a.track_date <= '".date('Y-m-d H:i:s', $fil[$key])."'";
                    $list_url_path["fil_dt[year]"] = cot_date('Y', $fil[$key]);
                    $list_url_path["fil_dt[month]"] = cot_date('m', $fil[$key]);
                    $list_url_path["fil_dt[day]"] = cot_date('d', $fil[$key]);
                }else{
                    $kkey = str_replace('.', '_', $key);
                    $params[$kkey] = $val;
                    $where['filter'][] = "$key = :$kkey";
                    $list_url_path["fil[{$key}]"] = $val;
                }
            }
            empty($where['filter']) || $where['filter'] = implode(' AND ', $where['filter']);
        }else{
            $fil = array();
        }

        $orderby = "$so $w";

        $where = array_filter($where);
        $where = ($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT a.track_date , a.track_type , a.track_count, a.ba_id, b.ba_title, b.ba_cat, cl.bac_title
            FROM $db_ba_tracks AS a
            LEFT JOIN $db_ba_banners AS b ON b.ba_id=a.ba_id
            LEFT JOIN $db_ba_clients AS cl ON cl.bac_id=b.bac_id
            $where ORDER BY $orderby LIMIT {$d}, {$maxrowsperpage}";

        $sqlCount = "SELECT COUNT(*)
            FROM $db_ba_tracks AS a
            LEFT JOIN $db_ba_banners AS b ON b.ba_id=a.ba_id
            LEFT JOIN $db_ba_clients AS cl ON cl.bac_id=b.bac_id
            $where";

        $totallines = $db->query($sqlCount, $params)->fetchColumn();
        $sqllist = $db->query($sql, $params);

        $pagenav = cot_pagenav('admin', $list_url_path, $d, $totallines, $maxrowsperpage);

        $types = array(
            1 => $L['ba_impressions'],
            2 => $L['ba_clicks']
        );

        $tpl = new XTemplate(cot_tplfile('banners.admin.track', 'plug'));

        $list = $sqllist->fetchAll();

        if($list){
            $i = $d+1;
            foreach ($list as $item){
                $time = cot_date2stamp($item['track_date'], 'auto');
                $tpl->assign(array(
                    'LIST_ROW_NUM' => $i,
                    'LIST_ROW_TITLE' => htmlspecialchars($item['ba_title']),
                    'LIST_ROW_EDIT_URL' => cot_url('admin', array('m'=>'other', 'p'=>'banners', 'a'=>'edit',
                                                                  'id'=>$item['ba_id'])),
                    'LIST_ROW_CATEGORY' => $item['ba_cat'],
                    'LIST_ROW_CATEGORY_TITLE' => htmlspecialchars($structure['banners'][$item['ba_cat']]['title']),
                    'LIST_ROW_CLIENT_TITLE' => htmlspecialchars($item['bac_title']),
                    'LIST_ROW_TRACK_TYPE' => $item['track_type'],
                    'LIST_ROW_TRACK_TYPE_TEXT' => $types[$item['track_type']],
                    'LIST_ROW_TRACK_COUNT' => $item['track_count'],
                    'LIST_ROW_TRACK_DATE' => cot_date('datetime_medium', $time),
                ));
                $i++;
                $tpl->parse('MAIN.LIST_ROW');
            }
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
            'FILTER_TRACK_TYPE' => cot_selectbox($fil['track_type'], 'fil[track_type]',  array_keys($types),
                array_values($types)),
            'FILTER_CLIENT' => cot_selectbox($fil['b.bac_id'], 'fil[b.bac_id]',  array_keys($clients),
                    array_values($clients)),
            'FILTER_CATEGORY' => ba_selectbox_structure('banners', $fil['ba_cat'], 'fil[ba_cat]', '', false, false, true),
            'FILTER_DATE_FROM' => cot_selectbox_date($fil['date_from'], 'short', 'fil_df'),
            'FILTER_DATE_TO' => cot_selectbox_date($fil['date_to'], 'short', 'fil_dt'),
            'FILTER_VALUES' => $fil

        ));

        $tpl->assign(array(

            'PAGE_TITLE' => $L['ba_tracks'],

        ));
        $tpl->parse('MAIN');
        return $tpl->text();

    }

    public function clearAction(){
        global $L, $cfg, $db_ba_banners, $db_ba_clients, $db_ba_tracks, $db;

        $so = cot_import('so', 'G', 'ALP'); // order field name without 'ba_'
        $w = cot_import('w', 'G', 'ALP', 4); // order way (asc, desc)

        $fil = cot_import('fil', 'G', 'ARR');  // filters
        $fil['date_from'] = cot_import_date('fil_df', true, false, 'G');
        $fil['date_to'] = cot_import_date('fil_dt', true, false, 'G');

        $list_url_path = array('m' => 'other', 'p'=>'banners', 'n'=>'track');
        if(empty($so)){
            $so = 'track_date';
        }else{
            $list_url_path['so'] = $so;
        }
        if(empty($w)){
            $w = 'DESC';
        }else{
            $list_url_path['w'] = $w;
        }

        $where = array();
        $params = array();
        $baWhere = array();

        if (!empty($fil)){
            foreach($fil as $key => $val){
                $val = trim(cot_import($val, 'D', 'TXT'));
                if(empty($val) && $val !== '0') continue;
                if(in_array($key, array('title') )){
                    $params[$key] = "%{$val}%";
                    $baWhere[] = "b.ba_title LIKE :$key";
                    $list_url_path["fil[{$key}]"] = $val;
                }elseif($key == 'date_from'){
                    if($fil[$key] == 0) continue;
                    $where['filter'][] = "track_date >= '".date('Y-m-d H:i:s', $fil[$key])."'";
                    $list_url_path["fil_df[year]"] = cot_date('Y', $fil[$key]);
                    $list_url_path["fil_df[month]"] = cot_date('m', $fil[$key]);
                    $list_url_path["fil_df[day]"] = cot_date('d', $fil[$key]);
                }elseif($key == 'date_to'){
                    if($fil[$key] == 0) continue;
                    $where['filter'][] = "track_date <= '".date('Y-m-d H:i:s', $fil[$key])."'";
                    $list_url_path["fil_dt[year]"] = cot_date('Y', $fil[$key]);
                    $list_url_path["fil_dt[month]"] = cot_date('m', $fil[$key]);
                    $list_url_path["fil_dt[day]"] = cot_date('d', $fil[$key]);
                }else{
                    $kkey = str_replace('.', '_', $key);
                    $params[$kkey] = $val;
                    if(mb_strpos($key, 'b.') === 0){
                        $baWhere[] = "$key = :$kkey";
                    }else{
                        $where['filter'][] = "$key = :$kkey";
                    }
                    $list_url_path["fil[{$key}]"] = $val;
                }
            }
            empty($where['filter']) || $where['filter'] = implode(' AND ', $where['filter']);
        }else{
            $fil = array();
        }

        if(!empty($baWhere)){
            $where['banners'] = "ba_id IN (SELECT b.ba_id FROM $db_ba_banners AS b WHERE ".implode(' AND ', $baWhere)." )";
        }

        $where = implode(' AND ', $where);

        $res = $db->delete($db_ba_tracks, $where, $params);

        if($res > 0){
            cot_message(sprintf($L['ba_deleted_records'], $res));
        }else{
            cot_message($L['ba_deleted_no']);
        }

        cot_redirect(cot_url('admin', $list_url_path, '', true));
    }

}