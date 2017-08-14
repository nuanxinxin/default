<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:70:"/home/wwwroot/default/public/../application/admin/view/pawn/index.html";i:1486969569;s:66:"/home/wwwroot/default/public/../application/admin/view/header.html";i:1483770727;s:67:"/home/wwwroot/default/public/../application/admin/view/loading.html";i:1481350888;}*/ ?>
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
        <legend>典当信息</legend>
    </fieldset>
    <fieldset class="layui-elem-field">
        <legend>查询搜索</legend>
        <div class="layui-field-box">
            <form action="" method="get" class="layui-form">
                <div class="layui-input-inline">
                    <input type="text" name="keyword" value="<?php echo \think\Request::instance()->param('keyword'); ?>" class="layui-input"
                           placeholder="标题|描述">
                </div>
                <div class="layui-input-inline">
                    <select name="status">
                        <option value="">状态</option>
                        <option value="全部">全部</option>
                        <?php $_58a6d8183dbd2=config('pawn_status'); if(is_array($_58a6d8183dbd2) || $_58a6d8183dbd2 instanceof \think\Collection || $_58a6d8183dbd2 instanceof \think\Paginator): if( count($_58a6d8183dbd2)==0 ) : echo "" ;else: foreach($_58a6d8183dbd2 as $key=>$item): ?>
                        <option value="<?php echo $item; ?>" <?php echo \think\Request::instance()->param('status')==$item?'selected':''; ?>><?php echo $item; ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <button type="submit" class="layui-btn">提交</button>
            </form>
        </div>
    </fieldset>
    <table class="table">
        <thead>
        <tr>
            <th class="number">Id</th>
            <th>标题</th>
            <th>类型</th>
            <th>购买时间</th>
            <th>购买价格</th>
            <th>寄售价格</th>
            <th>联系电话</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>

        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td class="number"><?php echo $item->id; ?></td>
            <td><?php echo $item->title; ?></td>
            <td><?php echo $item->pawn_type; ?></td>
            <td><?php echo $item->buy_time; ?></td>
            <td><?php echo $item->buy_price; ?></td>
            <td><?php echo $item->sale_price; ?></td>
            <td><?php echo $item->contact_phone; ?></td>
            <td><?php echo $item->status; ?></td>
            <td>
                <a href="javascript:;" data-id="<?php echo $item->id; ?>" class="layui-btn layui-btn-mini layui-btn-normal detail">
                    详情
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
        $(".detail").click(function () {
            var id = $(this).data("id");
            layui.base().fullBox({
                title: "典当信息",
                content: "<?php echo path('detail'); ?>?id=" + id,
                end: function () {
                    window.location.reload();
                }
            });
        });
    });
</script>
</body>
</html>