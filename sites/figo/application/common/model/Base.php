<?php
namespace app\common\model;

use think\Model;
use traits\model\SoftDelete;

class Base extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    public static function getLabel()
    {
        return [
            'id' => 'ID',
            'create_time' => '创建时间',
            'delete_time' => '删除时间',
        ];
    }
}