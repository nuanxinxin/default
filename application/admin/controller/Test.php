<?php
namespace app\admin\controller;
use think\Controller;
use org\Jhpay;
use org\Allinpay;
use app\common\model\ReplacePay;
class Test extends Controller
{
	
	public  function index(){
		$amount = $OrderData['amount'];
		$orderId = uniqid();
		$type = $OrderData['type'];
		// $url = 'http://xzl.xmzzss.com/ms.php/xzleasy/qr';ss
		$url = 'http://admin.sqstz360.com/pay/Jhf/pay';
		$ToorT1 = input('T0orT1', 'T0');
		$data = array(
				'mername'=>"半价商城",
				'extra' => '123',
				'notify_url' => $this->request->domain() . '/admin/Pub/test3?type=' . $type . '&order_id=' . $orderId,
				'productdesc' => "productdesc",
				'productname' => "productname",
				'order_id'=>$orderId,
				//            'notify_url_cash' => $this->domain . '/api/Wx/settlementNotify?type=' . $type,
				//            'settlement_type' => $this->PayeeInfoData['settlement_type'],
				'money' => 0.01,
		);
		$data['sign'] = $this->_sign($data);
		$result = curlPost($url, http_build_query($data));
		
		// $result = strchr($result, '{');
		$result = json_decode($result,true);  
		echo $result['data']['pay_string'];
		//dump($result['data']['pay_string']);
	}
	
	public function Allinpay(){
		$daifu = new Allinpay();
		$data=array(
			'account_no'=>'6232086500008779067',
			'account_name'=>'袁浩天',	
			'amount'=>'1',
			'bank_code'=>'102',	
			'user_id'=>'123',	
			'summary'=>'春风贷提现',
			'remark'=>'123'	
		);
		$ReplacePay=new ReplacePay();
		$result=$ReplacePay->singleCash($data);
		dump($result);
	}
	public function Allinpay2(){
		
		$daifu = new Allinpay();
		$daifu->batchTranx();
		
	}
	public function query(){
		$pay = new ReplacePay();
		$data=array(
				'req_sn'=>'200604000000445-dtdrtert452352543'
				
				
		);
		$result=$pay->quertRet($data);
		dump($result);
	}
	public function index2(){
		
		$data = array(
				'mername'=>"半价商城",
				'extra' => '123',
				'notify_url' => $this->request->domain() . '/admin/Pub/test3?type=' . $type . '&order_id=' . $orderId,
				'productdesc' => "productdesc",
				'productname' => "productname",
				//            'notify_url_cash' => $this->domain . '/api/Wx/settlementNotify?type=' . $type,
				//            'settlement_type' => $this->PayeeInfoData['settlement_type'],
				'money' => 0.01,
		);
		$result=$this->create_pay_jhf($data);
		echo $result;
		dump($result);
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
	
	public function create_pay_jhf($data) {
		$jhpay_version = '1.0';
		$jhpay_merid = '26100526'; // 商户ID
		$jhpay_mername = $data ['mername']; // 商户名称
		$jhpay_merorderid = 123; // 订单号
		$jhpay_paymoney = $data ['money']; // 金额
		$jhpay_productname = $data ['productname']; // 商品名称，尽量不要用云购，1元云购等
		$jhpay_productdesc = $data ['productdesc']; // 商品描述
		$jhpay_userid = $userid;
		$jhpay_username = '';
		$jhpay_email = '';
		$jhpay_phone = '';
		$jhpay_extra = $data ['extra']; // 添加自定义内容
		$jhpay_custom = '';
		$jhpay_redirecturl = "www.baidu.com"; // 自己回调地址
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
				"redirecturl" => $jhpay_redirecturl
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
	
	
}