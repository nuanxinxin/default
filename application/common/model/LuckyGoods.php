<?php

namespace app\common\model;

use think\Model;
use think\Db;

/**活动**/
class LuckyGoods extends Model
{
    public $isOutText = [0 => '进行中', 1 => '已结束'];
    public $resultText = [0 => '-', 1 => '失败', 2 => '成功'];

    public function buyRecord()
    {
        return $this->hasMany('LuckyGoodsBuyRecord', 'goods_id', 'goods_id')->where('pay_status', 'in', '0,1');
    }

    public function spoil()
    {
        return $this->belongsTo('LuckyGoodsSpoil', 'spoil_id', 'spoil_id');
    }

    public function comfortSpoil()
    {
        return $this->belongsTo('LuckyGoodsSpoil', 'comfort_spoil_id', 'spoil_id');
    }

    public function spoilRecord()
    {
        return $this->hasMany('LuckyGoodsSpoilRecord', 'goods_id', 'goods_id');
    }

    public function saveLuckyGoods($data)
    {
        $this->company_id = COMPANY_ID;
        $this->goods_title = $data['goods_title'];
        $this->page_thumb = $data['page_thumb'];
        $this->detail = $data['detail'];
        $this->phone = $data['phone'];
        $this->detail_thumb = $data['detail_thumb'];
        $this->single_price = floatval($data['single_price']);
        $this->min_people_number = $data['max_people_number'];
        $this->max_people_number = $data['max_people_number'];
        $this->start_time = strtotime($data['start_time']);
        $this->end_time = strtotime($data['end_time']);
        $this->spoil_id = $data['spoil_id'];
        $this->comfort_spoil_enable = intval($data['comfort_spoil_enable']);
        $this->comfort_spoil_id = $this->comfort_spoil_enable ? $data['comfort_spoil_id'] : 0;
        $this->save();
    }

    public function spoilGoods($goodsId = 0)
    {
        $time = nowTime();
        $data = [];
        $result = $this->where('goods_id', $goodsId)->where('start_time', 'lt', $time)->field('*')->find();
        if ($result) {
            $goodsThumb = !empty($result['detail_thumb']) ? fullPath($result['detail_thumb']) : fullPath($result['page_thumb']);
            $spoil = [
                'title' => $result->spoil->title,
                'desc' => $result->spoil->detail,
                'thumb' => fullPath($result->spoil->thumb)
            ];
            if ($result->comfortSpoil) {
                $comfort_spoil = [
                    'title' => $result->comfortSpoil->title,
                    'desc' => $result->comfortSpoil->detail,
                    'thumb' => fullPath($result->comfortSpoil->thumb)
                ];
            } else {
                $comfort_spoil = [];
            }
            $playPeopleList = $result->buyRecord;
            $data = [
                'goodsId' => $result['goods_id'],
                'goodsThumb' => $goodsThumb,
                'title' => $result['goods_title'],
                'desc' => $result['detail'],
                'maxPeople' => $result['max_people_number'],
                'singlePrice' => $result['single_price'],
                'spoil' => $spoil,
                'comfort_spoil' => $comfort_spoil,
                'status' => $this->goodsStatus($result),
                'playPeopleCount' => count($playPeopleList),
                'playPeopleList' => $this->playPeopleList($result),
                'endTime' => date('Y-m-d H:i:s', $result['end_time'])
            ];
        }
        return $data;
    }

    private function playPeopleList($goods)
    {
        $data = [];
        $result = $this->query("select u.nickname,u.phone,u.thumb,s.spoil_type from snake_lucky_goods_buy_record br left join snake_user u on br.user_id = u.id left join snake_lucky_goods_spoil_record s on br.user_id=s.user_id and br.goods_id = s.goods_id where br.goods_id={$goods['goods_id']} and br.pay_status in(0,1) group by br.user_id");
        foreach ($result as $item) {
            if ($item['spoil_type'] === NULL) {
                $winType = 0;
            } elseif ($item['spoil_type'] === 0) {
                $winType = 1;
            } elseif ($item['spoil_type'] === 1) {
                $winType = 2;
            }
            $data[] = [
                'winType' => $winType,
                'userThumb' => fullPath($item['thumb']),
                'userName' => $item['nickname'],
                'userPhone' => phone_encode($item['phone'])
            ];
        }
        return $data;
    }

    public function goodsList($company_id = 0,$page)
    {
 
        if ($company_id > 0) {
            $companyWhere = ' and goods.company_id=' . $company_id;
        } else {
            $companyWhere = '';
        }
        $time = nowTime();
        
        $page=$page?$page:1;
        $pageSize=10*($page-1);
        $result = $this->query("select goods_id,goods_title,page_thumb,single_price,end_time,max_people_number,(select count(record_id) from snake_lucky_goods_buy_record record where goods.goods_id=record.goods_id and record.pay_status in(0,1)) as people_count from snake_lucky_goods goods where goods.start_time < {$time}  and goods.end_time > {$time} and goods.is_out=0{$companyWhere}
        ORDER BY people_count DESC LIMIT ".$pageSize.",10");
        $list = [];
        foreach ($result as $item) {
            $list[] = [
                'goodsId' => $item['goods_id'],
                'goodsThumb' => fullPath($item['page_thumb']),
                'title' => $item['goods_title'],
                'maxPeople' => $item['max_people_number'],
                'singlePrice' => floatval($item['single_price']),
                'playPeopleCount' => $item['people_count'],
                'endTime' => date('Y/m/d H:i:s', $item['end_time'])
            ];
        }
        return $list;
    }
    
    public function goodsTotal(){
    	
    	$time = nowTime();

    	$result = $this->query("select count(*) as count from snake_lucky_goods goods where goods.start_time < {$time}  and goods.end_time > {$time} and goods.is_out=0 ");
    	
    	return $result[0]['count'];
    	
    }
    
    
    
    public function specialInfo($company_id = 0)
    {
        $xycj_score = unserialize(User::where('id', USER_ID)->value('xycj_score'));
        $beenBuyCount = intval($xycj_score[$company_id]);
        $conditionsBuyCount = intval(AdminCompany::where('id', $company_id)->value('xycj_conditions_buy_count'));
        $enable = !$conditionsBuyCount ? false : true;
        $data = [];
        $data['enable'] = $enable;
        $data['beenBuyCount'] = $beenBuyCount;
        $data['conditionsBuyCount'] = $conditionsBuyCount;
        $data['specialSpoilTitle'] = implode('、', LuckyGoodsSpoil::where('company_id', $company_id)->where('spoil_type', 2)->column('title'));
        return $data;
    }

    public function companyList()
    {
        $list = [];
        $result = $this
            ->view(['snake_admin_company' => 'company'], 'company_name,company_pic,identifier')
            ->view(['snake_lucky_goods' => 'goods'], 'company_id', 'company.id=goods.company_id')
            ->group('company_id')
            ->select();
        foreach ($result as $item) {
            $list[] = [
                'companyId' => $item['identifier'],
                'companyName' => $item['company_name'],
                'companyThumb' => empty($item['company_pic']) ? '' : fullPath($item['company_pic'])
            ];
        }
        return $list;
    }

    public function recommend()
    {
        $result = $this->field('goods_id,page_thumb')->where('is_out', 0)->where('is_recommend', 1)->order('goods_id desc')->limit(10)->select();
        $list = [];
        foreach ($result as $item) {
            $list[] = [
                'goodsId' => $item['goods_id'],
                'goodsThumb' => fullPath($item['page_thumb'])
            ];
        }
        return $list;
    }

    public function spoilGoodsOpenInfo($goodsId = 0)
    {
        $time = nowTime();
        $data = [];
        $result = $this->where('goods_id', $goodsId)->where('start_time', 'lt', $time)->field('*')->find();
        if ($result) {
            $playPeopleList = $result->buyRecord;
            $data = [
                'status' => $this->goodsStatus($result),
                'playPeopleCount' => count($playPeopleList),
                'playPeopleList' => $this->playPeopleList($result)
            ];
        }
        return $data;
    }

    private function goodsStatus($goods)
    {
        $time = nowTime();
        $spoilRecord = $goods->spoilRecord;
        $spoil_time = intval($spoilRecord[0]['spoil_time']);
        if ($time > $goods['end_time'] || ($spoil_time > 0 && ($time - 5) > $spoil_time)) {
            $status = 2;
        } elseif ($spoil_time > ($time - 5)) {
            $status = 1;
        } else {
            $status = 0;
        }
        return $status;
    }

    /**抽奖失败处理**/
    public function handleFailed()
    {
        $time = time();
        $result = $this
            ->view(['snake_lucky_goods' => 'a'], 'goods_id,company_id,single_price')
            ->view(['snake_lucky_goods_buy_record' => 'b'], 'user_id,pay_method,pay_status', 'a.goods_id=b.goods_id', 'LEFT')
            ->where('a.is_out', 0)
            ->where('a.end_time', 'lt', strtotime('-10 minute'))
            ->limit(10)
            ->select();
        $goods_ids = [];
        $data = [];

        $this->startTrans();
        try {
            foreach ($result as $item) {
                $goods_ids[] = $item['goods_id'];
                if ($item['pay_status'] == 1) {
                    //信用币支付退回
                    if ($item['pay_method'] == 1) {
                        $money = floatval($item['single_price']);
                        $creditData = array(
                            'user_id' => $item['user_id'],
                            'money' => $money,
                            'title' => '抽奖退回',
                            'create_time' => $time,
                            'type' => '抽奖退回',
                            'status' => '正常'
                        );
                        CreditRecord::create($creditData);

                        $xycj_score = unserialize(User::where('id', $item['user_id'])->value('xycj_score'));
                        $xycj_score[$item['company_id']]--;
                        if ($xycj_score[$item['company_id']] <= 0) {
                            $xycj_score[$item['company_id']] = 0;
                        }
                        $xycj_score = serialize($xycj_score);
                        $this->query("UPDATE snake_user SET credit_money=credit_money+{$money},xycj_score='{$xycj_score}' WHERE id={$item['user_id']}");//增加信用币
                    }

                    //微信支付退回
                    if ($item['pay_method'] == 2) {

                    }
                }
            }
            $goods_ids = array_unique($goods_ids);
            //更新活动状态为已结束
            $this->where('goods_id', 'in', $goods_ids)->update(['is_out' => 1, 'result' => 1]);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
        }
    }
}
