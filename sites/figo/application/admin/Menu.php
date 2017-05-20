<?php

/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/17
 * Time: 上午12:15
 */
namespace app\admin;
class Menu
{

    public static $cur = array();
    public static $menus = array();

    /**
     * 按用户组获取可访问权限
     * @staticvar array $rules
     * @param type $group_id
     * @return type
     */
    public static function getRules($group_id = 0)
    {
        static $rules = array();
        if (empty($rules[$group_id])) {
            if (is_array($group_id)) {
                $group_id = join(',', $group_id);
            }
            if ($group_id) {
                $data = model('auth_group')->where(['id' => ['in', $group_id]])->select();
                $rule_data = array();
                foreach ($data as $row) {
                    $rule_data = array_merge($rule_data, explode(',', $row['rules']));
                }
                $rule_data = array_unique($rule_data);
                $rules[$group_id] = $rule_data;
            } else {
                //所有权限
                $data = model('auth_rule')->where(['status' => 1])->select();
                $rule_data = ArrayHelper::extract_value($data, 'id');
                $rules[$group_id] = $rule_data;
            }
        }
        return $rules[$group_id];
    }

    /**
     * 获取子菜单
     * @param int $pid
     * @param string $rules
     * @return type
     */
    public static function getChild($pid = 0, $rules = null)
    {
        if (empty($rules)) {
            return array();
        } else if (is_array($rules)) {
            $rules = join(',', $rules);
        }
        $where = array(
            'extend_type' => 1, //菜单类型
            'pid' => $pid,
            'id' => ['in', $rules]
        );
        $data = model('auth_rule')->where($where)->order('list_order asc')->select();
        return $data ? $data : array();
    }

    /**
     * 导航面包屑
     * @return array|mixed
     */
    public static function getCrumb($path)
    {
        if ($path) {
            $where = [
                'id' => array('in', $path)
            ];
            return model('auth_rule')->where($where)->order("FIELD(`id`, {$path})")->select();
        }
        return array();
    }

    /**
     * 树形菜单
     * @param null $group_id
     * @return array
     */
    public static function getTreeMenus($group_id = null)
    {
        self::$menus = self::getMenus($group_id);
        function getTree($pid, $menus)
        {
            $data = array();
            $uri = \app\common\Request::instance()->getUri();
            foreach ($menus as $menu) {
                if ($pid == $menu['pid']) {
                    $menu['child'] = getTree($menu['id'], $menus);
                    $active = false;
                    foreach ($menu['child'] as $child) {
                        if ($child['active']) {
                            $active = true;
                        }
                    }
                    if (strpos(strtolower($menu['name']), strtolower($uri)) !== false || $active) {
                        $menu['active'] = true;
                    } else {
                        $menu['active'] = false;
                    }
                    $data[] = $menu;
                }
            }
            return $data;
        }

        $menus = getTree(0, self::$menus);
        return $menus;
    }

    /**
     * 获取当前菜单
     * @param null $group_id
     * @return array
     */
    public static function getCurMenu()
    {
        if (empty(self::$cur)) {
            $uri = \app\common\Request::instance()->getUri();
            $menus = self::$menus;
            foreach ($menus as $row) {
                if (strpos(strtolower($row['name']), strtolower($uri)) !== false) {
                    $row['crumb'] = self::getCrumb($row['path']);
                    self::$cur = $row;
                }
            }
            if(empty(self::$cur)){
                self::$cur = [
                    'title' => '',
                    'crumb' => []
                ];
            }
        }
        return self::$cur;
    }

    /**
     * 按用户组获取菜单列表
     * @param type $group_id
     * @return type
     */
    public static function getMenus($group_id = null)
    {
        if (empty(self::$menus)) {
            $rules = self::getRules($group_id);
            if (empty($rules)) {
                return array();
            } else if (is_array($rules)) {
                $rules = join(',', $rules);
            }
            $where = [
                'status' => 1,
            ];
            if($rules != '*'){
                $where['id'] = ['in', $rules];
            }
            self::$menus = model('auth_rule')->where($where)->order('pid asc, list_order asc')->select();
        }
        return self::$menus;
    }

    /**
     * 获取第一个菜单链接
     * @param type $group_id
     * @return type
     */
    public static function firstTopMenu($group_id)
    {
        $menus = self::getMenus($group_id);
        return $menus ? $menus[0]['name'] : '/Index/index';
    }

}