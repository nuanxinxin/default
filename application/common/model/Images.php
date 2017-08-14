<?php

namespace app\common\model;

use think\Model;

class Images extends Model
{

    /**
     * 事件注册
     */
    protected static function init()
    {
        self::event('before_insert', function ($model) {
            $model->create_time = nowTime();
        });

        self::event('before_update', function ($model) {

        });

        self::event('before_write', function ($model) {

        });
    }

    /**
     * getFilePathAttr
     * @param $value
     * @return string
     */
    public function getFilePathAttr($value)
    {
        return RES_IMAGES . DS . $value;
    }
}
