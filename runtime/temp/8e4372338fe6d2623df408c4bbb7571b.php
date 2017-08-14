<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:74:"D:\project\default\public/../application/admin\view\luck_draw\tuijian.html";i:1496195848;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
    <fieldset class="layui-elem-field">
        <legend>幸运抽奖</legend>
        <div class="layui-field-box">
            <form action="" method="get" class="layui-form">
                <div class="layui-input-inline">
                    <input type="text" name="goods_title" value="<?php echo \think\Request::instance()->param('goods_title'); ?>" class="layui-input"
                           placeholder="标题">
                </div>
                <button type="submit" class="layui-btn">搜索</button>
            </form>
        </div>
    </fieldset>
    <table class="table">
        <thead>
        <tr>
            <th>标题</th>
            <th>封面图</th>
            <th>参与价格</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>

        <?php if(is_array($search) || $search instanceof \think\Collection || $search instanceof \think\Paginator): if( count($search)==0 ) : echo "" ;else: foreach($search as $key=>$item): ?>
        <tr>
            <td><?php echo $item->goods_title; ?></td>
            <td><img src="<?php echo $item->page_thumb; ?>" style="max-width:100px;max-height:100px;"></td>
            <td><?php echo $item->single_price; ?></td>
            <td><?php echo date('Y-m-d H:i:s',$item->start_time); ?></td>
            <td><?php echo date('Y-m-d H:i:s',$item->end_time); ?></td>
            <td><?php echo $item->isOutText[$item->is_out]; ?></td>
            <td>
                <?php if($item->is_recommend == 1): ?>
                <a href="javascript:;" class="layui-btn layui-btn-mini layui-btn-normal recommend"
                   data-id="<?php echo $item->goods_id; ?>" daa-is_recommend="0">
                    取消推荐
                </a>
                <?php else: ?>
                <a href="javascript:;" class="layui-btn layui-btn-mini layui-btn-warm recommend"
                   data-id="<?php echo $item->goods_id; ?>" data-is_recommend="1">
                    推荐
                </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>
</div>
<script>
    layui.use(['base', 'form'], function () {
        var $ = layui.jquery;
        var base = layui.base();
        $(".recommend").click(function () {
            var goods_id = $(this).data("id");
            var is_recommend = $(this).data("is_recommend");
            base.request.post({
                data: {goods_id: goods_id, is_recommend: is_recommend},
                success: function (res) {
                    if (res.code == 200) {
                        window.location.reload();
                    } else {
                        layer.msg(res.message);
                    }
                }
            });
        });
    });
</script>
</body>
</html>