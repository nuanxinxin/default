<?php

namespace org;

use think\Exception;

/**支付类**/
class Pay
{
    /**商户编号**/
    const PARTNER = '2003';
    /**支付密钥**/
    const PAY_KEY = 'BF2BECF274DBA5501AA0CC7A2A33E672';
    /**收款人信息**/
    protected $PayeeInfoData = array();
    /**服务器域名**/
    protected $domain = '';

    protected $paymemt_ = '';

    public function __construct($PayeeInfo)
    {
        /**合并收款人信息**/
        if (isset($PayeeInfo) && !empty($PayeeInfo) && is_array($PayeeInfo)) {
            if ($PayeeInfo['pay_type'] == '121') {
                $PayeeInfo['pay_type'] = 'ALIPAY_QRCODE_PAY';
                $paymemt_ = '支付宝';
            } elseif ($PayeeInfo['pay_type'] == '120') {
                $PayeeInfo['pay_type'] = 'WECHAT_QRCODE_PAY';
                $paymemt_ = '微信';
            } elseif ($PayeeInfo['pay_type'] == '122') {
                $PayeeInfo['pay_type'] = 'JD_QRCODE_PAY';
                $paymemt_ = '京东';
            }
            $this->PayeeInfoData = array_merge($this->_PayeeInfo(), $PayeeInfo);
        } else {
            $this->PayeeInfoData = $this->_PayeeInfo();
        }
        $this->_setDomain();
    }

    /**设置服务器域名**/
    private function _setDomain()
    {
        $this->domain = request()->instance()->domain();
    }

    /**
     * 收款人默认信息
     * @return array
     */
    private function _PayeeInfo()
    {
        $data = array(
            'mobile' => '18296111222',//手机号
            'bank_no' => '6214837901282222',//银行卡号
            'bank_name' => '招商银行',//开户行
            'bank_sub' => '招商银行股份有限公司南昌青山湖支行',
            'bank_code' => '308421022098',//联行号
            'card_no' => '360103198811144450',//身份证
            'name' => '陈闽武',//银行卡持有人姓名
            'pay_type' => 'ALIPAY_QRCODE_PAY',//120-微信，121支付宝
            'settlement_type' => '130',//130-T0
            'rate' => '0.3',//商户费率
        );
        return $data;
    }

    /**
     * 生成签名
     * @param array $data
     * @return string
     */
    private function _sign(array $data)
    {
        ksort($data, SORT_STRING);
        $string = urldecode(http_build_query($data)) . '&key=' . self::PAY_KEY;
        return md5($string);
    }

    //=========================================================

    /**二维码支付**/
    public function QrCodePay(array $OrderData)
    {
        $amount = $OrderData['amount'];
        $orderId = $OrderData['order_id'];
        $type = $OrderData['type'];
        $channel = isset($OrderData['channel']) ? $OrderData['channel'] : 'wlb';
        // $url = 'http://xzl.xmzzss.com/ms.php/xzleasy/qr';
        $url = 'http://pay.sqstz360.com/api/pay';
        $ToorT1 = input('T0orT1', 'T0');
        $data = array(
            'channel' => $channel,
            'rest' => 'qrcode',
            'account' => $this->PayeeInfoData['mobile'],
            'password' => "123456",

            'mobile' => $this->PayeeInfoData['mobile'],
            'desc' => '',
            'notify_url' => $this->domain . '/api/Wx/payNotify?type=' . $type . '&order_id=' . $orderId,
            'bank_no' => $this->PayeeInfoData['bank_no'],
            'bank_sub' => $this->PayeeInfoData['bank_sub'],
            'bankname' => $this->PayeeInfoData['bank_name'],
            'bank_code' => $this->PayeeInfoData['bank_code'],
            'card_no' => $this->PayeeInfoData['card_no'],
            'name' => $this->PayeeInfoData['name'],
            'orderid' => $orderId,
            'paytype' => $this->PayeeInfoData['pay_type'],
            'partner' => self::PARTNER,
//            'notify_url_cash' => $this->domain . '/api/Wx/settlementNotify?type=' . $type,
//            'settlement_type' => $this->PayeeInfoData['settlement_type'],
            'amount' => $amount * 100,
            'rate' => $this->PayeeInfoData['rate'],
            'T0orT1' => $ToorT1
        );
        if ($data['account'] == "18979112001") {
            $data['account'] = "18979112002";
        }
        $data['sign'] = $this->_sign($data);
        $result = curlPost($url, http_build_query($data));
        // $result = strchr($result, '{');
        $result = json_decode($result);
        if ($result->data->url) {
            return $result->data->url;
        } else {
//            throw new Exception('info:' . $result->code_msg . ',status:' . $result->code);
            throw new Exception('该通道不可用');
        }
    }

    /**支付异步通知**/
    public function Notify($post)
    {
//        $data = array(
//            'amount' => $post['amount'],
//            'orderid' => $post['orderid'],
//            'respDesc' => $post['respDesc'],
//            'response_code' => $post['response_code']
//        );

//        if ($post['sign'] == $this->_sign($data) && $post['response_code'] == '00') {
//            return $post['orderid'];
//        } else {
//            return false;
//        }

        if ($post['uu'] == '45e30e155de1a4a1fe506228eb870ddf') {
            return $post['order_id'];
        } else {
            return false;
        }
    }

    /**支付清算异步通知**/
    public function SettlementNotify($post)
    {
        $data = array(
            'Actualamount' => $post['Actualamount'],
            'amount' => $post['amount'],
            'message' => $post['message'],
            'orderid' => $post['orderid'],
            'response_code' => $post['response_code']
        );

        if ($post['sign'] == $this->_sign($data) && $post['response_code'] == '00') {
            return $post;
        } else {
            return false;
        }
    }

    //=========================================================
}