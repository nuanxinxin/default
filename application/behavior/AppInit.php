<?php
namespace app\behavior;

class AppInit
{

    public function run(&$param)
    {
        //容错处理
        error_reporting(E_ERROR | E_PARSE);

        define('PUB', 'public');
        define('RES', DS . 'uploads');
        define('RES_IMAGES', RES . DS . 'images');
        define('RES_FILES', RES . DS . 'files');
        define('R_RES_IMAGES', ROOT_PATH . PUB . RES_IMAGES);
        define('R_RES_FILES', ROOT_PATH . PUB . RES_FILES);

        define('IMAGES', '../images');
    }

}