<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:80:"D:\PHP\wamp\www\default\public/../application/admin\view\company_home\index.html";i:1487325326;s:68:"D:\PHP\wamp\www\default\public/../application/admin\view\header.html";i:1483770726;s:69:"D:\PHP\wamp\www\default\public/../application/admin\view\loading.html";i:1481350888;}*/ ?>
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
        .table-info tbody tr td:first-child {
            background-color: #fbfbfb;
            text-align: right;
        }
    </style>
</head>
<body>
<script src="__LAYUI__/loading.js"></script>
<div class="layui-main">
    <fieldset class="layui-elem-field layui-field-title">
        <legend>基本信息</legend>
    </fieldset>
    <table class="table table-info">
        <tbody>
        <tr>
            <td style="width:100px;">账号</td>
            <td><?php echo $company->username; ?></td>
        </tr>
        <tr>
            <td>公司名称</td>
            <td><?php echo $company->company_name; ?></td>
        </tr>
        <tr>
            <td>最低费率</td>
            <td><?php echo $company->min_rate; ?></td>
        </tr>
        <tr>
            <td>自营费率</td>
            <td>
                <?php echo $company->rate; ?>
                <button type="button" data-value="<?php echo $company->rate; ?>" class="layui-btn layui-btn-normal layui-btn-mini"
                        id="set-rate"
                        style="margin-left:10px;">设置
                </button>
            </td>
        </tr>
        <tr>
            <td>标识符</td>
            <td><?php echo $company->identifier; ?></td>
        </tr>
        <tr>
            <td>推广码</td>
            <td>
                <div id="qrcode"></div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script src="__LAYUI__/qrcode.min.js"></script>
<script>
    layui.use('base', function () {
        var $ = layui.jquery;
        $("#set-rate").click(function () {
            layer.prompt({title: '自营费率', formType: 0, value: $(this).data('value')}, function (rate, index) {
                window.location.href = "<?php echo path('setRate'); ?>?rate=" + rate;
            });
        });

        $.get("/api/wx/qrcode?userId=&companyId=<?php echo $company->identifier; ?>", function (res) {
            var options = {
                text: res,
                width: 100,
                height: 100,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            }
            new QRCode('qrcode', options);
        });
    });
</script>
</body>
</html>