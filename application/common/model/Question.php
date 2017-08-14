<?php

namespace app\common\model;

use think\Model;

class Question extends Model
{
    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id')->field('thumb,nickname');
    }

    public function getAllList()
    {
        $list = $this->order('id desc')->select();
        $data = array();
        foreach ($list as $indx => $item) {
            $data[] = array(
                'id' => $item->id,
                'question' => $item->question,
                'question_time' => $item->question_time,
                'answer' => (string)$item->answer,
                'answer_time' => (string)$item->answer_time,
                'nickname' => $item->user->nickname,
                'thumb' => $item->user->thumb
            );
        }
        return $data;
    }
}