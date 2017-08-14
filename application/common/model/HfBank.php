<?php

namespace app\common\model;

use think\Model;

// 公司订单号
class HfBank extends Model {

	/**
	 * 事件注册
	 */
	protected static function init() {
		self::event ( 'before_insert', function ($model) {
			$model->create_time = nowTime ();
		} );
		self::event ( 'before_update', function ($model) {
			// 验证用户名唯一性
			$model->update_time = date("Y-m-d H:i:s");
		} );
	}
}