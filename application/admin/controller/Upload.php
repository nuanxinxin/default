<?php

namespace app\admin\controller;

use think\Image;
use app\common\model\Images;

/**
 * 上传
 * @package app\admin\controller
 */
class Upload extends AdminBase
{

    /**
     * 图片上传
     * @return array|string
     */
    public function image()
    {
        try {
            $file = $this->request->instance()->file('file');
            if ($file === null) {
                abort(500, '请选择上传文件');
            } else {
                $validate = [
                    'size' => 1024 * 1024 * 2,
                    'ext' => 'jpg,png,jpeg',
                    'type' => 'image/jpeg,image/png'
                ];
                $info = $file->validate($validate)->move(R_RES_IMAGES);
                if ($info) {
                    $file_path = $info->getSaveName();
                    //记录上传图片
//                    $imageModel = new Images;
//                    $imageModel->file_path = $file_path;
//                    $imageModel->save();

                    //生成缩略图
                    $path = RES_IMAGES . DS . $file_path;
//                    $thumbPath = str_replace('.' . $info->getExtension(), '_thumb.' . $info->getExtension(), $path);
//                    $image = Image::open($info->getRealPath());
//                    $image->thumb(300, 300)->save('.' . str_replace('_thumb', '_thumb300', $thumbPath));
//                    $image->thumb(100, 100)->save('.' . str_replace('_thumb', '_thumb100', $thumbPath));

                    return ['src' => $path, 'alt'];
                } else {
                    abort(500, $file->getError());
                }
            }
        } catch (\Exception $e) {
            return errorJson($e->getMessage());
        }
    }

    /**
     * 图片上传
     * @return array|string
     */
    public function editor()
    {
        try {
            $file = $this->request->instance()->file('file');
            if ($file === null) {
                return ['success' => false, 'msg' => '请选择上传文件'];
            } else {
                $validate = [
                    'size' => 1024 * 1024 * 2,
                    'ext' => 'jpg,png,jpeg',
                    'type' => 'image/jpeg,image/png'
                ];
                $info = $file->validate($validate)->move(R_RES_IMAGES);
                if ($info) {
                    $file_path = $info->getSaveName();
                    //记录上传图片
//                    $imageModel = new Images;
//                    $imageModel->file_path = $file_path;
//                    $imageModel->save();

                    //生成缩略图
                    $path = RES_IMAGES . DS . $file_path;
//                    $thumbPath = str_replace('.' . $info->getExtension(), '_thumb.' . $info->getExtension(), $path);
//                    $image = Image::open($info->getRealPath());
//                    $image->thumb(300, 300)->save('.' . str_replace('_thumb', '_thumb300', $thumbPath));
//                    $image->thumb(100, 100)->save('.' . str_replace('_thumb', '_thumb100', $thumbPath));

                    return ['success' => true, 'msg' => '上传成功', 'file_path' => $path];
                } else {
                    return ['success' => false, 'msg' => $file->getError()];
                }
            }
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }
}
