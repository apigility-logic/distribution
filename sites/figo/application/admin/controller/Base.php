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

class Base extends Controller
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

    protected function searches()
    {
        $keyword = request()->param('keyword');
        return [
            "<input placeholder='请输入关键字' type='text' name='keyword' class='form-control' value='{$keyword}'>",
        ];
    }

    protected function listAction()
    {
        $module = request()->module();
        $controller = request()->controller();
        $actions = [];
        if (check_priv("/{$module}/{$controller}/add")) {
            $actions[] = '<a class="btn btn-success" href="' . Url("{$module}/{$controller}/add") . '">新增</a>';
        }
        if (check_priv("/{$module}/{$controller}/delete")) {
            $actions[] = '<a class="btn btn-warning ajax-post confirm" target-form="ids"  url="' . Url("{$module}/{$controller}/delete") . '">删除</a>';
        }
        return $actions;
    }

    protected function dataAction($data)
    {
        $module = request()->module();
        $controller = request()->controller();
        $actions = [];
        if (check_priv("/{$module}/{$controller}/edit")) {
            $actions[] = '<a href="' . Url("{$module}/{$controller}/edit", ['id' => $data['id']]) . '"><i class="fa fa-fw fa-edit"></i><span class="hidden-xs hidden-sm">编辑</span></a>';
        }
        if (check_priv("/{$module}/{$controller}/delete")) {
            $actions[] = '<a class="ajax-get confirm" href="' . Url("{$module}/{$controller}/delete", ['id' => $data['id']]) . '""><i class="fa fa-fw fa-trash-o"></i><span class="hidden-xs hidden-sm">删除</span></a>';
        }
        return $actions;
    }

    protected function form()
    {
        return [];
    }

    protected function dataTable()
    {
        return [
            'fields' => [],
            'extends' => [],
        ];
    }

    public function lists()
    {
        $params = request()->param();
        $params['page'] = request()->param('page', 1);
        $data_table = $this->dataTable();
        $params['with'] = isset($data_table['with']) ? $data_table['with'] : '';
        $Curd = new Curd($this->model, $params, ['with' => explode(',', $params['with'])]);
        $data = $Curd->lists();
        $this->assign('list_data', $data['list']);
        $Page = new \Page($data['rows']);
        $this->assign('page', $Page->show());
        $this->assign('rows', $data['rows']);
        $this->assign('label', $Curd->getLabel());
        $this->assign('data_table', $data_table);
        $this->assign('list_action', $this->listAction());
        $this->assign('data_action', function ($data) {
            return $this->dataAction($data);
        });
        $this->assign('searches', $this->searches());
        $actions = $this->dataAction(['id' => 1]);
        if (count($actions) == 0) {
            $this->assign('has_action', false);
        } else {
            $this->assign('has_action', true);
        }
        $this->assign('scripts', $this->scripts('lists'));
        return $this->fetch();
    }

    public function add()
    {
        if (request()->isPost()) {
            return $this->insert();
        }
        $model = Loader::model($this->model);
        $FormHelper = new \FormHelper($this->form(), $model->getLabel());
        $this->assign('form', $FormHelper->fetch());
        $this->assign('scripts', $this->scripts('edit'));
        return $this->fetch('content/edit');
    }

    public function edit()
    {
        if (request()->isPost()) {
            return $this->update();
        }
        $params = request()->param();
        $Curd = new Curd($this->model, $params);
        $data = $Curd->read();
        $this->data = $data;
        $form = $this->form();
        $FormHelper = new \FormHelper($form, $label = $Curd->getLabel(), $this->data);
        $this->assign('form', $FormHelper->fetch());
        $this->assign('data', $data);
        $this->assign('scripts', $this->scripts('edit'));
        return $this->fetch();
    }

    public function delete()
    {
        $params = request()->param();
        $Curd = new Curd($this->model, $params);
        $res = $Curd->delete();
        if (\app\common\Response::instance()->isSuccess($res)) {
            $this->success('删除成功');
        } else {
            $this->error($Curd->getError());
        }
    }

    //插入
    public function insert($callback = null)
    {
        $params = request()->param();
        $params['create_time'] = time();
        $Curd = new Curd($this->model, $params);
        $res = $Curd->create();
        if (isset($res['id'])) {
            $params['id'] = $res['id'];
            $callback && call_user_func($callback, $params);
            $this->success('新增成功', Url(request()->module() . '/' . request()->controller() . '/lists'));
        } else {
            $this->error($Curd->getError());
        }
    }

    //更新
    public function update($callback = null)
    {
        $params = request()->param();
        $params['create_time'] = time();
        $Curd = new Curd($this->model, $params);
        $res = $Curd->update();
        if (Response::instance()->isSuccess($res)) {
            $referer = Url(request()->module() . '/' . request()->controller() . '/lists');
            $callback && call_user_func($callback, $params);
            $this->success('更新成功', request()->param('referer', $referer));
        } else {
            $this->error(CrudLogic::getErrorMsg());
        }
    }

    //脚本
    public function scripts($module = null)
    {
        $scripts = [
            'edit' => [],
            'lists' => [],
        ];
        $editor = false;
        foreach ($this->form() as $row) {
            if ($row[1] == \FormHelper::TYPE_EDITOR) {
                $editor = true;
            }
        }
        if ($editor) {
            $scripts['edit'][] = '<script src="' . request()->root() . '/static/ueditor/ueditor.config.js"></script>';
            $scripts['edit'][] = '<script src="' . request()->root() . '/static/ueditor/ueditor.all.min.js"></script>';
            $scripts['edit'][] = '<script src="' . request()->root() . '/static/ueditor/lang/zh-cn/zh-cn.js"></script>';
        }
        foreach($this->form() as $row){
            if ($row[1] == \FormHelper::TYPE_EDITOR) {
                $scripts['edit'][] = '<script>' ."UE.getEditor('editor_{$row[0]}');".'</script>';
            }
        }
        return $module && isset($scripts[$module]) ? $scripts[$module] : [];
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