<?php
namespace app\common\model;

use think\Model;
use traits\model\SoftDelete;

class SoftDeleteBase extends Base
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

}