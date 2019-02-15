<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/5 0005
 * Time: 下午 11:10
 */

namespace app\index\model;


use think\Model;
use traits\model\SoftDelete;

class Collection extends Model
{
    use SoftDelete;                 

    public $table ='collection';
}