<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:68:"D:\project\default\public/../application/admin\view\index\index.html";i:1483943508;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:67:"D:\project\default\public/../application/admin\view\index\menu.html";i:1502417672;}*/ ?>
<!doctype html>
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
<div class="logo"><img src="__LAYUI__/images/logo.jpg" style="width:100%;height:73px;"></div>
<?php if(LOGIN_TYPE == 'admin'): ?>
<div class="disk">
    <div class="used" style="width:<?php echo round(150-150/disk_total_space('/')*disk_free_space('/')); ?>px"></div>
    <span><img src="__LAYUI__/images/disk.png"> <?php echo round(100-100/disk_total_space('/')*disk_free_space('/')); ?>% / <?php echo round(100/disk_total_space('/')*disk_free_space('/')); ?>%</span>
</div>
<?php endif; ?>
<div class="layui-tab" lay-filter="nav" id="frame">
    <ul class="layui-tab-title">
        <?php $_598d143bb01c3=config('menu.top'); if(is_array($_598d143bb01c3) || $_598d143bb01c3 instanceof \think\Collection || $_598d143bb01c3 instanceof \think\Paginator): if( count($_598d143bb01c3)==0 ) : echo "" ;else: foreach($_598d143bb01c3 as $key=>$item): if(in_array(LOGIN_TYPE,$item['args'])): ?>
        <li class="<?php echo !empty($key) && $key>0?'':'layui-this'; ?>"><?php echo $item['text']; ?></li>
        <?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </ul>
    <div class="layui-login-info">
        <div class="shortcut">
            <a href="javascript:;"></a>
            <a href="javascript:;"></a>
        </div>
        您好! <span><?php echo session('admin.username'); ?></span>。您的IP是：<span><?php echo clientIp(); ?></span>
        <button href="javascript:;" class="layui-btn layui-btn-mini layui-btn-normal iframe-item"
                data-url="<?php echo path('Pub/updatePwd'); ?>">改密
        </button>
        <button href="javascript:;" class="layui-btn layui-btn-mini layui-btn-danger iframe-item sign-out"
                onclick="parent.window.location.href='<?php echo path('Pub/signOut'); ?>'">
            注销
        </button>
    </div>
    <div class="layui-tab-content">
        <!--左侧菜单-->
        <!--公司-->
<?php if(LOGIN_TYPE == 'company' or LOGIN_TYPE == 'admin'): ?>
<div class="layui-tab-item layui-show">
    <ul class="layui-menu">
        <li class="title"><img src="__LAYUI__/images/menu/seller.png"><a href="javascript:;">公司信息</a></li>
        <li class="item iframe-item" data-url="<?php echo path('CompanyHome/index'); ?>"><a href="javascript:;">基本信息</a></li>
    </ul>
    <ul class="layui-menu">
        <li class="title"><img src="__LAYUI__/images/menu/trade.png"><a href="javascript:;">交易管理</a></li>
        <li class="item iframe-item" data-url="<?php echo path('CompanyHome/toolRecord'); ?>"><a href="javascript:;">交易记录</a></li>
        <!--<li class="item iframe-item" data-url=""><a href="javascript:;">手续费配置</a></li>-->
    </ul>
    <ul class="layui-menu">
        <li class="title"><img src="__LAYUI__/images/menu/bonus.png"><a href="javascript:;">佣金管理</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Pub/tab'); ?>?op=认证推广,CompanyHome/authIncom"><a href="javascript:;">账单</a></li>
    </ul>
    <ul class="layui-menu">
        <li class="title"><img src="__LAYUI__/images/menu/user.png"><a href="javascript:;">用户管理</a></li>
        <li class="item iframe-item" data-url="<?php echo path('CompanyHome/member'); ?>"><a href="javascript:;">用户列表</a></li>
   		<li class="item iframe-item" data-url="<?php echo path('Pub/tab'); ?>?op=奖品,CompanyHome/recharge;充值记录,CompanyHome/rechargeRecord"><a href="javascript:;">充值信用币</a></li>
    </ul>
    <ul class="layui-menu">
        <li class="title"><img src="__LAYUI__/images/menu/bonus.png"><a href="javascript:;">幸运抽奖</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Pub/tab'); ?>?op=奖品,LuckDraw/prizeGoods;新增奖品,LuckDraw/prizeGoodsAdd"><a href="javascript:;">奖品管理</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Pub/tab'); ?>?op=活动,LuckDraw/activity;新增活动,LuckDraw/activityAdd"><a href="javascript:;">活动管理</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Pub/tab'); ?>?op=兑奖,LuckDraw/duijiang;中奖记录,LuckDraw/spoilRecord;幸运兑奖,LuckDraw/xydj"><a href="javascript:;">兑奖记录</a></li>
        <li class="item iframe-item" data-url="<?php echo path('LuckDraw/settlement'); ?>"><a href="javascript:;">每日结算</a></li>
        <li class="item iframe-item" data-url="<?php echo path('LuckDraw/setting'); ?>"><a href="javascript:;">设置</a></li>
    </ul>
     <ul class="layui-menu">
        <li class="title"><img src="__LAYUI__/images/menu/trade.png"><a href="javascript:;">账单详情</a></li>
       <li class="item iframe-item" data-url="<?php echo path('CompanyOrder/orderList'); ?>"><a href="javascript:;">交易记录</a></li>
       <li class="item iframe-item" data-url="<?php echo path('CompanyOrder/settleList'); ?>"><a href="javascript:;">提现记录</a></li>
        <li class="item iframe-item" data-url="<?php echo path('CompanyOrder/dailyRecord'); ?>"><a href="javascript:;">每日交易记录</a></li>

    </ul>
</div>
<?php endif; ?>
<!--平台-->
<?php if(LOGIN_TYPE == 'admin'): ?>
<div class="layui-tab-item">
    <ul class="layui-menu">
        <li class="title"><img src="__LAYUI__/images/menu/seller.png"><a href="javascript:;">公司管理</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Company/create'); ?>"><a href="javascript:;">添加新公司</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Company/index'); ?>"><a href="javascript:;">公司列表</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Company/toolRecord'); ?>"><a href="javascript:;">交易记录</a></li>
    </ul>
    <ul class="layui-menu">
        <li class="title"><img src="__LAYUI__/images/menu/user.png"><a href="javascript:;">用户管理</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Member/index'); ?>"><a href="javascript:;">用户列表</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Member/collection'); ?>"><a href="javascript:;">提现申请</a></li>
    </ul>
    <ul class="layui-menu">
        <li class="title"><img src="__LAYUI__/images/menu/spread.png"><a href="javascript:;">推广分销</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Distribution/profit'); ?>"><a href="javascript:;">推广记录</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Pub/tab'); ?>?op=推广收益,Distribution/setting"><a href="javascript:;">设置</a>
        </li>
    </ul>
    <!--<ul class="layui-menu">-->
        <!--<li class="title"><img src="__LAYUI__/images/menu/pay.png"><a href="javascript:;">支付通道</a></li>-->
        <!--<li class="item iframe-item" data-url=""><a href="javascript:;">支付页面链接</a></li>-->
        <!--<li class="item iframe-item" data-url=""><a href="javascript:;">费率</a></li>-->
    <!--</ul>-->
    <ul class="layui-menu">
        <li class="title"><img src="__LAYUI__/images/menu/document.png"><a href="javascript:;">文档内容</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Document/index'); ?>"><a href="javascript:;">文档列表</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Document/slide'); ?>"><a href="javascript:;">首页轮播图</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Document/webConfig'); ?>"><a href="javascript:;">站点配置</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Document/question'); ?>"><a href="javascript:;">问题回复</a></li>
    </ul>
    <ul class="layui-menu">
        <li class="title"><img src="__LAYUI__/images/menu/loan.png"><a href="javascript:;">贷款信息管理</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Loan/index'); ?>"><a href="javascript:;">信息列表</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Loan/index'); ?>?status=待审核"><a href="javascript:;">信息审核</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Loan/index'); ?>?apply_refund=1"><a href="javascript:;">退保证金</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Loan/setting'); ?>"><a href="javascript:;">信息参数配置</a></li>
    </ul>
    <ul class="layui-menu">
        <li class="title"><img src="__LAYUI__/images/menu/pawn.png"><a href="javascript:;">典当信息管理</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Pawn/index'); ?>"><a href="javascript:;">典当列表</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Pawn/index'); ?>?status=待审核"><a href="javascript:;">典当审核</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Pawn/index'); ?>?apply_refund=1"><a href="javascript:;">退押金</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Pawn/setting'); ?>"><a href="javascript:;">典当参数配置</a></li>
    </ul>
    <ul class="layui-menu">
        <li class="title"><img src="__LAYUI__/images/menu/bonus.png"><a href="javascript:;">幸运抽奖</a></li>
        <li class="item iframe-item" data-url="<?php echo path('LuckDraw/merchantSettlement'); ?>"><a href="javascript:;">商家结算</a></li>
        <li class="item iframe-item" data-url="<?php echo path('LuckDraw/tuijian'); ?>"><a href="javascript:;">推荐</a></li>
        <li class="item iframe-item" data-url="<?php echo path('LuckDraw/notify'); ?>"><a href="javascript:;">公告</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Pub/tab'); ?>?op=奖品分类,LuckDraw/goodsClass;新增分类,LuckDraw/goodsClassAdd"><a href="javascript:;">奖品分类</a></li>
        
    </ul>
     <ul class="layui-menu">
        <li class="title"><img src="__LAYUI__/images/menu/spread.png"><a href="javascript:;">代付</a></li>
        <li class="item iframe-item" data-url="<?php echo path('Pub/tab'); ?>?op=代付,Replace/pay;代付详情,Replace/detail"><a href="javascript:;">代付</a></li>
        </li>
    </ul>
</div>
<?php endif; ?>
    </div>
    <iframe id="main" src="<?php echo path('CompanyHome/index'); ?>" frameborder="0" seamless></iframe>
</div>
<script>
    layui.use('base');
</script>
</body>
</html>