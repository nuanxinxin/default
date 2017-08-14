<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:83:"D:\project\default\public/../application/admin\view\luck_draw\prize_goods_form.html";i:1496387016;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
        <form action="" method="post" class="layui-form">
            <div class="layui-form-item">
                <div class="label-title Validform_label">商品标题</div>
                <div><input type="text" name="title" value="<?php echo $data->title; ?>" class="layui-input" datatype="*">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">奖品详情</div>
                <div>
                    <textarea class="layui-textarea" name="detail" style="width:360px;"><?php echo $data->detail; ?></textarea>
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">图片</div>
                <div class="image_upload" data-name="thumb" data-value="<?php echo $data->thumb; ?>"></div>
                <span style="color:red;">图片比例必须1:1</span>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">奖品类型</div>
                <div>
                    <input type="radio" name="spoil_type" value="0" title="竞彩奖" checked>
                    <input type="radio" name="spoil_type" value="1" title="安慰奖" <?php echo !empty($data->spoil_type)?'checked':''; ?>>
                    <input type="radio" name="spoil_type" value="2" title="幸运奖" <?php echo !empty($data->spoil_type)?'checked':''; ?>>
                </div>
            </div>
            <div class="layui-form-item b-t-s">
                <input type="hidden" name="spoil_id" value="<?php echo $data->spoil_id; ?>">
                <button type="submit" class="layui-btn">提交</button>
                <?php if($data): ?>
                <button type="button" class="layui-btn layui-btn-primary" onclick="window.history.back()">返回</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<script>
    layui.use(['base', 'form', 'validform'], function () {
        var $ = layui.jquery;

        //表单验证
        $("form").Validform({
            tiptype: 3
        });
    });
</script>
</body>
</html>