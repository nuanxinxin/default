<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:69:"D:\project\default\public/../application/admin\view\member\index.html";i:1493198158;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
        <legend>用户列表</legend>
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
                    <select name="auth_status">
                        <option value="">认证状态</option>
                        <option value="全部">全部</option>
                        <option value="审核中" <?php echo \think\Request::instance()->param('auth_status')=='审核中'?'selected':''; ?>>审核中</option>
                        <option value="已通过" <?php echo \think\Request::instance()->param('auth_status')=='已通过'?'selected':''; ?>>已通过</option>
                        <option value="认证失败" <?php echo \think\Request::instance()->param('auth_status')=='认证失败'?'selected':''; ?>>认证失败</option>
                    </select>
                </div>
                <button type="submit" class="layui-btn">提交</button>
            </form>
        </div>
    </fieldset>
    <table class="table">
        <thead>
        <tr>
            <th>昵称</th>
            <th>姓名</th>
            <th>手机号</th>
            <th>城市</th>
            <th>信用币</th>
            <th>当前交易金额</th>
            <th>所属公司</th>
            <th>认证状态</th>
            <th>注册时间</th>
            <th>支付认证费订单号</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td><?php echo $item->nickname; ?></td>
            <td><?php echo $item->AuthInfo->name; ?></td>
            <td><?php echo $item->phone; ?></td>
            <td><?php echo $item->city; ?></td>
            <td><?php echo $item->credit_money; ?></td>
            <td><?php echo $item->current_tool_money; ?></td>
            <td><?php echo $item->AdminCompany->company_name; ?></td>
            <td><?php echo $item->AuthInfo->auth_status; ?></td>
            <td><?php echo date('Y-m-d',$item->register_time); ?></a></td>
            <td><?php echo !empty($item->AuthInfo->order_id)?$item->AuthInfo->order_id:'未支付'; ?></td>
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
                title: "查看用户信息",
                content: "<?php echo path('Pub/tab'); ?>?op=用户信息,Member/user?id=" + id + ";认证信息,Member/auth?user_id=" + id,
                end: function () {
                    window.location.reload();
                }
            });
        });
    });
</script>
</body>
</html>