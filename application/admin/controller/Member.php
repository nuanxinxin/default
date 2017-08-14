<?php

namespace app\admin\controller;

use app\common\model\User;
use app\common\model\AuthInfo;
use org\PrivateImage;
use app\common\model\CreditRecord;
use app\common\model\AdminCompany;

class Member extends AdminBase
{
    public function _initialize()
    {
        parent::_initialize();
        if (LOGIN_TYPE != 'admin') {
            $this->error('非法操作');
        }
    }

    /**
     * 用户列表
     * @return mixed
     */
    public function index($keyword = '', $auth_status = '', $company_id = 0)
    {
        $where = array();
        if ($company_id > 0) {
            $where['company_id'] = $company_id;
        }
        if ($auth_status != '' && $auth_status != '全部') {
            $ids = AuthInfo::where('auth_status', $auth_status)->column('user_id');
            if (empty($ids)) {
                $ids = 0;
            }
            $where['id'] = array('in', $ids);
        }
        $list = User::where('phone', 'like', '%' . $keyword . '%')->where($where)->order('id desc')->paginate(10, false, ['query' => ['keyword' => $keyword, 'auth_status' => $auth_status, 'company_id' => $company_id]]);
        $this->assign('list', $list);
        $this->assign('companys', AdminCompany::all());
        return $this->fetch();
    }

    public function setBankCode($id, $bank_code)
    {
        $model = AuthInfo::get($id);
        $model->bank_code = $bank_code;
        $model->save();
        $this->success('设置成功');
    }

    public function setAuthInfo($id, $name, $value)
    {
        $model = AuthInfo::get($id);
        $model->$name = $value;
        $model->save();
        $this->success('设置成功');
    }

    /**
     * 用户信息
     * @param $id
     * @return mixed
     */
    public function user($id)
    {
        $this->assign('data', User::get($id));
        return $this->fetch();
    }

    /**
     * 认证信息
     * @param string $user_id
     * @param string $auth_status
     * @return mixed
     */
    public function auth($user_id = '', $auth_status = '')
    {
        if ($auth_status != '') {
            try {
                $model = new AuthInfo;
                $result = $model->authSuccess($user_id, $auth_status);
                if ($result !== true) {
                    abort(500, $result);
                }
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success('操作成功');
        } else {
            $data = AuthInfo::get(['user_id' => $user_id]);
            $pics = array();
            foreach (explode(',', $data->id_pics) as $pic) {
                $pics[] = PrivateImage::getImageUrl($pic, User::userIdById($data->user_id));
            }
            $data->pics = $pics;
            $bank_card_pics = array();
            foreach (explode(',', $data->bank_card_pic) as $pic) {
                $bank_card_pics[] = PrivateImage::getImageUrl($pic, User::userIdById($data->user_id));
            }
            $data->bank_card_pics = $bank_card_pics;
            $this->assign('data', $data);
            return $this->fetch();
        }
    }

    public function auth_sz($user_id = '')
    {
        try {
            $model = new AuthInfo;
            $result = $model->authSuccessSz($user_id);
            if ($result !== true) {
                abort(500, $result);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('操作成功');
    }
	public function auth_hf($user_id=''){
 		try { 
			$model = new AuthInfo;
			$result = $model->authSuccessHf2($user_id);
			if ($result !== true) {
				abort(500, $result);
			}
	 	} catch (\Exception $e) {
			$this->error($e->getMessage());
		}
		$this->success('操作成功'); 
	}
    
    
    
    
    public function auth_wlb($user_id = '')
    {
        try {
            $model = new AuthInfo;
            $result = $model->authSuccessWlb($user_id);
            if ($result !== true) {
                abort(500, $result);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('操作成功');
    }

    /**提现列表**/
    public function collection()
    {
        $list = CreditRecord::where('type', '提现')->order('id desc')->paginate(1000);
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**提现处理**/
    public function collectionSub($id)
    {
        try {
            $model = CreditRecord::get($id);
            if ($model->status != '到账中') {
                abort(500, '请勿重复处理');
            }
            $result = $model->collectionSub();
            if ($result === 1) {
                $result = '提现成功';
            } elseif ($result === 2) {
                $result = '银联：银行已退单,交易失败，已重置重新提交银联代付';
            } else {
                abort(500, $result);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success($result);
    }
    
    /**驳回提现申请**/
    public function reject($id)
    {
    	try {
    		$model = CreditRecord::get($id);
    		if ($model->title == '提现驳回') {
    			abort(500, '请勿重复处理');
    		}
    		$result = $model->reject($id);
    		if($result===true){
    			$templateId = 'BuJgrXmpVP2ymSBuFonas935NeFGtlYW-zip0CIe6EI';
    			$data = array(
    					"first" => "驳回申请",
    					"keyword1" => "驳回提现操作",
    					"keyword2" =>"系统管理员",
    					"keyword3" => date("Y-m-d H:i:s"),
    					"remark" => "提现申请".abs($model->money)."金额已返回到你的账户,请查收",
    			);
    			sendTemplateMsg($model->User->wx, $data, $templateId); 
    			
    		}else{
    			abort(500, $result);
    			exit();
    		}
    	} catch (\Exception $e) {
    		$this->error($e->getMessage());
    	}
    	$this->success("驳回成功");
    	exit();
    	
    }
    

    /**银联代付交易**/
    public function daifu($id)
    {
        try {
            $model = CreditRecord::get($id);
            if ($model->mer_state == 1 || $model->status == '正常') {
                abort(500, '请勿重复提交');
            }
            $result = $model->daifu();
            if ($result !== true) {
                abort(500, $result);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('代付已提交银联处理');
    }
}
