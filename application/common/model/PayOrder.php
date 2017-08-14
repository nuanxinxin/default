<?php

namespace app\common\model;

use think\Model;

// 公司账号表
class PayOrder extends Model {
	
	/**
	 * 事件注册
	 */
	protected static function init() {
		self::event ( 'before_insert', function ($model) {
			$model->create_time=time();
			$model->status=0;

		} );
		
		self::event ( 'before_update', function ($model) {
			
			$model->update_time=time();
		} );
	}
}