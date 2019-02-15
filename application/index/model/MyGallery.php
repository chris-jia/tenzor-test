<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/3 0003
 * Time: 下午 5:03
 */

namespace app\index\model;


use think\Model;
use think\Validate;
use traits\model\SoftDelete;

class MyGallery extends Model
{
    use SoftDelete;
    public $table = "gallery";
    //获取器
    protected function getStatusAttr($value)
    {
        $status = [0=>'发布',1=>'取消发布'];
        return ['val' =>$value, 'text' => $status[$value]];
    }
    protected function getCollectAttr($value)
    {
        $collect = [0=>'收藏',1=>'取消收藏'];
        return ['val' =>$value, 'text' => $collect[$value]];
    }
    protected function getCreatorIdAttr($value)
    {
        $user = User::get($value);
        return ['val'=>$value,'text'=>$user->nickname];
    }



    //图库操作
    public function addMyGallery($data){
        $rule = [
            'gallery_name'  => 'require|max:25',
        ];
        $msg = [
            'gallery_name.require' => '图库名不能为空',
            'gallery_name.max'     => '名称最多不能超过25个字符',
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
            return '新增失败';
        }

    }
    public function edit($dataPost)
    {
        $rule = [
            'gallery_name'  => 'require'
        ];
        $msg = [
            'gallery_name.require' => '名称不能为空'
        ];
        $validate = new Validate($rule, $msg);
        if(!$validate->check($dataPost)){
            return $validate->getError();
        }
        $gallery = new MyGallery();
        $result = $gallery->where('gallery_id',$dataPost['gallery_id'])->find();
        $result->gallery_name=$dataPost['gallery_name'];//update没有时间变化 自动写入时间戳是save用法
        $result = $result->save();
        if($result){
            return 1;
        }
        else{
            //该用户不存在
            return '与原名称相同，修改失败';
        }
    }


    public function collect()
    {
        $all = MyGallery::all();
        foreach ($all as $item)
        {

            if ($item['id']==session('user.id')){
                $item->collect = '1';
                $item->save();
            }
        }
        return false;
    }



}