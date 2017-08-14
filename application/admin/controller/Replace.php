<?php

namespace app\admin\controller;

use app\common\model\ReplacePay;
use app\common\model\AllinpayBank;

class Replace extends AdminBase {
	public function pay() {
		if (isPost ()) {
			try {
				$data = array (
						'account_no' => $this->request->param ( 'account_no' ),
						'account_name' => $this->request->param ( 'account_name' ),
						'amount' => floor ( $this->request->param ( 'amount' ) * 100 ),
						'summary' => $this->request->param ( 'summary' ),
						'remark' => $this->request->param ( 'remark' ),
						'bank_code' => $this->request->param ( "bank_code" ) 
				);
				$ReplacePay = new ReplacePay ();
				$result = $ReplacePay->singleCash ( $data );
				if ($result !== true) {
					abort ( 500, $result );
				}
			} catch ( \Exception $e ) {
				$this->error ( $e->getMessage () );
			}
			$this->success("处理成功");
			
		} else {
			$this->assign ( 'banks', AllinpayBank::all () );
			return $this->fetch ();
		}
	}
	public function detail() {
		if (isPost ()) {
			try {
				$req_sn = $this->request->param ( "req_sn" );
				$pay = new ReplacePay ();
				$data = array (
						'req_sn' => $req_sn 
				);
				$result = $pay->quertRet ( $data );
			} catch ( \Exception $e ) {
				return json ( [ 
						'code' => 500,
						'message' => $e->getMessage () 
				] );
			}
			return json ( [
					'code' => 200,
					'message' =>$result['ERR_MSG']
			] );
		} else {
			$list = ReplacePay::paginate ( 10 );
			$this->assign ( "list", $list );
			return $this->fetch ();
		}
	}
}