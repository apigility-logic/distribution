<?php
namespace app\api\behavior;

use app\common\Request;

/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 16/11/22
 * Time: 下午11:10
 */
class UserAddress
{

    public function create(&$params)
    {
        $is_default = request()->param('is_default');
        $user_id = Request::instance()->getUserId();
        if ($is_default && isset($params['id'])) {
            $where = [
                'id' => ['<>', $params['id']],
                'user_id' => $user_id
            ];
            model('user_address')->save(['is_default' => 0], $where);
        }
    }

    public function update(&$params)
    {
        $is_default = request()->param('is_default');
        $user_id = Request::instance()->getUserId();
        $id = request()->param('id');
        if ($is_default && $id && isset($params['err_code']) && $params['err_code'] == 0) {
            $where = [
                'id' => ['<>', $id],
                'user_id' => $user_id
            ];
            model('user_address')->save(['is_default' => 0], $where);
        }
    }

    public function lists(&$params)
    {
        foreach($params['list'] as $row) {
            $province = model('district')->title($row['province_id']);
            $city = model('district')->title($row['city_id']);
            $area = model('district')->title($row['area_id']);
            $row['address'] = $province . $city . $area . $row['street'];
        }
    }

}