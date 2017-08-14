<?php

namespace app\common\model;

use think\Model;
use org\PrivateImage;

//贷款信息 基础信息
class CreditCard extends Model
{
    public function User()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }


}
