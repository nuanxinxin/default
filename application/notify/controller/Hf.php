<?php

namespace app\notify\controller;
use app\common\model\CompanyOrder;
use think\Controller;

class Hf extends Controller {
	public function index() {
		$post = $this->request->param ();
		
		file_put_contents ( './pay_hf_notify.txt', serialize ( $post ) );
		if ($post ['respCode'] == '000000') {
			$baseWhere = array(
					'trade_id' => $post['orderId'],
					'status'=>0
			);
			if(CompanyOrder::where($baseWhere)->count()){
				$orderShow = CompanyOrder::get ( [ 
						'trade_id' => $post['orderId'],
				] );
				$orderShow->status = 1;
				$orderShow->save ();
				$notify_url = $orderShow->notify_url;
				$data = array (
						'amount' => $orderShow -> amount,
						'order_id' => $orderShow->order_id,
						'complete_time' => time (),
						'responseCode' => '00000',
						'tradeStatus' => 'sucesss',
						'extra' => $orderShow->extra 
				);
				$data ['sign'] = $this->_sign ( $data );
				curlPost ( $notify_url, http_build_query ( $data ) );
			}
		}
	}
	/**
	 * 生成签名
	 * @param array $data
	 * @return string
	 */
	private function _sign(array $data)
	{
		ksort($data, SORT_STRING);
		$string = urldecode(http_build_query($data)) . '&key=BF2BECF274DBA5501AA0CC7A2A33E672';
		return md5($string);
	}
}