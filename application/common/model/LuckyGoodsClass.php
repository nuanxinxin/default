<?php

namespace app\common\model;

use think\Model;

class LuckyGoodsClass extends Model {
	/**
	 * 事件注册
	 */
	protected static function init() {
		self::event ( 'before_insert', function ($model) {
			$model->create_time = nowTime ();
		} );
	}		
	public function saveGoodsClass($data) {
		if( $data['id']){
			$class= LuckyGoodsClass::get( $data['id']);
		}else{
			$class = new LuckyGoodsClass();
			
		}
		$class->class_name = $data ['class_name'];
		$class->save();
	}
}
