<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午12:43
 */

namespace app\admin\controller;


use app\common\Curd;
use app\common\Request;
use app\common\Response;
use think\Controller;
use think\Url;

class Base extends Controller
{
    public $model = null;
    public $Request;
    public $Response;
    public $data = array();

    protected function _initialize()
    {
        $this->Request = Request::instance();
        $this->Response = Response::instance();
        if(is_null($this->model)){
            $this->model = $this->Request->controller();
        }
    }

    protected function form()
    {
        return [];
    }

    protected function dataTable()
    {
        return [];
    }

    public function lists()
    {
        $Curd = new Curd($this->model);
        $this->Request->setDefaultParam('page', 1);
        $data = $Curd->lists();
        $this->assign('list_data', $data['list']);
        $Page = new \Page($data['rows']);
        $this->assign('page', $Page->show());
        $this->assign('rows', $data['rows']);
        return $this->fetch();
    }

    public function add()
    {
        if ($this->Request->isPost()) {
            return $this->insert();
        }
        $FormHelper = new \FormHelper($this->form());
        $this->form = $FormHelper->fetch();
        $this->display('Content/edit');
    }

    public function edit()
    {
        if ($this->Request->isPost()) {
            return $this->update();
        }
        $id = $this->Request->getParam('id');
        $BaseLogic = new BaseLogic($this->model_name, $this->model);
        $data = $BaseLogic->find($id);
        $this->data = $data;
        $form = $this->form();
        $FormHelper = new FormHelper($form, $this->data);
        $this->form = $FormHelper->fetch();
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function delete()
    {

    }

    protected function update()
    {

    }

    protected function insert()
    {

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