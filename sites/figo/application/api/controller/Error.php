<?php
namespace app\api\controller;

use app\common\Code;
use app\common\Curd;

class Error extends Base
{
    public function _empty()
    {


        $controller = $this->request()->controller();
        $action = $this->request()->action();
        $access = config('access');
        if (empty($access[$controller]) || !in_array($action, $access[$controller]['action'])) {
            return $this->error(Code::APP_AUTHORITY_LIMIT);
        }
        $user_id = $this->request()->getUserId();
        $model = $access[$controller]['model'];
        $params = request()->param();
        unset($params['user_id']);
        if ($access[$controller]['requireAuth']) {
            if (empty($user_id)) {
                return $this->error(Code::APP_TOKEN_ERROR);
            }
            $cond = request()->param('cond/a', []);
            $cond["$model.user_id"] = $user_id;
            $params['cond'] = $cond;
            $params['user_id'] = $user_id;
        }

        $behavior = "\\app\\api\\behavior\\{$controller}";
        if (class_exists($behavior)) {
            $Behavior = new $behavior();
            $beforeAction = $action . 'Before';
            if(method_exists($Behavior, $beforeAction)) {
                $Behavior->$beforeAction($params);
            }
        }

        $Curd = new Curd($model, $params, $access[$controller]);
        $response = $Curd->$action();

        if (class_exists($behavior)) {
            if(method_exists($Behavior, $action)) {
                $Behavior->$action($response);
            }
        }

        return $response;
    }
}