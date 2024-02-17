<?php

function utf8_decode_($str_){
    $str = [$str_];
    //print_r($str);
    if(count($str)>0){
        $result = array_map(function($item){
            if($item != null){
                return mb_convert_encoding($item, 'UTF-8', mb_detect_encoding($item));
            }else{
                return '';
            }
        }, $str);
        return iconv('UTF-8', 'windows-1252',implode('', $result));
    }else{
        return '';
    }
    
}

?>