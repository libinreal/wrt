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
function td(val){
	var td = "<td>"+val+"</td>";
	return td;
}
function makeJson(command, entity, parameters){
	var json = {"command":command,"entity":entity,"parameters":parameters};
	return json;
}
function paginate(url,total,limit,offset){
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
function linkStr(link, text){
	var str = '<a href="'+link+'">'+text+'</a> ';
	return str;
}
function getQueryStringByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}