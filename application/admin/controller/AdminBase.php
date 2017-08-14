<?php

namespace app\admin\controller;

use think\Controller;

class AdminBase extends Controller
{
    public function _initialize()
    {
        parent::_initialize();

        //获取登录类型
        define('LOGIN_TYPE', session('login_type'));
        switch (LOGIN_TYPE) {
            case 'admin':
                if (!session('admin')) {
                    $this->redirect('admin/Pub/login');
                }
                break;
            case 'company':
                if (!session('company')) {
                    $this->redirect('admin/Pub/company');
                }
                break;
            default:
                exit('404');
                break;
        }

        //验证表单令牌，控制器Upload所有方法免验证
        if ($this->request->isPost() && $this->request->controller() != 'Upload') {
            if ($this->request->param('__token__') != session('__token__')) {
//                $this->error('非法操作！');
            }
        }
    }
}