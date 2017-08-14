<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:70:"D:\project\default\public/../application/admin\view\document\form.html";i:1482914432;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
        <legend>编辑文档</legend>
    </fieldset>
    <div class="layui-field-box">
        <form action="<?php echo path('edit',['id'=>$data->id]); ?>" method="post" class="layui-form">
            <div class="layui-form-item">
                <div class="label-title Validform_label">标题</div>
                <div><input type="text" name="title" value="<?php echo $data->title; ?>" class="layui-input" datatype="s2-15">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">图片</div>
                <div class="image_upload" data-name="pic" data-value="<?php echo $data->pic; ?>"></div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">内容</div>
                <div>
                    <textarea name="content" id="editor"><?php echo $data->content; ?></textarea>
                </div>
            </div>
            <div class="layui-form-item b-t-s">
                <?php echo token(); ?>
                <button type="submit" class="layui-btn">提交</button>
                <?php if($data): ?>
                <button type="button" class="layui-btn layui-btn-primary" onclick="window.history.back()">返回</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="/static/simditor/styles/simditor.css"/>
<script type="text/javascript" src="/static/simditor/scripts/jquery.min.js"></script>
<script type="text/javascript" src="/static/simditor/scripts/module.js"></script>
<script type="text/javascript" src="/static/simditor/scripts/hotkeys.js"></script>
<script type="text/javascript" src="/static/simditor/scripts/uploader.js"></script>
<script type="text/javascript" src="/static/simditor/scripts/simditor.js"></script>
<script>
    layui.use(['base', 'validform'], function () {
        var $ = layui.jquery;
        var editor = new Simditor({
            textarea: $('#editor'),
            toolbar: ['title', 'bold', 'italic', 'underline', 'strikethrough', 'fontScale', 'color', 'ol', 'ul', 'blockquote', 'table', 'link', 'image', 'hr', 'indent', 'outdent', 'alignment'],
            allowedAttributes: {
                img: ['src', 'alt', 'width', 'height', 'data-non-image'],
                a: ['href', 'target'],
                font: ['color'],
                code: ['class']
            },
            defaultImage: '/static/simditor/images/image.png',
            upload: {
                url: '/upload-editor',
                params: null,
                fileKey: 'file',
                connectionCount: 1,
                leaveConfirm: '正在上传图片'
            }
        });
        //表单验证
        $("form").Validform({
            tiptype: 3
        });
    });
</script>
</body>
</html>