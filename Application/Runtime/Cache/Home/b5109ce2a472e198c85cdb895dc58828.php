<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>加入-MyCircle</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1, maximum-scale=2.0, user-scalable=no" />
    <link href="/interest/Public/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/interest/Public/CSS/main-style.css" rel="stylesheet" type="text/css" media="all">
    <script src="/interest/Public/js/jquery-3.2.1.js"></script>
    <script>
        var module="/interest/index.php/Home";
        $(function () {
            $.ajax({
                type:"post",
                url:"/interest/index.php/Home/Account/check_login",
                success:function(confirm){
//                    if(confirm)
                },error:function () {
                    alert("error");
                }
            });
        })
    </script>
    <script src="/interest/Public/js/my_circle.js"></script>
</head>
<body>
<div class="main-body">
    <header >
        <div class="nav_container bg">
            <div class="nav-menu fl">
                <img src="/interest/Public/img/logo.png" class="logo fl">
                <ul class="nav-menu-list fl">
                    <li><a href="/interest/index.php/Home">首页</a></li>
                    <li><a href="/interest/index.php/Home/Circle/circle_index">兴趣圈</a></li>
                    <!--<li>我的文章</li>-->
                    <!--<li>我的收藏</li>-->
                    <li class="li-bottom"></li>
                </ul>
            </div>

            <div class="fr nav-user">
                <div class="fl user-status">
                    <ul>
                        <li><a href="#">
                            <div class="top-face face fl">
                                <img src="/interest/Public/img/akari.jpg" class="img-face" alt="头像">
                            </div>
                        </a></li>
                        <li><a href="#">登录</a></li>
                        <li><a href="#">注册</a></li>
                    </ul>
                </div>
                <!--<div class="test bg">-->
                <!--123333-->
                <!--</div>-->
            </div>

        </div>
    </header>
    <div class="create-container bg">
        <form class="circle-create">
            <h1>Circle创建</h1>
            <div class="form-group">
                <h3>兴趣圈名称：</h3>
                <input type="text" class="form-control" id="circle_name" name="circle_name">
            </div>
            <div class="form-group">
                <h3>兴趣圈简介：</h3>
                <input type="text" class="form-control" id="circle_intro" name="circle_intro">
            </div>
            <div class="form-group circle-class-group">
                <h3>兴趣圈分类：</h3>
                <select id="circle_class" class="form-control circle_class">
                    <option selected value="">请选择</option>
                </select>
            </div>
            <div class="cr"></div>
            <div class="form-group">
                <h3>兴趣圈头像：</h3>
                <input type="file" id="circle_face" name="circle_face">
                <p class="tip">请上传图片.</p>
            </div>
            <input type="submit" class="btn user-btn">
        </form>
    </div>
</div>
</body>
<script src="/interest/Public/bootstrap/js/bootstrap.min.js"></script>
</html>