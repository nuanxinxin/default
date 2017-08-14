<?php

namespace app\common\model;
use think\Model;
use org\Allinpay;
use think\exception;
// 公司订单号
class ReplacePay extends Model {
	
	
	public function BankName() {
		return $this->belongsTo ( 'AllinpayBank', 'bank_code', 'bank_code' );
	}
	/**
	 * 事件注册
	 */
	protected static function init() {
		self::event ( 'before_insert', function ($model) {
			$model->create_time = nowTime ();
			$model->status=0;
		} );
		self::event ( 'before_update', function ($model) {
			// 验证用户名唯一性
			$model->update_time = nowTime ();
		} );
	}
	
	public function singleCash($data){
		try {
			$daifu = new Allinpay();
			$result=$daifu->singleCash($data);
			if($result['AIPG']['INFO']['RET_CODE']=='0000'){
				return true;
			}else{
				return $result['AIPG']['INFO']['ERR_MSG'];
				
			}
		} catch ( \Exception $e ) {
			throw Exception (  $e->getMessage());
			
		}
	}
	public function quertRet($data){
		try {
			$daifu = new Allinpay();
			$result=$daifu->queryRet($data);
			return $result['AIPG']['INFO'];
		} catch ( \Exception $e ) {
			return $e->getMessage();
		}
	}
	
	
	
}