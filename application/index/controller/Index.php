<?php
namespace app\index\controller;

use app\index\model\Collection;
use app\index\model\MyGallery;
use app\index\model\Photo;
use app\index\model\User;
use think\Controller;

class Index extends Controller
{
    //前置验证登录
    protected $beforeActionList = [
        'first' =>  ['except'=>'login,register'],
    ];
    protected function first()
    {
        $is_login = session('user');
        if (empty($is_login)){
            $this->redirect('index/index/login');
        }
    }


    //登录
    public function login()
    {


        if (request()->isPost()) {
            $data = [
                'username' => input('post.username'),//养成写post的习惯
                'password' => input('post.password'),
            ];
            $user = new User();
            $result = $user->login($data);
            if ($result == 1) {
                $this->success('登录成功！',url('index/index/home','location=个人广场'));
            }else {
                $this->error($result);
            }
        }else{
            //是否已登录判断
            $is_login = session('user');
            if (!empty($is_login)){
                $this->redirect(url('index/index/home','location=个人广场'));
            }
        }
        return view();
    }
    //注册
    public function register()
    {
        if(request()->isPost())
        {
            $data = [
                'username' => input('post.username'),//养成写post的习惯
                'password' => input('post.password'),
                'password_confirm' => input('post.password_confirm'),
                'nickname' => input('post.nickname'),
                'email' =>input('post.email')
            ];
            $user = new User();
            //用户名验证
            $result = $user->where('username',$data['username'])->find();
            if (!empty($result)){
                $this->error('用户名已存在');
            }
            $result = $user->register($data);
            if ($result == 1) {
                $this->success('注册成功！',url('index/index/home','location=个人广场'));
            }else {
                $this->error($result);
            }
        }

        return view();
    }
    //注销
    public function logoff()
    {
        //清空session
        session(null);
        $this->redirect('index/index/login');
    }

    //个人中心
    public function home()
    {
        //初始化收藏表
        $user = User::get(session('user.id'));
        $user->check();//初始化收藏表
        if (request()->isPost()){
            $dataPost = [
                'password' => input('post.password'),//养成写post的习惯
                'password_new' => input('post.password_new'),
                'password_new_confirm' => input('post.password_new_confirm'),
                'nickname' => input('post.nickname'),
                'email' =>input('post.email'),
            ];
            $file = request()->file('profile_photo');
            if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'profile_photo');
                if(!$info){
                    echo "上传头像失败";
                }else{
                    $dataPost['profile_photo']=$info->getSaveName();
                }
            }
            $user = new User();
            $result = $user->updates($dataPost);
            if ($result == 1) {
                $this->success('修改成功',url('index/index/home','location=个人广场'));
            }else {
                $this->error($result);
            }

        }
        $location = input('location');
        session('location',$location);
        return view();
    }

    //图库操作
    public function myGallery()
    {
        $gallery = MyGallery::all();
        $location=input('location');
        session('location',$location);
        //设置默认第一张封面
        foreach ($gallery as $item)
        {
            if (empty($item->head_photo_path)){
                $photo = new Photo();
                $photo = $photo->where('gallery_id',$item->gallery_id)->find();
                if (!empty($photo)){
                    $gallery = MyGallery::get($item->gallery_id);
                    $path = $photo->path;
                    $gallery->head_photo_path =$path;
                    $gallery->save();
                }

            }
        }

        $gallery = MyGallery::all(['creator_id'=>session('user.id')]);
        $this->assign('gallery',$gallery);
        return view();
    }
    public function addMyGallery(){
        if (request()->isPost()) {
            $data = [
                'gallery_name'=>input('post.gallery_name'),
                'creator_id'=>session('user.id'),
                'gallery_describe'=>input('post.gallery_describe')
            ];
            $gallery = new MyGallery();
            $result = $gallery->addMyGallery($data);
            if ($result == 1) {
                $this->success('新增成功',url('index/index/myGallery','location=我的图库'));
            }else {
                $this->error($result);
            }
        }
        $location=input('location');
        $this->assign('location',$location);
        return view();
    }
    public function editGallery()
    {
        $gallery_id = input('gallery_id');
        $gallery = MyGallery::get($gallery_id);
        if (empty($gallery)||$gallery->creator_id['val']!=session('user.id')){
            $this->error('无权访问');
        }
        if (request()->isPost()) {
            $dataPost = [
                'gallery_id' =>$gallery->gallery_id,
                'gallery_name' => input('gallery_name'),//养成写post的习惯
            ];
            $result = $gallery->edit($dataPost);
            if ($result == 1) {
                $this->success('修改成功', url('index/index/myGallery','location=我的图库'));
            } else {
                $this->error($result);
            }
        }
        $list = Photo::all(['gallery_id'=>$gallery->gallery_id]);
        $this->assign('list',$list);
        $this->assign('gallery',$gallery);
        return view();
    }
    public function deleteGallery()
    {
        $gallery_id = input('gallery_id');
        $gallery = MyGallery::get($gallery_id);
        if (empty($gallery)||$gallery->creator_id['val']!=session('user.id')){
            $this->error('无权访问');
        }
        $result = MyGallery::get($gallery_id)->delete();
        if($result){
            $this->success('删除成功',url('index/index/myGallery','location=我的图库'));
        }else{
            $this->error('删除失败');
        }
    }
    public function release()
    {
        $gallery_id = input('gallery_id');
        $gallery = MyGallery::get($gallery_id);
        if (empty($gallery)||$gallery->creator_id['val']!=session('user.id')){
            $this->error('无权访问');
        }
        if ($gallery->getData('status')!=0)
        {
            $gallery->status='0';
            $result = $gallery->save();
            if ($result){
                $this->success('取消发布成功',url('index/index/myGallery','location=我的图库'));
            }else{
                $this->error('取消发布失败');
            }
        }else{
            $photo = Photo::all(['gallery_id'=>$gallery->gallery_id]);
            if (empty($photo)){
                $this->error('图库为空，不能发布');
            }

            $gallery->status='1';
            $result = $gallery->save();
            if ($result){
                $this->success('发布成功',url('index/index/myGallery','location=我的图库'));
            }else{
                $this->error('发布失败');
            }
        }

    }


    //图片操作
    public function  photo(){


        $gallery_id = input('gallery_id');
        $gallery = MyGallery::get($gallery_id);
        //url验证
        if (empty($gallery)||$gallery->creator_id['val']!=session('user.id')){
            return $this->error('无权访问');
        }
        $arr = [
          'name'=>$gallery['gallery_name'],
          'id' =>$gallery['gallery_id']
        ];
        session('gallery',$arr);
        $list = Photo::all(['gallery_id'=>$arr['id']]);
        $this->assign('list',$list);
        return view();
    }
    public function addPhoto(){
        if (request()->isPost()) {
            $data = [
                'creator_id'=>session('user.id'),
                'gallery_id'=>session('gallery.id'),
                'photo_name'=>input('post.photo_name'),
            ];
            $file = request()->file('photo');
            if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'photo');
                if(!$info){
                    echo "上传图片失败";
                }else{
                    $data['path']=$info->getSaveName();
                }
            }else{
                $this->error('图片不能为空');
            }
            $photo = new Photo();
            $result = $photo->addPhoto($data);
            if ($result == 1) {
                $this->success('增加成功',url('index/index/photo',['gallery_id' => $data['gallery_id']]));
            }else {
                $this->error($result);
            }
        }
        return view();

    }
    public function editPhotoName()
    {
        $photo_id = input('photo_id');
        $photo = Photo::get($photo_id);
        if (empty($photo)||$photo->gallery_id!=session('gallery.id')){
            $this->error('无权访问');
        }
        $photo = Photo::get($photo_id);
        if (request()->isPost()) {
            $dataPost = [
                'photo_id' =>input('photo_id'),
                'photo_name' => input('post.photo_name'),//养成写post的习惯
            ];
            $result = $photo->edit($dataPost);
            if ($result == 1) {
                $this->success('修改成功', url('index/index/photo', ['gallery_id'=>session('gallery.id')]));
            } else {
                $this->error($result);
            }
        }
        $this->assign('data', $photo);
        return view();

    }
    public function deletePhoto()
    {
        $photo_id = input('photo_id');
        $photo = Photo::get($photo_id);
        if (empty($photo)||$photo->gallery_id!=session('gallery.id')){
            $this->error('无权访问');
        }
        $result = Photo::get($photo_id);

        //判断是否为第一张图片,是则更新封面文件
        $first = new Photo();
        $first = $first->where('gallery_id',session('gallery.id'))->find();
        if ($result->photo_id==$first->photo_id)
        {
            $gallery = MyGallery::get(session('gallery.id'));
            $gallery->head_photo_path=null;
            $gallery->save();
        }
        $result = $result->delete();
        if($result){
            $this->success('删除成功',url('index/index/photo',['gallery_id'=>session('gallery.id')]));
        }else{
            $this->error('删除失败');
        }
    }


    //图库广场
    public function gallerySquare(){

        //对相册当前是否被该用户收藏检测
//        $all = new MyGallery();
//        $all->collect();
        $list = MyGallery::all(['status'=>1]);
        $this->assign('list',$list);
        $location=input('location');
        session('location',$location);
        return view();
    }
    public function showPhoto()
    {
        $gallery_id = input('gallery_id');
        $gallery = MyGallery::get($gallery_id);
        //url验证
        if (empty($gallery)||$gallery->status['val']!=1){
            return $this->error('无权访问');
        }
        $arr = [
            'name'=>$gallery['gallery_name'],
            'id' =>$gallery['gallery_id']
        ];
        session('gallery',$arr);
        $list = Photo::all(['gallery_id'=>$arr['id']]);
        $this->assign('list',$list);
        return view();
    }

    //我的收藏
    public function myCollection()
    {
        session('location',input('location'));
        $list = MyGallery::all(['collect'=>'1','status'=>'1']);
        $this->assign('list',$list);
        return view();
    }
    public function isCollected()
    {

        $gallery_id = input('gallery_id');
        $gallery = MyGallery::get($gallery_id);
        if (empty($gallery)||$gallery->status['val']=='0'){
            $this->error('无权访问',url('index/index/myCollection','location=我的收藏'));
        }
        if ($gallery->getData('collect') != 0) {
            $gallery->collect = '0';
            $result = $gallery->save();
            if ($result) {
                $arr = [
                    'id'=>session('user.id'),
                    'gallery_id'=>$gallery_id
                ];
                $collection =  new Collection();
                $collection->where($arr)->delete();

                $this->success('取消收藏成功');
            } else {
                $this->error('取消收藏失败');
            }
        } else {
            $gallery->collect= '1';
            $result = $gallery->save();
            if ($result) {
                $arr = [
                    'id'=>session('user.id'),
                    'gallery_id'=>$gallery_id
                ];
                $collection =  new Collection();
                $collection->id = $arr['id'];
                $collection->gallery_id = $arr['gallery_id'];
                $collection->save();
                $this->success('收藏成功');
            } else {
                $this->error('收藏失败');
            }
        }
    }

}
