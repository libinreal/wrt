/**
 * 我的合同js
 */
define(function(require) {
	var $ = require('jquery'),
	    config = require('./config'),
	    Ajax = require('../base/ajax'),
	    Tools = require('../base/tools');

	require('../base/common');
    require('../module/laydate/laydate.dev');
	
	//获取列表数据
    config.paging = function() {
        var formData = Ajax.formJson("#search_form");
        var data = {};
        $.each(formData, function(k, v){
            if(v != ""){
                data[k] = v;
            }
        });
        data.size = config.pageSize;
        Ajax.paging({
            url: config.contractList,
            data: data,
            renderFor : 'contract-list-tmpl', 
            renderEle : '#contract-list', 
            timeKey   : 'contract_id'
        });
    };
    config.paging();
    $(document).ready(function(){
        laydate({
            elem: '#start'
        });
        laydate({
            elem: '#end'
        });
        $('#search_button').on('click', function(e) {
            config.paging();
        });
    });
});