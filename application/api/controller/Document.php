<?php

namespace app\api\controller;

use think\Controller;
use app\common\model\Document as DocumentModel;

class Document extends Controller
{
    public function detail($id)
    {
        $data = DocumentModel::get($id);
        if ($data) {
            $this->assign('data', $data);
            return $this->fetch();
        } else {
            return 'error';
        }
    }

    public function test()
    {
        file_put_contents('./test1.txt', serialize($GLOBALS));
    }

    public function test2()
    {
        dump(unserialize(file_get_contents('./test1.txt')));
    }

    public function test3(){
        print_r($GLOBALS);
    }
}