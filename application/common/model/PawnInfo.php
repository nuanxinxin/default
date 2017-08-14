<?php

namespace app\common\model;

use think\Model;

class PawnInfo extends Model
{
    public function PawnInfoPics()
    {
        return $this->hasMany('PawnInfoPics', 'pawn_id', 'id');
    }

    public function add($data)
    {
        return $this->sub($data, false);
    }

    public function edit($data)
    {
        return $this->sub($data, true);
    }

    public function sub($data, $update)
    {
        $data['status'] = '待审核';
        $pics = explode(',', $data['pics']);
        $picsData = array();
        foreach ($pics as $item) {
            $picsData[] = array(
                'path' => $item
            );
        }

        $this->startTrans();
        try {
            $this->allowField(true)->save($data);

            if ($update) {
                $this->PawnInfoPics()->delete();
            }

            $this->PawnInfoPics()->saveAll($picsData);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }

    public function del()
    {
        $this->startTrans();
        try {
            $this->PawnInfoPics()->delete();
            $this->delete();

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }

    public static function HaltOrder()
    {
        self::where('distance_out_time', '<', nowTime())->setField('status', '已下架');
    }
}