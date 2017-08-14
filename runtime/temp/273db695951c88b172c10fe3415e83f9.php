<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:79:"/home/wwwroot/default/public/../application/admin/view/document/web_config.html";i:1488365717;s:66:"/home/wwwroot/default/public/../application/admin/view/header.html";i:1483770727;s:67:"/home/wwwroot/default/public/../application/admin/view/loading.html";i:1481350888;}*/ ?>
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
    <fieldset class="layui-elem-field layui-field-title">
        <legend>站点配置</legend>
    </fieldset>
    <div class="layui-field-box">
        <form action="<?php echo path('webConfig'); ?>" method="post" class="layui-form">
            <div class="layui-form-item">
                <div class="label-title Validform_label">开通城市</div>
                <div><input type="text" name="open_city" value="<?php echo $data['open_city']; ?>" class="layui-input" datatype="*">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="label-title Validform_label">公告信息</div>
                <div><input type="text" name="notice_message" value="<?php echo $data['notice_message']; ?>" class="layui-input" datatype="*">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="label-title Validform_label">提现条件（提现金额不能低于设置金额）</div>
                <div><input type="text" name="withdrawal_condition" value="<?php echo $data['withdrawal_condition']; ?>" class="layui-input" datatype="*">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="label-title Validform_label">通道返利</div>
                <div><input type="text" name="channel_rebate" value="<?php echo $data['channel_rebate']; ?>" class="layui-input" datatype="*">
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