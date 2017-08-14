<?php

/**聚合富**/
namespace app\pay\controller;
use think\Controller;
use org\Jhpay;
use app\common\model\AdminCompany;
use app\common\model\CompanyOrder;
class Jhf extends Controller {
	public function pay() {
		if ($this->request->isPost ()) {
			try {
				$parm = $this->request->param ();
				file_put_contents ( './pay_jhf.txt', $data );
				$sign = $parm['sign'];
				unset ( $parm['sign'] );
				unset($parm['/pay/Jhf/pay']);
				$valiate_sign = $this->_sign ( $parm);
				if ($sign != $valiate_sign) {
					$this->_returnMsg ( "00010", "签名错误" );
				}
				$company = AdminCompany::get ( [
						'username' => $parm['mername']
				] );
				if (! $company->id) {
					$this->_returnMsg ( "00001", "公司不存在" );
				}
				if(! isset ( $parm['money'] )){
					$this->_returnMsg ( "00002", "请填写金额" );
				}
				if(! isset ( $parm['notify_url'] )){
					$this->_returnMsg ( "00003", "请填写回调地址" );
				}
				$data = array (
						'mername' => $parm ['mername'],
						'money' => $parm ['money'],
						'productname' => $parm ['productname'],
						'productdesc' => $parm ['productdesc'],
						'extra' => $parm ['extra'] ,
						'order_id'=>$parm['order_id'],
						'redirecturl'=>$parm['redirecturl']
				);
				$pay_string=$this->create_pay_jhf ( $data );
				if($pay_string){
					$model = new CompanyOrder;
					$model->company_id =  $company->id;
					$model->order_id =  $parm['order_id'];
					$model->amount =  $parm['money'];
					$model->notify_url =$parm['notify_url'];
					$model->channel="JHF";
					$model->extra=$data['extra'];
					$model->trade_id=$payCode['data']['orderId'];
					$model->save();
					$value = array (
							"pay_string"=>$pay_string
					);
				}
				
				$this->_returnMsg ( "00000", 'success', $value );
			} catch ( \Exception $e ) {
				return $this->_returnMsg ( 500, $e->getMessage () );
			}
		} else {
			$this->_returnMsg ( "00010", "访问页面方式不正确" );
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
}
