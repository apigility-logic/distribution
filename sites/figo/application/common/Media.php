<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MediaLogic
 *
 * @author David
 */
namespace app\common;

//use think\Image;

use qn\qn;

class Media
{
    public static $upload = ROOT_PATH . 'upload';
    public static $base = 'http://localhost/figo/mb2/sites/figo/public/upload';
    public static $is_upload_qiniu = true;

    //put your code here
    public function add($data)
    {
        $data['create_time'] = time();
        return model('media')->isUpdate(false)->save($data);
    }

    public static function getUrl($path)
    {
        if(false === strpos($path, 'http://')) {
            return self::$base . $path;
        }
        return $path;
    }

    public static function thumb($path, $width, $height/*, $type = Image::IMAGE_THUMB_CENTER*/)
    {
        if(false === strpos($path, 'http://')){
            return $path;
        } else {
            $Qiniu = new qn();
            return $Qiniu->getThumbImg($path, $width, $height);
        }

//        $pathinfo = pathinfo($path);
//        $thumb_path = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $pathinfo['filename'] . "-{$width}-{$height}." . $pathinfo['extension'];
//        if(!is_file(self::$upload . $thumb_path) && is_file(self::$upload . $path)){
//            $image = new Image();
//            $image->open(APP_UPLOAD . $path);
//            $image->thumb($width, $height, $type)->save(self::$upload . $thumb_path);
//        }
//        return $thumb_path;
    }

}
