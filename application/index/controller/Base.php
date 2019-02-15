<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31 0031
 * Time: 下午 4:05
 */

namespace app\index\controller;


use think\Controller;

class Base extends Controller
{
    public function _initialize()
    {
        echo session('user.id');
        if(session('?user.id')){
//            $this->redirect('index/index/home');
        }
    }

}