<?php

namespace app\api\controller;

use app\common\model\Question;
use think\Controller;
use app\common\model\AdminCompany;//公司
use app\common\model\User;//用户
use app\common\model\AuthInfo;//用户认证
use app\common\model\CreditRecord;//信用币
use app\common\model\LoanInfo;//贷款信息
use org\PrivateImage;//私密图片
use think\Image;
use org\StringCrypt;//字符串加密解密
use app\common\model\Setting;//设置项
use org\Pay;//支付
use app\common\model\SettlementNotify;
use app\common\model\ToolRecord;
use app\common\model\PawnInfo;
use app\common\model\CreditCard;
use app\common\model\DistributionProfit;
use think\Db;
use app\common\model\Frozen;
use EasyWeChat\Foundation\Application;


class Wx extends Controller
{
    private $options = [
        'debug' => false,
        'app_id' => 'wxb1993648e68557fd',
        'secret' => 'c2796e3276cfb19f6e4ce0a88c19fcd3',
        'token' => 'easywechat',
        'aes_key' => 'WZm4U6pdTFTy1AGa7rHG03HJLev9BsvMkNXsGZNuAOp'
    ];

    public function _initialize()
    {
        parent::_initialize();
        @header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie');
//        @header('Access-Control-Allow-Origin:http://wx.sqstz360.com');
        @header('Access-Control-Allow-Origin:*');
    }
    /**
     * 新用户注册
     * @return array
     */
    public function register()
    {
//        return $this->_returnMsg(500, '注册通道已关闭！');
        $phone = $this->request->param('phone/s');
        $openid = $this->request->param('openid/s');

        $model = User::get(['wx' => $openid,'source'=>'wx']);
        if ($model->id) {
            $model->phone = $phone;
            $result = $model->save();
        } else {
            return $this->_returnMsg(500, '公众号升级，请重新关注！');
        }
        if ($result !== false) {
            $returnData = array(
                'identifier' => $model->identifier,
                'companyName' => $model->AdminCompany->company_name,
                'companyId' => $model->AdminCompany->id,
                'authId' => '',
                'authStatus' => '',
                'authPayTime' => ''
            );
            return $this->_returnMsg(200, 'success', $returnData);
        } else {
            return $this->_returnMsg(500, 'error');
        }
    }
    /**
     * 用户登录
     * @return array
     */
	public function appLogin(){
		$phone=$this->request->param('phone/s');
		$password=$this->request->param('password/s');
		$model=User::get(['phone'=>$phone,'source'=>'app']);
		if($model->id){
			$md5password=md5($password);
			if($model->password==$md5password){
				$data = array(
						'identifier' => $model->identifier,
						'companyName' => $model->AdminCompany->company_name,
						'companyId' => $model->AdminCompany->identifier,
						'authId' => (string)$model->AuthInfo->id,
						'authStatus' => (string)$model->AuthInfo->auth_status,
						'authPayTime' => $model->AuthInfo->auth_pay_time
				);
				return $this->_returnMsg(200, 'success', $data);
			}else{
				return $this->_returnMsg(400,'密码错误');
				
			}
		}else{
			return $this->_returnMsg(500,'没有该手机号,赶紧去注册吧');		
		}
	}
    /**
     * 用户注册
     */
    public function appRegist(){
    	$phone=$this->request->param('phone/s');
    	$password=$this->request->param('password/s');
    	$model=User::get(['phone'=>$phone,'source'=>'app']);
    	if(!isset($model->id)){
    		$user= new User();
    		$parent_id = intval(User::where('identifier', $this->request->param('ui/s'))->value('id'));
    		$company_id = AdminCompany::where('identifier', $this->request->param('ci/s'))->value('id');
    		$company_id = intval($company_id) ? intval($company_id) : 1;
    		if(strlen($password)<6){
    			return $this->_returnMsg(400,'没有该手机号,密码小于6位数');
    		}
    		$user->nickname = "手牵手用户";
    		$user->thumb=$this->request->domain()."/static/layui/images/gray.png";
    		$user->phone=$phone;
    		$user->company_id = $company_id;
    		$user->password=md5($password);
    		$user->city="南昌";
    		$user->parent_id = $parent_id;
    		$identifier=md5(nowTime());
    		$user->identifier = $identifier;
    		$user->source='app';
			$user->save();
			$data = array(
					'identifier' => $identifier,
					'companyName' => "手牵手",
					'companyId' => 1,
					'authId' => "",
					'authStatus' =>"",
					'authPayTime' => ""
			);
			return $this->_returnMsg(200,'注册成功',$data);
    	}else{
    		return $this->_returnMsg(500,'你已经注册过了');
    	}
    }
    /**
     * 重置密码
     */
    public function changePassword(){
    	$phone=$this->request->param('phone/s');
    	$password=$this->request->param('password/s');
    	$model=User::get(['phone'=>$phone,'source'=>'app']);
    	if($model->id){
    		if(strlen($password)<6){
    			return $this->_returnMsg(400,'没有该手机号,密码小于6位数');
    		}
    		$model->password=md5($password);
    		$model->save();
    		return $this->_returnMsg(200,'密码修改成功');
    	}else{
    		return $this->_returnMsg(500,'没有该手机号,赶紧去注册吧');
    	}
    }
    
    
    
    /**
     * 提交认证信息
     * @return array
     */
    public function commitAuth()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $data = array(
                'user_id' => $userId,
                'name' => $this->request->param('name/s'),
                'id_number' => $this->request->param('IDNumber/s'),
                'id_pics' => $this->request->param('IDPics/s'),
                'user_type' => $this->request->param('userType/s'),
                'company_name' => $this->request->param('companyName/s'),
                'company_addr' => $this->request->param('companyAddr/s'),
                'bank_card_number' => $this->request->param('bankCardNumber/s'),
                'bank_card_pic' => $this->request->param('bankCardPic/s'),
                'bank_name' => $this->request->param('bankName/s'),
                'bank_sub_name' => $this->request->param('bankSubName'),
                ''
            );
            $result = $this->validate($data, 'User.auth');
            if ($result !== true) {
                return $this->_returnMsg(500, $result);
            }
            //如果存在则修改
            $model = AuthInfo::get(array('user_id' => $data['user_id']));
            if (!$model) {
                //否则创建
                $model = new AuthInfo;
            }
            $model->user_id = $data['user_id'];
            $model->name = $data['name'];
            $model->id_number = $data['id_number'];
            $model->id_pics = $data['id_pics'];
            $model->user_type = $data['user_type'];
            $model->company_name = $data['company_name'];
            $model->company_addr = $data['company_addr'];
            $model->bank_card_number = $data['bank_card_number'];
            $model->bank_card_pic = $data['bank_card_pic'];
            $model->bank_name = $data['bank_name'];
            $model->bank_sub_name = $data['bank_sub_name'];
            $model->auth_time = nowTime();
            $model->auth_status = '审核中';
            $model->save();
            $returnData = array(
                'authId' => $model->id
            );
            return $this->_returnMsg(200, 'success', $returnData);
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 支付认证费用
     * @return array
     */
    public function payAuth()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $authId = $this->request->param('authId/s') OR abort(500, '参数错误');
            $authInfo = AuthInfo::get(array('id' => $authId, 'user_id' => $userId));
            if (!$authInfo) {
                abort(500, '没有认证信息');
            } else {
                $authInfo->order_id = $authInfo->createOrderId();
                if ($authInfo->save()) {
                    /**发起扫码支付**/
                    $Pay = new Pay;
                    $payCode = $Pay->QrCodePay(array('order_id' => $authInfo->order_id, 'amount' => Setting::getConfigValue(2, 'auth_fee'), 'type' => 'auth'));
                    $data = array(
                        'authId' => $authId,
                        'payCode' => $payCode
                    );
                    return $this->_returnMsg(200, 'success', $data);
                } else {
                    abort(500, '保存订单号失败');
                }
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 登录
     * @return array
     */
    public function login()
    {
        $openid = $this->request->param('openid/s');
        $city = $this->request->param('city/s');
        $result = $this->validate(array('wx' => $openid), 'User.login');
        if ($result !== true) {
            return $this->_returnMsg(500, $result);
        }
        $user = User::loginByWx(array('wx' => $openid, 'city' => $city));
        if ($user === false || !$user->phone) {
            return $this->_returnMsg(300, '该用户尚未注册');
        } else {
            $data = array(
                'identifier' => $user->identifier,
                'companyName' => $user->AdminCompany->company_name,
                'companyId' => $user->AdminCompany->identifier,
                'authId' => (string)$user->AuthInfo->id,
                'authStatus' => (string)$user->AuthInfo->auth_status,
                'authPayTime' => $user->AuthInfo->auth_pay_time
            );
            return $this->_returnMsg(200, 'success', $data);
        }
    }

    /**
     * 获取用户信息
     * @return array
     */
    public function profileInfo()
    {
        try {
            $userId = $this->request->param('userId/s') OR abort(500, '参数错误');
            $user = User::get(array('identifier' => $userId));
            if (!$user) {
                abort(500, '用户不存在');
            } else {
                $data = array(
                    'phone' => $user->phone,
                    'companyId' => $user->AdminCompany->identifier,
                    'name' => $user->AuthInfo->name ? (string)$user->AuthInfo->name : $user->nickname,
                    'authId' => (string)$user->AuthInfo->id,
                    'authStatus' => (string)$user->AuthInfo->auth_status,
                    'authPayTime' => $user->AuthInfo->auth_pay_time,
                    'thumb' => $user->thumb,
                    'creditMoney' => $user->credit_money
                );
                return $this->_returnMsg(200, 'success', $data);
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 获取用户认证信息
     * @return array
     */
    public function authInfo()
    {
        try {
            $userId = $this->request->param('userId/s') OR abort(500, '参数错误');
            $authId = $this->request->param('authId/s') OR abort(500, '参数错误');
            $authInfo = AuthInfo::get(array('id' => $authId, 'user_id' => User::userIdByIdentifier($userId)));
            if (!$authInfo) {
                abort(500, '没有认证信息');
            } else {
                $pics = explode(',', $authInfo->id_pics);
                $id_pics = array();
                foreach ($pics as $pic) {
                    $id_pics[] = PrivateImage::getImageUrl($pic, $userId);
                }
                $bankCardPics_ = explode(',', $authInfo->bank_card_pic);
                $bankCardPics = array();
                foreach ($bankCardPics_ as $pic) {
                    $bankCardPics[] = PrivateImage::getImageUrl($pic, $userId);
                }
                $data = array(
                    'name' => $authInfo->name,
                    'IDNumber' => $authInfo->id_number,
                    'IDPics' => $authInfo->id_pics,
                    'IDPicsArray' => $id_pics,
                    'userType' => $authInfo->user_type,
                    'companyName' => $authInfo->company_name,
                    'companyAddr' => $authInfo->company_addr,
                    'bankCardNumber' => $authInfo->bank_card_number,
                    'bankName' => $authInfo->bank_name,
                    'bankSubName' => $authInfo->bank_sub_name,
                    'bankCode' => $authInfo->bank_code,
                    'bankCardPic' => $authInfo->bank_card_pic,
                    'bankCardPicArray' => $bankCardPics
                );
                return $this->_returnMsg(200, 'success', $data);
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 信用币充值
     * @return array
     */
    public function rechargeCreditMoney()
    {
        try {
            $userId = $this->request->param('userId/s') OR abort(500, '参数错误');
            $money = $this->request->param('money/d') OR abort(500, '参数错误');
            $payType = $this->request->param('payType/s') OR abort(500, '参数错误');
            $payment = '未知';
            if ($payType == '120') {
                $payment = '微信';
            }
            if ($payType == '121') {
                $payment = '支付宝';
            }
            if ($payType == '122') {
                $payment = '京东';
            }

            $model = new CreditRecord;
            $model->user_id = User::userIdByIdentifier($userId) OR abort(500, '用户不存在');
            $model->money = $money;
            $model->title = '充值';
            $model->order_id = $model->createOrderId();
            $model->pay_type = $payment;
            $model->add(1);

            /**发起扫码支付**/
            $Pay = new Pay(array('pay_type' => $payType));
            $payCode = $Pay->QrCodePay(array('order_id' => $model->order_id, 'amount' => $model->money, 'type' => 'recharge'));
            return $this->_returnMsg(200, 'success', array('payCode' => $payCode));
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 信用币提现
     * @return array
     */
    public function creditMoneyCollection()
    {
        try {
            $userId = $this->request->param('userId/s') OR abort(500, '参数错误');
            $price = $this->request->param('price/s') OR abort(500, '参数错误');
            $user = User::get(['identifier' => $userId]) OR abort(500, '用户不存在');
            if ($user->credit_money < $price) {
            	abort(500, '信用币余额不足' . $price. '元');
            }
            if ($price< Setting::getConfigValue(4, 'withdrawal_condition')) {
                abort(500, '提现金额不足' . Setting::getConfigValue(4, 'withdrawal_condition') . '元,不能提现');
            }
            
            $model = new CreditRecord;
            $model->user_id = $user->id;
            /* $model->money = -$user->credit_money; */
            $model->money = -$price;
            $model->title = '提现';
            $result = $model->add(2,$price);
            if ($result !== true) {
                abort(500, $result);
            }
            return $this->_returnMsg(200, 'success');
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 分销分红信息
     * @return array
     */
    public function distributionInfo()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $result = DistributionProfit::where('c_id', $userId)->where('c_type', '个人')->field('count(*) peopleCount,sum(money) moneyCount')->find();
            $mondyWaitCount = DistributionProfit::where('c_id', $userId)->where('c_type', '个人')->where('status', '未结算')->sum('money');
            $data = array(
                'peopleCount' => intval($result->peopleCount),
                'moneyCount' => floatval($result->moneyCount),
                'moneyWaitCount' => floatval($mondyWaitCount)
            );
            return $this->_returnMsg(200, 'success', $data);
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 收付款工具历史交易列表
     * @return array
     */
    public function toolsBillRecord()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $data = array('list' => array());
            $result = ToolRecord::where('user_id', $userId)->where('user_in_come_status', '已到账')->order('month asc')->select();
            $months = array();
            foreach ($result as $item) {
                if (!in_array($item->month, $months)) {
                    $months[] = $item->month;
                    $data['list'][] = array(
                        'month' => $item->month . '月'
                    );
                }
                foreach ($data['list'] as $index => $value) {
                    if ($item->month . '月' == $value['month']) {
                        $data['list'][$index]['bills'][] = array(
                            'id' => $item->id,
                            'type' => $item->type,
                            'time' => date('Y-m-d H:i', $item->create_time),
                            'money' => $item->money
                        );
                    }
                }
            }
            return $this->_returnMsg(200, 'success', $data);
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 推广列表
     * @return array
     */
    public function myPartners()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $model = User::get($userId);
            $result = $model->myPartners();
            return $this->_returnMsg(200, 'success', $result);
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 信用币账单列表
     * @return array
     */
    public function creditMoneyList()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $page = $this->request->param('page/d', 1);
            $pageSize = $this->request->param('pageSize/d', 10);
            $data = array('listSize' => 0, 'list' => array());
            $model = new CreditRecord;
            $list = $model->where('user_id', $userId)->page($page, $pageSize)->order("create_time desc")->select();
            if ($list) {
                $data['listSize'] = $model->where('user_id', $userId)->count();
                foreach ($list as $index => $item) {
                    $data['list'][] = array(
                        'id' => $item->id,
                        'type' => $item->type,
                        'title' => $item->title,
                        'time' => date('Y-m-d H:i', $item->create_time),
                        'money' => $item->money,
                        'status' => $item->status
                    );
                }
            }
            return $this->_returnMsg(200, 'success', $data);
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 提交的贷款信息列表
     * @return array
     */
    public function myLoanList()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $data = array('list' => array());
            $list = LoanInfo::all(array('user_id' => $userId));
            if ($list) {
                foreach ($list as $index => $item) {
                    $data['list'][] = array(
                        'id' => $item->id,
                        'loanType' => $item->type,
                        'name' => $item->name,
                        'time' => date('Y-m-d H:i:s', $item->add_time),
                        'status' => $item->status,
                        'info' => array(
                            'marryStatus' => $item->marry_status,
                            'loanMoney' => $item->loan_money,
                            'interest' => $item->interest,
                            'taobaoCredit' => $item->taobao_credit
                        ),
                    );
                }
            }
            return $this->_returnMsg(200, 'success', $data);
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 贷款信息浏览(交保)记录列表
     * @return array
     */
    public function viewedLoanList()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $data = array('list' => array());
            $list = LoanInfo::where("FIND_IN_SET('" . $userId . "',pay_view_user_ids) or pay_margin_user_id=" . $userId)->select();
            if ($list) {
                foreach ($list as $index => $item) {
                    if ($item->pay_margin_user_id == $userId) {
                        $type = '交保';
                        $unfrozen_time = Frozen::where('finish_status', 0)->where('o_id', $item->id)->where('o_type', 'loan')->where('user_id', $userId)->value('unfrozen_time');
                        $backEnable = ($unfrozen_time > nowTime() && $item->apply_refund == 0) ? true : false;
                    } else {
                        $backEnable = false;
                        $type = '浏览';
                    }
                    $data['list'][] = array(
                        'id' => $item->id,
                        'loanType' => $item->type,
                        'name' => $item->name,
                        'time' => date('Y-m-d H:i:s', $item->add_time),
                        'type' => $type,
                        'backEnable' => $backEnable,
                        'info' => array(
                            'marryStatus' => $item->marry_status,
                            'loanMoney' => $item->loan_money,
                            'interest' => $item->interest,
                            'taobaoCredit' => $item->taobao_credit
                        ),
                    );
                }
            }
            return $this->_returnMsg(200, 'success', $data);
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 提交贷款信息-信贷
     * @return array
     */
    public function commitCreditLoan()
    {
        try {
            $loanId = $this->request->param('loanId/d');
            $common = $this->_getLoanCommonData();
            $data = array(
                'job_company_type' => $this->request->param('jobCompanyType/s'),
                'monthly' => $this->request->param('monthly/s'),
                'social_security' => $this->request->param('socialSecurity/s'),
                'provident_fund' => $this->request->param('providentFund/s'),
                'in_come_certificate_pic' => $this->request->param('inComeCertificatePic/s'),
                'had_house' => $this->request->param('hadHouse/s'),
                'had_car' => $this->request->param('hadCar/s'),
                'had_loan' => $this->request->param('hadLoan/s'),
                'type' => '信贷'
            );
            $data = array_merge($common, $data);

            $result = $this->validate($data, 'LoanInfo.commit_credit_loan');
            if ($result !== true) abort(500, $result);

            if ($loanId) {
                $model = LoanInfo::get($loanId);
                if ($model->pay_margin_user_id > 0) {
                    abort(500, '已交保证金无法修改');
                }
            } else {
                $model = new LoanInfo;
            }

            $result = $model->commitCreditLoan($data);
            if ($result !== true) {
                abort(500, $result);
            }
            return $this->_returnMsg(200, 'success', array('loanId' => $model->id));
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 提交贷款信息-房贷
     * @return array
     */
    public function commitHouseLoan()
    {
        try {
            $loanId = $this->request->param('loanId/d');
            $common = $this->_getLoanCommonData();
            $data = array(
                'loan_buy_house' => $this->request->param('loanBuyHouse/s'),
                'house_both_have' => $this->request->param('houseBothHave/s'),
                'house_may_price' => $this->request->param('houseMayPrice/s'),
                'type' => '房贷'
            );
            $data = array_merge($common, $data);

            $result = $this->validate($data, 'LoanInfo.commit_house_loan');
            if ($result !== true) abort(500, $result);

            if ($loanId) {
                $model = LoanInfo::get($loanId);
            } else {
                $model = new LoanInfo;
            }

            $result = $model->commitHouseLoan($data);
            if ($result !== true) {
                abort(500, $result);
            }
            return $this->_returnMsg(200, 'success', array('loanId' => $model->id));
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 提交贷款信息-车贷
     * @return array
     */
    public function commitCarLoan()
    {
        try {
            $loanId = $this->request->param('loanId/d');
            $common = $this->_getLoanCommonData();
            $data = array(
                'car_brand' => $this->request->param('carBrand/s'),
                'car_mill' => $this->request->param('carMill/s'),
                'loan_buy_car' => $this->request->param('loanBuyCar/s'),
                'car_full_pic' => $this->request->param('carFullPic/s'),
                'car_reg_pic' => $this->request->param('carRegPic/s'),
                'driving_licence_pics' => $this->request->param('drivingLicencePics/s'),
                'vehicle_licence_pics' => $this->request->param('vehicleLicencePics/s'),
                'car_invoice_pic' => $this->request->param('carInvoicePic/s'),
                'policy_pic' => $this->request->param('policyPic/s'),
                'type' => '车贷'
            );
            $data = array_merge($common, $data);

            $result = $this->validate($data, 'LoanInfo.commit_car_loan');
            if ($result !== true) abort(500, $result);

            if ($loanId) {
                $model = LoanInfo::get($loanId);
            } else {
                $model = new LoanInfo;
            }

            $result = $model->commitCarLoan($data);
            if ($result !== true) {
                abort(500, $result);
            }
            return $this->_returnMsg(200, 'success', array('loanId' => $model->id));
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 提交贷款信息-美丽贷
     * @return array
     */
    public function commitBeautyLoan()
    {
        try {
            $loanId = $this->request->param('loanId/d');
            $common = $this->_getLoanCommonData();
            $data = array(
                'had_house' => $this->request->param('hadHouse/s'),
                'had_car' => $this->request->param('hadCar/s'),
                'had_loan' => $this->request->param('hadLoan/s'),
                'take_bank_card_pic' => $this->request->param('takeBankCardPic/s'),
                'father_name' => $this->request->param('fatherName/s'),
                'father_phone' => $this->request->param('fatherPhone/s'),
                'mother_name' => $this->request->param('motherName/s'),
                'mother_phone' => $this->request->param('motherPhone/s'),
                'friend1_name' => $this->request->param('friend1Name/s'),
                'friend1_phone' => $this->request->param('friend1Phone/s'),
                'friend2_name' => $this->request->param('friend2Name/s'),
                'friend2_phone' => $this->request->param('friend2Phone/s'),
                'friend3_name' => $this->request->param('friend3Name/s'),
                'friend3_phone' => $this->request->param('friend3Phone/s'),
                'type' => '美丽贷'
            );
            $data = array_merge($common, $data);

            $result = $this->validate($data, 'LoanInfo.commit_beauty_loan');
            if ($result !== true) abort(500, $result);

            if ($loanId) {
                $model = LoanInfo::get($loanId);
            } else {
                $model = new LoanInfo;
            }

            $result = $model->commitBeautyLoan($data);
            if ($result !== true) {
                abort(500, $result);
            }
            return $this->_returnMsg(200, 'success', array('loanId' => $model->id));
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 支付贷款信息费用
     * @return array
     */
    public function payLoanInfo()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $loanId = $this->request->param('loanId/s');
            $type = $this->request->param('type/s');
            $loanInfo = LoanInfo::get(array('id' => $loanId, 'status' => '已上架'));
            if (!$loanInfo) abort(500, '信息不存在');
            $model = new CreditRecord;
            $result = $model->payLoanInfo($userId, $loanInfo, $type);
            if ($result === true) {
                return $this->_returnMsg(200, 'success');
            } else {
                abort(500, $result);
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 贷款信息退保证金
     * @return array
     */
    public function loanBackMoney()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $loanId = $this->request->param('loanId/s');
            $loanInfo = LoanInfo::get(array('id' => $loanId, 'status' => '已交保证金'));

            if (!$loanInfo) abort(500, '信息不存在');

            if ($loanInfo->pay_margin_user_id != $userId) {
                abort(500, '无权操作');
            }

            $frozen = Frozen::where('o_type', 'loan')->where('o_id', $loanInfo->id)->where('finish_status', 0)->order('id desc')->find();
            if ($frozen->unfrozen_time <= nowTime()) {
                abort(300, '该信息不能申请退还保证金');
            }

            $loanInfo->apply_refund = 1;
            $result = $loanInfo->save();
            if ($result) {
                return $this->_returnMsg(200, 'success');
            } else {
                abort(500, '申请失败');
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 典当信息退押金
     * @return array
     */
    public function pawnBackMoney()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $pawnId = $this->request->param('pawnId/s');
            $pawnInfo = PawnInfo::get(array('id' => $pawnId, 'status' => '已成交'));

            if (!$pawnInfo) abort(500, '信息不存在');

            if ($pawnInfo->pay_margin_user_id != $userId) {
                abort(500, '无权操作');
            }

            $frozen = Frozen::where('o_type', 'pawn')->where('o_id', $pawnInfo->id)->where('finish_status', 0)->order('id desc')->find();
            if ($frozen->unfrozen_time <= nowTime()) {
                abort(300, '该信息不能申请退还押金');
            }

            $pawnInfo->apply_refund = 1;
            $result = $pawnInfo->save();
            if ($result) {
                return $this->_returnMsg(200, 'success');
            } else {
                abort(500, '申请失败');
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 支付典当信息费用
     * @return array
     */
    public function payPawnInfo()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $pawnId = $this->request->param('pawnId/s');
            $type = $this->request->param('type/s');
            $pawnInfo = PawnInfo::get(array('id' => $pawnId, 'status' => '交易中'));
            if (!$pawnInfo) abort(500, '典当信息不存在');
            $model = new CreditRecord;
            $result = $model->payPawnInfo($userId, $pawnInfo, $type);
            if ($result === true) {
                return $this->_returnMsg(200, 'success');
            } else {
                abort(500, $result);
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 获取贷款信息列表
     * @return array
     */
    public function loanList()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            // $city = $this->request->param('city/s');
            $city = '';
            $page = $this->request->param('page/d', 1);
            $pageSize = $this->request->param('pageSize/d', 10);
            $data = array('viewMoney' => Setting::getConfigValue(1, 'browse_fee'), 'listSize' => 0, 'list' => array());
            $model = new LoanInfo;
            $where = array('status' => '已上架', 'user_id' => array('neq', $userId));
            if ($city) {
                $where['city'] = $city;
            }
            $list = $model->view('LoanInfo', 'id,type,add_time,name,loan_money,marry_status,pay_view_user_ids,interest,taobao_credit,user_id')->view('AuthInfo', 'user_type', 'AuthInfo.user_id=LoanInfo.user_id', 'LEFT')->where($where)->page($page, $pageSize)->select();
            if ($list) {
                $data['listSize'] = $model->where($where)->count();
                foreach ($list as $index => $item) {
                    if (!in_array($userId, array_filter(explode(',', $item->pay_view_user_ids))) && $item->user_id != $userId) {
                        $viewEnable = false;
                    } else {
                        $viewEnable = true;
                    }
                    $data['list'][] = array(
                        'id' => $item->id,
                        'type' => $item->type,
                        'time' => date('Y-m-d H:i:s', $item->add_time),
                        'commitUserType' => (string)$item->user_type,
                        'name' => $item->name,
                        'loanMoney' => $item->loan_money,
                        'marryStatus' => $item->marry_status,
                        'interest' => $item->interest,
                        'taobaoCredit' => $item->taobao_credit,
                        'viewEnable' => $viewEnable
                    );
                }
            }
            return $this->_returnMsg(200, 'success', $data);
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 获取贷款信息详情
     * @return array
     */
    public function loanInfoDetail()
    {
        try {
            $userId = $this->request->param('userId/s');
            $uid = User::userIdByIdentifier($userId) OR abort(500, '用户不存在');

            $loanId = $this->request->param('loanId/s') OR abort(500, '参数错误');
            $model = new LoanInfo;
            $result = $model->loanInfoDetail(array('id' => $loanId));


            if ($result->status != '已上架' && $result->status != '已交保证金' && $result->user_id != $uid && $result->pay_margin_user_id != $uid) {
                return $this->_returnMsg(300, 'success');
            }


            if (!in_array($uid, array_filter(explode(',', $result->pay_view_user_ids))) && $result->user_id != $uid) {
                return $this->_returnMsg(300, '该用户尚未支付浏览费');
            }

            $myMarginTime = CreditRecord::where(array('user_id' => $result->user_id, 'loan_id' => $result->id, 'type' => '支付信息保证金'))->value('create_time');
            $myMarginTime = date('Y-m-d H:i:s', $myMarginTime);

            /**判断手机号是否显示，交保人和发布人可见**/
            if ($result->pay_margin_user_id == $uid || $result->user_id == $uid) {
                $phone = $result->phone;
            } else {
                $phone = '';
            }

            $data = array();
            $data['type'] = $result->type;
            $data['payViewCount'] = count(array_filter(explode(',', $result->pay_view_user_ids)));
            $data['myMargin'] = $result->pay_margin_user_id == $uid ? true : false;
            $data['myMarginTime'] = $myMarginTime;
            $data['phone'] = $phone;
            $data['creditMoney'] = $result->credit_money;
            $data['agentPhone'] = $result->agent_phone;
            $data['name'] = $result->name;
            $data['gender'] = $result->gender;
            $data['IDNumber'] = $result->id_number;
            $data['takeIDPics'] = $result->take_id_pics;
            $data['takeIDPicsArray'] = $result->take_id_pics_array;
            $data['marryStatus'] = $result->marry_status;
            $data['marryBookPic'] = $result->marry_book_pic;
            $data['marryBookPicArray'] = isset($result->marry_book_pic_array) ? $result->marry_book_pic_array : [];
            $data['houseHoldBookPic'] = $result->house_hold_book_pic;
            $data['houseHoldBookPicArray'] = isset($result->house_hold_book_pic_array) ? $result->house_hold_book_pic_array : [];
            $data['taobaoAccount'] = $result->taobao_account;
            $data['taobaoCredit'] = $result->taobao_credit;
            $data['creditCardCount'] = $result->credit_card_count;
            $data['creditReportPic'] = $result->credit_report_pic;
            $data['creditReportPicArray'] = $result->credit_report_pic_array;
            $data['loanMoney'] = $result->loan_money;
            $data['interest'] = $result->interest;
            $data['city'] = $result->city;

            switch ($result->type) {
                case '车贷':
                    $data['carBrand'] = $result->car_brand;
                    $data['carMill'] = $result->car_mill;
                    $data['loanBuyCar'] = $result->loan_buy_car;
                    $data['carFullPic'] = $result->car_full_pic;
                    $data['carFullPicArray'] = $result->car_full_pic_array;
                    $data['carRegPic'] = $result->car_reg_pic;
                    $data['carRegPicArray'] = $result->car_reg_pic_array;
                    $data['drivingLicencePics'] = $result->driving_licence_pics;
                    $data['drivingLicencePicsArray'] = $result->driving_licence_pics_array;
                    $data['vehicleLicencePics'] = $result->vehicle_licence_pics;
                    $data['vehicleLicencePicsArray'] = $result->vehicle_licence_pics_array;
                    $data['carInvoicePic'] = $result->car_invoice_pic;
                    $data['carInvoicePicArray'] = $result->car_invoice_pic_array;
                    $data['policyPic'] = $result->policy_pic;
                    $data['policyPicArray'] = $result->policy_pic_array;
                    break;
                case '房贷':
                    $data['loanBuyHouse'] = $result->loan_buy_house;
                    $data['houseBothHave'] = $result->house_both_have;
                    $data['houseMayPrice'] = $result->house_may_price;
                    break;
                case '信贷':
                    $data['hadHouse'] = $result->had_house;
                    $data['hadCar'] = $result->had_car;
                    $data['hadLoan'] = $result->had_loan;
                    $data['jobCompanyType'] = $result->job_company_type;
                    $data['monthly'] = $result->monthly;
                    $data['socialSecurity'] = $result->social_security;
                    $data['providentFund'] = $result->provident_fund;
                    $data['inComeCertificatePic'] = $result->in_come_certificate_pic;
                    $data['inComeCertificatePicArray'] = isset($result->in_come_certificate_pic_array) ? $result->in_come_certificate_pic_array : [];
                    break;
                case '美丽贷':
                    $data['hadHouse'] = $result->had_house;
                    $data['hadCar'] = $result->had_car;
                    $data['hadLoan'] = $result->had_loan;
                    $data['takeBankCardPic'] = $result->take_bank_card_pic;
                    $data['takeBankCardPicArray'] = $result->take_bank_card_pic_array;
                    $data['fatherName'] = $result->father_name;
                    $data['fatherPhone'] = $result->father_phone;
                    $data['motherName'] = $result->mother_name;
                    $data['motherPhone'] = $result->mother_phone;
                    $data['friend1Name'] = $result->friend1_name;
                    $data['friend1Phone'] = $result->friend1_phone;
                    $data['friend2Name'] = $result->friend2_name;
                    $data['friend2Phone'] = $result->friend2_phone;
                    $data['friend3Name'] = $result->friend3_name;
                    $data['friend3Phone'] = $result->friend3_phone;
                    break;
            }
            return $this->_returnMsg(200, 'success', $data);
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 删除贷款信息
     * @return array
     */
    public function delLoanInfo()
    {
        try {
            $userId = $this->request->param('userId/s');
            $uid = User::userIdByIdentifier($userId) OR abort(500, '用户不存在');
            $loanId = $this->request->param('loanId/s') OR abort(500, '参数错误');
            $result = LoanInfo::get($loanId);
            if ($result->pay_margin_user_id > 0) {
                abort(500, '已交保证金无法删除');
            }
            if ($result->status == '待审核' || $result->status == '未通过') {
                $delResult = $result->del();
                if ($delResult === true) {
                    return $this->_returnMsg(200, 'success');
                } else {
                    abort(500, $delResult);
                }
            } else {
                abort(500, '删除失败');
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 提交典当信息
     * @return array
     */
    public function commitPawn()
    {
        try {
            $userId = $this->request->param('userId/s');
            $uid = User::userIdByIdentifier($userId) OR abort(500, '用户不存在');
            $pawnId = $this->request->param('pawnId/d');
            $data['user_id'] = $uid;
            $data['pawn_type'] = $this->request->param('pawnType/s') OR abort(500, '请选择类型');
            $data['city'] = $this->request->param('city/s') OR abort(500, '请填写城市');
            $data['buy_time'] = $this->request->param('buyTime/s') OR abort(500, '请填写购买时间');
            $data['buy_price'] = $this->request->param('buyPrice/s') OR abort(500, '请填写购买价格');
            $data['sale_price'] = $this->request->param('salePrice/s') OR abort(500, '请填写寄卖价格');
            $data['contact_phone'] = $this->request->param('phone/s') OR abort(500, '请填写联系电话');
            $data['title'] = $this->request->param('title/s') OR abort(500, '请填写标题');
            $data['desc'] = $this->request->param('desc/s') OR abort(500, '请填写描述');
            $data['pics'] = $this->request->param('pics/s') OR abort(500, '请上传描述图片');

            if ($pawnId > 0) {
                $model = PawnInfo::get($pawnId);
                if ($model->pay_margin_user_id > 0) {
                    abort(500, '已交押金无法修改');
                }
                $result = $model->edit($data);
            } else {
                $model = new PawnInfo;
                $result = $model->add($data);
            }
            if ($result !== true) {
                return $this->_returnMsg(500, $result);
            } else {
                return $this->_returnMsg(200, 'success', array('pawnId' => $model->id));
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 典当信息列表
     * @return array
     */
    public function pawnList()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
//            $city = $this->request->param('city/s');
            $page = $this->request->param('page/d', 1);
            $pageSize = $this->request->param('pageSize/d', 10);

            $data = array('viewMoney' => Setting::getConfigValue(3, 'pawn_browse_fee'), 'scp' => array(), 'jyzb' => array(), 'qt' => array());
            $model = new PawnInfo;

            $where = array('status' => '交易中', 'user_id' => array('neq', $userId));
            if ($city) {
                $where['city'] = $city;
            }
            $list = $model->where($where)->order("id desc")->select();
            if ($list) {
//                $data['listSize'] = $model->where($where)->count();
                foreach ($list as $index => $item) {
                    if (!in_array($userId, array_filter(explode(',', $item->pay_view_user_ids))) && $item->user_id != $userId) {
                        $viewEnable = false;
                    } else {
                        $viewEnable = true;
                    }
                    $pics = $item->PawnInfoPics()->column('path');
                    $picsArray = array();
                    $picsLength = 3;
                    foreach ($pics as $pic) {
                        if ($picsLength > 0) {
                            $picsArray[] = PrivateImage::getImageUrl($pic, User::userIdById($item->user_id));
                        }
                        $picsLength--;
                    }

                    $temp = array(
                        'id' => $item->id,
                        'title' => $item->title,
                        'price' => $item->sale_price,
                        'buyPrice' => $item->buy_price,
                        'pics' => implode(',', $pics),
                        'picsArray' => $picsArray,
                        'city' => $item->city,
                        'distanceOutTime' => date('Y-m-d H:i', $item->distance_out_time),
                        'viewEnable' => $viewEnable
                    );


                    switch ($item->pawn_type) {
                        case '奢侈品':
                            $data['scp'][] = $temp;
                            break;
                        case '金银珠宝':
                            $data['jyzb'][] = $temp;
                            break;
                        case '其他':
                            $data['qt'][] = $temp;
                            break;
                    }
                }
            }
            return $this->_returnMsg(200, 'success', $data);

        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 获取典当信息详情
     * @return array
     */
    public function pawnInfoDetail()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $pawnId = $this->request->param('pawnId/d') OR abort(500, '参数错误');

            $result = PawnInfo::get($pawnId);

            if ($result) {

                if ($result->status != '交易中' && $result->status != '已成交' && $result->user_id != $userId && $result->pay_margin_user_id != $userId) {
                    return $this->_returnMsg(300, 'success');
                }


                if (!in_array($userId, array_filter(explode(',', $result->pay_view_user_ids))) && $result->user_id != $userId) {
                    return $this->_returnMsg(300, '该用户尚未支付浏览费');
                }

                $myMargin = $result->pay_margin_user_id == $userId ? true : false;
                if ($myMargin) {
                    $myMarginTime = CreditRecord::where(array('user_id' => $result->user_id, 'loan_id' => $result->id, 'type' => '支付典当押金'))->value('create_time');
                    $myMarginTime = date('Y-m-d H:i:s', $myMarginTime);
                } else {
                    $myMarginTime = '';
                }


                /**判断手机号是否显示，交保人和发布人可见**/
                if ($result->pay_margin_user_id == $userId || $result->user_id == $userId) {
                    $phone = $result->contact_phone;
                } else {
                    $phone = '';
                }

                $data = array();
                $data['pawnType'] = $result->pawn_type;
                $data['myMargin'] = $myMargin;
                $data['myMarginTime'] = $myMarginTime;
                $data['phone'] = $phone;
                $data['title'] = $result->title;
                $data['buyTime'] = $result->buy_time;
                $data['buyPrice'] = $result->buy_price;
                $data['desc'] = $result->desc;
                $data['salePrice'] = $result->sale_price;
                $data['distanceOutTime'] = $result->distance_out_time > 0 ? date('Y-m-d H:i', $result->distance_out_time) : '';
                $data['city'] = $result->city;

                $pics = $result->PawnInfoPics()->column('path');
                $data['pics'] = implode(',', $pics);
                $picsArray = array();
                foreach ($pics as $pic) {
                    $picsArray[] = PrivateImage::getImageUrl($pic, User::userIdById($result->user_id));
                }
                $data['picsArray'] = $picsArray;

                return $this->_returnMsg(200, 'success', $data);
            } else {
                abort(500, '信息不存在');
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 我的典当列表
     * @return array
     */
    public function myPawnList()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $page = $this->request->param('page/d', 1);
            $pageSize = $this->request->param('pageSize/d', 10);

            $data = array('viewMoney' => Setting::getConfigValue(3, 'pawn_browse_fee'), 'listSize' => 0, 'list' => array());
            $model = new PawnInfo;

            $where = array('user_id' => array('eq', $userId));
            $list = $model->where($where)->select();

            if ($list) {
                $data['listSize'] = $model->where($where)->count();
                foreach ($list as $index => $item) {
                    $pics = $item->PawnInfoPics()->column('path');
                    $picsArray = array();
                    $picsLength = 3;
                    foreach ($pics as $pic) {
                        if ($picsLength > 0) {
                            $picsArray[] = PrivateImage::getImageUrl($pic, User::userIdById($item->user_id));
                        }
                        $picsLength--;
                    }

                    $data['list'][] = array(
                        'id' => $item->id,
                        'title' => $item->title,
                        'price' => $item->sale_price,
                        'pics' => implode(',', $pics),
                        'picsArray' => $picsArray,
                        'city' => $item->city,
                        'status' => $item->status
                    );
                }
            }

            return $this->_returnMsg(200, 'success', $data);
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 我买到的列表
     * @return array
     */
    public function myBuyPawnList()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $page = $this->request->param('page/d', 1);
            $pageSize = $this->request->param('pageSize/d', 10);

            $data = array('viewMoney' => Setting::getConfigValue(3, 'pawn_browse_fee'), 'listSize' => 0, 'list' => array());
            $model = new PawnInfo;

            $where = array('pay_margin_user_id' => array('eq', $userId));
            $list = $model->where($where)->select();

            if ($list) {
                $data['listSize'] = $model->where($where)->count();
                foreach ($list as $index => $item) {
                    $pics = $item->PawnInfoPics()->column('path');
                    $picsArray = array();
                    $picsLength = 3;
                    foreach ($pics as $pic) {
                        if ($picsLength > 0) {
                            $picsArray[] = PrivateImage::getImageUrl($pic, User::userIdById($item->user_id));
                        }
                        $picsLength--;
                    }

                    $unfrozen_time = Frozen::where('finish_status', 0)->where('o_id', $item->id)->where('o_type', 'pawn')->where('user_id', $userId)->value('unfrozen_time');
                    $backEnable = ($unfrozen_time > nowTime() && $item->apply_refund == 0) ? true : false;

                    $data['list'][] = array(
                        'id' => $item->id,
                        'title' => $item->title,
                        'price' => $item->sale_price,
                        'pics' => implode(',', $pics),
                        'picsArray' => $picsArray,
                        'city' => $item->city,
                        'status' => $item->status,
                        'backEnable' => $backEnable
                    );
                }
            }

            return $this->_returnMsg(200, 'success', $data);
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 删除典当信息
     * @return array
     */
    public function delPawnInfo()
    {
        try {
            $userId = $this->request->param('userId/s');
            $uid = User::userIdByIdentifier($userId) OR abort(500, '用户不存在');
            $pawnId = $this->request->param('pawnId/d');
            $result = PawnInfo::get($pawnId);
            if ($result->pay_margin_user_id > 0) {
                abort(500, '已交押金无法删除');
            }
            if ($result->status == '待审核'||$result->status == '已下架') {
                $delResult = $result->del();
                if ($delResult === true) {
                    return $this->_returnMsg(200, 'success');
                } else {
                    abort(500, $delResult);
                }
            } else {
                abort(500, '信息不存在');
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 获取短信验证码
     * @return array
     */
    public function pushCode()
    {
        try {
            $phone = $this->request->param('phone/s');
            isMobilePhone($phone) OR abort(500, '请输入正确手机号');
            $type = $this->request->param('type/s');
//            return $this->_returnMsg(200, 'success', array('code' => '123456'));
            //用户获取验证码，包括注册验证码（类型为：101）、提交认证验证码（类型为：102）
            $code = rand(100000, 999999);
            $sendResult = sendMsg($phone, $code);
            if ($sendResult === true) {
                return $this->_returnMsg(200, 'success', array('code' => $code));
            } else {
                abort(500, $sendResult);
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 获取首页内容
     * @return array
     */
    public function mainInfo()
    {
        $data = array();
        $data['banners'] = array();

        //读取轮播图缓存数据
        cache(['path' => CACHE_PATH . 'slide']);
        $cache = cache('slide');

        foreach ($cache['pic'] as $index => $pic) {
            if (!empty($pic) && isset($cache['is_show'][$index])) {
                $data['banners'][] = array(
                    'pic' => fullPath($pic),
                    'link' => $cache['url'][$index]
                );
            }
        }
        $data['cities'] = explode(',', Setting::getConfigValue(4, 'open_city'));
        $data['info'] = Setting::getConfigValue(4, 'notice_message');
        return $this->_returnMsg(200, 'success', $data);
    }

    /**
     * 收付款工具个人信息
     * @return array
     */
    public function toolsProfileInfo()
    {
        try {
            $userId = $this->request->param('userId/s');
            $user = User::get(['identifier' => $userId]);
            if ($user) {
                $data = array(
                    'currentToolMoney' => (float)ToolRecord::where('user_id', $user->id)->where('user_in_come_status', '已到账')->sum('abs(money)'),
                    'rate' => $user->AdminCompany->rate,
                    'authMoney' => (float)Setting::getConfigValue(2, 'auth_fee'),
                	'info'=>Setting::getConfigValue(4, 'tool_notice_message')
                );
                return $this->_returnMsg(200, 'success', $data);
            } else {
                abort(500, '用户不存在');
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 收付款工具收款
     * @return array
     */
    public function collectionMoney()
    {
        try {
            $userId = $this->request->param('userId/s') OR abort(500, '参数错误');
            $money = $this->request->param('money/f') OR abort(500, '参数错误');
            $type = $this->request->param('type/s') OR abort(500, '参数错误');
            $payType = $this->request->param('payType/s') OR abort(500, '参数错误');
            $channel = $this->request->param('channel/s', 'hf');
            $payment = '未知';
            if ($payType == '120') {
                $payment = '微信';
            }
            if ($payType == '121') {
                $payment = '支付宝';
            }
            if ($payType == '122') {
                $payment = '京东';
            }
            $user = User::get(['identifier' => $userId]);
            if ($user) {
                if ($payment != '微信') {
                    $model = new ToolRecord;
                    $model->user_id = $user->id;
                    $model->company_id = $user->company_id;
                    $model->type = $type;
                    $model->money = $money;
                    $model->create_time = nowTime();
                    $model->month = date('m', nowTime());
                    $model->admin_in_come_status = '未到账';
                    $model->user_in_come_status = '未到账';
                    $model->order_id = $model->createOrderId();
                    $model->payment = $payment;
                    $model->pay_channel = $channel;
                    $result = $model->save();
                } else {
                    $result = true;
                }
                if ($result) {
                    /**发起扫码支付**/
                    $config = array(
                        'mobile' => $user->phone,
                        'bank_no' => $user->AuthInfo->bank_card_number,
                        'bank_name' => $user->AuthInfo->bank_name,
                        'bank_sub' => $user->AuthInfo->bank_sub_name,
                        'bank_code' => $user->AuthInfo->bank_code,
                        'card_no' => $user->AuthInfo->id_number,
                        'name' => $user->AuthInfo->name,
                        'rate' => $user->AdminCompany->rate,
                        'pay_type' => $payType
                    );
                    $Pay = new Pay($config);
                    $payCode = $Pay->QrCodePay(array('order_id' => $model->order_id, 'amount' => $money, 'type' => 'tool', 'channel' => $channel));
                    return $this->_returnMsg(200, 'success', array('payCode' => $payCode));
                } else {
                    abort(500, '保存订单失败');
                }
            } else {
                abort(500, '用户不存在');
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 推广分红转信用币
     * @return array
     */
    public function distributionCollection()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $model = new DistributionProfit;
            $result = $model->distributionCollection($userId);
            if ($result === true) {
                return $this->_returnMsg(200, 'success');
            } else {
                abort(500, $result);
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 付款码付款
     * payByQRCode
     */
    public function payByQRCode()
    {
        try {
//            $user = User::get(['identifier' => $this->request->param('userId/s')]) OR abort(500, '用户不存在1');
            $objUser = User::get(['identifier' => $this->request->param('objUserId/s')]) OR abort(500, '用户不存在2');
            $money = $this->request->param('money/f');
            $channel = $this->request->param('channel/s', 'hf');
            $payType = $this->request->param('payType/s') OR abort(500, '参数错误');
            $payment = '未知';
            if ($payType == '120') {
                $payment = '微信';
            }
            if ($payType == '121') {
                $payment = '支付宝';
            }
            if ($payType == '122') {
                $payment = '京东';
            }

            $model = new ToolRecord;
            $orderId = $model->createOrderId2();
            $data = array(
//                array(
//                    'user_id' => $user->id,
//                    'company_id' => $user->company_id,
//                    'type' => '付款码付款',
//                    'money' => -$money,
//                    'create_time' => nowTime(),
//                    'month' => date('m', nowTime()),
//                    'admin_in_come_status' => '未到账',
//                    'user_in_come_status' => '未到账',
//                    'order_id' => $orderId
//                ),
                array(
                    'user_id' => $objUser->id,
                    'company_id' => $objUser->company_id,
                    'type' => '付款码收款',
                    'money' => $money,
                    'create_time' => nowTime(),
                    'month' => date('m', nowTime()),
                    'admin_in_come_status' => '未到账',
                    'user_in_come_status' => '未到账',
                    'order_id' => $orderId,
                    'payment' => $payment,
                    'pay_channel' => $channel
                )
            );

            $model->saveAll($data);


            /**发起扫码支付**/
            $config = array(
                'mobile' => $objUser->phone,
                'bank_no' => $objUser->AuthInfo->bank_card_number,
                'bank_name' => $objUser->AuthInfo->bank_name,
                'bank_sub' => $objUser->AuthInfo->bank_sub_name,
                'bank_code' => $objUser->AuthInfo->bank_code,
                'card_no' => $objUser->AuthInfo->id_number,
                'name' => $objUser->AuthInfo->name,
                'rate' => $objUser->AdminCompany->rate,
                'pay_type' => $payType
            );
            $Pay = new Pay($config);
            $payCode = $Pay->QrCodePay(array('order_id' => $orderId, 'amount' => $money, 'type' => 'tool2'));

            return $this->_returnMsg(200, 'success', array('payCode' => $payCode));
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * 获取私密图片
     * @param string $u 用户标示
     * @param string $i 图片地址加密字符串
     * @return string
     */
    public function getImage($u = '', $i = '')
    {
        try {
            $u = User::userIdByIdentifier($u);
            return PrivateImage::getImage($i, $u);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 上传图片
     * @return array|string
     */
    public function uploadPic()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            //$fileName = $this->request->param('fileName/s');
            if ($fileName && $fileName != '') {
                $fileName = IMAGES . DS . $fileName;
            } else {
                $fileName = true;
            }
            $file = $this->request->instance()->file('file');
            if ($file === null) {
                abort(500, '请选择上传文件');
            } else {
                $validate = array(
                    'size' => 1024 * 1024 * 5,
                    'ext' => 'jpg,png,jpeg',
                    'type' => 'image/jpeg,image/png'
                );
                $info = $file->validate($validate)->move(IMAGES, $fileName);
                if ($info) {

                    $image = Image::open($info->getRealPath());
                    $image->thumb(800, 600)->save($info->getRealPath());

                    $saveName = str_replace(['/', '.'], ['AAAAA', 'BBBBB'], $info->getSaveName());
                    $file_path = urlencode(StringCrypt::encrypt($saveName, $userId));
                    $result = array(
                        'fileName' => $file_path
                    );
                    return $this->_returnMsg(200, 'success', $result);
                } else {
                    abort(500, $file->getError());
                }
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**
     * _getLoanCommonData
     * @return array
     */
    private function _getLoanCommonData()
    {
        $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
        $data = array(
            'user_id' => $userId,
            'name' => $this->request->param('name/s'),
            'phone' => $this->request->param('phone/s'),
            'gender' => $this->request->param('gender/s'),
            'id_number' => $this->request->param('IDNumber/s'),
            'take_id_pics' => $this->request->param('takeIDPics/s'),
            'marry_status' => $this->request->param('marryStatus/s'),
            'marry_book_pic' => $this->request->param('marryBookPic/s'),
            'house_hold_book_pic' => $this->request->param('houseHoldBookPic/s'),
            'taobao_account' => $this->request->param('taobaoAccount/s'),
            'taobao_credit' => $this->request->param('taobaoCredit/s'),
            'credit_card_count' => $this->request->param('creditCardCount/s'),
            'credit_report_pic' => $this->request->param('creditReportPic/s'),
            'loan_money' => $this->request->param('loanMoney/s'),
            'interest' => $this->request->param('interest/s'),
            'status' => '待审核',
            'add_time' => nowTime(),
            'pay_view_user_ids' => '',
            'city' => $this->request->param('city/s')
        );
        return $data;
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

    public function payNotify2()
    {
        file_put_contents('./pay_notify2.txt', json_encode($this->request->param()));
        $Pay = new Pay;
        $orderId = $Pay->Notify($this->request->param());
        if ($orderId !== false) {
            $model = ToolRecord::get(['order_id' => $orderId]);
            if (!$model) {
                $user = User::get(['phone' => $this->request->param('phone/s')]);
                $ToolRecord = new ToolRecord;
                $ToolRecord->user_id = $user->id;
                $ToolRecord->company_id = $user->company_id;
                $ToolRecord->type = 'T0';
                $ToolRecord->money = $this->request->param('money') / 100;
                $ToolRecord->create_time = nowTime();
                $ToolRecord->month = date('m', nowTime());
                $ToolRecord->admin_in_come_status = '未到账';
                $ToolRecord->user_in_come_status = '未到账';
                $ToolRecord->order_id = $orderId;
                $ToolRecord->payment = '微信';
                $ToolRecord->pay_channel = 'hf';
                $ToolRecord->save();
            }
            $model = ToolRecord::get(['order_id' => $orderId]);
            if ($model->admin_in_come_status == '已到账') {
                return '';
            } else {
                $model->saveOrder();
            }
        }
    }

    /**支付成功异步通知**/
    public function payNotify()
    {
    	file_put_contents('./pay_notify.txt', json_encode($this->request->param()),FILE_APPEND);
        $Pay = new Pay;
        $orderId = $Pay->Notify($this->request->param());
        file_put_contents('./pay_notify.txt', "订单号:".$orderId,FILE_APPEND);
        if ($orderId !== false) {
            switch ($this->request->param('type')) {
                //信用币充值
                case 'recharge':
                    $model = CreditRecord::get(['order_id' => $orderId]);
                    if ($model->status == '正常') {
                        return '';
                    } else {
                        $model->recharge();
                    }
                    break;
                //支付认证费
                case 'auth':
                    $model = AuthInfo::get(['order_id' => $orderId]);
                    if ($model->auth_pay_time > 0) {
                        return '';
                    } else {
                        $model->paySuccess();
                    }
                    break;
                //收付款工具收款
                case 'tool':
                    $model = ToolRecord::get(['order_id' => $orderId]);
                    if ($model->admin_in_come_status == '已到账') {
                        return '';
                    } else {
                        $model->saveOrder();
                    }
                    break;
                //付款码付款
                case 'tool2':
                    ToolRecord::update(['admin_in_come_status' => '已到账', 'user_in_come_status' => '已到账'], ['order_id' => $orderId]);
                    break;
            }
        }
    }

    /**支付成功清算异步通知**/
    public function settlementNotify()
    {
        file_put_contents('./settlement_notify.txt', json_encode($this->request->param()));
        $Pay = new Pay;
        $post = $Pay->SettlementNotify($this->request->param());
        if ($post !== false) {
            $model = new SettlementNotify;
            $model->type = $this->request->param('type');
            $model->create_time = time();
            $model->content = serialize($post);
            $model->save();
        }
    }

    /**微信网页授权**/
    public function wxAuth()
    {
        $code = $this->request->param('code');
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxb1993648e68557fd&secret=c2796e3276cfb19f6e4ce0a88c19fcd3&code=' . $code . '&grant_type=authorization_code';
        $result = json_decode(curlGet($url), true);
        if (isset($result['errcode'])) {
            return $this->_returnMsg(500, 'errcode:' . $result['errcode'] . ',errmsg:' . $result['errmsg']);
        }
//        $access_token = $result['access_token'];
//        $openid = $result['openid'];
//        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
//        $result = json_decode(curlGet($url), true);
//        if (isset($result['errcode'])) {
//            return $this->_returnMsg(500, 'errcode:' . $result['errcode'] . ',errmsg:' . $result['errmsg']);
//        }
        return $this->_returnMsg(200, 'success', array('openid' => $result['openid']));
    }

    public function wxAuth2()
    {
        $code = $this->request->param('code');
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxb1993648e68557fd&secret=c2796e3276cfb19f6e4ce0a88c19fcd3&code=' . $code . '&grant_type=authorization_code';
        $result = json_decode(curlGet($url), true);
        if (isset($result['errcode'])) {
            return $this->_returnMsg(500, 'errcode:' . $result['errcode'] . ',errmsg:' . $result['errmsg']);
        }
        $access_token = $result['access_token'];
        $openid = $result['openid'];

        //新用户注册
        $model = new User;
        if (!$model->where('wx', $result['openid'])->count()) {

            $parent_id = intval(User::where('identifier', $this->request->param('ui/s'))->value('id'));
            $company_id = AdminCompany::where('identifier', $this->request->param('ci/s'))->value('id');
            $company_id = intval($company_id) ? intval($company_id) : 1;

            $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
            $result = json_decode(curlGet($url), true);
            if (isset($result['errcode'])) {
                return $this->_returnMsg(500, 'errcode:' . $result['errcode'] . ',errmsg:' . $result['errmsg']);
            }

            $model->wx = $result['openid'];
            $model->nickname = $result['nickname'];
            $model->company_id = $company_id;
            $model->city = $result['city'];
            $model->thumb = $result['headimgurl'];
            $model->parent_id = $parent_id;
            $model->addUser();
        }
        return $this->_returnMsg(200, 'success', array('openid' => $result['openid']));
    }

    public function qrcode()
    {
        $userId = $this->request->param('userId/s');
        $uid = User::userIdByIdentifier($userId);
        $companyId = AdminCompany::companyIdByIdentifier($this->request->param('companyId/s'));
        if ($userId != '' && !$uid) {
            $result = array('code' => 500, 'codeMsg' => '用户不存在');
            return json_encode($result);
        } elseif (!$companyId) {
            $result = array('code' => 500, 'codeMsg' => '公司不存在');
            return json_encode($result);
        }

        $app = new Application($this->options);
        $qrcode = $app->qrcode;
        $result = $qrcode->forever('user:' . $uid . ',company:' . $companyId);
        $ticket = $result->ticket;// 或者 $result['ticket']
        return $result->url; // 二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片
    }

    public function wxJsSign()
    {
        $url = str_replace(['[1]', '[2]', '[3]', '[4]', '[5]'], ['/', '&', '#', '?', '='], $this->request->param('url/s'));
        $app = new Application($this->options);
        $app->js->setUrl($url);
        $config = $app->js->config(array('onMenuShareAppMessage', 'onMenuShareTimeline', 'hideMenuItems'), false, false, true);
        return $config;
    }

    public function templateMessage()
    {
        try {
            $userId = $this->request->param('userId/s');
            $openid = User::where('identifier', $userId)->value('wx');
            $app = new Application($this->options);
            $notice = $app->notice;
            $templateId = 'P2cb_wd5IlshaoBTOfTtUpRYPk5ntEice4wYCrfvA5c';
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxb1993648e68557fd&redirect_uri=http://wx.sqstz360.com/&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
            $data = array(
                "first" => "认证提醒",
                "keyword1" => "您身边的金融平台等您来牵手",
                "keyword2" => "费率0.30立马到账",
                "keyword3" => date('Y-m-d'),
                "remark" => "请务必填写真实信息",
            );
            $result = $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openid)->send();
            if ($result->errcode == 0) {
                return $this->_returnMsg(200, 'success');
            } else {
                return $this->_returnMsg(500, $result->errmsg);
            }
        } catch (\Exception $e) {
            if (strchr($e->getMessage(), 'require subscribe hint')) {
                return $this->_returnMsg(500, '对方未关注');
            }
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    public function sharePawnInfoDetail()
    {
        try {
            $pawnId = $this->request->param('pawnId/d') OR abort(500, '参数错误');

            $result = PawnInfo::get($pawnId);

            if ($result) {
                $userId = $this->request->param('userId/s');
                $uid = User::userIdByIdentifier($userId);
                $companyId = AdminCompany::companyIdByIdentifier($this->request->param('companyId/s'));
                if ($userId != '' && !$uid) {
                    abort(500, '用户不存在');
                } elseif (!$companyId) {
                    abort(500, '公司不存在');
                }
                $app = new Application($this->options);
                $qrcode = $app->qrcode;
                $wx = $qrcode->forever('user:' . $uid . ',company:' . $companyId);
                //$ticket = $result->ticket;// 或者 $result['ticket']

                $data = array();
                $data['pawnType'] = $result->pawn_type;
                $data['title'] = $result->title;
                $data['buyTime'] = $result->buy_time;
                $data['buyPrice'] = $result->buy_price;
                $data['desc'] = $result->desc;
                $data['salePrice'] = $result->sale_price;
                $data['distanceOutTime'] = $result->distance_out_time > 0 ? date('Y-m-d H:i', $result->distance_out_time) : '';
                $data['city'] = $result->city;

                $pics = $result->PawnInfoPics()->column('path');
                $data['pics'] = implode(',', $pics);
                $picsArray = array();
                foreach ($pics as $pic) {
                    $picsArray[] = PrivateImage::getImageUrl($pic, User::userIdById($result->user_id));
                }
                $data['picsArray'] = $picsArray;
                $data['wxUrl'] = $wx->url;

                return $this->_returnMsg(200, 'success', $data);
            } else {
                abort(500, '信息不存在');
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    public function shareLoanInfoDetail()
    {
        try {
            $loanId = $this->request->param('loanId/s') OR abort(500, '参数错误');
            $model = new LoanInfo;
            $result = $model->loanInfoDetail(array('id' => $loanId));

            $data = array();
            $data['type'] = $result->type;
            $data['payViewCount'] = count(array_filter(explode(',', $result->pay_view_user_ids)));
            $data['creditMoney'] = $result->credit_money;
            $data['agentPhone'] = $result->agent_phone;
            $data['name'] = $result->name;
            $data['gender'] = $result->gender;
            $data['IDNumber'] = $result->id_number;
            $data['takeIDPics'] = $result->take_id_pics;
            $data['takeIDPicsArray'] = $result->take_id_pics_array;
            $data['marryStatus'] = $result->marry_status;
            $data['marryBookPic'] = $result->marry_book_pic;
            $data['marryBookPicArray'] = isset($result->marry_book_pic_array) ? $result->marry_book_pic_array : [];
            $data['houseHoldBookPic'] = $result->house_hold_book_pic;
            $data['houseHoldBookPicArray'] = isset($result->house_hold_book_pic_array) ? $result->house_hold_book_pic_array : [];
            $data['taobaoAccount'] = $result->taobao_account;
            $data['taobaoCredit'] = $result->taobao_credit;
            $data['creditCardCount'] = $result->credit_card_count;
            $data['creditReportPic'] = $result->credit_report_pic;
            $data['creditReportPicArray'] = $result->credit_report_pic_array;
            $data['loanMoney'] = $result->loan_money;
            $data['interest'] = $result->interest;
            $data['city'] = $result->city;

            switch ($result->type) {
                case '车贷':
                    $data['carBrand'] = $result->car_brand;
                    $data['carMill'] = $result->car_mill;
                    $data['loanBuyCar'] = $result->loan_buy_car;
                    $data['carFullPic'] = $result->car_full_pic;
                    $data['carFullPicArray'] = $result->car_full_pic_array;
                    $data['carRegPic'] = $result->car_reg_pic;
                    $data['carRegPicArray'] = $result->car_reg_pic_array;
                    $data['drivingLicencePics'] = $result->driving_licence_pics;
                    $data['drivingLicencePicsArray'] = $result->driving_licence_pics_array;
                    $data['vehicleLicencePics'] = $result->vehicle_licence_pics;
                    $data['vehicleLicencePicsArray'] = $result->vehicle_licence_pics_array;
                    $data['carInvoicePic'] = $result->car_invoice_pic;
                    $data['carInvoicePicArray'] = $result->car_invoice_pic_array;
                    $data['policyPic'] = $result->policy_pic;
                    $data['policyPicArray'] = $result->policy_pic_array;
                    break;
                case '房贷':
                    $data['loanBuyHouse'] = $result->loan_buy_house;
                    $data['houseBothHave'] = $result->house_both_have;
                    $data['houseMayPrice'] = $result->house_may_price;
                    break;
                case '信贷':
                    $data['hadHouse'] = $result->had_house;
                    $data['hadCar'] = $result->had_car;
                    $data['hadLoan'] = $result->had_loan;
                    $data['jobCompanyType'] = $result->job_company_type;
                    $data['monthly'] = $result->monthly;
                    $data['socialSecurity'] = $result->social_security;
                    $data['providentFund'] = $result->provident_fund;
                    $data['inComeCertificatePic'] = $result->in_come_certificate_pic;
                    $data['inComeCertificatePicArray'] = isset($result->in_come_certificate_pic_array) ? $result->in_come_certificate_pic_array : [];
                    break;
                case '美丽贷':
                    $data['hadHouse'] = $result->had_house;
                    $data['hadCar'] = $result->had_car;
                    $data['hadLoan'] = $result->had_loan;
                    $data['takeBankCardPic'] = $result->take_bank_card_pic;
                    $data['takeBankCardPicArray'] = $result->take_bank_card_pic_array;
                    $data['fatherName'] = $result->father_name;
                    $data['fatherPhone'] = $result->father_phone;
                    $data['motherName'] = $result->mother_name;
                    $data['motherPhone'] = $result->mother_phone;
                    $data['friend1Name'] = $result->friend1_name;
                    $data['friend1Phone'] = $result->friend1_phone;
                    $data['friend2Name'] = $result->friend2_name;
                    $data['friend2Phone'] = $result->friend2_phone;
                    $data['friend3Name'] = $result->friend3_name;
                    $data['friend3Phone'] = $result->friend3_phone;
                    break;
            }
            return $this->_returnMsg(200, 'success', $data);
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    public function sharePawnReturn()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $pawnId = $this->request->param('pawnId/d') OR abort(500, '参数错误');
            $pawn = PawnInfo::get($pawnId);
            $CreditRecord = new CreditRecord;
            $result = $CreditRecord->sharePawnReturn($pawn, $userId);
            if ($result === true) {
                return $this->_returnMsg(200, 'success');
            } else {
                abort(500, $result);
            }
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }
    public function commitCredit(){
    	
    	try {
    		$data = $this->_getCreditData();
    		$result = $this->validate($data, 'CreditCard.apply');
    		if ($result !== true) abort(500, $result);
    		if ($loanId) {
    			$model = CreditCard::get($loanId);
    			
    		} else {
    			$model = new CreditCard;
    			$model->user_id = $data['user_id'];
    			$model->ali_account= $data['ali_account'];
    			$model->bank_name= $data['bank_name'];
    			$model->bank_number= $data['bank_number'];
    			$model->contact_people= $data['contact_people'];
    			$model->contact_phone= $data['contact_phone'];
    			$model->contact_relation= $data['contact_relation'];
    			$model->credit_number= $data['credit_number'];
    			$model->credit_report_pics= $data['credit_report_pics'];
    			$model->name= $data['name'];
    			$model->phone= $data['phone'];
    			$model->save();
    		}
    		return $this->_returnMsg(200, 'success');
    	} catch (\Exception $e) {
    		return $this->_returnMsg(500, $e->getMessage());
    	}
    }
    /**
     * _getLoanCommonData
     * @return array
     */
    private function _getCreditData()
    {
    	$userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
    	$data = array(
    			'user_id' => $userId,
    			'ali_account' => $this->request->param('aliAccount/s'),
    			'bank_name' => $this->request->param('bankName/s'),
    			'bank_number' => $this->request->param('bankNumber/s'),
    			'contact_people' => $this->request->param('contactPeople/s'),
    			'contact_phone' => $this->request->param('contactPhone/s'),
    			'contact_relation' => $this->request->param('contactRelation/s'),
    			'credit_number' => $this->request->param('creditNumber/s'),
    			'credit_report_pics' => $this->request->param('creditReportPics/s'),
    			'name' => $this->request->param('name/s'),
    			'phone' => $this->request->param('phone/s'),
    	);
    	return $data;
    }
    
    
    public function pawnNotice()
    {
        $app = new Application($this->options);
        $notice = $app->notice;
        $templateId = 'P2cb_wd5IlshaoBTOfTtUpRYPk5ntEice4wYCrfvA5c';
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxb1993648e68557fd&redirect_uri=http://wx.sqstz360.com/&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
        $jobs = Db::table('snake_jobs')->limit(100)->select();
        foreach ($jobs as $job) {
            try {
                $openid = $job['openid'];
                $data = array(
                    "first" => $job['first'],
                    "keyword1" => $job['keyword1'],
                    "keyword2" => $job['keyword2'],
                    "keyword3" => $job['keyword3'],
                    "remark" => $job['remark']
                );
                Db::table('snake_jobs')->where('id', $job['id'])->delete();
                //$notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openid)->send();
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    public function question()
    {
        try {
            $userId = User::userIdByIdentifier($this->request->param('userId/s')) OR abort(500, '用户不存在');
            $content = $this->request->param('question/s');
            if (empty($content)) abort(500, '请填写问题');
            $question = new Question;
            $question->user_id = $userId;
            $question->question = $content;
            $question->save();
            return $this->_returnMsg(200, 'success');
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    public function questionList()
    {
        try {
            $question = new Question;
            $list = $question->getAllList();
            return $this->_returnMsg(200, 'success', $list);
        } catch (\Exception $e) {
            return $this->_returnMsg(500, $e->getMessage());
        }
    }

    /**计划任务**/
    public function task($type)
    {
        switch ($type) {
            //php /home/wwwroot/default/public/index.php api/Wx/task/type/FinishOrder
            case 'FinishOrder':
                Frozen::finishOrder();
                break;
            case 'HaltOrder':
                PawnInfo::HaltOrder();
                break;
            default:
                echo '-';
                break;
        }
    }

}