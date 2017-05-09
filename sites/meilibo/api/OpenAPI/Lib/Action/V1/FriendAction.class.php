<?php

class FriendAction extends BaseAction
{
    protected $default_msg = array(
        'category' => 'friend_api',
        'ref' => '友情链接相关API',
        'links' => array(
            'friend' => array(
                'href' => 'v1/friend/getFriend',
                'ref' => '查询友情链接列表',
                'method' => 'POST',
                'parameters' => array(),
            ),
        ),
    );

    /**
    * 查询友情链接列表
    *  
    */
    public function getFriend()
    {
        $friend = M('friend')->field(" id, f_name, f_url, f_pic, f_addtime")->order(" f_sort asc ")->select();
        foreach( $friend as $key => $one_data){
            $friend[$key]['f_pic'] = "/style/Friend/".$one_data['f_pic'];
        }
        $this->responseSuccess($friend);
    }
}
