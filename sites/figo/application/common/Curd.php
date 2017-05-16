<?php
namespace app\common;

use think\Db;
use think\Loader;
use think\Model;

class Curd
{
    protected $_model;
    protected $Model;

    public function __construct($model)
    {
        $this->_model = $model;
        $this->Model = Loader::model($model);

        if($with = Request::instance()->getParam('with')){
            $this->Model->with($with);
        }
    }

    public function lists()
    {
        $params = Request::instance()->getParams();
        $this->_filterParams($params);
        if(!empty($params['where']['keyword']) && !empty($this->Model->keyword) ) {
            $params['where'][$this->Model->keyword] = ['like', '%' . $params['where']['keyword'] . '%'];
        }
        unset($params['where']['keyword']);
        $this->Model->where($params['where']);
        $this->Model->limit($params['limit']);

        !empty($params['page']) && $this->Model->page($params['page']);
        $data = $this->Model->order($params['order'])->select();
        $response = ['list' => $data];
        // 处理分页数据
        if(!empty($params['page'])) {
            $response['page'] = $params['page'];
            if($with = Request::instance()->getParam('with')){
                $this->Model->with($with);
            }
            $response['rows'] =$this->Model->where($params['where'])->count();
            $response['pages'] = ceil($response['rows'] / $params['limit']);
        }
        return $response;
    }

    public function create()
    {
        $params = Request::instance()->getParams();
        $params['create_time'] = time();
        $this->Model->data($params);
        if (false === $this->Model->allowField(true)->save()) {
            return Response::instance()->getJson(Code::INSERT_ERROR);
        } else {
            return ['id' => $this->Model->id];
        }
    }

    public function update()
    {
        $Response = Response::instance();
        $params['update_time'] = time();
        $params = Request::instance()->getParams();
        if (!isset($params['id'])) {
            return $Response->getJson(Code::PARAM_MISSING);
        }
        $data = $this->Model->find($params['id']);
        if (empty($data)) {
            return $Response->getJson(Code::RECORD_NOT_EXIST);
        }
        if (false === $this->Model->allowField(true)->save($params, ['id' => $params['id']])) {
            return $Response->getJson(Code::UPDATE_ERROR);
        } else {
            return $Response->getJson(Code::SUCCESS);
        }
    }

    public function read()
    {
        $id = Request::instance()->getParam('id', 0);
        $where = Request::instance()->getParam('where', []);
        if($id){
            $where['id'] = $id;
        }
        $data = $this->Model->where($where)->find();
        if(empty($data)) {
            return Response::instance()->getJson(Code::RECORD_NOT_EXIST);
        }
        return $data;
    }


    public function delete()
    {
        $Response = Response::instance();
        $id = Request::instance()->getParam('id', 0);
        $ids = explode(',', $id);
        if(count($ids) > 1) {
            $this->Model->where('id', 'in', $ids)->delete();
        } else {
            $this->Model->where('id', $id)->delete();
        }
        return $Response->getJson(Code::SUCCESS);
    }

    protected function _filterParams(&$params)
    {
        !isset($params['where']) && $params['where'] = [];
        (!isset($params['limit']) || $params['limit'] < 0) && $params['limit'] = 200;
        !isset($params['order']) && $params['order'] = 'id desc';
    }
}
