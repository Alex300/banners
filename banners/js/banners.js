/**
 * Cotonti Plugin Banners
 * Banner rotation plugin with statistics
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */

$(function() {
    var bids = new Array();
    $('div.banner-loading').each(function(){
        var id = $(this).attr('id');
        id = parseInt(id.replace('banner_', ''));
        if(id > 0) bids.push(id);
    });

    if(bids.length > 0){
        $.post('index.php?e=banners&a=ajxLoad', {bids: bids, x : bannerx}, function(data){
            if(data.error != ''){
                alert(data.error)
            }else{
                $.each(data.banners, function(index, value) {
                    $('div#banner_'+index).html(value);
                });
            }
        }, 'json');
    }
});