<?php

namespace org;

use org\allinpayInter\PhpTools;
use app\common\model\ReplacePay;
use think\exception;
class Allinpay {
	private $password="111111";
	private $user_name='20042100000837904';
	private $version="03";
	private $merchant_id='200421000008379';
	public function __construct() {
		header ( 'Content-Type: text/html; Charset=UTF-8' );
	}
	public function singleCash($data) {
		try {
			$req_sn = $this->merchant_id . time () . mt_rand ( 1000, 9999 );
			$tools = new PhpTools ();
			// 源数组
			$params = array (
					'INFO' => array (
							'TRX_CODE' => '100014',
							'VERSION' => $this->version,
							'DATA_TYPE' => '2',
							'LEVEL' => '6',
							'USER_NAME' => $this->user_name,
							'USER_PASS' => $this->password,
							'REQ_SN' => $req_sn 
					),
					'TRANS' => array (
							'BUSINESS_CODE' => '09900',
							'MERCHANT_ID' => $this->merchant_id,
							'SUBMIT_TIME' => date("YmdHis"),
							'BANK_CODE' => $data['bank_code'],
							'ACCOUNT_TYPE' => '00',
							'ACCOUNT_NO' => $data['account_no'],
							'ACCOUNT_NAME' => $data['account_name'],
							'ACCOUNT_PROP' => '0',
							'AMOUNT' => $data['amount'],
							'CURRENCY' => 'CNY',
							'CUST_USERID' => $data['user_id'],
							'SUMMARY' =>$data['summary'],
							'REMARK' => $data['remark'] 
					) 
			);
			// 发起请求
			$result = $tools->send ( $params );
			$model = new ReplacePay;
			$model->req_sn =  $req_sn;
			$model->submit_time=date("YmdHis");
			$model->account_name=$data['account_name'];
			$model->account_no=$data['account_no'];
			$model->bank_code=$data['bank_code'];
			$model->amount =  $data['amount'];
			$model->summary=$data['summary'];
			$model->remark=$data['remark'];
			$model->user_id=$data['user_id'];
			$model->return_code=$result['AIPG']['INFO']['RET_CODE'];
			$model->return_msg=$result['AIPG']['INFO']['ERR_MSG'];
			$model->save();
			return $result;
		} catch ( \Exception $e ) {
			throw Exception ($e->getMessage());
		}
	}
	public function batchTranx() {
		$tools = new PhpTools ();
		// 源数组
		$req_sn = $this->merchant_id . time () . mt_rand ( 1000, 9999 );
		$params = array (
				'INFO' => array (
						'TRX_CODE' => '100001',
						'VERSION' => '03',
						'DATA_TYPE' => '2',
						'LEVEL' => '6',
						'USER_NAME' => $this->user_name,
						'USER_PASS' => $this->password,
						'REQ_SN' => $req_sn
				),
				'BODY' => array (
						'TRANS_SUM' => array (
								'BUSINESS_CODE' => '10600',
								'MERCHANT_ID' => $this->merchant_id,
								'SUBMIT_TIME' => date("YmdHis"),
								'TOTAL_ITEM' => '2',
								'TOTAL_SUM' => '2000',
								'SETTDAY' => '' 
						),
						'TRANS_DETAILS' => array (
								'TRANS_DETAIL' => array (
										'SN' => '00001',
										'E_USER_CODE' => '00001',
										'BANK_CODE' => '0105',
										'ACCOUNT_TYPE' => '00',
										'ACCOUNT_NO' => '6225883746567298',
										'ACCOUNT_NAME' => '张三',
										'PROVINCE' => '',
										'CITY' => '',
										'BANK_NAME' => '',
										'ACCOUNT_PROP' => '0',
										'AMOUNT' => '1000',
										'CURRENCY' => 'CNY',
										'CUST_USERID' => '用户自定义号',
										'REMARK' => '备注信息1',
										'SUMMARY' => '',
										'UNION_BANK' => '010538987654' 
								),
								'TRANS_DETAIL2' => array (
										'SN' => '00002',
										'E_USER_CODE' => '00001',
										'BANK_CODE' => '0103',
										'ACCOUNT_TYPE' => '00',
										'ACCOUNT_NO' => '6225883746567228',
										'ACCOUNT_NAME' => '王五',
										'PROVINCE' => '',
										'CITY' => '',
										'BANK_NAME' => '',
										'ACCOUNT_PROP' => '0',
										'AMOUNT' => '1000',
										'CURRENCY' => 'CNY',
										'CUST_USERID' => '用户自定义号',
										'REMARK' => '备注信息2',
										'SUMMARY' => '',
										'UNION_BANK' => '010538987654' 
								) 
						) 
				) 
		);
		// 发起请求
		$result = $tools->send ( $params );
		dump ( $result );
		if ($result != FALSE) {
			echo '验签通过，请对返回信息进行处理';
			// 下面商户自定义处理逻辑，此处返回一个数组
		} else {
			print_r ( "验签结果：验签失败，请检查通联公钥证书是否正确" );
		}
	}
	
	public function queryRet($data){
		try {
		$tools=new PhpTools();
		// 源数组
			$params = array(
					'INFO' => array(
							'TRX_CODE' => '200004',
							'VERSION' => $this->version	,
							'DATA_TYPE' => '2',
							'LEVEL' => '6',
							'USER_NAME' => $this->user_name,
							'USER_PASS' => $this->password,
					),
					'QTRANSREQ' => array(
							'QUERY_SN' =>$data['req_sn'],
							'MERCHANT_ID' => $this->merchant_id,
							'STATUS' => '2',
							'TYPE' => '1',
							'START_DAY' => $data['start_day'],
							'END_DAY' => $data['end_day']
					),
			);
			//发起请求
			$result = $tools->send( $params);
			return $result;
		} catch ( \Exception $e ) {
			throw Exception ($e->getMessage());
		}
	}
}
