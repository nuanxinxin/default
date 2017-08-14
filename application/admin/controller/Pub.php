<?php

namespace app\admin\controller;

use think\Controller;
use app\common\model\AdminUser;
use app\common\model\AdminCompany;
use org\Easypay;
use org\hfpay;
use org\Identity;

class Pub extends Controller {
	/**
	 * 管理员登录
	 * 
	 * @return mixed
	 */
	public function login() {
		if (isPost ()) {
			$username = $this->request->post ( 'username' );
			$password = $this->request->post ( 'password' );
			$model = new AdminUser ();
			$result = $model->login ( $username, $password );
			if ($result === true) {
				$this->success ( '登陆成功', '/admin' );
			} else {
				$this->error ( $result );
			}
		} else {
			$data = [ 
					'title' => '后台登录' 
			];
			$this->assign ( 'data', $data );
			return $this->fetch ();
		}
	}
	
	/**
	 * 公司登录
	 * 
	 * @return mixed
	 */
	public function company() {
		if (isPost ()) {
			$username = $this->request->post ( 'username' );
			$password = $this->request->post ( 'password' );
			$model = new AdminCompany ();
			$result = $model->login ( $username, $password );
			if ($result === true) {
				$this->success ( '登陆成功', '/admin' );
			} else {
				$this->error ( $result );
			}
		} else {
			$data = [ 
					'title' => '公司登录' 
			];
			$this->assign ( 'data', $data );
			return $this->fetch ( 'company_login' );
		}
	}
	
	/**
	 * 登录注销
	 */
	public function signOut() {
		session ( 'admin', null );
		session ( 'company', null );
		$this->redirect ( '/admin' );
	}
	
	/**
	 * 密码修改
	 * 
	 * @return \think\response\View
	 */
	public function updatePwd() {
		try {
			if (isPost ()) {
				$oldPassword = input ( 'old_password' );
				$newPassword = input ( 'new_password' );
				if (session ( 'login_type' ) == 'admin') {
					$model = session ( 'admin' );
				} else {
					$model = session ( 'company' );
				}
				
				$model->old_password = $oldPassword;
				$model->new_password = $newPassword;
				$model->updatePwd ();
			} else {
				return $this->fetch ( 'update_pwd' );
			}
		} catch ( \Exception $e ) {
			$this->error ( $e->getMessage () );
		}
		$this->success ( '修改成功', path ( 'signOut' ) );
	}
	public function test4() {
		try {
			$data= [ 
					'channel_code' => 'WXPAY',
					'amount' => 100,
					'info' => '' 
			];
			$pay = new hfpay ($data);
			$result = $pay->payTest ( $data);
			return $this->_returnMsg( 200, "success", $result);
		} catch ( \Exception $e ) {
			return $this->_returnMsg( 500, $e->getMessage () );
		}
	}
	public function test5() {
		$url = $this->request->domain().'/pay/Jhf/pay';
		$orderId = uniqid ();
		$data = array(
				'mername'=>"haitian",
				'extra' => "12333",
				'notify_url' => $this->request->domain () . '/admin/Pub/test3?type=' . $type . '&order_id=' . $orderId,
				'productdesc' => "充值卡",
				'productname' => "充值卡",
				'order_id'=>$orderId,
				'money' => 0.01,
				'redirecturl'=>$referer
		);
		$data['sign'] = $this->_sign($data);
		$result = curlPost($url, http_build_query($data));
		$result = json_decode($result,true);
		echo $result['data']['pay_string'];
	}
	
	
	
	
	
	/**
	 * _returnMsg
	 * @param int $code
	 * @param string $msg
	 * @param array $data
	 * @return array
	 */
	private function _returnMsg($code = 200, $msg = '', $data = array())
	{
		$result = array('code' => $code, 'codeMsg' => $msg, 'data' => $data);
		return json_encode($result);
	}
	public function test6() {
		$pay = new hfpay ();
		$result = $pay->verifyInfo ( $data );
	}
	public function test7() {
		$url = $this->request->domain().'/pay/Hf/pay';
		$orderId = uniqid ();
		$data=array(
				'channel_code'=>'ALIPAY',
				'amount'=>1,
				'mername'=>'haitian',
				'extra'=>"123",
				'order_id'=>$orderId,
				'notify_url' => $this->request->domain () . '/admin/Pub/test3?type=' . $type . '&order_id=' . $orderId,
		);
		$data ['sign'] = $this->_sign ( $data );
		$result = curlPost ( $url, http_build_query ( $data ) );
		dump($result);
	}

	
	public function verify() {
	}
	public function test() {
		$pay = new Easypay ();
		$parameters = array (
				"merchantNo" => "DFAC45001XJCZ",
				"outTradeNo" => uniqid (),
				"currency" => "CNY",
				"amount" => 100,
				"payType" => "CREDIT_BANK_CARD_PAY",
				"content" => "PHP SDK",
				"callbackURL" => "http://gw.xzlpay.com/callback.do" 
		);
		dump ( $parameters );
		$response = $pay->request ( "com.opentech.cloud.easypay.trade.create", "0.0.1", $parameters );
		dump ( $response );
	}
	public function test1() {
		$amount = $OrderData ['amount'];
		$orderId = uniqid ();
		$type = $OrderData ['type'];
		// $url = 'http://xzl.xmzzss.com/ms.php/xzleasy/qr';ss
		$url =  $this->request->domain().'/pay/Wlb/pay';
		$ToorT1 = input ( 'T0orT1', 'T0' );
		$data = array (
				'mername' => "haiteng",
				'extra' => '123',
				'notify_url' => $this->request->domain () . '/admin/Pub/test3?type=' . $type . '&order_id=' . $orderId,
				'bank_no' => "6222021502016825725",
				'bank_sub' => "南昌市塘山支行",
				'bank_name' => "工商银行",
				'bank_code' => "102421000405",
				'card_no' => "360111199101312538",
				'order_id' => $orderId,
				'pay_type' => 'WEIXIN_PAY',
				// 'notify_url_cash' => $this->domain . '/api/Wx/settlementNotify?type=' . $type,
				// 'settlement_type' => $this->PayeeInfoData['settlement_type'],
				'amount' => 0.01 
		);
		$data ['sign'] = $this->_sign ( $data );
		$result = curlPost ( $url, http_build_query ( $data ) );
		dump ( $result );
		// $result = strchr($result, '{');
		$result = json_decode ( $result,true );
		echo $result['data']['pay_string'];
	}
	public function test3() {
		$data = $this->request->param ();
		file_put_contents ( './pay_wlb_test3.txt', "test" . serialize ( $data ) );
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
	 * 选项卡结构页面入口
	 * 
	 * @param $parameter 格式：title,module/controller/action
	 *        	多个用";"分隔
	 * @return mixed
	 */
	public function tab($op) {
		$op = array_filter ( explode ( ';', $op ) );
		$list = [ ];
		foreach ( $op as $item ) {
			list ( $title, $url ) = explode ( ',', $item );
			$list [] = [ 
					'title' => $title,
					'url' => path ( $url ) 
			];
		}
		$this->assign ( 'list', $list );
		return $this->fetch ();
	}
}