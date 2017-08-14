<?php

namespace app\admin\controller;

use app\common\model\CompanyOrder as CompanyOrderModel;
use app\common\model\CompanySettle as CompanySettleModel;
use app\common\model\AdminCompany;

class CompanyOrder extends AdminBase {
	public function _initialize() {
		parent::_initialize ();
		// 公司ID
		define ( 'COMPANY_ID', session ( 'company.id' ) );
	}
	function orderList() {
		$status = $this->request->param ( "status" );
		$order_id = $this->request->param ( "order_id" );
		$settle = $this->request->param ( "settle" );
		$baseWhere=array();
		if (session ( 'login_type' ) == 'admin') {
			$company_id = $this->request->param ( "company_id" );
			$channel= $this->request->param ( "channel" );
			if (isset ( $settle )&&$settle!='') {
				$baseWhere['settle']=$settle;
			}
			if (isset ( $company_id )&&$company_id!='') {
				$baseWhere['company_id']=$company_id;
			}
			if (isset ( $channel)&&$channel!='') {
				$baseWhere['channel']=$channel;
			}
			$this->assign ( 'companys', AdminCompany::all () );
		} else {
			if (isset ( $settle )) {
				$baseWhere = array (
						'company_id' => COMPANY_ID,
						'settle' => $settle 
				);
			} else {
				$baseWhere = array (
						'company_id' => COMPANY_ID 
				);
				$settleWhere = array (
						'company_id' => COMPANY_ID 
				);
			}
		}
		if ($status != '' && $status != '全部') {
			$list = CompanyOrderModel::where ( $baseWhere )->where ( 'order_id', 'like', '%' . $order_id . '%' )->where ( 'status', $status )->order ( 'id desc' )->paginate ( 20, false, [ 
					'query' => [ 
							'status' => $status,
							'settle' => $settle,
							'company_id'=>$company_id,
							'channel'=>$channel
					] 
			] );
		} else {
			$list = CompanyOrderModel::where ( $baseWhere )->where ( 'order_id', 'like', '%' . $order_id . '%' )->order ( 'id desc' )->paginate ( 20 );
		}
		
		// 已结算(元)
		$this->assign ( 'totalSettle', CompanyOrderModel::where ( "status=1" )->where ( $baseWhere )->where ( 'settle', '1' )->sum ( 'amount' ) );
		// 未结算(元)
		$this->assign ( 'todayTradeNo', CompanyOrderModel::where ( "status=1" )->where ( $baseWhere )->where ( 'settle', '0' )->whereTime('update_time', 'today')->sum ( 'amount' ) );
		
		$this->assign ( 'totalSettleNo', CompanyOrderModel::where ( "status=1" )->where ( $baseWhere )->where ( 'settle', '0' )->sum ( 'amount' ) );
		$this->assign ( 'canTotalSettleNo', CompanyOrderModel::where ( "status=1" )->where ( $baseWhere )->where ( 'settle', '0' )->where('update_time','<= time',date("Y-m-d 00:00:00"))->sum ( 'amount' ));		
		// 待结算
		$this->assign ( 'totalCommission', CompanySettleModel::where ( $settleWhere )->sum ( 'commission' ) );
		$this->assign ( 'list', $list );
		return $this->fetch ();
	}
	public function orderSettle() {
		
		$company=AdminCompany::get(['id'=>COMPANY_ID]);
		$where = array (
				'company_id' => COMPANY_ID,
				'status' => '1',
				'settle' => '0' 
		);
		$price = round ( CompanyOrderModel::where ( $where )->where('update_time','<= time',date("Y-m-d 00:00:00"))->sum ( 'amount' ), 2 );
		$order_count_sum= bcmul(CompanyOrderModel::where ( $where )->where('update_time','<= time',date("Y-m-d 00:00:00"))->count(),$company->order_expense,2);
		$sx_price=bcmul ( bcsub($price, $order_count_sum,2), $company->commission_charge?$company->commission_charge:0.008, 2 );
		if ($price< 100) {
			$this->error ( "提现金额不足100元" );
		}
		$data ['order_id'] = implode ( ',', CompanyOrderModel::where ( $where )->where('update_time','<= time',date("Y-m-d 00:00:00"))->column ( 'id' ) );
		$data ['commission'] = bcadd($sx_price,$order_count_sum,2);
		$data ['price'] = bcsub ( $price, $data ['commission'], 2 );
		$data ['company_id'] = COMPANY_ID;
		$result = CompanySettleModel::create ( $data );
		if (CompanyOrderModel::where ( $where )->where('update_time','<= time',date("Y-m-d 00:00:00"))->update ( [ 'settle' => '1' ] ) && $result) {
			$this->success ( '结算成功.结算金额为:'.$data ['price']);
		} else {
			$this->error ( '结算失败。' );
		}
	}
	public function settleList() {
		if (session ( 'login_type' ) == 'admin') {
			$company_id = $this->request->param ( "company_id" );
			$this->assign ( 'companys', AdminCompany::all () );
			if ($company_id) {
				$baseWhere = array (
						'company_id' => $company_id 
				);
			}
		} else {
			$baseWhere = array (
					'company_id' => COMPANY_ID 
			);
		}
		if ($status != '' && $status != '全部') {
			$list = CompanySettleModel::where ( $baseWhere )->where ( 'order_id', 'like', '%' . $order_id . '%' )->where ( 'status', $status )->order ( 'id desc' )->paginate ( 10, false, [ 
					'query' => [ 
							'status' => $status,
							'company_id' => $company_id 
					] 
			] );
		} else {
			$list = CompanySettleModel::where ( $baseWhere )->where ( 'order_id', 'like', '%' . $order_id . '%' )->order ( 'id desc' )->paginate ( 10 );
		}
		$this->assign ( 'list', $list );
		return $this->fetch ();
	}
	
	public function dailyrecord(){
		if (session ( 'login_type' ) == 'admin') {
			$baseWhere = array (
					'status'=>1
			);
		} else {
				$baseWhere = array (
						'company_id' => COMPANY_ID,
						'status'=>1
				);
		}
		
		$list = CompanyOrderModel::where ( $baseWhere )->field("FROM_UNIXTIME(create_time,'%Y-%m-%d') as days,sum(amount) as total_price")->order("days desc")->group("FROM_UNIXTIME(create_time,'%Y-%m-%d')")->paginate ( 10 );
		
		$this->assign ( 'list', $list );
		return $this->fetch ();
		//dump($list);
		
	}
	
	
	public function detail(){
		$date = $this->request->param ( "id" );
		$order_id= $this->request->param ( "order_id" );
		$settle= $this->request->param ( "settle" );
		if($date){
			session("date_",$date,"date");
		}else{
			$date=session("date_",'',"date");
		}
		
		$nextTime=date("Y-m-d H:i:s",bcadd(strtotime($date),86399));
		if (session ( 'login_type' ) == 'admin') {
			$company_id = $this->request->param ( "company_id" );
			$this->assign ( 'companys', AdminCompany::all () );
			$channel= $this->request->param ( "channel" );
			
			$baseWhere['status'] = 1;
			if (isset ( $company_id )&&$company_id!='') {
				$baseWhere['company_id']=$company_id;
			}
			if (isset ( $channel)&&$channel!='') {
				$baseWhere['channel']=$channel;
			}
			if (isset ( $settle )&&$settle!='') {
				$baseWhere['settle'] = $settle;
			} 
			$this->assign ( 'todayTradeNo', CompanyOrderModel::where('create_time','between time',[$date,$nextTime])->where ( $baseWhere )->where ( 'order_id', 'like', '%' . $order_id . '%' )->sum ( 'amount' ) );
		} else {
			$baseWhere = array (
					'status'=>1,
					'company_id' => COMPANY_ID
			);
		}
		$list = CompanyOrderModel::where ( $baseWhere )->where('create_time','between time',[$date,$nextTime])->where ( 'order_id', 'like', '%' . $order_id . '%' )->order ( 'id desc' )->paginate ( 10, false, [
				'query' => [
						'status' => $status,
						'settle' => $settle,
						'company_id'=>$company_id,
						'channel'=>$channel
				]
		] );
		$this->assign('date',$date);
		$this->assign ( 'list', $list );
		return $this->fetch ();
	}
	
	public function settleDetail(){
		$id = $this->request->param ( "id" );
		if($id){
			session("id_",$id,"settleDetail");
		}else{
			$id=session("id_",'',"settleDetail");
		}
		$settleOrder=CompanySettleModel::get(['id'=>$id])->order_id;
		$list=CompanyOrderModel::where('id','in',$settleOrder)->paginate ( 10 );
		$this->assign ( 'list', $list );
		return $this->fetch ();
	}
	
	public function settleStatus() {
		if (session ( 'login_type' ) == 'admin') {
			$id = $this->request->param ( "id" );
			if (CompanySettleModel::where ( "id", $id )->update ( [ 
					'status' => '1' 
			] )) {
				$this->success ( '打款成功。' );
			} else {
				$this->error('打款失败。');
			}
		}else{
			$this->error("你没有权限进行此操作");
		}
		
	}
	
	
	
}