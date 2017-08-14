<?php

namespace app\common\model;

use think\Model;

class LuckyDaySettlement extends Model
{
    public $stateText = [0 => '待付款', 1 => '已付款', 2 => '商户已确认'];

    /**每日统计**/
    public function everydayCount()
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $yesterdayStart = date('Y-m-d H:i:s', strtotime($yesterday));
        $yesterdayEnd = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($yesterday)) - 1);
        //查询昨天抽奖成功的活动并且统计商家举行活动的支付订单总金额金额
        $result = $this->query("select sum(a.single_price) amount,a.company_id from snake_lucky_goods a inner join snake_lucky_goods_buy_record b on a.goods_id=b.goods_id where (b.buy_time between '{$yesterdayStart}' and '{$yesterdayEnd}') and  a.result=2 and b.pay_status=1 group by a.company_id");
        if ($this->where('day', $yesterday)->count()) {
            return;
        } else {
            $data = [];
            foreach ($result as $item) {
                $data[] = [
                    'company_id' => $item['company_id'],
                    'day' => $yesterday,
                    'amount' => $item['amount'],
                    'state' => 0
                ];
            }
            if (!empty($data)) {
                $this->insertAll($data);
            }
        }
    }

    /**日结算统计列表**/
    public function settlement($company_id = 0)
    {
        if ($company_id > 0) {
            $result = $this
                ->view(['snake_lucky_day_settlement' => 'a'], 'id,company_id,day,amount,state')
                ->view(['snake_admin_company' => 'b'], 'company_name', 'a.company_id=b.id')
                ->where('b.id', $company_id)
                ->paginate(10);
        } else {
            $result = $this
                ->view(['snake_lucky_day_settlement' => 'a'], 'id,company_id,day,amount,state')
                ->view(['snake_admin_company' => 'b'], 'company_name', 'a.company_id=b.id')
                ->paginate(10);
        }
        return $result;
    }
}
