<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Qiniu
 * 七牛操作类
 * @author David
 */

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
require_once ROOT_PATH . '/extend/Qiniu/autoload.php';

class Qiniu
{

    public $access_key = '';
    public $secret_key = '';
    public $bucket = '';
    public $domain = '';

    public function __construct()
    {
        $this->config();
    }

    /**
     * 
     * @param string $access_key
     * @param string $secret_key
     * @return \Qiniu\Auth
     */
    public function getAuth($access_key, $secret_key)
    {
        return new Auth($access_key, $secret_key);
    }

    /**
     * 
     * @param string $access_key
     * @param string $secret_key
     * @param string $bucket
     * @return type
     */
    public function getUploadToken($access_key = null, $secret_key = null, $bucket = null)
    {
        is_null($access_key) && $access_key = $this->access_key;
        is_null($secret_key) && $secret_key = $this->secret_key;
        is_null($bucket) && $bucket = $this->bucket;
        $Auth = $this->getAuth($access_key, $secret_key);
        return $Auth->uploadToken($bucket);
    }

    /**
     * 配置
     * @param array $config
     */
    public function config($config = array())
    {
        empty($config) && $config = \think\Config::get('qiniu');
        $this->access_key = $config['ACCESS_KEY'];
        $this->secret_key = $config['SECRET_KEY'];
        $this->bucket = $config['BUCKET'];
        $this->domain = $config['DOMAIN'];
    }

    /**
     * 文件上传
     * @param string $filepath
     * @return mixed|boolean
     */
    public function upload($filepath)
    {
        $token = $this->getUploadToken($this->access_key, $this->secret_key, $this->bucket);
        $UploadManager = new UploadManager();
        $file_content = @file_get_contents($filepath);
        list($ret, $err) = $UploadManager->put($token, null, $file_content);
        if ($err !== null) {
            return false;
        } else {
            return $ret['key'];
        }
    }
    
    /**
     * 文件内容上传
     * @param type $file_content
     * @return boolean
     */
    public function upload_file_content($file_content)
    {
        $token = $this->getUploadToken($this->access_key, $this->secret_key, $this->bucket);
        $UploadManager = new UploadManager();
        list($ret, $err) = $UploadManager->put($token, null, $file_content);
        if ($err !== null) {
            return false;
        } else {
            return $ret['key'];
        }
    }

    /**
     * 获取文件外网访问连接
     * @param string $key
     * @return string
     */
    public function getUrl($key)
    {
        return 'http://' . $this->domain . '/' . $key;
    }

    /**
     * 获取七牛上传token
     * @param string $access_key
     * @param string $secret_key
     * @param string $bucket
     */
    public function getAppUploadToken($access_key = null, $secret_key = null, $bucket = null)
    {
        is_null($access_key) && $access_key = $this->access_key;
        is_null($secret_key) && $secret_key = $this->secret_key;
        is_null($bucket) && $bucket = $this->bucket;
        require_once(EXTEND_PATH . "qn/app/rs.php");
        Qiniu_SetKeys($access_key, $secret_key);
        $putPolicy = new \Qiniu_RS_PutPolicy($bucket);
        $upToken = $putPolicy->Token(null);
        return $upToken;
    }

    /**
     * 缩略图
     * @param string $url
     * @param int $width
     * @param int $height
     * @param int $mode 0等比，1剪切
     * @return string
     */
    function getThumbImg($url, $width = NULL, $height = NULL, $mode = 1)
    {
        $size = '';
        $width && $size .= "w/{$width}";
        if ($height) {
            $width && $size.='/';
            $size .= "h/{$height}";
        }
        return $url . "?imageView/{$mode}/{$size}";
    }

}
