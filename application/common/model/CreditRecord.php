<?php

namespace app\common\model;

use think\Model;

//信用币纪录
class CreditRecord extends Model
{
    public function User()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    public function LoanInfo()
    {
        return $this->belongsTo('LoanInfo', 'loan_id', 'id');
    }

    /**
     * 事件注册
     */
    protected static function init()
    {
        self::event('before_insert', function ($model) {
            $model->create_time = nowTime();
        });
    }

    private function setType($type)
    {
        switch ($type) {
            //充值
            case '1':
                $this->data('type', '充值');
                $this->status = '到账中';
                break;
            case '2':
                $this->data('type', '提现');
                $this->status = '到账中';
                break;
            case '3':
                $this->data('type', '退还信息保证金');
                $this->status = '审核中';
                break;
            case '4':
                $this->data('type', '收取信息保障金');
                $this->status = '正常';
                break;
            case '5':
                $this->data('type', '支付信息保证金');
                $this->status = '正常';
                break;
            case '6':
                $this->data('type', '支付信息浏览费');
                $this->status = '正常';
                break;
            default:
                abort(500, '未知类型');
                break;
        }
        return $this;
    }

    /**
     * 添加信用币纪录
     * @param $type
     */
    public function add($type,$price)
    {
        $this->setType($type);
        if ($type == 2) {
            $this->startTrans();
            try {
            	//信用币保存
            	$this->query("UPDATE snake_user SET credit_money=credit_money-{$price} WHERE id={$this->user_id}");
               // $this->query("UPDATE snake_user SET credit_money=0 WHERE id={$this->user_id}");
                $this->save();
                $this->commit();
            } catch (\Exception $e) {
                $this->rollback();
                return $e->getMessage();
            }
            return true;
        } else {
            $this->save();
        }
    }

    /**
     * 支付信用币（信息）
     * @param $payUserId 支付信用币用户
     * @param $loanInfo 贷款信息
     * @param $type 支付类型 ("101":"浏览费","102":"保证金")
     * @return bool|string
     */
    public function payLoanInfo($payUserId, $loanInfo, $type)
    {
        switch ($type) {
            case '101':
                $payType = '支付信息浏览费';
                $collectType = '收取信息浏览费';
                $title = '信息浏览费';
                $money = Setting::getConfigValue(1, 'browse_fee');
                break;
            case '102':
                return $this->payLoanInfo2($payUserId, $loanInfo);
                break;
            default:
                abort(500, '未知支付类型');
                break;
        }

        //判断是否是用户自己的信息
        if ($payUserId == $loanInfo->user_id) abort(500, '不能支付自己发布的信息！');

        //用户可用信用币
        $payUserCreditMoney = User::where('id', $payUserId)->value('credit_money');
        if ($payUserCreditMoney < $money) abort(500, '信用币不足，请充值！');

        //开启事务
        $this->startTrans();
        try {
            //信用币纪录保存
            $list = array(
                //支付信用币纪录
                array(
                    'user_id' => $payUserId,
                    'money' => -$money,
                    'title' => $title,
                    'loan_id' => $loanInfo->id,
                    'type' => $payType,
                    'status' => '正常'
                ),
                //获取信用币纪录
                array(
                    'user_id' => $loanInfo->user_id,
                    'money' => $money,
                    'title' => $title,
                    'loan_id' => $loanInfo->id,
                    'type' => $collectType,
                    'status' => '正常'
                )
            );
            $this->saveAll($list);

            //信用币保存
            $this->query("UPDATE snake_user SET credit_money=credit_money-{$money} WHERE id={$payUserId}");//扣除信用币
            $this->query("UPDATE snake_user SET credit_money=credit_money+{$money} WHERE id={$loanInfo->user_id}");//增加信用币

            if ($payType == '支付信息浏览费') {
                //记录浏览用户id
                $this->query("UPDATE snake_loan_info set pay_view_user_ids=CONCAT_WS(',',pay_view_user_ids,{$payUserId}) WHERE id={$loanInfo->id}");
            } elseif ($payType == '支付信息保证金') {
                //记录交保证金用户id
                $this->query("UPDATE snake_loan_info set pay_margin_user_id={$payUserId},status='已交保证金' WHERE id={$loanInfo->id}");
            }

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }

    /**支付信息保证金**/
    public function payLoanInfo2($payUserId, $loanInfo)
    {
        $money = $loanInfo->credit_money;

        //判断是否是用户自己的信息
        if ($payUserId == $loanInfo->user_id) abort(500, '不能支付自己发布的信息！');

        //用户可用信用币
        $payUserCreditMoney = User::where('id', $payUserId)->value('credit_money');
        if ($payUserCreditMoney < $money) abort(500, '信用币不足，请充值！');

        //开启事务
        $this->startTrans();
        try {
            //信用币纪录保存
            $list = array(
                //支付信用币纪录
                array(
                    'user_id' => $payUserId,
                    'money' => -$money,
                    'title' => '保证金',
                    'loan_id' => $loanInfo->id,
                    'type' => '支付信息保证金',
                    'status' => '正常'
                )
            );
            $this->saveAll($list);

            //信用币保存
            if($money){
            	$this->query("UPDATE snake_user SET credit_money=credit_money-{$money} WHERE id={$payUserId}");//扣除信用币
            }
            //记录交保证金用户id
            $this->query("UPDATE snake_loan_info set pay_margin_user_id={$payUserId},status='已交保证金' WHERE id={$loanInfo->id}");

            $data = array(
                'o_type' => 'loan',
                'o_id' => $loanInfo->id,
                'money' => $money,
                'create_time' => nowTime(),
                'unfrozen_time' => strtotime('+3 day'),
                'finish_status' => 0,
                'user_id' => $payUserId
            );
            $this->table('snake_frozen')->insert($data);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }

    /**
     * 支付信用币（典当）
     * @param $payUserId 支付信用币用户
     * @param $pawnInfo 典当信息
     * @param $type 支付类型 ("101":"浏览费","102":"押金")
     * @return bool|string
     */
    public function payPawnInfo($payUserId, $pawnInfo, $type)
    {
        switch ($type) {
            case '101':
                $payType = '支付典当浏览费';
                $collectType = '收取典当浏览费';
                $title = '典当信息浏览费';
                $money = Setting::getConfigValue(3, 'pawn_browse_fee');
                break;
            case '102':
                return $this->payPawnInfo2($payUserId, $pawnInfo);
                break;
            default:
                abort(500, '未知支付类型');
                break;
        }

        //判断是否是用户自己的信息
        if ($payUserId == $pawnInfo->user_id) abort(500, '不能支付自己发布的信息！');

        //用户可用信用币
        $payUserCreditMoney = User::where('id', $payUserId)->value('credit_money');
        if ($payUserCreditMoney < $money) abort(500, '信用币不足，请充值！');

        //开启事务
        $this->startTrans();
        try {
            //信用币纪录保存
            $list = array(
                //支付信用币纪录
                array(
                    'user_id' => $payUserId,
                    'money' => -$money,
                    'title' => $title,
                    'pawn_id' => $pawnInfo->id,
                    'type' => $payType,
                    'status' => '正常'
                ),
                //获取信用币纪录
                array(
                    'user_id' => $pawnInfo->user_id,
                    'money' => $money,
                    'title' => $title,
                    'pawn_id' => $pawnInfo->id,
                    'type' => $collectType,
                    'status' => '正常'
                )
            );
            $this->saveAll($list);

            //信用币保存
            $this->query("UPDATE snake_user SET credit_money=credit_money-{$money} WHERE id={$payUserId}");//扣除信用币
            $this->query("UPDATE snake_user SET credit_money=credit_money+{$money} WHERE id={$pawnInfo->user_id}");//增加信用币

            if ($payType == '支付典当浏览费') {
                //记录浏览用户id
                $this->query("UPDATE snake_pawn_info set pay_view_user_ids=CONCAT_WS(',',pay_view_user_ids,{$payUserId}) WHERE id={$pawnInfo->id}");
            } elseif ($payType == '支付典当押金') {
                //记录押金用户id
                $this->query("UPDATE snake_pawn_info set pay_margin_user_id={$payUserId},status='已成交' WHERE id={$pawnInfo->id}");
            }

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }

    /**支付典当押金**/
    public function payPawnInfo2($payUserId, $pawnInfo)
    {
        $money = $pawnInfo->sale_price * 0.1;

        //判断是否是用户自己的信息
        if ($payUserId == $pawnInfo->user_id) abort(500, '不能支付自己发布的信息！');

        //用户可用信用币
        $payUserCreditMoney = User::where('id', $payUserId)->value('credit_money');
        if ($payUserCreditMoney < $money) abort(500, '信用币不足，请充值！');

        //开启事务
        $this->startTrans();
        try {
            //信用币纪录保存
            $list = array(
                //支付信用币纪录
                array(
                    'user_id' => $payUserId,
                    'money' => -$money,
                    'title' => '押金',
                    'pawn_id' => $pawnInfo->id,
                    'type' => '支付典当押金',
                    'status' => '正常'
                )
            );
            $this->saveAll($list);

            //信用币保存
            $this->query("UPDATE snake_user SET credit_money=credit_money-{$money} WHERE id={$payUserId}");//扣除信用币

            //记录押金用户id
            $this->query("UPDATE snake_pawn_info set pay_margin_user_id={$payUserId},status='已成交' WHERE id={$pawnInfo->id}");

            $data = array(
                'o_type' => 'pawn',
                'o_id' => $pawnInfo->id,
                'money' => $money,
                'create_time' => nowTime(),
                'unfrozen_time' => strtotime('+3 day'),
                'finish_status' => 0,
                'user_id' => $payUserId
            );
            $this->table('snake_frozen')->insert($data);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }

    /**贷款信息退保证金**/
    public function loanBackMoney($loanInfo)
    {
        //开启事务
        $this->startTrans();
        try {
            $frozen = Frozen::get(['o_id' => $loanInfo->id, 'o_type' => 'loan', 'user_id' => $loanInfo->pay_margin_user_id, 'finish_status' => 0]);
            $frozen->finish_status = 1;
            $frozen->save();

            $loanInfo->apply_refund = 0;
            $loanInfo->status = '已上架';
            $loanInfo->pay_margin_user_id = '';
            $loanInfo->save();

            $data = array(
                'user_id' => $frozen->user_id,
                'money' => $frozen->money,
                'title' => '退还信息保证金',
                'loan_id' => $frozen->o_id,
                'type' => '退还信息保证金',
                'status' => '正常',
                'create_time' => nowTime()
            );
            $this->table('snake_credit_record')->insert($data);

            $this->query("UPDATE snake_user SET credit_money=credit_money+{$frozen->money} WHERE id={$frozen->user_id}");//退回信用币

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }

    /**典当信息退押金**/
    public function pawnBackMoney($pawnInfo)
    {
        //开启事务
        $this->startTrans();
        try {
            $frozen = Frozen::get(['o_id' => $pawnInfo->id, 'o_type' => 'pawn', 'user_id' => $pawnInfo->pay_margin_user_id, 'finish_status' => 0]);
            $frozen->finish_status = 1;
            $frozen->save();

            $pawnInfo->apply_refund = 0;
            $pawnInfo->status = '交易中';
            $pawnInfo->pay_margin_user_id = '';
            $pawnInfo->save();

            $data = array(
                'user_id' => $frozen->user_id,
                'money' => $frozen->money,
                'title' => '退还典当押金',
                'pawn_id' => $frozen->o_id,
                'type' => '退还典当押金',
                'status' => '正常',
                'create_time' => nowTime()
            );
            $this->table('snake_credit_record')->insert($data);

            $this->query("UPDATE snake_user SET credit_money=credit_money+{$frozen->money} WHERE id={$frozen->user_id}");//退回信用币

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }

    /**
     * 充值支付成功处理
     * @return string
     */
    public function recharge()
    {
        //开启事务
        $this->startTrans();
        try {
            $this->status = '正常';
            $this->save();

            $this->query("UPDATE snake_user SET credit_money=credit_money+{$this->money} WHERE id={$this->user_id}");//增加信用币

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }

    public function createOrderId()
    {
        return 'recharge_' . getRandString(15);
    }

    public function collectionSub()
    {
        $this->startTrans();
        try {
            $result = $this->daifuQuery($this->mer_date, $this->mer_seq_id);
            if ($result === true) {
                $this->status = '正常';
                $this->save();
                $this->commit();
                return 1;
            } elseif ($result == '银联：银行已退单,失败') {
                //重置银联提交状态
                $this->mer_state = 0;
                $this->save();
                $this->commit();
                return 2;
            } else {
                abort(500, $result);
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }

    private function daifuQuery($merDate, $merSeqId)
    {
        $key = '&key=5EC798E5429B234ECB9D96B4EA72D0CA';
        $option['merDate'] = $merDate;
        $option['merSeqId'] = $merSeqId;
        ksort($option);
        $option['sign'] = md5(urldecode(http_build_query($option)) . $key);
        $option['channel'] = 'yl';
        $option['rest'] = 'query';
        $result = curlPost('http://pay.sqstz360.com/api/pay', http_build_query($option));
        $result = json_decode($result, true);
        if ($result['code'] == '200' && $result['data']['code'] == '000') {
            if ($result['data']['stat_info'] == '代付成功') {
                return true;
            } else {
                return $result['data']['stat_info'];
            }
        } else {
            return $result['message'];
        }
    }

    public function daifu()
    {
        $this->startTrans();
        try {
            $authInfo = $this->User->AuthInfo;
            $bank_code = BankCode::where('bank_code', $authInfo['bank_code'])->find();
            if (!$bank_code) {
                abort(500, '联行号错误');
            }

            $order_sn = (time() . rand(100000, 999999));

            $this->mer_date = date('Ymd');
            $this->mer_seq_id = $order_sn;
            $this->mer_state = 1;
            $this->save();

            $key = '&key=5EC798E5429B234ECB9D96B4EA72D0CA';

            $option['name'] = $authInfo['name'];
            $option['bank_card_number'] = $authInfo['bank_card_number'];
            $option['bank_name'] = $authInfo['bank_name'];
            $option['amount'] = (intval(abs($this->money) * 100));
            $option['order_sn'] = $order_sn;
            $option['province'] = $bank_code['province'];
            $option['city'] = $bank_code['city'];
            ksort($option);
            $option['sign'] = md5(urldecode(http_build_query($option)) . $key);
            $option['channel'] = 'yl';
            $option['rest'] = 'daipay';
            $result = curlPost('http://pay.sqstz360.com/api/pay', http_build_query($option));
            $result = json_decode($result, true);
            $state = array('s', '2', '3', '4', '5', '7', '8');
            if ($result['code'] == '200' && in_array($result['data']['stat'], $state)) {
                $this->commit();
                return true;
            } else {
                abort(500, $result['message']);
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }
    /**
     * 支付信用币（典当）
     * @param $userId 信用币用户
     * @param $money 充值金额
     * @param $company_id 公司id
     * @return bool|string
     */
    public function companyRecharge($userId, $money, $company_id)
    {    	
    	//开启事务
    	$this->startTrans();
    	$company=AdminCompany::get($company_id);
    	try {
    		//信用币纪录保存
    		$list = array(
    						'user_id' => $userId,
    						'money' => "+".$money,
    						'title' => $company->company_name."公司充值",
    						'company_id' => $company_id,
    						'type' => "公司充值",
    						'status' => '正常'
    				);

    		$this->save($list);
    		//信用币保存
    		$this->query("UPDATE snake_admin_company SET give_credit_money=give_credit_money-{$money} WHERE id={$company_id}");//扣除信用币
    		$this->query("UPDATE snake_user SET credit_money=credit_money+{$money} WHERE id={$userId}");//增加信用币    		
    		$this->commit();
    	} catch (\Exception $e) {
    		$this->rollback();
    		return $e->getMessage();
    	}
    	return true;
    }
    /**
     * 支付信用币（典当）
     * @param $payUserId 支付信用币用户
     * @param $pawnInfo 典当信息
     * @return bool|string
     */
    public function reject($id)
    {
    	//开启事务
    	$this->startTrans();
    	try {
    		$this->get($id);
    		$this->title='提现驳回';
    		$this->status='审核中';
    		$this->save();
    		$money=abs($this->money);
    		//信用币纪录保存
    		$list = array(
    				'user_id' => $this->user_id,
    				'money' => $money,
    				'title' => "提现驳回",
    				'type' => "提现驳回",
					'create_time'=>time(),
    				'status' => '正常'
    		);
    		$this->insert($list);
    		//信用币保存
    		$this->query("UPDATE snake_user SET credit_money=credit_money+{$money} WHERE id={$this->user_id}");//增加信用币
    		$this->commit();
    		return true;
    	} catch (\Exception $e) {
    		$this->rollback();
    		return $e->getMessage();
    	}
    }
    
    
    
    
    
    
    public function sharePawnReturn($pawn, $userId)
    {
        if (!$this->where('pawn_id', $pawn->id)->where('user_id', $userId)->where('type', '典当分享返还')->count()) {
            $money = floatval(Setting::getConfigValue(3, 'pawn_browse_fee'));
            //开启事务
            $this->startTrans();
            try {
                $data = array(
                    'user_id' => $userId,
                    'money' => $money,
                    'title' => '分享返还',
                    'create_time' => time(),
                    'pawn_id' => $pawn->id,
                    'type' => '典当分享返还',
                    'status' => '正常'
                );
                $this->insert($data);

                $this->query("UPDATE snake_user SET credit_money=credit_money+{$money} WHERE id={$userId}");//增加信用币

                $this->commit();
            } catch (\Exception $e) {
                $this->rollback();
                return $e->getMessage();
            }
        }
        return true;
    }
}
