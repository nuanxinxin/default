<?php

namespace app\admin\controller;

use app\common\model\PawnInfo;
use app\common\model\PawnInfoPics;
use app\common\model\Setting;
use org\PrivateImage;
use app\common\model\User;
use app\common\model\CreditRecord;
use EasyWeChat\Foundation\Application;
use think\Db;

class Pawn extends AdminBase {
	public function _initialize() {
		parent::_initialize ();
		if (LOGIN_TYPE != 'admin') {
			$this->error ( '非法操作' );
		}
	}
	public function index($keyword = '', $status = '', $apply_refund = '') {
		$where = [ ];
		if ($status != '' && $status != '全部') {
			$where ['status'] = $status;
		}
		
		if ($apply_refund) {
			$where ['apply_refund'] = 1;
		}
		
		$list = PawnInfo::where ( 'title|desc', 'like', '%' . $keyword . '%' )->where ( $where )->order ( 'id desc' )->paginate ( 10, false, [ 
				'query' => [ 
						'keyword' => $keyword,
						'status' => $status,
						'apply_refund' => $apply_refund 
				] 
		] );
		$this->assign ( 'list', $list );
		return $this->fetch ();
	}
	public function detail($id) {
		$data = PawnInfo::get ( $id );
		$pics = array ();
		$userId = User::userIdById ( $data->user_id );
		foreach ( $data->PawnInfoPics ()->column ( 'path' ) as $pic ) {
			$pics [] = PrivateImage::getImageUrl ( $pic, $userId );
		}
		$this->assign ( 'pics', $pics );
		$this->assign ( 'data', $data );
		return $this->fetch ();
	}
	public function refund($id) {
		$pawnInfo = PawnInfo::get ( $id );
		$model = new CreditRecord ();
		$result = $model->pawnBackMoney ( $pawnInfo );
		if ($result === true) {
			$this->success ( '操作成功' );
		} else {
			$this->error ( $result );
		}
	}
	public function statusSuccess($id) {
		$pawnInfo = PawnInfo::get ( $id );
		$jobs = array ();
		$openids = User::where ( 'wx', 'neq', '' )->column ( 'wx' );
		foreach ( $openids as $openid ) {
			$jobs [] = array (
					'first' => '名表、名包、钻戒寄售，挥泪转让！！！',
					'keyword1' => $pawnInfo->title,
					'keyword2' => $pawnInfo->pawn_type,
					'keyword3' => date ( 'Y-m-d' ),
					'remark' => $pawnInfo->desc,
					'openid' => $openid 
			);
		}
		Db::table ( 'snake_jobs' )->insertAll ( $jobs );
		$pawnInfo->status = '交易中';
		$pawnInfo->distance_out_time = strtotime ( '+' . Setting::getConfigValue ( 3, 'pawn_distance_out_time' ) . ' day' );
		$pawnInfo->save ();
		$this->success ( '操作成功' );
	}
	public function statusFailed($id) {
		$pawnInfo = PawnInfo::get ( $id );
		$pawnInfo->status = '待审核';
		$pawnInfo->save ();
		$this->success ( '操作成功' );
	}
	public function delPawnInfo() {
		$pawnId = $this->request->param ( 'id/d' );
		$result = PawnInfo::get ( $pawnId );
		if ($result->pay_margin_user_id > 0) {
			$this->error('已交押金无法删除' );
		}		
		if ($result->status == '待审核'||$result->status == '已下架') {
			$delResult = $result->del();
			if ($delResult === true) {
				$this->success ( "删除成功" );
			} else {
				$this->error ( "删除失败" );
			}
		} else {
			$this->error ( "交易的典当不能删除" );
		}
	}
	public function setting() {
		if (isPost ()) {
			$data = $this->request->except ( '__token__', 'post' );
			$model = new Setting ();
			foreach ( $data as $index => $item ) {
				$model->clearCache ( 3 )->where ( 'name', $index )->setField ( 'value', $item );
			}
			$this->success ( '设置成功' );
		} else {
			$this->assign ( 'data', Setting::getConfig ( 3 ) );
			return $this->fetch ();
		}
	}
}