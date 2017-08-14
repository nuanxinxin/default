<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:67:"D:\project\default\public/../application/admin\view\loan\index.html";i:1487130558;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
        <legend>贷款信息</legend>
    </fieldset>
    <fieldset class="layui-elem-field">
        <legend>查询搜索</legend>
        <div class="layui-field-box">
            <form action="" method="get" class="layui-form">
                <div class="layui-input-inline">
                    <input type="text" name="keyword" value="<?php echo \think\Request::instance()->param('keyword'); ?>" class="layui-input"
                           placeholder="姓名|手机号|身份证">
                </div>
                <div class="layui-input-inline">
                    <select name="status">
                        <option value="">状态</option>
                        <option value="全部">全部</option>
                        <?php $_594b1c0b0924e=config('loan_status'); if(is_array($_594b1c0b0924e) || $_594b1c0b0924e instanceof \think\Collection || $_594b1c0b0924e instanceof \think\Paginator): if( count($_594b1c0b0924e)==0 ) : echo "" ;else: foreach($_594b1c0b0924e as $key=>$item): ?>
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
            <th>姓名</th>
            <th>电话</th>
            <th>性别</th>
            <th>身份证</th>
            <th>信用币价值</th>
            <th>状态</th>
            <th>添加时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>

        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td class="number"><?php echo $item->id; ?></td>
            <td><?php echo $item->name; ?></td>
            <td><?php echo $item->phone; ?></td>
            <td><?php echo $item->gender; ?></td>
            <td><?php echo $item->id_number; ?></td>
            <td><?php echo $item->credit_money; ?></td>
            <td><?php echo $item->status; ?></td>
            <td><?php echo date('Y-m-d',$item->add_time); ?></td>
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
                title: "贷款信息",
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