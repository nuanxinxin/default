<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:96:"D:\PHP\wamp\www\default\public/../application/admin\view\company_home\recharge_credit_money.html";i:1499322478;s:68:"D:\PHP\wamp\www\default\public/../application/admin\view\header.html";i:1483770726;s:69:"D:\PHP\wamp\www\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
            <div class="layui-form-item">
                <div class="label-title Validform_label">充值手机号码</div>
                <div><input type="text" name="phone"  class="layui-input" datatype="s2-15">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="label-title Validform_label">充值金额</div>
                <div><input type="text" name="balance"  class="layui-input" datatype="s2-15">
                </div>
            </div>
            <div class="layui-form-item b-t-s">
                <button type="button" id="recharge" class="layui-btn">充值</button>
            </div>
    </div>
</div>
<script>
    layui.use(['base', 'form', 'validform'], function () {
        var $ = layui.jquery;
        var base = layui.base();
			
        $("#recharge").click(function () {
            var phone=$("input[name='phone']").val();
            var balance=$("input[name='balance']").val();
            var reg = new RegExp("^[0-9]*$");
            if(!reg.test(balance)){
            	layer.msg("请输入数字!");
                return false;
            }
        	if(phone){
        		if ((/^1(3|4|5|7|8)\d{9}$/.test(phone))) {
        			base.request.post({
                        data: {phone: phone,balance:balance},
                        success: function (res) {
                            if (res.code == 200) {
                            	layer.msg(res.message);
                            } else {
                                layer.msg(res.message);
                            }
                        }
                    }); 
		  		}else{
        			layer.msg("请填写正确格式手机号码");
        			return false;
		  		}
        	}else{
        		layer.msg("请填写手机号码");
        		return false;
        	}
        	
           
        });
    });
</script>
</body>
</html>