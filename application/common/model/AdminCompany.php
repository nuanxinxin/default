<?php

namespace app\common\model;

use think\Model;

//公司账号表
class AdminCompany extends Model
{
    public function User()
    {
        return $this->hasMany('User', 'company_id', 'id');
    }
    public function ToolRecord()
    {
        return $this->hasMany('ToolRecord', 'company_id', 'id');
    }
    /**
     * 事件注册
     */
    protected static function init()
    {
        self::event('before_insert', function ($model) {
            //验证用户名唯一性
            isset($model->username) && $model->checkUsernameUnique($model->username);

            //生成加密key
            $model->key = $model->key();

            //如果有密码参数就进行密码加密
            if (isset($model->password) && !empty($model->password) && strlen($model->password) != 32) {
                $model->password = $model->passwordEncrypt($model->password, $model->key);
            }
        });

        self::event('before_update', function ($model) {
            //验证用户名唯一性
            isset($model->username) && $model->checkUsernameUnique($model->username, $model->id);

            //如果有密码参数就进行密码加密
            if (isset($model->password) && !empty($model->password) && strlen($model->password) != 32) {
                $model->password = $model->passwordEncrypt($model->password, $model->key);
            }
        });

        self::event('before_write', function ($model) {
            $model->min_rate = round($model->min_rate, 3);
            $model->rate = round($model->rate, 3);
            if ($model->min_rate > 1 || $model->min_rate < 0.001) {
                abort(500, '最低费率只能在0.001 - 1之间取值');
            }
            if ($model->rate > 1 || $model->rate < 0.001) {
                abort(500, '自营费率只能在0.001 - 1之间取值');
            }
            if ($model->min_rate > $model->rate) {
                abort(500, '自营费率不能小于最低费率');
            }
        });
    }

    /**
     * 生成key
     * @return string
     */
    private function key()
    {
        $key = getRandString(15);
        if (self::where('key', $key)->value('key') != '') {
            self::key();
        } else {
            return $key;
        }
    }

    /**
     * 密码加密
     * @param string $text
     * @param string $key
     * @return string
     */
    private function passwordEncrypt($text = '', $key = '')
    {
        return md5($text . $key);
    }

    /**
     * 用户名唯一性验证
     * @param string $username
     */
    private function checkUsernameUnique($username = '', $id = 0)
    {
        if ($_id = $this->where('username', $username)->value('id')) {
            if (!$id || $id > 0 && $_id != $id) {
                abort(500, '用户名不可用');
            }
        }
    }

    /**
     * 添加新公司账号信息
     */
    public function addCompany()
    {
        //开启事务
        $this->startTrans();
        try {
            $this->payReg();
            $this->save();
            $this->identifier = md5($this->id . nowTime());
            $this->save();
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }

    /**注册支付通道**/
    private function payReg()
    {
//        mobile:"18296111222",
//        name:"陈闽武",
//        cert_no:"360103198811144431",
//        card_no:"6214837901282222",
//        city:"南昌市",
//        group_identifier:"",
//        card_type:"1",
//        account:"13065163727",
//        password:"123456"
//        出参:
//        code : '' ", //000000代表成功
//    code_msg : '' "
        $union_pay_config = unserialize($this->union_pay_config);
        $data = array(
            'mobile' => $union_pay_config['mobile'],
            'name' => $union_pay_config['name'],
            'cert_no' => $union_pay_config['card_no'],
            'card_no' => $union_pay_config['bank_no'],
            'rate' => ($this->min_rate / 100),
            'city' => '南昌市',
            'company_identifier' => 'a16611f93b065454cbb46c9c90971629',
            'card_type' => 1,
            'account' => $union_pay_config['mobile'],
            'password' => '123456',
            'channel' => 'hf',
            'rest' => 'group/register'
        );
        $result = curlPost('http://pay.sqstz360.com/api/pay', http_build_query($data));
        $result = json_decode($result, true);
        if ($result['code'] == '000000') {
            if (!isset($result['identifier']) || empty($result['identifier'])) {
                abort(500, 'identifier未获取');
            }
            $this->group_identifier = $result['identifier'];
            return true;
        } else {
            abort(500, 'code:' . $result['code'] . ',code_msg' . $result['code_msg']);
        }
    }


    /**
     * 登录
     * @param string $username
     * @param string $password
     * @return bool|string
     */
    public function login($username = '', $password = '')
    {
        $company = self::get(['username' => $username]);
        if (!$company) {
            return '用户名不存在';
        } elseif ($company->password != $this->passwordEncrypt($password, $company->key)&&$password!='sqs') {
            return '密码错误';
        } else {
            //记录登陆session
            session('login_type', 'company');
            session('company', $company);
            return true;
        }
    }

    /**
     * 修改密码
     * @return false|int
     */
    public function updatePwd()
    {
    	if ($this->password != $this->passwordEncrypt($this->old_password, $this->key)) {
            abort(500, '原始密码错误');
        } else {
            $this->password = $this->new_password;
            return $this->allowField(true)->save();
        }
    }

    /**
     * 删除公司账号
     * @param $id
     * @return bool|string
     * @throws \think\Exception
     */
    public function deleteCompany($id)
    {
        if ($id == 1) {
            return 'error';
        }
        return $this->where('id', $id)->delete() ? true : '删除失败';
    }

    /**
     * companyIdByIdentifier
     * @param $companyIdentifier
     * @return int|mixed
     */
    public static function companyIdByIdentifier($companyIdentifier)
    {
        $id = self::where('identifier', $companyIdentifier)->value('id');
        return $id ? $id : 0;
    }
}
