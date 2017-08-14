<?php

namespace app\common\validate;

use think\Validate;
use org\Identity;//身份证验证类

class LoanInfo extends Validate
{
    protected $rule = [
        //基础信息
        'user_id' => 'require',//用户标识
        'name' => 'require',//姓名
        'phone' => 'require',//电话
        'gender' => 'require|in:男,女',//性别
        'id_number' => 'require|checkIdNumber',//身份证号码
        'take_id_pics' => 'require',//手持身份证照片
        'marry_status' => 'require|in:已婚,未婚,离异',//婚姻状态
        'marry_book_pic' => 'requireIf:marry_status,已婚',//结婚证照片
        'house_hold_book_pic' => 'require',//户口本照片
        'credit_report_pic' => 'require',//征信报告照片
        'loan_money' => 'require|number',
        'interest' => 'require',
        'city' => 'require',


        //信贷扩展
        'had_house' => 'require|in:有,无',//房产
        'had_car' => 'require|in:有,无',//车辆
        'had_loan' => 'require|in:有,无',//贷款
        'job_company_type' => 'require|in:国企,私营,民营,其他',//工作公司性质
        'monthly' => 'require|number',//月薪
        'social_security' => 'require|in:有,无',//社保
        'provident_fund' => 'require|in:有,无',//公积金
        'in_come_certificate_pic' => 'require',//收入证明照片


        //房贷扩展
        'loan_buy_house' => 'require',//是否是按揭购房
        'house_both_have' => 'require',//是否是共同所有
        'house_may_price' => 'require',//房产预估价格

        //车贷扩展
        'car_brand' => 'require',//车辆品牌
        'car_mill' => 'require',//公里数
        'loan_buy_car' => 'require',//是否是按揭购车
        'car_full_pic' => 'require',//车辆全身照片
        'car_reg_pic' => 'require',//车辆登记证照片
        'driving_licence_pics' => 'require',//驾驶证照片
        'vehicle_licence_pics' => 'require',//行驶证照片
        'carInvoice_pic' => 'require',//购车发票照片
        'policy_pic' => 'require',//保单照片

        //美丽贷
        'take_bank_card_pic' => 'require',
        'father_name' => 'require',
        'father_phone' => 'require',
        'mother_name' => 'require',
        'mother_phone' => 'require',
        'friend1_name' => 'require',
        'friend1_phone' => 'require',
        'friend2_name' => 'require',
        'friend2_phone' => 'require',
        'friend3_name' => 'require',
        'friend3_phone' => 'require',


        //不验证
        'taobao_account' => 'require',//淘宝账户
        'taobao_credit' => 'require',//芝麻信用
        'credit_card_count' => 'require|number',//信用卡张数


    ];

    protected $message = [
        'user_id.require' => '未知发布人',
        'name.require' => '请填写姓名',
        'phone.require' => '请填写电话',
        'gender.require' => '请选择性别',
        'gender.in' => '性别可选值(男,女)',
        'job_company_type.require' => '请选择工作公司性质',
        'job_company_type.in' => '工作公司性质可选值(国企,私营,民营,其他)',
        'id_number.require' => '请填写身份证号码',
        'id_number.checkIdNumber' => '身份证号码验证失败',
        'take_id_pics.require' => '请上传手持身份证照片',
        'marry_status.require' => '请选择婚姻状态',
        'marry_status.in' => '婚姻状态可选值(已婚,未婚,离异)',
        'marry_book_pic.requireIf' => '请上传结婚证照片',
        'house_hold_book_pic.require' => '请上传户口本照片',
        'monthly.require' => '请填写月薪',
        'social_security.require' => '请填写社保选项',
        'social_security.in' => '社保可选值(有,无)',
        'provident_fund.require' => '请填写公积金选项',
        'provident_fund.in' => '公积金可选值(有,无)',
        'in_come_certificate_pic.require' => '请上传收入证明照片',
        'taobao_account.require' => '请填写淘宝账户',
        'taobao_credit.require' => '请填写芝麻信用',
        'credit_card_count.require' => '请填写信用卡张数',
        'credit_report_pic.require' => '请上传征信报告照片',
        'had_house.require' => '请填写房产选项',
        'had_house.in' => '房产可选值(有,无)',
        'had_car.require' => '请填写车辆选项',
        'had_car.in' => '车辆可选值(有,无)',
        'had_loan.require' => '请填写贷款选项',
        'had_loan.in' => '贷款可选值(有,无)',
        'loan_money.require' => '请填写贷款金额',
        'interest.require' => '请填写接受月息',
        'city.require' => '请填写城市',
        'take_bank_card_pic.require' => '请上传手持银行卡照片',
        'father_name.require' => '请填写父亲姓名',
        'father_phone.require' => '请填写父亲电话',
        'mother_name.require' => '请填写母亲姓名',
        'mother_phone.require' => '请填写母亲电话',
        'friend1_name.require' => '请填写亲友姓名1',
        'friend1_phone.require' => '请填写亲友电话1',
        'friend2_name.require' => '请填写亲友姓名2',
        'friend2_phone.require' => '请填写亲友电话2',
        'friend3_name.require' => '请填写亲友姓名3',
        'friend3_phone.require' => '请填写亲友电话3',
    ];

    protected $scene = [
        'commit_credit_loan' => [
            'user_id',
            'name',
            'phone',
            'gender',
            'id_number',
            'take_id_pics',
            'marry_status',
            'marry_book_pic',
            'credit_report_pic',
            'loan_money',
            'interest',
            'had_house',
            'had_car',
            'had_loan',
            'job_company_type',
            'monthly',
            'social_security',
            'provident_fund',
/*             'in_come_certificate_pic', */
            'city'
        ],
        'commit_house_loan' => [
            'user_id',
            'name',
            'phone',
            'gender',
            'id_number',
            'take_id_pics',
            'marry_status',
            'marry_book_pic',
            'credit_report_pic',
            'loan_money',
            'interest',
            'loan_buy_house',
            'house_both_have',
            'city'
        ],
        'commit_car_loan' => [
            'user_id',
            'name',
            'phone',
            'gender',
            'id_number',
            'take_id_pics',
/*             'marry_status',
            'marry_book_pic', */
            'credit_report_pic',
            'loan_money',
            'interest',
            'car_brand',
            'car_mill',
            'loan_buy_car',
            'car_full_pic',
            'car_reg_pic',
            'driving_licence_pics',
            'vehicle_licence_pics',
       /*      'car_invoice_pic', */
       /*      'policy_pic', */
            'city'
        ],
        'commit_beauty_loan' => [
            'user_id',
            'name',
            'phone',
            'gender',
            'id_number',
            'take_id_pics',
            'marry_status',
            'marry_book_pic',
            'credit_report_pic',
            'loan_money',
            'interest',
            'had_house',
            'had_car',
            'had_loan',
            'take_bank_card_pic',
            'father_name',
            'father_phone',
            'mother_name',
            'mother_phone',
            'friend1_name',
            'friend1_phone',
            'friend2_name',
            'friend2_phone',
            'friend3_name',
            'friend3_phone',
            'city'
        ]
    ];

    /**
     * 验证身份证
     * @param $value
     */
    protected function checkIdNumber($value)
    {
        $Identity = new Identity;
        if ($Identity->checkIdentity($value) === false) {
            return false;
        } else {
            return true;
        }
    }
}