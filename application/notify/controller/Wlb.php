<?php
namespace app\notify\controller;
use think\Controller;
use app\common\model\CompanyOrder;
class Wlb extends Controller{
	public function payNotify(){
		$post = $this->request->param ();
		file_put_contents ( './pay_wlb_notify.txt', $data );
		 if ($post['uu'] == '45e30e155de1a4a1fe506228eb870ddf') { 
			file_put_contents ( './pay_wlb_notify.txt', $post['order_id']."成功");
			$order_id=$post["order_id"];
			$orderShow=CompanyOrder::get(['order_id'=>$order_id]);
			$orderShow->status=1;
			$orderShow->save();
			$notify_url=$orderShow->notify_url;
			$data=array(
					'amount'=>$orderShow>amount,
					'order_id'=>$order_id,
					'complete_time'=>time(),
					'responseCode'=>'00000',
					'tradeStatus'=>'sucesss',
					'extra'=>$orderShow->extra
			);
			$data['sign'] = $this->_sign($data);
			curlPost($notify_url, http_build_query($data));
 		} else {
			file_put_contents ( './pay_wlb_notify.txt', "签名错误");
			echo "FAILED";
		} 
		
		
		
	}
	public function test(){
		$data=$this->request->param ();
		file_put_contents ( './pay_wlb_test.txt', "test".serialize($data));
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


