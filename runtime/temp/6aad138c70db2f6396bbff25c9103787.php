<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:78:"/home/wwwroot/default/public/../application/admin/view/luck_draw/duijiang.html";i:1496311217;s:66:"/home/wwwroot/default/public/../application/admin/view/header.html";i:1483770727;s:67:"/home/wwwroot/default/public/../application/admin/view/loading.html";i:1481350888;}*/ ?>
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
        .info div {
            margin-bottom: 15px;
        }

        .info span {
            display: inline-block;
            width: 100px;
        }
    </style>
</head>
<body>
<script src="__LAYUI__/loading.js"></script>
<div class="layui-main">
    <div class="layui-field-box">
        <form action="" method="get" class="layui-form">
            <div class="layui-form-item">
                <div class="label-title Validform_label">兑奖号</div>
                <div><input type="text" name="ticket_identifier" value="<?php echo \think\Request::instance()->param('ticket_identifier'); ?>"
                            class="layui-input" datatype="s2-15">
                </div>
            </div>
            <div class="layui-form-item b-t-s">
                <button type="submit" class="layui-btn">查询</button>
            </div>
        </form>
        <?php if(\think\Request::instance()->param('ticket_identifier')): if($data == false): ?>
        <div>没有中奖信息！</div>
        <?php else: ?>
        <div class="info">
            <div><span>奖品：</span><img src="<?php echo $data->goods_spoil->thumb; ?>" style="width:50px;height:50px;margin-right:10px;" onerror="this.src='/static/layui/images/prize.jpeg'"><?php echo $data->goods_spoil->title; ?>
            </div>
            <div><span>活动标题：</span><?php echo $data->goods_title; ?></div>
            <div><span>中奖人：</span><?php echo $data->nickname; ?></div>
            <div><span>联系电话：</span><?php echo $data->phone; ?></div>
            <div><span>奖品类型：</span><?php echo $data->spoilTypeText[$data->spoil_type]; ?></div>
            <div><span>兑奖状态：</span><?php echo $data->ticketStatusText[$data->ticket_status]; if($data->ticket_status == 0): ?>
                <button type="submit" class="layui-btn layui-btn-normal layui-btn-mini duijiang"
                        style="margin-left:10px;">兑奖
                </button>
                <?php endif; ?>
            </div>
            <div><span>兑奖时间：</span><?php echo $data->ticket_time; ?></div>
        </div>
        <?php endif; endif; ?>
    </div>
</div>
<script>
    layui.use(['base', 'form', 'validform'], function () {
        var $ = layui.jquery;
        var base = layui.base();

        //表单验证
        $("form").Validform({
            tiptype: 3
        });

        $(".duijiang").click(function () {
            var ticket_identifier = "<?php echo \think\Request::instance()->param('ticket_identifier'); ?>";
            base.request.post({
                data: {ticket_identifier: ticket_identifier},
                success: function (res) {
                    if (res.code == 200) {
                        window.location.reload();
                    } else {
                        layer.msg(res.message);
                    }
                }
            });
        });
    });
</script>
</body>
</html>