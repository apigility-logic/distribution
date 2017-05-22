<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Item
 *
 * @author David
 */

namespace FormHelper;

use app\common\Media;
use think\View;

class Item
{

    protected $_label;
    protected $_type;           //text, radio, checkbox, file, textarea, select
    protected $_name;
    protected $_value;
    protected $_placeholder;
    protected $_group;
    protected $_options = array();

    public function __construct()
    {

    }

    public function setLabel($label)
    {
        $this->_label = $label;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    public function setType($type)
    {
        $this->_type = $type;
    }

    public function setOptions($options)
    {
        $this->_options = $options;
    }

    public function setValue($value)
    {
        $this->_value = $value;
    }

    public function fetch()
    {
        $html = "";
        $html .= "<div class=\"form-group\"" . ($this->_type == 'hidden' ? ' hidden' : '') . ">";
        $html .= "<label class='col-sm-2 col-md-21 control-label'>{$this->_label}</label>";
        $html .= '<div class="col-sm-8 col-md-8">';

        $class = '';
        $attr = [];
        if (isset($this->_options['attr'])) {
            foreach ($this->_options['attr'] as $key => $value) {
                if ($key == 'class') {
                    $class = $value;
                } else {
                    $attr[] = $key . '="' . $value . '"';
                }
            }
        }
        $attr = join(' ', $attr);

        switch ($this->_type) {
            case \FormHelper::TYPE_STATIC:

                $item_html = "<input type=\"text\" "
                    . "class=\"form-control\" "
                    . "value=\"{$this->_value}\" "
                    . "disabled=\"\""
                    . "placeholder=\"{$this->_placeholder}\">";
                break;

            case \FormHelper::TYPE_TEXT:  //文本

                $item_html = "<input type=\"text\" "
                    . "class=\"form-control\" "
                    . "name=\"{$this->_name}\" "
                    . "value=\"{$this->_value}\" "
                    . "placeholder=\"{$this->_placeholder}\">";
                break;

            case \FormHelper::TYPE_TAGS: //标签

                $item_html = "<input type=\"text\" "
                    . "class=\"form-control tags\" "
                    . "name=\"{$this->_name}\" "
                    . "value=\"{$this->_value}\" "
                    . "placeholder=\"{$this->_placeholder}\">";
                break;

            case \FormHelper::TYPE_TEXT:  //文本

                $item_html = "<input type=\"hidden\" "
                    . "class=\"form-control\" "
                    . "name=\"{$this->_name}\" "
                    . "value=\"{$this->_value}\" "
                    . "placeholder=\"{$this->_placeholder}\">";
                break;

            case \FormHelper::TYPE_PASSWORD:  //密码

                $item_html = "<input type=\"password\" "
                    . "class=\"form-control\" "
                    . "name=\"{$this->_name}\" "
                    . "value=\"\" "
                    . "placeholder=\"{$this->_placeholder}\">";
                break;

            case \FormHelper::TYPE_DATETIME: //日期时间

                $item_html = '<div class="input-group date">'
                    . '<div class="input-group-addon">'
                    . '<i class="fa fa-calendar"></i>'
                    . '</div>'
                    . "<input type=\"text\" "
                    . "name=\"{$this->_name}\" "
                    . "class=\"form-control picker-datetime\" "
                    . "value=\"" . (is_numeric($this->_value) ? date('Y-m-d', $this->_value) : $this->_value) . "\" "
                    . "placeholder=\"\" />"
                    . '</div>';
                break;

            case \FormHelper::TYPE_DATE: //日期

                $item_html = '<div class="input-group date">'
                    . '<div class="input-group-addon">'
                    . '<i class="fa fa-calendar"></i>'
                    . '</div>'
                    . "<input type=\"text\" "
                    . "name=\"{$this->_name}\" "
                    . "class=\"form-control picker-date\" "
                    . "value=\"" . (is_numeric($this->_value) ? date('Y-m-d', $this->_value) : $this->_value) . "\" "
                    . "placeholder=\"\" />"
                    . '</div>';
                break;

            case \FormHelper::TYPE_RADIO: //单选

                $item_html = '';
                $default = isset($this->_options['default']) ? $this->_options['default'] : null;
                foreach ($this->_options['options'] as $key => $value) {
                    $item_html .= "<div class='radio-inline'><label>"
                        . "<input type=\"radio\" value=\"{$key}\" name=\"{$this->_name}\" "
                        . ((isset($this->_value) && $key == $this->_value || is_null($this->_value) && $default == $key) ? "checked" : "")
                        . ">{$value}</label></div>";
                }
                break;

            case \FormHelper::TYPE_CHECKBOX: //复选

                $item_html = '';
                $this->_value = is_null($this->_value) ? null : explode(',', $this->_value);
                $default = isset($this->_options['default']) ? $this->_options['default'] : [];
                foreach ($this->_options['options'] as $key => $value) {
                    $item_html .= "<div class='checkbox-inline'><label>"
                        . "<input type=\"checkbox\" value=\"{$key}\" name=\"{$this->_name}[]\" "
                        . ((isset($this->_value) && in_array($key, $this->_value) || is_null($this->_value) && in_array($key, $default)) ? "checked" : "")
                        . ">{$value}</label></div>";
                }
                break;

            case \FormHelper::TYPE_FILE:

                break;

            case \FormHelper::TYPE_IMAGE:

                $item_html = "<input class=\"upload_image\" type=\"file\" id=\"upload_image_{$this->_name}\">"
                    . "<input type=\"hidden\" name=\"{$this->_name}\" id=\"cover_id_{$this->_name}\" value=\"{$this->_value}\"/>"
                    . "<div class=\"upload-img-box\">";
                if ($this->_value) {
                    $thumb = Media::thumb($this->_value, 200, 200);
                    $item_html .= "<div class=\"upload-pre-item\">"
                        . "<div class=\"delete-item\"></div>"
                        . "<img width='116', height='116' data-origin=\"" . Media::getUrl($this->_value) . "\" src=\"" . Media::getUrl($thumb) . "\"/>"
                        . "</div>";
                }
                $item_html .= "</div>";
                break;

            case \FormHelper::TYPE_IMAGES:

                $item_html = "<input class=\"upload_images\" type=\"file\" id=\"upload_images_{$this->_name}\">"
                    . "<input type=\"hidden\" name=\"{$this->_name}\" id=\"cover_id_{$this->_name}\" value=\"{$this->_value}\"/>"
                    . "<div class=\"upload-img-box\" style=\"width:400px;\">";
                if ($this->_value) {
                    $images = explode(',', $this->_value);
                    foreach ($images as $image) {
                        $thumb = Media::thumb($image, 200, 200);
                        $item_html .= "<div class=\"upload-pre-item\" style=\"float:left;\">"
                            . "<div class=\"delete-item\"></div>"
                            . "<img data-origin=\"" . Media::getUrl($image) . "\" src=\"" . Media::getUrl($thumb) . "\"/>"
                            . "</div>";
                    }
                }
                $item_html .= "</div>";
                break;

            case \FormHelper::TYPE_STATIC_IMAGES:

                $item_html = "<div class=\"upload-img-box\" style=\"width:400px;\">";
                if ($this->_value) {
                    $images = explode(',', $this->_value);
                    foreach ($images as $image) {
                        $thumb = Media::thumb($image, 200, 200);
                        $item_html .= "<div class=\"upload-pre-item\" style=\"float:left;\">"
                            . "<img data-origin=\"" . Media::getUrl($image) . "\" src=\"" . Media::getUrl($thumb) . "\"/>"
                            . "</div>";
                    }
                }
                $item_html .= "</div>";
                break;

            case \FormHelper::TYPE_TEXTAREA: //富文本
                $item_html = "<textarea name=\"{$this->_name}\" class=\"form-control {$class}\" {$attr}>{$this->_value}</textarea>";
                break;

            case \FormHelper::TYPE_SELECT: //下拉菜单

                $item_html = "<select class='form-control' style='width:auto;' name=\"{$this->_name}\">";
                if (isset($this->_options['model'])) {
                    list($model, $value, $label) = $this->_options['model'];
                    $data = model($model)->field("$value,$label")->select();
                    foreach ($data as $row) {
                        $this->_options['options'][$row[$value]] = $row[$label];
                    }
                }
                $default = isset($this->_options['default']) ? $this->_options['default'] : null;
                foreach ($this->_options['options'] as $key => $value) {
                    $item_html .= "<option value=\"{$key}\" "
                        . ((isset($this->_value) && $key == $this->_value || is_null($this->_value) && $key == $default) ? "selected" : "")
                        . ">{$value}</option>";
                }
                $item_html .= "</select>";
                break;

            case \FormHelper::TYPE_GANGED: //下拉菜单联动

                $item_html = "<div class=\"ganged\" data='" . json_encode($this->_options) . "'>";
                foreach ($this->_name as $name) {
                    $item_html .= "<select name=\"{$name}\"></select>";
                }
                $item_html .= '</div>';
                break;

            case \FormHelper::TYPE_EDITOR: //编辑器

                $item_html = "<textarea id=\"editor_{$this->_name}\" name=\"{$this->_name}\" {$attr}>{$this->_value}</textarea>";
                break;

            case \FormHelper::TYPE_VIEW: //视图文件
                $View = new View();
                foreach ($this->_options[1] as $key => $value) {
                    $View->assign($key, $value);
                }
                $item_html = $View->fetch($this->_options[0]);
                break;
            case \FormHelper::TYPE_OUTPUT: //直接输入
                $item_html = $this->_options;
                break;
            case \FormHelper::TYPE_LIST:
                $item_html = '<table id="example22" class="table table-bordered table-hover" style="min-width: 400px;"><thead><tr>';
                foreach ($this->_options['fields'] as $field) {
                    $item_html .= "<th>" . get_label($this->_options['label'], $field) . "</th>";
                }
                $item_html .= '</tr></thead><tbody>';
                foreach ($this->_value as $data) {
                    $item_html .= "<tr>";
                    foreach ($this->_options['fields'] as $field) {
                        $item_html .= "<td>" . get_data($this->_options, $field, $data) . "</td>";
                    }
                    $item_html .= "</tr>";
                }
                $item_html .= '</tbody></table>';
                break;
            case \FormHelper::TYPE_DISTRICT:
                $item_html = '<div>';
                list($province_id, $city_id, $area_id) = explode(',', $this->_value);
                foreach ($this->_options['ganged'] as $key => $ganged) {
                    $name = $ganged . '_id';
                    $target = isset($this->_options['ganged'][$key + 1]) ? $this->_options['ganged'][$key + 1] : '';
                    $item_html .= '<select id="' . $ganged .'" target-ganged=" '. $target .'" name="' . $name . '" class="form-control inline district" style=\'width:auto; margin-right: 15px;\'><option value="0">选择省份</option>';
                    if($ganged == 'province') {
                        $pid = 0;
                    } else if($ganged == 'city'){
                        $pid = $province_id;
                    } else {
                        $pid = $city_id;
                    }
                    $value = $$name;
                    $child = model('district')->child($pid);
                    foreach ($child as $data) {
                        if ($value == $data['id']) {
                            $item_html .= '<option value="' . $data['id'] . '" selected="">';
                        } else {
                            $item_html .= '<option value="' . $data['id'] . '">';
                        }
                        $item_html .= $data['title'];
                        $item_html .= '</option>';
                    }
                    $item_html .= '</select>';
                }
                $item_html .= '</div>';
                break;
        }
        $html .= $item_html . '</div>';
        $html .= '</div>';
        return $html;
    }

}
