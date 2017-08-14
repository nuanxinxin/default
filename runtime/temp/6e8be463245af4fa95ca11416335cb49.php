<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:83:"/home/wwwroot/default/public/../application/admin/view/luck_draw/activity_form.html";i:1496901619;s:66:"/home/wwwroot/default/public/../application/admin/view/header.html";i:1483770727;s:67:"/home/wwwroot/default/public/../application/admin/view/loading.html";i:1481350888;}*/ ?>
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
    <div class="layui-field-box">
        <form action="" method="post" class="layui-form">
            <div class="layui-form-item">
                <div class="label-title Validform_label">标题</div>
                <div><input type="text" name="goods_title" value="<?php echo $data->goods_title; ?>" class="layui-input"
                            datatype="*">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">封面图</div>
                <div class="image_upload" data-name="page_thumb" data-value="<?php echo $data->page_thumb; ?>"></div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">详情</div>
                <div>
                    <textarea class="layui-textarea" name="detail" style="width:360px;"><?php echo $data->detail; ?></textarea>
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">详情图</div>
                <div class="image_upload" data-name="detail_thumb" data-value="<?php echo $data->detail_thumb; ?>"></div>
                <span style="color:red;">图片比例必须1:1</span>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">参与价格</div>
                <div><input type="text" name="single_price" value="<?php echo $data->single_price; ?>" class="layui-input"
                            datatype="*">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">参与人数</div>
                <div><input type="text" name="max_people_number" value="<?php echo $data->max_people_number; ?>" class="layui-input"
                            datatype="n">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">时间</div>
                <div class="layui-input-inline">
                    <input class="layui-input" name="start_time" value="<?php if($data->start_time): ?><?php echo date('Y-m-d H:i:s',$data->start_time); endif; ?>" placeholder="开始日"
                           id="LAY_demorange_s" datatype="*">
                </div>
                <div class="layui-input-inline">
                    <input class="layui-input" name="end_time" value="<?php if($data->end_time): ?><?php echo date('Y-m-d H:i:s',$data->end_time); endif; ?>" placeholder="截止日"
                           id="LAY_demorange_e" datatype="*">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">奖品</div>
                <div style="width:360px;">
                    <select name="spoil_id" datatype="*">
                        <option value=""></option>
                        <?php if(is_array($prizeGoods) || $prizeGoods instanceof \think\Collection || $prizeGoods instanceof \think\Paginator): if( count($prizeGoods)==0 ) : echo "" ;else: foreach($prizeGoods as $key=>$item): ?>
                        <option value="<?php echo $item->spoil_id; ?>" <?php echo !empty($data->
                            spoil_id) && $data->
                            spoil_id==$item->spoil_id?'selected':''; ?>><?php echo $item->title; ?>
                        </option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">安慰奖开关</div>
                <div>
                    <input type="checkbox" name="comfort_spoil_enable" value="1" lay-skin="switch" <?php echo !empty($data->comfort_spoil_enable)?'checked':''; ?>>
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">安慰奖</div>
                <div style="width:360px;">
                    <select name="comfort_spoil_id">
                        <option value=""></option>
                        <?php if(is_array($comfortPrizeGoods) || $comfortPrizeGoods instanceof \think\Collection || $comfortPrizeGoods instanceof \think\Paginator): if( count($comfortPrizeGoods)==0 ) : echo "" ;else: foreach($comfortPrizeGoods as $key=>$item): ?>
                        <option value="<?php echo $item->spoil_id; ?>" <?php echo !empty($data->
                            comfort_spoil_id) && $data->
                            comfort_spoil_id==$item->spoil_id?'selected':''; ?>><?php echo $item->title; ?>
                        </option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item b-t-s">
                <input type="hidden" name="goods_id" value="<?php echo $data->goods_id; ?>">
                <button type="submit" class="layui-btn">提交</button>
                <?php if($data): ?>
                <button type="button" class="layui-btn layui-btn-primary" onclick="window.history.back()">返回</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<script>
    layui.use(['base', 'form', 'validform', 'laydate'], function () {
        var $ = layui.jquery;
        var laydate = layui.laydate;

        var start = {
            min: laydate.now()
            , max: '2099-06-16 23:59:59'
            , istoday: false
            , format: 'YYYY-MM-DD hh:mm:ss'
            , istime: true
            , choose: function (datas) {
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        };

        var end = {
            min: laydate.now()
            , max: '2099-06-16 23:59:59'
            , istoday: false
            , format: 'YYYY-MM-DD hh:mm:ss'
            , istime: true
            , choose: function (datas) {
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };

        document.getElementById('LAY_demorange_s').onclick = function () {
            start.elem = this;
            laydate(start);
        }
        document.getElementById('LAY_demorange_e').onclick = function () {
            end.elem = this
            laydate(end);
        }

        //表单验证
        $("form").Validform({
            tiptype: 3
        });
    });
</script>
</body>
</html>