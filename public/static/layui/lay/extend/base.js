layui.define(["jquery", "layer", "element"], function (exports) {
    "use strict";
    var j = layui.jquery;
    var layer = layui.layer;
    var element = layui.element();

    //左侧菜单点击
    j(".iframe-item").click(function () {
        var a = j(this), url = a.data("url");
        j(".layui-menu .item").removeClass("active");
        a.addClass("active");
        j("#main").attr("src", url);
    });

    //导航页面切换
    element.on('tab(nav_box)', function () {
        //关闭动态添加的选项卡页面
        j(j(this).siblings("li[class=append]")).each(function (index, item) {
            element.tabDelete('nav_box', j(item).index());
        });
        j("iframe").attr("src", j(this).data("url"));
    });

    //表格空数据填充提示信息
    if (j(".table tbody").length > 0 && j(".table tbody tr").length === 0) {
        j(".table tbody").html('<tr><td colspan="99" style="color:#34a8fe;">没有符合条件的记录</td></tr>');
    }

    //关闭表单自动记录
    j("input[type=text]").attr("autocomplete", "off");

    //全选
    j(".checkbox-all").on("click", function (event) {
        if (event.target != this) {
            var id = j(this).attr("lay-filter");
            if (j(this).children("input").is(':checked')) {
                j("input[lay-filter=" + id + "]").prop("checked", true);
            } else {
                j("input[lay-filter=" + id + "]").prop("checked", false);
            }
        }
    });

    //删除记录提醒
    j("a.del").click(function () {
        var url = j(this).attr('href');
        layer.confirm('你确定要删除吗?', {
            title: '安全提示',
            icon: 3,
            btn: ['确定', '取消']
        }, function () {
            window.location.href = url;
        });
        return false;
    });


    var base_function = {
        //全屏弹出层
        fullBox: function (options) {
            var opts = j.extend({type: 2, area: ['100%', '100%']}, options);
            layer.open(opts);
        },
        //获取全选值
        getIds: function (id) {
            var ids = [];
            j(id).each(function (index, item) {
                ids.push(j(item).data("id"));
            });
            return ids.join(',');
        },
        //表格checkbox点击修改值
        checkboxEditValue: function (name, url, request) {
            j("input[name=" + name + "]").click(function () {
                var value = j(this).is(":checked") === true ? 1 : 0;
                var id = j(this).data("id");
                this.request.post({
                    url: url,
                    data: {field: value, act: name, id: id},
                    success: function (res) {
                    }
                });
            });
        },
        //新增一个选项卡页面
        addTabView: function (title, url) {
            var element = window.parent.layui.element();
            var document = j(window.parent.document);
            var obj = document.find(".layui-tab-title li:contains(" + title + ")");
            //已存在关闭
            if (obj.length > 0) {
                element.tabDelete("nav_box", obj.index());
            }
            element.tabAdd("nav_box", {title: title});
            document.find(".layui-tab-title li:last").attr("data-url", url).addClass("append").click();
        },
        //关闭当前选项卡页面
        closeCurrentTabView: function (title, showIndex) {
            var document = j(window.parent.document);
            document.find(".layui-tab-title li:eq(" + showIndex + ")").click();
            window.parent.layui.element().tabDelete("nav_box", document.find(".layui-tab-title li:contains(" + title + ")").index());
        },
        //ajax请求
        request: {
            get: function (options) {
                var opts = j.extend({type: "get"}, this.default, options);
                j.ajax(opts);
            },
            post: function (options) {
                var opts = j.extend({type: "post"}, this.default, options);
                j.ajax(opts);
            },
            default: {
                loading: null,
                url: "",
                data: {},
                dataType: "json",
                async: true,
                cache: true,
                password: "",
                timeout: 30000,
                contentType: "application/x-www-form-urlencoded",
                beforeSend: function () {
                    this.loading = layer.load(2, {time: 10 * 1000});
                },
                complete: function () {
                    layer.close(this.loading);
                },
                success: function (result) {
                    layer.msg(result.message, {icon: 1, time: 1000}, function () {
                        window.location.reload();
                    });
                },
                error: function (e) {
                    layer.msg(JSON.parse(e.responseText).message, {icon: 2, time: 1000});
                },
            }
        },
        upload: {
            config: {
                url: '',
                btnText: '选择',
                accept: '*',
                maxUploadFileSize: 1024 * 1024 * 50,
                success: function (data, o) {
                },
                error: function (s) {
                    layer.msg(s, {icon: 2, time: 1000});
                },
                progress: function (percent, o) {
                    o.siblings('.percent').css({width: percent * 3 + 'px'});
                },
                progressOk: function (o) {
                    o.siblings('.percent').css({width: 0});
                }
            },
            setConfig: function (options) {
                this.config = j.extend({}, this.config, options);
                return this;
            },
            initCss: function () {
                j('.image_upload,.file_upload').css({
                    position: 'relative',
                    height: '38px'
                }).find('button').css({
                    backgroundImage: 'url(/static/layui/images/upload-icon.png)',
                    backgroundRepeat: 'no-repeat',
                    backgroundSize: '21px',
                    backgroundPosition: '10px',
                    paddingLeft: '35px',
                    paddingRight: '10px'
                });
            },
            image_InitView: function (add) {//图片上传初始化
                var u = this, config = u.config, o = j(".image_upload");
                if (typeof add == 'undefined') {
                    o.each(function () {
                        var item = j(this);
                        item.html(u.image_FormHtml(item.data('name'), item.data('value')));
                    });
                } else {
                    var item = o.last();
                    item.html(u.image_FormHtml(item.data('name'), item.data('value')));
                }
                u.bindClickEvent(o.find(".select_file_button")).bindChangeEvent(o.find("input[type=file]")).bindShowImageEvent(j('.show_image'));
            },
            image_InitViewEditor: function () {//editor图片上传初始化
                var u = this;
                u.bindClickEvent(j(".layedit-tool-image")).bindChangeEvent(j(".layedit-tool-image input[type=file]"));
            },
            file_InitView: function (add) {//文件上传初始化
                var u = this, config = u.config, o = j(".file_upload");
                if (typeof add == 'undefined') {
                    o.each(function () {
                        var item = j(this);
                        item.html(u.file_FormHtml(item.data('name'), item.data('value')));
                    });
                } else {
                    var item = o.last();
                    item.html(u.file_FormHtml(item.data('name'), item.data('value')));
                }
                u.bindClickEvent(o.find(".select_file_button")).bindChangeEvent(o.find("input[type=file]"));
            },
            bindClickEvent: function (o) {//绑定按钮点击事件
                this.initCss();
                o.unbind().click(function () {
                    j(this).siblings('input[type=file]').click();
                });
                return this;
            },
            bindChangeEvent: function (o) {//绑定inputfile改变事件
                var c = this.config;
                o.unbind().change(function (e) {
                    var f = j(this);
                    if (f.val() == '') {
                        return false;
                    }
                    var fo = f[0].files[0];
                    if (fo.size > c.maxUploadFileSize) {
                        f.val('');
                        c.error('超出允许上传文件大小限制！');
                        return false;
                    }
                    var fd = new FormData();
                    fd.append('file', fo);
                    j.ajax({
                        url: c.url,
                        type: 'post',
                        data: fd,
                        dataType: 'json',
                        cache: false,
                        processData: false,
                        contentType: false,
                        xhr: function () {
                            var xhr = j.ajaxSettings.xhr();
                            xhr.upload.onload = function () {
                                c.progressOk(f);
                            }
                            xhr.upload.onprogress = function (ev) {
                                if (ev.lengthComputable) {
                                    var percent = 100 * ev.loaded / ev.total;
                                    c.progress(parseInt(percent), f);
                                }
                            }
                            return xhr;
                        },
                        success: function (result) {
                            c.success(result, f);
                        },
                        error: function (e) {
                            c.error(JSON.parse(e.responseText).message);
                        },
                        complete: function () {
                            f.val('');//重置inputfile
                        }
                    });
                });
                return this;
            },
            bindShowImageEvent: function (o) {//绑定鼠标进入显示图片事件
                o.unbind().hover(
                    function () {
                        j(this).next().css('display', 'block');
                    },
                    function () {
                        j(this).next().css('display', 'none');
                    }
                );
                return this;
            },
            image_FormHtml: function (name, value) {//表单初始化
                typeof value == 'undefined' && (value = '');
                var c = this.config;
                return '<input type="text" name="' + name + '" value="' + value + '" class="layui-input" style="vertical-align:middle;width:360px;height:100%;line-height:100%;"/><button type="button" class="layui-btn layui-btn-normal select_file_button" style="margin-left:5px;height:100%;line-height:100%;">' + c.btnText + '</button><input type="file" style="display:none;"/><span class="image-box" style="margin-left:5px;display:inline-block;width:auto;height:100%;position:relative;"><img src="/static/layui/images/show_image.png" class="show_image" style="height:100%;"/><div class="type-file-preview" style="position:absolute;z-index:99;display:none;"><img src="' + value + '" style="border:6px solid #1E9FFF;max-width:100px;"/></div></span><div class="percent" style="background:#5fb878;height:100%;width:0;position:absolute;top:0;bottom:0;"></div>';
            },
            file_FormHtml: function (name, value) {
                var c = this.config;
                typeof value == 'undefined' && (value = '');
                return '<input type="text" name="' + name + '" value="' + value + '" class="layui-input" style="vertical-align:middle;width:360px;height:100%;line-height:100%;"/><button type="button" class="layui-btn layui-btn-normal select_file_button" style="margin-left:5px;height:100%;line-height:100%;">' + c.btnText + '</button><input type="file" style="display:none;"/><div class="percent" style="background:#5fb878;height:100%;width:0;position:absolute;top:0;bottom:0;"></div>';
            }
        },
        tableEdit: {
            initEvent: function () {
                j("table tbody tr td[edit=true]").dblclick(function () {
                    var t = j(this);
                    if (!t.data('lock')) {
                        var value = t.text();
                        t.data('lock', 1).addClass('close-checked').html('<input class="layui-input" style="vertical-align:middle;margin-right:5px;height:22px;line-height:22px;" value="' + value + '"><button type="button" class="layui-btn layui-btn-mini">保存</button>');

                        t.find('button').click(function () {
                            var url = t.data('url'), name = t.data('name'), value = t.find('input').val();
                            base_function.request.post({
                                url: url,
                                data: {name: name, value: value},
                                success: function () {
                                }
                            });
                            t.removeData('lock').removeClass('close-checked').text(value);
                        });
                    }
                }).css({cursor: 'pointer'});
            },
        },
        color: {
            config: {
                colors: ['#660099', '#009688', '#5FB878', '#393D49', '#1E9FFF', '#F7B824', '#FF5722']
            },
            initEvent: function () {
                var span = j("span.color-select"), colors = this.config.colors, name = span.data('name'), value = span.data('value');
                span.append('<input type="hidden" name="' + name + '" value="' + value + '"><div style="width:100%;height:2px;position:absolute;top:38px;"></div>').css({
                    display: 'inline-block',
                    height: '38px',
                    width: '38px',
                    verticalAlign: 'middle',
                    backgroundColor: value ? value : '#393D49',
                    cursor: 'pointer',
                    position: 'relative',
                    borderRadius: '2px',
                    backgroundImage: 'url(/static/layui/images/color-select.png)',
                    backgroundSize: '65%',
                    backgroundRepeat: 'no-repeat',
                    backgroundPosition: 'center',
                    marginLeft: '2px'
                }).hover(function () {
                    var h = '<div class="color-box" style="width: 100%;background:#333;z-index:99;position:absolute;top:40px;">';
                    for (var i = 0; i < colors.length; i++) {
                        h += '<div data-value="' + colors[i] + '" style="height:21px;background:' + colors[i] + '"></div>';
                    }
                    h += '</div>';
                    j(this).append(h);

                    j(this).find('div>div').click(function () {
                        var value = j(this).data('value');
                        j(this).parent().parent().css({backgroundColor: value}).find('input').val(value).siblings('.color-box').remove();
                    });
                }, function () {
                    j(this).find('.color-box').remove();
                });
            }
        }
    }

    //自动初始化upload.image
    if (j('.image_upload').length > 0) {
        base_function.upload.setConfig({
            url: "/upload-image",
            btnText: '选择图片',
            success: function (data, o) {
                var input_text = o.siblings('input'), image = o.siblings('.image-box').find('.type-file-preview img');
                input_text.val(data.src);
                image.attr("src", data.src);
            }
        }).image_InitView();
    }
    //自动初始化upload.file
    if (j('.file_upload').length > 0) {
        base_function.upload.setConfig({
            url: "/upload-image",
            btnText: '选择文件',
            success: function (data, o) {
                var input_text = o.siblings('input');
                input_text.val(data.path);
            }
        }).file_InitView();
    }

    //自动初始化tableEdit
    if (j("table tbody tr td[edit=true]").length > 0) {
        base_function.tableEdit.initEvent();
    }

    //自动初始化color
    if (j("span.color-select").length > 0) {
        base_function.color.initEvent();
    }

    //自动初始化时间组
    if (j(".date-group").length > 0) {
        var o = j(".date-group");
        layui.use('laydate', function () {
            var laydate = layui.laydate;
            console.log(o);
            var start = {
                istoday: true
                , choose: function (datas) {
                    end.min = datas; //开始日选好后，重置结束日的最小日期
                    end.start = datas //将结束日的初始值设定为开始日
                }
            };

            var end = {
                istoday: true
                , choose: function (datas) {
                    start.max = datas; //结束日选好后，重置开始日的最大日期
                }
            };

            o.find(".range_s").click(function () {
                start.elem = this;
                laydate(start);
            });
            o.find(".range_e").click(function () {
                end.elem = this
                laydate(end);
            });
        });
    }

    exports('base', function () {
        return base_function;
    });
});

//===============upload.image,upload.file用法START=================
//html部分
//<div class="image_upload" data-name="" data-value=""></div>
//<div class="file_upload" data-name="" data-value=""></div>
//===============uploadImage用法END===================

//===============tableEdit用法START===================
//html部分
//<td edit="true" data-name="inputname" data-url="ajaxurl">value</td>
//===============tableEdit用法END=====================

//===============color用法START===================
//html部分
//<span class="color-select" data-name="inputname" data-value="inputvalue"></span>
//===============tableEdit用法END=====================