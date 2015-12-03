define(function(require) {
	var $ = require('jquery'),
		config = require('./config'),
		Ajax = require('../base/ajax'),
		Storage = require('../base/storage'),
		Tools = require('../base/tools');

	require('../base/common');
	require('fileupload');
	require('../module/validator/local/zh_CN');

	//validator config
	$.validator.config({
		focusCleanup: true,
		msgWrapper: 'div',
		msgMaker: function(opt) {
			return '<span class="' + opt.type + '">' + opt.msg + '</span>';
		}
	});
	//获取用户信息
	var user = Storage.get(Storage.ACCOUNT);

	//回填数据
	log(user);
	$('.user-icon').attr('src',config.absImg(user.icon))
	if($('#user-tmpl').length&&$('#user-data').length){
		$('#user-data').append(template.render('user-tmpl',user));
	}
	$('#username').text(user.account || '');
	$('#mobile').text(user.telephone || '');
	$('input[name="contacts"]').val(user.contacts || '');
	$('input[name="email"]').val(user.email || '');
	$('input[name="fax"]').val(user.fax || '');
	$('input[name="gender"]').each(function() {
		if ($(this).val() == user.gender) {
			$(this).prop('checked', true);
			return;
		}
	});
	$('input[name="officePhone"]').val(user.officePhone || '');
	$('input[name="companyName"]').val(user.companyName || '');
	$('input[name="companyAddress"]').val(user.companyAddress || '');
	$('input[name="position"]').val(user.position || '');
	$('input[name="secondContacts"]').val(user.secondContacts || '');
	$('input[name="secondPhone"]').val(user.secondPhone || '');
	$('input[name="weixin"]').val(user.weixin || '');
	$('input[name="qq"]').val(user.qq || '');
	$('input[name="department"]').val(user.department || '');

	//验证提交表单
	$('#updateInfoForm').validator({
		fields: {
			"email": {
				rule: "email;"
			},
			"contacts": {
				rule: "联系人: required;length[1~10]"
			},
			"secondContacts": {
				rule: "length[1~10]"
			},
			"secondPhone": {
				rule: "mobile"
			},
			"qq": {
				rule: "QQ: qq"
			},
			"weixin": {
				rule: "微信: length[1~20];"
			},
			"companyName": {
				rule: "单位名称: required;length[1~40]"
			},
			"companyAddress": {
				rule: "单位地址: required;length[1~150]"
			},
			"officePhone": {
				rule: "办公电话: tel|mobile"
			},
			"fax": {
				rule: "办公传真: tel"
			},
			"position": {
				rule: "职位: required;length[1~20]"
			},
			"department": {
				rule: "所在部门: required;length[1~50]"
			}
		},
		//验证成功
		valid: function() {
			Ajax.submit({
				url: config.iModifyInfo,
				data: $('#updateInfoForm'),
			}, function(data) {
				if (!data || data.code != 0) {
					Tools.showAlert(data.message);
					return;
				}
				updateUserInfo($('#updateInfoForm').serializeArray());
				location.href = "index.html";
			});
		}
	});

	//更新用户信息
	function updateUserInfo(arr) {
		if (!arr || !arr.length || !user) {
			return;
		}
		for (i in user) {
			for (k in arr) {
				if (i == arr[k].name) {
					log(user[i] = arr[k].value);
				}
			}
		}
		Storage.set(Storage.ACCOUNT, user);
	}

	//upload icon
	$('input:file').fileupload({
		url:config.upload,
		dataType : "JSON",
		acceptFileTypes : /(\.|\/)(jpe?g|png)$/i,
		maxFileSize:5000000,
		done:function(e,data){
			$('.show-icon')
			.find('img')
			.attr('src',config.image+data.result.body[0]);
		},
		process: function(e,data){
          for(var i=0,l=data.processQueue.length;i<l;i++){
              if(data.processQueue[i].action == 'validate'){
                  data.messages.acceptFileTypes = '上传文件格式不支持.';
              }
          }
          data.messages.maxFileSize = '上传文件太大，限制'+data.maxFileSize/1000+'K以内.';
     	}
	});

	//default icon
	$('#icons li').on('click',function(e){
		e.preventDefault();
		$('.show-icon')
		.find('img')
		.attr('src',$(this).find('img').attr('src'));
	});

	//保存头像
	$('#uploadIconForm').submit(function(e){
		e.preventDefault();
		Ajax.custom({
			url:config.iModifyIcon,
			type:'POST',
			data:{icon:$('.show-icon').find('img').attr('src')}
		});
	});

	//修改密码
	$('#modifyPwdForm').validator({
		rules: {
          phoneValid: function(element) {
              return $.ajax({
                  url: config.iVerifyMobile,
                  data: 'mobile=' + element.value,
                  type: 'GET'
              });
          }
	      },
	      fields: {
	         "oldpwd": {
	             rule: "旧密码: required;password;"
	         },		          
	         "newpwd_one": {
	              rule: "新密码: required;password;"
	          },
	          "newpwd": {
	              rule: "确认新密码: required;match(newpwd_one);"
	          }
	      },
	      //验证成功
	      valid: function(form) {
	          Ajax.submit({
	              url: config.iModifyPwd,
	              data: $('#modifyPwdForm'),
	          }, function(data) {
	              if (!data || data.code != 0) {
	                  Tools.showAlert(data.message);
	                  return;
	              }
	              Tools.showAlert('密码修改成功！');
	              $('#modifyPwdForm')[0].reset();
	          });
	      }
	});

	//短信发送
	var phone = $('input[name="mobile"]');

	$('#sendMsg').click(function(e) {
	    e.preventDefault();
	    var _this = $(this);

	    phone.isValid(function(v) {
	        if (!v) {
	            return;
	        }
	        _this.addClass('disable-btn');
	        $('input[name="step"]').val(1);
	        Ajax.submit({
	            url: config.iModifyPhone,
	            data: $('#modifyPhoneForm'),
	        }, function(data) {
	            if (!data || data.code != 0) {
	                Tools.showAlert(data.message);
	                return;
	            }
	            changeBtnState(_this);
	        });
	    });

	});

	//短信计时
	function changeBtnState(obj) {
	    var second = 60;
	    var text = obj.text();
	    obj.prop('disabled', true);
	    var timer = setInterval(function() {
	        obj.text(text + '(' + (second--) + ')');
	        if (second < 0) {
	            obj.prop('disabled', false);
	            obj.text(text);
	            obj.removeClass('disable-btn');
	            clearInterval(timer);
	        }
	    }, 1000);
	}

	//修改手机号
	$('#modifyPhoneForm').validator({
		rules: {
		    phoneValid: function(element) {
		        return $.ajax({
		            url: config.iVerifyMobile,
		            data: 'mobile=' + element.value,
		            type: 'GET'
		        });
		    }
		},
		fields: {
		    "mobile": {
		        rule: "联系人手机号码: required;mobile;phoneValid;"
		    },
		    "vcode": {
		        rule: "验证码: required;"
		    }
		},
		//验证成功
		valid: function(form) {
			$('input[name="step"]').val(2);
		    Ajax.submit({
		        url: config.iModifyPhone,
		        data: $('#modifyPhoneForm'),
		    }, function(data) {
		        if (!data || data.code != 0) {
		            Tools.showAlert(data.message);
		            return;
		        }
		        $('#modifyPhoneForm')[0].reset();
		    });
		}
	});
});