<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:69:"D:\project\default\public/../application/admin\view\company\form.html";i:1501492975;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;s:64:"D:\project\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
        <legend><?php echo !empty($data)?'修改公司账号信息':'添加新公司'; ?></legend>
    </fieldset>
    <div class="layui-field-box">
        <form action="<?php echo !empty($data)?path('edit',['id'=>$data->id]):path('create'); ?>" method="post" class="layui-form">
            <div class="layui-form-item">
                <div class="label-title">公司LOGO</div>
                <div class="image_upload" data-name="company_pic" data-value="<?php echo $data->company_pic; ?>"></div>
                <span style="color:red;">图片比例必须1:1</span>
            </div>
            <div class="layui-form-item">
                <div class="label-title">公司营业执照正面</div>
                <div class="image_upload" data-name="business_licence_zheng" data-value="<?php echo $data->business_licence_zheng; ?>"></div>
               <!--  <span style="color:red;">图片比例必须1:1</span> -->
            </div>
            <div class="layui-form-item">
                <div class="label-title">法人身份证</div>
                <div class="image_upload" data-name="cert_correct" data-value="<?php echo $data->cert_correct; ?>"></div>
                <!-- <span style="color:red;">图片比例必须1:1</span> -->
            </div>
             <div class="layui-form-item">
                <div class="label-title">法人身份证反面</div>
                <div class="image_upload" data-name="cert_opposite" data-value="<?php echo $data->cert_opposite; ?>"></div>
                <!-- <span style="color:red;">图片比例必须1:1</span> -->
            </div>
            <div class="layui-form-item">
                <div class="label-title">银行正面</div>
                <div class="image_upload" data-name="card_correct" data-value="<?php echo $data->card_correct; ?>"></div>
                <!-- <span style="color:red;">图片比例必须1:1</span> -->
            </div>
            <div class="layui-form-item">
                <div class="label-title">银行背面</div>
                <div class="image_upload" data-name="card_opposite" data-value="<?php echo $data->card_opposite; ?>"></div>
                <!-- <span style="color:red;">图片比例必须1:1</span> -->
            </div>
              <div class="layui-form-item">
                <div class="label-title">手持身份证图片</div>
                <div class="image_upload" data-name="cert_meet" data-value="<?php echo $data->cert_meet; ?>"></div>
                <!-- <span style="color:red;">图片比例必须1:1</span> -->
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">登录账号</div>
                <div><input type="text" name="username" value="<?php echo $data->username; ?>" class="layui-input" datatype="s6-15">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title">登录密码</div>
                <div><input type="password" name="password" class="layui-input" placeholder="<?php echo !empty($data)?'留空不修改':''; ?>"></div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">公司名称</div>
                <div><input type="text" name="company_name" value="<?php echo $data->company_name; ?>" class="layui-input"
                            datatype="s3-50"></div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">平台赋予信用币</div>
                <div><input type="text" name="give_credit_money" value="<?php echo $data->give_credit_money; ?>" class="layui-input"
                            datatype="s1-50"></div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">最低费率（0.001-0.999）</div>
                <div><input type="text" name="min_rate" value="<?php echo $data->min_rate; ?>" class="layui-input"
                            datatype="/^0\.\d{1,3}$/">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">自营费率（0.001-0.999）</div>
                <div><input type="text" name="rate" value="<?php echo $data->rate; ?>" class="layui-input" datatype="/^0\.\d{1,3}$/">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">通道收取费率</div>
                <div><input type="text" name="commission_charge" value="<?php echo $data->commission_charge; ?>" class="layui-input"  datatype="/^0\.\d{1,3}$/">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">通道每笔订单收取费用</div>
                <div><input type="text" name="order_expense" value="<?php echo $data->order_expense; ?>" class="layui-input" datatype="/^\d+(\.\d+)?$/">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">银行卡号</div>
                <div><input type="text" name="union_pay_config[bank_no]" value="<?php echo $data->union_pay_config['bank_no']; ?>" class="layui-input" datatype="*">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">开户行</div>
                <div><input type="text" name="union_pay_config[bank_name]" value="<?php echo $data->union_pay_config['bank_name']; ?>" class="layui-input" datatype="*">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">开户支行</div>
                <div><input type="text" name="union_pay_config[bank_sub]" value="<?php echo $data->union_pay_config['bank_sub']; ?>" class="layui-input" datatype="*">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">联行号</div>
                <div>
                    <input type="text" name="union_pay_config[bank_code]" value="<?php echo $data->union_pay_config['bank_code']; ?>" class="layui-input" datatype="*">
                    <a href="http://www.posp.cn/" target="_blank" style="color:blue;">联行号查询</a>
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">身份证</div>
                <div><input type="text" name="union_pay_config[card_no]" value="<?php echo $data->union_pay_config['card_no']; ?>" class="layui-input" datatype="*">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">银行卡持有人姓名</div>
                <div><input type="text" name="union_pay_config[name]" value="<?php echo $data->union_pay_config['name']; ?>" class="layui-input" datatype="*">
                </div>
            </div>
            <div class="layui-form-item b-t-d">
                <div class="label-title Validform_label">手机号</div>
                <div><input type="text" name="union_pay_config[mobile]" value="<?php echo $data->union_pay_config['mobile']; ?>" class="layui-input" datatype="*">
                </div>
            </div>
            <div class="layui-form-item b-t-s">
                <?php echo token(); ?>
                <button type="submit" class="layui-btn">提交</button>
                <?php if($data): ?>
                <button type="button" class="layui-btn layui-btn-primary" onclick="window.history.back()">返回</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
    layui.use(['base','validform'], function () {
        var $ = layui.jquery;

        //表单验证
        $("form").Validform({
            tiptype: 3
        });
    });
</script>


</body>
</html>