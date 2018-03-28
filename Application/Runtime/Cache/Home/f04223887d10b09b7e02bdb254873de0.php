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
            <form>
                <input type="search"  class="search" name="search"  maxlength="20"/>
                <a class="glyphicon glyphicon-search" name="searchSubmit"></a>
            </form>
        </div>
        <div class="fr nav-user">
            <div class="fl user-status">
                <ul class="user-status-list">
                    <li>
                        <a href="#">
                            <div class="top-face face fl">
                                <img src="/mycircle/Public/img/akari.jpg" class="img-face" alt="头像">
                            </div>
                        </a>
                        <ul class="user-dropdown-menu">

                        </ul>
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
            <li><a href="/mycircle/Account/mydata">我的信息</a></li>
            <li><a href="/mycircle/Account/mycircle">我的兴趣圈</a></li>
            <li><a href="/mycircle/Account/mypost">我的文章</a></li>
            <li><a href="#">设置</a></li>
        </ul>
    </nav>
</aside>
        <div class="wrapper M-box m-style">
        </div>
    </div>
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
    var show = 3;
    var current;
    $(function () {
        total = Math.ceil(total/3);
        $('.M-box').pagination({
            mode: 'fixed',
            pageCount: total,
            callback: function (api) {
                current = api.getCurrent();
                getCircleList(current,show);
            }
        },function (api) {
            current = api.getCurrent();
            getCircleList(current,show);
        });
        $('.wrapper').on('click','.card a',function () {
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
                                $('.warpper').remove('.card');
                                getCircleList(current,show);
                            }
                        });
                        layer.close(index);
                    });
                });

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
                    circleDisplay($('.wrapper'),data[i]);
                }
            },error:function () {
                alert('get circle error');
            }
        })
    }
    function circleDisplay(obj,data) {
        $model = '<div class="card" data-id="'+data['circle_id']+'">\n' +
            '                <img src="'+data['circle_avatar']+'">\n' +
            '                <span class="title">'+data['circle_name']+'</span>\n' +
            '                <span class="intro">'+data['circle_intro']+'</span>\n' +
            '                <span class="divider"></span>\n' +
            '                <a class="quit" href="javascipt: ;">退出</a>\n' +
            '                <a class="edit" href="javascipt: ;">发文</a>\n' +
            '                <a class="detail" href="javascipt: ;">进入</a>\n' +
            '            </div>';
        obj.append($model);
    }
</script>
</html>