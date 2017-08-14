<?php

namespace app\admin\controller;

use app\common\model\LuckyGoodsSpoilRecord;
use app\common\model\User;
use app\common\model\AdminCompany;
use app\common\model\LuckyGoods;
use app\common\model\LuckyGoodsSpoil;
use think\Db;
use app\common\model\LuckyDaySettlement;
use app\common\model\LuckyXydj;
use app\common\model\Setting;
use app\common\model\LuckyGoodsClass;

/**幸运抽奖**/
class LuckDraw extends AdminBase
{
    public function _initialize()
    {
        parent::_initialize();
        define('COMPANY_ID', session('company.id'));
    }

    /**奖品**/
    public function prizeGoods()
    {
        $list = LuckyGoodsSpoil::where('company_id', COMPANY_ID)->order('spoil_id desc')->paginate(30);
        $this->assign('list', $list);
        return $this->fetch('prize_goods');
    }
    
    
    /**
     * 奖品分类
     */
    public function goodsClass(){
    	
    	$list = LuckyGoodsClass::where()->order('id desc')->paginate(10);
    	
    	$this->assign('list', $list);
    	return $this->fetch('goods_class');
    	
    }
    /**
     * 奖品分类
     */
    public function goodsClassAdd(){
    	if (isPost()) {
    		try {
    			$model = new LuckyGoodsClass;
    			$model->saveGoodsClass($this->request->param());
    		} catch (\Exception $e) {
    			$this->error($e->getMessage());
    		}
    		$this->success('新增成功', path('goodsClass'));
    	} else {
    		$id=$this->request->param("class_id");
			if($id){
				$data=LuckyGoodsClass::where("id",$id)->find();
				$this->assign("data",$data);
			}
    		return $this->fetch('class_add');
    	}
    	
    }
    
    /**分类删除**/
    public function goodsClassDel()
    {
    	try {
    		$id=$this->request->param("id");
    		$model = LuckyGoodsClass::where('id', $id)->find();
    		$model->delete();
    	} catch (\Exception $e) {
    		$this->error($e->getMessage());
    	}
    	$this->success('删除成功', path('goodsClass'));
    }

    /**奖品新增**/
    public function prizeGoodsAdd()
    {
        if (isPost()) {
            try {
                $model = new LuckyGoodsSpoil;
                $model->savePrizeGoods($this->request->param());
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success('新增成功', path('prizeGoodsAdd'));
        } else {
            return $this->fetch('prize_goods_form');
        }
    }

    /**奖品编辑**/
//    public function prizeGoodsEdit($spoil_id)
//    {
//        $model = LuckyGoodsSpoil::get($spoil_id);
//        if (isPost()) {
//            try {
//                $model->savePrizeGoods($this->request->param());
//            } catch (\Exception $e) {
//                $this->error($e->getMessage());
//            }
//            $this->success('修改成功', path('prizeGoods'));
//        } else {
//            $this->assign('data', $model);
//            return $this->fetch('prize_goods_form');
//        }
//    }

    /**奖品删除**/
    public function prizeGoodsDel($spoil_id)
    {
        try {
            $model = LuckyGoodsSpoil::where('company_id', COMPANY_ID)->where('spoil_id', $spoil_id)->find();
            if (LuckyGoods::where('company_id', COMPANY_ID)->where('spoil_id|comfort_spoil_id', $spoil_id)->count() > 0) {
                abort(500, '已使用的奖品不能删除');
            } else {
                $model->delete();
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('删除成功', path('prizeGoods'));
    }

    /**活动列表**/
    public function activity()
    {
        $where = array();
        $where['company_id'] = COMPANY_ID;
        $list = LuckyGoods::where($where)->order('goods_id desc')->paginate(30);
        $this->assign('list', $list);
        return $this->fetch('activity');
    }

    /**活动新增**/
    public function activityAdd()
    {
        if (isPost()) {
            try {
                $model = new LuckyGoods;
                $model->saveLuckyGoods($this->request->param());
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success('新增成功', path('activityAdd'));
        } else {
            $prizeGoods = LuckyGoodsSpoil::where('company_id', COMPANY_ID)->where('spoil_type', 0)->order('spoil_id desc')->select();
            $comfortPrizeGoods = LuckyGoodsSpoil::where('company_id', COMPANY_ID)->where('spoil_type', 1)->order('spoil_id desc')->select();
            $this->assign('prizeGoods', $prizeGoods);
            $this->assign('comfortPrizeGoods', $comfortPrizeGoods);
            return $this->fetch('activity_form');
        }
    }

    /**活动修改**/
    public function activityEdit($goods_id)
    {
        $model = LuckyGoods::where('company_id', COMPANY_ID)->where('goods_id', $goods_id)->find();
        if (isPost()) {
            try {
                $model->saveLuckyGoods($this->request->param());
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success('修改成功', path('activity'));
        } else {
            $prizeGoods = LuckyGoodsSpoil::where('company_id', COMPANY_ID)->where('spoil_type', 0)->order('spoil_id desc')->select();
            $comfortPrizeGoods = LuckyGoodsSpoil::where('company_id', COMPANY_ID)->where('spoil_type', 1)->order('spoil_id desc')->select();
            $this->assign('prizeGoods', $prizeGoods);
            $this->assign('comfortPrizeGoods', $comfortPrizeGoods);
            $this->assign('data', $model);
            return $this->fetch('activity_form');
        }
    }

    /**复制活动**/
    public function activityCopy($goods_id = 0)
    {
        if (isPost()) {
            try {
                $model = new LuckyGoods;
                $model->saveLuckyGoods($this->request->param());
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success('复制成功', path('activity'));
        } else {
            $model = LuckyGoods::where('company_id', COMPANY_ID)->where('goods_id', $goods_id)->find();
            $prizeGoods = LuckyGoodsSpoil::where('company_id', COMPANY_ID)->where('spoil_type', 0)->order('spoil_id desc')->select();
            $comfortPrizeGoods = LuckyGoodsSpoil::where('company_id', COMPANY_ID)->where('spoil_type', 1)->order('spoil_id desc')->select();
            $model->goods_id = 0;
            $this->assign('prizeGoods', $prizeGoods);
            $this->assign('comfortPrizeGoods', $comfortPrizeGoods);
            $this->assign('data', $model);
            return $this->fetch('activity_form');
        }
    }

    /**活动删除**/
    public function activityDel($goods_id)
    {
        try {
            $model = LuckyGoods::where('company_id', COMPANY_ID)->where('goods_id', $goods_id)->find();
            if ($model->buyRecord()->count() > 0) {
                abort(500, '已有人参与的活动不能删除');
            } else {
                $model->delete();
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('删除成功', path('activity'));
    }

    /**兑奖**/
    public function duijiang()
    {
        if (isPost()) {
            try {
                $ticket_identifier = $this->request->param('ticket_identifier/s');
                LuckyGoodsSpoilRecord::where('ticket_identifier', $ticket_identifier)->where('ticket_status', 0)->update(['ticket_status' => 1]);
            } catch (\Exception $e) {
                return json(['code' => 500, 'message' => $e->getMessage()]);
            }
            return json(['code' => 200]);
        } else {
            $ticket_identifier = $this->request->param('ticket_identifier/s');
            if (!empty($ticket_identifier)) {
                $model = new LuckyGoodsSpoilRecord;
                $data = $model->spoilDetail($ticket_identifier);
                if (!$data) {
                    $data = false;
                }
                $this->assign('data', $data);
            }
            return $this->fetch('duijiang');
        }
    }

    /**中奖纪录**/
    public function spoilRecord()
    {
        $model = new LuckyGoodsSpoilRecord;
        $list = $model->company_spoil_list();
        $this->assign('list', $list);
        return $this->fetch('spoil_record');
    }

    /**幸运兑奖**/
    public function xydj()
    {
        if (isPost()) {
            try {
                $ticket_identifier = $this->request->param('ticket_identifier/s');
                LuckyXydj::where('ticket_identifier', $ticket_identifier)->where('ticket_status', 0)->update(['ticket_status' => 1]);
            } catch (\Exception $e) {
                return json(['code' => 500, 'message' => $e->getMessage()]);
            }
            return json(['code' => 200]);
        } else {
            $ticket_identifier = $this->request->param('ticket_identifier/s');
            if (!empty($ticket_identifier)) {
                $model = new LuckyXydj;
                $data = $model->spoilDetail($ticket_identifier);
                if (!$data) {
                    $data = false;
                }
                $this->assign('data', $data);
            }
            return $this->fetch('xydj');
        }
    }

    /**设置**/
    public function setting()
    {
        if (isPost()) {
            AdminCompany::where('id', COMPANY_ID)->setField('xycj_conditions_buy_count', $this->request->param('xycj_conditions_buy_count/d'));
            $this->success('设置成功');
        } else {
            $this->assign('data', AdminCompany::get(COMPANY_ID));
            return $this->fetch('setting');
        }
    }

    /**活动失败列表**/
    public function activityFailed()
    {
        return $this->fetch('failed');
    }

    /**商户结算**/
    public function merchantSettlement()
    {
        if (LOGIN_TYPE != 'admin') {
            $this->error('非法操作');
        }
        $model = new LuckyDaySettlement;
        $list = $model->settlement();
        $this->assign('list', $list);
        return $this->fetch('settlement');
    }

    /**每日结算**/
    public function settlement()
    {
        $model = new LuckyDaySettlement;
        $list = $model->settlement(COMPANY_ID);
        $this->assign('list', $list);
        return $this->fetch('settlement_company');
    }

    /**推荐**/
    public function tuijian($goods_title = '')
    {
        if (LOGIN_TYPE != 'admin') {
            $this->error('非法操作');
        }
        if ($this->request->isAjax()) {
            try {
                LuckyGoods::where('goods_id', $this->request->param('goods_id/d'))->setField('is_recommend', $this->request->param('is_recommend/d'));
                return json(['code' => 200]);
            } catch (\Exception $e) {
                return json(['code' => 500, 'message' => $e->getMessage()]);
            }
        }
        if (!empty($goods_title)) {
            $search = LuckyGoods::where('goods_title', 'like', '%' . $goods_title . '%')->where('is_out', 0)->order('goods_id desc')->select();
            $this->assign('search', $search);
        }
        return $this->fetch('tuijian');
    }

    /**公告**/
    public function notify(){
        if (isPost()) {
            $data = $this->request->except('__token__', 'post');
            $model = new Setting;
            foreach ($data as $index => $item) {
                $model->clearCache(5)->where('name', $index)->setField('value', $item);
            }
            $this->success('设置成功');
        } else {
            $this->assign('data', Setting::getConfig(5));
            return $this->fetch();
        }
    }

}