<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UtilModel
 *
 * @author David
 */

class Util
{

    /**
     * 随机字符串
     * @param type $length
     * @return type
     */
    public static function createRandomString($length = 8)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str.= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 密码MD5加密
     * @param type $password
     * @param type $password_salt
     * @return type
     */
    public static function passwordMd5($password, $password_salt)
    {
        return md5($password_salt . $password . $password_salt);
    }
    
    public static function openid($prefix = '')
    {
        return md5(uniqid($prefix));
    }

    /**
     * 数组转键值映射
     * @param array $data
     * @param string $key
     * @return array
     */
    public static function hash($data, $key)
    {
        $hash = array();
        foreach ($data as $row) {
            $hash[$row[$key]] = $row;
        }
        return $hash;
    }

}
