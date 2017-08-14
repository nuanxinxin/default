<?php

namespace app\common\model;

use think\Model;

class Document extends Model
{
    public function setContentAttr($value)
    {
        return htmlspecialchars($value);
    }

    public function getContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }
}