<?php

/*
 * ************************************************************************
 * ThinkPHP5.0 助手函数扩展
 * 使用方法：公共配置文件修改字段[extra_file_list] EXTEND_PATH . 'helper.php'
 * ************************************************************************
 * requestValue 获取请求参数
 * isPost 判断是否post请求
 * isGet 判断是否get请求
 * isAjax 判断是否ajax请求
 * moduleName 获取模块名称
 * controllerName 获取控制器名称
 * actionName 获取方法名称
 * domain 获取域名
 * clientIp 获取客户端ip
 * fullPath 获取完整路径
 * nowTime 获取当前时间
 * uploadImage 图片上传
 * uploadFile 文件上传
 * minImagePath 获取缩略图地址
 * randNumber 生成随机数字串
 * randString 生成随机md5
 * curlPost post请求
 * curlGet get请求
 * errorJson 返回json格式错误
 * successJson 返回json格式成功
 * verify 验证码
 * encrypt 有时效性加密文本
 * decrypt 解密文本
 * textAreaEncode 文本保存处理
 * textAreaDecode 文本显示处理
 * textSubStr 字符串截取
 * qrCode 生成二维码（支持LOGO）
 * planTask 计划任务
 */

use think\Request;
use think\Image;
use org\Crypt;
use EasyWeChat\Foundation\Application;
/**
 * @param string $url
 * @param string $vars
 * @param bool $suffix
 * @param bool $domain
 * @return string
 */
function path($url = '', $vars = '', $suffix = true, $domain = false)
{
    if (moduleName() == 'admin') {
//        $vars['t'] = randString(null);
    }
    return url($url, $vars, $suffix, $domain);
}

/**
 * @return string
 */
function getNewToken()
{
    return Request::instance()->token();
}

/**
 * @return bool
 */
function isPost()
{
    return Request::instance()->isPost();
}

/**
 * @return bool
 */
function isGet()
{
    return Request::instance()->isGet();
}

/**
 * @return bool
 */
function isAjax()
{
    return Request::instance()->isAjax();
}

/**
 * @return $this|string
 */
function moduleName()
{
    return Request::instance()->module();
}

/**
 * @return $this|string
 */
function controllerName()
{
    return Request::instance()->controller();
}

/**
 * @return string
 */
function actionName()
{
    return Request::instance()->action();
}

/**
 * @return string
 */
function domain()
{
    return Request::instance()->domain();
}

/**
 * @return mixed
 */
function clientIp()
{
    return Request::instance()->ip();
}

/**
 * @param string $path
 * @return string
 */
function fullPath($path = '')
{
    if (empty($path)) {
        return '';
    } elseif (strchr($path, 'http://') || strchr($path, 'https://')) {
        return $path;
    } else {
        return Request::instance()->domain() . $path;
    }
}

/**
 * @param bool $float
 * @return float|int
 */
function nowTime($float = false)
{
    return Request::instance()->time($float);
}


/**
 * @param string $inputname
 * @param array $validate
 * @param string $folder
 * @return string
 */
function uploadImage($folder = 'image', $inputname = '', $validate = [])
{
    $file = Request::instance()->file($inputname);
    if ($file === null) {
        abort('请选择上传文件');
    } else {
        $info = $file->validate($validate)->move(ROOT_PATH . 'public' . DS . 'uploads/' . $folder);
        if ($info) {
            $path = '/uploads/' . $folder . '/' . $info->getSaveName();
            //生成缩略图
            $thumbPath = str_replace('.' . $info->getExtension(), '_thumb.' . $info->getExtension(), $path);
            $image = Image::open($info->getRealPath());
            $image->thumb($image->width() / 2, $image->height() / 2)->save('.' . $thumbPath);
            $data = [
                'type' => $info->getInfo('type'),
                'name' => $info->getInfo('name'),
                'path' => $path,
                'thumb' => $thumbPath,
                'size' => $info->getInfo('size')
            ];
            $commonUpload = new CommonUpload();
            $commonUpload->record($data);
            return json_encode(['path' => $path]);
        } else {
            abort($file->getError());
        }
    }
}

/**
 * @param string $name 表单名称
 * @param string $folder 保存文件夹名称
 * @param array $validate 验证规则(ext|type|size)
 * @param bool $savename
 * @return array
 * @throws Exception
 */
function uploadFile($name = 'file', $folder = 'file', $validate = [], $savename = true)
{
    $file = Request::instance()->file($name);
    if ($file === null) {
        throw new \Exception('$_FILES不存在');
    } else {
        $savename = ($savename === false) ? $file->getInfo('name') : true;
        $info = $file->validate($validate)->move(ROOT_PATH . 'public' . DS . 'uploads/' . $folder, $savename, false);
        if ($info) {
            return ['state' => true, 'path' => '/' . str_replace('\\', '/', strchr($info->getRealPath(), 'uploads'))];
        } else {
            throw new \Exception($file->getError());
        }
    }
}

/**
 * @param $imagePath
 * @return mixed|string
 */
function minImagePath($imagePath)
{
    if ($imagePath == '') {
        return '/static/admin/img/default-image.png';
    }
    $imageExt = strrchr($imagePath, '.');
    $fullPath = fullPath($imagePath);
    if (strchr($fullPath, domain())) {
        return str_replace($imageExt, '_min' . $imageExt, $fullPath);
    } else {
        return $fullPath;
    }
}

/**
 * @param $check
 * @return mixed
 */
function randNumber($check)
{
    if ($check === null) {
        return date('ymdHis') . mt_rand(100, 999);
    } else {
        $value = $check(date('ymdHis') . mt_rand(100, 999));
    }
    return ($value === false) ? randNumber($check) : $value;
}

/**
 * @param $check
 * @return string
 */
function randString($check)
{
    if ($check === null) {
        return md5(date('ymdHis') . mt_rand(100, 999));
    } else {
        $value = $check(md5(date('ymdHis') . mt_rand(100, 999)));
    }
    return ($value === false) ? randString($check) : $value;
}

/**
 * @param $length
 * @return string
 */
function getRandString($length)
{
    $array = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
    shuffle($array);
    $value = '';
    for ($i = 0; $i < $length; $i++) {
        $value .= $array[array_rand($array, 1)];
    }
    return $value;
}


/**
 * @param string $url
 * @param array $data
 * @return mixed
 */
function curlPost($url = '', $data = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * @param string $url
 * @return mixed
 */
function curlGet($url = '')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * @param string $message
 * @param int $code
 * @param array $data
 * @return string
 */
function errorJson($message = '', $code = 1, $data = [])
{
    http_response_code(404);
    if (isPost()) {
        //ajax表单令牌刷新
        $token = randString(null);
        $data['__token__'] = $token;
        if ($message != '请勿重复操作') {
            session('__token__', $token);
        }
    }
    exit(json_encode(['code' => (int)$code, 'message' => (string)$message, 'data' => (array)$data]));
}

/**
 * @param string $message
 * @param int $code
 * @param array $data
 * @return string
 */
function successJson($message = '', $code = 0, $data = [])
{
    http_response_code(200);
    exit(json_encode(['code' => (int)$code, 'message' => (string)$message, 'data' => (array)$data]));
}

/**
 * 验证码
 */
function verify()
{
    $verify = new Verify();
    return $verify->entry();
}

/**
 * @param string $value
 * @param string $key
 * @param int $expire
 * @return string
 */
function encrypt($value = '', $key = '', $expire = 0)
{
    return Crypt::encrypt($value, $key, $expire);
}

/**
 * @param string $value
 * @param string $key
 * @return string
 */
function decrypt($value = '', $key = '')
{
    return Crypt::decrypt($value, $key);
}

/**
 * @param $text
 * @param string $s
 * @return string
 */
function textAreaEncode($text, $s = ',')
{
    $array = explode($s, str_replace(["\r", "\n"], ['', $s], $text));
    foreach ($array as $index => $item) {
        $array[$index] = trim($item);
    }
    return implode($s, array_filter($array));
}

/**
 * @param $text
 * @param string $s
 * @return mixed
 */
function textAreaDecode($text, $s = ',')
{
    return str_replace($s, "\r\n", $text);
}

/**
 * @param string $text
 * @param null $length
 * @return string
 */
function textSubStr($text = '', $length = null)
{
    return mb_substr($text, 0, $length, 'utf-8');
}

/**
 * @param string $content
 * @param string $logo
 * @throws Exception
 */
function qrCode($content = '', $logo = '')
{
    $path = 'uploads/qrcode/';
    if (is_dir($path) || mkdir($path, 0755, true)) {
        $qrCodePath = $path . randString() . '.png';
        QRcode::png($content, $qrCodePath, 'L', 6, 3);
        if ($logo) {
            $image = Image::open($qrCodePath);
            unlink($qrCodePath);
            $image->water($logo, 5)->save($qrCodePath);
        }
        echo 'success';
    } else {
        throw new \Exception('目录创建失败');
    }
}

/**
 * 计划任务
 * 需数据库配合，计划任务表
 * 字段：
 * id
 * title 任务标题
 * state 状态，0停止，1运行
 * cycle_time 任务周期时间，最小单位分钟
 * last_time 最后一次执行时间
 * action 执行方法url
 */
function planTask()
{
    /*
    SET FOREIGN_KEY_CHECKS=0;
    DROP TABLE IF EXISTS `n_plan_task`;
    CREATE TABLE `n_plan_task` (
    `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
    `state` tinyint(1) unsigned DEFAULT '0' COMMENT '1',
    `last_time` int(10) unsigned DEFAULT NULL,
    `cycle_time` int(10) unsigned DEFAULT NULL,
    `action` char(255) DEFAULT NULL,
    `title` char(15) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
    */
    $tasks = db('plan_task')->select();
    foreach ($tasks as $index => $item) {
        if (!$item['state']) continue;
        if ($item['cycle_time'] == 0 || strtotime('-' . $item['cycle_time'] . ' minute', NOW_TIME) >= $item['last_time']) {
            $this->curl($item['action']);
            db('plan_task')->where('id', $item['id'])->setField('last_time', NOW_TIME);
        }
    }
    echo 'success';
}

/**
 * @param array $post
 * @param string $name
 * @param string $scene
 * @throws Exception
 */
function postValidate($post = [], $name = '', $scene = '')
{
    $validate = validate($name);
    $validate->scene($scene);
    if (!$validate->check($post)) {
        throw new \Exception($validate->getError());
    }
}

/**
 * @throws Exception
 */
function postValidateToken()
{
    $__token__ = requestValue('__token__');
    if ($__token__ != session('__token__')) {
        throw new \Exception('请勿重复操作');
    } else {
        session('__token__', null);
    }
}

/**
 * @param $password
 * @return string
 */
function password($password = '')
{
    return md5($password) . '~!@#$%^&*()_+';
}

function arraySort($array = [], $field = '', $rule = SORT_ASC)
{
    $sort = array();
    foreach ($array as $value) {
        $sort[] = $value[$field];
    }
    array_multisort($sort, $rule, $array);
    return $array;
}

/**
 * @param $size
 * @return string
 */
function sizeConversion($size)
{
    if ($size > 1024 * 1024 * 1024) {
        return round(($size / 1024 / 1024.1024), 2) . 'G';
    } elseif ($size > 1024 * 1024) {
        return round(($size / 1024 / 1024), 2) . 'M';
    } elseif ($size > 1024) {
        return round(($size / 1024), 2) . 'Kb';
    } else {
        return $size . 'b';
    }
}

/**
 * @param $text
 * @return bool
 */
function isUrl($text)
{
    return (bool)preg_match("/^http:\/\/|^https:\/\//", $text);
}

/**
 * @param $text
 * @return bool
 */
function isMobilePhone($text)
{
    return (bool)preg_match("/^1(3[0-9]|4[57]|5[0-35-9]|7[01678]|8[0-9])\\d{8}$/", $text);
}

/**
 * 随机手机号
 * @return string
 */
function randPhone()
{
    $array = ['131', '135', '138', '156', '158', '186', '187', '188'];
    shuffle($array);
    return $array[array_rand($array, 1)] . rand(10000000, 99999999);
}

function sendMsg($mobile, $content)
{
    $content = $content . '(验证码)10分钟内有效!';
    $flag = 0;
    $params = '';//要post的数据
    $argv = array(
        'name' => 'dxw543461',     //必填参数。用户账号
        'pwd' => '9FA6EB4D075A98F84F896CF0D451',     //必填参数。（web平台：基本资料中的接口密码）
        'content' => $content,   //必填参数。发送内容（1-500 个汉字）UTF-8编码
        'mobile' => $mobile,   //必填参数。手机号码。多个以英文逗号隔开
        'stime' => '',   //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
        'sign' => '手牵手投资',    //必填参数。用户签名。
        'type' => 'pt',  //必填参数。固定值 pt
        'extno' => ''    //可选参数，扩展码，用户定义扩展码，只能为数字
    );

    foreach ($argv as $key => $value) {
        if ($flag != 0) {
            $params .= "&";
            $flag = 1;
        }
        $params .= $key . "=";
        $params .= urlencode($value);// urlencode($value);
        $flag = 1;
    }
    $url = "http://web.duanxinwang.cc/asmx/smsservice.aspx?" . $params; //提交的url地址
    $con = explode(',', file_get_contents($url));
//    echo file_get_contents($url);exit;
    if ($con[0] == 0) {
        return true;
    } else {
        return $con[1];
    }
}

function sendMsgContent($mobile, $content)
{
	$flag = 0;
	$params = '';//要post的数据
	$argv = array(
			'name' => 'dxw543461',     //必填参数。用户账号
			'pwd' => '9FA6EB4D075A98F84F896CF0D451',     //必填参数。（web平台：基本资料中的接口密码）
			'content' => $content,   //必填参数。发送内容（1-500 个汉字）UTF-8编码
			'mobile' => $mobile,   //必填参数。手机号码。多个以英文逗号隔开
			'stime' => '',   //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
			'sign' => '手牵手投资',    //必填参数。用户签名。
			'type' => 'pt',  //必填参数。固定值 pt
			'extno' => ''    //可选参数，扩展码，用户定义扩展码，只能为数字
	);
	
	foreach ($argv as $key => $value) {
		if ($flag != 0) {
			$params .= "&";
			$flag = 1;
		}
		$params .= $key . "=";
		$params .= urlencode($value);// urlencode($value);
		$flag = 1;
	}
	$url = "http://web.duanxinwang.cc/asmx/smsservice.aspx?" . $params; //提交的url地址
	$con = explode(',', file_get_contents($url));
	//    echo file_get_contents($url);exit;
	if ($con[0] == 0) {
		return true;
	} else {
		return $con[1];
	}
}

function sendTemplateMsg($wx, $data,$templateId)
{
	$options = [
			'debug' => false,
			'app_id' => 'wxb1993648e68557fd',
			'secret' => 'c2796e3276cfb19f6e4ce0a88c19fcd3',
			'token' => 'easywechat',
			'aes_key' => 'WZm4U6pdTFTy1AGa7rHG03HJLev9BsvMkNXsGZNuAOp'
	];
	$app = new Application($options);
	$notice = $app->notice;
	$result = $notice->uses($templateId)->andData($data)->andReceiver($wx)->send();
}






function phone_encode($phone)
{
    return str_replace(substr($phone, 3, 4), '****', $phone);
}