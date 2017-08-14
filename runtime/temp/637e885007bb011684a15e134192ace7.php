<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:73:"/home/wwwroot/default/public/../application/api/view/document/detail.html";i:1486536005;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $data->title; ?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" type="text/css" href="/static/simditor/styles/simditor.css"/>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        img {
            max-width: 100%;
            height: auto !important;
        }

        .simditor {
            border: 0;
            padding: 10px;
        }

        .simditor .simditor-body {
            padding: 0;
        }

        .simditor .simditor-body img, .editor-style img {
            margin: 0;
        }
    </style>
</head>
<body>
<div class="simditor">
    <div class="simditor-body">
        <?php echo $data->content; ?>
    </div>
</div>
</body>

</html>