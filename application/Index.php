<?php
namespace app\index\controller;


use app\common\model\Good as GoodModel;
use app\common\model\Order as OrderModel;
use app\common\model\User as UserModel;
class Index extends Common
{
    public function index()
    {
        $isMore=input("param.isMore");


        $cards_100=GoodModel::where('good_type', 1)->order('sort_order asc','good_id desc')->select();

        $cards_50=GoodModel::where('good_type', 0)->order('sort_order asc','good_id desc')->select();


        //最新参与  延迟30秒显示
        $time=time()-30;
        $time=date("Y-m-d H:i:s",$time);
        if($isMore){
            $order=OrderModel::whereTime("create_time",'<=',$time)->where("o_status",0)->order('create_time desc')->paginate(50);
            foreach($order as $key=>$val){
            	if($val['order_amount']>7){
       				$random=mt_rand('1','5');
       				$order[$key]['order_amount']=$val['order_amount']*$random;
            		
            	}
            	
            }
        }else{
            $order=OrderModel::whereTime("create_time",'<=',$time)->where("o_status",0)->order('create_time desc')->paginate(getPageSize());
            foreach($order as $key=>$val){
            	if($val['order_amount']>7){
            		$random=mt_rand('1','5');
            		$order[$key]['order_amount']=$val['order_amount']*$random;
            		
            	}
            	
            }
        }



        //最新夺宝订单

        if($isMore){
            $winOrder=OrderModel::where('order_status', 1)->where("o_status",0)->order('create_time desc')->paginate(50);
        }else{
            $winOrder=OrderModel::where('order_status', 1)->where("o_status",0)->whereTime("create_time",'<=',$time)->order('create_time desc')->paginate(getPageSize());
        }







        return $this->fetch('content/index',['cards_100' => $cards_100,'cards_50' => $cards_50,'order' => $order,'winOrder' => $winOrder,'isMore' => $isMore]);



    }


    /**
     * 注册手机号码验证
     */
    public function  regMobile()
    {
        if (isAjax()) {
            $postData=input("post.");
            if($postData['agent_id'] == ''){
                error("请输入推广编号！");
            }
            if($postData['account'] == ''){
                error("请输入手机号！");
            }
           $postData['code']!= session("verify") && error("验证码错误！");
			
            //判断此号码是否进行过注册
            $hasRegMobile=\app\common\model\User::where("user_status",0)->where(array('phone'=>array('eq',$postData['account'])))->count();
            $hasRegMobile &&  error("该手机号已经注册了请使用其他号码注册!");

            //判断推广编号是否存在
            $config_yewu = cache('config_yewu');
            if($postData['agent_id'] == $config_yewu['operate_agent_code']){//运营邀请编号
                $that_user = \app\common\model\User::get(['user_id' => $postData['user_id']]);
                $parentUserId = $that_user->user_id;
                $company_agent = $that_user->user_id;
                $from_sys = 1;
            }else{
                $parent = \app\common\model\User::where("agent_id",$postData['agent_id'])->find();

                $parentUserId = $parent->user_id;
                !$parentUserId &&  error("该推广编号无效!");
                
                if($parent->getData('user_type') == '2'){
                    $company_agent = $parent->user_id;
                }elseif($parent->getData('user_type') == '1'){
                    $company_agent = $parent->agent;
                }else{
                    error("推广类型不正确!");
                }

            

                $from_sys = 0;
            }


            if(\app\common\model\User::update(["phone"=>$postData['account'],'agent'=>$parentUserId,'company_agent'=>$company_agent,'from_sys'=>$from_sys],['user_id' => $postData['user_id']])){

                session("verify",null);
                return success("操作成功!");
            }

        }
    }


    /**
     * 获取注册短信验证码
     */
    public function getMsg()
    {


        if (isAjax()) {

            $param=input("post.param");
            $param=json_decode($param,true);

            if(isset($param['account'])){


                //判断此号码是否进行过注册
                $hasRegMobile=\app\common\model\User::where("user_status",0)->where(array('phone'=>array('eq',$param['account'])))->count();
                $hasRegMobile &&  error("该手机号已经注册了请使用其他号码注册!");







                $verify = rand(123456, 999999);//获取随机验证码
                $content=sprintf("%s(验证码)10分钟内有效!",$verify);
                sendMsg($param['account'],$content);
                session("verify",$verify);
                return success("发送短信验证码成功!",$verify);
            }

        }
    }


    /**
     * 注册查询密码
     */
    public function  regPwd()
    {
        if (isAjax()) {
            $postData = input("post.");

            $postData['code'] != session("verify2") && error("验证码错误！");
            $user_id=intval($postData['user_id']);
            $user=UserModel::get($user_id);
            $user->query_password=$postData['pwd'];
            $user->save();
            session("verify2",null);
            return success("操作成功!");

        }
    }


    /**
     * 获取查询密码短信验证码
     */
    public function getMsg2()
    {



        if (isAjax()) {

            $param=input("post.param");
            $param=json_decode($param,true);

            if(isset($param['user_id'])){


                $user_id=intval($param['user_id']);
                $user=UserModel::get($user_id);

                $verify = rand(123456, 999999);//获取随机验证码
                $content=sprintf("%s(验证码)10分钟内有效!",$verify);
                sendMsg($user->phone,$content);
                session("verify2",$verify);
                return success("发送短信验证码成功!",$verify);
            }

        }
    }

}
