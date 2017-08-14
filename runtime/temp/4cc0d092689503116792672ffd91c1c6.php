<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:73:"/home/wwwroot/default/public/../application/admin/view/company/index.html";i:1495089510;s:66:"/home/wwwroot/default/public/../application/admin/view/header.html";i:1483770727;s:67:"/home/wwwroot/default/public/../application/admin/view/loading.html";i:1481350888;}*/ ?>
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
        <legend>公司管理</legend>
    </fieldset>
    <fieldset class="layui-elem-field">
        <legend>查询搜索</legend>
        <div class="layui-field-box">
            <form action="" method="get">
                <input type="text" name="keyword" value="<?php echo \think\Request::instance()->param('keyword'); ?>" class="layui-input"
                       placeholder="账号或公司名称">
                <button type="submit" class="layui-btn">提交</button>
            </form>
        </div>
    </fieldset>
    <table class="table">
        <thead>
        <tr>
            <th class="number">Id</th>
            <th>账号</th>
            <th>公司名称</th>
            <th>最低费率</th>
            <th>自营费率</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>

        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td class="number"><?php echo $item->id; ?></td>
            <td><?php echo $item->username; ?></td>
            <td><?php echo $item->company_name; ?></td>
            <td><?php echo $item->min_rate; ?></td>
            <td><?php echo $item->rate; ?></td>
            <td>
                <a href="<?php echo path('edit',['id'=>$item->id]); ?>" class="layui-btn layui-btn-mini layui-btn-normal">
                    编辑
                </a>
                <?php if($item->id == 1): ?>
                <!--<a href="javascript:;" class="layui-btn layui-btn-mini layui-btn-disabled">-->
                    <!--删除-->
                <!--</a>-->
                <?php else: ?>
                <!--<a href="<?php echo path('delete',['id'=>$item->id]); ?>" class="layui-btn layui-btn-mini layui-btn-danger del">-->
                    <!--删除-->
                <!--</a>-->
                <?php endif; if($item->balance): ?>
                <a href="javascript:;" class="layui-btn layui-btn-danger layui-btn-mini balance" data-id="<?php echo $item->id; ?>">结算</a>
                <?php endif; ?>
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
    layui.use('base', function () {
        var $ = layui.jquery;
        $('.balance').click(function () {
            var id = $(this).data('id');
            layer.confirm('操作确认?', {
                title: '安全提示',
                icon: 3,
                btn: ['确定', '取消']
            }, function () {
                window.location.href = "<?php echo path('balance'); ?>?id=" + id;
            });
        });
    });
</script>
</body>
</html>