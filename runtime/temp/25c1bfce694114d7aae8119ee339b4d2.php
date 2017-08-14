<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:74:"D:\project\default\public/../application/admin\view\member\collection.html";i:1499328677;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
        <legend>提现列表</legend>
    </fieldset>
    <table class="table">
        <thead>
        <tr>
            <th>姓名</th>
            <th>手机号</th>
            <th>银行卡号</th>
            <th>银行名称</th>
            <th>金额</th>
            <th>时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>

        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td><?php echo $item->User->AuthInfo->name; ?></td>
            <td><?php echo $item->User->phone; ?></td>
            <td><?php echo $item->User->AuthInfo->bank_card_number; ?></td>
            <td><?php echo $item->User->AuthInfo->bank_name; ?></td>
            <td><?php echo $item->money; ?></td>
            <td><?php echo date('Y-m-d H:i',$item->create_time); ?></td>
            <td>
                <?php if($item->status == '到账中'): if($item->mer_state == 0): ?>
                <a href="javascript:;" data-id="<?php echo $item->id; ?>" class="layui-btn layui-btn-mini layui-btn-normal daifu">
                    银联代付
                </a>
                <a href="javascript:;" data-id="<?php echo $item->id; ?>" class="layui-btn layui-btn-mini layui-btn-danger reject">
                    提现驳回
                </a>
                <?php endif; if($item->mer_state == 1): ?>
                <a href="javascript:;" data-id="<?php echo $item->id; ?>" class="layui-btn layui-btn-mini layui-btn-normal detail">
                    提现确认
                </a>
                <?php endif; else: if($item->title == '提现驳回'): ?>提现驳回 <?php else: ?> 已处理<?php endif; endif; ?>
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
        $(".daifu").click(function () {
            var id = $(this).data('id');
            layer.confirm('银联代付确认?', {
                title: '安全提示',
                icon: 3,
                btn: ['确定', '取消']
            }, function () {
                window.location.href = "<?php echo path('daifu'); ?>?id=" + id;
            });
        });
        $(".detail").click(function () {
            var id = $(this).data('id');
            layer.confirm('操作确认?', {
                title: '安全提示',
                icon: 3,
                btn: ['确定', '取消']
            }, function () {
                window.location.href = "<?php echo path('collectionSub'); ?>?id=" + id;
            });
        });
        $(".reject").click(function () {
            var id = $(this).data('id');
            layer.confirm('确认驳回?', {
                title: '安全提示',
                icon: 3,
                btn: ['确定', '取消']
            }, function () {
                window.location.href = "<?php echo path('reject'); ?>?id=" + id;
            });
        });
        
    });
</script>
</body>
</html>