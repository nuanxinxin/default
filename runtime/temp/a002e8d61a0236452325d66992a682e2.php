<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:82:"D:\project\default\public/../application/admin\view\company_order\dailyrecord.html";i:1501665241;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
    <style>
        .fee-total span {
            display: inline-block;
            height: 50px;
            line-height: 50px;
        }

        .fee-total span a {
            color: #FF5722 !important;
            cursor: default;
        }
    </style>
</head>
<body>
<script src="__LAYUI__/loading.js"></script>
<div class="layui-main">
    

    <table class="table">
        <thead>
        <tr>
        	<th>交易时间</th>
        	<?php if(LOGIN_TYPE == 'admin'): ?>
        	<th>平台交易总金额</th>
            
            <?php else: ?>
            <th>交易金额</th>
              <?php endif; ?>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>

        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td><?php echo $item->days; ?></td>
            <td><?php echo sprintf('%.2f',$item->total_price); ?>元</td>
            <td><a href="javascript:;" class="layui-btn layui-btn-danger layui-btn-mini status" data-id="<?php echo $item->days; ?>">订单详情</a></td>
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
    $(".status").click(function () {
        var id = $(this).data("id");
        layui.base().fullBox({
            title: "详情",
            content: "<?php echo path('detail'); ?>?id=" + id,
            end: function () {
                window.location.reload();
            }
        });
    });

});
    layui.use(['base', 'form']);
</script>
</body>
</html>