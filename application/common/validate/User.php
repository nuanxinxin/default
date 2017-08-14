<?php

namespace app\common\validate;

use think\Validate;
use org\Identity;//身份证验证类

class User extends Validate
{
    protected $rule = [
        'phone' => 'require|length:11',
        'wx' => 'require',
        'company_id' => 'require',
        'user_id' => 'require',
        'name' => 'require',
        'id_number' => 'require|checkIdNumber',
        'id_pics' => 'require',
        'bank_card_number' => 'require',
        'bank_card_pic' => 'require',
        'bank_name' => 'require',
        'bank_sub_name' => 'require',
    ];

    protected $message = [
        'phone.require' => '请输入手机号',
        'phone.length' => '手机号长度不符',
        'wx.require' => '获取openid失败',
        'company_id.require' => '未知公司',
        'user_id.require' => '用户读取失败',
        'name.require' => '请输入姓名',
        'id_number.require' => '请输入身份证号码',
        'id_pics.require' => '请上传身份证照片',
        'bankCard_number.require' => '请输入银行卡号',
        'bank_name.require' => '请输入银行名称',
        'bank_sub_name.require' => '请输入支行名称',
        'bank_card_pic.require' => '请上传银行卡照片',
    ];

    protected $scene = [
        'register' => ['phone', 'wx', 'company_id'],//注册验证
        'auth' => ['user_id', 'name', 'id_number', 'id_pics', 'bank_card_number', 'bank_name', 'bank_sub_name', 'bank_card_pic'],//提交认证信息验证
        'login' => ['wx']//登录验证
    ];

    /**
     * 验证身份证
     * @param $value
     */
    protected function checkIdNumber($value)
    {
        return true;
        $Identity = new Identity;
        if ($Identity->checkIdentity($value) === false) {
            abort(500, '身份证号码验证失败');
        } else {
            return true;
        }
    }
}