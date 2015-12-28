// 公用接口调用
var getBillType = function(){
	strJson = createJson("bill_type", "bill_type", {});
	$.post("TypeModel.php", strJson, function(obj){
		return obj;
	});
	return "test";
}