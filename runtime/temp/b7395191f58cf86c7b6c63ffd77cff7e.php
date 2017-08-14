<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:74:"/home/wwwroot/default/public/../application/admin/view/pub/update_pwd.html";i:1482736183;s:66:"/home/wwwroot/default/public/../application/admin/view/header.html";i:1483770727;}*/ ?>
<!doctype html>
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
<div class="layui-main">
    <fieldset class="layui-elem-field layui-field-title">
        <legend>修改密码</legend>
    </fieldset>
    <form class="layui-form" action="" method="post">
        <div class="layui-form-item">
            <label class="layui-form-label Validform_label">旧密码</label>
            <input type="password" name="old_password" class="layui-input layui-width-300" datatype="*6-16" errormsg="密码范围在6~16位之间！">
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label Validform_label">新密码</label>
            <input type="password" name="new_password" class="layui-input layui-width-300" datatype="*6-16" errormsg="密码范围在6~16位之间！">
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label Validform_label">确认新密码</label>
            <input type="password" name="new_password2" class="layui-input layui-width-300" datatype="*" recheck="new_password">
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn">立即提交</button>
            </div>
        </div>
    </form>
</div>
<script>
    layui.use(['validform'], function () {
        var $ = layui.jquery;

        //表单验证
        $("form").Validform({
            tiptype: 3
        });
    });
</script>
</body>
</html>