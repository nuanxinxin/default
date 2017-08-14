<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:82:"/home/wwwroot/default/public/../application/admin/view/luck_draw/spoil_record.html";i:1495679306;s:66:"/home/wwwroot/default/public/../application/admin/view/header.html";i:1483770727;s:67:"/home/wwwroot/default/public/../application/admin/view/loading.html";i:1481350888;}*/ ?>
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
            <th>活动标题</th>
            <th>奖品</th>
            <th>中奖人</th>
            <th>联系电话</th>
            <th>奖品类型</th>
            <th>兑奖状态</th>
            <th>兑奖时间</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td><?php echo $item->goods_title; ?></td>
            <td><img src="<?php echo $item->goods_spoil->thumb; ?>" style="width:50px;height:50px;margin-right:10px;"><?php echo $item->goods_spoil->title; ?></td>
            <td><?php echo $item->nickname; ?></td>
            <td><?php echo $item->phone; ?></td>
            <td><?php echo $item->spoilTypeText[$item->spoil_type]; ?></td>
            <td><?php echo $item->ticketStatusText[$item->ticket_status]; ?></td>
            <td><?php echo $item->ticket_time; ?></td>
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
                window.location.href = "<?php echo path('activityDel'); ?>?goods_id=" + id;
            });
        });
    });
</script>
</body>
</html>