<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:77:"/home/wwwroot/default/public/../application/admin/view/document/question.html";i:1494583455;s:66:"/home/wwwroot/default/public/../application/admin/view/header.html";i:1483770727;s:67:"/home/wwwroot/default/public/../application/admin/view/loading.html";i:1481350888;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
<title></title>
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<!--<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">-->
<link rel="stylesheet" href="__LAYUI__/css/layui.css" media="all">
<link rel="stylesheet" href="__LAYUI__/css/style.css?<?php echo time(); ?>" media="all">
<link rel="stylesheet" href="//at.alicdn.com/t/font_gmdaa4vgna0s5rk9.css" media="all">
<script src="__LAYUI__/layui.js" charset="utf-8"></script>
<script>
    layui.config({
        base: '__LAYUI__/lay/extend/',
        debug: false
    });
</script>
</head>
<body>
<script src="__LAYUI__/loading.js"></script>
<div class="layui-main">
    <fieldset class="layui-elem-field layui-field-title">
        <legend>问题回复</legend>
    </fieldset>
    <table class="table">
        <thead>
        <tr>
            <th>问题</th>
            <th>提问时间</th>
            <th>回复</th>
            <th>回复时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$item): ?>
        <tr>
            <td><?php echo $item['question']; ?></td>
            <td><?php echo $item['question_time']; ?></td>
            <td><?php echo $item['answer']; ?></td>
            <td><?php echo $item['answer_time']; ?></td>
            <td>
                <?php if(empty($item['answer'])): ?>
                <a href="javascript:;" data-id="<?php echo $item->id; ?>" class="layui-btn layui-btn-mini layui-btn-normal detail">
                    回复
                </a>
                <?php else: ?>
                已回复
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="99">
                <div class="pagination-box">
                    <span class="info">
                        共<?php echo $list->total(); ?>条数据 <?php echo $list->currentPage(); ?>/<?php echo $list->lastPage(); ?>页
                    </span>
                    <?php echo $list->render(); ?>
                </div>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<script>
    layui.use(['base', 'form'], function () {
        var $ = layui.jquery, base = layui.base();
        $(".detail").click(function () {
            var id = $(this).data("id");
            layer.prompt({
                formType: 2,
                value: '',
                title: '请输入回复内容',
                area: ['800px', '350px']
            }, function (value, index, elem) {
                base.request.post({
                    url: "",
                    data: {id: id, content: value},
                    success: function (res) {
                        if (res.code == 200) {
                            layer.msg(res.message, function () {
                                window.location.reload();
                            });
                        } else {
                            layer.msg(res.message);
                        }
                    }
                });
                layer.close(index);
            });
        });
    });
</script>
</body>
</html>