<?php
/* *
 * 配置文件
 * 版本：1.0
 * 日期：2015-07-19
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究聚合富接口使用，只是提供一个参考
 */
//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

//商户号
$jhf_config['partner']		= '26100526';//
//MD5 key值，可以从数据库获取或配置此处，建议数据库获取
$jhf_config['key']          = 'st2eaagftjakyx5rtcxe0zx0o4uk1tq4';
//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

//签名方式 不需修改，也可以使用RSA
$jhf_config['sign_type']    = strtoupper('MD5');

//字符编码格式 目前支持 gbk 或 utf-8
$jhf_config['input_charset']= strtolower('utf-8');

//ca证书路径地址，用于curl中ssl校验，目前暂不用
$jhf_config['cacert']    = getcwd().'\\cacert.pem';

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$jhf_config['transport']    = 'http';
?>