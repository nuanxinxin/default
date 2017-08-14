<?php

namespace app\common\model;

use think\Model;

// 公司订单号
class CompanyOrder extends Model {
	
	
	public function company()
	{
		return $this->belongsTo('AdminCompany', 'company_id', 'id');
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
}