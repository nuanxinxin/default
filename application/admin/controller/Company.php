<?php

namespace app\admin\controller;

use app\common\model\AdminCompany;
use app\common\model\DistributionProfit;
use app\common\model\ToolRecord;
use app\common\model\User;

class Company extends AdminBase
{
    public function _initialize()
    {
        parent::_initialize();
        if (LOGIN_TYPE != 'admin') {
            $this->error('非法操作');
        }
    }
    /**
     * 公司列表
     * @param string $keyword
     * @return mixed
     */
    public function index($keyword = '')
    {
        $list = AdminCompany::where('username|company_name', 'like', '%' . $keyword . '%')->order('id desc')->paginate(10, false, ['query' => ['keyword' => $keyword]]);
        foreach ($list as $index => $item) {
            $where = array(
                'c_id' => $item->id,
                'c_type' => '公司',
                'status' => '待结算'
            );
            $list[$index]->balance = DistributionProfit::where($where)->sum('money') > 0 ? true : false;
        }
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 结算
     * @param $id
     */
    public function balance($id)
    {
        $where = array(
            'c_id' => $id,
            'c_type' => '公司',
            'status' => '待结算'
        );
        if (DistributionProfit::where($where)->update(['status' => '已结算'])) {
            $this->success('结算成功。');
        } else {
            $this->error('结算失败。');
        }
    }

    /**
     * 添加新公司信息
     * @return mixed
     */
    public function create()
    {
        if (isPost()) {
            try {
                $result = $this->setData(new AdminCompany)->addCompany();
                if ($result !== true) {
                    abort(500, $result);
                }
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success('添加成功', path('index'));
        } else {
            return $this->fetch('form');
        }
    }

    /**
     * 修改公司信息
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
    	
        if (isPost()) {
            try {
                $this->setData(AdminCompany::get($id))->save();
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success('修改成功', cookie('referer'));
        } else {
            cookie('referer', $this->request->header('referer'));
            $data = AdminCompany::get($id);
            $data->union_pay_config = unserialize($data->union_pay_config);
            $this->assign('data', $data);
            return $this->fetch('form');
        }
    }

    /**
     * 删除公司
     * @param $id
     */
    public function delete($id)
    {
        $model = new AdminCompany;
        $result = $model->deleteCompany($id);
        if ($result === true) {
            $this->success('删除成功');
        } else {
            $this->error($result);
        }
    }

    /**
     * 设置需要保存的数据
     * @param $model
     * @return mixed
     */
    private function setData($model)
    {
        $model->company_pic = $this->request->param('company_pic');
        $model->username = $this->request->param('username');
        $model->company_name = $this->request->param('company_name');
        $model->min_rate = $this->request->param('min_rate');
        $model->rate = $this->request->param('rate');
        $model->business_licence_zheng= $this->request->param('business_licence_zheng');
        $model->cert_correct= $this->request->param('cert_correct');
        $model->cert_opposite= $this->request->param('cert_opposite');
        $model->card_correct= $this->request->param('card_correct');
        $model->card_opposite= $this->request->param('card_opposite');
        $model->cert_meet= $this->request->param('cert_meet');
        $model->commission_charge= $this->request->param('commission_charge');
        $model->order_expense= $this->request->param('order_expense');
        $model->give_credit_money= $this->request->param('give_credit_money');
        if ($this->request->param('password')) {
            $model->password = $this->request->param('password');
        }
        $union_pay_config = serialize($this->request->param('union_pay_config/a'));
        $model->union_pay_config = $union_pay_config;
        return $model;
    }

    public function toolRecord($keyword = '', $company_id = 0, $user_in_come_status = '', $payment = '')
    {
        $where = array();
        if ($keyword != '') {
            $user_id = User::where('phone', $keyword)->value('id');
            $where['user_id'] = $user_id;
        }
        if ($company_id > 0) {
            $where['company_id'] = $company_id;
        }
        if ($user_in_come_status != '' && $user_in_come_status != '全部') {
            $where['user_in_come_status'] = $user_in_come_status;
        }
        if ($payment != '' && $payment != '全部') {
            $where['payment'] = $payment;
        }
        $list = ToolRecord::where($where)->order('id desc')->paginate(10, false, ['query' => ['keyword' => $keyword, 'company_id' => $company_id, 'user_in_come_status' => $user_in_come_status, 'payment' => $payment]]);

        $this->assign('list', $list);
        $this->assign('companys', AdminCompany::all());
        return $this->fetch('tool_record');
    }
}
