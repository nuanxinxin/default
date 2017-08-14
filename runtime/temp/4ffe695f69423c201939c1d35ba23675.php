<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:80:"D:\project\default\public/../application/admin\view\company_home\auth_incom.html";i:1487146124;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
    <div class="fee-total">
        <span class="layui-breadcrumb" lay-separator="|">
          <a href="javascript:;">推广总收益：<?php echo sprintf('%.2f',$totalAuthFeeDistribution); ?>元</a>
          <a href="javascript:;">已结算：<?php echo sprintf('%.2f',$totalAuthFeeDistributionOk); ?>元</a>
          <a href="javascript:;">未结算：<?php echo sprintf('%.2f',$totalAuthFeeDistributionNo); ?>元
              <button type="button" class="layui-btn layui-btn-danger layui-btn-mini"
                      onclick="window.location.href='<?php echo path('balance'); ?>'">结算
              </button>
          </a>
            <a href="javascript:;">待结算：<?php echo sprintf('%.2f',$totalAuthFeeDistributionWait); ?>元</a>
        </span>
    </div>
    <fieldset class="layui-elem-field">
        <legend>查询搜索</legend>
        <div class="layui-field-box">
            <form action="" method="get" class="layui-form">
                <div class="layui-input-inline">
                    <select name="status">
                        <option value="">结算状态</option>
                        <option value="全部">全部</option>
                        <option value="未结算" <?php echo \think\Request::instance()->param('status')=='未结算'?'selected':''; ?>>未结算</option>
                        <option value="已结算" <?php echo \think\Request::instance()->param('status')=='已结算'?'selected':''; ?>>已结算</option>
                        <option value="待结算" <?php echo \think\Request::instance()->param('status')=='待结算'?'selected':''; ?>>待结算</option>
                    </select>
                </div>
                <button type="submit" class="layui-btn">提交</button>
            </form>
        </div>
    </fieldset>
    <table class="table">
        <thead>
        <tr>
            <th>收益金额</th>
            <th>时间</th>
            <th>状态</th>
        </tr>
        </thead>
        <tbody>

        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td><?php echo $item->money; ?></td>
            <td><?php echo date('Y-m-d H:i:s',$item->add_time); ?></td>
            <td><?php echo $item->status; ?></td>
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
    layui.use(['base', 'form']);
</script>
</body>
</html>