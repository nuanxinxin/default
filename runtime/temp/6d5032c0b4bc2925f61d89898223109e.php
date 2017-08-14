<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:68:"D:\project\default\public/../application/admin\view\pawn\detail.html";i:1492657956;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;}*/ ?>
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
        .column {
            width: 350px;
            float: left;
            margin-right: 15px;
        }

        .table tbody tr td:first-child {
            background-color: #f8f8f8;
            width: 90px;
        }

        .table tbody tr td:first-child.image {
            background-image: url(__LAYUI__/images/image.png);
            background-repeat: no-repeat;
            background-size: 19px;
            background-position: top right;
            padding-right: 10px;
        }

        .layui-breadcrumb a {
            font-size: 12px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="layui-main">

    <div id="layer-photos">
        <div class="column">
            <fieldset class="layui-elem-field layui-field-title">
                <legend>基础信息</legend>
            </fieldset>
            <table class="table">
                <tbody>
                <tr>
                    <td>类型</td>
                    <td><?php echo $data->pawn_type; ?></td>
                </tr>
                <tr>
                    <td>标题</td>
                    <td><?php echo $data->title; ?></td>
                </tr>
                <tr>
                    <td>描述</td>
                    <td><?php echo $data->desc; ?></td>
                </tr>
                <tr>
                    <td>购买时间</td>
                    <td><?php echo $data->buy_time; ?></td>
                </tr>
                <tr>
                    <td>购买价格</td>
                    <td><?php echo $data->buy_price; ?></td>
                </tr>
                <tr>
                    <td>寄卖价格</td>
                    <td><?php echo $data->sale_price; ?></td>
                </tr>
                <tr>
                    <td>联系电话</td>
                    <td><?php echo $data->contact_phone; ?></td>
                </tr>
                <tr>
                    <td>城市</td>
                    <td><?php echo $data->city; ?></td>
                </tr>
                <tr>
                    <td>状态</td>
                    <td><?php echo $data->status; ?></td>
                </tr>
                <tr>
                    <td>下架时间</td>
                    <td><?php echo !empty($data->distance_out_time) && $data->distance_out_time>0?date('Y-m-d H:i',$data->distance_out_time):'-'; ?></td>
                </tr>
                <tr>
                    <td>审核</td>
                    <td>
                        <button class="layui-btn layui-btn-mini success">通过</button>
                        <button class="layui-btn layui-btn-mini failed">未通过</button>
                        <?php if($data->apply_refund == 1): ?>
                        <button class="layui-btn layui-btn-mini layui-btn-danger apply_refund">退押金</button>
                        <?php endif; ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="column">
            <fieldset class="layui-elem-field layui-field-title">
                <legend>图片</legend>
            </fieldset>
            <?php if(is_array($pics) || $pics instanceof \think\Collection || $pics instanceof \think\Paginator): if( count($pics)==0 ) : echo "" ;else: foreach($pics as $key=>$pic): ?>
            <img style="width:100px;height:100px;border: 1px solid #ccc;margin-bottom: 5px;" layer-src="<?php echo $pic; ?>" src="<?php echo $pic; ?>"/>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
</div>
<script>
    layui.use('base', function () {
        var $ = layui.jquery;
        var base = layui.base();
        layer.photos({
            photos: "#layer-photos"
            , anim: 1
        });

        $(".show-image").click(function () {
            $(this).siblings("img").click();
        });

        $(".success").click(function () {
            if($(this).text() == '正在推送，请耐心等待...'){
                return;
            }
            $(this).text('正在推送，请耐心等待...').css("background","#ccc");
            window.location.href = "<?php echo path('statusSuccess',['id'=>$data->id]); ?>";
        });

        $(".failed").click(function () {
            window.location.href = "<?php echo path('statusFailed',['id'=>$data->id]); ?>";
        });

        $(".apply_refund").click(function(){
            layer.confirm('确定操作吗?', {
                title: '安全提示',
                icon: 3,
                btn: ['确定', '取消']
            }, function () {
                window.location.href = "<?php echo path('refund',['id'=>$data->id]); ?>";
            });
        });
    });
</script>
</body>
</html>