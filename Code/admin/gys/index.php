<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>供应商管理中心</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script type="text/javascript" src="../admin/images/jquery-1.10.2.min.js"></script>
    <style type="text/css">
        body {
            background: #E7E7E7;
            padding: 0;
            margin: 0 auto;
            width: 100%;
        }
        *{
            margin:0;
            padding:0;
            font:16px "微软雅黑";
            color:#525252;
        }
        input{
            border:none;background:none;outline:none;position:relative;
        }
        .bg {
            z-index:0;position:relative;width: 100%;height: 100%;
        }
        .bg img {
            width: 100%;height: 100%;
        }
        .loginBox{
            position:absolute;
            background: #FFFFFF;
            width:450px;
            height:370px;
            z-index:1;
            top:40%;
            left:50%;
            margin-left:-179.5px;
            margin-top:-158px;
        }
        .loginBox .inbox{
            width:400px;margin: 10px 20px;text-align:center;
        }
        .loginBox .logo{
            margin-bottom:8px;
        }
        .loginBox .txt{
            margin-bottom:10px;color:#000000;font-size: 16px;
        }
        .myform {
            overflow: inherit;margin: 0px;padding: 0px;text-align: center;margin: 0 10px;
        }


        .Copyright {
            position: absolute;bottom: 0px;left: 0px;height: 45px;line-height: 45px;background: #131313;width: 100%;
            font-size: 14px;text-align: center;color: #FFFFFF;
        }

        .login_area{width: 351px;height: 275px;margin:27px auto 27px;}
        .login_logo{width: 250px;height: 50px;background: url("/admin/images/login_logo.gif") no-repeat;margin: 25px auto;}
        .login_txt {height: 20px;line-height: 20px;font-size: 16px;font-weight: 600;text-align: center;}
        .input_text{width: 351px;height: 95px;overflow: hidden;margin-bottom: 18px;}
        .input_text input{display: block;width: 300px;height: 38px;border: 1px solid #ccc;border-radius:5px;margin-bottom: 15px;padding-left: 45px;}
        .remember_pwd{display: block;width: 351px;height: 16px;line-height: 16px;margin-bottom: 22px;}
        .remember_pwd span{font-size: 14px;}
        .goLogin{width: 351px;height: 45px;background: url("../admin/images/subBtn.png") no-repeat;cursor: pointer;}
    </style>
</head>
<body>
<div class="loginBox">
    <div class="login_area">
        <form id="login" action="../admin/privilege.php" method="post" name="myform" onsubmit="return fnGetLogin();">
            <div class="login_logo"></div>
            <div class="input_text">
                <input name="username" type="text" style="background:url('/admin/images/username_ico.jpg') no-repeat;" placeholder="供应商账户"/>
                <input name="password" type="password" style="background:url('/admin/images/password_ico.jpg') no-repeat;" placeholder="输入密码"/>
            </div>
            <label class="remember_pwd"><input type="checkbox" name="remember" id="remember" value="1" /><span>记住密码</span></label>
            <input class="goLogin" type="submit" value="" />
            <input type="hidden" name="act" value="signin">
            <input type="hidden" name="role" value="2"/>
        </form>
    </div>
</div>
<div class="bg"><img src="../admin/images/bg-1920.png"></div>
<div class="Copyright">Copyright <?php echo date('Y',time());?> 物融通集成服务平台有限公司   版权所有</div>
<script type="text/javascript">

    function fnGetLogin() {
        if(!$.trim($('.input_text input').eq(0).val())) {
            $('.input_text input').eq(0).css('borderColor','red').focus();
            return false;
        }
        if(!$.trim($('.input_text input').eq(1).val())) {
            $('.input_text input').eq(1).css('borderColor','red').focus();
            return false;
        }
        return true;
    }

</script>
</body>
</html>
