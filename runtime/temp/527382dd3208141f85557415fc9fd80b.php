<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:74:"D:\project\default\public/../application/admin\view\luck_draw\setting.html";i:1496306848;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
    <div class="layui-field-box">
        <form action="<?php echo path('setting'); ?>" method="post" class="layui-form">
            <div class="layui-form-item">
                <div class="label-title Validform_label">幸运抽奖条件</div>
                <div><input type="text" name="xycj_conditions_buy_count" value="<?php echo $data['xycj_conditions_buy_count']; ?>" class="layui-input" datatype="n">
                </div>
            </div>
            <div class="layui-form-item b-t-s">
                <?php echo token(); ?>
                <button type="submit" class="layui-btn">提交</button>
            </div>
        </form>
    </div>
</div>

<script>
    layui.use('validform', function () {
        var $ = layui.jquery;

        //表单验证
        $("form").Validform({
            tiptype: 3
        });
    });
</script>


</body>
</html>