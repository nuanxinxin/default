<?php

/**聚合富**/
namespace app\pay\controller;
use think\Controller;
class Bns extends Controller {
		public function  pay(){
			
			$url="http://www.brcb-sandbox.sunfund.com/gateway";
			$data['service_type']='WECHAT_WEBPAY';
			$data['mch_id']='C149628461779610201';
/* 			$data['appid']=''; */
			$data['out_trade_no']=uniqid();
			$data['device_info']='WECHAT_SCANNED';
			$data['body']='body';
			$data['attach']='attach';
			$data['total_fee']='1';
			$data['spbill_create_ip']='127.0.0.1';
			$data['notify_url']='www.baidu.com';
			$data['nonce_str']='3211';
			$data['sign']=$this->_sign($data);
			dump($data);
			$result=curlPost($url,$data);
			dump($result);
			
		}
		public function wechat(){
			$data['service_type']='WECHAT_WEBPAY';
			$data['mch_id']='C149628461779610201';
			/* 			$data['appid']=''; */
			$data['out_trade_no']=uniqid();
			$data['device_info']='WECHAT_SCANNED';
			$data['body']='body';
			$data['attach']='attach';
			$data['total_fee']='1';
			$data['spbill_create_ip']='127.0.0.1';
			$data['notify_url']='www.baidu.com';
			$data['nonce_str']='3211';
			$data['sign']=$this->_sign($data);
			$result=$this->buildRequestForm($data);
			echo $result;
		}
		/**
		 * 生成签名
		 *
		 * @param array $data
		 * @return string
		 */
		private function _sign(array $data) {
			ksort ( $data, SORT_STRING );
			$string = urldecode ( http_build_query ( $data ) ) . 'acc503c56b0c4fd399f7f7093d25223c';
			return strtoupper(md5 ( $string ));
		}
		/**
		 * 建立请求，以表单HTML形式构造（默认）
		 * @param $para_temp 请求参数数组
		 * @param $method 提交方式。两个值可选：post、get
		 * @param $button_name 确认按钮显示文字
		 * @return 提交表单HTML文本
		 */
		function buildRequestForm($para_temp) {
			//待请求参数数组
			$sHtml = "<h3>正在跳转支付页面.</h3>";
			$sHtml .= "<form id=jhfpaysubmit' name='form' action='http://www.brcb-sandbox.sunfund.com/gateway' method='post'>";
			while (list ($key, $val) = each ($para_temp)) {
				$sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
				//echo $key.'->'.$val.'</br>';
			}
			//submit按钮控件请不要含有name属性
			$sHtml = $sHtml."<script>document.forms['form'].submit();</script>";
			
			return $sHtml;
		}
		
}

