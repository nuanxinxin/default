<?php

namespace app\api\controller;

use think\Controller;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\News;
use app\common\model\User;
use app\common\model\AdminCompany;
use app\common\model\Document;

class Server extends Controller
{
    private $options = [
        'debug' => false,
        'app_id' => 'wxb1993648e68557fd',
        'secret' => 'c2796e3276cfb19f6e4ce0a88c19fcd3',
        'token' => 'easywechat',
        'aes_key' => 'WZm4U6pdTFTy1AGa7rHG03HJLev9BsvMkNXsGZNuAOp'
    ];

    public function index()
    {

        $app = new Application($this->options);

        $app->server->setMessageHandler(function ($message) use (&$app) {
            switch ($message->MsgType) {
                case 'event':

                    if ($message->Event == 'subscribe') {
//                        file_put_contents('./text.txt', $message);
                        $user = $app->user->get($message->FromUserName);
                        $parent_id = 0;
                        $company_id = 1;
                        if (isset($message->EventKey) && $message->EventKey != '') {
                            $str = str_replace('qrscene_', '', $message->EventKey);
                            list($u, $c) = explode(',', $str);
                            list($user_name, $parent_id) = explode(':', $u);
                            list($company_name, $company_id) = explode(':', $c);
                        }

                        //新用户注册
                        $model = new User;
                        if (!$model->where('wx', $message->FromUserName)->count()) {
                            $model->wx = $message->FromUserName;
                            $model->nickname = $user->nickname;
                            $model->company_id = $company_id;
                            $model->city = $user->city;
                            $model->thumb = $user->headimgurl;
                            $model->parent_id = $parent_id;
                            $model->addUser();
                        }

                        $document = Document::get(5);
                        $news = new News([
                            'title' => $document['title'],
                            'description' => '',
                            'url' => 'http://admin.sqstz360.com/document/5.html',
                            'image' => fullPath($document['pic'])
                        ]);
                        return $news;
                    }
                    break;
            }
        });
        $response = $app->server->serve();
        return $response->send();
    }

    public function menu()
    {
        return '';
        $app = new Application($this->options);
        $menu = $app->menu;

        $buttons = [
            [
                "type" => "view",
                "name" => "立即登录",
                "url" => "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxb1993648e68557fd&redirect_uri=http://wx.sqstz360.com/&response_type=code&scope=snsapi_base&state=1#wechat_redirect"
            ]
        ];
        $menu->add($buttons);
    }
}