<?php

function date2($timestamp)
{
    return date('Y-m-d', $timestamp);
}

function datetime2($timestamp)
{
    return date('Y-m-d H:i:s', $timestamp);
}

function map($data, $value)
{
    return isset($data[$value]) ? $data[$value] : null;
}

function check_priv($uri)
{
    return \app\admin\Auth::checkPriv($uri);
}

function get_label($label, $field)
{
    $map = explode('|', $field);
    $field = $map[0];
    if(isset($map[1])){
        return $map[1];
    }
    $fields = explode('.', $field);
    $count = count($fields);
    if ($count == 1) {
        return isset($label[$field]) ? $label[$field] : $field;
    } else if ($count == 2) {
        return isset($label[$fields[0]]) && isset($label[$fields[0]][$fields[1]]) ? $label[$fields[0]][$fields[1]] : $field;
    }
    return $field;
}

function get_data($data_table, $field, $data)
{
    $map = explode('|', $field);
    $field = $map[0];
    $extends = $data_table['extends'];
    if (isset($extends[$field])) {
        return call_user_func($extends[$field], $data);
    }
    $fields = explode('.', $field);
    $count = count($fields);
    if ($count == 1) {
        return isset($data[$field]) ? $data[$field] : $field;
    } else if ($count == 2) {
        return isset($data[$fields[0]]) && isset($data[$fields[0]][$fields[1]]) ? $data[$fields[0]][$fields[1]] : '';
    }
    return '';
}