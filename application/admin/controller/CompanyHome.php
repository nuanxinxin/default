<?php

namespace app\admin\controller;

use app\common\model\User;
use app\common\model\AdminCompany;
use app\common\model\ToolRecord;
use app\common\model\DistributionProfit;
use app\common\model\CreditRecord;
use think\Request;


class CompanyHome extends AdminBase
{
    public function _initialize()
    {
        parent::_initialize();
        //公司ID
        define('COMPANY_ID', session('company.id'));
    }

    /**
     * 公司主页
     * @return mixed
     */
    public function index()
    {
        $this->assign('company', AdminCompany::get(COMPANY_ID));
        return $this->fetch();
    }

    /**
     * 设置自营费率
     * @param $rate
     */
    public function setRate($rate)
    {
        try {
            $model = AdminCompany::get(COMPANY_ID);
            $model->rate = $rate;
            $model->save();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('设置成功');
    }

    /**
     * 用户列表
     * @param string $keyword
     * @return mixed
     */
    public function member($keyword = '')
    {
        $list = User::where('phone', 'like', '%' . $keyword . '%')->where('company_id', COMPANY_ID)->order('id desc')->paginate(10, false, ['query' => ['keyword' => $keyword]]);
        $this->assign('list', $list);
        return $this->fetch();
    }
    /**
     * 公司充值信用币
     * @return mixed
     */
    public function recharge(){
    	if($this->request->ispost()){
    		//平台赋予公司的信用币
    		$company=AdminCompany::get(COMPANY_ID);
    		$phone=$this->request->param("phone");
    		$money=$this->request->param("balance");
    		if($company->give_credit_money>=$money){
	    		$model = User::get(['phone' => $phone]);
	    		if($model->id){
	    			$CreditRecord= new CreditRecord;
	    			$result=$CreditRecord->companyRecharge($model->id,$money,COMPANY_ID);
	    			if($result === true){
	    				
	    				$templateId = 'kXan0ANJ1nkONYB2oSWDIw2J8KL47r8O14mc1hjO6GY';
	    				$data = array(
	    						"first" => "充值通知",
	    						"keyword1" => date('Y-m-d'),
	    						"keyword2" => $company->company_name."公司充值",
	    						"keyword3" => $phone,
	    						"keyword4" => $money."元",
	    						"keyword5" => bcadd($model->credit_money,$money)."元",
	    						"remark" => "充值成功",
	    				);
	    				sendTemplateMsg($model->wx, $data, $templateId);
	    				$value=array(
	    						'code'=>200,
	    						'message'=>'充值成功'
	    				);
	    			}else{
	    				$value=array(
	    						'code'=>300,
	    						'message'=>$result
	    				);
	    				
	    			}
	    		}else{
	    			$value=array(
	    				'code'=>301,	
	    				'message'=>'没有找到该用户'
	    			);
	    			
	    		}
    		}else{
    			$value=array(
    					'code'=>300,
    					'message'=>'公司所持有信用币不足'
    			);    			
    		}
    		echo json_encode($value);
    		exit();
    	}else{
    		
    		return $this->fetch("recharge_credit_money");
    		
    	}
    	
    	
    }
    public function rechargeRecord(){
    	$give_credit_money=AdminCompany::get(COMPANY_ID)->give_credit_money;
    	$baseWhere = array('company_id' => COMPANY_ID);
    	$list=CreditRecord::where($baseWhere)->order('id desc')->paginate(10);    	
    	$this->assign('list',$list);
    	$this->assign('give_credit_money',sprintf("%.2f",$give_credit_money,2));
    	return $this->fetch("recharge_record");
    	
    }
    
    
    

    //认证推广收益
    public function authIncom($status = '')
    {
        $baseWhere = array('c_id' => COMPANY_ID, 'c_type' => '公司');
        if ($status != '' && $status != '全部') {
            $list = DistributionProfit::where($baseWhere)->where('status', $status)->order('id asc')->paginate(30, false, ['query' => ['status' => $status]]);
        } else {
            $list = DistributionProfit::where($baseWhere)->order('id asc')->paginate(30);
        }
        $this->assign('list', $list);
        //推广总收益
        $this->assign('totalAuthFeeDistribution', DistributionProfit::where($baseWhere)->sum('money'));
        //已结算
        $this->assign('totalAuthFeeDistributionOk', DistributionProfit::where($baseWhere)->where('status', '已结算')->sum('money'));
        //未结算
        $this->assign('totalAuthFeeDistributionNo', DistributionProfit::where($baseWhere)->where('status', '未结算')->sum('money'));
        //待结算
        $this->assign('totalAuthFeeDistributionWait', DistributionProfit::where($baseWhere)->where('status', '待结算')->sum('money'));
        return $this->fetch('auth_incom');
    }

    //结算
    public function balance()
    {
        $where = array(
            'c_id' => COMPANY_ID,
            'c_type' => '公司',
            'status' => '未结算'
        );
        if (DistributionProfit::where($where)->update(['status' => '待结算'])) {
            $this->success('等待客服处理。');
        } else {
            $this->error('结算失败');
        }
    }

    //交易记录
    public function toolRecord($keyword = '')
    {
        if ($keyword != '') {
            $user_id = User::where('phone', $keyword)->value('id');
            $list = ToolRecord::where('company_id', COMPANY_ID)->where('user_id', $user_id)->order('id desc')->paginate(10, false, ['query' => ['keyword' => $keyword]]);
        } else {
            $list = ToolRecord::where('company_id', COMPANY_ID)->order('id desc')->paginate(10, false, ['query' => ['keyword' => $keyword]]);
        }
        $this->assign('list', $list);
        return $this->fetch('tool_record');
    }
}