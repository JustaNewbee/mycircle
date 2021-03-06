<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>我的兴趣圈-MyCircle</title>
    <link href="/mycircle/Public/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/mycircle/Public/CSS/main-style.css" rel="stylesheet" type="text/css" media="all">
    <link href="/mycircle/Public/CSS/signup-style.css" rel="stylesheet" type="text/css" media="all">
    <link href="/mycircle/Public/CSS/pagination.css" rel="stylesheet" type="text/css" >
    <link rel="stylesheet" href="/mycircle/Public/layui/css/layui.css">
    <script src="/mycircle/Public/js/jquery-3.2.1.js"></script>
    <script>
        var MODULE = "/mycircle";
    </script>
    <script src="/mycircle/Public/js/my_circle.js"></script>
</head>
<body>
<div class="main-body circlepage">
    <header>
    <div class="nav_container bg">
        <div class="nav-menu fl">
            <a href="/mycircle" class="fl">
                <img src="/mycircle/Public/img/logo.png" class="logo">
            </a>
            <ul class="nav-menu-list fl">
                <li><a href="/mycircle">首页</a></li>
                <li><a href="/mycircle/Circle">兴趣圈</a></li>
                <li class="li-bottom"></li>
            </ul>
        </div>
        <div class="search-field">
            <form id="#search">
                <input type="search"  class="search" name="search" id="input_search" maxlength="20"/>
                <a class="glyphicon glyphicon-search" name="searchSubmit" id="search-btn"></a>
            </form>
        </div>
        <div class="fr nav-user">
            <div class="fl user-status">
                <ul class="user-status-list">
                    <li>
                        <a class="top-face face fl">
                            <img src="/mycircle/Public/img/akari.jpg" class="img-face" alt="头像">
                        </a>
                        <ul class="user-dropdown-menu"></ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
    <div class="container">
        <aside class="data-bar">
    <h2>个人中心</h2>
    <nav>
        <ul>
            <li><a href="mydata">我的信息</a></li>
            <li><a href="mycircle">我的兴趣圈</a></li>
            <li><a href="mypost">我的文章</a></li>
            <li><a href="setting">设置</a></li>
        </ul>
    </nav>
</aside>
        <div class="wrapper">
            <div class="join-circle-wrapper">
                <h3 class="title">我加入的：</h3>
                <div class="join-circle-list M-box m-style"></div>
            </div>
            <div class="create-circle-wrapper">
                <h3 class="title">我创建的：</h3>
                <div class="create-circle-list M-box m-style"></div>
            </div>
        </div>
    </div>
    <p class='tip'>审核中……</p>
    <ul class="bg-bubbles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</div>
</body>
<script src="/mycircle/Public/bootstrap/js/bootstrap.min.js"></script>
<script src="/mycircle/Public/js/jquery.pagination.js"></script>
<script src="/mycircle/Public/layui/layui.js"></script>
<script>
    var total = "<?php echo ($total); ?>";
    var show = 6;
    var current;
    var current2;
    var total_create = "<?php echo ($total_create); ?>";
    $(function () {
        $('.data-bar a:eq(1)').addClass('active');
        total = Math.ceil(total/show);
        $('.join-circle-wrapper .M-box').pagination({
            showData:  show,
            pageCount: total,
            mode: 'fixed',
            callback: function (api) {
                current = api.getCurrent();
                getCircleList(current,show);

            }
        },function (api) {
            current = api.getCurrent();
            getCircleList(current,show);
            if(show>='<?php echo ($total); ?>') {
                $('.join-circle-wrapper .page-wrapper').remove();
            }
        });
        total_create = Math.ceil(total_create/3);
        $('.create-circle-wrapper .M-box').pagination({
            showData:  3,
            pageCount: total_create,
            mode: 'fixed',
            callback: function (api) {
                current2 = api.getCurrent();
                getCreateCircleList(current2,3);

            }
        },function (api) {
            current2 = api.getCurrent();
            getCreateCircleList(current2,3);
            if('<?php echo ($total_create); ?>'<=3) {
                $('.create-circle-wrapper .page-wrapper').remove();
            }
        });
        $('.create-circle-wrapper').on('click','.locked',function (e) {
            e.stopPropagation();
        }).on('mouseover','.locked',function (e) {
            $('.tip').css({'display':'block','top':e.pageY+10,'left':e.pageX+10});
        }).on('mouseout','.locked',function () {
            $('.tip').css('display','');
        });

        $('.wrapper').on('click','.btn-card',function () {
            var id = $(this).parent(".card").data("id");
            if($(this).hasClass('detail')){
                window.open(MODULE+"/Circle/"+id);
            }
            if($(this).hasClass('edit')){
                window.open(MODULE+"/Article/write?circle="+id);
            }
            if($(this).hasClass('quit')){
                layui.use('layer', function(){
                    var layer = layui.layer;
                    layer.confirm('确定退出？',function (index) {
                        $.post(MODULE+"/Circle/quit",{circle_id:id},function(data){
                            if(!data){
                                $('.card').remove();
                                getCircleList(current,show);
                            }
                        });
                        layer.close(index);
                    });
                });
            }
            if($(this).hasClass('admin')){
                window.open(MODULE + '/MyAdmin/mycircle?circle=' + $(this).parents(".card").data("id"));
            }
        });

    });
    function getCircleList(current,page) {
        $.ajax({
            type:'post',
            url: MODULE+'/Account/getMyCircle',
            data: {current:current,page:page},
            success:function (data) {
                for(var i=0;i<data.length;i++){
                    circleDisplay($('.join-circle-list'),data[i]);
                }
            },error:function () {
                alert('get circle error');
            }
        })
    }
    function getCreateCircleList(current,page) {
        $.ajax({
            type: 'post',
            url: MODULE + '/Account/getMyCreateCircle',
            data: {current:current,page:page},
            success:function (data) {
                for(var i=0;i<data.length;i++){
                    circleDisplay($('.create-circle-list'),data[i]);
                }
            },error:function () {
                alert('get circle error');
            }
        })
    }
    function circleDisplay(obj,data) {

        var model = '<div class="card" data-id="'+data['circle_id']+'">\n' +
            '                <img src="'+data['circle_avatar']+'">\n' +
            '                <span class="title">'+data['circle_name']+'</span>\n' +
            '                <span class="intro">'+data['circle_intro']+'</span>\n' +
            '                <span class="divider"></span>\n' +
            '                <span class="btn-card quit">退出</span>\n' +
            '                <span class="btn-card edit">发文</span>\n' +
            '                <span class="btn-card detail">进入</span>\n' +
            '            </div>';
        if(obj.is('.create-circle-list')) {
             model = '<div class="card" data-id="'+data['circle_id']+'">\n';
             if(data['checked']=='unpass'){
                 model = '<div class="card locked" data-id="'+data['circle_id']+'">\n';
             }
             model += '                <img src="'+data['circle_avatar']+'">\n' +
                '                <span class="title">'+data['circle_name']+'</span>\n' +
                '                <span class="intro">'+data['circle_intro']+'</span>\n' +
                '                <span class="divider"></span>\n' +
                '                <span class="btn-card admin">管理</span>\n' +
                '                <span class="btn-card edit">发文</span>\n' +
                '                <span class="btn-card detail">进入</span>\n' +
                '            </div>';
        }
        if(data.length==0) model = '<p>暂无</p>';
        obj.append(model);
    }
</script>
</html>