<?php

namespace app\common\model;

use think\Model;

/**奖品**/
class LuckyGoodsSpoil extends Model
{
    public $spoilTypeText = [0 => '竞彩奖', 1 => '安慰奖', 2 => '幸运奖'];

    public function savePrizeGoods($data)
    {
        $this->company_id = COMPANY_ID;
        $this->title = $data['title'];
        $this->detail = $data['detail'];
        $this->thumb = $data['thumb'];
        $this->spoil_type = intval($data['spoil_type']);
        $this->save($data);
    }
}
