<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:76:"D:\project\default\public/../application/admin\view\company\tool_record.html";i:1492744064;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
        <legend>交易记录</legend>
    </fieldset>
    <fieldset class="layui-elem-field">
        <legend>查询搜索</legend>
        <div class="layui-field-box">
            <form action="" method="get" class="layui-form">
                <div class="layui-input-inline">
                    <input type="text" name="keyword" value="<?php echo \think\Request::instance()->param('keyword'); ?>" class="layui-input"
                           placeholder="手机号">
                </div>
                <div class="layui-input-inline">
                    <select name="company_id">
                        <option value="">选择公司</option>
                        <?php if(is_array($companys) || $companys instanceof \think\Collection || $companys instanceof \think\Paginator): if( count($companys)==0 ) : echo "" ;else: foreach($companys as $key=>$item): ?>
                        <option value="<?php echo $item->id; ?>" <?php echo \think\Request::instance()->param('company_id')==$item->id?'selected':''; ?>><?php echo $item->company_name; ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="user_in_come_status">
                        <option value="全部">全部状态</option>
                        <option value="未到账" <?php echo \think\Request::instance()->param('user_in_come_status')=='未到账'?'selected':''; ?>>未到账</option>
                        <option value="已到账" <?php echo \think\Request::instance()->param('user_in_come_status')=='已到账'?'selected':''; ?>>已到账</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="payment">
                        <option value="全部">全部支付方式</option>
                        <option value="微信" <?php echo \think\Request::instance()->param('payment')=='微信'?'selected':''; ?>>微信</option>
                        <option value="支付宝" <?php echo \think\Request::instance()->param('payment')=='支付宝'?'selected':''; ?>>支付宝</option>
                        <option value="京东" <?php echo \think\Request::instance()->param('payment')=='京东'?'selected':''; ?>>京东</option>
                    </select>
                </div>
                <button type="submit" class="layui-btn">提交</button>
            </form>
        </div>
    </fieldset>
    <table class="table">
        <thead>
        <tr>
            <th>所属公司</th>
            <th>手机号</th>
            <th>类型</th>
            <th>金额（元）</th>
            <th>交易时间</th>
            <th>平台到账状态</th>
            <th>用户到账状态</th>
            <th>支付订单号</th>
            <th>支付方式</th>
        </tr>
        </thead>
        <tbody>

        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td><?php echo $item->AdminCompany->company_name; ?></td>
            <td><?php echo $item->User->phone; ?></td>
            <td><?php echo $item->type; ?></td>
            <td><?php echo $item->money; ?></td>
            <td><?php echo date('Y-m-d H:i:s',$item->create_time); ?></td>
            <td><?php echo $item->admin_in_come_status; ?></td>
            <td><?php echo $item->user_in_come_status; ?></td>
            <td><?php echo $item->order_id; ?></td>
            <td><?php echo $item->payment; ?></td>
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
    layui.use(['base','form']);
</script>
</body>
</html>