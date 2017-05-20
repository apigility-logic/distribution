<?php
namespace app\common\model;

use think\Model;

class District extends Model
{
    public function lists()
    {
        static $lists = [];
        if (empty($lists)) {
            $data = $this->where('level', '<', 4)->select();
            $lists = \ArrayHelper::hash($data, 'id');
        }
        return $lists;
    }

    public function child($pid)
    {
        $lists = $this->lists();
        $child = [];
        foreach ($lists as $data) {
            if ($data['parentid'] == $pid) {
                $child[] = $data;
            }
        }
        return $child;
    }

    public function title($id)
    {
        $lists = $this->lists();
        return isset($lists[$id]) ? $lists[$id]['title'] : '';
    }
}