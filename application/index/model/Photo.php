<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/3 0003
 * Time: 下午 7:47
 */

namespace app\index\model;


use think\Model;
use think\Validate;
use traits\model\SoftDelete;

class Photo extends Model
{
    use SoftDelete;
    public $table = "photo";


    public function addPhoto($data){
        $rule = [
            'photo_name'  => 'require|max:25'
        ];
        $msg = [
            'photo_name.require' => '名称不能为空'
        ];
        $validate = new Validate($rule, $msg);
        if(!$validate->check($data)){
            return $validate->getError();
        }
        $result = $this->allowField(true)->save($data);//数据库有的才插入
        if($result){
            return 1;
        }
        else{
            //该用户不存在
            return '添加失败';
        }
    }
    public function edit($dataPost)
    {
        $rule = [
            'photo_name'  => 'require|max:25'
        ];
        $msg = [
            'photo_name.require' => '名称不能为空'
        ];
        $validate = new Validate($rule, $msg);
        if(!$validate->check($dataPost)){
            return $validate->getError();
        }
        $photo = new Photo();
        $result = $photo->where('photo_id',$dataPost['photo_id'])->update(['photo_name'=>$dataPost['photo_name']]);//数据库有的才插入
        if($result){
            return 1;
        }
        else{
            //该用户不存在
            return '与原名称相同，失败';
        }
    }



}