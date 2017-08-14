<?php

namespace app\common\model;

use think\Model;

class LuckyGoodsSpoilRecord extends Model
{
    public $spoilTypeText = [0 => '竞彩奖', 1 => '安慰奖'];
    public $ticketStatusText = [0 => '未兑奖', 1 => '已兑奖'];

    public function mySpoilList($user_id = 0)
    {
        $data = [];
        $result = $this->query("select r.record_id,r.goods_id,r.user_id,r.spoil_time,r.spoil_type,r.ticket_status,r.ticket_time,r.ticket_identifier,g.goods_title,g.page_thumb,g.single_price,g.max_people_number,g.spoil_id,g.comfort_spoil_id,br.buy_time,(select s.title from snake_lucky_goods_spoil s where s.spoil_id=if(r.spoil_type=0,g.spoil_id,g.comfort_spoil_id)) spoil_title from snake_lucky_goods_spoil_record r left join snake_lucky_goods g on r.goods_id = g.goods_id left join snake_lucky_goods_buy_record br on br.goods_id = r.goods_id where r.user_id={$user_id} group by r.goods_id order by r.spoil_time desc");
        foreach ($result as $item) {
            $data[] = array(
                'goodsId' => $item['goods_id'],
                'goodsTitle' => $item['goods_title'],
                'goodsThumb' => fullPath($item['page_thumb']),
                'maxPeople' => $item['max_people_number'],
                'singlePrice' => floatval($item['single_price']),
                'spoilTitle' => $item['spoil_title'],
                'buyTime' => $item['buy_time'],
                'spoilTicket' => $item['ticket_identifier'],
                'ticketStatus' => $item['ticket_status']
            );
        }
        $result2 = $this->query("select b.title,b.thumb,a.spoil_time,a.ticket_status,a.ticket_identifier from snake_lucky_xydj a left join snake_lucky_goods_spoil b on b.spoil_id=a.spoil_id where a.user_id={$user_id}");
        foreach ($result2 as $item) {
            $data[] = array(
                'goodsId' => '-1',
                'goodsTitle' => '幸运抽奖',
                'goodsThumb' => fullPath($item['thumb']),
                'maxPeople' => '-1',
                'singlePrice' => '-1',
                'spoilTitle' => $item['title'],
                'buyTime' => date('Y-m-d H:i:s', $item['spoil_time']),
                'spoilTicket' => $item['ticket_identifier'],
                'ticketStatus' => $item['ticket_status']
            );
        }
        $data = arraySort($data, 'buyTime', SORT_DESC);
        return $data;
    }

    public function spoilList()
    {
        $data = [];
        $result = $this->query("select r.goods_id,r.user_id,g.goods_title,u.nickname,u.phone,u.thumb,br.buy_time from snake_lucky_goods_spoil_record r left join snake_lucky_goods g on r.goods_id = g.goods_id left join snake_user u on u.id = r.user_id left join snake_lucky_goods_buy_record br on br.goods_id = r.goods_id group by r.goods_id order by r.spoil_time desc");
        foreach ($result as $item) {
            $data[] = array(
                'goodsId' => $item['goods_id'],
                'goodsTitle' => $item['goods_title'],
                'userThumb' => fullPath($item['thumb']),
                'userName' => $item['nickname'],
                'userPhone' => phone_encode($item['phone']),
                'buyTime' => $item['buy_time'],
            );
        }
        return $data;
    }

    public function getGoodsSpoilAttr($value, $data)
    {
        $spoil_id = 0;
        switch ($data['spoil_type']) {
            case '0':
                $spoil_id = $data['spoil_id'];
                break;
            case '1':
                $spoil_id = $data['comfort_spoil_id'];
                break;
        }
        return LuckyGoodsSpoil::where('spoil_id', $spoil_id)->field('title,thumb')->find();
    }

    public function company_spoil_list()
    {
        return $this
            ->view(['snake_lucky_goods_spoil_record' => 'r'], 'goods_id,user_id,spoil_time,spoil_type,ticket_status,ticket_time')
            ->view(['snake_lucky_goods' => 'g'], 'goods_title,spoil_id,comfort_spoil_id', 'r.goods_id = g.goods_id')
            ->view(['snake_user' => 'u'], 'nickname,phone,thumb', 'u.id = r.user_id')
            ->view(['snake_lucky_goods_buy_record' => 'br'], 'buy_time', 'br.goods_id = r.goods_id')
            ->where('g.company_id', COMPANY_ID)
            ->order('ticket_status desc,spoil_time asc')
             ->group('r.record_id') 
            ->paginate(15);
    }

    public function spoilDetail($ticket_identifier)
    {
        return $this
            ->view(['snake_lucky_goods_spoil_record' => 'r'], 'goods_id,user_id,spoil_time,spoil_type,ticket_status,ticket_time,ticket_identifier')
            ->view(['snake_lucky_goods' => 'g'], 'goods_title,spoil_id,comfort_spoil_id', 'r.goods_id = g.goods_id')
            ->view(['snake_user' => 'u'], 'nickname,phone,thumb', 'u.id = r.user_id')
            ->view(['snake_lucky_goods_buy_record' => 'br'], 'buy_time', 'br.goods_id = r.goods_id')
            ->where('g.company_id', COMPANY_ID)
            ->where('r.ticket_identifier', $ticket_identifier)
            ->order('ticket_status desc,spoil_time asc')
            ->find();
    }
}
