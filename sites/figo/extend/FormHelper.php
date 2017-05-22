<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FormHelper
 *
 * @author David
 */

class FormHelper
{

    const TYPE_STATIC = 'static';
    const TYPE_TEXT = 'text';
    const TYPE_TAGS = 'tags';
    const TYPE_SELECT = 'select';
    const TYPE_RADIO = 'radio';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_DATETIME = 'datetime';
    const TYPE_DATE = 'date';
    const TYPE_FILE = 'file';
    const TYPE_IMAGE = 'image';
    const TYPE_IMAGES = 'images';
    const TYPE_STATIC_IMAGES = 'static_images';
    const TYPE_EDITOR = 'editor';
    const TYPE_GANGED = 'ganged';
    const TYPE_PASSWORD = 'password';
    const TYPE_VIEW = 'view';
    const TYPE_HIDDEN = 'hidden';
    const TYPE_JS = 'javascript';
    const TYPE_OUTPUT = 'output';
    const TYPE_LIST = 'list';
    const TYPE_DISTRICT = 'district';

    protected $_items = [];

    function __construct($params, $label = [], $data = [])
    {
        if (!is_array($params[0][1])) {
            $params = array($params);
        }
        foreach ($params as $group) {
            $group_name = is_string($group[0]) ? $group[0] : 'default';
            foreach ($group as $param) {
                if (is_array($param)) {
                    $name = $param[0];
                    $default = isset($param[4]) ? $param[4] : NULL;
                    if (strpos($name, ',')) {
                        $name = explode(',', $name);
                        foreach ($name as $key) {
                            $value[$key] = isset($data[$name]) ? $data[$name] : null;
                        }
                    } else {
                        $value = isset($data[$name]) ? $data[$name] : $default;
                    }
                    $type = $param[1];
                    $options = isset($param[2]) ? $param[2] : array();
                    $Item = new \FormHelper\Item();
                    $Item->setLabel(get_label($label, $name));
                    $Item->setName($name);
                    $Item->setType($type);
                    $Item->setOptions($options);
                    $Item->setValue($value);
                    $this->_items[$group_name][] = $Item;
                }
            }
        }
    }

    public function fetch()
    {
        $html = [];
        foreach ($this->_items as $group => $items) {
            $html[$group] = '';
            foreach ($items as $item) {
                $html[$group] = $html[$group] . $item->fetch();
            }
        }
        return $html;
    }

    public function verify()
    {
        
    }


}
