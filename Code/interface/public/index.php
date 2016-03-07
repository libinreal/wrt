<?php header('location:/mall');die;?>
<!DOCTYPE HTML>
<!--[if lt IE 9 ]><html class="ie"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html>
<!--<![endif]-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>首页-物融通平台</title>
    <link rel="stylesheet" href="content/css/common.css" />
    <link rel="stylesheet" href="content/css/home.css" />
</head>
<body class="home">
    <div class="header">
        <div class="page clearfix">
        	<ul class="header-links">
        		<li><a class="i1" href="wrt/">物融通</a></li>
        		<li><a class="i2" href="jifen/">积分</a></li>
                <li id="loginbar" style="display: none;"><a class="i3 icon-login" href="#open-login">登录</a></li>
        		<li id="userbar" style="display: none;"><a class="i3 icon-personal" href="personal/"></a><a class="i4 icon-logout" href="#">退出</a></li>
        	</ul>
            <div class="home-search">
                <form action="mall/search.html">
                    <input type="text" name="key" class="key" />
                    <input type="submit" value="" class="btn-search" />
                </form>
            </div>
        </div>
    </div>
    <!--//header-->

    <div class="section home-container">
        <div class="home-logo"><a>&nbsp;</a></div>
        <div class="home-nav">
            <a class="home-item" href="credit/">
            	<div class="home-thumb"><img src="content/images/home/xyz.png" alt="" /></div>
            	<div class="home-title">
            		<div class="home-icon home-icon-1"></div>
            		<h3>信用池</h3>
            		<p>Credit Pool</p>
            	</div>
            </a>
            <a class="home-item" href="mall/">
            	<div class="home-thumb"><img src="content/images/home/r-jjsc.png" alt="" /></div>
            	<div class="home-title">
            		<div class="home-icon home-icon-2"></div>
            		<h3>基建商城</h3>
            		<p>Infrastructure Mall</p>
            	</div>
            </a>
            <a class="home-item" href="customize/">
            	<div class="home-thumb"><img src="content/images/home/r-dzzq.png" alt="" /></div>
            	<div class="home-title">
            		<div class="home-icon home-icon-3"></div>
            		<h3>定制专区</h3>
            		<p>Material Tailor</p>
            	</div>
            </a>
            <a class="home-item" href="znsj/">
            	<div class="home-thumb"><img src="content/images/home/r-sjzn.png" alt="" /></div>
            	<div class="home-title">
            		<div class="home-icon home-icon-4"></div>
            		<h3>数据智能</h3>
            		<p>Data Intelligence</p>
            	</div>
            </a>
            <a class="home-item" href="project/">
            	<div class="home-thumb"><img src="content/images/home/r-gczx.png" alt="" /></div>
            	<div class="home-title">
            		<div class="home-icon home-icon-5"></div>
            		<h3>工程资讯</h3>
            		<p>Project News</p>
            	</div>
            </a>
            <a class="home-item" href="ydwr/">
            	<div class="home-thumb"><img src="content/images/home/r-ydwr.png" alt="" /></div>
            	<div class="home-title">
            		<div class="home-icon home-icon-6"></div>
            		<h3>移动物融</h3>
            		<p>Mobile Material Finance</p>
            	</div>
            </a>
        </div>
        <div class="home-news">
            <a class="home-item" href="news/">
        		<div class="home-thumb"><img src="content/images/home/r-wrxw.png" alt="" /></div>
        		<div class="home-title">
        			<div class="home-icon home-icon-7"></div>
        			<h3>物融新闻</h3>
        			<p>Platform News</p>
        		</div>
            </a>
            <div class="home-list">
        		<ul class="items" id="home-news"></ul>
            </div>
        </div>
    </div>
    <!--//section-->

    <div class="home-footer">让建设无忧&nbsp;&nbsp;让工程无患&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Copyright 2014 物融通集成服务平台有限公司&nbsp;&nbsp;&nbsp;版权所有<a href="help/index.html">帮助中心</a></div>
    <div class="modal modal2" id="modal-login">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="modal-return">&lt;</button>
                    <button class="modal-close">×</button>
                    <h4 class="modal-title">登录</h4>
                </div>
                <div class="modal-body">
                    <div class="login-form">
                        <form id="login-form">
                            <div class="input-un-border">
                                <label>用户名：</label>
                                <input type="text" name="account" placeholder="请输入用户名" maxlength="50" autocomplete="off" />
                                <ul class="login-history"></ul>
                            </div>
                            <div class="input-pw-border">
                                <label>密码：</label>
                                <input type="password" name="password" placeholder="请输入用密码" maxlength="50" />
                            </div>
                            <div class="input-rm clearfix">
                                <label><input type="checkbox" name="rememberMe" checked="checked" />记住密码</label>
                                <label><a href="javascrip:void(0);" id="findPwd">找回密码</a></label>
                            </div>
                            <div class="login-btn">
                                <button type="submit" class="button btn-secondary big">登录</button><button 
                                class="button btn-reg big" id="registerBtn">注册</button>
                            </div>
                        </form>
                    </div>
                    <div class="forget-form">
                        <form id="forget-form" class="form">
                            <div class="forget-phone">
                                <label>手机号：</label>
                                <input type="text" name="mobile" placeholder="请输入手机号" maxlength="11" />
                            </div>
                            <div class="forget-vcode">
                                <label>验证码：</label>
                                <input type="text" name="verifyCode" placeholder="请输入验证码" maxlength="6" />
                            </div>
                            <div class="forget-btn">
                                <input type="button" id="sendMsg" class="button btn-secondary big" value="发送验证码" />
                                <label></label>
                                <input type="submit" class="button btn-secondary big disabled" value="提交" disabled />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="zj-panel" class="panel">
        <div class="panel-content">
            <div class="panel-cell">
                <h3 class="panel-title">提示</h3>
                <p class="panel-text"></p>
            </div>
        </div>
        <div class="panel-buttons">
            <div class="options">
                <a href="javascript:;" class="btn btn-ok">确定</a>
                <a href="javascript:;" class="btn btn-cancel">取消</a>
            </div>
        </div>
    </div>
    <div id="panelBg_" class="panel-bg"></div>

    <script id="zj-news-tmpl" type="text/html">
        <!--[for(i = 0; i < list.length; i ++) {]-->
        <li><a href="news/detail.html?type=<!--[= list[i].catType]-->&id=<!--[= list[i].id]-->" title="<!--[= list[i].title]-->"><!--[= list[i].title]--></a><em><!--[= $formatDate(list[i].createAt,'MM/dd')]--></em></li>
        <!--[}]-->
    </script>

    <script src="content/js/module/seajs/2.2.0/sea.js"></script>
    <script src="content/js/module/seajs/2.2.0/sea-config.js"></script>
    <script>
        seajs.use('./content/js/home/main');
    </script>
</body>
</html>
