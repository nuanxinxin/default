<?php

namespace app\common\model;

use think\Model;
use EasyWeChat\Foundation\Application;
use org\PrivateImage;
use org\hfpay;
use app\common\model\HfUser;
use app\common\model\User;
use app\common\model\HfBank;

// 认证信息
class AuthInfo extends Model {
	public function User() {
		return $this->belongsTo ( 'User', 'user_id', 'id' );
	}
	
	/**
	 * 事件注册
	 */
	protected static function init() {
		self::event ( 'before_insert', function ($model) {
		} );
		
		self::event ( 'before_update', function ($model) {
		} );
	}
	
	/**
	 * 认证通过
	 *
	 * @return bool|string
	 */
	public function authSuccess($user_id, $auth_status) {
		
		// 开启事务
		$this->startTrans ();
		try {
			// 查询认证信息
			$model = $this->get ( [ 
					'user_id' => $user_id 
			] );
			// 判断认证信息是否存在
			if (! $model)
				abort ( 500, '认证信息不存在' );
			// 通过认证并保存
			if ($auth_status == '已通过') {
				/**
				 * 支付通道注册*
				 */
				// if (!$model->pay_reg_hf) {
				// $this->payRegHf($model);
				// $model->pay_reg_hf = 1;
				// }
				
				$this->templateMessage ( $model->User->wx, '您的认证信息审核已通过' );
			} else {
				$this->templateMessage ( $model->User->wx, '您的认证信息审核不通过，说明：' . input ( 'msg' ) );
			}
			$model->auth_status = $auth_status;
			$model->save ();
			
			$this->commit ();
		} catch ( \Exception $e ) {
			$this->rollback ();
			return $e->getMessage ();
		}
		return true;
	}
	
	/**
	 * 注册SZ商户*
	 */
	public function authSuccessSz($user_id) {
		// 开启事务
		$this->startTrans ();
		try {
			// 查询认证信息
			$model = $this->get ( [ 
					'user_id' => $user_id 
			] );
			// 判断认证信息是否存在
			if (! $model)
				abort ( 500, '认证信息不存在' );
			
			/**
			 * 支付通道注册*
			 */
			if (! $model->pay_reg_sz) {
				// 上传图片
				$pics = array ();
				foreach ( explode ( ',', $model->id_pics ) as $pic ) {
					$pics [] = '/home/wwwroot/default/' . str_replace ( '../', '', PrivateImage::trueImageUrl ( $pic, $model->user_id ) );
				}
				$post = array (
						'image0' => realpath ( $pics [0] ),
						'image1' => realpath ( $pics [1] ) 
				);
				$result_ = postPlus ( 'http://pay.sqstz360.com/api/pay/sz/upload', [ ], $post );
				$result = json_decode ( $result_, true );
				if ($result ['code'] != '000000') {
					abort ( 500, $result_ );
				}
				
				$this->payRegSz ( $model, array (
						'image0' => $result ['images'] ['image1'],
						'image1' => $result ['images'] ['image2'] 
				) );
				$model->pay_reg_sz = 1;
				$model->save ();
			}
			$this->commit ();
		} catch ( \Exception $e ) {
			$this->rollback ();
			return $e->getMessage ();
		}
		return true;
	}
	
	/**
	 * 注册WLB商户*
	 */
	public function authSuccessWlb($user_id) {
		// 开启事务
		$this->startTrans ();
		try {
			// 查询认证信息
			$model = $this->get ( [ 
					'user_id' => $user_id 
			] );
			// 判断认证信息是否存在
			if (! $model)
				abort ( 500, '认证信息不存在' );
			// 通过认证并保存
			/**
			 * 支付通道注册*
			 */
			if (! $model->pay_reg_wlb) {
				$this->payRegWlb ( $model );
				$model->pay_reg_wlb = 1;
			}
			$model->auth_status = '已通过';
			$model->save ();
			
			$this->commit ();
		} catch ( \Exception $e ) {
			$this->rollback ();
			return $e->getMessage ();
		}
		return true;
	}
	
	/**
	 * 注册Hf商户*
	 */
	public function authSuccessHf2($user_id) {
		
		// 开启事务
		$this->startTrans ();
		try {
			// 查询认证信息
			$model = $this->get ( [ 
					'user_id' => $user_id 
			] );
			// 判断认证信息是否存在
			if (! $model)
				abort ( 500, '认证信息不存在' );
			// 通过认证并保存
			/**
			 * 支付通道注册*
			 */
			$result = $this->payRegHfnew ( $model );
			$model->pay_reg_hf = 1;
			$model->auth_status = '已通过';
			$model->save ();
			if($result['respCode']!=='000000'){
				return $result['respInfo'];
			}
			$this->commit ();
		} catch ( \Exception $e ) {
			$this->rollback ();
			return $e->getMessage ();
		}
		return true;
	}
	
	/**
	 * 注册支付通道*
	 */
	private function payRegHfnew($model) {
		// 开启事务
		$this->startTrans ();
		try {
			$hf = new hfpay ();
			$registData = array (
					'account' => $model->User->phone,
					'password' => "123456" 
			);
			$regist = $hf->regist ( $registData );
			$registResult = json_decode ( $regist, true );
			// 注册成功
			if ($registResult ['respCode'] == '000000' || $registResult ['respCode'] == '100005') {
				// 下载密钥
				$HfUser = HfUser::get ( [ 
						'account' => $registData ['account'] 
				] );
				$downloadData = array (
						'account' => $model->User->phone 
				);
				if (! $HfUser) {
					$HfUser = new HfUser ();
					$HfUser->account = $registData ['account'];
					$HfUser->password = $registData ['password'];
					$private_key = $hf->toDownloadKey ( $downloadData );
					$HfUser->private_key = $private_key;
					$HfUser->save ();
				}
				$verifyData = array (
						'mobile' => $model->User->phone,
						'name' => $model->getData ( 'name' ),
						'cert_no' => $model->id_number,
						'card_no' => $model->bank_card_number,
						'city' => $model->User->city,
						'card_type' => 1,
						'address' => $model->bank_sub_name,
						"region_code" => $model->area_code,
						'account' => $model->User->phone,
						'id_pics' => $model->id_pics,
						'bank_card_pic' => $model->bank_card_pic,
						'password' => '123456' ,
						'user_id'=>$model->user_id
				);
				$result = $hf->verifyInfo ( $verifyData );
				foreach ( explode ( ',', $verifyData ['id_pics'] ) as $pic ) {
					$pics [] = PrivateImage::getImageUrl( $pic, User::userIdById ( $model->user_id ) );
				}
				$data->pics = $pics;
				$bank_card_pics = array ();
				foreach ( explode ( ',', $verifyData ['bank_card_pic'] ) as $pic ) {
					$bank_card_pics [] = PrivateImage::getImageUrl( $pic, User::userIdById ( $model->user_id ) );
				}
				$data->bank_card_pics = $bank_card_pics;
				$bank = HfBank::get ( [ 
						'base_id' => $HfUser->id 
				] );
				if (! $bank) {
					// 否则创建
					$bank = new HfBank ();
					$bank->base_id = $HfUser->id;
				}
				$bank->card_type = "1";
				$bank->card_name = $model->getData ( 'name' );
				$bank->card_bank_name = $model->bank_name;
				$bank->card_number = $verifyData ['card_no'];
				$bank->cert_number = $verifyData ['cert_no'];
				$bank->cert_type = "00";
				$bank->card_bank_sub_name = $model->bank_sub_name;
				$bank->cert_correct = $pics [0];
				$bank->cert_opposite = $pics [1];
				$bank->cert_meet = $pics [2];
				$bank->region_code = $verifyData ['region_code'];
				$bank->card_correct = $bank_card_pics [0];
				$bank->card_opposite = $bank_card_pics [1];
				$bank->card_phone = $verifyData ['mobile'];
				$bank->card_city = $verifyData ['city'];
				$bank->card_bank_code = $verifyData ['city'];
				$bank->save ();
			}
			return $result;
		} catch ( \Exception $e ) {
			$this->rollback ();
			return $e->getMessage ();
		}
		return true;
	}
	/**
	 * 注册支付通道*
	 */
	
	/**
	 * 注册支付通道*
	 */
	private function payRegWlb($model) {
		$bank_code = BankCode::get ( [ 
				'bank_code' => $model->bank_code 
		] );
		if (! $bank_code) {
			abort ( 500, 'bank_code查询为空' );
		}
		$data = array (
				'channel' => 'wlb',
				'rest' => 'register',
				'phone' => $model->User->phone,
				'name' => $model->getData ( 'name' ),
				'idNumber' => $model->id_number,
				'cardNumber' => $model->bank_card_number,
				'bankName' => $model->bank_name,
				'bankBranch' => $model->bank_sub_name,
				'bankCode' => $model->bank_code,
				'bankProv' => $bank_code->province,
				'bankCity' => $bank_code->city,
				'bankArea' => $bank_code->city,
				'group_identifier' => $model->User->AdminCompany->group_identifier,
				'account' => $model->User->phone,
				'password' => '123456' 
		);
		$result = curlPost ( 'http://pay.sqstz360.com/api/pay', http_build_query ( $data ) );
		$result = json_decode ( $result, true );
		if ($result ['code'] == '000000') {
			return true;
		} else {
			abort ( 500, 'code:' . $result ['code'] . ',code_msg' . $result ['code_msg'] );
		}
	}
	
	/**
	 * 注册支付通道*
	 */
	private function payRegHf($model) {
		$data = array (
				'channel' => 'hf',
				'rest' => 'register',
				'mobile' => $model->User->phone,
				'name' => $model->getData ( 'name' ),
				'cert_no' => $model->id_number,
				'card_no' => $model->bank_card_number,
				'city' => $model->User->city,
				'group_identifier' => $model->User->AdminCompany->group_identifier,
				'card_type' => 1,
				'account' => $model->User->phone,
				'password' => '123456' 
		);
		$result = curlPost ( 'http://pay.sqstz360.com/api/pay', http_build_query ( $data ) );
		$result = json_decode ( $result, true );
		if ($result ['code'] == '000000') {
			return true;
		} else {
			abort ( 500, 'code:' . $result ['code'] . ',code_msg' . $result ['code_msg'] );
		}
	}
	private function payRegSz($model, $extend = []) {
		$bank_code = BankCode::get ( [ 
				'bank_code' => $model->bank_code 
		] );
		if (! $bank_code) {
			abort ( 500, 'bank_code查询为空' );
		}
		$industry = curlPost ( 'http://pay.sqstz360.com/api/pay/industry/random' );
		$industry = json_decode ( $industry, true );
		if (isset ( $industry ['code'] )) {
			abort ( 500, 'code_msg:' . $industry ['code_msg'] );
		}
		$data = array (
				'channel' => 'sz',
				'rest' => 'register',
				'company_identifier' => 'a16611f93b065454cbb46c9c90971629',
				'card_phone' => $model->User->phone,
				'name' => $model->getData ( 'name' ),
				'cert_no' => $model->id_number,
				'card_no' => $model->bank_card_number,
				'card_bank_name' => $model->bank_name,
				'card_bank_sub_name' => $model->bank_sub_name,
				'card_bank_code' => $model->bank_code,
				'card_province' => $bank_code->province,
				'card_city' => $bank_code->city,
				'group_identifier' => $model->User->AdminCompany->group_identifier,
				'card_type' => 1,
				'account' => $model->User->phone,
				'password' => '123456',
				'cert_img_0' => $extend ['image0'],
				'cert_img_1' => $extend ['image1'],
				'industry' => $industry ['industry'],
				'alipayCategory' => $industry ['alipay'],
				'wechatCategory' => $industry ['wechat'] 
		);
		$result = curlPost ( 'http://pay.sqstz360.com/api/pay', http_build_query ( $data ) );
		$result = json_decode ( $result, true );
		if ($result ['code'] == '000000') {
			return true;
		} else {
			abort ( 500, 'code:' . $result ['code'] . ',code_msg' . $result ['code_msg'] );
		}
	}
	private function templateMessage($openid, $message) {
		try {
			$options = [ 
					'debug' => false,
					'app_id' => 'wxb1993648e68557fd',
					'secret' => 'c2796e3276cfb19f6e4ce0a88c19fcd3',
					'token' => 'easywechat',
					'aes_key' => 'WZm4U6pdTFTy1AGa7rHG03HJLev9BsvMkNXsGZNuAOp' 
			];
			$app = new Application ( $options );
			$notice = $app->notice;
			$templateId = 'GmiU_OsXewUeZsX7if_jO-HXcmNrzNtkxVB0xxoS870';
			$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxb1993648e68557fd&redirect_uri=http://wx.sqstz360.com/&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
			$data = array (
					"first" => '您有认证审核提醒',
					"keyword1" => '认证审核',
					"keyword2" => date ( 'Y-m-d' ),
					"remark" => $message 
			);
			$result = $notice->uses ( $templateId )->withUrl ( $url )->andData ( $data )->andReceiver ( $openid )->send ();
			if ($result->errcode == 0) {
				return $this->_returnMsg ( 200, 'success' );
			} else {
				return $this->_returnMsg ( 500, $result->errmsg );
			}
		} catch ( \Exception $e ) {
		}
	}
	public function paySuccess() {
		// 开启事务
		$this->startTrans ();
		try {
			$this->auth_pay_time = time ();
			$this->save ();
			
			// 认证费提成分配配置值
			$AuthFeeDistribution = Setting::getAuthFeeDistribution ();
			
			// 邀请人关系树
			$inviter = $this->query ( "SELECT dt.ancestor_id,dt.path_length,(SELECT COUNT(*) FROM snake_distribution_tree WHERE ancestor_id=dt.ancestor_id AND path_length>0) descendant_count FROM snake_distribution_tree AS dt WHERE dt.descendant_id={$this->user_id} AND dt.path_length BETWEEN 1 AND 3 ORDER BY dt.path_length ASC" );
			
			// 提成分配保存
			for($i = 0; $i < 3; $i ++) {
				if (isset ( $inviter [$i] )) {
					$data = array (
							'c_id' => $inviter [$i] ['ancestor_id'],
							'c_type' => '个人',
							'money' => $AuthFeeDistribution [$i],
							'add_time' => nowTime (),
							'status' => '未结算' 
					);
					DistributionProfit::create ( $data );
				} else {
					$data = array (
							'c_id' => $this->User->company_id,
							'c_type' => '公司',
							'money' => $AuthFeeDistribution [$i],
							'add_time' => nowTime (),
							'status' => '未结算' 
					);
					DistributionProfit::create ( $data );
					break;
				}
			}
			$this->commit ();
		} catch ( \Exception $e ) {
			$this->rollback ();
			return $e->getMessage ();
		}
		return true;
	}
	public function createOrderId() {
		return 'auth_' . getRandString ( 15 );
	}
}
