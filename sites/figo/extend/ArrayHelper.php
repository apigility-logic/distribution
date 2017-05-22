<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ArrayHelper
 *
 * @author David
 */

class ArrayHelper
{

    public function __construct()
    {
        
    }

    /**
     * 将二维数组的key对应的值作为数组首层的key值，便于快速搜索
     * @param array $data
     * @param string $key
     * @return array
     */
    public static function hash($data, $key, $multi = false)
    {
        $hash = array();
        foreach ($data as $row) {
            if ($multi) {
                $hash[$row[$key]][] = $row;
            } else {
                $hash[$row[$key]] = $row;
            }
        }
        return $hash;
    }

    /**
     * 将二维数组的key对应的值取出放入新数组
     * @param type $data
     * @param type $key
     */
    public static function extract_value($data, $key, $condition = array())
    {
        $arr = array();
        foreach ($data as $row) {
            $flag = true;
            if ($condition) {
                foreach ($condition as $k => $v) {
                    if ($row[$k] != $v) {
                        $flag = false;
                    }
                }
            }
            if ($flag) {
                $arr[] = $row[$key];
            }
        }
        return $arr;
    }

    public static function options($data, $value_key, $name_key)
    {
        $options = array();
        foreach ($data as $row) {
            $options[] = array(
                'value' => $row[$value_key],
                'name' => $row[$name_key]
            );
        }
        return $options;
    }

    /**
     * Merges two or more arrays into one recursively.
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from array_merge_recursive).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array.
     * @param array $a array to be merged to
     * @param array $b array to be merged from. You can specify additional
     * arrays via third argument, fourth argument etc.
     * @return array the merged array (the original arrays are not changed.)
     */
    public static function merge($a, $b)
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (is_integer($k)) {
                    if (isset($res[$k])) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::merge($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    /**
     * Retrieves the value of an array element or object property with the given key or property name.
     * If the key does not exist in the array or object, the default value will be returned instead.
     *
     * The key may be specified in a dot format to retrieve the value of a sub-array or the property
     * of an embedded object. In particular, if the key is `x.y.z`, then the returned value would
     * be `$array['x']['y']['z']` or `$array->x->y->z` (if `$array` is an object). If `$array['x']`
     * or `$array->x` is neither an array nor an object, the default value will be returned.
     * Note that if the array already has an element `x.y.z`, then its value will be returned
     * instead of going through the sub-arrays.
     *
     * Below are some usage examples,
     *
     * ~~~
     * // working with array
     * $username = \yii\helpers\ArrayHelper::getValue($_POST, 'username');
     * // working with object
     * $username = \yii\helpers\ArrayHelper::getValue($user, 'username');
     * // working with anonymous function
     * $fullName = \yii\helpers\ArrayHelper::getValue($user, function ($user, $defaultValue) {
     *     return $user->firstName . ' ' . $user->lastName;
     * });
     * // using dot format to retrieve the property of embedded object
     * $street = \yii\helpers\ArrayHelper::getValue($users, 'address.street');
     * ~~~
     *
     * @param array|object $array array or object to extract value from
     * @param string|\Closure $key key name of the array element, or property name of the object,
     * or an anonymous function returning the value. The anonymous function signature should be:
     * `function($array, $defaultValue)`.
     * @param mixed $default the default value to be returned if the specified array key does not exist. Not used when
     * getting value from an object.
     * @return mixed the value of the element if found, default value otherwise
     * @throws InvalidParamException if $array is neither an array nor an object.
     */
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (is_array($array) && array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            return $array->$key;
        } elseif (is_array($array)) {
            return array_key_exists($key, $array) ? $array[$key] : $default;
        } else {
            return $default;
        }
    }

    /**
     * Returns the values of a specified column in an array.
     * The input array should be multidimensional or an array of objects.
     *
     * For example,
     *
     * ~~~
     * $array = [
     *     ['id' => '123', 'data' => 'abc'],
     *     ['id' => '345', 'data' => 'def'],
     * ];
     * $result = ArrayHelper::getColumn($array, 'id');
     * // the result is: ['123', '345']
     *
     * // using anonymous function
     * $result = ArrayHelper::getColumn($array, function ($element) {
     *     return $element['id'];
     * });
     * ~~~
     *
     * @param array $array
     * @param string|\Closure $name
     * @param boolean $keepKeys whether to maintain the array keys. If false, the resulting array
     * will be re-indexed with integers.
     * @return array the list of column values
     */
    public static function getColumn($array, $name, $keepKeys = true)
    {
        $result = array();
        if ($keepKeys) {
            foreach ($array as $k => $element) {
                $result[$k] = static::getValue($element, $name);
            }
        } else {
            foreach ($array as $element) {
                $result[] = static::getValue($element, $name);
            }
        }

        return $result;
    }

    /**
     * 删除列表数组的某一栏或多栏
     * @param $arr
     * @param $field_name string|array
     */
    public static function UnsetColumn(&$arr, $field_name)
    {
        if (is_array($arr) && !empty($arr)) {
            foreach ($arr as $key => $val) {
                if (is_array($field_name)) {
                    foreach ($field_name as $v) {
                        unset($arr[$key][$v]);
                    }
                } else {
                    unset($arr[$key][$field_name]);
                }
            }
        }
        return $arr;
    }

    /**
     * 一个数组，按一定的数量分组
     * 例如array(1,2,3,4,5,6,7,8,9,10,11,12,13);每4个分成一份，
     * 返回 array(array(1,2,3,4),array(5,6,7,8),array(9,10,11,12),array(13))
     * @param $array
     * @param int $num
     * @return array
     */
    public static function PiecesByNum($array, $num = 1000)
    {
        $new_arr = array();
        $count = count($array);
        $count = ceil($count / $num);
        $i = 0;
        while ($i < $count) {
            $new_arr[] = array_slice($array, $i * $num, $num);
            $i++;
        }
        return $new_arr;
    }

    /**
     * 获取一个一个列表某一字段的总和
     * @param $list
     * @param $key
     * @return float
     */
    public static function SumColumn(&$list, $key)
    {
        $sum = 0.00;
        if ($list) {
            foreach ($list as $v) {
                $sum +=$v[$key];
            }
        }
        return $sum;
    }

    /**
     * 将数组中为空的元素去除
     * @param $arr
     * @return array
     */
    public static function TrimEmpty($arr)
    {
        $new_arr = array();
        $arr = array_map('trim', $arr);
        foreach ($arr as $val) {
            if (!empty($val)) {
                $new_arr[] = $val;
            }
        }
        return $new_arr;
    }

}
