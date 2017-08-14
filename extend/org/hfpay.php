<?php

namespace org;

use think\exception;
use org\PrivateImage;
use app\common\model\User;

class hfpay {
	public function __construct() {
		define ( "RSA_MAX_ORIGINAL", 117 ); // RSA明文分段最大长度
		define ( "RSA_MAX_CIPHER", 256 ); // RSA密文分段最大长度
		define ( "URL", "http://payclient.chinavalleytech.com/ChannelConn/" ); // 项目地址(下发文档中的地址)
		define ( "REGISTER", URL . "rlregister" ); // 注册地址
		define ( "KUBEI", URL . "Kubei" ); // 其他请求地址
		define ( "PRIVATEKEYPATH", __DIR__ . DIRECTORY_SEPARATOR . "Hf/key/" ); // 私钥路径
		define ( "PUBLICKEYPATH", __DIR__ . DIRECTORY_SEPARATOR . "Hf/key/rsa_public_key.pem" ); // 公钥路径
		define ( "CODE", "311429" );
		define ( "CBZID", "820170311134513784461" );
		define ( "KEYSTRING", "BD161A60C8933E7EC1D1B802376D6245" );
	}
	/**
	 * 发送支付请求
	 * 
	 * @param string $url
	 *        	请求地址
	 * @param array $post_data
	 *        	post键值对数据
	 * @return string
	 */
	public static function pay($data) {
		$post_data = [ 
				'account' => "18296111222",
				'orderCode' => 'tb_WeixinPay' 
		];
		$msgDate = [ 
				'channel_code' => $data ['channel_code'],
				'amount' => $data ['amount'],
				'info' => $data ['info'] 
		];
		$msg = base64_encode ( json_encode ( $msgDate, JSON_UNESCAPED_SLASHES ) );
		
		$post_data ['msg'] = $msg;
		$priKeyFilePath = PRIVATEKEYPATH . $post_data ['account'] . "_private_key.pem";
		$sign = self::rsaDataSign ( $msg, $priKeyFilePath );
		$data = base64_encode ( json_encode ( $post_data, JSON_UNESCAPED_SLASHES ) );
		$decrypt = self::rsaPublicEncrypt ( $data, PUBLICKEYPATH ); // RSA公钥加密
		$send_data = [ 
				'data' => $decrypt,
				'signature' => $sign 
		];
		$res = self::send_post1 ( KUBEI, json_encode ( $send_data, JSON_UNESCAPED_SLASHES ) );
		$res = json_decode ( $res, true );
		if (! isset ( $res ['respCode'] )) {
			$res_data = $res ['data'];
			$res_sign = $res ['signature'];
			$original = self::rsaPrivateDecrypt ( base64_decode ( $res_data ), $priKeyFilePath ); // RSA私钥解密
			$original = json_decode ( $original, true );
			$res_msg = json_decode ( $original ['msg'], true );
			// 验证签名
			$valid = self::isValid ( json_encode ( $res_msg, JSON_UNESCAPED_SLASHES ), base64_decode ( $res_sign ), PUBLICKEYPATH );
			if ($valid) {
				return $res_msg;
			}
		} else {
			throw Exception ( $res ['respInfo'] );
		}
	}
	/**
	 * 发送支付请求
	 * 
	 * @param string $url
	 *        	请求地址
	 * @param array $post_data
	 *        	post键值对数据
	 * @return string
	 */
	public static function payTest($data) {
		$post_data = [ 
				'account' => "15270177995",
				'orderCode' => 'tb_WeixinPay' 
		];
		$msgDate = [ 
				'channel_code' => $data ['channel_code'],
				'amount' => $data ['amount'],
				'info' => $data ['info'] 
		];
		$msg = base64_encode ( json_encode ( $msgDate, JSON_UNESCAPED_SLASHES ) );
		
		$post_data ['msg'] = $msg;
		$priKeyFilePath = PRIVATEKEYPATH . $post_data ['account'] . "_private_key.pem";
		$sign = self::rsaDataSign ( $msg, $priKeyFilePath );
		$data = base64_encode ( json_encode ( $post_data, JSON_UNESCAPED_SLASHES ) );
		$decrypt = self::rsaPublicEncrypt ( $data, PUBLICKEYPATH ); // RSA公钥加密
		$send_data = [ 
				'data' => $decrypt,
				'signature' => $sign 
		];
		$res = self::send_post1 ( KUBEI, json_encode ( $send_data, JSON_UNESCAPED_SLASHES ) );
		$res = json_decode ( $res, true );
		if (! isset ( $res ['respCode'] )) {
			$res_data = $res ['data'];
			$res_sign = $res ['signature'];
			$original = self::rsaPrivateDecrypt ( base64_decode ( $res_data ), $priKeyFilePath ); // RSA私钥解密
			$original = json_decode ( $original, true );
			$res_msg = json_decode ( $original ['msg'], true );
			// 验证签名
			$valid = self::isValid ( json_encode ( $res_msg, JSON_UNESCAPED_SLASHES ), base64_decode ( $res_sign ), PUBLICKEYPATH );
			if ($valid) {
				return $res_msg;
			}
		} else {
			throw Exception ( $res ['respInfo'] );
		}
	}
	/**
	 * 注册商户
	 * 
	 * @param array $data        	
	 * @return string
	 */
	public function regist($data) {
		$post_data = array (
				'account' => $data ['account'],
				'pass' => $data ['password'],
				'code' => CODE,
				'cbzid' => CBZID 
		);
		
		$result = self::send_post ( REGISTER, $post_data );
		$res = json_decode ( $result, true );
		return $result;
	}
	public function GetACodePay($msg) {
		$post_data = [ 
				'account' => "18296111222",
				'orderCode' => 'tb_GetACodePay' 
		];
		$post_data ['msg'] = $msg;
		$priKeyFilePath = PRIVATEKEYPATH . $post_data ['account'] . "_private_key.pem";
		$sign = self::rsaDataSign ( $msg, $priKeyFilePath );
		$data = base64_encode ( json_encode ( $post_data, JSON_UNESCAPED_SLASHES ) );
		$decrypt = self::rsaPublicEncrypt ( $data, PUBLICKEYPATH ); // RSA公钥加密
		$send_data = [ 
				'data' => $decrypt,
				'signature' => $sign 
		];
		$res = self::send_post1 ( KUBEI, json_encode ( $send_data, JSON_UNESCAPED_SLASHES ) );
		dump ( $res );
		$res = json_decode ( $res, true );
		
		$res_data = $res ['data'];
		$res_sign = $res ['signature'];
		
		$original = self::rsaPrivateDecrypt ( base64_decode ( $res_data ), $priKeyFilePath ); // RSA私钥解密
		$original = json_decode ( $original, true );
		$res_msg = json_decode ( $original ['msg'], true );
		
		// 验证签名
		$valid = self::isValid ( json_encode ( $res_msg, JSON_UNESCAPED_SLASHES ), base64_decode ( $res_sign ), PUBLICKEYPATH );
		if ($valid) {
			return $res_msg;
		}
	}
	
	/**
	 * 下载密钥
	 *
	 * @param array $data        	
	 * @return string
	 */
	public function toDownloadKey($data) {
		$post_data = [ 
				'orderCode' => 'tb_DownLoadKey',
				'account' => $data ['account'],
				'password' => "123456",
				'language' => 'PHP'  // 非必填项,不填默认为Java
		];
		$datas = base64_encode ( json_encode ( $post_data ) );
		$encrypted = $this->rsaPublicEncrypt ( $datas, PUBLICKEYPATH );
		$params = [  // 5.4以上版本
				'data' => $encrypted 
		];
		$res = $this->send_post1 ( KUBEI, json_encode ( $params, JSON_UNESCAPED_SLASHES ) );
		$res = json_decode ( $res, true );
		$resData = $res ['data'];
		$count = $res ['count'];
		$plain_text = mcrypt_decrypt ( MCRYPT_3DES, $this->hexStrToBytes ( KEYSTRING, 24 ), $this->hexStrToBytes ( $resData ), MCRYPT_MODE_ECB );
		$resjson = substr ( $plain_text, 0, $count );
		$resArr = json_decode ( $resjson, true );
		$respCode = $resArr ['respCode'];
		if ("000000" != $respCode) {
			// print("下载密钥失败,信息:" . $resArr['respInfo']);
			return false;
		}
		$priKey = $resArr ['privatekey'];
		if ($priKey == null || $priKey == '') {
			return false;
		}
		$priKeyFilePath = PRIVATEKEYPATH . $post_data ['account'] . "_private_key.pem";
		if (file_exists ( $priKeyFilePath )) {
			unlink ( $priKeyFilePath );
		}
		if (! file_put_contents ( $priKeyFilePath, $resArr ['privatekey'] )) {
			return false;
		} else {
			return $priKeyFilePath;
		}
	}
	public function verifyInfo($data) {
		try {
			$post_data = [ 
					'account' => $data ['mobile'],
					'orderCode' => 'tb_verifyInfo' 
			];
			$msgDate = [ 
					'real_name' => base64_encode ( $data ['name'] ), // 真实姓名
					'cmer' => base64_encode ( "纯樱电子用品超市" ), // 商户全称
					'cmer_short' => base64_encode ( "纯樱电子用品超市" ), // 商户简称
					'channel_code' => 'WXPAY', // 通道标识
					'region_code' => $data ['region_code'], // 地区编码 参照:http://www.stats.gov.cn/tjsj/tjbz/xzqhdm/201703/t20170310_1471429.html?spm=a219a.7629140.0.0.7aZWPD
					'address' => $data ['address'], // 详细地址
					'business_id' => 203, // 经营类目(传对应的微信MCC)
					'phone' => $data ['mobile'], // 商户联系电话
					'card_type' => '1', // 结算卡类型(默认值1,借记卡)
					'card_no' => $data ['card_no'], // 结算卡号
					'cert_type' => '00', // 身份证件号类型(默认值00,身份证号)
					'cert_no' => $data ['cert_no'], // 身份证件号码
					'mobile' => $data ['mobile'], // 结算卡开户手机号
					'location' => base64_encode ( $data ['city'] )  // 结算卡开户城市
			];
			$pics = array ();
			foreach ( explode ( ',', $data ['id_pics'] ) as $pic ) {
				$pics [] = PrivateImage::trueImageUrl ( $pic, $data ['user_id'] );
			}
			
			$data->pics = $pics;
			$bank_card_pics = array ();
			foreach ( explode ( ',', $data ['bank_card_pic'] ) as $pic ) {
				$bank_card_pics [] = PrivateImage::trueImageUrl ( $pic, $data ['user_id'] );
			}
			$data->bank_card_pics = $bank_card_pics;
			$picJson = [ 
					'cert_correct' => $this->base64EncodeImage ( $pics [0] ),
					'cert_opposite' => $this->base64EncodeImage ( $pics [1] ),
					'cert_meet' => $this->base64EncodeImage ( $pics [2] ),
					'card_correct' => $this->base64EncodeImage ( $bank_card_pics [0] ),
					'card_opposite' => $this->base64EncodeImage ( $bank_card_pics [1] ) 
			];
			$priKeyFilePath = PRIVATEKEYPATH . $post_data ['account'] . "_private_key.pem";
			$sign = $this->rsaDataSign ( json_encode ( $msgDate, JSON_UNESCAPED_SLASHES ), $priKeyFilePath ); // RSA签名
			$post_data ['msg'] = json_encode ( $msgDate, JSON_UNESCAPED_SLASHES );
			$data = base64_encode ( json_encode ( $post_data, JSON_UNESCAPED_SLASHES ) );
			$decrypt = $this->rsaPublicEncrypt ( $data, PUBLICKEYPATH ); // RSA公钥加密
			$send_data = [ 
					'data' => $decrypt,
					'signature' => $sign,
					'pic' => json_encode ( $picJson, JSON_UNESCAPED_SLASHES ) 
			];
			
			$res = $this->send_post1 ( KUBEI, json_encode ( $send_data, JSON_UNESCAPED_SLASHES ) );
			$res = json_decode ( $res, true );
			$res_data = $res ['data'];
			$res_sign = $res ['signature'];
			
			$original = $this->rsaPrivateDecrypt ( base64_decode ( $res_data ), $priKeyFilePath ); // RSA私钥解密
			$original = json_decode ( $original, true );
			$res_msg = json_decode ( $original ['msg'], true );
			return $res_msg;
		}catch (\Exception $e) {
			return array('respCode'=>"00001","respInfo"=>$e->getMessage());
		}
	}
	public static function send_post($url, $post_data) {
		$postdata = http_build_query ( $post_data );
		$options = array (
				'http' => array (
						'method' => 'POST',
						'header' => 'Content-type:application/x-www-form-urlencoded',
						'content' => $postdata,
						'timeout' => 30 * 60  // 超时时间（单位:s）
				) 
		);
		$context = stream_context_create ( $options );
		$result = file_get_contents ( $url, false, $context );
		
		return $result;
	}
	public static function send_post1($url = '', $post_data = '') {
		if (empty ( $url ) || empty ( $post_data )) {
			return false;
		}
		$postUrl = $url;
		$curlPost = $post_data;
		$ch = curl_init (); // 初始化curl
		curl_setopt ( $ch, CURLOPT_URL, $postUrl ); // 抓取指定网页
		curl_setopt ( $ch, CURLOPT_HEADER, 0 ); // 设置header
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 ); // 要求结果为字符串且输出到屏幕上
		curl_setopt ( $ch, CURLOPT_POST, 1 ); // post提交方式
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $curlPost );
		$data = curl_exec ( $ch ); // 运行curl
		curl_close ( $ch );
		return $data;
	}
	
	/**
	 * RSA公钥加密(分段加密)
	 * 
	 * @param type $data        	
	 * @param type $keyPath        	
	 * @return type
	 */
	public static function rsaPublicEncrypt($data, $keyPath) {
		$key = openssl_pkey_get_public ( file_get_contents ( $keyPath ) );
		$ciphertext = null;
		$cipher_len = strlen ( $data );
		if ($cipher_len - RSA_MAX_ORIGINAL > 0) {
			$flag = 0;
			for($i = ceil ( $cipher_len / RSA_MAX_ORIGINAL ); $i > 0; $i --) {
				$temp = substr ( $data, $flag, RSA_MAX_ORIGINAL );
				$r = openssl_public_encrypt ( $temp, $encryptData, $key );
				$ciphertext .= $encryptData;
				if ($r) {
					$flag += RSA_MAX_ORIGINAL;
				} else {
					throw Exception ( "PublicRSA分段加密失败." );
				}
			}
		} else {
			$r = openssl_public_encrypt ( $data, $encryptData, $key );
			if ($r) {
				$ciphertext = $encryptData;
			}
		}
		return base64_encode ( $ciphertext );
	}
	
	/**
	 * RSA私钥解密(分段解密)
	 * 
	 * @param type $data        	
	 * @param type $keyPath        	
	 */
	public static function rsaPrivateDecrypt($data, $keyPath) {
		$key = openssl_pkey_get_private ( file_get_contents ( $keyPath ) );
		
		$originalText = null;
		$original_len = strlen ( $data );
		
		if ($original_len - RSA_MAX_CIPHER > 0) {
			$flag = 0;
			for($i = ceil ( $original_len / RSA_MAX_CIPHER ); $i > 0; $i --) {
				$temp = substr ( $data, $flag, RSA_MAX_CIPHER );
				$r = openssl_private_decrypt ( $temp, $decrypted, $key );
				
				$originalText .= $decrypted;
				if ($r) {
					$flag += RSA_MAX_CIPHER;
				} else {
					throw Exception ( "RSA分段加密失败." );
				}
			}
		} else {
			$r = openssl_private_decrypt ( $data, $decrypted, $key );
			if ($r) {
				$originalText = $decrypted;
			} else {
				throw Exception ( "RSA加密失败" );
			}
		}
		return base64_decode ( $originalText );
	}
	
	/**
	 * 数据签名
	 * 
	 * @param type $data        	
	 * @param type $keyPath        	
	 * @return boolean
	 */
	public static function rsaDataSign($data, $keyPath) {
		if (empty ( $data )) {
			throw Exception ( "data为空" );
		}
		
		$private_key = file_get_contents ( $keyPath );
		if (empty ( $private_key )) {
			throw Exception ( "Private Key error!" );
		}
		
		$pkeyid = openssl_get_privatekey ( $private_key );
		if (empty ( $pkeyid )) {
			throw Exception ( "private key resource identifier False!" );
		}
		
		$verify = openssl_sign ( $data, $signature, $pkeyid, OPENSSL_ALGO_MD5 );
		openssl_free_key ( $pkeyid );
		return base64_encode ( $signature );
	}
	
	/**
	 * 数据验签
	 * 
	 * @param type $data        	
	 * @param type $signature        	
	 * @param type $keyPath        	
	 * @return boolean
	 */
	public static function isValid($data = '', $signature = '', $keyPath) {
		if (empty ( $data ) || empty ( $signature )) {
			return False;
		}
		$public_key = file_get_contents ( $keyPath );
		if (empty ( $public_key )) {
			throw Exception ( "Public Key error!" );
		}
		$pkeyid = openssl_get_publickey ( $public_key );
		if (empty ( $pkeyid )) {
			throw Exception ( "public key resource identifier False!" );
		}
		$ret = openssl_verify ( $data, $signature, $pkeyid, OPENSSL_ALGO_MD5 );
		return $ret;
	}
	public static function hexStrToBytes($str, $length = null) {
		$ret = [ 
				'c*' 
		];
		for($i = 0, $l = strlen ( $str ) / 2; $i < $l; ++ $i) {
			$x = intval ( substr ( $str, 2 * $i, 2 ), 16 );
			if ($x > 128)
				$x -= 256;
			$ret [] = $x;
		}
		// 补全24位
		if (isset ( $length )) {
			for($i = count ( $ret ), $j = 1; $i <= $length; ++ $i, ++ $j)
				$ret [] = $ret [$j];
		}
		return call_user_func_array ( 'pack', $ret );
	}
	
	/**
	 * 图片Base64编码
	 * 
	 * @param type $image_file        	
	 * @return string
	 */
	function base64EncodeImage($image_file) {
		$image_info = getimagesize ( $image_file );
		$image_data = fread ( fopen ( $image_file, 'r' ), filesize ( $image_file ) );
		$base64_image = [ 
				'suffix' => pathinfo ( $image_file, PATHINFO_EXTENSION ),
				'content' => chunk_split ( base64_encode ( $image_data ) ) 
		];
		return json_encode ( $base64_image, JSON_UNESCAPED_SLASHES );
	}
}
