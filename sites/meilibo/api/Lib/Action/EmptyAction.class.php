<?php
/**
 * Created by PhpStorm.
 * User: shellvon
 * Date: 16/4/12
 * Time: 上午9:17
 */
class EmptyAction extends Action
{
    public function _empty()
    {
        header("HTTP/1.0 404 Not Found");
        echo "<h1 style='text-align: center'>404</h1>";
    }
}