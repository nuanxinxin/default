<?php

namespace app\common\model;

use think\Model;

class LuckyGoodsBuyRecord extends Model
{
    /**信用币支付下单**/
    public function creditPay($user_id, $goods_id = 0)
    {
        $this->startTrans();
        try {
            $credit_money = floatval(User::where('id', $user_id)->value('credit_money'));
            $goods = $this->lock(true)->query("select a.end_time,a.goods_title,a.goods_id,a.company_id,a.single_price,a.comfort_spoil_enable,a.spoil_id,a.comfort_spoil_id,(a.max_people_number-(select count(b.user_id) from snake_lucky_goods_buy_record b where b.goods_id=a.goods_id and b.pay_status in(0,1))) surplus_count from snake_lucky_goods a where a.goods_id={$goods_id} and a.is_out=0 group by a.goods_id")[0];
            if (!$goods) {
                abort(300, '正在开奖或已结束');
            } elseif (!$goods['surplus_count']) {
                abort(300, '参与人数已满');
            } elseif ($goods['single_price'] > $credit_money) {
                abort(300, '信用币不足');
            }

            if ($this->where('user_id', $user_id)->where('goods_id', $goods_id)->where('pay_status', 1)->count()) {
                abort(300, '你已参与');
            }

            $data = array(
                'goods_id' => $goods_id,
                'user_id' => $user_id,
                'pay_method' => 1,
                'pay_status' => 1
            );
            $this->insert($data);

            $money = floatval($goods['single_price']);

            $creditData = array(
                'user_id' => $user_id,
                'money' => -$money,
                'title' => '抽奖支付',
                'create_time' => nowTime(),
                'type' => '抽奖支付',
                'status' => '正常'
            );
            CreditRecord::create($creditData);

            $xycj_score = unserialize(User::where('id', $user_id)->value('xycj_score'));
            if (isset($xycj_score[$goods['company_id']])) {
                $xycj_score[$goods['company_id']]++;
            } else {
                $xycj_score[$goods['company_id']] = 1;
            }
            $xycj_score = serialize($xycj_score);
            $this->query("UPDATE snake_user SET credit_money=credit_money-{$money},xycj_score='{$xycj_score}' WHERE id={$user_id}");//扣除信用币
			
            if(User::where('id', $user_id)->value('wx')){
            //发送参与成功模版信息
	            $tempData=array(
	            		'first'=>User::where('id', $user_id)->value('nickname').'您好购买成功',
	            		'productType'=>'欢乐购奖品',
	            		'name'=>$goods['goods_title'],
	            		'number'=>购买数量1,
	            		'expDate'=>date("Y-m-d H:i:s",$goods['end_time']),
	            		'remark'=>'恭喜你购买商品成功,祝你好运,还需'.bcsub($goods['surplus_count'],1).'人次开奖'
	            );
	            
	            sendTemplateMsg(User::where('id', $user_id)->value('wx'), $tempData, "EpYjIeX_yb7IVNLQYJHpHwa8U0LiQ5XKFwedxTb21g8");
            }
            //库存等于1时开始抽奖
            if ($goods['surplus_count'] == 1) {
                $this->startLuckDraw($goods);
            }

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }

    //微信支付下单
    public function creditOrder($user_id, $goods_id = 0)
    {
        $this->startTrans();
        try {
            $goods = $this->lock(true)->query("select a.goods_id,a.company_id,a.single_price,a.comfort_spoil_enable,a.spoil_id,a.comfort_spoil_id,(a.max_people_number-(select count(b.user_id) from snake_lucky_goods_buy_record b where b.goods_id=a.goods_id and b.pay_status in(0,1))) surplus_count from snake_lucky_goods a where a.goods_id={$goods_id} and a.is_out=0 group by a.goods_id")[0];
            if (!$goods) {
                abort(300, '已结束');
            } elseif (!$goods['surplus_count']) {
                abort(300, '参与人数已满');
            }

            if ($this->where('user_id', $user_id)->where('goods_id', $goods_id)->where('pay_status', 1)->count()) {
                abort(300, '参与次数不能大于1');
            }

            $data = array(
                'goods_id' => $goods_id,
                'user_id' => $user_id,
                'pay_method' => 2,
                'pay_status' => 0
            );
            $this->insert($data);

            $money = floatval($goods['single_price']);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }

    /**开始抽奖**/
    private function startLuckDraw($goods)
    {
        $time = nowTime();
        $user_ids = $this->where('goods_id', $goods['goods_id'])->where('pay_status', 1)->column('user_id');
        $win_user_index = array_rand($user_ids, 1);
        $win_user_id = $user_ids[$win_user_index];

        if ($goods['comfort_spoil_enable'] && $goods['comfort_spoil_id']) {
            $ticketIdentifiers = $this->createTicketId(count($user_ids));
            $win_ticket_id = $ticketIdentifiers[$win_user_index];
            unset($user_ids[$win_user_index]);
            unset($ticketIdentifiers[$win_user_index]);
        } else {
            $ticketIdentifiers = $this->createTicketId(1);
            $win_ticket_id = $ticketIdentifiers[0];
        }

        $goods_spoil_record = [];
        $goods_spoil_record[] = [
            'goods_id' => $goods['goods_id'],
            'user_id' => $win_user_id,
            'spoil_time' => $time,
            'spoil_type' => 0,
            'ticket_status' => 0,
            'ticket_identifier' => $win_ticket_id
        ];
		
        //发送中奖提示
        $awardData=array(
        		'first'=>User::where('id', $win_user_id)->value('nickname').'恭喜您,中奖了',
        		'keyword1'=>$goods['goods_title'],
        		'keyword2'=>$win_ticket_id,
        		'remark'=>'恭喜你获得奖品,赶紧去领奖吧'        		
        );
        sendTemplateMsg(User::where('id', $win_user_id)->value('wx'), $awardData, "5kG9R_uBDOmRhHFvGjyJW56huHYS1QREo875JGby-CQ");
        if ($goods['comfort_spoil_enable'] && $goods['comfort_spoil_id']) {
            foreach ($user_ids as $index => $user_id) {
                $goods_spoil_record[] = [
                    'goods_id' => $goods['goods_id'],
                    'user_id' => $user_id,
                    'spoil_time' => $time,
                    'spoil_type' => 1,
                    'ticket_status' => 0,
                    'ticket_identifier' => $ticketIdentifiers[$index]
                ];
            }
        }
        LuckyGoodsSpoilRecord::insertAll($goods_spoil_record);

        $this->updateGoods($goods, 'success');
    }

    /**生成兑奖号**/
    private function createTicketId($number, $data = [])
    {
        for ($i = 0; $i < $number; $i++) {
            $data[] = rand(1000000000, 9999999999);
        }
        $repeat = LuckyGoodsSpoilRecord::where('ticket_identifier', 'in', $data)->column('ticket_identifier');
        $data = array_diff($data, $repeat);
        $data = array_unique($data);
        $data = array_values($data);
        $i = $number - count($data);
        if ($i > 0) {
            $data = $this->createTicketId($number, $data);
        }
        return $data;
    }

    /**开奖成功或失败更新活动信息**/
    private function updateGoods($goods, $result)
    {
        switch ($result) {
            case 'success':
                LuckyGoods::where('goods_id', $goods['goods_id'])->update(['is_out' => 1, 'result' => 2]);
                break;
            case 'failed':
                LuckyGoods::where('goods_id', $goods['goods_id'])->update(['is_out' => 1, 'result' => 1]);
                break;
        }
    }

    /**购买记录**/
    public function myBuyLuckyGoodsList()
    {
        $result = $this->query("select b.goods_id,b.goods_title,b.end_time,b.page_thumb,b.max_people_number,b.single_price,a.buy_time,(select count(user_id) from snake_lucky_goods_buy_record where goods_id=a.goods_id) playPeopleCount,c.title,e.nickname winUserName from snake_lucky_goods_buy_record a left join snake_lucky_goods b on b.goods_id=a.goods_id left join snake_lucky_goods_spoil c on c.spoil_id=b.spoil_id left join snake_lucky_goods_spoil_record d on d.goods_id=a.goods_id and d.spoil_type=0 left join snake_user e on e.id=d.user_id where a.pay_status=1 and a.user_id=" . USER_ID . " group by a.goods_id");
        $data = [];
        $data['ingList'] = [];
        $data['edList'] = [];
        foreach ($result as $item) {
            if (!$item['winUserName']) {
                $data['ingList'][] = [
                    'goodsId' => $item['goods_id'],
                    'goodsTitle' => $item['goods_title'],
                    'goodsThumb' => fullPath($item['page_thumb']),
                    'maxPeople' => $item['max_people_number'],
                    'singlePrice' => $item['single_price'],
                    'spoilTitle' => $item['title'],
                    'buyTime' => $item['buy_time'],
                    'playPeopleCount' => $item['playPeopleCount'],
                    'goodsStatus' => $this->goodsStatus($item),
                ];
            } else {
                $data['edList'][] = [
                    'goodsId' => $item['goods_id'],
                    'goodsTitle' => $item['goods_title'],
                    'goodsThumb' => fullPath($item['page_thumb']),
                    'maxPeople' => $item['max_people_number'],
                    'singlePrice' => $item['single_price'],
                    'spoilTitle' => $item['title'],
                    'buyTime' => $item['buy_time'],
                    'winUserName' => $item['winUserName']
                ];
            }
        }
        return $data;
    }


    private function goodsStatus($goods)
    {
        $time = nowTime();
        $spoil_time = intval(LuckyGoodsSpoilRecord::where('goods_id', $goods['goods_id'])->where('spoil_type', 0)->value('spoil_time'));
        if ($time > $goods['end_time'] || ($spoil_time > 0 && ($time - 5) > $spoil_time)) {
            $status = 2;
        } elseif ($spoil_time > ($time - 5)) {
            $status = 1;
        } else {
            $status = 0;
        }
        return $status;
    }
}
