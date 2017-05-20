<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午12:43
 */

namespace app\admin\controller;


use app\admin\Auth;
use app\admin\Menu;
use app\common\Curd;
use app\common\Request;
use app\common\Response;
use think\Controller;
use think\Loader;

class TreeBase extends Controller
{
    public $model = null;
    public $data = [];

    protected function _initialize()
    {
        if (!Auth::isAuth()) {
            $this->redirect(Url('/admin/auth/login'));
        }
        if (is_null($this->model)) {
            $this->model = request()->controller();
        }
        if (!request()->isAjax()) {
            $group = Auth::getGroup();
            if (empty($group)) {
                exit('没有访问权限');
            }
            $menus = Menu::getTreeMenus($group);
            $this->assign('menus', $menus);
            $this->assign('cur_menu', Menu::getCurMenu());
            $this->assign('uid', Auth::getUid());
        }
    }

    protected function actions()
    {
        $module = request()->module();
        $controller = request()->controller();
        $actions = [];
        if (check_priv("/{$module}/{$controller}/add")) {
            $actions[] = '<button type="button" class="btn btn-success" onclick="jstree_create()">新 增</button>';
        }
        if (check_priv("/{$module}/{$controller}/rename")) {
            $actions[] = '<button type="button" class="btn btn-info" onclick="jstree_rename()">重命名</button>';
        }
        if (check_priv("/{$module}/{$controller}/delete")) {
            $actions[] = '<button type="button" class="btn btn-default" onclick="jstree_delete()">删 除</button>';
        }
        return $actions;
    }

    public function tree()
    {
        if (request()->isPost()) {

        } else {
            $this->assign('actions', $this->actions());
            return $this->fetch();
        }
    }

    protected function config(){
        return ['name' => 'root'];
    }

    public function data()
    {
        $this->data = model($this->model)->order('pid asc, sort asc')->select();
        $config = $this->config();
        $tree_data = [
            ['id' => 0, 'parent' => '#', 'text' => $config['name']]
        ];
        foreach($this->data as $row){
            $tree_data[] = [
                'id' => $row['id'],
                'parent' => $row['pid'],
                'text' => $row['title'],
            ];
        }
        return $tree_data;
    }

    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        if ($template === '') {
            $view_file = APP_PATH . request()->module() . '/view/' . request()->controller() . '/' . request()->action() . config('template.view_suffix');
            if (!is_file($view_file)) {
                $template = 'content/' . request()->action();
            } else {
                $template = request()->action();
            }
        }
        return parent::fetch($template, $vars, $replace, $config);
    }
}