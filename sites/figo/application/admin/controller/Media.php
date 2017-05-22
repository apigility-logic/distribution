<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UploadController
 * 文件控制器
 * @author David
 */

namespace app\admin\controller;

use qn\qn;
use think\Controller;

class Media extends Controller
{

    public $base_url;
    public $upload_path;

    public function __construct()
    {
        parent::__construct();
    }

    public function image()
    {
        $width = request()->param('width', 200);
        $height = request()->param('height', 200);
        $file = request()->file('image');
        // 移动到框架应用根目录/public/upload/ 目录下
        $update_path = ROOT_PATH . 'public' . DS . 'upload';
        $image_path = '/image';
        $info = $file->validate(['size'=>8 * 1024 * 1024,'ext'=>'jpg,png,gif,jpeg'])->move($update_path . $image_path);
        if (!$info) {// 上传错误提示错误信息
            $data = [
                'msg' => $file->getError(),
                'code' => 0,
            ];
        } else {// 上传成功
            $mime_type = $info->getMime();
            $save_name = $info->getSaveName();
            $filename = $info->getFilename();
            $size = $info->getSize();
            $path = $image_path . '/' . $save_name;
            $media_data = array(
                'filename' => $filename,
                'filepath' => $path,
                'filesize' => $size,
                'mime_type' => $mime_type,
                'remote_url' => '',
            );
            if(\app\common\Media::$is_upload_qiniu){
                $Qiniu = new \Qiniu();
                $key = $Qiniu->upload($update_path . $path);
                if ($key) {
                    $fileurl = $Qiniu->getUrl($key);
                    $data = [
                        'data' => [
                            'thumb' => $Qiniu->getThumbImg($fileurl, $width, $height),
                            'filepath' => $fileurl,
                            'savepath' => $fileurl
                        ],
                        'code' => 1,
                    ];
                    $media_data['remote_url'] = $fileurl;
                } else {
                    $data = array(
                        'msg' => '上传七牛失败',
                        'code' => 0
                    );
                }
            } else {
                $thumb = \app\common\Media::thumb($image_path . '/' . $save_name, 200, 200);
                $data = array(
                    'data' => array(
                        'thumb' => \app\common\Media::getUrl($thumb),
                        'filepath' => \app\common\Media::getUrl($path),
                        'savepath' => $path,
                    ),
                    'code' => 1
                );
            }
            $MediaLogic = new \app\common\Media();
            $MediaLogic->add($media_data);
        }
        return json_encode($data);
    }

    public function video()
    {
        $Upload = new \Think\Upload();
        $Upload->exts = array(
            'avi', 'rmvb', 'rm', 'asf', 'divx', 'mpg',
            'mpeg', 'mpe', 'wmv', 'mp4', 'mkv', 'vob'
        );
        $Upload->maxSize = 200 * 1024 * 1024; //最大200M
        $Upload->savePath = $this->upload_path . '/Video/';
        // 上传文件 
        $info = $Upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $data = array(
                'info' => $Upload->getError(),
                'status' => 0
            );
        } else {// 上传成功
            $file_info = array();
            foreach ($info as $row) {
                $file_info = $row;
                break;
            }
            $path = $file_info['savepath'] . $file_info['savename'];
            $data = array(
                'filepath' => $this->base_url . $path,
                'filesize' => $file_info['size'],
                'status' => 1
            );
        }
        $this->ajaxReturn($data);
    }

}
