$(document).ajaxStart(function() {
	$('#message_area').html(createLoading());
});
$(document).ajaxSend(function(){
});
$(document).ajaxSuccess(function(){
})
$(document).ajaxError(function(event, jqxhr, settings, thrownError){
	$("#error").html(settings.url +" error: "+ thrownError);
	$("#error").fadeIn();
})
$(document).ajaxComplete(function(){
})
$(document).ajaxStop(function () {
});
$.fn.FormtoJson = function(options) {
    options = jQuery.extend({}, options);

    var self = this,
        json = {},
        push_counters = {},
        patterns = {
            "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
            "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
            "push":     /^$/,
            "fixed":    /^\d+$/,
            "named":    /^[a-zA-Z0-9_]+$/
        };

    this.build = function(base, key, value){
        base[key] = value;
        return base;
    };

    this.push_counter = function(key){
        if(push_counters[key] === undefined){
            push_counters[key] = 0;
        }
        return push_counters[key]++;
    };

    jQuery.each(jQuery(this).serializeArray(), function(){

        // skip invalid keys
        if(!patterns.validate.test(this.name)){
            return;
        }

        var k,
            keys = this.name.match(patterns.key),
            merge = this.value,
            reverse_key = this.name;

        while((k = keys.pop()) !== undefined){

            // adjust reverse_key
            reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

            // push
            if(k.match(patterns.push)){
                merge = self.build([], self.push_counter(reverse_key), merge);
            }

            // fixed
            else if(k.match(patterns.fixed)){
                merge = self.build([], k, merge);
            }

            // named
            else if(k.match(patterns.named)){
                merge = self.build({}, k, merge);
            }
        }

        json = jQuery.extend(true, json, merge);
    });

    return json;
}
$.fn.slideFadeToggle = function(easing, callback) {
	return this.animate({ opacity: 'toggle', height: 'toggle' }, 'fast', easing, callback);
};
// value值改变监听
$.event.special.valuechange = {
  teardown: function (namespaces) {
    $(this).unbind('.valuechange');
  },
  handler: function (e) {
    $.event.special.valuechange.triggerChanged($(this));
  },
  add: function (obj) {
    $(this).on('keyup.valuechange cut.valuechange paste.valuechange input.valuechange', obj.selector, $.event.special.valuechange.handler)
  },
  triggerChanged: function (element) {
    var current = element[0].contentEditable === 'true' ? element.html() : element.val()
      , previous = typeof element.data('previous') === 'undefined' ? element[0].defaultValue : element.data('previous')
    if (current !== previous) {
      element.trigger('valuechange', [element.data('previous')])
      element.data('previous', current)
    }
  }
}
var getQueryStringByName = function(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
var createPaginate = function(url,total,limit,offset){
	var total_page = Math.ceil(total/offset);
    var current_page = limit+1;
	var str = '<table id="page-table" cellspacing="0"><tbody><tr><td align="right" nowrap="true">';
	str += '<div id="turn-page">总计'+total+'条记录，';
	str +=	offset+'条/页，';
    str +=  '共'+total_page+'页，';
	str +=	'当前第'+current_page+'页';
	str +=	'<span id="page-link">';
	if(total_page > 1 && current_page > 1){
		str +=	'&nbsp;<a href="javascript:void(0)" onclick="first()">第一页</a>&nbsp;|&nbsp;';
		str +=	'&nbsp;<a href="javascript:void(0)" onclick="prev()">上一页</a>&nbsp;|&nbsp;';
	}
	if(total_page > 1 && current_page < total_page){
		str +=	'&nbsp;<a href="javascript:void(0)" onclick="next()">下一页</a>&nbsp;|&nbsp;';
		str +=	'&nbsp;<a href="javascript:void(0)" onclick="last()">最末页</a>&nbsp;';
	}
	str +=	'</span></div></td></tr></tbody></table>';
	return str;
}
var createJson = function(command, entity, parameters, is_not_string){
    var json = {"command":command,"entity":entity,"parameters":parameters};
    if(typeof(is_not_string) === "undefined"){
        return  JSON.stringify(json);
    }else{
        return  json;
    }
}
var createTd = function(value, url){
	if(typeof(url)=='undefined'){
		var str = "<td>"+value+"</td>";
	}else{
		var str = "<td>"+createLink(url, value)+"</td>";
	}
	return str;
}
var createButton = function(func_name, value){
	var str = "<input type='button' class='button' value='"+value+"' onclick='"+func_name+"' />";
	return str;
}
var createCheckbox = function(name, value, label, checked){
	if(checked == 1){
		var str = "<input type='checkbox' name='"+name+"[]' value='"+value+"' checked='checked' /><label>"+label+"</label>";
	}else{
		var str = "<input type='checkbox' name='"+name+"[]' value='"+value+"' /><label>"+label+"</label>";
	}
	return str;
}
var createLink = function(url, text, action, target){
    if(typeof(target) === "undefined"){
        var str = '<a href="'+url+'" onclick="'+action+'" target=_self>'+text+'</a> ';
    }else if(target == "blank"){
        var str = '<a href="'+url+'" onclick="'+action+'" target=_blank>'+text+'</a> ';
    }
	return str;
}
var createError = function(text){
	var str = '<span class="error">'+text+'</span>';
	return str;
}
var createWarn = function(text){
	var str = '<div class="warn">'+text+'</div>';
	return str;
}
var createTip = function(text){
	var str = '<div class="tips">'+text+'</div>';
	return str;
}
var createLoading = function(){
	var str = '<div id="loading" align="center">Loading ...</div>';
	return str;
}
var redirectToUrl = function(url){
	window.location.replace(url);
}
var popupLayer = function(){
	$('#popupLayer').slideFadeToggle();
}
var appendOption = function(name, text, selected){
    if(selected === 1){
        var str = '<option value="'+name+'" selected="selected">'+text+'</option>';  
    }else{
        var str = '<option value="'+name+'">'+text+'</option>';
    }
	return str;
}
var subString = function(str, len, hasDot)
{ 
    var newLength = 0; 
    var newStr = ""; 
    var chineseRegex = /[^\x00-\xff]/g;
    var singleChar = ""; 
    var strLength = str.replace(chineseRegex,"**").length; 
    for(var i = 0;i < strLength;i++) 
    { 
        singleChar = str.charAt(i).toString(); 
        if(singleChar.match(chineseRegex) != null) 
        { 
            newLength += 2; 
        }     
        else 
        { 
            newLength++; 
        } 
        if(newLength > len) 
        { 
            break; 
        } 
        newStr += singleChar; 
    } 
     
    if(hasDot && strLength > len) 
    { 
        newStr += "..."; 
    } 
    return newStr; 
}
var validateNumber = function(str){
    return /^\d+$/.test(str);
}

var isNumeric = function( obj ) {
    return !jQuery.isArray( obj ) && (obj - parseFloat( obj ) + 1) >= 0;
}

function piliskys(input_id, show_id){
    var test1= $("#input_id").val();
    creat(input_id, show_id)  
}
function creat(input_id, show_id){
    var test1= $("#"+input_id).val();
    var money1 = new Number(test1);
    if(money1> 1000000000000000000) {
        alert("您输入的数字太大，重新输入！");
        return;
    }
    var monee = Math.round(money1*100).toString(10)
    var i,j;
    j=0;
    var leng = monee.length;
    var monval="";
    for( i=0;i<leng;i++){
        monval= monval+to_upper(monee.charAt(i))+to_mon(leng-i-1);
    }
    repace_acc(monval, show_id);
}

function to_upper( a)
{
    switch(a){
        case '0' : return '零'; break;
        case '1' : return '壹'; break;
        case '2' : return '贰'; break;
        case '3' : return '叁'; break;
        case '4' : return '肆'; break;
        case '5' : return '伍'; break;
        case '6' : return '陆'; break;
        case '7' : return '柒'; break;
        case '8' : return '捌'; break;
        case '9' : return '玖'; break;
        default: return '' ;
    }
}
function to_mon(a){
    if(a>10){
        a=a - 8;
        return(to_mon(a));
    }
    switch(a){
        case 0 : return '分'; break;
        case 1 : return '角'; break;
        case 2 : return '元'; break;
        case 3 : return '拾'; break;
        case 4 : return '佰'; break;
        case 5 : return '仟'; break;
        case 6 : return '万'; break;
        case 7 : return '拾'; break;
        case 8 : return '佰'; break;
        case 9 : return '仟'; break;
        case 10 : return '亿'; break;
    }
}
function repace_acc(Money, show_id){
    Money=Money.replace("零分","");
    Money=Money.replace("零角","零");
    var yy;
    var outmoney;
    outmoney=Money;
    yy=0;
    while(true){
        var lett= outmoney.length;
        outmoney= outmoney.replace("零元","元");
        outmoney= outmoney.replace("零万","万");
        outmoney= outmoney.replace("零亿","亿");
        outmoney= outmoney.replace("零仟","零");
        outmoney= outmoney.replace("零佰","零");
        outmoney= outmoney.replace("零零","零");
        outmoney= outmoney.replace("零拾","零");
        outmoney= outmoney.replace("亿万","亿零");
        outmoney= outmoney.replace("万仟","万零");
        outmoney= outmoney.replace("仟佰","仟零");
        yy= outmoney.length;
        if(yy==lett) break;
    }
    yy = outmoney.length;
    if ( outmoney.charAt(yy-1)=='零'){
        outmoney=outmoney.substring(0,yy-1);
    }
    yy = outmoney.length;
    if ( outmoney.charAt(yy-1)=='元'){
        outmoney=outmoney +'整';
    }
    $("#"+show_id).html(outmoney);
}
