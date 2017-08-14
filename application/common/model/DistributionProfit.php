<?php

namespace app\common\model;

use think\Model;

//分销盈利记录
class DistributionProfit extends Model
{
    /**推广分红转信用币**/
    public function distributionCollection($userId)
    {
        $money = $this->where('c_id', $userId)->where('c_type', '个人')->where('status', '未结算')->sum('money');
        if ($money == 0) {
            return '可结算余额不足';
        }
        $this->startTrans();
        try {
            $data = array(
                'user_id' => $userId,
                'money' => $money,
                'title' => '推广结算',
                'create_time' => nowTime(),
                'type' => '推广结算',
                'status' => '正常'
            );
            $this->table('snake_credit_record')->insert($data);

            $this->query("UPDATE snake_user SET credit_money=credit_money+{$money} WHERE id={$userId}");

            $this->where('c_id', $userId)->where('c_type', '个人')->where('status', '未结算')->update(['status' => '已结算']);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }
}
