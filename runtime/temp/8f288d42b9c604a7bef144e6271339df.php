<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:81:"D:\project\default\public/../application/admin\view\company_order\settlelist.html";i:1501666856;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
    <fieldset class="layui-elem-field">
        <legend>查询搜索</legend>
        <div class="layui-field-box">
            <form action="" method="get" class="layui-form">
                <div class="layui-input-inline">
                    <select name="status">
                        <option value="">打款状态</option>
                        <option value="全部">全部</option>
                        <option value="0" <?php echo \think\Request::instance()->param('status')=='0'?'selected':''; ?>>等待打款</option>
                        <option value="1" <?php echo \think\Request::instance()->param('status')=='1'?'selected':''; ?>>确认大款</option>
                    </select>
                                     
                </div>
                 <?php if(LOGIN_TYPE == 'admin'): ?>
                <div class="layui-input-inline">
                    <select name="company_id">
                        <option value="">选择公司</option>
                        <?php if(is_array($companys) || $companys instanceof \think\Collection || $companys instanceof \think\Paginator): if( count($companys)==0 ) : echo "" ;else: foreach($companys as $key=>$item): ?>
                        <option value="<?php echo $item->id; ?>" <?php echo \think\Request::instance()->param('company_id')==$item->id?'selected':''; ?>><?php echo $item->company_name; ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <?php endif; ?>
                <button type="submit" class="layui-btn">提交</button>
            </form>
        </div>
    </fieldset>
    <table class="table">
        <thead>
        <tr>
        	<th>提现编号</th>
            <th>提现金额</th>
            <th>提现时间</th>
            <th>提现手续费</th>
           <?php if(LOGIN_TYPE == 'admin'): ?>
            <th>公司名</th>
            <?php endif; ?>
            <th>状态</th>
        </tr>
        </thead>
        <tbody>

        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td><?php echo $item->id; ?></td>
            <td><?php echo $item->price; ?>元</td>
            <td><?php echo date('Y-m-d H:i:s',$item->create_time); ?></td>
             <td><?php echo $item->commission; ?></td>
           <?php if(LOGIN_TYPE == 'admin'): ?>
            <th><?php echo $item->company->company_name; ?></th>
            <?php endif; if(LOGIN_TYPE == 'admin'): ?>
            <td><?php if($item->status == 0): ?><a href="javascript:;" class="layui-btn layui-btn-danger layui-btn-mini status" data-id="<?php echo $item->id; ?>">确认打款</a>
           <?php else: ?>&nbsp;&nbsp;已打款<?php endif; ?>
           &nbsp;&nbsp;<a href="javascript:;" class="layui-btn layui-btn-danger layui-btn-mini detail" data-id="<?php echo $item->id; ?>">提现订单详情</a>
            </td>
           <?php else: ?>
            <td><?php if($item->status == 0): ?>等待打款<?php else: ?>已打款<?php endif; ?><span style="width:10px;">&nbsp;</sapn><a href="javascript:;" class="layui-btn layui-btn-danger layui-btn-mini detail" data-id="<?php echo $item->id; ?>">提现订单详情</a></td>
            <?php endif; ?>
            
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
            title: "提现信息",
            content: "<?php echo path('settleStatus'); ?>?id=" + id,
            end: function () {
                window.location.reload();
            }
        });
    });
    $(".detail").click(function () {
        var id = $(this).data("id");
        layui.base().fullBox({
            title: "提现订单",
            content: "<?php echo path('settleDetail'); ?>?id=" + id,
            end: function () {
                window.location.reload();
            }
        });
    });
});
</script>
</body>
</html>