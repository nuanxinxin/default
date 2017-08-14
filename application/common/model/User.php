<?php

namespace app\common\model;

use think\Model;

//用户表
class User extends Model
{
    public function AdminCompany()
    {
        return $this->belongsTo('AdminCompany', 'company_id', 'id');
    }

    public function AuthInfo()
    {
        return $this->hasOne('AuthInfo', 'user_id', 'id');
    }

    public function CreditRecord()
    {
        return $this->hasMany('CreditRecord', 'user_id', 'id');
    }

    public function ToolRecord()
    {
        return $this->hasMany('ToolRecord', 'user_id', 'id');
    }

    /**
     * 事件注册
     */
    protected static function init()
    {
        self::event('before_insert', function ($model) {
            //验证手机号唯一性
            isset($model->phone) && $model->checkPhoneUnique($model->phone,$model->source);
			/* isset($model->password)&&$model->checkPasswordLength($model->password); */
            //注册时间
            $model->register_time = nowTime();
            $model->credit_money = 0;
            $model->current_tool_money = 0;
        });

        self::event('before_update', function ($model) {
        	/* isset($model->password)&&$model->checkPasswordLength($model->password); */
        });
    }
	
    /**
     * 手机号唯一性验证
     * @param string $username
     */
    private function checkPhoneUnique($phone = '', $source='wx')
    {
    	$id=0;
    	if ($_id = $this->where('phone', $phone)->where('source',$source)->value('id')) {
        	
            if (!$id || ($id > 0 && $_id != $id)) {
                abort(500, '手机号不可用');
            }
        }
    }
    private function checkPasswordLength($password){
    	if(strlen($password)<6){
    		abort(500, '密码小于6位');
    	}else{
    		$this->password=md5($password);
    	}
    	
    }
    /**
     * 添加新用户
     * @return bool|string
     */
    public function addUser()
    {
        //开启事务
        $this->startTrans();
        try {
            //用户数据保存
            $this->allowField(true)->save();
            $this->identifier = md5($this->id . nowTime());
            $this->allowField(true)->save();

            //推广关系保存
            $descendantId = $this->id;
            $ancestorId = $this->parent_id;
            $this->query("INSERT INTO snake_distribution_tree(ancestor_id,descendant_id,path_length) SELECT t.ancestor_id,{$descendantId},t.path_length+1 FROM snake_distribution_tree AS t WHERE t.descendant_id = {$ancestorId} UNION ALL SELECT {$descendantId},{$descendantId},0");

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }
    
    
    
    
    
    /**
     * userIdByIdentifier
     * @param $userIdentifier
     * @return int|mixed
     */
    public static function userIdByIdentifier($userIdentifier)
    {
        $id = self::where('identifier', $userIdentifier)->value('id');
        return $id ? $id : 0;
    }

    /**
     * userIdById
     * @param $id
     * @return int|mixed
     */
    public static function userIdById($id)
    {
        $identifier = self::where('id', $id)->value('identifier');
        return $identifier ? $identifier : 0;
    }

    /**
     * 微信openid登录
     * @param $wx
     * @return static
     */
    public static function loginByWx($data)
    {
        $model = self::get(array('wx' => $data['wx']));
        if (!$model) {
            return false;
        } else {
            if ($data['city'] != '') {
                $model->city = $data['city'];
                $model->save();
            }
            return $model;
        }
    }

    /**推广列表**/
    public function myPartners()
    {
        $data = array('auth' => array(), 'unAuth' => array());
        $result = $this->view('User', 'identifier,nickname,thumb')->view('AuthInfo', 'auth_status', 'User.id=AuthInfo.user_id', 'LEFT')->where('User.parent_id', $this->id)->order('User.id desc')->select();
        foreach ($result as $item) {
            if ($item->auth_status == '已通过') {
                $data['auth'][] = array(
                    'nickname' => $item->nickname,
                    'thumb' => $item->thumb,
                    'userId' => $item->identifier
                );
            } else {
                $data['unAuth'][] = array(
                    'nickname' => $item->nickname,
                    'thumb' => $item->thumb,
                    'userId' => $item->identifier
                );
            }
        }
        return $data;
    }
}
