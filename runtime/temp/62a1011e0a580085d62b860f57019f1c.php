<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:67:"/home/wwwroot/default/public/../application/admin/view/pub/tab.html";i:1482805778;s:66:"/home/wwwroot/default/public/../application/admin/view/header.html";i:1483770727;}*/ ?>
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
<div class="layui-main">
    <div class="layui-tab layui-tab-brief" lay-filter="navigate" style="height:calc(100% - 20px);margin:0;">
        <ul class="layui-tab-title">
            <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): if($key == 0): ?>
            <li class="layui-this" data-url="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></li>
            <?php else: ?>
            <li class="" data-url="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></li>
            <?php endif; endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <iframe width="100%" style="height:calc(100% - 60px);margin-top:10px;" src="<?php echo $list[0]['url']; ?>" frameborder="0" seamless></iframe>
    </div>
</div>

<script>
    layui.use(['layer', 'element'], function () {
        var element = layui.element();
        var $ = layui.jquery;
        element.on('tab(navigate)', function () {
            $("iframe").attr("src", $(this).data("url"));
        });
    });
</script>

</body>
</html>