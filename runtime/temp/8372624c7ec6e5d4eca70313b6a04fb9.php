<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:68:"D:\project\default\public/../application/admin\view\member\auth.html";i:1501226589;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;}*/ ?>
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
<div class="layui-main">
    <?php if($data): ?>
    <form>
        <div class="layui-field-box" id="layer-photos">
            <div class="layui-form-item">
                <div class="label-title">认证状态</div>
                <div><input type="text" value="<?php echo $data->auth_status; ?>" class="layui-input" disabled></div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">姓名</div>
                <div>
                    <input type="text" name="name" value="<?php echo $data->name; ?>" class="layui-input">
                    <a href="javascript:setAuthInfo('name');" class="layui-btn">设置</a>
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">身份证</div>
                <div>
                    <input type="text" name="id_number" value="<?php echo $data->id_number; ?>" class="layui-input">
                    <a href="javascript:setAuthInfo('id_number');" class="layui-btn">设置</a>
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">身份证扫描件</div>
                <div>
                    <?php if(is_array($data->pics) || $data->pics instanceof \think\Collection || $data->pics instanceof \think\Paginator): if( count($data->pics)==0 ) : echo "" ;else: foreach($data->pics as $key=>$pic): ?>
                    <img src="<?php echo $pic; ?>" style="width:243px;height:153px;">
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">认证类型</div>
                <div><input type="text" value="<?php echo $data->user_type; ?>" class="layui-input" disabled></div>
            </div>
            <?php if($data->user_type == '中介'): ?>
            <div class="layui-form-item b-t-d">
                <div class="label-title">公司名称</div>
                <div><input type="text" value="<?php echo $data->company_name; ?>" class="layui-input" disabled></div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">公司地址</div>
                <div><input type="text" value="<?php echo $data->company_addr; ?>" class="layui-input" disabled></div>
            </div>
            <?php endif; ?>
            <div class="layui-form-item b-t-d">
                <div class="label-title">银行卡号</div>
                <div>
                    <input type="text" name="bank_card_number" value="<?php echo $data->bank_card_number; ?>" class="layui-input">
                    <a href="javascript:setAuthInfo('bank_card_number');" class="layui-btn">设置</a>
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">银行卡照片</div>
                <div>
                    <?php if(is_array($data->bank_card_pics) || $data->bank_card_pics instanceof \think\Collection || $data->bank_card_pics instanceof \think\Paginator): if( count($data->bank_card_pics)==0 ) : echo "" ;else: foreach($data->bank_card_pics as $key=>$pic): ?>
                    <img src="<?php echo $pic; ?>" style="width:243px;height:153px;">
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">银行名称</div>
                <div>
                    <input type="text" name="bank_name" value="<?php echo $data->bank_name; ?>" class="layui-input">
                    <a href="javascript:setAuthInfo('bank_name');" class="layui-btn">设置</a>
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">支行名称</div>
                <div>
                    <input type="text" name="bank_sub_name" value="<?php echo $data->bank_sub_name; ?>" class="layui-input">
                    <a href="javascript:setAuthInfo('bank_sub_name');" class="layui-btn">设置</a>
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">联行号</div>
                <div>
                    <input type="text" name="bank_code" value="<?php echo $data->bank_code; ?>" class="layui-input">
                    <a href="javascript:;" id="setBankCode" class="layui-btn">设置</a>
                    <a href="http://www.posp.cn/" target="_blank" style="color:blue;">联行号查询</a>
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">区域编码</div>
                <div>
                    <input type="text" name="area_code" value="<?php echo $data->area_code; ?>" class="layui-input">
                    <a href="javascript:setAuthInfo('area_code');" class="layui-btn">设置</a>
                    <a href="http://www.stats.gov.cn/tjsj/tjbz/xzqhdm/201703/t20170310_1471429.html?spm=a219a.7629140.0.0.7aZWPD" target="_blank" style="color:blue;">区域编码查询</a>
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">申请时间</div>
                <div><input type="text" value="<?php echo date('Y-m-d',$data->auth_time); ?>" class="layui-input" disabled></div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">支付认证费用时间</div>
                <div><input type="text" value="<?php echo !empty($data->auth_pay_time) && $data->auth_pay_time>0?date('Y-m-d',$data->auth_pay_time):'未支付'; ?>"
                            class="layui-input" disabled>
                </div>
            </div>
            <div class="layui-form-item b-t-s">
                <a href="javascript:;" id="auth_hf" class="layui-btn">通过认证</a>
                <!--<a href="javascript:;" id="auth_sz" class="layui-btn">通道注册(SZ)<?php echo !empty($data->pay_reg_sz) && $data->pay_reg_sz==1?'-已提交注册':''; ?></a>-->
                <a href="javascript:;" id="auth_wlb" class="layui-btn">通道注册(WLB)<?php echo !empty($data->pay_reg_wlb) && $data->pay_reg_wlb==1?'-已提交注册':''; ?></a>
                <a href="javascript:;" id="auth_hf_reg" class="layui-btn">通道注册(Hf)<?php echo !empty($data->pay_reg_hf) && $data->pay_reg_hf==1?'-已提交注册':''; ?></a>
                <a href="javascript:;" id="error" class="layui-btn">驳回认证</a>
            </div>
        </div>
    </form>
    <?php else: ?>
    <div style="color:#FF5722;">没有认证信息！</div>
    <?php endif; ?>
</div>
<script>
    var jq;
    layui.use('base', function () {
        var $ = layui.jquery;
        var base = layui.base();
        jq = $;
        layer.photos({
            photos: "#layer-photos"
            , anim: 1
        });

        $("#auth_hf").on('click', function () {
            if ($(this).text() == '正在提交，请耐心等待...') {
                return;
            }
            $(this).text('正在提交，请耐心等待...').css("background", "#ccc");
            window.location.href = "<?php echo path('auth',['user_id'=>$data->user_id,'auth_status'=>'已通过']); ?>";
        });

        $("#auth_sz").on('click', function () {
            if ($(this).text() == '正在提交，请耐心等待...') {
                return;
            }
            $(this).text('正在提交，请耐心等待...').css("background", "#ccc");
            window.location.href = "<?php echo path('auth_sz',['user_id'=>$data->user_id]); ?>";
        });
        $("#auth_hf_reg").on('click', function () {
            if ($(this).text() == '正在提交，请耐心等待...') {
                return;
            }
            $(this).text('正在提交，请耐心等待...').css("background", "#ccc");
            window.location.href = "<?php echo path('auth_hf',['user_id'=>$data->user_id]); ?>";
        });
        $("#auth_wlb").on('click', function () {
            if ($(this).text() == '正在提交，请耐心等待...') {
                return;
            }
            $(this).text('正在提交，请耐心等待...').css("background", "#ccc");
            window.location.href = "<?php echo path('auth_wlb',['user_id'=>$data->user_id]); ?>";
        });

        $("#error").on('click', function () {
            layer.prompt({title: '输入驳回说明', formType: 0}, function (msg, index) {
                layer.close(index);
                window.location.href = "<?php echo path('auth',['user_id'=>$data->user_id,'auth_status'=>'认证失败']); ?>?msg=" + msg;
            });
        });

        $("#setBankCode").click(function () {
            var bank_code = $("input[name=bank_code]").val();
            window.location.href = "<?php echo path('setBankCode'); ?>?id=<?php echo $data->id; ?>&bank_code=" + bank_code;
        });
    });

    function setAuthInfo(name) {
        var value = jq("input[name=" + name + "]").val();
        window.location.href = "<?php echo path('setAuthInfo'); ?>?id=<?php echo $data->id; ?>&name=" + name + "&value=" + value;
    }
</script>
</body>
</html>