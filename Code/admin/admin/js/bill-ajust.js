var BillAjust = {
	limit: 0,
	offset: 8,
	total_page: 0,
	url: "BillAdjustModel.php",
	entity: "bill_adjust",

	getAddInitAction: function(){
		var id = getQueryStringByName('id');
		if(id===""||!validateNumber(id)){
			return false;
		}
		params = {"user_id":id}
		strJson = createJson("addInit", this.entity, params);
		that = this
		console.log(strJson);
		$.post(this.url, strJson, function(obj){
			if(obj.error == -1){
				$('#message_area').html(createError(obj.message));
				return false;
			}else{
				console.log(obj.content);
				$.each(obj.content.contracts, function(k, v){
					
				});
			}
			$('#message_area').html('');
		}, "json");
	}
}