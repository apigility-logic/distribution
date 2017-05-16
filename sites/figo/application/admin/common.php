<?php

function map($data, $value)
{
    return isset($data[$value]) ? $data[$value] : null;
}

function get_list_field($data, $grid, $text = false)
{
    $field = $grid[0];
    $attrs = $grid[2];
    if(strpos($field, '.') > 0){
        $fields = explode('.', $field);
        $value = $data[$fields[0]][$fields[1]];
    } else {
        $value = $data[$field];
    }
    foreach ($attrs as $key => $attr) {
        switch ($key) {
            case Admin\Logic\ModelLogic::ATTR_TIMESTAMP:
                $format = isset($attr['format']) ? $attr['format'] : 'Y-m-d H:i:s';
                $value = empty($value) ? '-' : date($format, $value);
                break;
            case \Admin\Logic\ModelLogic::ATTR_REPLACE:
                if ($value != '') {
                    $value = str_replace('[VALUE]', $value, $attr);
                    foreach ($data as $k => $v) {
                        $value = str_replace("[VAR_{$k}]", $v, $value);
                    }
                }
                break;
            case \Admin\Logic\ModelLogic::ATTR_MAP:
                $value = isset($attr[$value]) ? $attr[$value] : $value;
                break;
            case \Admin\Logic\ModelLogic::ATTR_FUNCTION:
                $value = call_user_func($attr, $data);
                break;
            case \Admin\Logic\ModelLogic::MODEL_CALLBACK:
                list($model, $action) = explode('.', $attr);
                $value = D($model)->$action($value);
                break;
        }
    }
    return $value;
}

function get_list_action($action, $data)
{
    if (is_array($action)) {
        $title = $action['title'];
        $uri = $action['uri'];
        $key = $action['key'];
        $params = array();
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $params[$v] = $data[$k];
            }
        } else {
            $params[$key] = $data[$key];
        }
        $html = "<a href=\"" . U($uri, $params) . "\">{$title}</a>";
    } else {
        switch ($action) {
            case \Admin\Logic\ModelLogic::ACTION_ADD:
                $html = '';
                break;
            case \Admin\Logic\ModelLogic::ACTION_EDIT:
                $html = "<a href=\"" . U($action, array('id' => $data['id'])) . "\"><i class='fa fa-fw fa-edit'></i><span class='hidden-xs hidden-sm'>编辑</span></a>";
                break;
            case Admin\Logic\ModelLogic::ACTION_DELETE:
                $html = "<a class=\"ajax-get confirm\" href=\"" . U($action, array('ids' => $data['id'])) . "\"><i class='fa fa-fw fa-trash-o'></i><span class='hidden-xs hidden-sm'>删除</span></a>";
                break;
            case \Admin\Logic\ModelLogic::ACTION_EXPORT:
                $html = '';
                break;
            case \Admin\Logic\ModelLogic::ACTION_BUTTON:
                $html = '';
                break;
            default :
                $html = $action;
                if ($html != '') {
                    foreach ($data as $k => $v) {
                        $html = str_replace("[VAR_{$k}]", $v, $html);
                    }
                }
        }
    }
    return $html;
}

function get_list_search_form($search_fields)
{
    $html = '';
    $keyword = false;
    foreach ($search_fields as $field_name => $options) {
        switch ($options[\Admin\Logic\ModelLogic::SEARCH_TYPE]) {
            case \Admin\Logic\ModelLogic::SEARCH_SELECT:
                $html .= " <label>{$options[\Admin\Logic\ModelLogic::SEARCH_LABEL]} ";
                $html .= "<select name='{$field_name}'>";
                foreach ($options[\Admin\Logic\ModelLogic::SEARCH_SELECT] as $value => $name) {
                    $html .= "<option value='{$value}'";
                    if (isset($_GET[$field_name]) && $_GET[$field_name] == $value) {
                        $html .= ' selected="selected"';
                    }
                    $html .= ">{$name}</option>";
                }
                $html .= "</select>";
                $html .= '</label>';
                break;
            case \Admin\Logic\ModelLogic::SEARCH_TEXT:
                break;
        }
        if ($field_name == \Admin\Logic\ModelLogic::SEARCH_KEYWORD) {
            $keyword = true;
            $html .= " ";
        }
    }
    if ($html) {
        $html .= '<div class="input-group">' .
            ($keyword ? "<input placeholder='请输入关键字' type='text' name='keyword' class='form-control' value='{$_GET['keyword']}'>" : '') .
            '<span class="input-group-btn"><button type="submit" class="btn btn-info">查询</button></span></div>';
    }
    return $html;
}