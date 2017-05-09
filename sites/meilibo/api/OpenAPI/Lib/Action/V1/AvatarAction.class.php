<?php
/**
 * Created by PhpStorm.
 * User: shellvon
 * Date: 16/4/24
 * Time: 下午2:38.
 */
class AvatarAction extends BaseAction
{
    protected $default_msg = array(
        'category' => 'avatar_api',
        'ref' => '头像相关API',
        'links' => array(
            'avatar_upload_api' => array(
                'href' => 'v1/avatar/upload',
                'ref' => '上传头像',
                'method' => 'POST',
                'parameters' => array('token' => 'string, required', 'file' => 'File Object,support jpg/png'),
            ),
        ),
    );

    public function upload()
    {
        $this->responseError(L('_AVATAR_UPLOAD_'));
    }
}
