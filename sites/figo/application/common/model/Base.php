<?php
namespace app\common\model;

use think\Model;

class Base extends Model
{
    protected static $fields = [];
    public $keyword = null;

    public static function getFields($table)
    {
        return isset(self::$fields[$table]) ? self::$fields[$table] : '*';
    }

    public static function setFields($fields)
    {
        self::$fields = $fields;
    }

    public static function getLabel()
    {
        return [
            'id' => 'ID',
            'update_time' => '更新时间',
            'create_time' => '创建时间',
            'delete_time' => '删除时间',
        ];
    }
}