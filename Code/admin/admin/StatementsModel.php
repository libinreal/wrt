<?php
/**
 * 报表查看，导出
 * @date 2016-03-22
 */

define('IN_ECS', true);
//
require(dirname(__FILE__) . '/includes/init.php');
	
	/**
	 * 导出Excel
	 * 请求方式：GET|POST 两种均可
	 * 请求地址：dev.zj.admin/admin/StatementsModel.php
	 * 请求参数：
	 * 			act = "customPageExport"
	 * 			//act 可能的值有 'customPageExport' 'suppliersPageExport' 'contractPageExport'
	 * 			params = '{"contract_name":"cccc","customer_name":"aaaa","contract_sn":"sss","due_date1":"2015-01-01","due_date2":"2015-01-01","limit":0,"offset":20000}'
	 * 	  		//params 是查询条件以及查询条数的json字符串
	 * 
	 */
	if ( in_array( $_REQUEST['act'], array( 'customPageExport', 'suppliersPageExport', 'contractPageExport' ) ) ) {
		$action = $_REQUEST['act'] . 'Action';

		$content['entity'] = 'order_info';
		$content['command'] = $_REQUEST['act'];

		$_REQUEST['params'] = $_REQUEST['params'] ? $_REQUEST['params'] : '{}';
		$params = json_decode( stripcslashes( $_REQUEST['params']), true );
		$my_params = array( 'where' => array( 'like' => array() ) );
		
		if( $params['contract_name'] ){
			$my_params['where']['like']['contract_name'] = $params['contract_name'];
		}

		if( $params['customer_name'] ){
			$my_params['where']['like']['customer_name'] = $params['customer_name'];
		}

		if( $params['contract_sn'] ){
			$my_params['where']['like']['contract_sn'] = $params['contract_sn'];
		}

		if( $params['due_date1'] ){
			$my_params['where']['due_date1'] = $params['due_date1'];
		}

		if( $params['due_date2'] ){
			$my_params['where']['due_date2'] = $params['due_date2'];
		}

		$my_params['limit'] = $params['limit'] ? $params['limit'] : 0;
		$my_params['offset'] = $params['offset'] ? $params['offset'] : 10000;

		$content['parameters'] = array( 
							'params' => $my_params
							);

		require './MyExcel.php';
		$statementsModel = new StatementsModel($content);
		$statementsModel->$action();
		exit;
	}

	class StatementsModel {
		
		//$_POST
		protected $content = false;
		//
		protected $command = false;
		
		//
		public function __construct($content){
			$this->content = $content;
			$this->command = $content['command'];
		}
		
		/**
		 * 
		 */
		public function run(){
			if ($this->command == 'customPage') {
				//
				$this->customPageAction();
			}elseif ($this->command == 'suppliersPage'){
				//
				$this->suppliersPageAction();
			}elseif ($this->command == 'contractPage'){
				//
				$this->contractPageAction();
			}elseif ($this->command == 'customPagePrint'){
				//
				$this->customPagePrintAction();
			}elseif ($this->command == 'suppliersPagePrint'){
				//
				$this->suppliersPagePrintAction();
			}elseif ($this->command == 'contractPagePrint'){
				//
				$this->contractPagePrintAction();
			}elseif ($this->command == 'customPageExport'){
				//
				$this->customPageExportAction();
			}elseif ($this->command == 'suppliersPageExport'){
				//
				$this->suppliersPageExportAction();
			}elseif ($this->command == 'contractPageExport'){
				//
				$this->contractPageExportAction();
			}
		}
		
		
		/**
		 * 接口名称：下游客户对帐单  
		 * 接口地址：http://admin.zj.dev/admin/StatementsModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'order_info',
	     *      "command": 'customPage',
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              "like":{
	     *              	"contract_name":"no11232",//合同名称
	     *              	"contract_sn":"no11232",//合同编号
	     *              },
	     *              "due_date1": 2015-01-01,//起始日期
	     *              "due_date2": 2015-01-01//结束日期
	     *              },
	     *              "limit": 0,//起始行号
	     *              "offset": 2//返回的行数
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "报表查询成功",
		 *	    "content": { 
		 *	    	"data":[
		 *	        {
		 *                  "order_sn":2 ,//订单号
		 *                  "goods_name": 'xxx' ,//产品名称
		 *                  "goods_sn": "ad11223", //产品编号
		 *                  "attr": "1mm/1m/3m/" ,//型号
		 *                  "goods_number_arr_buyer": 6,//发货数量
		 *                  "goods_price_arr_buyer": 1200 ,  //单价
		 *                  "shipping_fee_arr_buyer": 1000 ,//物流费
		 *                  "financial_arr": 1000 ,//金融费
		 *                  "order_amount_arr_buyer": 8200 ,//贷款额度
		 *                  "remark": "2323123dsd",//备注
		 *           }
		 *	         ],
		 *	         "count_total":100,//合计数量
		 *	         "amount_total":3,//合计金额
		 *	         "total":3
		 *	}
		 */
		public	function customPageAction(){

			$content = $this->calCustomPage();

			if( $content ){
				make_json_response( $content, "0", "帐单查询成功");
			}else{
				make_json_response("", "-1", "帐单查询失败");
			}
		}

		/**
		 * 接口名称： 供应商对帐单  
		 * 接口地址：http://admin.zj.dev/admin/StatementsModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'order_info',
	     *      "command": 'suppliersPage',
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              "like":{
	     *              	"customer_name":"no11232",//客户名称
	     *              },
	     *              "due_date1": 2015-01-01,//起始日期
	     *              "due_date2": 2015-01-01//结束日期
	     *              },
	     *              "limit": 0,//起始行号
	     *              "offset": 2//返回的行数
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "报表查询成功",
		 *	    "content": { 
		 *	    	"data":[
		 *	        {
		 *                  "order_sn":2 ,//订单号
		 *                  "goods_name": 'xxx' ,//产品名称
		 *                  "goods_sn": "ad11223", //产品编号
		 *                  "attr": "3mm/1m/20m" ,//型号
		 *                  "goods_number_arr_saler": 2,//发货数量
		 *                  "goods_price_arr_saler": 62 ,  //单价
		 *                  "order_amount_arr_saler": 10000,//贷款额度
		 *                  "shipping_fee_arr_saler": 1000,//物流
		 *                  "remark": 200,//备注
		 *           }
		 *	         ],
		 *	         "count_total":100,//合计数量
		 *	         "amount_total":3,//合计金额
		 *	         "total":3
		 *	}
		 */
		public	function suppliersPageAction(){

			$content = $this->calSuppliersPage();

			if( $content ){
				make_json_response( $content, "0", "账单查询成功");
			}else{
				make_json_response("", "-1", "账单查询失败");
			}
		}
		
		/**
		 * 接口名称： 项目内部对帐单  
		 * 接口地址：http://admin.zj.dev/admin/StatementsModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "contractPage",
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              "like":{
	     *              	"contract_name":"no11232",//项目名称
	     *              }
	     *              },
	     *              "limit": 0,//起始行号
	     *              "offset": 2//返回的行数
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "报表查询成功",
		 *	    "content": { 
		 *	    	"data":[
		 *	        {
		 *                  "order_sn":2 ,//订单号
		 *                  "goods_name": 'xxx' ,//商品名称
		 *                  "goods_sn": "ad11223", //商品编号
		 *                  "attr": "3mm/1m/20m" ,//型号
		 *                  
		 *                  "goods_number_arr_buyer": 6,//销售数量
		 *                  "goods_price_arr_buyer": 1200 ,  //销售价格
		 *                  "shipping_fee_arr_buyer": 1000 ,//销售订单物流费
		 *                  "financial_arr": 1000 ,//金融费
		 *                  "order_amount_arr_buyer": 8200 ,//销售金额
		 *                  "purchase_sn":"2-cg",//销售订单编号
		 *                  "goods_number_arr_saler": 2,//采购数量
		 *                  "goods_price_arr_saler": 62 ,  //采购价格
		 *                  "order_amount_arr_saler": 10000,//采购金额
		 *                  "shipping_fee_arr_saler": 1000,//采购订单物流
		 *                  "suppliers_name":"申通"//供应商名称
		 *                  "differential"：300,//交易差价
		 *                  "remark": 200,//备注
		 *           }
		 *	         ],
		 *	         "buyer_count_total":100,//总销售数量
		 *	         "buyer_amount_total":3,//总销售金额
		 *	         
		 *	         "saler_count_total":100,//总采购数量
		 *	         "saler_amount_total":3,//总采购金额
		 *	         "differential_total":2000,/总交易差价
		 *
		 * 			"dates":"2016-03-01———2016-03-22",//对账日期
		 *	         "total":3
		 *	}
		 */
		public	function contractPageAction(){
			
			$content = $this->calContractPage();

			if( $content ){
				make_json_response( $content, "0", "账单查询成功");
			}else{
				make_json_response("", "-1", "账单查询失败");
			}
		}


		/**
		 * 接口名称：下游客户对帐单导出
		 * 接口地址：http://admin.zj.dev/admin/StatementsModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'order_info',
	     *      "command": 'customPageExport',
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              "like":{
	     *              	"contract_name":"no11232",//合同名称
	     *              	"contract_sn":"no11232",//合同编号
	     *              },
	     *              "due_date1": 2015-01-01,//起始日期
	     *              "due_date2": 2015-01-01//结束日期
	     *              },
	     *              "limit": 0,//起始行号
	     *              "offset": 2//返回的行数
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "报表查询成功",
		 *	    "content": { 
		 *	    	"data":[
		 *	        {
		 *                  "order_sn":2 ,//订单号
		 *                  "goods_name": 'xxx' ,//产品名称
		 *                  "goods_sn": "ad11223", //产品编号
		 *                  "attr": "1mm/1m/3m/" ,//型号
		 *                  "goods_number_arr_buyer": 6,//发货数量
		 *                  "goods_price_arr_buyer": 1200 ,  //单价
		 *                  "shipping_fee_arr_buyer": 1000 ,//物流费
		 *                  "financial_arr": 1000 ,//金融费
		 *                  "order_amount_arr_buyer": 8200 ,//贷款额度
		 *                  "remark": "2323123dsd",//备注
		 *           }
		 *	         ],
		 *	         "count_total":100,//合计数量
		 *	         "amount_total":3,//合计金额
		 *	         "total":3
		 *	}
		 */
		public function customPageExportAction(){

			$data = $this->calCustomPage();
			if( $data ){
				if( $data['total'] == 0 ){
					echo '没有数据';
				}else{
					$myExcel = new MyExcel();
					$myExcel->customerStatements( $data );
				}
			}else{
				echo '导出失败';
			}
		}		
		

		/**
		 * 接口名称： 供应商对帐单导出
		 * 接口地址：http://admin.zj.dev/admin/StatementsModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'order_info',
	     *      "command": 'suppliersPageExport',
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              "like":{
	     *              	"customer_name":"no11232",//客户名称
	     *                  "due_date1": 2015-01-01,//起始日期
	     *                  "due_date2": 2015-01-01//结束日期
	     *              }
	     *              },
	     *              "limit": 0,//起始行号
	     *              "offset": 2//返回的行数
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "报表查询成功",
		 *	    "content": { 
		 *	    	"data":[
		 *	        {
		 *                  "order_sn":2 ,//订单号
		 *                  "goods_name": 'xxx' ,//产品名称
		 *                  "goods_sn": "ad11223", //产品编号
		 *                  "attr": "3mm/1m/20m" ,//型号
		 *                  "goods_number_arr_saler": 2,//发货数量
		 *                  "goods_price_arr_saler": 62 ,  //单价
		 *                  "order_amount_arr_saler": 10000,//贷款额度
		 *                  "shipping_fee_arr_saler": 1000,//物流
		 *                  "remark": 200,//备注
		 *           }
		 *	         ],
		 *	         "count_total":100,//合计数量
		 *	         "amount_total":3,//合计金额
		 *	         "total":3
		 *	}
		 */
		public function suppliersPageExportAction(){
			$data = $this->calSuppliersPage();
			if( $data ){

				if( $data['total'] != 0 ){
					$myExcel = new MyExcel();
					$myExcel->suppliersStatements( $data );
				}else{
					echo '没有数据';
				}
				
			}else{
				echo '导出失败';
			}
		}


		/**
		 * 接口名称： 项目内部对帐单  
		 * 接口地址：http://admin.zj.dev/admin/StatementsModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "contractPageExport",
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              "like":{
	     *              	"contract_name":"no11232",//项目名称
	     *              }
	     *              },
	     *              "limit": 0,//起始行号
	     *              "offset": 2//返回的行数
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "报表查询成功",
		 *	    "content": { 
		 *	    	"data":[
		 *	        {
		 *                  "order_sn":2 ,//订单号
		 *                  "goods_name": 'xxx' ,//商品名称
		 *                  "goods_sn": "ad11223", //商品编号
		 *                  "attr": "3mm/1m/20m" ,//型号
		 *                  
		 *                  "goods_number_arr_buyer": 6,//销售数量
		 *                  "goods_price_arr_buyer": 1200 ,  //销售价格
		 *                  "shipping_fee_arr_buyer": 1000 ,//销售订单物流费
		 *                  "financial_arr": 1000 ,//金融费
		 *                  "order_amount_arr_buyer": 8200 ,//销售金额
		 *                  "purchase_sn":"2-cg",//销售订单编号
		 *                  "goods_number_arr_saler": 2,//采购数量
		 *                  "goods_price_arr_saler": 62 ,  //采购价格
		 *                  "order_amount_arr_saler": 10000,//采购金额
		 *                  "shipping_fee_arr_saler": 1000,//采购订单物流
		 *                  "suppliers_name":"申通"//供应商名称
		 *                  "differential"：300,//交易差价
		 *                  "remark": 200,//备注
		 *           }
		 *	         ],
		 *	         "buyer_count_total":100,//总销售数量
		 *	         "buyer_amount_total":3,//总销售金额
		 *	         
		 *	         "saler_count_total":100,//总采购数量
		 *	         "saler_amount_total":3,//总采购金额
		 *	         "differential_total":2000,/总交易差价
		 *
		 * 			"dates":"2016-03-01———2016-03-22",//对账日期
		 *	         "total":3
		 *	}
		 */
		public function contractPageExportAction(){
			$data = $this->calContractPage();
			if( $data ){
				if( $data['total'] == 0 ){
					echo '没有数据';
				}else{
					$myExcel = new MyExcel();
					$myExcel->contractStatements( $data );
				}
			}else{
				echo '导出失败';
			}
		}

		/**
		 * 获取客户账单数据
		 * @return array|bool 账单列表或false
		 */
		private function calCustomPage(){

			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$contract_table = $GLOBALS["ecs"]->table("contract");
			$user_table = $GLOBALS["ecs"]->table("users");
			$order_table = $GLOBALS['ecs']->table('order_info');

			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//规格/型号/材质
			$category_table = $GLOBALS['ecs']->table('category');

			$sql = 'SELECT odr.`order_sn`, odr.`order_amount_arr_buyer`, odr.`shipping_fee_arr_buyer`, odr.`financial_arr`, og.`goods_sn`, og.`goods_name`, og.`goods_id`, ' .
				   ' odr.`inv_content` AS remark, odr.`add_time`, og.`goods_number_arr_buyer`, og.`goods_price_arr_buyer`, IFNULL( cat.`measure_unit`, \'\' ) AS `unit`, ' .
				   ' crt.`contract_name`, crt.`contract_num` FROM ' . $order_table . ' AS odr ' .
				   ' LEFT JOIN ' . $order_goods_table . ' AS og ON og.`order_id` = odr.`order_id` ' .
				   ' LEFT JOIN ' . $contract_table . ' AS crt ON crt.`contract_num` = odr.`contract_sn` ' .
				   ' LEFT JOIN ' . $category_table . ' AS cat ON cat.`code` = og.`cat_code` ';

			$total_sql = 'SELECT COUNT(*) as `total` FROM ' . $order_table .' AS odr' .
						 ' LEFT JOIN ' . $order_goods_table . ' AS og ON og.`order_id` = odr.`order_id` ' .
				         ' LEFT JOIN ' . $contract_table . ' AS crt ON crt.`contract_num` = odr.`contract_sn` ' .
				         ' LEFT JOIN ' . $category_table . ' AS cat ON cat.`code` = og.`cat_code` ';

			$where = array();	
			if( isset($params['where']) )
				$where = $params['where'];

			$where_str = '';

			$order_ids = '';
			$contract_ids = '';
			$user_ids = '';

			if( isset( $where["like"] ) )
			{
				$like = $where["like"];
				if( isset( $like["contract_name"] ) && $like['contract_name'] ){
					
					if( $where_str )
						$where_str .= ' AND crt.`contract_name` LIKE \'%' . $like['contract_name'] . '%\'';
					else
						$where_str .= ' WHERE crt.`contract_name` LIKE \'%' . $like['contract_name'] . '%\'';
				}else{
					make_json_response('', -1, '没有填写项目名称');
				}

				if( isset( $like["contract_sn"] ) && $like['contract_sn'] ){
					
					if( $where_str )
						$where_str .= ' AND crt.`contract_num` LIKE \'%' . $like['contract_sn'] . '%\'';
					else
						$where_str .= ' WHERE crt.`contract_num` LIKE \'%' . $like['contract_sn'] . '%\'';
				}
			}else{
				make_json_response('', -1, '没有填写项目名称');
			}

			if( isset( $where["due_date1"] ) && isset( $where["due_date2"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				$where['due_date2'] = strtotime( $where['due_date2'] );
				if( $where_str )
					$where_str .= " AND odr.`add_time` >= '" . $where['due_date1'] . "' AND odr.`add_time` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE odr.`add_time` >= '" . $where['due_date1'] . "' AND odr.`add_time` <= '" . $where['due_date2'] . "'";
			}
			else if( isset( $where["due_date1"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				if( $where_str )
					$where_str .= " AND odr.`add_time` >= '" . $where['due_date1'] . "'";
				else
					$where_str .= " WHERE odr.`add_time` >= '" . $where['due_date1'] . "'";
			}
			else if( isset( $where["due_date2"] ) )
			{
				$where['due_date2'] = strtotime( $where['due_date2'] );
				if( $where_str )
					$where_str .= " AND odr.`add_time` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE odr.`add_time` <= '" . $where['due_date2'] . "'";
			}

			//过滤子订单
			if( $where_str ){
				$where_str .= ' AND odr.`parent_order_id` IS NOT NULL AND odr.`parent_order_id` <> 0 AND odr.`child_order_status` = ' .
								SOS_ARR_PC2;
			}else{
				$where_str .= ' WHERE odr.`parent_order_id` IS NOT NULL AND odr.`parent_order_id` <> 0 AND odr.`child_order_status` = ' .
								SOS_ARR_PC2;
			}

			$sql = $sql . $where_str . ' ORDER BY odr.`add_time` ASC' .
				   ' LIMIT ' . $params['limit'].','.$params['offset'];
			$orders = $GLOBALS['db']->getAll($sql);
			
			$total_sql = $total_sql . $where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);
			$count_total = $amount_total = 0;

			if( $resultTotal )
			{
				$orders = $orders ? $orders : array();
				$contract_name_arr = array();
				$contract_sn_arr = array();

				$dates1 = $where['due_date1'] ? $where['due_date1'] : '';
				$dates2 = $where['due_date2'] ? $where['due_date2'] : '';

				//订单是否可取消
				foreach ($orders as &$v) {

					//规格、型号、材质
					$goods_attr_sql = 'SELECT `attr_value` FROM ' . $goods_attr_table .' WHERE `goods_id` = ' . $v['goods_id'];
					$goods_attr = $GLOBALS['db']->getAll( $goods_attr_sql );
					if( empty( $goods_attr ) ){
						$v['attr'] = '';
					}else{
						$attr_arr = array();
						foreach ($goods_attr as $value) {
							$attr_arr[] = $value['attr_value'];
						}
						$v['attr'] = implode('/', $attr_arr);
					}
					$count_total += $v['goods_number_arr_buyer'];
					$amount_total += $v['order_amount_arr_buyer'];

					if( !in_array($v['contract_name'], $contract_name_arr) ){
						$contract_name_arr[] = '《' . $v['contract_name'] . '》';
						$contract_sn_arr[] = '《' . $v['contract_num'] . '》';
					}

					unset($v['contract_name']);
					unset($v['contract_sn']);
					$v['goods_number_arr_buyer'] .= $v['unit'];

				}
				unset( $v );

				$content = array();

				$content['data'] = $orders;
				$content['total'] = $resultTotal['total'];
				$content['contract_name'] = implode(',', $contract_name_arr);

				$content['contract_sn'] = implode(',', $contract_sn_arr);

				$content['count_total'] = $count_total;
				$content['amount_total'] = $amount_total;
				$cur_row = count( $content['data'] );

				if( $content['total'] > 0 ){
					if( $dates1 && !$dates2){
						$dates = date('Y年m月d日', $dates1) . '—' . date('Y年m月d日', $content['data'][ $cur_row - 1 ]['add_time']);
					}else if(!$dates1 && $dates2 ){
						$dates = date('Y年m月d日', $content['data'][ 0 ]['add_time']) . '—' . date('Y年m月d日', $dates2);
					}else if($dates1 && $dates2){
						$dates = date('Y年m月d日', $dates1) . '—' .date('Y年m月d日', $dates2);
					}else{
						$dates = date('Y年m月d日', $content['data'][ 0 ]['add_time']) . '—' . date('Y年m月d日', $content['data'][ $cur_row - 1 ]['add_time']);
					}
				}
				$content['dates'] = $dates;

				return $content;
			}else{
				return false;
			}
		}


		/**
		 * 获取供应商账单数据
		 * @return array|bool 账单列表或false
		 */
		private function calSuppliersPage(){

			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$contract_table = $GLOBALS["ecs"]->table("contract");
			$user_table = $GLOBALS["ecs"]->table("users");
			$order_table = $GLOBALS['ecs']->table('order_info');

			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//规格/型号/材质
			$category_table = $GLOBALS['ecs']->table('category');

			$sql = 'SELECT odr.`order_sn`, odr.`add_time`, odr.`order_amount_arr_saler`, odr.`shipping_fee_arr_saler`, og.`goods_sn`, og.`goods_name`, og.`goods_id`, ' .
				   ' odr.`inv_content` AS remark, og.`goods_number_arr_saler`, og.`goods_price_arr_saler`, IFNULL( cat.`measure_unit`, \'\' ) AS `unit`, ' .
				   ' usr.`companyName` AS customer_name ' .
				   ' FROM ' . $order_table . ' AS odr ' .
				   ' LEFT JOIN ' . $order_goods_table . ' AS og ON og.`order_id` = odr.`order_id` ' .
				   ' LEFT JOIN ' . $contract_table . ' AS crt ON crt.`contract_num` = odr.`contract_sn` ' .
				   ' LEFT JOIN ' . $user_table . ' AS usr ON usr.`user_id` = crt.`customer_id` ' .
				   ' LEFT JOIN ' . $category_table . ' AS cat ON cat.`code` = og.`cat_code` ';

			$total_sql = 'SELECT COUNT(*) as `total` FROM ' . $order_table .' AS odr' .
						 ' LEFT JOIN ' . $order_goods_table . ' AS og ON og.`order_id` = odr.`order_id` ' .
				         ' LEFT JOIN ' . $contract_table . ' AS crt ON crt.`contract_num` = odr.`contract_sn` ' .
				         ' LEFT JOIN ' . $user_table . ' AS usr ON usr.`user_id` = crt.`customer_id` ' .
				         ' LEFT JOIN ' . $category_table . ' AS cat ON cat.`code` = og.`cat_code` ';
			$where = array();	
			if( isset($params['where']) )
				$where = $params['where'];

			$where_str = '';

			$order_ids = '';
			$contract_ids = '';
			$user_ids = '';

			if( isset( $where["like"] ) )
			{
				$like = $where["like"];
				if( isset( $like["contract_name"] ) && $like['contract_name'] ){
					
					if( $where_str )
						$where_str .= ' AND crt.`contract_name` LIKE \'%' . $like['contract_name'] . '%\'';
					else
						$where_str .= ' WHERE crt.`contract_name` LIKE \'%' . $like['contract_name'] . '%\'';
				}

				if( isset( $like["contract_sn"] ) && $like['contract_sn'] ){
					
					if( $where_str )
						$where_str .= ' AND crt.`contract_num` LIKE \'%' . $like['contract_sn'] . '%\'';
					else
						$where_str .= ' WHERE crt.`contract_num` LIKE \'%' . $like['contract_sn'] . '%\'';
				}

				if( isset( $like["customer_name"] ) && $like['customer_name'] ){
					
					if( $where_str )
						$where_str .= ' AND usr.`companyName` LIKE \'%' . $like['customer_name'] . '%\'';
					else
						$where_str .= ' WHERE usr.`companyName` LIKE \'%' . $like['customer_name'] . '%\'';
				}else{
					make_json_response('', -1, '没有填写客户名称');
				}
			}else{
				make_json_response('', -1, '没有填写客户名称');
			}

			if( isset( $where["due_date1"] ) && isset( $where["due_date2"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				$where['due_date2'] = strtotime( $where['due_date2'] );
				if( $where_str )
					$where_str .= " AND odr.`add_time` >= '" . $where['due_date1'] . "' AND odr.`add_time` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE odr.`add_time` >= '" . $where['due_date1'] . "' AND odr.`add_time` <= '" . $where['due_date2'] . "'";
			}
			else if( isset( $where["due_date1"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				if( $where_str )
					$where_str .= " AND odr.`add_time` >= '" . $where['due_date1'] . "'";
				else
					$where_str .= " WHERE odr.`add_time` >= '" . $where['due_date1'] . "'";
			}
			else if( isset( $where["due_date2"] ) )
			{
				$where['due_date2'] = strtotime( $where['due_date2'] );
				if( $where_str )
					$where_str .= " AND odr.`add_time` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE odr.`add_time` <= '" . $where['due_date2'] . "'";
			}

			//过滤子订单
			if( $where_str ){
				$where_str .= ' AND odr.`parent_order_id` IS NOT NULL AND odr.`parent_order_id` <> 0 AND odr.`child_order_status` = ' .
								SOS_ARR_PC2;
			}else{
				$where_str .= ' WHERE odr.`parent_order_id` IS NOT NULL AND odr.`parent_order_id` <> 0 AND odr.`child_order_status` = ' .
								SOS_ARR_PC2;
			}

			$sql = $sql . $where_str . ' ORDER BY odr.`add_time` ASC' .
				   ' LIMIT ' . $params['limit'].','.$params['offset'];
			$orders = $GLOBALS['db']->getAll($sql);
			
			$total_sql = $total_sql . $where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);
			$count_total = $amount_total = 0;

			if( $resultTotal )
			{
				$orders = $orders ? $orders : array();

				$dates1 = $where['due_date1'] ? $where['due_date1'] : '';
				$dates2 = $where['due_date2'] ? $where['due_date2'] : '';

				//订单是否可取消
				foreach ($orders as &$v) {

					//规格、型号、材质
					$goods_attr_sql = 'SELECT `attr_value` FROM ' . $goods_attr_table .' WHERE `goods_id` = ' . $v['goods_id'];
					$goods_attr = $GLOBALS['db']->getAll( $goods_attr_sql );
					if( empty( $goods_attr ) ){
						$v['attr'] = '';
					}else{
						$attr_arr = array();
						foreach ($goods_attr as $value) {
							$attr_arr[] = $value['attr_value'];
						}
						$v['attr'] = implode('/', $attr_arr);
					}

					$count_total += $v['goods_number_arr_saler'];
					$amount_total += $v['order_amount_arr_saler'];
					$v['order_sn'] .= '-cg';

					$v['goods_number_arr_saler'] .= $v['unit'];
				}
				unset( $v );

				$content = array();
				$content['data'] = $orders;
				$content['total'] = $resultTotal['total'];

				$cur_row = count( $content['data'] );

				if( $content['total'] > 0 ){
					if( $dates1 && !$dates2){
						$dates = date('Y年m月d日', $dates1) . '—' . date('Y年m月d日', $content['data'][ $cur_row - 1 ]['add_time']);
					}else if(!$dates1 && $dates2 ){
						$dates = date('Y年m月d日', $content['data'][ 0 ]['add_time']) . '—' . date('Y年m月d日', $dates2);
					}else if($dates1 && $dates2){
						$dates = date('Y年m月d日', $dates1) . '—' .date('Y年m月d日', $dates2);
					}else{
						$dates = date('Y年m月d日', $content['data'][ 0 ]['add_time']) . '—' . date('Y年m月d日', $content['data'][ $cur_row - 1 ]['add_time']);
					}
				}
				$content['dates'] = $dates;

				$content['count_total'] = $count_total;
				$content['amount_total'] = $amount_total;
				return $content;
			}else{
				return false;
			}
		}

		/**
		 * 获取项目内部账单数据
		 * @return array|bool 账单数据或false
		 */
		private function calContractPage(){
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$contract_table = $GLOBALS["ecs"]->table("contract");
			$user_table = $GLOBALS["ecs"]->table("users");
			$order_table = $GLOBALS['ecs']->table('order_info');

			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//规格/型号/材质
			$suppliers_table = $GLOBALS['ecs']->table('suppliers');

			$goods_table = $GLOBALS['ecs']->table('goods');
			$category_table = $GLOBALS['ecs']->table('category');

			$sql = 'SELECT odr.`order_sn`, odr.`add_time`, spl.`suppliers_name`, odr.`order_amount_arr_saler`, odr.`shipping_fee_arr_saler`, og.`goods_sn`, og.`goods_name`, og.`goods_id`, ' .
				   ' odr.`inv_content` AS remark, odr.`order_amount_arr_buyer`, odr.`financial_arr`, odr.`shipping_fee_arr_buyer`, ' .
				   ' og.`goods_number_arr_saler`, og.`goods_price_arr_saler`, og.`goods_number_arr_buyer`, og.`goods_price_arr_buyer`, IFNULL( cat.`measure_unit`, \'\' ) AS `unit`, ' .
				   ' crt.`contract_name` ' .
				   ' FROM ' . $order_table . ' AS odr ' .
				   ' LEFT JOIN ' . $order_goods_table . ' AS og ON og.`order_id` = odr.`order_id` ' .
				   ' LEFT JOIN ' . $contract_table . ' AS crt ON crt.`contract_num` = odr.`contract_sn` ' .
				   ' LEFT JOIN ' . $suppliers_table . ' AS spl ON spl.`suppliers_id` = odr.`suppers_id` ' .
				   ' LEFT JOIN ' . $category_table . ' AS cat ON cat.`code` = og.`cat_code` ';

			$total_sql = 'SELECT COUNT(*) as `total` FROM ' . $order_table .' AS odr' .
						 ' LEFT JOIN ' . $order_goods_table . ' AS og ON og.`order_id` = odr.`order_id` ' .
				         ' LEFT JOIN ' . $contract_table . ' AS crt ON crt.`contract_num` = odr.`contract_sn` ' .
				         ' LEFT JOIN ' . $suppliers_table . ' AS spl ON spl.`suppliers_id` = odr.`suppers_id` ' .
				   		 ' LEFT JOIN ' . $category_table . ' AS cat ON cat.`code` = og.`cat_code` ';

			$where = array();	
			if( isset($params['where']) )
				$where = $params['where'];

			$where_str = '';

			$order_ids = '';
			$contract_ids = '';
			$user_ids = '';

			if( isset( $where["like"] ) )
			{
				$like = $where["like"];
				if( isset( $like["contract_name"] ) && $like['contract_name'] ){
					
					if( $where_str )
						$where_str .= ' AND crt.`contract_name` LIKE \'%' . $like['contract_name'] . '%\'';
					else
						$where_str .= ' WHERE crt.`contract_name` LIKE \'%' . $like['contract_name'] . '%\'';
				}else{
					make_json_response('', -1, '没有填写项目名称');
				}

				if( isset( $like["contract_sn"] ) && $like['contract_sn'] ){
					
					if( $where_str )
						$where_str .= ' AND crt.`contract_num` LIKE \'%' . $like['contract_sn'] . '%\'';
					else
						$where_str .= ' WHERE crt.`contract_num` LIKE \'%' . $like['contract_sn'] . '%\'';
				}
			}else{
				make_json_response('', -1, '没有填写项目名称');
			}

			if( isset( $where["due_date1"] ) && isset( $where["due_date2"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				$where['due_date2'] = strtotime( $where['due_date2'] );
				if( $where_str )
					$where_str .= " AND odr.`add_time` >= '" . $where['due_date1'] . "' AND odr.`add_time` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE odr.`add_time` >= '" . $where['due_date1'] . "' AND odr.`add_time` <= '" . $where['due_date2'] . "'";
			}
			else if( isset( $where["due_date1"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				if( $where_str )
					$where_str .= " AND odr.`add_time` >= '" . $where['due_date1'] . "'";
				else
					$where_str .= " WHERE odr.`add_time` >= '" . $where['due_date1'] . "'";
			}
			else if( isset( $where["due_date2"] ) )
			{
				$where['due_date2'] = strtotime( $where['due_date2'] );
				if( $where_str )
					$where_str .= " AND odr.`add_time` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE odr.`add_time` <= '" . $where['due_date2'] . "'";
			}

			//过滤子订单
			if( $where_str ){
				$where_str .= ' AND odr.`parent_order_id` IS NOT NULL AND odr.`parent_order_id` <> 0 AND odr.`child_order_status` = ' .
								SOS_ARR_PC2;
			}else{
				$where_str .= ' WHERE odr.`parent_order_id` IS NOT NULL AND odr.`parent_order_id` <> 0 AND odr.`child_order_status` = ' .
								SOS_ARR_PC2;
			}

			$sql = $sql . $where_str . ' ORDER BY odr.`add_time` ASC' .
				   ' LIMIT ' . $params['limit'].','.$params['offset'];
			$orders = $GLOBALS['db']->getAll($sql);
			
			$total_sql = $total_sql . $where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);
			$count_total = $amount_total = $differential_total = 0;

			if( $resultTotal )
			{
				$orders = $orders ? $orders : array();

				$dates1 = $where['due_date1'] ? $where['due_date1'] : '';
				$dates2 = $where['due_date2'] ? $where['due_date2'] : '';
				
				//订单是否可取消
				foreach ($orders as &$v) {

					//规格、型号、材质
					$goods_attr_sql = 'SELECT `attr_value` FROM ' . $goods_attr_table .' WHERE `goods_id` = ' . $v['goods_id'];
					$goods_attr = $GLOBALS['db']->getAll( $goods_attr_sql );
					if( empty( $goods_attr ) ){
						$v['attr'] = '';
					}else{
						$attr_arr = array();
						foreach ($goods_attr as $value) {
							$attr_arr[] = $value['attr_value'];
						}
						$v['attr'] = implode('/', $attr_arr);
					}

					$saler_count_total += $v['goods_number_arr_saler'];
					$saler_amount_total += $v['order_amount_arr_saler'];

					$buyer_count_total += $v['goods_number_arr_buyer'];
					$buyer_amount_total += $v['order_amount_arr_buyer'];

					$v['purchase_sn'] = $v['order_sn'] . '-cg';

					$v['goods_number_arr_saler'] .= $v['unit'];
					$v['goods_number_arr_buyer'] .= $v['unit'];

					$v['differential'] = $v['order_amount_arr_buyer'] - $v['order_amount_arr_saler'];

					$differential_total += $v['differential'];
				}
				unset( $v );

				$content = array();
				$content['data'] = $orders;
				$content['total'] = $resultTotal['total'];

				$content['saler_count_total'] = $saler_count_total;
				$content['saler_amount_total'] = $saler_amount_total;

				$content['buyer_count_total'] = $buyer_count_total;
				$content['buyer_amount_total'] = $buyer_amount_total;
				$content['differential_total'] = $differential_total;

				$cur_row = count( $content['data'] );
				if( $content['total'] > 0 ){
					if( $dates1 && !$dates2){
						$dates = date('Y年m月d日', $dates1) . '—' . date('Y年m月d日', $content['data'][ $cur_row - 1 ]['add_time']);
					}else if(!$dates1 && $dates2 ){
						$dates = date('Y年m月d日', $content['data'][ 0 ]['add_time']) . '—' . date('Y年m月d日', $dates2);
					}else if($dates1 && $dates2){
						$dates = date('Y年m月d日', $dates1) . '—' .date('Y年m月d日', $dates2);
					}else{
						$dates = date('Y年m月d日', $content['data'][ 0 ]['add_time']) . '—' . date('Y年m月d日', $content['data'][ $cur_row - 1 ]['add_time']);
					}
				}
				$content['dates'] = $dates;

				return $content;
			}else{
				return false;
			}
		}
	}
	$content = jsonAction( array( "customPage", "suppliersPage", "contractPage", "customPagePrint", "suppliersPagePrint", "contractPagePrint",

						 ) );
	$statementsModel = new StatementsModel($content);
	$statementsModel->run();