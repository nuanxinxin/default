<?php

namespace app\common\validate;

use think\Validate;

class CreditCard extends Validate
{
    protected $rule = [
        //基础信息
        'user_id' => 'require',//用户标识
        'name' => 'require',//姓名
        'phone' => 'require',//电话
		'bank_name'=>'require',
    	'bank_number'=>'require',
    	'contact_people'=>'require',
    	'contact_phone'=>'require',
    	'contact_relation'=>'require',
    	'credit_number'=>'require',	
    	'ali_account' => 'require',//
		'credit_report_pics'=>'require'

    ];

    protected $message = [
        'name.require' => '请填写姓名',
        'phone.require' => '请填写电话',
    	'phone.isMoblie' => '手机号验证错误',
    	'phone.isMoblie' => '紧急联系人手机号验证错误',
    	'bank_name.require' => '请填写银行名',
   		'bank_number.require' => '请填写银行号码',
   		'contact_people.require' => '请填写紧急联系人',
   		'contact_phone.require' => '请填写紧急人电话',
   		'contact_relation.require' => '请填写紧急联系人关系',
    	'credit_number.require' => '请填写信用卡号',
    	'ali_account.require' => '阿里账号',
    	'credit_report_pics.require' => '请填写账单',
    ];

    protected $scene = [
        'apply' => [
            'user_id',
            'name',
            'phone',
            'bank_name',
            'bank_number',
            'contact_people',
            'contact_phone',
            'contact_relation',
            'credit_number',
            'ali_account',
			'credit_report_pics'
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
    /**
     * 验证手机号是否正确
     * @author honfei
     * @param number $mobile
     */
    function isMobile($value) {
    	if (strlen ( $value) != 11 || ! preg_match ( '/^1[3|4|5|7|8][0-9]\d{4,8}$/', $value)) {
    		return false;
    	} else {
    		return true;
    	}
    }
}