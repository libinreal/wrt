define(function(require, exports, module) {

	var config = require('../base/config');
	
	config.iModifyInfo = config.server+'/account/uinfo'; //修改个人信息
	config.iModifyPwd = config.server+'/account/upwd';//修改密码
	config.iModifyPhone = config.server+'/account/uphone';//修改手机号
	config.iModifyIcon = config.server+'/account/uicon';//修改头像
	config.iSeeCreditsGetLog = config.server+'/personcenter/creditslog';//查看积分获取记录
	config.iSeeCreditsExchangeOrder = config.server+'/personcenter/creditsorder';//查看积分兑换记录
	config.iSeeCollect = config.server+'/favorites/getList';//我的收藏
	config.iCollectProduct = config.server+'/favorites/save';//收藏商品
	config.iCancelCollect = config.server+'/favorites/delete';//取消收藏
	config.iMyAddress = config.server+'/address/getlist';//收货地址列表
	config.iSaveAddress = config.server+'/address/save';//保存收货地址
	config.iSetDefaultAddress = config.server+'/address/setDefault';//设置收货地址
	config.iDeleteAddress = config.server+'/address/delete';//删除收货地址
	config.iFindAddressById = config.server+'/address/detail';//查找收货地址
	config.iVerifyMobile = config.server + '/account/checkmobile'; //手机号唯一验证
	config.iSendMsg = config.server + '/account/createVcode';
	config.myapplys = config.server + '/customize/myapplys'; //定制专区
	config.mycreamt = config.server + '/credit/mycreamt'; //信用额度登记机构
	config.getcged = config.server + '/credit/getcged'; //采购额度
    config.getrestorehistory = config.server + '/credit/getrestorehistory';//信用流水记录
    config.getbillnotice = config.server + '/credit/getbillnotice';//票据到期提醒
    config.creditdetail = config.server + '/credit/creditdetail';//信用等级详情
    
    config.contractList   = config.server + '/contract/getList';//我的合同列表
    config.contractDetail = config.server + '/contract/getsingle';//我的合同详情
    config.getNoteList    = config.server + '/note/getlist';//我的票据列表
    config.noteDetail     = config.server + '/note/noteinfo';//我的票据详情 
    config.creditList     = config.server + '/applyCredit/creditList';//自有授信列表
    config.creditContractList   = config.server + '/applycredit/contractList';//自有授信列表
    config.getCreditDetail= config.server + '/applyCredit/creditSingle';//自有授信详情
    config.CreditAdd      = config.server + '/applyCredit/creditAdd';//申请自有授信
    config.CreditSave      = config.server + '/applyCredit/saveCredit';//保存自有授信
    config.CreditContract = config.server + '/applyCredit/contractList';//申请自有授信用途列表
    config.applyAttachment= config.server + '/applyCredit/uploadAttach';//上传申请附件
    config.checkApply     = config.server + '/applyCredit/updateStatus';//审核自有授信

    config.getlist = config.server + '/order/getlist';//我的订单-列表
    config.getdetail = config.server + '/order/getinfo';//我的订单-详情
    config.getchildrenlist = config.server + '/order/childrenlist';//我的子订单-列表
    config.getchildreninfo = config.server + '/order/childreninfo';//我的子订单-详情
    config.uchildstatus = config.server + '/order/uchildstatus';//我的子订单-更改状态
    config.getbatchs = config.server + '/order/getbatchs';//我的订单-批次详情
    config.orderlogistics = config.server + '/order/getlogistics';//我的订单-物流信息
    config.orderustatus = config.server + '/order/ustatus';//我的订单-提交订单状态
    config.orderpay = config.server + '/order/pay';//我的订单-订单支付
    config.ordercheck = config.server + '/order/check';//我的订单-订单验收
    config.orderrequestProc = config.server + '/order/requestProc';//我的订单-催办订单

    config.projectList = config.server + '/project/getList';//我的项目
    config.projectDetail = config.server + '/project/getDetail';//我的项目-订单详情
    config.projectCart = config.server + '/project/addCart';//我的项目-添加购物车

    config.addCart = config.server + '/goods/addCart';
    config.favoritesSave = config.server + '/favorites/save';
    config.favoritesDelete = config.server + '/favorites/delete';
    
    config.getsort = config.server + '/goods/getAllCategory'; //分类接口

    config.myproject = config.server + '/customize/myproject';//我的工程定制
    config.myprojectdetail = config.server + '/customize/myprojectdetail';//我的工程定制详情

    config.newsList = config.server + '/notice/getannouncelist'; //获取公告
    config.userInfo = config.server + '/credit/userinfo'; //获取个人资料

    config.recommendGoods = config.server + '/project/recommendGoods'; //获取默认发票信息
    
    
    //订单状态（二期）
    config.SENDORDERSTATUS = {
    	'0' : '已下单', 
        '1' : '处理中', 
        '2' : '验收中', 
    	'3' : '对账中', 
    	'4' : '已完成', 
    	'5' : '订单取消'
    };
    
    //子订单状态（二期）
    config.KIDORDERSTATUS = {
    		'0' : '未确认', 
    		'1' : '已确认', 
    		'2' : '客户已验签(发货)', 
    		'3' : '平台已验签(发货)', 
            '4' : '平台已推单(发货)',
    		'5' : '供应商已验签(发货)',
    		'6' : '平台已验签(发货)', 
    		'7' : '客户已验签(到货)', 
    		'8' : '平台已验签(到货)', 
    		'9' : '供应商已验签(到货)', 
    		'10' : '平台已验签(到货)',
    		'11' : '订单已取消',
    };

    //订单状态，0：已提交，1：确认中 ，2：验收中，3、对账中，4、完成
    //添加验签逻辑
    //-1订单提交->(验签)->
    //订单<2可以取消订单，订单=1取消需重新发起验签
    config.ORDERSTATUS = {
    	'0' : '已下单', 
        '1' : '处理中', 
        '2' : '验收中', 
        '3' : '对账中', 
        '4' : '已完成', 
        '5' : '订单取消'
    };

    //订单中发票类型
    config.INVTYPE = {
    	'0': '增值税专用发票',
    	'1': '普通发票'
    }

    //订单中的支付机构
    config.PAYENUM = {
    	'01': '浙商银行',
    	'02': '工商银行',
    	'03': '招商银行',
    	'04': '浦发银行',
    	'05': '中信银行',
    	'06': '中交银行',
    	'07': '华夏银行',
    };

    //订单状态值，0：已提交，1：确认中 ，2：验收中，3、对账中，4、完成，5、已取消
    config.ORDERENUM = {
        'YTJ': 0,
        'QRZ': 1,
        'YSZ': 2,
        'DZZ': 3,
        'WC': 4,
        'YQX': 5
    };

    //子订单状态
    config.SUBORDERSTATUS = {
        '-1': '已保存',
        '0': '已提交',
        '1': '待支付',
        '2': '确认支付',
        '3': '待验收',
        '4': '确认验收',
        '5': '完成',
    };

    //子订单状态值
    config.SUBORDERENUM = {
        'YTJ': 0,
        'DZF': 1,
        'QRZF': 2,
        'DYS': 3,
        'QRYS': 4,
        'WC': 5
    };

    //生成签名数据
    config.signPkcs7 = function(data){
        var result = {};
        var doit = document.getElementById('doit');

        if(!doit){
            result.success = false;
            result.errorInfo = '请插入object标签';
            return result;
        }
        if(typeof doit.SignPkcs7 == 'undefined'){
            result.success = false;
            result.errorInfo = '请插入U盾';
            return result;
        }
        var hr = doit.SignPkcs7(data);
        if (hr >= 0){
            result.success = true,
            result.errorInfo = "";
            result.data = doit.GetPkcsStr();
        }
        else{
            result.success = false;
            result.errorInfo = doit.ErrorMessage(hr);
            result.data = "";
        }

        return result;
    };

    //检测是否ie浏览器
    config.isMsie = function(){
        var msie = /msie/.test(navigator.userAgent.toLowerCase());
        var msie11 = /rv:11/.test(navigator.userAgent.toLowerCase());//ie11

        return msie || msie11;
    };

	module.exports = config;
});