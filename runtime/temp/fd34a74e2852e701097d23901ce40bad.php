<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:68:"D:\project\default\public/../application/admin\view\loan\detail.html";i:1487131062;s:63:"D:\project\default\public/../application/admin\view\header.html";i:1483770726;}*/ ?>
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
                    <td>姓名</td>
                    <td><?php echo $data->name; ?></td>
                </tr>
                <tr>
                    <td>电话</td>
                    <td><?php echo $data->phone; ?></td>
                </tr>
                <tr>
                    <td>性别</td>
                    <td><?php echo $data->gender; ?></td>
                </tr>
                <tr>
                    <td>身份证</td>
                    <td><?php echo $data->id_number; ?></td>
                </tr>
                <tr>
                    <td class="image">身份证照片</td>
                    <td>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">正面</button>
                            <img style="display:none;" layer-src="<?php echo $data->take_id_pics_array[0]; ?>" src="<?php echo $data->take_id_pics_array[0]; ?>" alt="身份证正面照"/>
                        </a>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">反面</button>
                            <img style="display:none;" layer-src="<?php echo $data->take_id_pics_array[1]; ?>" src="<?php echo $data->take_id_pics_array[1]; ?>" alt="身份证反面照"/>
                        </a>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">手持身份证照片</button>
                            <img style="display:none;" layer-src="<?php echo $data->take_id_pics_array[2]; ?>" src="<?php echo $data->take_id_pics_array[2]; ?>" alt="手持身份证照片"/>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>婚姻状态</td>
                    <td><?php echo $data->marry_status; ?></td>
                </tr>
                <?php if($data->marry_status == '已婚'): ?>
                <tr>
                    <td class="image">结婚证照片</td>
                    <td>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">查看</button>
                            <img style="display:none;" layer-src="<?php echo $data->marry_book_pic_array[0]; ?>" src="<?php echo $data->marry_book_pic_array[0]; ?>" alt="结婚证照片"/>
                        </a>
                    </td>
                </tr>
                <?php endif; if($data->house_hold_book_pic != ''): ?>
                <tr>
                    <td class="image">户口本照片</td>
                    <td>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">查看1</button>
                            <img style="display:none;" layer-src="<?php echo $data->house_hold_book_pic_array[0]; ?>" src="<?php echo $data->house_hold_book_pic_array[0]; ?>" alt="户口本照片"/>
                        </a>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">查看2</button>
                            <img style="display:none;" layer-src="<?php echo $data->house_hold_book_pic_array[1]; ?>" src="<?php echo $data->house_hold_book_pic_array[1]; ?>" alt="户口本照片"/>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>淘宝账户</td>
                    <td><?php echo $data->taobao_account; ?></td>
                </tr>
                <tr>
                    <td>芝麻信用</td>
                    <td><?php echo $data->taobao_credit; ?></td>
                </tr>
                <tr>
                    <td>信用卡张数</td>
                    <td><?php echo $data->credit_card_count; ?></td>
                </tr>
                <tr>
                    <td class="image">征信报告照片</td>
                    <td>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">查看</button>
                            <img style="display:none;" layer-src="<?php echo $data->credit_report_pic_array[0]; ?>" src="<?php echo $data->credit_report_pic_array[0]; ?>" alt="征信报告照片"/>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="column">
            <fieldset class="layui-elem-field layui-field-title">
                <legend>贷款类型</legend>
            </fieldset>
            <table class="table">
                <tbody>
                <tr>
                    <td>类型</td>
                    <td><?php echo $data->type; ?></td>
                </tr>

                <?php if($data->type == '车贷'): ?>
                <tr>
                    <td>车辆品牌</td>
                    <td><?php echo $data->car_brand; ?></td>
                </tr>
                <tr>
                    <td>公里数</td>
                    <td><?php echo $data->car_mill; ?></td>
                </tr>
                <tr>
                    <td>是否是按揭购车</td>
                    <td><?php echo $data->loan_buy_car; ?></td>
                </tr>
                <tr>
                    <td class="image">车辆全身照片</td>
                    <td>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">查看</button>
                            <img style="display:none;" layer-src="<?php echo $data->car_full_pic_array[0]; ?>" src="<?php echo $data->car_full_pic_array[0]; ?>" alt="车辆全身照片"/>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="image">车辆登记证照片</td>
                    <td>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">查看</button>
                            <img style="display:none;" layer-src="<?php echo $data->car_reg_pic_array[0]; ?>" src="<?php echo $data->car_reg_pic_array[0]; ?>" alt="车辆登记证照片"/>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="image">驾驶证照片</td>
                    <td>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">查看</button>
                            <img style="display:none;" layer-src="<?php echo $data->driving_licence_pics_array[0]; ?>" src="<?php echo $data->driving_licence_pics_array[0]; ?>" alt="驾驶证照片"/>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="image">行驶证照片</td>
                    <td>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">查看</button>
                            <img style="display:none;" layer-src="<?php echo $data->car_invoice_pic_array[0]; ?>" src="<?php echo $data->car_invoice_pic_array[0]; ?>" alt="行驶证照片"/>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="image">购车发票照片</td>
                    <td>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">查看</button>
                            <img style="display:none;" layer-src="<?php echo $data->car_invoice_pic_array[0]; ?>" src="<?php echo $data->car_invoice_pic_array[0]; ?>" alt="购车发票照片"/>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="image">保单照片</td>
                    <td>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">查看</button>
                            <img style="display:none;" layer-src="<?php echo $data->policy_pic_array[0]; ?>" src="<?php echo $data->policy_pic_array[0]; ?>" alt="保单照片"/>
                        </a>
                    </td>
                </tr>
                <?php endif; if($data->type == '房贷'): ?>
                <tr>
                    <td>是否是按揭购房</td>
                    <td><?php echo $data->loan_buy_house; ?></td>
                </tr>
                <tr>
                    <td>是否是共同所有</td>
                    <td><?php echo $data->house_both_have; ?></td>
                </tr>
                <tr>
                    <td>房产预估价格</td>
                    <td><?php echo $data->house_may_price; ?></td>
                </tr>
                <?php endif; if($data->type == '信贷'): ?>
                <tr>
                    <td>房产</td>
                    <td><?php echo $data->had_house; ?></td>
                </tr>
                <tr>
                    <td>车辆</td>
                    <td><?php echo $data->had_car; ?></td>
                </tr>
                <tr>
                    <td>贷款</td>
                    <td><?php echo $data->had_loan; ?></td>
                </tr>
                <tr>
                    <td>工作公司性质</td>
                    <td><?php echo $data->job_company_type; ?></td>
                </tr>
                <tr>
                    <td>月薪</td>
                    <td><?php echo $data->monthly; ?></td>
                </tr>
                <tr>
                    <td>社保</td>
                    <td><?php echo $data->social_security; ?></td>
                </tr>
                <tr>
                    <td>公积金</td>
                    <td><?php echo $data->provident_fund; ?></td>
                </tr>
                <?php if($data->in_come_certificate_pic != ''): ?>
                <tr>
                    <td>收入证明照片</td>
                    <td>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">查看</button>
                            <img style="display:none;" layer-src="<?php echo $data->in_come_certificate_pic_array[0]; ?>" src="<?php echo $data->in_come_certificate_pic_array[0]; ?>" alt="收入证明照片"/>
                        </a>
                    </td>
                </tr>
                <?php endif; endif; if($data->type == '美丽贷'): ?>
                <tr>
                    <td>房产</td>
                    <td><?php echo $data->had_house; ?></td>
                </tr>
                <tr>
                    <td>车辆</td>
                    <td><?php echo $data->had_car; ?></td>
                </tr>
                <tr>
                    <td>贷款</td>
                    <td><?php echo $data->had_loan; ?></td>
                </tr>
                <tr>
                    <td class="image">手持银行卡照片</td>
                    <td>
                        <a href="javascript:;">
                            <button class="layui-btn layui-btn-mini layui-btn-normal show-image">查看</button>
                            <img style="display:none;" layer-src="<?php echo $data->take_bank_card_pic_array[0]; ?>" src="<?php echo $data->take_bank_card_pic_array[0]; ?>" alt="手持银行卡照片"/>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>父亲姓名</td>
                    <td><?php echo $data->father_name; ?></td>
                </tr>
                <tr>
                    <td>父亲电话</td>
                    <td><?php echo $data->father_phone; ?></td>
                </tr>
                <tr>
                    <td>母亲姓名</td>
                    <td><?php echo $data->mother_name; ?></td>
                </tr>
                <tr>
                    <td>母亲电话</td>
                    <td><?php echo $data->mother_phone; ?></td>
                </tr>
                <tr>
                    <td>亲友姓名1</td>
                    <td><?php echo $data->friend1_name; ?></td>
                </tr>
                <tr>
                    <td>亲友电话1</td>
                    <td><?php echo $data->friend1_phone; ?></td>
                </tr>
                <tr>
                    <td>亲友姓名2</td>
                    <td><?php echo $data->friend2_name; ?></td>
                </tr>
                <tr>
                    <td>亲友电话2</td>
                    <td><?php echo $data->friend2_phone; ?></td>
                </tr>
                <tr>
                    <td>亲友姓名3</td>
                    <td><?php echo $data->friend3_name; ?></td>
                </tr>
                <tr>
                    <td>亲友电话3</td>
                    <td><?php echo $data->friend3_phone; ?></td>
                </tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>

        <div class="column">
            <fieldset class="layui-elem-field layui-field-title">
                <legend>贷款信息</legend>
            </fieldset>
            <table class="table">
                <tbody>
                <tr>
                    <td>贷款金额（元）</td>
                    <td><?php echo $data->loan_money; ?></td>
                </tr>
                <tr>
                    <td>接受月息（元）</td>
                    <td><?php echo $data->interest; ?></td>
                </tr>
                <tr>
                    <td>中介电话</td>
                    <td><input type="text" class="layui-input" name="agent_phone" value="<?php echo $data->agent_phone; ?>" style="width:150px;"></td>
                </tr>
                <tr>
                    <td>信用币价值</td>
                    <td><input type="text" class="layui-input" name="credit_money" value="<?php echo $data->credit_money; ?>" style="width:150px;"></td>
                </tr>
                <tr>
                    <td>状态</td>
                    <td><?php echo $data->status; ?></td>
                </tr>
                <tr>
                    <td>审核</td>
                    <td>
                        <button class="layui-btn layui-btn-mini success">通过</button>
                        <button class="layui-btn layui-btn-mini failed">未通过</button>
                        <?php if($data->apply_refund == 1): ?>
                        <button class="layui-btn layui-btn-mini layui-btn-danger apply_refund">退保证金</button>
                        <?php endif; ?>
                    </td>
                </tr>
                </tbody>
            </table>
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

        $(".success").click(function(){
            var agent_phone = $("input[name=agent_phone]").val();
            var credit_money = $("input[name=credit_money]").val();;
            window.location.href = "<?php echo path('statusSuccess',['id'=>$data->id]); ?>?agent_phone="+agent_phone+"&credit_money="+credit_money;
        });

        $(".failed").click(function(){
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