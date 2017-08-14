<?php
namespace app\admin\controller;

//后台主页面
class Index extends AdminBase
{
    public function index()
    {
        return $this->fetch();
    }

    public function home()
    {
        return $this->fetch();
    }
}
