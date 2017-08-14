<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

 Route::rule('admin-login', 'admin/Pub/login');//管理员登录
Route::rule('company-login', 'admin/Pub/company');//公司登录
Route::rule('upload-image', 'admin/Upload/image');//上传图片
Route::rule('upload-editor', 'admin/Upload/editor');//上传图片
Route::rule('register/[:identifier]', 'api/Wx/distribution');//推广
Route::rule('pic/:u/:i', 'api/Wx/getImage');//访问私密图片
Route::rule('document/:id', 'api/Document/detail');//文档

Route::rule('api/Wx/lucky_mySpoilList', 'api/Lucky/mySpoilList');
Route::rule('api/Wx/lucky_spoilList', 'api/Lucky/spoilList');
Route::rule('api/Wx/lucky_spoilGoodsOpenInfo', 'api/Lucky/spoilGoodsOpenInfo');
Route::rule('api/Wx/lucky_spoilGoodsDetail', 'api/Lucky/spoilGoodsDetail');
Route::rule('api/Wx/lucky_buyLuckyGoods', 'api/Lucky/buyLuckyGoods');
Route::rule('api/Wx/lucky_companyHomeList', 'api/Lucky/companyHomeList');
Route::rule('api/Wx/lucky_specialDraw', 'api/Lucky/specialDraw');
Route::rule('api/Wx/lucky_myBuyLuckyGoodsList', 'api/Lucky/myBuyLuckyGoodsList');
Route::rule('api/Wx/lucky_spoilHome', 'api/Lucky/spoilHome');
Route::rule('api/Wx/lucky_share', 'api/Lucky/share');
Route::rule('api/Wx/lucky_prize', 'api/Lucky/luckyPrize');  
Route::rule('onlinePayNotify', 'api/Lucky/onlinePayNotify');  
