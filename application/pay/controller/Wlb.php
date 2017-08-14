<?php

namespace app\pay\controller;

use think\Controller;
use app\common\model\AdminCompany;
use app\common\model\CompanyOrder;
use org\Pay;
use org\hfpay;
use org\Jhpay;
 // 支付
class Wlb extends Controller {
	protected $pay_key = 'BF2BECF274DBA5501AA0CC7A2A33E672';
	public function pay() {
		if ($this->request->isPost ()) {
			try {
				$data = $this->request->param ();
				file_put_contents ( './pay_wlb.txt', $data );
				$company = AdminCompany::get ( [ 
						'username' => $data ['mername'] 
				] );
				$parm=array(
					'mername'=>$data['mername'],	
					'extra'=>$data['extra'],	
					'notify_url'=>$data['notify_url'],	
					'bank_no'=>$data['bank_no'],	
					'bank_sub'=>$data['bank_sub'],	
					'bank_name'=>$data['bank_name'],	
					'bank_code'=>$data['bank_code'],	
					'card_no'=>$data['card_no'],
					'order_id'=>$data['order_id'],
					'pay_type'=>$data['pay_type'],
					'amount'=>$data['amount']
				);
				$sign = $data ['sign'];
				unset ( $data ['sign'] );
				$valiate_sign = $this->_sign ( $parm);
				if ($sign != $valiate_sign) {
					$this->_returnMsg ( "00010", "签名错误" );
				}
				// 验证签名
				if (! $company->id) {
					$this->_returnMsg ( "00001", "公司不存在" );
				}
				
				$union_pay_config = unserialize ( $company->union_pay_config );
				if ($union_pay_config ['bank_name'] != $data ['bank_name']) {
					$this->_returnMsg ( "00002", "开户银行与此商户不相符" );
				}
				if ($union_pay_config ['bank_code'] != $data ['bank_code']) {
					$this->_returnMsg ( "00003", "联行号与此商户不相符" );
				}
				if ($union_pay_config ['bank_no'] != $data ['bank_no']) {
					$this->_returnMsg ( "00009", "银行卡号与此商户不相符" );
				}
				if ($union_pay_config ['card_no'] != $data ['card_no']) {
					$this->_returnMsg ( "000012", "身份证与此商户不相符" );
				}
				if ($union_pay_config ['bank_sub'] != $data ['bank_sub']) {
					$this->_returnMsg ( "000013", "开户支行与此商户不相符" );
				}
				if (! isset ( $data ['notify_url'] )) {
					$this->_returnMsg ( "00007", "请上传支付成功回调地址" );
				}
				
				if($data ['pay_type']=='WEIXIN_QRCODE_PAY'){
					$payCode=$this->weixinPay($data);
					if($payCode['code']=='00000'){
						$model = new CompanyOrder;
						$model->company_id =  $company->id;
						$model->order_id = $data['order_id'];
						$model->amount = $data['amount'];
						$model->notify_url = $data['notify_url'];
						$model->channel="HF";
						$model->extra=$data['extra'];
						$model->trade_id=$payCode['data']['orderId'];
						$model->save();
						$value = array (
								"resposeCode"=>0,
								"QRcodeURL"=>$payCode['data']['QRcodeURL'],
								"orderId"=>$data['order_id']
								
						);
						$this->_returnMsg ( "00000", 'success', $value );
					}else{
						return $this->_returnMsg ( 500, 'error',$value);
						
					}
					exit();
				}
				if($data ['pay_type']=='WEIXIN_PAY'){
					$data = array (
							'mername' => $data['mername'],
							'money' => $data['amount'],
							'productname' => "",
							'productdesc' => "",
							'extra' => $parm ['extra'] ,
							'order_id'=>$parm['order_id'],
							'redirecturl'=>$parm['redirecturl']
					);
					$pay_string=$this->create_pay_jhf ( $data );
					if($pay_string){
						$model = new CompanyOrder;
						$model->company_id =  $company->id;
						$model->order_id =  $parm['order_id'];
						$model->amount =  $parm['amount'];
						$model->notify_url =$parm['notify_url'];
						$model->channel="JHF";
						$model->extra=$data['extra'];
						$model->trade_id="";
						$model->save();
						$value = array (
								"pay_string"=>$pay_string
						);
					}
					$this->_returnMsg ( "00000", 'success', $value );
				}
				
				
				if($data ['pay_type']!='ALIPAY_QRCODE_PAY'){
					$this->_returnMsg ( "00020", "支付通道不支持" );
				}
				
				// 添加数据库
				$payCode = $this->QrCodePay ( array (
						'order_id' => $data ['order_id'],
						'amount' => $data ['amount'],
						'pay_type'=>'ALIPAY_QRCODE_PAY'
				) );
				
				
				if($payCode['msg']=='success'){
					$model = new CompanyOrder;
					$model->company_id =  $company->id;
					$model->order_id = $data['order_id'];
					$model->amount = $data['amount'];
					$model->notify_url = $data['notify_url'];
					$model->channel="wlb";
					$model->extra=$data['extra'];
					$model->save();
					$value = array (
							"resposeCode"=>0,
							"QRcodeURL"=>$payCode['respose'],
							"orderId"=>$data['order_id']
							
					);
					$this->_returnMsg ( "00000", 'success', $value );
				}
				
				
			} catch ( \Exception $e ) {
				return $this->_returnMsg ( 500, $e->getMessage () );
			}
		} else {
			$this->_returnMsg ( "00010", "访问页面方式不正确" );
		}
	}
	public function  weixinPay($data){
		try {
			$data= [
					'channel_code' => "WXPAY",
					'amount' =>floor($data['amount']*100),
					'info' => "company"
			];
			$Hf = new hfpay ();
			$res=$Hf->pay($data);
			if($res['respCode']=='000000'){
				return $result = array (
						'code' => "00000",
						'codeMsg' => "success",
						'data' => $res
				);
			}else{
				return $result = array (
						'code' => "50000",
						'codeMsg' => "error",
						'data' => $res
				);
			}			
		} catch ( \Exception $e ) {
			return $this->_returnMsg ( 500, $e->getMessage () );
		}
		
	}
	public function create_pay_jhf($data) {
		$jhpay_version = '1.0';
		$jhpay_merid = '26100526'; // 商户ID
		$jhpay_mername = $data ['mername']; // 商户名称
		$jhpay_merorderid = $data ['order_id']; // 订单号
		$jhpay_paymoney = $data ['money']; // 金额
		$jhpay_productname = $data ['productname']; // 商品名称，尽量不要用云购，1元云购等
		$jhpay_productdesc = $data ['productdesc']; // 商品描述
		$jhpay_userid = $userid;
		$jhpay_username = '';
		$jhpay_email = '';
		$jhpay_phone = '';
		$jhpay_extra = $data ['extra']; // 添加自定义内容
		$jhpay_custom = '';
		$jhpay_redirecturl = ""; // 自己回调地址
		$jhpay_md5 = 'st2eaagftjakyx5rtcxe0zx0o4uk1tq4'; // 尽量不要明文赋值
		
		$jhpay_config_input_charset = strtolower ( 'utf-8' );
		
		// 构造要请求的参数数组，无需改动
		$parameter = array (
				"version" => $jhpay_version,
				"merid" => $jhpay_merid,
				"mername" => $jhpay_mername,
				"merorderid" => $jhpay_merorderid,
				"paymoney" => $jhpay_paymoney,
				"productname" => $jhpay_productname,
				"productdesc" => $jhpay_productdesc,
				"userid" => $jhpay_userid,
				"username" => $jhpay_username,
				"email" => $jhpay_email,
				"phone" => $jhpay_phone,
				"extra" => $jhpay_extra,
				"custom" => $jhpay_custom,
				"redirecturl" => $data['redirecturl']
		);
		
		// 签名方式 不需修改
		$jhpay_config_sign_type = strtoupper ( 'MD5' );
		// 字符编码格式 目前支持 gbk 或 utf-8
		$jhpay_config_input_charset = strtolower ( 'utf-8' );
		
		$jhpay_config_transport = 'http';
		
		$jhpay_config = array (
				"partner" => $jhpay_merid,
				"key" => $jhpay_md5,
				"sign_type" => $jhpay_config_sign_type,
				"input_charset" => $jhpay_config_input_charset,
				"transport" => $jhpay_config_transport
		);
		$jhpaySubmit = new Jhpay ( $jhpay_config );
		$url = $jhpaySubmit->buildRequestForm ( $parameter, 'POST', 'submit' );
		return $url;
		exit ();
	}
	
	
	
	/**
	 * 二维码支付*
	 */
	public function QrCodePay(array $OrderData) {
		$amount = $OrderData ['amount'];
		$orderId = $OrderData ['order_id'];
		$type = $OrderData ['pay_type'];
		$channel = isset ( $OrderData ['channel'] ) ? $OrderData ['channel'] : 'wlb';
		// $url = 'http://xzl.xmzzss.com/ms.php/xzleasy/qr';
		$url = 'http://pay.sqstz360.com/api/pay';
		$this->_setDomain ();
		$ToorT1 = input ( 'T0orT1', 'T0' );
		$data = array (
				'channel' => "wlb",
				'rest' => 'qrcode',
				'account' => "18296111222",
				'password' => "123456",
				'mobile' => "18296111222",
				'desc' => '',
				'notify_url' => $this->domain . '/notify/Wlb/payNotify?&order_id=' . $orderId,
				'bank_no' => "6214837901282222",
				'bank_sub' => "招商银行股份有限公司南昌青山湖支行",
				'bankname' => "招商银行",
				'bank_code' => "308421022098",
				'card_no' => "360103198811144450",
				'name' => "陈闽武",
				'orderid' => $orderId,
				'paytype' => $type,
				'partner' => "2003",
				// 'notify_url_cash' => $this->domain . '/api/Wx/settlementNotify?type=' . $type,
				// 'settlement_type' => $this->PayeeInfoData['settlement_type'],
				'amount' => $amount * 100,
				'rate' => '0.3',
				'T0orT1' => "T0" 
		);
		if ($data ['account'] == "18979112001") {
			$data ['account'] = "18979112002";
		}
		$data ['sign'] = $this->_sign ( $data );
		$result = curlPost ( $url, http_build_query ( $data ) );
		// $result = strchr($result, '{');
		$result = json_decode ( $result );
		if ($result->data->url) {
			$return=array(
				'respose'=>	$result->data->url,
				'msg'=>'success'	
			);
			return $return;
		} else {
			// throw new Exception('info:' . $result->code_msg . ',status:' . $result->code);
			$this->_returnMsg($result->code,$result->code_msg);
		}
	}
	
	/**
	 * 设置服务器域名*
	 */
	private function _setDomain() {
		$this->domain = request ()->instance ()->domain ();
	}
	
	/**
	 * 生成签名
	 * 
	 * @param array $data        	
	 * @return string
	 */
	private function _sign(array $data) {
		ksort ( $data, SORT_STRING );
		$string = urldecode ( http_build_query ( $data ) ) . '&key=BF2BECF274DBA5501AA0CC7A2A33E672';
		return md5 ( $string );
	}
	/**
	 * _returnMsg
	 * 
	 * @param int $code        	
	 * @param string $msg        	
	 * @param array $data        	
	 * @return array
	 */
	private function _returnMsg($code = 200, $msg = '', $data = array()) {
		$result = array (
				'code' => $code,
				'codeMsg' => $msg,
				'data' => $data 
		);
		echo json_encode ( $result );
		exit ();
	}
	/**
	 * 密码加密
	 * 
	 * @param string $text        	
	 * @param string $key        	
	 * @return string
	 */
	private function passwordEncrypt($text = '', $key = '') {
		return md5 ( $text . $key );
	}
}