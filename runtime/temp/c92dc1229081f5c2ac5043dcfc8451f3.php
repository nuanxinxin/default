<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:85:"D:\PHP\wamp\www\default\public/../application/admin\view\company_order\orderlist.html";i:1502247337;s:68:"D:\PHP\wamp\www\default\public/../application/admin\view\header.html";i:1483770726;s:69:"D:\PHP\wamp\www\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
          <a href="javascript:;">已结算：<?php echo sprintf('%.2f',$totalSettle); ?>元</a>
          <a href="javascript:;">未结算：<?php echo sprintf('%.2f',$totalSettleNo); ?>元</a>
           <a href="javascript:;">可结算：<?php echo sprintf('%.2f',$canTotalSettleNo); ?>元</a>
          <a href="javascript:;">今日交易金额：<?php echo sprintf('%.2f',$todayTradeNo); ?>元</a>
          <?php if(LOGIN_TYPE == 'admin'): ?>
           <a href="javascript:;">手续费：<?php echo sprintf('%.2f',$totalCommission); ?>元</a>
           <else/>
           <a href="javascript:;">手续费：0.008</a>
          <?php endif; if(LOGIN_TYPE != 'admin'): ?>
              <button type="button" class="layui-btn layui-btn-danger layui-btn-mini"
                       onclick="window.location.href='<?php echo path('orderSettle'); ?>'">提现
              </button>
         <?php endif; ?>
         
          </a>
        </span>
        <span>T1提现</span>
    </div>
    <fieldset class="layui-elem-field">
        <legend>查询搜索</legend>
        <div class="layui-field-box">
            <form action="" method="get" class="layui-form">
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
                <div class="layui-input-inline">
                    <select name="status">
                        <option value="">支付状态</option>
                        <option value="全部">全部</option>
                        <option value="0" <?php echo \think\Request::instance()->param('status')=='0'?'selected':''; ?>>等待支付</option>
                        <option value="1" <?php echo \think\Request::instance()->param('status')=='1'?'selected':''; ?>>已支付</option>
                    </select>
                </div>
                 <div class="layui-input-inline">
                    <select name="settle">
                        <option value="">结算状态</option>
                        <option value="全部">全部</option>
                        <option value="0" <?php echo \think\Request::instance()->param('settle')=='0'?'selected':''; ?>>等待结算</option>
                        <option value="1" <?php echo \think\Request::instance()->param('settle')=='1'?'selected':''; ?>>已结算</option>
                    </select>
                </div>
                <?php if(LOGIN_TYPE == 'admin'): ?>
                <div class="layui-input-inline">
                    <select name="channel">
                        <option value="">选择支付渠道</option>
						<option value="wlb" <?php echo \think\Request::instance()->param('channel')=='wlb'?'selected':''; ?>>微联宝(支付宝)</option>
						<option value="JHF" <?php echo \think\Request::instance()->param('channel')=='JHF'?'selected':''; ?>>聚合富(微信)</option>
						<option value="HF" <?php echo \think\Request::instance()->param('channel')=='HF'?'selected':''; ?>>恒丰(支付宝)</option>
                    </select>
                </div>
                <?php endif; ?>
                <div class="layui-input-inline">
                    <input type="text" name="order_id" value="<?php echo \think\Request::instance()->param('order_id'); ?>" class="layui-input"
                           placeholder="订单号查询">
                </div>
                <button type="submit" class="layui-btn">提交</button>
            </form>
        </div>
    </fieldset>
    <table class="table">
        <thead>
        <tr>
        	<th>订单号</th>
            <th>订单金额</th>
            <th>创建时间</th>
            <th>支付时间</th>
           <?php if(LOGIN_TYPE == 'admin'): ?>
            <th>公司名</th>
            <?php endif; ?>
            <th>状态</th>
             <th>结算状态</th>
             <?php if(LOGIN_TYPE == 'admin'): ?>
            <th>使用通道</th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>

        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td><?php echo $item->order_id; ?></td>
            <td><?php echo $item->amount; ?>元</td>
            <td><?php echo date('Y-m-d H:i:s',$item->create_time); ?></td>
             <td><?php if(empty($item->update_time) || (($item->update_time instanceof \think\Collection || $item->update_time instanceof \think\Paginator ) && $item->update_time->isEmpty())): ?>未支付<?php else: ?><?php echo date('Y-m-d H:i:s',$item->update_time); endif; ?></td>
           <?php if(LOGIN_TYPE == 'admin'): ?>
            <th><?php echo $item->company->company_name; ?></th>
            <?php endif; ?>
            <td><?php if($item->status == 0): ?><span style="color:#f90707">等待支付</span><?php else: ?><span style="color:#15af40">支付成功</span><?php endif; ?></td>
            <td><?php if($item->settle == 0): ?><span style="color:#f90707">未结算</span><?php else: ?><span style="color:#15af40">已结算</span><?php endif; ?></td>
       		<?php if(LOGIN_TYPE == 'admin'): ?>
       		<td><?php switch($item->channel): case "wlb": ?>微联宝(支付宝)<?php break; case "JHF": ?>聚合富(微信)<?php break; case "HF": ?>恒丰(支付宝)<?php break; endswitch; ?></td>	
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
    layui.use(['base', 'form']);
</script>
</body>
</html>