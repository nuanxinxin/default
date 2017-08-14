<?php

namespace app\common\model;

use think\Model;

class Setting extends Model
{
    /**
     * type说明
     * 1:贷款信息参数配置
     * 2:认证信息配置
     * 3:典当信息配置
     */
    public static function getConfig($type)
    {
        $data = self::_getCache($type);
        if (!$data) {
            $data = self::where(array('type' => $type))->column('value', 'name');
            self::_setCache($type, $data);
        }
        return $data;
    }

    /**
     * 获取设置项值
     * @param $type
     * @param $name
     * @return mixed
     */
    public static function getConfigValue($type, $name)
    {
        $data = self::getConfig($type);
        return $data[$name];
    }

    /**
     * 清除缓存
     * @param $name
     * @return $this
     */
    public function clearCache($name)
    {
        $this->_setCache($name, null);
        return $this;
    }

    /**
     * 设置缓存
     * @param $name
     * @param $data
     */
    private function _setCache($name, $data)
    {
        cache($name, $data, ['path' => CACHE_PATH . 'setting']);
    }

    /**
     * 读取缓存
     * @param $name
     * @return mixed
     */
    private function _getCache($name)
    {
        cache(['path' => CACHE_PATH . 'setting']);
        return cache($name);
    }

    /**
     * getAuthFeeDistribution
     */
    public static function getAuthFeeDistribution()
    {
        $result = self::getConfigValue(2, 'auth_fee_distribution');
        $result = explode(',', $result);
        dump($result);
        for ($i = 0; $i < 3; $i++) {
            if (!isset($result[$i])) {
                abort(500, '推广认证费配置不完整');
            }
        }
        return $result;
    }
}
