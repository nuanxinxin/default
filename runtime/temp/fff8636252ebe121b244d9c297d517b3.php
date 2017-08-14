<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:78:"/home/wwwroot/default/public/../application/admin/view/luck_draw/activity.html";i:1496901406;s:66:"/home/wwwroot/default/public/../application/admin/view/header.html";i:1483770727;s:67:"/home/wwwroot/default/public/../application/admin/view/loading.html";i:1481350888;}*/ ?>
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
            <th>标题</th>
            <th>封面图</th>
            <th>参与价格</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>创建时间</th>
            <th>状态</th>
            <th>结果</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td><?php echo $item->goods_title; ?></td>
            <td><img src="<?php echo $item->page_thumb; ?>" style="max-width:100px;max-height:100px;"></td>
            <td><?php echo $item->single_price; ?></td>
            <td><?php echo date('Y-m-d H:i:s',$item->start_time); ?></td>
            <td><?php echo date('Y-m-d H:i:s',$item->end_time); ?></td>
            <td><?php echo $item->create_time; ?></td>
            <td><?php echo $item->isOutText[$item->is_out]; ?></td>
            <td><?php echo $item->resultText[$item->result]; ?></td>
            <td>
                <a href="<?php echo path('activityCopy',['goods_id'=>$item->goods_id]); ?>" class="layui-btn layui-btn-mini layui-btn-warm">
                    复制活动
                </a>
                <?php if($item->result == 0): ?>
                <a href="<?php echo path('activityEdit',['goods_id'=>$item->goods_id]); ?>" class="layui-btn layui-btn-mini layui-btn-normal">
                编辑
                </a>
                <?php endif; ?>
                <a href="javascript:;" data-id="<?php echo $item->goods_id; ?>" class="layui-btn layui-btn-mini layui-btn-danger del">
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
                window.location.href = "<?php echo path('activityDel'); ?>?goods_id=" + id;
            });
        });
    });
</script>
</body>
</html>