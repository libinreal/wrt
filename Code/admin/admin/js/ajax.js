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
var createJson = function(command, entity, parameters){
	var json = {"command":command,"entity":entity,"parameters":parameters};
	return  JSON.stringify(json);
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
var createLink = function(url, text, action){
	var str = '<a href="'+url+'" onclick="'+action+'">'+text+'</a> ';
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