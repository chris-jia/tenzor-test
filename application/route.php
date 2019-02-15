<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;//保证每一个url都要按要求访问
Route::rule('/','index/index/login','get|post');
Route::rule('register','index/index/register','get|post');
Route::rule('home/:location','index/index/home','get|post');
Route::rule('myGallery/:location','index/index/myGallery','get|post');
Route::rule('addMyGallery','index/index/addMyGallery','get|post');
Route::rule('photo/:gallery_id','index/index/photo','get|post');//已验证
Route::rule('addPhoto','index/index/addPhoto','get|post');
Route::rule('gallerySquare/:location','index/index/gallerySquare','get|post');
Route::rule('showPhoto/:gallery_id','index/index/showPhoto','get|post');//已验证
Route::rule('editGallery/:gallery_id','index/index/editGallery','get|post');//已验证
Route::rule('deleteGallery/:gallery_id','index/index/deleteGallery','get|post');//已验证
Route::rule('release/:gallery_id','index/index/release','get|post');//已验证
Route::rule('deletePhoto/:photo_id','index/index/deletePhoto','get|post');//已验证
Route::rule('editPhotoName/:photo_id','index/index/editPhotoName','get|post');//已验证
Route::rule('myCollection/:location','index/index/myCollection','get|post');
Route::rule('isCollected/:gallery_id','index/index/isCollected','get|post');//已验证
Route::rule('logoff','index/index/logoff','get|post');









