<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:83:"D:\project\default\public/../application/admin\view\company_order\settledetail.html";i:1501666482;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
        	<th>订单号</th>
            <th>订单金额</th>
            <th>操作</th>
           <?php if(LOGIN_TYPE == 'admin'): ?>
            <th>公司名</th>
            <?php endif; ?>
            <th>结算状态</th>
        </tr>
        </thead>
        <tbody>

        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td><?php echo $item->order_id; ?></td>
            <td><?php echo sprintf('%.2f',$item->amount); ?>元</td>
            <td><?php echo date('Y-m-d H:i:s',$item->update_time); ?></td>
                       <?php if(LOGIN_TYPE == 'admin'): ?>
            <th><?php echo $item->company->company_name; ?></th>
            <?php endif; ?>
            <td><?php if($item->settle == 0): ?><span style="color:#f90707">未结算</span><?php else: ?><span style="color:#15af40">已结算</span><?php endif; ?></td>
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