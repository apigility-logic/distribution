<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\api\controller;

class Qiniu extends Base
{
    public function token()
    {
        $Qiniu = new \Qiniu();
        $token = $Qiniu->getUploadToken();
        return [
            'domain' => 'http://' . $Qiniu->domain,
            'token' => $token,
            'expire_time' => time() + 3000
        ];
    }

}