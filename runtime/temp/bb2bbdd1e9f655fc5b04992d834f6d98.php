<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:74:"/home/wwwroot/default/public/../application/admin/view/document/slide.html";i:1484212632;s:66:"/home/wwwroot/default/public/../application/admin/view/header.html";i:1483770727;s:67:"/home/wwwroot/default/public/../application/admin/view/loading.html";i:1481350888;}*/ ?>
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
        <legend>首页轮播图</legend>
    </fieldset>
    <blockquote class="layui-elem-quote">
        <p style="color:#FF5722;">请确保图片大小保持一致</p>
    </blockquote>
    <form class="layui-form" action="" method="post">
        <table class="table">
            <thead>
            <tr>
                <th>图片</th>
                <th>链接</th>
                <th>状态</th>
            </tr>
            </thead>
            <tbody>
            <?php $__FOR_START_554070962__=0;$__FOR_END_554070962__=5;for($i=$__FOR_START_554070962__;$i < $__FOR_END_554070962__;$i+=1){ ?>
            <tr>
                <td>
                    <div class="image_upload" data-name="pic[]" data-value="<?php echo $data['pic'][$i]; ?>"></div>
                </td>
                <td>
                    <input type="text" name="url[]" value="<?php echo $data['url'][$i]; ?>" class="layui-input" placeholder="http://">
                </td>
                <td>
                    <input type="checkbox" title="应用" name="is_show[<?php echo $i; ?>]" value="1" <?php if(!empty($data['is_show'][$i])) echo checked; ?> class="layui-input">
                </td>
            </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">
                    <?php echo token(); ?>
                    <button type="submit" class="layui-btn">提交</button>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<script>
    layui.use(['base','form']);
</script>
</body>
</html>