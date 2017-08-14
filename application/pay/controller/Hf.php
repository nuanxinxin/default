<?php
/**恒丰**/
namespace app\pay\controller;
use think\Controller;
use org\hfpay;
use app\common\model\AdminCompany;
use app\common\model\CompanyOrder;
class Hf extends Controller {
	public function pay() {
		if ($this->request->isPost ()) {
			try {
				$param= $this->request->param ();
				$data=array(
						'channel_code'=>$param['channel_code'],
						'amount'=>$param['amount'],
						'mername'=>$param['mername'],
						'order_id'=>$param['order_id'],
						'extra'=>$param['extra'],
						'notify_url'=>$param['notify_url']
				);
				$sign=$param['sign'];
				$valiate_sign = $this->_sign ( $data );
				if ($sign != $valiate_sign) {
					$this->_returnMsg ( "00010", "签名错误" );
				}
				$company = AdminCompany::get ( [
						'username' => $data ['mername']
				] );
				// 验证签名
				if (! $company->id) {
					$this->_returnMsg ( "00003", "公司不存在" );
				}
				if (!isset($param['channel_code'])) {
					$this->_returnMsg ( "00001", "缺少必要参数channel_code");
				}
				if (!isset($param['amount'])) {
					$this->_returnMsg ( "00002", "缺少必要参数amount");
				}
				$data= [
						'channel_code' => $param['channel_code'],
						'amount' => $param['amount'],
						'info' => ""
				];
				$Hf = new hfpay ();
				$pay=$Hf->pay($data);
				if($pay['respCode']=='00000'){
					$model = new CompanyOrder;
					$model->company_id =  $company->id;
					$model->order_id = $param['order_id'];
					$model->amount = $param['amount']/100;
					$model->notify_url = $param['notify_url'];
					$model->channel="HF";
					$model->extra=$param['extra'];
					$model->trade_id=$pay['orderId'];
					$model->save();
					$this->_returnMsg ( "00000", 'success', $pay);
				}else{
					$this->_returnMsg ( "50000", 'error', $pay);
					
				}
				
				
			} catch ( \Exception $e ) {
				return $this->_returnMsg ( 500, $e->getMessage () );
			}
		} else {
			$this->_returnMsg ( "00010", "访问页面方式不正确" );
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