<?php

namespace app\api\controller;

use think\Controller;
use app\common\model\AdminCompany;
use app\common\model\LuckyGoodsBuyRecord;
use app\common\model\User;
use app\common\model\LuckyGoods;
use app\common\model\LuckyGoodsSpoilRecord;
use app\common\model\LuckyXydj;
use app\common\model\LuckyGoodsClass;
use EasyWeChat\Foundation\Application;
use app\common\model\Setting;

class Lucky extends Controller {
	private $options = [ 
			'debug' => false,
			'app_id' => 'wxb1993648e68557fd',
			'secret' => 'c2796e3276cfb19f6e4ce0a88c19fcd3',
			'token' => 'easywechat',
			'aes_key' => 'WZm4U6pdTFTy1AGa7rHG03HJLev9BsvMkNXsGZNuAOp' 
	];
	public function _initialize() {
		parent::_initialize ();
		@header ( 'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie' );
		@header ( 'Access-Control-Allow-Origin:*' );
	}
	
	/**
	 * todo 个人中奖记录列表
	 */
	public function mySpoilList() {
		try {
			$this->_initUserId ();
			$model = new LuckyGoodsSpoilRecord ();
			$data = array (
					'spoilList' => $model->mySpoilList ( USER_ID ) 
			);
			return $this->_returnMsg ( 200, 'success', $data );
		} catch ( \Exception $e ) {
			return $this->_returnMsg ( 300, $e->getMessage () );
		}
	}
	
	/**
	 * todo 中奖记录列表
	 */
	public function spoilList() {
		try {
			$this->_initUserId ();
			$model = new LuckyGoodsSpoilRecord ();
			$data = array (
					'spoilList' => $model->spoilList () 
			);
			return $this->_returnMsg ( 200, 'success', $data );
		} catch ( \Exception $e ) {
			return $this->_returnMsg ( 300, $e->getMessage () );
		}
	}
	
	/**
	 * todo 商品开奖信息
	 */
	public function spoilGoodsOpenInfo() {
		try {
			$this->_initUserId ();
			$model = new LuckyGoods ();
			$data = array (
					'openInfo' => $model->spoilGoodsOpenInfo ( $this->request->param ( 'goodsId/d' ) ) 
			);
			return $this->_returnMsg ( 200, 'success', $data );
		} catch ( \Exception $e ) {
			return $this->_returnMsg ( 300, $e->getMessage () );
		}
	}
	
	/**
	 * todo 商品详情
	 */
	public function spoilGoodsDetail() {
		try {
			$this->_initUserId ();
			$model = new LuckyGoods ();
			$data = array (
					'spoilGoods' => $model->spoilGoods ( $this->request->param ( 'goodsId/d' ) ) 
			);
			return $this->_returnMsg ( 200, 'success', $data );
		} catch ( \Exception $e ) {
			return $this->_returnMsg ( 300, $e->getMessage () );
		}
	}
	
	/**
	 * todo 用户下单
	 */
	public function buyLuckyGoods() {
		try {
			$this->_initUserId ();
			$goodsId = $this->request->param ( 'goodsId/d' );
			if (! $goodsId)
				abort ( 300, '未知商品' );
			$payMethod = $this->request->param ( 'payMethod/d' );
			switch ($payMethod) {
				case 1 :
					$model = new LuckyGoodsBuyRecord ();
					// 信用币支付
					$result = $model->creditPay ( USER_ID, $goodsId );
					if ($result === true) {
						$data = [ 
								'result' => [ 
										'code' => '000',
										'codeMsg' => '支付成功' 
								] 
						];
						return $this->_returnMsg ( 200, 'success', $data );
					} else {
						abort ( 300, $result );
					}
					break;
				case 2 :
					$model = new LuckyGoodsBuyRecord ();
					// 微信支付
					$result = $model->creditOrder ( USER_ID, $goodsId );
					// http://admin.sqstz360.com/onlinePayNotify
					if ($result === true) {
						$data = [ 
								'result' => [ 
										'code' => '001',
										'codeMsg' => '订单创建成功' 
								] 
						];
						return $this->_returnMsg ( 200, 'success', $data );
					} else {
						abort ( 300, $result );
					}
					break;
				default :
					abort ( 300, '未知支付方式' );
					break;
			}
		} catch ( \Exception $e ) {
			return $this->_returnMsg ( 300, $e->getMessage () );
		}
	}
	
	/**
	 * 创建在线支付订单*
	 */
	private function createPayOrder() {
		// $key = '&key=5EC798E5429B234ECB9D96B4EA72D0CA';
		// $option['merDate'] = $merDate;
		// $option['merSeqId'] = $merSeqId;
		// ksort($option);
		// $option['sign'] = md5(urldecode(http_build_query($option)) . $key);
		// $option['channel'] = 'yl';
		// $option['rest'] = 'query';
		// $result = curlPost('http://pay.sqstz360.com/api/pay', http_build_query($option));
		// $result = json_decode($result, true);
		// if ($result['code'] == '200' && $result['data']['code'] == '000') {
		// if ($result['data']['stat_info'] == '代付成功') {
		// return true;
		// } else {
		// return $result['data']['stat_info'];
		// }
		// } else {
		// return $result['message'];
		// }
	}
	
	/**
	 * 在线支付通知*
	 */
	public function onlinePayNotify() {
		// $data = $this->request->param();
	}
	public function luckyPrize() {
		try { 
			$this->_initUserId ();
			$ticket_identifier = $this->request->param ( 'ticket_identifier/s' );
			$goods_id=LuckyGoodsSpoilRecord::where ( 'ticket_identifier', $ticket_identifier )->value("goods_id");
			if (! $goods_id) {
				abort ( 300, '奖品不存在' );
			}
			$phone=LuckyGoods::where ( 'goods_id', $goods_id)->value("phone");
			LuckyGoodsSpoilRecord::where ( 'ticket_identifier', $ticket_identifier )->where ( 'ticket_status', 0 )->update ( [ 
					'ticket_status' => 1 
			] );
			if($phone){
				$content=LuckyGoods::where ( 'goods_id', $goods_id)->value("goods_title");
				$flag=sendMsgContent($phone, "客户兑换".$content);	
			}
			
		 } catch ( \Exception $e ) {
			return $this->_returnMsg(500, $e->getMessage());
		}  
		return $this->_returnMsg ( 200, 'success');
	}
	
	/**
	 * todo 公司商品列表
	 */
	public function companyHomeList() {
		try {
			$company_id = AdminCompany::where ( 'identifier', $this->request->param ( 'companyId/s' ) )->value ( 'id' );
			if (! $company_id) {
				abort ( 300, '公司不存在' );
			}
			$this->_initUserId ();
			$model = new LuckyGoods ();
			$page=$this->request->param ( 'page/s' );

			$data = array (
					'goodsList' => $model->goodsList ( $company_id,$page),
					'specialInfo' => $model->specialInfo ( $company_id ) 
			);
			return $this->_returnMsg ( 200, 'success', $data );
		} catch ( \Exception $e ) {
			return $this->_returnMsg ( 300, $e->getMessage () );
		}
	}
	
	/**
	 * todo 用户幸运奖抽奖
	 */
	public function specialDraw() {
		try {
			$company_id = AdminCompany::where ( 'identifier', $this->request->param ( 'companyId/s' ) )->value ( 'id' );
			if (! $company_id) {
				abort ( 300, '公司不存在' );
			}
			$this->_initUserId ();
			$model = new LuckyXydj ();
			$result = $model->specialDraw ( $company_id );
			if (! is_array ( $result )) {
				abort ( 300, $result );
			}
			$data = array (
					'result' => $result 
			);
			return $this->_returnMsg ( 200, 'success', $data );
		} catch ( \Exception $e ) {
			return $this->_returnMsg ( 300, $e->getMessage () );
		}
	}
	
	/**
	 * todo 个人购买记录列表
	 */
	public function myBuyLuckyGoodsList() {
		try {
			$this->_initUserId ();
			$model = new LuckyGoodsBuyRecord ();
			$data = $model->myBuyLuckyGoodsList ();
			return $this->_returnMsg ( 200, 'success', $data );
		} catch ( \Exception $e ) {
			return $this->_returnMsg ( 300, $e->getMessage () );
		}
	}
	
	/**
	 * todo 首页
	 */
	public function spoilHome() {
		try {
			$this->_initUserId ();
			$model = new LuckyGoods ();
			$page=$this->request->param ( 'page/d' );
			$data = array (
					'recommendGoodsList' => $model->recommend (),
					'companyList' => $model->companyList (),
					'goodsList' => $model->goodsList ($company_id = 0,$page),
					'goodTotal'=>$model->goodsTotal(),
					'goodClass'=>LuckyGoodsClass::all(),
					'notify' => Setting::getConfigValue ( 5, 'lucky_notify' ) 
			);
			return $this->_returnMsg ( 200, 'success', $data );
		} catch ( \Exception $e ) {
			return $this->_returnMsg ( 300, $e->getMessage () );
		}
	}
	public function share() {
		try {
			$this->_initUserId ();
			$model = new LuckyGoods ();
			$companyId = AdminCompany::companyIdByIdentifier ( $this->request->param ( 'companyId/s' ) );
			$data = array (
					'spoilGoods' => $model->spoilGoods ( $this->request->param ( 'goodsId/d' ) ) 
			);
			
			$app = new Application ( $this->options );
			$qrcode = $app->qrcode;
			$wx = $qrcode->forever ( 'user:' . USER_ID . ',company:' . $companyId );
			$data ['wxUrl'] = $wx->url;
			return $this->_returnMsg ( 200, 'success', $data );
		} catch ( \Exception $e ) {
			return $this->_returnMsg ( 500, $e->getMessage () );
		}
	}
	
	// ************************************************************************************
	private function _initUserId() {
		$userId = User::userIdByIdentifier ( $this->request->param ( 'userId/s' ) );
		if ($userId) {
			define ( 'USER_ID', $userId);
        } else {
            abort(300, '用户不存在');
        }
    }

    private function _returnMsg($code = 200, $msg = '', $data = array())
    {
        $result = array('code' => $code, 'codeMsg' => $msg, 'data' => $data);
        return json_encode($result);
    }

}