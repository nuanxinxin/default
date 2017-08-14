<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:71:"/home/wwwroot/default/public/../application/admin/view/member/user.html";i:1482804150;s:66:"/home/wwwroot/default/public/../application/admin/view/header.html";i:1483770727;s:67:"/home/wwwroot/default/public/../application/admin/view/loading.html";i:1481350888;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
<title></title>
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<!--<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">-->
<link rel="stylesheet" href="__LAYUI__/css/layui.css" media="all">
<link rel="stylesheet" href="__LAYUI__/css/style.css?<?php echo time(); ?>" media="all">
<link rel="stylesheet" href="//at.alicdn.com/t/font_gmdaa4vgna0s5rk9.css" media="all">
<script src="__LAYUI__/layui.js" charset="utf-8"></script>
<script>
    layui.config({
        base: '__LAYUI__/lay/extend/',
        debug: false
    });
</script>
</head>
<body>
<script src="__LAYUI__/loading.js"></script>
<div class="layui-main">
    <blockquote class="layui-elem-quote user">
        <div class="row-info">
            <span>Id</span>
            <span><?php echo $data->id; ?></span>
        </div>
        <div class="row-info">
            <span>手机号</span>
            <span><?php echo $data->phone; ?></span>
        </div>
        <div class="row-info">
            <span>所属公司</span>
            <span><?php echo $data->AdminCompany->company_name; ?></span>
        </div>
        <div class="row-info">
            <span>城市</span>
            <span><?php echo $data->city; ?></span>
        </div>
        <div class="row-info">
            <span>注册时间</span>
            <span><?php echo date('Y-m-d',$data->register_time); ?></span>
        </div>
        <div class="row-info">
            <span>信用币</span>
            <span><?php echo $data->credit_money; ?></span>
        </div>
        <div class="row-info">
            <span>当前交易金额</span>
            <span><?php echo $data->current_tool_money; ?></span>
        </div>
        <div class="row-info">
            <span>标识符</span>
            <span><?php echo $data->identifier; ?></span>
        </div>
    </blockquote>
</div>
</body>
</html>