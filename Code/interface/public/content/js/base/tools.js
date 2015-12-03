define(function(require) {
    var $ = require('jquery'),
        config = require('./config');

    var preventDefault, panel, panelBg, delay, count = 0,
        toastPanel;

    return {
    	formatCurrency1: function(content, defaultValue, unit) {
	        if (!content) {
	            return defaultValue || '--';
	        }

	        content = content + '';//转字符串

	        var prefix,subfix,idx = content.indexOf('.');
	        if(idx > 0){
	            prefix = content.substring(0, idx);
	            subfix = content.substring(idx, content.length);
	        }else{
	            prefix = content;
	            subfix = '';
	        }

	        var mod = prefix.toString().length % 3;
	        var sup = '';
	        if (mod == 1) {
	            sup = '00';
	        } else if (mod == 2) {
	            sup = '0';
	        }

	        prefix = sup + prefix;
	        prefix = prefix.replace(/(\d{3})/g, '$1,');
	        prefix = prefix.substring(0, prefix.length - 1);
	        if (sup.length > 0) {
	            prefix = prefix.replace(sup, '');
	        }
	        if(subfix){
	            if(subfix.length == 2){
	                subfix += '0';
	            }else if(subfix.length == 1){
	                subfix += '00';
	            }
	            subfix = subfix.substring(0,3);
	        }
	        return prefix + subfix;
	    },
        strToDate: function(str) { //字符串转日期，yyyy-MM-dd hh:mm:ss
            var tempStrs = str.split(" ");
            var dateStrs = tempStrs[0].split("-");
            var year = parseInt(dateStrs[0], 10);
            var month = parseInt(dateStrs[1], 10) - 1;
            var day = parseInt(dateStrs[2], 10);

            var timeStrs = tempStrs[1].split(":");
            var hour = parseInt(timeStrs[0], 10);
            var minute = parseInt(timeStrs[1], 10) - 1;
            var second = parseInt(timeStrs[2], 10);
            var date = new Date(year, month, day, hour, minute, second);
            return date;
        },
        getQueryValue: function(key) {
            var q = location.search,
                keyValuePairs = new Array();

            if (q.length > 1) {
                var idx = q.indexOf('?');
                q = q.substring(idx + 1, q.length);
            } else {
                q = null;
            }

            if (q) {
                for (var i = 0; i < q.split("&").length; i++) {
                    keyValuePairs[i] = q.split("&")[i];
                }
            }

            for (var j = 0; j < keyValuePairs.length; j++) {
                if (keyValuePairs[j].split("=")[0] == key) {
                    // 这里需要解码，url传递中文时location.href获取的是编码后的值
                    // 但FireFox下的url编码有问题
                    return decodeURI(keyValuePairs[j].split("=")[1]);

                }
            }
            return '';
        },
        formatDate: function(content, type) {
            var pattern = "yyyy-MM-dd hh:mm";
            switch (type) {
                case 1:
                    pattern = "yyyy年M月d日";
                    break;
                case 2:
                    pattern = "hh:mm";
                    break;
                case 3:
                    pattern = "yyyy.M.d";
                    break;
                case 4:
                    pattern = "yyyy-MM-dd hh:mm:ss";
                    break;
                case 5:
                    pattern = "yyyy年MM月";
                    break;
                case 6:
                    pattern = "yyyy-MM-dd";
                    break;
                default:
                    pattern = !!type ? type : pattern;
                    break;
            }
            if (isNaN(content) || content == null) {
                return content;
            } else if (typeof(content) == 'object') {
                var y = dd.getFullYear(),
                    m = dd.getMonth() + 1,
                    d = dd.getDate();
                if (m < 10) {
                    m = '0' + m;
                }
                var yearMonthDay = y + "-" + m + "-" + d;
                var parts = yearMonthDay.match(/(\d+)/g);
                var date = new Date(parts[0], parts[1] - 1, parts[2]);
                return format(date, pattern);
            } else {
                var date = new Date(parseInt(content));
                return format(date, pattern);
            }
        },
        // 获取窗口尺寸，包括滚动条
        getWindow: function() {
            return {
                width: window.innerWidth,
                height: window.innerHeight
            };
        },
        // 获取文档尺寸，不包括滚动条但是高度是文档的高度
        getDocument: function() {
            var doc = document.documentElement || document.body;
            return {
                width: doc.clientWidth,
                height: doc.clientHeight
            };
        },
        // 获取屏幕尺寸
        getScreen: function() {
            return {
                width: screen.width,
                height: screen.height
            };
        },
        // 显示、禁用滚动条
        showOrHideScrollBar: function(isShow) {
            preventDefault = preventDefault || function(e) {
                e.preventDefault();
            };
            (document.documentElement || document.body).style.overflow = isShow ? 'auto' : 'hidden';
            // 手机浏览器中滚动条禁用取消默认touchmove事件
            if (isShow) {
                // 注意这里remove的事件必须和add的是同一个
                document.removeEventListener('touchmove', preventDefault, false);
            } else {
                document.addEventListener('touchmove', preventDefault, false);
            }
        },
        // 显示对话框
        showDialog: function() {},
        // 显示着遮罩曾
        showOverlay: function() {},
        // 显示确认框
        showConfirm: function(msg, yesCallback, noCallback) {
            var opt = {};
            if (typeof msg == 'object') {
                opt = msg;
            } else {
                opt.message = msg;
                opt.yesCallback = yesCallback;
                opt.noCallback = noCallback;
            }
            opt.type = 'confirm';
            opt.showTitle = true;
            opt.showTip = false;

            this.showPanel0(opt);
        },
        // 显示提示
        showAlert: function(msg, tick, callback) {
            var opt = {};
            if (typeof msg == 'object') {
                opt = msg;
            } else {
                opt.message = msg;
                opt.tick = tick;
                opt.yesCallback = callback;
            }
            opt.type = 'alert';

            this.showPanel0(opt);
        },
        // 显示加载框
        showLoading: function() {
            $('#zj-loading').show();
        },
        hideLoading: function() {
            $('#zj-loading').hide();
        },
        showTip: function(msg, tick, callback) {
            this.setPanelBtnText('确认');
            this.hidePanelTip();
            this.showPanel0('message', msg, tick, callback);
        },
        //设置OK上文本
        setPanelBtnText: function(text) {
            panel = panel || $('#zj-panel');
            if (panel.find('.btn-ok')) {
                panel.find('.btn-ok').val(text);
            }
        },
        //隐藏panel-tips
        hidePanelTip: function() {
            panel = panel || $('#zj-panel');
            panel.find('.panel-tips').hide();
        },
        showPanel0: function(options) {
            panel = panel || $('#zj-panel');
            panelBg = panelBg || $('#zj-panel-bg');
            options = options || {}; 

            var type = options.type || 'error',
                message = options.message || '',
                tick = options.tick || 0,
                okText = options.okText || '确定',
                cancelText = options.cancelText || '取消',
                showTitle = options.showTitle || false,
                showTips = options.showTips || false,
                tipsText = options.tipsText || '';

            config.onYesClick = options.yesCallback;
            config.onNoClick = options.noCallback;
            config.onTipsClick = options.tipsCallback;

            $('body').addClass('hidden');

            if (showTitle) {
                panel.find('.panel-title').show();
            } else {
                panel.find('.panel-title').hide();
            }
            if (showTips) {
                panel.find('.panel-tips').show();
            } else {
                panel.find('.panel-tips').hide();
            }
            if (okText) {
                panel.find('.btn-ok').text(okText);
            }
            if (cancelText) {
                log(cancelText)
                panel.find('.btn-cancel').text(cancelText);
            }
            if (tipsText) {
                panel.find('.panel-tips').html(tipsText);
            }
            if (type == 'confirm') {
                panel.find('.btn-ok').show();
                panel.find('.btn-cancel').show();
            } else {
                panel.find('.btn-ok').show();
                panel.find('.btn-cancel').hide();
            }
            panel.find('.panel-text').html(message);
            panel.css('margin-top', -(panel.height() / 2)).show();
            panelBg.show();

            if (tick > 1000) {
                panel.find('.panel-tick').text(tick / 1000);
                delay = setInterval(function() {
                    if (count < tick - 1000) {
                        count = count + 1000;
                        panel.find('.panel-tick').text((tick - count) / 1000);
                    } else {
                        end();
                        count = 0;
                        clearInterval(delay);
                    }
                }, 1000);
            } else if (tick <= 1000 && tick > 0) {
                delay = setTimeout(function() {
                    end();
                }, tick);
            }

            function end() {
                panel.hide();
                panelBg.hide();

                if (typeof config.onTipsClick == 'function') {
                    config.onTipsClick();
                    config.onTipsClick = undefined;
                } else if (typeof config.onYesClick == 'function') {
                    config.onYesClick();
                    config.onYesClick = undefined;
                }
            }
        },
        hidePanel: function(yesClick) {
            if (delay) {
                clearTimeout(delay);
            }
            if (!panel) {
                return;
            }
            panel.hide();
            panelBg.hide();

            if (yesClick) {
                typeof config.onYesClick == 'function' && config.onYesClick();
            } else {
                typeof config.onNoClick == 'function' && config.onNoClick();
            }
            config.onYesClick = undefined;
            config.onNoClick = undefined;
            $('body').removeClass('hidden');
        },
        showToast: function(msg, tick) {
            toastPanel = toastPanel || $('#zj-toast');
            tick = tick || 600;

            if (delay) {
                clearTimeout(delay);
            }

            toastPanel.find('span').text(msg);
            toastPanel.show();
            delay = setTimeout(function() {
                toastPanel.hide();
            }, tick);
        }
    };

    function format(date, pattern) {
        var that = date;
        var o = {
            "M+": that.getMonth() + 1,
            "d+": that.getDate(),
            "h+": that.getHours(),
            "m+": that.getMinutes(),
            "s+": that.getSeconds(),
            "q+": Math.floor((that.getMonth() + 3) / 3),
            "S": that.getMilliseconds()
        };
        if (/(y+)/.test(pattern)) {
            pattern = pattern.replace(RegExp.$1, (that.getFullYear() + "")
                .substr(4 - RegExp.$1.length));
        }
        for (var k in o) {
            if (new RegExp("(" + k + ")").test(pattern)) {
                pattern = pattern.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
            }
        }
        return pattern;
    };


});
