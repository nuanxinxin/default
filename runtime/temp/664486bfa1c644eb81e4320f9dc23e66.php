<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:74:"D:\project\default\public/../application/admin\view\pub\company_login.html";i:1501051618;}*/ ?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <title><?php echo $data['title']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/static/login//css/reset.css">
    <link rel="stylesheet" href="/static/login//css/supersized.css">
    <link rel="stylesheet" href="/static/login//css/style.css">

</head>

<body>

<div class="page-container">
    <h1><?php echo $data['title']; ?></h1>

    <form action="" method="post">
        <input type="text" name="username" class="username" autocomplete="off" placeholder="用户名">
        <input type="password" name="password" class="password" placeholder="密码">
        <!--<input type="text" name="code" placeholder="验证码">-->
        <!--<div><?php echo captcha_img(); ?></div>-->
        <button type="submit">登录</button>
    </form>
</div>

<script src="/static/login/js/jquery-1.8.2.min.js"></script>
<script src="/static/login//js/supersized.3.2.7.min.js"></script>
<script src="/static/login//js/supersized-init1.js"></script>
<script>
    $(function () {
        $("input[name=code]").focus(function(){

        });
        $("form").submit(function () {

        });
    });
</script>

</body>

</html>