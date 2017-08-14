<?php

namespace app\common\model;

use think\Model;

//收付款工具交易纪录
class ToolRecord extends Model
{

    public function AdminCompany()
    {
        return $this->belongsTo('AdminCompany', 'company_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    public function createOrderId()
    {
        return 'tool_' . getRandString(15);
    }

    public function createOrderId2()
    {
        return 'tool2_' . getRandString(15);
    }

    public function saveOrder()
    {
        $this->startTrans();
        try {

            $this->admin_in_come_status = '已到账';
            $this->user_in_come_status = '已到账';
            $this->save();

            $money = $this->money * (Setting::getConfigValue(4, 'channel_rebate') / 100);
            if ($money > 0) {
                $this->query("UPDATE snake_user SET credit_money=credit_money+{$money} WHERE id={$this->user_id}");//增加信用币

                $data = array(
                    'user_id' => $this->user_id,
                    'money' => $money,
                    'title' => '通道返利',
                    'type' => '通道返利',
                    'status' => '正常',
                    'create_time' => nowTime()
                );
                CreditRecord::create($data);
            }

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }
}
