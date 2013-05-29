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
        $ret = array(
            'error' => ''
        );

        $bids = cot_import('bids', 'P', 'ARR');
        if(!$bids){
            $ret['error'] = 'Nothing to load';
            echo json_encode($ret);
            exit;
        }
        $ret['banners'] = array();
        foreach($bids as $key => $bid){
            $bid = (int)$bid;
            if($bid > 0){
                $ret['banners'][$bid] = '';
            }else{
                unset($bids[$key]);
            }
        }
        $cond = array(
            array('ba_id', $bids)
        );
        $banners = BaBanner::find($cond);
        if(!$banners){
            echo json_encode($ret);
            exit;
        }

        foreach ($banners as $banner){
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
                        $ret['banners'][$banner->ba_id] = cot_rc('banner', array(
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
                        $ret['banners'][$banner->ba_id] = cot_rc('banner', array(
                                'banner' => $image
                            ));
                    }
                    break;

                case BaBanner::TYPE_CUSTOM:
                    $ret['banners'][$banner->ba_id] = cot_rc('banner', array(
                            'banner' => $banner->ba_customcode
                        ));
                    break;
            }
        }

        echo json_encode($ret);
        exit;
    }

}