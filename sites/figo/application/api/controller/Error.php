<?php
namespace app\api\controller;

use app\common\Code;
use app\common\Curd;

class Error extends Base
{
    public function _empty()
    {
        $controller = $this->Request->controller();
        $action = $this->Request->action();
        $access = config('access');
        if (empty($access[$controller]) || !in_array($action, $access[$controller]['action'])) {
            return $this->error(Code::APP_AUTHORITY_LIMIT);
        }
        $user_id = $this->Request->getUserId();
        $model = $access[$controller]['model'];
        if ($access[$controller]['requireAuth']) {
            if (empty($user_id)) {
                return $this->error(Code::APP_TOKEN_ERROR);
            }
            $where = $this->Request->getParam('where', []);
            $where["$model.user_id"] = $user_id;
            $this->Request->setParam('where', $where);
        }
        $Curd = new Curd($model);
        $response = $Curd->$action();
        return $response;
    }
}