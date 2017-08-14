<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:68:"D:\project\default\public/../application/admin\view\replace\pay.html";i:1502249540;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8">
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
			<legend>代付</legend>
		</fieldset>
		<div class="layui-field-box">
			<form action="" method="post" class="layui-form">
				<div class="layui-form-item b-t-d">
					<div class="label-title Validform_label">代付银行卡号</div>
					<div>
						<input type="text" name="account_no" value="" class="layui-input"
							datatype="n15-19">
					</div>
				</div>
				<div class="layui-form-item b-t-d">
					<div class="label-title Validform_label">代付银行卡所属人</div>
					<div>
						<input type="text" name="account_name" value=""
							class="layui-input" datatype="s1-5">
					</div>
				</div>
				<div class="layui-form-item b-t-d">
					<div class="label-title Validform_label">银行</div>
					<div style="width: 360px;">
						<select name="bank_code" datatype="*">
							<option value=""></option> 
							<?php if(is_array($banks) || $banks instanceof \think\Collection || $banks instanceof \think\Paginator): if( count($banks)==0 ) : echo "" ;else: foreach($banks as $key=>$item): ?>
							<option value="<?php echo $item->bank_code; ?>"><?php echo $item->bank_name; ?></option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</div>
				</div>
				<div class="layui-form-item b-t-d">
					<div class="label-title Validform_label">代付金额</div>
					<div>
						<input type="text" name="amount" value="" class="layui-input"
							datatype=" /^\d+\.?\d*$/i">
					</div>
				</div>
				<div class="layui-form-item b-t-d">
					<div class="label-title ">代付摘要</div>
					<div>
						<input type="text" name="summary" value="" class="layui-input">
					</div>
				</div>
				<div class="layui-form-item b-t-d">
					<div class="label-title ">代付备注</div>
					<div>
						<input type="text" name="remark" value="" class="layui-input">
					</div>
				</div>



				<div class="layui-form-item b-t-s">
					<?php echo token(); ?>
					<button type="submit" class="layui-btn">代付</button>

				</div>
			</form>
		</div>
	</div>

	<script>
    layui.use(['base','validform','form'], function () {
        var $ = layui.jquery;

        //表单验证
        $("form").Validform({
            tiptype: 3
        });
    });
</script>


</body>
</html>