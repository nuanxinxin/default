<?php

namespace app\admin\controller;

use app\common\model\AuthInfo;
use app\common\model\DistributionLevel;//分销等级
use app\common\model\DistributionProfit;//分销盈利记录
use app\common\model\DistributionTree;//分销关系树
use app\common\model\Setting;

class Distribution extends AdminBase
{
    public function _initialize()
    {
        parent::_initialize();
        if (LOGIN_TYPE != 'admin') {
            $this->error('非法操作');
        }
    }

    /**
     * index
     * @return mixed
     */
    public function index()
    {
        $list = DistributionLevel::order('id asc')->paginate(100);
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * create
     * @return mixed
     */
    public function create()
    {
        if (isPost()) {
            try {
                $this->setData(new DistributionLevel)->save();
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success('添加成功');
        } else {
            return $this->fetch('form');
        }
    }

    /**
     * edit
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        if (isPost()) {
            try {
                $this->setData(DistributionLevel::get($id))->save();
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success('修改成功', cookie('referer'));
        } else {
            cookie('referer', $this->request->header('referer'));
            $this->assign('data', DistributionLevel::get($id));
            return $this->fetch('form');
        }
    }

    /**
     * 设置需要保存的数据
     * @param $model
     * @return mixed
     */
    private function setData($model)
    {
        $model->level = $this->request->param('level');
        $model->scale = $this->request->param('scale');
        return $model;
    }

    public function setting()
    {
        if (isPost()) {
            $data = $this->request->except('__token__', 'post');
            $data['auth_fee_distribution'] = implode(',', $data['auth_fee_distribution']);
            $model = new Setting;
            foreach ($data as $index => $item) {
                $model->clearCache(2)->where('name', $index)->setField('value', $item);
            }
            $this->success('设置成功');
        } else {
            $data = Setting::getConfig(2);
            $data['auth_fee_distribution'] = explode(',', $data['auth_fee_distribution']);
            $this->assign('data', $data);
            return $this->fetch();
        }
    }

    public function profit($status = '')
    {
        if ($status != '' && $status != '全部') {
            $list = DistributionProfit::where('status', $status)->order('id asc')->paginate(30, false, ['query' => ['status' => $status]]);
        } else {
            $list = DistributionProfit::order('id asc')->paginate(30, false, ['query' => ['status' => $status]]);
        }
        $this->assign('list', $list);
        //推广认证支出总金额统计
        $this->assign('totalAuthFeeDistribution', DistributionProfit::sum('money'));
        //已结算
        $this->assign('totalAuthFeeDistributionOk', DistributionProfit::where('status', '已结算')->sum('money'));
        //未结算
        $this->assign('totalAuthFeeDistributionNo', DistributionProfit::where('status', '未结算')->sum('money'));
        //待结算
        $this->assign('totalAuthFeeDistributionWait', DistributionProfit::where('status', '待结算')->sum('money'));
        return $this->fetch();
    }
}