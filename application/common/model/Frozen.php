<?php

namespace app\common\model;

use think\Model;

class Frozen extends Model
{
    public static function finishOrder()
    {
        $list = self::where('finish_status', 0)->where('unfrozen_time', '<', nowTime())->select();
        foreach ($list as $index => $item) {
            $apply_refund = false;
            $info = array();
            switch ($item->o_type) {
                case 'loan':
                    $info = LoanInfo::get($item->o_id);
                    $apply_refund = (bool)$info->apply_refund;
                    $title = '收取信息保证金';
                    $loanId = $item->o_id;
                    $pawnId = '';
                    break;
                case 'pawn':
                    $info = PawnInfo::get($item->o_id);
                    $apply_refund = (bool)$info->apply_refund;
                    $title = '收取典当押金';
                    $pawnId = $item->o_id;
                    $loanId = '';
                    break;
            }
            if ($apply_refund === false && $info) {
                self::startTrans();
                try {
                    $item->finish_status = 1;
                    $item->save();
                    self::query("UPDATE snake_user SET credit_money=credit_money+{$item->money} WHERE id={$info->user_id}");
                    $data = array(
                        'user_id' => $info->user_id,
                        'money' => $item->money,
                        'title' => $title,
                        'loan_id' => $loanId,
                        'pawn_id' => $pawnId,
                        'type' => $title,
                        'status' => '正常'
                    );
                    CreditRecord::create($data);
                    self::commit();
                } catch (\Exception $e) {
                    self::rollback();
                }
            }
        }
        file_put_contents('/home/wwwroot/default/public/test.txt', date('Y-m-d H:i:s'));
    }
}