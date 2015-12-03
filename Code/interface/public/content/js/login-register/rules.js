define(function(require, exports, module) {
    var rules = require('../base/rules');

    //用户名
    rules.username =/^\w{3,12}$/;
    //密码位数限制
    rules.password = /^[0-9a-zA-Z]{6,16}$/; 
    //邮箱验证
    rules.email = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/; 
    //座机号验证
    rules.tel = /^(?:(?:0\d{2,3}[\- ]?[1-9]\d{6,7})|(?:[48]00[\- ]?[1-9]\d{6}))$/;
    //手机号验证
    rules.mobile = /^1[3-9]\d{9}$/;
    //QQ号验证
    rules.isQQ = /^[1-9]\d{4,8}$/;
    //请输入中文
    rules.chinese = /^[\u0391-\uFFE5]+$/;
    //url
    rules.url = /^(https?|ftp):\/\/[^\s]+$/;
    //字母
    rules.letters = /^[a-z]+$/;
    //数字
    rules.digits = /^\d+$/;
    //身份证号
    rules.ID_card = /^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[A-Z])$/;
    //验证码
    rules.vcode = /^\d{6}$/;
    
    module.exports = rules;
});
