<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:78:"D:\project\default\public/../application/admin\view\luck_draw\prize_goods.html";i:1495418214;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
    <table class="table">
        <thead>
        <tr>
            <th>奖品图片</th>
            <th>奖品标题</th>
            <th>奖品详情</th>
            <th>奖品类型</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td><img src="<?php echo $item->thumb; ?>" style="max-width:100px;max-height:100px;" onerror="this.src='/static/layui/images/prize.jpeg'"></td>
            <td><?php echo $item->title; ?></td>
            <td><?php echo $item->detail; ?></td>
            <td><?php echo $item->spoilTypeText[$item->spoil_type]; ?></td>
            <td>
                <!--<a href="<?php echo path('prizeGoodsEdit',['spoil_id'=>$item->spoil_id]); ?>" class="layui-btn layui-btn-mini layui-btn-normal">-->
                    <!--编辑-->
                <!--</a>-->
                <a href="javascript:;" data-id="<?php echo $item->spoil_id; ?>" class="layui-btn layui-btn-mini layui-btn-danger del">
                    删除
                </a>
            </td>
        </tr>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="99">
                <div class="pagination-box">
                    <span class="info">
                        共<?php echo $list->total(); ?>条数据 <?php echo $list->currentPage(); ?>/<?php echo $list->lastPage(); ?>页
                    </span>
                    <?php echo $list->render(); ?>
                </div>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<script>
    layui.use(['base', 'form'], function () {
        var $ = layui.jquery;

        $('.del').click(function () {
            var id = $(this).data('id');
            layer.confirm('操作确认?', {
                title: '安全提示',
                icon: 3,
                btn: ['确定', '取消']
            }, function () {
                window.location.href = "<?php echo path('prizeGoodsDel'); ?>?spoil_id=" + id;
            });
        });
    });
</script>
</body>
</html>