<?php

namespace org;

use think\Exception;//异常处理类
use org\StringCrypt;//字符串加密解密类

class PrivateImage
{
    /**
     * 读取图片
     * @param string $path
     * @param string $key
     * @return string
     * @throws Exception
     */
    public static function getImage($path = '', $key = '')
    {
        header("Content-type:image/*");
        if ($path == '') self::error('图片路径错误');
        $path = IMAGES . DS . StringCrypt::decrypt($path, $key);
        $path = str_replace(['AAAAA', 'BBBBB'], ['/', '.'], $path);
        if (!is_file($path)) self::error('图片不存在');
        $image = fread(fopen($path, "r"), filesize($path));
        return $image;
    }

    public static function trueImageUrl($path = '', $key = ''){
        $path = IMAGES . DS . StringCrypt::decrypt($path, $key);
        $path = str_replace(['AAAAA', 'BBBBB'], ['/', '.'], $path);
        if (!is_file($path)) self::error('图片不存在');
        return $path;
    }

    /**
     * 获取私密图片地址
     * @param string $path 加密路径字符串
     * @param string $key 用户标识符
     * @return string
     */
    public static function getImageUrl($path = '', $key = '')
    {
        return domain() . DS . 'pic' . DS . $key . DS . $path;
    }

    /**
     * 错误信息
     * @param string $msg
     * @throws Exception
     */
    private function error($msg = 'error')
    {
        throw new Exception($msg);
    }
}