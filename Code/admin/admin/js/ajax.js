$(document).ajaxStart(function() {
	$("#loading").fadeIn();
});
$(document).ajaxSend(function(){
});
$(document).ajaxSuccess(function(){
	$("#error").fadeOut();
})
$(document).ajaxError(function(event, jqxhr, settings, thrownError){
	$("#error").html(settings.url +" error: "+ thrownError);
	$("#error").fadeIn();
})
$(document).ajaxComplete(function(){
})
$(document).ajaxStop(function () {
	$("#loading").fadeOut();
});
jQuery.fn.FormtoJson = function(options) {

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
var getQueryStringByName = function(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
var createPaginate = function(url,total,limit,offset){
	var total_page = Math.floor(total/offset);
	var str = '<table id="page-table" cellspacing="0"><tbody><tr><td align="right" nowrap="true">';
	str += '<div id="turn-page">总计'+total+'个记录，';
	str +=	'分为'+total_page+'页，';
	str +=	'当前第'+limit+'页，';
	str +=	'每页'+offset+'条'
	str +=	'<span id="page-link">';
	str +=	'<a href="javascript:void(0)" onclick="first()">第一页</a> ';
	str +=	'<a href="javascript:void(0)" onclick="prev()">上一页</a> ';
	str +=	'<a href="javascript:void(0)" onclick="next()">下一页</a> ';
	str +=	'<a href="javascript:void(0)" onclick="last()">最末页</a> ';
	str +=	'</span></div></td></tr></tbody></table>';
	return str;
}
var createJson = function(command, entity, parameters){
	var json = {"command":command,"entity":entity,"parameters":parameters};
	return json;
}
var createTd = function(value){
	var str = "<td>"+value+"</td>";
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
var createLink = function(url, text){
	var str = '<a href="'+url+'">'+text+'</a> ';
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