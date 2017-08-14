<?php
namespace app\admin\controller;

use app\common\model\CreditRecord;
use app\common\model\LoanInfo;
use app\common\model\LoanInfoCar;
use app\common\model\LoanInfoHouse;
use app\common\model\Setting;

class Loan extends AdminBase
{
    public function _initialize()
    {
        parent::_initialize();
        if (LOGIN_TYPE != 'admin') {
            $this->error('非法操作');
        }
    }

    public function index($keyword = '', $status = '', $apply_refund = '')
    {
        $where = [];
        if ($status != '' && $status != '全部') {
            $where['status'] = $status;
        }

        if ($apply_refund) {
            $where['apply_refund'] = 1;
        }

        $list = LoanInfo::where('name|phone', 'like', '%' . $keyword . '%')->where($where)->order('id desc')->paginate(10, false, ['query' => ['keyword' => $keyword, 'status' => $status, 'apply_refund' => $apply_refund]]);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function detail($id)
    {
        $loanInfo = new LoanInfo;
        $this->assign('data', $loanInfo->loanInfoDetail(['id' => $id]));
        return $this->fetch();
    }

    public function refund($id)
    {
        $loanInfo = LoanInfo::get($id);
        $model = new CreditRecord;
        $result = $model->loanBackMoney($loanInfo);
        if ($result === true) {
            $this->success('操作成功');
        } else {
            $this->error($result);
        }
    }

    public function statusSuccess($id)
    {
        $loanInfo = LoanInfo::get($id);
        $loanInfo->status = '已上架';
        $loanInfo->agent_phone = $this->request->param('agent_phone');
        $loanInfo->credit_money = $this->request->param('credit_money');
        $loanInfo->save();
        $this->success('操作成功');
    }

    public function statusFailed($id)
    {
        $loanInfo = LoanInfo::get($id);
        $loanInfo->status = '未通过';
        $loanInfo->save();
        $this->success('操作成功');
    }

    public function setting()
    {
        if (isPost()) {
            $data = $this->request->except('__token__', 'post');
            $model = new Setting;
            foreach ($data as $index => $item) {
                $model->clearCache(1)->where('name', $index)->setField('value', $item);
            }
            $this->success('设置成功');
        } else {
            $this->assign('data', Setting::getConfig(1));
            return $this->fetch();
        }
    }
}