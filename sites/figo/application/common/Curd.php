<?php
namespace app\common;

use think\Db;
use think\Loader;
use think\Model;

class Curd
{
    protected $_model;
    protected $Model;
    protected $_params;
    protected $_options;

    public function __construct($model, $params = [], $options = [])
    {
        $this->_model = $model;
        $this->Model = Loader::model($model);
        $this->_options = $options;
        $this->_params = $params;
        $fields = isset($options['fields']) ? $options['fields'] : [];
        $this->Model->setFields($fields);
        $this->Model->field($this->Model->getFields($this->_model));
        $this->_params['with'] = $this->_filterWith();
        if ($this->_params['with']) {
            $this->Model->with($this->_params['with']);
        }
    }

    public function getError()
    {
        return $this->Model->getError();
    }

    public function param($key, $default = null)
    {
        return isset($this->_params[$key]) ? $this->_params[$key] : $default;
    }

    public function lists()
    {
        $params = $this->_params;
        $this->_filterParams($params);
        $keyword = isset($params['keyword']) ? $params['keyword'] : (isset($params['cond']['keyword']) ? $params['cond']['keyword'] : null);
        if (!empty($keyword) && !empty($this->Model->keyword)) {
            $params['cond'][$this->Model->keyword] = ['like', '%' . $keyword . '%'];
        }
        unset($params['cond']['keyword']);
        $this->Model->where($params['cond']);
        $this->Model->limit($params['limit']);
        !empty($params['page']) && $this->Model->page($params['page']);
        $data = $this->Model->order($params['order'])->select();
        $response = ['list' => $data];
        // 处理分页数据
        if (!empty($params['page'])) {
            $response['page'] = $params['page'];
            if ($with = $this->param('with')) {
                $this->Model->with($with);
            }
            $response['rows'] = $this->Model->where($params['cond'])->count();
            $response['pages'] = ceil($response['rows'] / $params['limit']);
        }
        return $response;
    }

    public function create()
    {
        $params = $this->_params;
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
        $params = $this->_params;
        $Response = Response::instance();
        $params['update_time'] = time();
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
        $id = $this->param('id', 0);
        $where = $this->param('where', []);
        if ($id) {
            $where['id'] = $id;
        }
        $data = $this->Model->where($where)->find();
        if (empty($data)) {
            return Response::instance()->getJson(Code::RECORD_NOT_EXIST);
        }
        return $data;
    }

    public function delete()
    {
        $params = $this->_params;
        $Response = Response::instance();
        $id = $params['id'];
        if (!is_array($id)) {
            $id = explode(',', $id);
        }
        $this->Model->get($id)->delete();
        //$this->Model->where('id', 'in', $id)->delete();
        return $Response->getJson(Code::SUCCESS);
    }

    public function getLabel()
    {
        return $this->Model->getLabel();
    }

    protected function _filterParams(&$params)
    {
        !isset($params['cond']) && $params['cond'] = [];
        (!isset($params['limit']) || $params['limit'] < 0) && $params['limit'] = 200;
        !isset($params['order']) && $params['order'] = 'id desc';
    }

    protected function _filterWith()
    {
        if (!isset($this->_params['with'])) {
            return '';
        } else {
            $withs = explode(',', $this->_params['with']);
            $ret = array_intersect($withs, isset($this->_options['with']) ? $this->_options['with'] : []);
            return join(',', $ret);
        }
    }
}
