<?php

namespace app\common\model;

use think\Model;

class LuckyXydj extends Model
{
    public $ticketStatusText = [0 => '未兑奖', 1 => '已兑奖'];

    public function getGoodsSpoilAttr($value, $data)
    {
        return LuckyGoodsSpoil::where('spoil_id', $data['spoil_id'])->field('title,thumb')->find();
    }

    public function company_spoil_list()
    {
        return $this
            ->view(['snake_lucky_xydj' => 'a'], 'record_id,user_id,spoil_time,ticket_status,ticket_time,spoil_id')
            ->view(['snake_user' => 'b'], 'nickname,phone,thumb', 'b.id = a.user_id')
            ->view(['snake_lucky_goods_spoil' => 'c'], 'title,thumb goods_thumb', 'c.spoil_id = a.spoil_id')
            ->where('c.company_id', COMPANY_ID)
            ->order('ticket_status desc,spoil_time asc')
            ->group('a.user_id')
            ->paginate(15);
    }

    public function spoilDetail($ticket_identifier)
    {
        return $this
            ->view(['snake_lucky_xydj' => 'a'], 'record_id,user_id,spoil_time,ticket_status,ticket_time,ticket_identifier,spoil_id')
            ->view(['snake_user' => 'b'], 'nickname,phone,thumb', 'b.id = a.user_id')
            ->view(['snake_lucky_goods_spoil' => 'c'], 'title,thumb goods_thumb', 'c.spoil_id = a.spoil_id')
            ->where('c.company_id', COMPANY_ID)
            ->where('a.ticket_identifier', $ticket_identifier)
            ->order('ticket_status desc,spoil_time asc')
            ->find();
    }

    public function specialDraw($company_id = 0)
    {
        $spoil_ids = LuckyGoodsSpoil::where('company_id', $company_id)->where('spoil_type', 2)->column('spoil_id');
        $spoil_id = $spoil_ids[array_rand($spoil_ids, 1)];
        $goods_spoil = LuckyGoodsSpoil::get($spoil_id);
        $data = [];
        $data['spoilThumb'] = fullPath($goods_spoil->thumb);
        $data['spoilTitle'] = $goods_spoil->title;

        $xycj_score = unserialize(User::where('id', USER_ID)->value('xycj_score'));
        $xycj_conditions_buy_count = intval(AdminCompany::where('id', $company_id)->value('xycj_conditions_buy_count'));
        if ($xycj_conditions_buy_count > $xycj_score[$company_id]) {
            abort(500, '不符合抽奖条件');
        } else {
            $xycj_score[$company_id] = $xycj_score[$company_id] - $xycj_conditions_buy_count;
            if ($xycj_score[$company_id] <= 0) {
                $xycj_score[$company_id] = 0;
            }
            $xycj_score = serialize($xycj_score);
        }

        $this->startTrans();
        try {
            User::where('id', USER_ID)->setField('xycj_score', $xycj_score);
            $xydj = [
                'user_id' => USER_ID,
                'spoil_time' => nowTime(),
                'ticket_status' => 0,
                'ticket_identifier' => $this->createTicketId(),
                'spoil_id' => $spoil_id
            ];
            $this->insert($xydj);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return $data;
    }

    /**生成兑奖号**/
    private function createTicketId()
    {
        $data = rand(1000000000, 9999999999);
        if ($this->where('ticket_identifier', $data)->count()) {
            $data = $this->createTicketId();
        }
        return $data;
    }
}