<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/28 0028
 * Time: 下午 11:41
 */

namespace app\index\model;
//注意命名空间防冲突


use think\Validate;
use think\Model;
use traits\model\SoftDelete;

class User extends Model
{
    //软删除
    use SoftDelete;//更新，删除操作要先插后操作

    //关联图库


    //确定表名
    public $table = "user";

    //登录校验
    public function login($data)
    {
        $rule = [
            'username|用户名'  => 'require|max:25',
            'password|密码'   => 'require|max:25',
        ];
        $msg = [
            'username.require' => '用户名不能为空',
            'username.max'     => '名称最多不能超过25个字符',
            'password.require'   => '密码不能为空',
            'password.max'  => '密码最多不能超过25个字符',
        ];
        $validate = new Validate($rule, $msg);
        if(!$validate->check($data)){
            return $validate->getError();
        }
        $data['password']=md5($data['password']);
        $result = $this->where($data)->find();
        if($result){
            //该用户存在
            $sessionData = [
                'id' => $result['id'],
                'username'=>$result['username'],
                'nickname'=>$result['nickname'],
                'email' => $result['email'],
                'profile_photo'=>$result['profile_photo']
            ];
            session('user',$sessionData);
            return 1;
        }
        else{
            //该用户不存在
            return '用户名或密码错误';
        }

    }

    //注册验证
    public function register($data)
    {
        $rule = [
            'username'  => 'require|max:25',
            'password'   => 'require|max:25|confirm',
            'nickname' => 'require',
            'email' => 'email'
        ];
        $msg = [
            'username.require' => '用户名不能为空',
            'username.max'     => '名称最多不能超过25个字符',
            'password.require'   => '密码不能为空',
            'password.max'  => '密码最多不能超过25个字符',
            'password.confirm' =>'密码不一致',
            'nickname.require' =>'昵称不能为空',
            'email.email' =>'邮箱格式不正确'
        ];
        $validate = new Validate($rule, $msg);
        if(!$validate->check($data)){
            return $validate->getError();
        }
        $data['password']=md5($data['password']);
        $result = $this->allowField(true)->save($data);//数据库有的才插入
        if($result){
            $user = User::get($this->getAttr('id'));//此处重新获取？？？
            $sessionData = [
                'id' => $this->getAttr('id'),
                'username' => $this->getAttr('username'),
                'nickname' => $this->getAttr('nickname'),
                'email' => $this->getAttr('email'),
                'profile_photo'=>$user->profile_photo
            ];
            session('user',$sessionData);
            return 1;
        }
        else{
            //该用户不存在
            return '注册失败';
        }

    }

    //更新
    public function updates($dataPost)
    {
        $user = User::get(session('user.id'));
        if (empty($dataPost['password'])) {
            $rule = [
                'nickname' => 'require|max:25',
                'email' => 'email',
            ];
            $msg = [
                'nickname.require' => '用户名不能为空',
                'nickname.max' => '名称最多不能超过25个字符',
                'email.email' => '邮箱不符合格式'
            ];
        } else {
            $dataPost['password']=md5($dataPost['password']);
            if ($dataPost['password']!=$user->getAttr('password')){
                return "密码不正确";
            }
            $rule = [
                'password_new'   => 'require|max:25|confirm',
                'nickname' => 'require|max:25',
                'email' => 'email',
            ];
            $msg = [
                'password_new.require'   => '密码不能为空',
                'password_new.max'  => '密码最多不能超过25个字符',
                'password_new.confirm' =>'密码不一致',
                'nickname.require' => '用户名不能为空',
                'nickname.max' => '名称最多不能超过25个字符',
                'email.email' => '邮箱不符合格式'
            ];
        }
        $validate = new Validate($rule, $msg);
        if(!$validate->check($dataPost)){
            return $validate->getError();
        }
        if (!empty($dataPost['password'])){
            $dataPost['username']=$user->getAttr('username');
            $dataPost['password']=$dataPost['password_new'];
        }else{
            $dataPost['username']=$user->getAttr('username');
            $dataPost['password']=$user->getAttr('password');
        }
        $result = $user->allowField(true)->save($dataPost);
        if($result){
            //该用户存在
            $sessionData = [
                'id' => $user->getAttr('id'),
                'username' => $user->getAttr('username'),
                'nickname' => $user->getAttr('nickname'),
                'email' => $user->getAttr('email'),
                'profile_photo' =>$user->getAttr('profile_photo')
            ];
            session('user',$sessionData);
            return 1;
        }
        else{
            //该用户不存在
            return '与原信息相同，修改失败';
        }



    }

    //图库是否被收藏校验
    public function check()
    {
        $gallery = MyGallery::all();
        foreach ($gallery as $item)
        {
            $gallery1 = MyGallery::get($item['gallery_id']);
            $gallery1->collect = 0;
            $gallery1->save();

        }

        $collection = new Collection();
        $collection = $collection->where('id',$this->id)->select();


        //bug
        foreach ($collection as $item)
        {
            $gallery = MyGallery::get($item['gallery_id']);
            if (!empty($gallery)){
                $gallery->collect='1';
                $gallery->save();

            }
        }
        return false;
        //这里效率极低
    }




}