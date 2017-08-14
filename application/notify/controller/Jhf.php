<?php
namespace app\notify\controller;
use think\Controller;
use app\common\model\CompanyOrder;
class Jhf extends Controller{
	public function payNotify(){
		
		include "../extend/org/jhf/jhfpay.config.php";
		include "../extend/org/jhf/lib/jhpay_core.function.php";
		include "../extend/org/jhf/lib/jhpay_notify.class.php";
		$jhpayNotify = new \JhpayNotify($jhf_config);
		$verify_result = $jhpayNotify->verifyMD5();
		//商户订单号
 		if($verify_result){
			$out_trade_no = $_POST['merorderid'];
			$trade_no = $_POST['tradeid'];
			$trade_status = $_POST['success'];
			$success_money = $_POST['successmoney'];
			if ($trade_status == '1') {
				$orderShow=CompanyOrder::get(['order_id'=>$out_trade_no,'channel'=>'JHF']);
				$orderShow->status=1;
				$orderShow->trade_id=$trade_no;
				$orderShow->save();
				$data=array(
						'amount'=>$success_money,
						'order_id'=>$out_trade_no,
						'complete_time'=>time(),
						'responseCode'=>'00000',
						'tradeStatus'=>'sucesss',
						'extra'=>$orderShow->extra
				);
				$data['sign'] = $this->_sign($data);
				curlPost($orderShow->notify_url, http_build_query($data));
				//==
				file_put_contents ( './pay_jhf_notify.txt', serialize($_POST) );
				//==
			}
			
			echo "success";
			Header("Response: 1");
			exit;
 		}else{
			echo "fail";
			Header("Response: 0");
			exit;
		} 
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
}