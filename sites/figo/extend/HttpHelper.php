<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HttpHelper
 *
 * @author David
 */

class HttpHelper
{

    /**
     * 异步请求
     * @param string $url
     * @return boolean
     */
    public function asyn($url)
    {
        $url_data = parse_url($url);
        $fp = fsockopen($url_data['host'], 80, $errno, $errstr, 30);
        $get_path = $url_data['path'] . '?' . $url_data['query'];
        if (!$fp) {
            return false;
        } else {
            $out = "GET {$get_path}  / HTTP/1.1\r\n";
            $out .= "Host: {$url_data['host']}\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);
            fclose($fp);
        }
    }

    /**
     * 发起一次GET请求
     * @param string $url
     * @return string
     */
    public function get($url)
    {
        return $this->http($url, array());
    }

    /**
     * 发起一次post请求
     * @param string $url
     * @param array $params
     * @return string
     */
    public function post($url, $params)
    {
        return $this->http($url, $params, 'POST');
    }

    /**
     * 发起一次http请求
     * @param type $url
     * @param type $params
     * @param type $method
     * @param type $header
     * @param type $multi
     * @return string
     */
    public function http($url, $params, $method = 'GET', $header = array(), $multi = false)
    {
        $opts = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $header
        );

        /* 根据请求类型设置特定参数 */
        switch (strtoupper($method)) {
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
//                throw new Exception('不支持的请求方式！');
                return '';
        }

        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
