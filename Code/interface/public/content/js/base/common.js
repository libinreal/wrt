define(function(require) {
    var $ = require('jquery'),
        template = require('template'),
        msg = require('../login-register/messages'),
        rules = require('../login-register/rules'),
        config = require('../login-register/config'),
        Ajax = require('./ajax.js'),
        Tools = require('./tools'),
        Cookie = require('./cookie'),
        Storage = require('./storage');

    //require('../login-register/login');

    //获取公告
    //物融公告(2001)，商城公告(2002)，定制专区公告(2003)，积分公告(2004)
    config.getNotice = function(type, size, renderFor, renderEle) {
        renderFor = renderFor || 'zj-notice-tmpl';
        renderEle = renderEle || '#zj-notice';
        size = size || 6;

        Ajax.custom({
            url: config.notice,
            data: {
                type: type,
                size: size
            }
        }, function(response, textStatus, jqXHR) {
            if (response.code != 0) {
                return;
            }

            var result = template.render(renderFor, {
                'list': response.body || []
            });
            $(renderEle).html(result || config.nodata);

        });
    };

    //获取广告
    //position 查看接口文档
    config.getAd = function(position, callback, renderFor, renderEle) {
        renderFor = renderFor || 'zj-ad-tmpl';
        renderEle = renderEle || '#zj-ad';

        Ajax.custom({
            url: config.ad,
            data: {
                position: position
            }
        }, function(response, textStatus, jqXHR) {
            if (response.code != 0) {
                return;
            }
            if (typeof callback == 'function') {
                callback(response);
            }else{
                var result = template.render(renderFor, {
                    'list': response.body || []
                });
                $(renderEle).html(result);

                if (response.body.length > 1) {
                    $('.scroll-img').scrollable({
                        circular: true
                    }).autoscroll({
                        autoplay: true
                    }).navigator();
                }
            }
        });
    };

    //获取省市
    config.getProvince = function(renderFor, renderEle, callback, type) {
        renderFor = renderFor || 'zj-province-tmpl';
        renderEle = renderEle || '#zj-province';

        Ajax.custom({
            url: config.province,
            data: {
                type: type
            }
        }, function(response, textStatus, jqXHR) {
            if (response.code != 0) {
                return;
            }

            var result = template.render(renderFor, {
                'list': response.body || []
            });
            $(renderEle).html(result);

            if (typeof callback == 'function')
                callback(response);
        });
    };

    //获取合同列表
    config.getContracts = function(renderFor, renderEle, callback) {
        renderEle = renderEle || '#zj-contracts';

        Ajax.custom({
            url: config.contracts
        }, function(response) {
            if (response.code != 0) {
                return;
            }

            var result = '',
                data = response.body || [],
                len = data.length;

            for (var i = 0; i < len; i++) {
                result += '<li data-v="' + data[i].code + '">' + data[i].name + '</li>';
            }
            $('#zj-contracts').append(result);

            if (typeof callback == 'function') {
                callback(response);
            }
        });
    };

    //收藏、取消商品
    config.saveFavorite = function(dom, id, callback) {
        var isHave = dom.hasClass('active');
        if (!id) {
            id = dom.attr('data-id');
        }

        Ajax.custom({
            url: isHave ? config.favoritesDelete : config.favoritesSave,
            data: {
                id: id
            },
            type: 'POST'
        }, function(response) {
            if (response.code != 0) {
                return;
            }

            if (isHave)
                dom.removeClass('active');
            else
                dom.addClass('active');
            Tools.showToast((isHave ? '取消' : '新增') + '收藏成功');

            if (typeof callback == 'function') {
                callback(response);
            }
        });
    };

    //添加购物车
    config.addToCart = function(id, num) {
        num = num || 1;

        Ajax.custom({
            url: config.addCart,
            data: {
                id: id,
                number: num
            },
            type: 'POST'
        }, function(response) {
            if (response.code != 0) {
                return;
            }
            location.href = '../mall/cart.html';
        })
    };

        $(document).click(function(e){
            var hasActive = false,
                target = e.target;
            while(target && !$(target).hasClass('zj-select') && target.nodeName != 'BODY'){
                target = target.parentNode;
            }
            target = $(target);
            if(target.hasClass('zj-select') && !target.hasClass('disabled')){
                hasActive = target.hasClass('active');
            }else{
                target = undefined;
            }
            $('.zj-select').removeClass('active');
            if(target){
                pos(target);
                if(hasActive){
                    target.removeClass('active');
                }else{
                    target.addClass('active');
                    checked(target.find('.dropdown li'), target.find('input[type="hidden"]').val());
                }
            }
        });
        //定位下拉弹出框
        function pos(target){
            var offset = target.offset();
            var dh = $(document).height();
            var dropdown = target.find('.dropdown');
            var dd = dh - offset.top - target.height();
            var mm = dropdown.height();

            // console.log('dd:' + dd +' mm:' + mm);
            if(dd <= mm){
                dropdown.addClass('bottom').css({
                    'bottom': target.height() - 1
                });
            }else{
                dropdown.removeClass('bottom').css({
                    'top': target.height() - 1
                });
            }
            target.css({'width': target.width()})
        }
        //设置默认选中值
        function checked(dom, v){
            dom.each(function(){
                if($(this).attr('data-v') == v){
                    $(this).addClass('active');
                }else{
                    $(this).removeClass('active');
                }
            });
        }
        $('.zj-select').click(function(){
            //$(this).toggleClass('active');
        });
        $('.zj-select .dropdown').on('click','li',function(){
            $(this).parent().prev().text($(this).text());
            $(this).parent().next().val($(this).attr('data-v'));
            $(this).parents('.zj-select').trigger('change');
        });
        $('.zj-select .dropdown').on('mouseover','li',function(){
            $('.zj-select .dropdown li').removeClass('active');
        });
        $('.zj-select input[type="hidden"]').change(function(){
            $(this).parent().find('.default').text(findTextByValue($(this).parent().find('.dropdown li'), $(this).val()));
        });
        function findTextByValue(dom, v){
            var text = '';
            dom.each(function(){
                if($(this).attr('data-v') == v){
                    text = $(this).text();
                }
            });
            return text;
        }

    //自适应页面高度
    var headerHeight = $('.header').height() || 0;
    var footerHeight = $('.footer').outerHeight() || 0;
    var mHeight = document.documentElement.clientHeight - headerHeight - footerHeight;
    //$('.section .page-vertical').css('min-height', mHeight);
    $('.footer').fadeIn(200);

    //返回顶部
    if($(document).height() > $(window).height()){
		$(window).scroll(function(){
	        if ($(window).scrollTop() > 100)
	        {
	        	$("#sidepanel").fadeIn(500);
	        }
	        else
	        {
	        	$("#sidepanel").fadeOut(500);
	        }
	    });
	    //当点击跳转链接后，回到页面顶部位置
	    $("#gotop").click(function() {
	    	$('body,html').animate({scrollTop:0},100);
	    		return false;
		});
	}

    // 分页按钮
    $('body').on('click', '.nextpage a', function(e) {
        e.preventDefault();

        if ($(this).hasClass('loading') || $(this).hasClass('disabled')) {
            // 正在加载，不可点击
            return;
        }

        if ($(this).hasClass('zj-prev')) {
            config.forward = 1;
        } else {
            config.forward = 0;
        }

        if (typeof config.paging == 'function') {
            config.paging();
        }
        config.forward = undefined;
    });

    //提示框的关闭
    $('#zj-panel').on('click', '.btn-ok', function(e) {
        e.preventDefault();
        Tools.hidePanel(true);
    });

    //提示框的取消
    $('#zj-panel').on('click', '.btn-cancel', function(e) {
        e.preventDefault();
        Tools.hidePanel();
    });

    //提示框的关闭
    $('#zj-panel').on('click', '.panel-close', function(e) {
        e.preventDefault();
        $('#zj-panel').hide();
        $('#zj-panel-bg').hide();
        $('body').removeClass('hidden');
    });

    //单选框点击
    $('.form-radio-group label').click(function() {
        $(this).parents('.form-radio-group').find('label').removeClass('active');
        $(this).addClass('active');
    });

    //头部状态栏
    var loginbar = $('#loginbar'),
        userbar = $('#userbar');
    Ajax.custom({
        url: config.getaccount
    }, function(response) {
        if (response.code != 0) {
            loginbar.show();
            userbar.hide();
            if (typeof config.failedCheckAccount == 'function')
                config.failedCheckAccount();
        } else {
            loginbar.hide();
            userbar.show();
            $('.icon-personal').text(response.body.account);
            if (typeof config.afterCheckAccount == 'function')
                config.afterCheckAccount(response.body);
        }
    }, function() {
        loginbar.show();
        userbar.hide();
        if (typeof config.failedCheckAccount == 'function')
            config.failedCheckAccount();
    });

    if (location.hash == '#open-login') {
        $('#modal-login').show();
    }
    //打开登陆框
    $('.icon-login').click(function(e) {
        $('#modal-login').show();
    });

    //退出
    $('.icon-logout').click(function(e) {
        e.preventDefault();
        Ajax.custom({
            url: config.logout
        }, function(response) {
            if (response.code != 0) {
                loginbar.show();
                userbar.hide();
                return;
            }

            loginbar.show();
            userbar.hide();
        })
    });

    //$('.zj-select').select();

    //頁面含有登陸框
    if ($('#modal-login').length) {
        // Array remove 扩展
        function remove(arr, dx) {
            if (isNaN(dx) || dx > arr.length) {
                return false;
            }
            for (var i = 0, n = 0; i < arr.length; i++) {
                if (arr[i] != arr[dx]) {
                    arr[n++] = arr[i];
                }
            }
            arr.length -= 1;
        }

        var username = $('#login-form input[name="account"]'),
            password = $('#login-form input[name="password"]'),
            verifyCode = $('#login-form input[name="verifyCode"]'),
            loginHistory = {},
            historyContainer = $('.login-history'),
            isAppend = false,
            indexLI; //当前用户选中序号

        // 记住密码
        loginHistory = $.parseJSON(Cookie.get(Storage.LOGIN_HISTORY)) || loginHistory;
        if (loginHistory.infos && loginHistory.infos.length) {
            if (loginHistory.infos[loginHistory.infos.length - 1].un) { //username
                username.val(loginHistory.infos[loginHistory.infos.length - 1].un);
            }
            if (!loginHistory.infos[loginHistory.infos.length - 1].pw) {
                $('input[name="rememberMe"]').attr('checked', false);
            }
            $('input[name="rememberMe"]').attr('checked', true);
            password.val(loginHistory.infos[loginHistory.infos.length - 1].pw);
        }

        //添加，设置默认用户
        function addUser(defaultUser) {
            if (!loginHistory.infos || !loginHistory.infos.length) {
                loginHistory.infos = [];
            }
            if (loginHistory.infos.length == 5) {
                loginHistory.infos.shift();
            }

            for (i = 0; i < loginHistory.infos.length; i++) {
                if (defaultUser.un == loginHistory.infos[i].un) {
                    remove(loginHistory.infos, i);
                }
            }
            loginHistory.infos.push(defaultUser);

            Cookie.set(Storage.LOGIN_HISTORY, JSON.stringify(loginHistory));
        };

        // 账号提示
        username.on('focusin', function() {
            if (historyContainer.children().length > 0) {
                historyContainer.show();
                return;
            }
            if (!loginHistory.infos || loginHistory.infos.length == 0) {
                return;
            }
            for (i = 0; i < loginHistory.infos.length; i++) {
                historyContainer.append('<li>' + loginHistory.infos[i].un + '</li>');
                historyContainer.show();
            }
        });
        username.on('keyup', function(e) {
            var max = historyContainer.children().length;
            historyContainer.find('li').removeClass('active');
            //down=40 up=38
            if (e.keyCode == 40) {
                if (typeof indexLI == 'undefined') indexLI = -1;
                indexLI++;
            } else if (e.keyCode == 38) {
                if (typeof indexLI == 'undefined') indexLI = 0;
                indexLI--;
            }
            if (indexLI >= max) {
                indexLI = 0;
            }
            if (indexLI < 0) {
                indexLI = max - 1;
            }
            historyContainer.find('li:eq(' + indexLI + ')').addClass('active');
        });
        password.on('focusin', function(e) {
            closeHistoryContainer();
        });
        historyContainer.on('click', 'li', function() {
            username.val($(this).text());
            if (loginHistory.infos[$(this).index()].pw) {
                $('input[name="rememberMe"]').prop('checked', true);
                password.val(loginHistory.infos[$(this).index()].pw);
            } else {
                $('input[name="rememberMe"]').removeAttr('checked', false);
                password.val('');
            }
            closeHistoryContainer();
        });
        $(document).on('click', function(event) {
            if ($(event.target).parent().hasClass('login-history') || $(event.target).attr('name') == 'account') return;
            closeHistoryContainer();
        });

        //关闭弹出下拉框，重置数据
        function closeHistoryContainer() {
            historyContainer.hide();
            historyContainer.find('li').removeClass('active');
            indexLI = undefined;
        }

        //表单验证
        function validateLoginForm() {
            if (rules.empty.test(username.val())) {
                Tools.showAlert(msg.unEmpty);
                return false;
            }
            if (rules.empty.test(password.val())) {
                Tools.showAlert(msg.pwEmpty);
                return false;
            }
            if (!rules.username.test(username.val())) {
                Tools.showAlert(msg.usernameErr);
                return false;
            }
            if (!rules.password.test(password.val())) {
                Tools.showAlert(msg.pwErr);
                return false;
            }
            return true;
        }

        // 登陆
        $('#login-form').submit(function(e) {
            e.preventDefault();
            if (!validateLoginForm()) {
                return;
            }

            Ajax.submit({
                url: config.iLogin,
                data: $(this)
            }, function(data) {
                if (!data || data.code != 0) {
                    Tools.showAlert(data.message);
                    return;
                }

                var defaultUser = {};
                defaultUser.un = username.val();
                if ($('input[name="rememberMe"]').prop('checked')) {
                    defaultUser.pw = password.val();
                }
                addUser(defaultUser);

                //redirect
                var from = Tools.getQueryValue('from');
                if (from) {
                    location.href = decodeURIComponent(from);
                } else {
                    location.href = config.index;
                }

            });
        });

        //----------页面切换----------//
        // 找回密码
        $('#findPwd').on('click', function(e) {
            e.preventDefault();
            $('.modal-title').html('找回密码');
            $('.forget-form').show();
            $('.login-form').hide();
            $('.modal-return').show();
        });
        // 注册
        $('#registerBtn').click(function() {
            location.href = config.register + '?from=' + encodeURIComponent(location.href);
        });
        // modal close
        $('.modal-close').on('click', function() {
            $('.modal').hide();
        });
        $('.modal-return').on('click', function() {
            $(this).hide();
            $('.modal-title').html('登录');
            $('.forget-form').hide();
            $('.login-form').show();
        });

        //忘记密码 js

        var mobile = $('#forget-form input[name="mobile"]'),
            verifyCode = $('#forget-form input[name="verifyCode"]'),
            timer = undefined; //短信计时

        // 第一步、短信发送
        $('#sendMsg').click(function(e) {
            if ($(this).hasClass('disabled')) {
                return;
            }

            var _this = $(this);
            if (rules.empty.test(mobile.val())) {
                Tools.showAlert(msg.phoneEmpty);
                return;
            }
            if (!rules.mobile.test(mobile.val())) {
                Tools.showAlert(msg.phoneErr);
                return;
            }
            $(this).addClass('disabled');

            Ajax.custom({
                url: config.iForget,
                data: {
                    step: 1,
                    mobile: mobile.val()
                },
                type: 'POST'
            }, function(data) {
                if (data.code != 0) {
                    _this.removeClass('disabled');
                    Tools.showAlert(data.message);
                    return;
                }

                changeBtnState(_this);
            }, function(jqxhr, textStatus, errorThrown) {
                log('找回密码第一步失败 ' + textStatus);
                _this.removeClass('disabled');
            });
        });
        //验证提交按钮状态
        function checkSubmit() {
            $('input[type="submit"]').addClass('disabled').prop('disabled', true);
            if (!rules.mobile.test(mobile.val())) {
                return;
            }
            if (!rules.vcode.test(verifyCode.val())) {
                return;
            }

            $('input[type="submit"]').removeClass('disabled').prop('disabled', false);
        }

        // 确认第二步
        mobile.keyup(checkSubmit).blur(checkSubmit);
        verifyCode.keyup(checkSubmit).blur(checkSubmit);

        // 第二部、提交表单
        $('#forget-form').submit(function(e) {
            e.preventDefault();
            if (!validateForm()) {
                return;
            }
            var that = $(this);

            Ajax.submit({
                url: config.iForget,
                data: {
                    step: 2,
                    mobile: mobile.val(),
                    vcode: verifyCode.val()
                },
                type: 'POST'
            }, function(data) {
                if (data.code != 0) {
                    if (data.code == 204) {
                        Tools.showAlert(msg.phoneNotRigester);
                        return;
                    }
                    Tools.showAlert(data.message);
                    return;
                }
                Tools.showAlert(msg.sendSmsSuccess, 0, function() {
                    //成功之后，清除form，停止计时，还原按钮状态，关闭弹窗
                    that[0].reset();
                    if (timer) {
                        clearInterval(timer);
                    }
                    $('#sendMsg').removeClass('disabled').prop('disabled', false);
                    $('input[type="submit"]').addClass('disabled').prop('disabled', true);
                    $('.modal').hide();
                    $('#panelBg_').hide();
                });
            });
        });


        //短信计时
        function changeBtnState(obj) {
            var second = 60;
            var text = obj.val();
            obj.prop('disabled', true);
            timer = setInterval(function() {
                obj.val(text + '(' + (second--) + ')');
                if (second < 0) {
                    obj.prop('disabled', false);
                    obj.val(text);
                    obj.removeClass('disabled');
                    clearInterval(timer);
                }
            }, 1000);
        }

        //表单验证
        function validateForm() {
            if (rules.empty.test(mobile.val())) {
                Tools.showAlert(msg.phoneEmpty);
                return false;
            }
            if (!rules.mobile.test(mobile.val())) {
                Tools.showAlert(msg.phoneErr);
                return false;
            }
            if (rules.empty.test(verifyCode.val())) {
                Tools.showAlert(msg.vcEmpty);
                return false;
            }
            if (!rules.vcode.test(verifyCode.val())) {
                Tools.showAlert(msg.vcodeTypeErr);
                return false;
            }
            return true;
        }

    }
});
