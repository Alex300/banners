<?php
defined('COT_CODE') or die('Wrong URL.');

/**
 * Main Controller class for the Banners
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */
class MainController{

    public function clickAction(){
        global $cfg;

        $id = cot_import('id', 'G', 'INT');
        $banner = BaBanner::getById($id);
        if(!$banner) cot_diefatal('banner not found');

        $banner->click();

        if(!empty($banner->ba_clickurl)) header('Location: '.$banner->ba_clickurl);

        exit();
    }

    /**
     * Вывод баненров ajax
     */
    public function ajxLoadAction(){
        global $sys;

        $ret = array(
            'error' => ''
        );

        $cats = cot_import('cats', 'P', 'ARR');
        if(!$cats){
            $ret['error'] = 'Nothing to load';
            echo json_encode($ret);
            exit;
        }
        // Пока выбыраем баненры по одному,
        // @todo оптимизировать
        // @todo учесть $client, $order
        $client = false;
        $order = 'order';
        $cond = array(
            array('ba_published', 1),
            "ba_publish_up <='".date('Y-m-d H:i:s', $sys['now'])."'",
            array('RAW', "ba_publish_down >='".date('Y-m-d H:i:s', $sys['now'])."' OR ba_publish_down ='0000-00-00 00:00:00'"),
            array('RAW', "ba_imptotal = 0 OR ba_impmade < ba_imptotal"),
        );
        $cnt = 0;
        foreach($cats as $pid => $cat){
            $pid = (int)$pid;
            $cat = cot_import($cat, 'D', 'TXT');

            if($pid == 0) continue;
            if(empty($cat)){
                $ret['banners'][$pid] = '';
                continue;
            }

            $cond['category'] = array('ba_cat', $cat);

            if($client){
                $cond['client'] = array('bac_id', $client);
            }
            $ord = "ba_lastimp ASC";
            if($order == 'rand') $ord = 'RAND()';

            $banner = BaBanner::find($cond, 1, 0, $ord);
            if(empty($banner)){
                $ret['banners'][$pid] = '';
                continue;
            }
            $banner = $banner[0];
            $banner->impress();

            $url = cot_url('banners', 'a=click&id='.$banner->ba_id);
            switch($banner->ba_type){

                case BaBanner::TYPE_IMAGE:
                    if(!empty($banner->ba_file)){
                        $image = cot_rc('banner_image', array(
                            'file' => $banner->ba_file,
                            'alt' => $banner->ba_alt,
                            'width' => $banner->ba_width,
                            'height' => $banner->ba_height
                        ));
                        if(!empty($banner->ba_clickurl)){
                            $image = cot_rc_link($url, $image, array('target' => '_blank'));
                        }
                        $ret['banners'][$pid] = cot_rc('banner', array(
                            'banner' => $image
                        ));
                    }
                    break;

                case BaBanner::TYPE_FLASH:
                    if(!empty($banner->ba_file)){
                        $image = cot_rc('banner_flash', array(
                            'file' => $banner->ba_file,
                            'width' => $banner->ba_width,
                            'height' => $banner->ba_height
                        ));
                        if(!empty($banner->ba_clickurl)){
                            $image = cot_rc_link($url, $image, array('target' => '_blank'));
                        }
                        $ret['banners'][$pid] = cot_rc('banner', array(
                            'banner' => $image
                        ));
                    }
                    break;

                case BaBanner::TYPE_CUSTOM:
                    $ret['banners'][$pid] = cot_rc('banner', array(
                        'banner' => $banner->ba_customcode
                    ));
                    break;
            }

            $cnt++;
        }


        echo json_encode($ret);
        exit;
    }

}