<?php
namespace app\api\behavior;

use app\common\Request;

/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 16/11/22
 * Time: 下午11:10
 */
class UserProfile
{

    public function updateBefore(&$params)
    {
        $user_id = $params['user_id'];
        $profile = model('user_profile')->where('user_id', $user_id)->find();
        if($profile){
            $params['id'] = $profile['id'];
        }
    }

}