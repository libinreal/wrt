<?php include("../web.php");?>
<div class="header">
    <div class="page-vertical">
        <a class="icon-logo" href="../"></a>
        <div style="width:5">

        </div>
        <ul class="header-right">
            <li class="my-customize">
                <div class="unlink overlay-name">筛选：</div>
                <div class="link overlay">
                    <span class="overlay-title">时间</span>
                    <div class="overlay-content overlay-creditb" id="mycustomize-time">
                        <a href="#" data-v="d3">近3天</a>
                        <a href="#" data-v="d5">近5天</a>
                        <a href="#" data-v="d15">近15天</a>
                        <a href="#" data-v="min">近一个月</a>
                        <a href="#" data-v="mout">一个月前信息</a>
                    </div>
                </div>
                <div class="link overlay">
                    <span class="overlay-title">类别</span>
                    <div class="overlay-content overlay-creditb" id="mycustomize-type"></div>
                </div>
            </li>
            <li class="header-creditb">
                <div class="unlink overlay-name">筛选：</div>
                <div class="link overlay">
                    <span class="overlay-title">时间</span>
                    <div class="overlay-content overlay-creditb" id="creditb-time">
                        <a href="#" data-v="m1">1个月</a>
                        <a href="#" data-v="m3">3个月</a>
                        <a href="#" data-v="m6">半年内</a>
                        <a href="#" data-v="y1">一年内</a>
                    </div>
                </div>
                <div class="link overlay">
                    <span class="overlay-title">票据</span>
                    <div class="overlay-content overlay-creditb" id="creditb-type">
                        <a href="#" data-v="1">信用恢复</a>
                        <a href="#" data-v="0">信用扣减</a>
                    </div>
                </div>
            </li>
            <li class="header-znsj"><a class="link icon znsj-header" href="#">付款方式<span>|</span>提示方式</a></li>
            <li class="header-customize">
                <div class="unlink overlay-name">筛选：</div>
                <div class="link overlay">
                    <span class="overlay-title">类别</span>
                    <div class="overlay-content overlay-customize" id="customize-type"></div>
                </div>
            </li>
            <li id="loginbar" style="display: <?php echo isset($auth) ? 'none' : 'block' ?>"><a class="link icon icon-login" href="#open-dialog">登录</a></li>
            <li id="userbar" style="display: <?php echo isset($auth) ? 'block' : 'none' ?>;">
                <a class="link icon icon-personal" href="../personal/"><?php if(isset($auth)){echo $auth->account;} ?></a>
                <a class="link icon icon-logout" href="#" style="padding-left: 10px;">退出</a>
            </li>
            <li><a class="icon link icon-home" href="../">首页</a></li>
        </ul>
    </div>
</div><!--//header-->