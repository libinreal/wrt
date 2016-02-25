<?php
/**
 * 供应商后台 
 */

define('IN_ECS', true);
//
require(dirname(__FILE__) . '/includes/init.php');
	
	/**
	 * 主要作用是：仅仅只做模板输出。具体数据需要POST调用 class里面的方法。
	 */
	if ($_REQUEST['act'] == 'edit' || $_REQUEST['act'] == 'add' ) {
		
		exit;
	}

	class SupplierModel {
		
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
			if ($this->command == 'find') {
				//
				$this->findAction();
			}elseif ($this->command == 'page'){
				//
				$this->pageAction();
			}elseif ($this->command == 'shippingAdd'){
				//
				$this->shippingAddAction();
			}elseif ($this->command == 'orderPage'){
				//
				$this->orderPageAction();
			}elseif ($this->command == 'orderDetail'){
				//
				$this->orderDetailAction();
			}elseif ($this->command == 'orderPayDetail'){
				//
				$this->orderPayDetailAction();
			}elseif($this->command == 'updateChilderStatus'){
				//
				$this->updateChilderStatusAction();
			}elseif($this->command == 'addShippingInfo'){
				//
				$this->addShippingInfoAction();
			}elseif($this->command == 'addShippingLog'){
				//
				$this->addShippingLogAction();			
			}elseif($this->command == 'addCategoryShippingFee'){
				//
				$this->addCategoryShippingFeeAction();			
			}elseif($this->command == 'initcategoryShipping'){
				//
				$this->initcategoryShippingAction();			
			}elseif($this->command == 'removeCategoryShipping'){
				//
				$this->removeCategoryShippingAction();			
			}elseif($this->command == 'saveCategorShipping'){
				//
				$this->saveCategorShippingAction();			
			}elseif($this->command == 'categoryShippingDetail'){
				//
				$this->categoryShippingDetailAction();			
			}elseif ($this->command == 'initOrderPay'){
				//应付单初始化
				$this->initOrderPayAction();
			}elseif ($this->command == 'createOrderPay'){
				//生成支付单信息
				$this->createOrderPayAction();
			}elseif( $this->command == 'orderPayList' ){
				//
				$this->orderPayListAction();
			}elseif( $this->command == 'upload' ){
				//
				$this->uploadAction();
			}
			elseif( $this->command == 'completeList'){
				//
				$this->completeListAction();
			}


		}
				
		public function findAction(){
			
		}

		/**
		 * @param suppers_id int 订单关联的供应商id
		 * @return [type] [description]
		 */
		private function orderPrivilege( $suppers_id = '', $child_order_status = 0 )
		{
			//验证是否有权限
			$admin_sql = "SELECT `user_id`, `user_name`, `suppliers_id` ".
	           "FROM " .$GLOBALS['ecs']->table('admin_user'). " WHERE `user_id` = '".$_SESSION['admin_id']."'";
	    	$admin_user = $GLOBALS['db']->getRow($admin_sql);

	    	if( empty( $admin_user['suppliers_id'] ) || $admin_user['suppliers_id'] != $suppers_id ){
	    		make_json_response('', '-1', '权限不足，无法执行该操作');
	    	}

	    	if ( $child_order_status < SOS_SEND_PP ){//未推单
	    		make_json_response('', '-1', '无法执行该操作');
	    	}
		}

		/**
		 * 获取供应商id
		 * @return int 供应商id
		 */
		private function getSuppliersId(){
			$sql = "SELECT `user_id`, `user_name`, `suppliers_id` ".
		           "FROM " .$GLOBALS['ecs']->table('admin_user'). " WHERE `user_id` = '".$_SESSION['admin_id']."'";
		    $admin_user = $GLOBALS['db']->getRow($sql);

		    if( empty( $admin_user['suppliers_id'] ) ){
		    	return 0;
		    }else{
		    	return $admin_user['suppliers_id'];
		    }
		}

		/**
		 * 获取供应商名称
		 * @param $suppliers_id 供应商id
		 * @return $result string 供应商名称
		 */
		private function getSuppliersName( $suppliers_id = 0 )
		{
			if( empty($suppliers_id) ){
				$suppliers_id = $this->getSuppliersId();
			}
			$sql = "SELECT `suppliers_name` FROM " .$GLOBALS['ecs']->table('suppliers'). " WHERE `suppliers_id` = '". $suppliers_id ."'";
		    $result = $GLOBALS['db']->getOne($sql);

		    if( empty( $result ) ){
		    	return '';
		    }else{
		    	return $result;
		    }
		}

		/**
		 * 接口名称：订单列表
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'order_info',
	     *      "command": 'orderPage',
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              "like":{"order_sn":"no11232","goods_name":"14m宽轧钢"},//订单编号 商品名称
	     *                  "status": 0,//订单状态 0 未确认
	     *                  "due_date1": 2015-01-01,//起始日期
	     *                  "due_date2": 2015-01-01,//结束日期
	     *              },
	     *              "limit": 0,//起始行号
	     *              "offset": 2//返回的行数
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "订单列表查询成功",
		 *	    "content": { 
		 *	    	"data":[
		 *	        {
		 *	        		"order_id":1, //订单ID
		 *                  "order_sn":2 ,//订单号
		 *                  "goods_name": 'xxx' ,//商品名称
		 *                  "goods_sn": "ad11223", //商品编号
		 *                  "attr": "2015-12-12" ,//规格/型号/材质
		 *                  "goods_price": "薛某" ,//单价
		 *                  "goods_number": 12 ,  //数量
		 *                  "shipping_fee": 300.00 ,//物流费
		 *                  "order_status": "" ,//订单状态
		 *           }
		 *	         ],
		 *	         "total":3
		 *	}
		 */
		public	function orderPageAction(){
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$order_table = $GLOBALS['ecs']->table('order_info');
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//规格/型号/材质

			$suppliers_id = $this->getSuppliersId();
			$childer_map = C('purchase_to_childer_map');
		    if( empty( $suppliers_id ) ){
		    	make_json_response('', '-1', '当前登录的必须是供应商账号');
		    }

			$sql = 'SELECT odr.`order_id` , odr.`order_sn`, og.`goods_id`, og.`goods_name`, og.`goods_sn`, odr.`add_time`, ' .
				   ' og.`goods_price_send_saler`, og.`goods_price_arr_saler`, og.`goods_number_send_saler`, og.`goods_number_arr_saler`,' .
				   ' odr.`shipping_fee_send_saler`,odr.`shipping_fee_arr_saler`, odr.`child_order_status` ' .
				   ' FROM ' . $order_table .
				   ' AS odr LEFT JOIN ' . $order_goods_table . ' AS og ON odr.`order_id` = og.`order_id`' .
				   ' WHERE odr.`suppers_id` = ' . $suppliers_id . ' AND odr.`child_order_status` >= ' . SOS_SEND_PP .
				   ' ORDER BY odr.`add_time` DESC';//订单为已推给当前登录的供应商

			$total_sql = 'SELECT COUNT(*) AS `total`' .
				   		 ' FROM ' . $order_table .
				   		 ' AS odr LEFT JOIN ' . $order_goods_table . ' AS og ON odr.`order_id` = og.`order_id`' .
				   		 ' WHERE odr.`suppers_id` = ' . $suppliers_id . ' AND odr.`child_order_status` >= ' . SOS_SEND_PP;
		
			$where = array();	
			if( isset($params['where']) )
				$where = $params['where'];

			$where_str = '';

			if( isset( $where["like"] ) )
			{
				$like = $where["like"];

				if( isset( $like["order_sn"] ) ){
					$where_str .= ' AND odr.`order_sn` LIKE \'%' . trim( $like['order_sn'] ) . '%\'';					
				}

				if( isset( $like["goods_name"] ) ){
					$where_str .= ' AND og.`goods_name` LIKE \'%' . trim( $like['goods_name'] ) . '%\'';
				}
			
			}
			
			if( isset( $where["status"] ) )
			{
				$where_status = intval( $where['status'] );
				
				if( isset( $childer_map[ $where_status ] ) ){

					$childer_map_values = $childer_map[ $where_status ];
					$chiler_status_str = implode(',', $childer_map_values);
					$where_str .= " AND odr.`child_order_status` IN(" . $chiler_status_str . ")";

				}
			}	

			if( isset( $where["due_date1"] ) && isset( $where["due_date2"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				$where['due_date2'] = strtotime( $where['due_date2'] );
				
				$where_str .= " AND odr.`add_time` >= '" . $where['due_date1'] . "' AND odr.`add_time` <= '" . $where['due_date2'] . "'";
				
			}
			else if( isset( $where["due_date1"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				$where_str .= " AND odr.`add_time` >= '" . $where['due_date1'] . "'";
			}
			else if( isset( $where["due_date2"] ) )
			{
				$where['due_date2'] = strtotime( $where['due_date2'] );
				$where_str .= " AND odr.`add_time` <= '" . $where['due_date2'] . "'";
			}

			$sql = $sql . $where_str .
				   ' LIMIT ' . $params['limit'].','.$params['offset'];//echo $sql;exit;
			$orders = $GLOBALS['db']->getAll($sql);
			
			$total_sql = $total_sql . $where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);

			if( $resultTotal )
			{
				$orders = $orders ? $orders : array();
				$purchase_status = C('purchase_status');
				//订单是否可取消
				foreach ($orders as &$v) {
					$v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
					//物流费
					if( $v['child_order_status'] <= SOS_SEND_PC2){
						$v['shipping_fee'] = $v['shipping_fee_send_saler'];//发货
						$v['goods_price'] = $v['goods_price_send_saler'];//发货
						$v['goods_number'] = $v['goods_number_send_saler'];//发货
					}else{
						$v['shipping_fee'] = $v['shipping_fee_arr_saler'];//到货
						$v['goods_number'] = $v['goods_number_arr_saler'];//到货
						$v['goods_price'] = $v['goods_price_arr_saler'];//到货
					}

					$v['order_status'] = '';
					//订单状态转换
					if (!empty($childer_map)) {
						foreach ($childer_map as $pk => $pv) {
							foreach ($pv as $s) {
								if( $s == $v['child_order_status'] ){
									$v['order_status'] = $purchase_status[ $pk ];
									break 2;
								}
							}
						}
					}
					

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

				}
				unset( $v );

				$content = array();
				$content['data'] = $orders;
				$content['total'] = $resultTotal['total'];
				make_json_response( $content, "0", "订单查询成功");
			}
			else
			{
				make_json_response("", "-1", "订单查询失败");
			}
		}
		
		/**
		 * 接口名称：已完成的订单
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "completeList",
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *                  "status": 0,//订单状态 0 未确认
	     *                  "due_date1": 2015-01-01,//起始日期
	     *                  "due_date2": 2015-01-01,//结束日期
	     *              },
	     *              "limit": 0,//起始行号
	     *              "offset": 2//返回的行数
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "订单列表查询成功",
		 *	    "content": { 
		 *	    	"data":[
		 *	        {
		 *	        		"order_id":1, //订单ID
		 *                  "order_sn":2 ,//订单号
		 *                  "goods_name": 'xxx' ,//商品名称
		 *                  "goods_sn": "ad11223", //商品编号
		 *                  "attr": "2015-12-12" ,//规格/型号/材质
		 *                  "goods_price": "薛某" ,//单价
		 *                  "goods_number": 12 ,  //数量
		 *                  "shipping_fee": 300.00 ,//物流费
		 *                  "purchase_pay_status": 0,//订单状态
		 *           }
		 *	         ],
		 *	         "total":3
		 *	}
		 */
		public function completeListAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$order_table = $GLOBALS['ecs']->table('order_info');
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//规格/型号/材质

			$suppliers_id = $this->getSuppliersId();
			$childer_map = C('purchase_to_childer_map');
		    if( empty( $suppliers_id ) ){
		    	make_json_response('', '-1', '当前登录的必须是供应商账号');
		    }

			$sql = 'SELECT odr.`order_id` , odr.`order_sn`, og.`goods_id`, og.`goods_name`, og.`goods_sn`, odr.`add_time`, ' .
				   ' og.`goods_price_send_saler`, og.`goods_price_arr_saler`, og.`goods_number_send_saler`, og.`goods_number_arr_saler`,' .
				   ' odr.`shipping_fee_send_saler`,odr.`shipping_fee_arr_saler`, odr.`child_order_status`,odr.`purchase_pay_status` ' .
				   ' FROM ' . $order_table .
				   ' AS odr LEFT JOIN ' . $order_goods_table . ' AS og ON odr.`order_id` = og.`order_id`' .
				   ' WHERE odr.`suppers_id` = ' . $suppliers_id . ' AND odr.`child_order_status` >= ' . SOS_SEND_PP . ' AND ' .//订单为已推给当前登录的供应商
				   ' odr.`child_order_status` <> ' . SOS_CANCEL;

			$total_sql = 'SELECT COUNT(*) AS `total`' .
				   		 ' FROM ' . $order_table .
				   		 ' AS odr LEFT JOIN ' . $order_goods_table . ' AS og ON odr.`order_id` = og.`order_id`' .
				   		 ' WHERE odr.`suppers_id` = ' . $suppliers_id . ' AND odr.`child_order_status` >= ' . SOS_SEND_PP .
				   		 ' AND odr.`child_order_status` <> ' . SOS_CANCEL;
		
			$where = array();	
			if( isset($params['where']) )
				$where = $params['where'];

			$where_str = ' AND odr.`child_order_status` = ' . SOS_ARR_PC2 . ' ';//已完成的采购订单
			
			if( isset( $where["status"] ) )
			{
				$where_status = intval( $where['status'] );
				$where_str .= ' AND odr.`purchase_pay_status` = ' . $where_status;

			}	

			if( isset( $where["due_date1"] ) && isset( $where["due_date2"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				$where['due_date2'] = strtotime( $where['due_date2'] );
				
				$where_str .= " AND odr.`add_time` >= '" . $where['due_date1'] . "' AND odr.`add_time` <= '" . $where['due_date2'] . "'";
				
			}
			else if( isset( $where["due_date1"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				$where_str .= " AND odr.`add_time` >= '" . $where['due_date1'] . "'";
			}
			else if( isset( $where["due_date2"] ) )
			{
				$where['due_date2'] = strtotime( $where['due_date2'] );
				$where_str .= " AND odr.`add_time` <= '" . $where['due_date2'] . "'";
			}

			$sql = $sql . $where_str . ' ORDER BY odr.`add_time` DESC' .
				   ' LIMIT ' . $params['limit'].','.$params['offset'];//echo $sql;exit;
			$orders = $GLOBALS['db']->getAll($sql);
			
			$total_sql = $total_sql . $where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);

			if( $resultTotal )
			{
				$orders = $orders ? $orders : array();
				$purchase_status = C('purchase_status');
				//订单是否可取消
				foreach ($orders as &$v) {
					$v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
					//物流费
					if( $v['child_order_status'] <= SOS_SEND_PC2){
						$v['shipping_fee'] = $v['shipping_fee_send_saler'];//发货
						$v['goods_price'] = $v['goods_price_send_saler'];//发货
						$v['goods_number'] = $v['goods_number_send_saler'];//发货
					}else{
						$v['shipping_fee'] = $v['shipping_fee_arr_saler'];//到货
						$v['goods_number'] = $v['goods_number_arr_saler'];//到货
						$v['goods_price'] = $v['goods_price_arr_saler'];//到货
					}

					$v['order_status'] = '';
					//订单状态转换
					if (!empty($childer_map)) {
						foreach ($childer_map as $pk => $pv) {
							foreach ($pv as $s) {
								if( $s == $v['child_order_status'] ){
									$v['order_status'] = $purchase_status[ $pk ];
									break 2;
								}
							}
						}
					}
					

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

				}
				unset( $v );

				$content = array();
				$content['data'] = $orders;
				$content['total'] = $resultTotal['total'];
				make_json_response( $content, "0", "订单查询成功");
			}
			else
			{
				make_json_response("", "-1", "订单查询失败");
			}	
		}

		/**
		 * 接口名称：生成应收单初始化
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
		 *  {
		 *	    "command": "initOrderPay",
		 *	    "entity": "order_pay",
		 *	    "parameters": {
		 *	        "order_id":"1,2,3"//多个id用","分隔
		 *	    }
		 *	}
		 * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "应收单详情查询成功",
		 *	    "content": 
		 *	    { 
		 *	    	"info":
		 *	    	{
		 *	    		"order_total":1500,//总金额
		 *	    		"order_count":3,//订单数量
		 *	    	},
		 *	    	"orders"://订单信息
		 *	        [
		 *	        {
		 *	        	"order_id":1,//订单id
		 *	        	"order_sn":"os122311",//订单编号
		 *	        	"goods_name":"纹钢",//商品名称
		 *	        	"goods_sn":"s20000",//商品编号
		 *	        	"attr":"",//规格/牌号/材质
		 *	        	"goods_price":152,//单价
		 *          	"goods_number":251,//数量
		 *          	"shipping_fee":250.0//物流
		 *          	"total":250.0//小计
		 *	        }
		 *	        ],
		 *	        "file_0"://发票文件
		 *	        [
		 *	        	{
		 *	        		"upload_name":"20160224_s1.jpg",//文件名字
		 *	        		"upload_id":1,//文件id
		 *	        	}
		 *	        ],
		 *	        "file_1"://送货单文件
		 *	        [
		 *	        	{
		 *	        		"upload_name":"20160224_s1.jpg",//文件名字
		 *	        		"upload_id":1,//文件id
		 *	        	}
		 *	        ]
		 *	    }
		 *	}
		 */
		public function initOrderPayAction()
		{
			$content = $this->content;
			$order_id_str = strval( $content['parameters']['order_id'] );
			$order_ids = array();
			if (!empty($order_id_str)) {
				$order_ids = explode(',',$order_id_str);
			}

			$suppliers_id = $this->getSuppliersId();
			if( empty( $suppliers_id ) ){
				make_json_response('', '-1', '当前登录的必须是供应商账号');
			}

			
			$order_table = $GLOBALS['ecs']->table('order_info');
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//规格/型号/材质

			$order_pay_table = $GLOBALS['ecs']->table('order_pay');//生成单表
			$upload_table = $GLOBALS['ecs']->table('order_pay_upload');//文件上传表

			$sql = 'SELECT odr.`order_id` , odr.`order_sn`, og.`goods_id`, og.`goods_name`, og.`goods_sn`, odr.`add_time`, ' .
				   ' og.`goods_price_send_saler`, og.`goods_price_arr_saler`, og.`goods_number_send_saler`, og.`goods_number_arr_saler`,' .
				   ' odr.`shipping_fee_send_saler`,odr.`shipping_fee_arr_saler`, odr.`child_order_status`,odr.`purchase_pay_status`, ' .
				   ' odr.`order_amount_send_saler`, odr.`order_amount_arr_saler`' .
				   ' FROM ' . $order_table .
				   ' AS odr LEFT JOIN ' . $order_goods_table . ' AS og ON odr.`order_id` = og.`order_id`' .
				   ' WHERE odr.`suppers_id` = ' . $suppliers_id . ' AND odr.`child_order_status` >= ' . SOS_ARR_PC2 .//订单为已推给当前登录的供应商
				   ' AND odr.`purchase_pay_status` = 0 AND odr.`child_order_status` <> ' . SOS_CANCEL .' AND odr.`order_id` IN (' . $order_id_str . ')';

			$order_infos = $GLOBALS['db']->getAll( $sql );

			if( empty( $order_infos ) ){
				make_json_response('', '-1', '没有可以生成应付款的订单');
			}

			$order_total = 0;
			foreach ($order_infos as $order_info){

				$order_goods = array();

				$order_goods['order_id'] = $order_info['order_id'];
				$order_goods['order_sn'] = $order_info['order_sn'] . '-cg,';
				$order_goods['goods_sn'] = $order_info['goods_sn'];
				$order_goods['goods_name'] = $order_info['goods_name'];
				// $order_goods['add_time'] = date('Y-m-d H:i:s', $order_info['add_time']);
				//物流费
				if( $order_goods['child_order_status'] <= SOS_SEND_PC2){
					$order_goods['shipping_fee'] = $order_info['shipping_fee_send_saler'];//发货
					$order_goods['goods_price'] = $order_info['goods_price_send_saler'];//发货
					$order_goods['goods_number'] = $order_info['goods_number_send_saler'];//发货
					$order_goods['total'] = $order_info['order_amount_send_saler'];//发货
				}else{
					$order_goods['shipping_fee'] = $order_info['shipping_fee_arr_saler'];//到货
					$order_goods['goods_number'] = $order_info['goods_number_arr_saler'];//到货
					$order_goods['goods_price'] = $order_info['goods_price_arr_saler'];//到货
					$order_goods['total'] = $order_info['order_amount_arr_saler'];//到货
				}

				//规格、型号、材质
				$goods_attr_sql = 'SELECT `attr_value` FROM ' . $goods_attr_table .' WHERE `goods_id` = ' . $order_info['goods_id'];
				$goods_attr = $GLOBALS['db']->getAll( $goods_attr_sql );
				if( empty( $goods_attr ) ){
					$order_goods['attr'] = '';
				}else{
					$attr_arr = array();
					foreach ($goods_attr as $value) {
						$attr_arr[] = $value['attr_value'];
					}
					$order_goods['attr'] = implode('/', $attr_arr);
				}

				$order_total += $order_goods['total'];
				$goods[] = $order_goods;
			}

			$content = array();
			$content['info'] = array('order_total' => $order_total, 'order_count' => count( $goods) );
			$content['orders'] = $goods;

			$file_0 = $file_1 = array();

			//已上传文件
			$upload_sql = 'SELECT `upload_name`,`upload_type`, `upload_id` FROM ' . $upload_table;
			$files = $GLOBALS['db']->getAll( $upload_sql );

			$file_0 = $file_1 = array();

			foreach ($files as $f) {
				if( $f['upload_type'] == 0 )
					$file_0[] = array('upload_id' => $f['upload_id'], 'upload_name' => $f['upload_name']);
				else
					$file_1[] = array('upload_id' => $f['upload_id'], 'upload_name' => $f['upload_name']);
			}

			$content['file_0'] = $file_0;
			$content['file_1'] = $file_1;
			make_json_response($content, '0', '生成初始化成功');
		}


		/**
		 * 接口名称：生成应收单
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
		 *  {
		 *	    "command": "createOrderPay",
		 *	    "entity": "order_pay",
		 *	    "parameters": {
		 *	        "order_id":"1,2,3"//多个id用","分隔
		 *	        "file_0":["20160224.jpg", "20160224.jpg"],//上传的发票文件名字，数组形式
		 *	        "file_1":["20160224.jpg", "20160224.jpg"]//上传的送货单文件名字，数组形式
		 *	    }
		 *	}
		 * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "生成应收单成功",
		 *	    "content": {}
		 *	}
		 */
		public function createOrderPayAction(){
// 			print_r($_SESSION);die;
			$content = $this->content;
			$order_id_str = $content['parameters']['order_id'];
			$order_ids = array();
			if (!empty($order_id_str)) {
				$order_ids = explode(',',$order_id_str);
			}

			$file_0 = $content['parameters']['file_0'];
			$file_1 = $content['parameters']['file_1'];

			if( !is_array( $file_0 ) )
				$file_0 = array();

			if( !is_array( $file_1 ) )
				$file_1 = array();

			$suppliers_id = $this->getSuppliersId();
			if( empty( $suppliers_id ) ){
				make_json_response('', '-1', '当前登录的必须是供应商账号');
			}

			$order_table = $GLOBALS['ecs']->table('order_info');
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//规格/型号/材质

			$order_pay_table = $GLOBALS['ecs']->table('order_pay');//生成单表

			$sql = 'SELECT odr.`order_id` , odr.`order_sn`, og.`goods_id`, og.`goods_name`, og.`goods_sn`, odr.`add_time`, ' .
				   ' og.`goods_price_send_saler`, og.`goods_price_arr_saler`, og.`goods_number_send_saler`, og.`goods_number_arr_saler`,' .
				   ' odr.`shipping_fee_send_saler`,odr.`shipping_fee_arr_saler`, odr.`child_order_status`,odr.`purchase_pay_status`, ' .
				   ' odr.`order_amount_send_saler`, odr.`order_amount_arr_saler`' .
				   ' FROM ' . $order_table .
				   ' AS odr LEFT JOIN ' . $order_goods_table . ' AS og ON odr.`order_id` = og.`order_id`' .
				   ' WHERE odr.`suppers_id` = ' . $suppliers_id . ' AND odr.`child_order_status` >= ' . SOS_ARR_PC2 .//订单为已推给当前登录的供应商
				   ' AND odr.`purchase_pay_status` = 0 AND odr.`child_order_status` <> ' . SOS_CANCEL .' AND odr.`order_id` IN (' . $order_id_str . ')';

			$order_infos = $GLOBALS['db']->getAll( $sql );

			if( empty( $order_infos ) ){
				make_json_response('', '-1', '没有可以生成应付款的订单');
			}

			$order_sn_str = '';
			$order_id_str = '';
			$order_total = 0;
			foreach ($order_infos as $order_info){

				$order_goods = array();

				$order_goods['order_id'] = $order_info['order_id'];
				$order_goods['order_sn'] = $order_info['order_sn'];
				$order_goods['goods_sn'] = $order_info['goods_sn'];
				$order_goods['goods_name'] = $order_info['goods_name'];
				// $order_goods['add_time'] = date('Y-m-d H:i:s', $order_info['add_time']);
				//物流费
				if( $order_goods['child_order_status'] <= SOS_SEND_PC2){
					$order_goods['shipping_fee'] = $order_info['shipping_fee_send_saler'];//发货
					$order_goods['goods_price'] = $order_info['goods_price_send_saler'];//发货
					$order_goods['goods_number'] = $order_info['goods_number_send_saler'];//发货
					$order_goods['total'] = $order_info['order_amount_send_saler'];//发货
				}else{
					$order_goods['shipping_fee'] = $order_info['shipping_fee_arr_saler'];//到货
					$order_goods['goods_number'] = $order_info['goods_number_arr_saler'];//到货
					$order_goods['goods_price'] = $order_info['goods_price_arr_saler'];//到货
					$order_goods['total'] = $order_info['order_amount_arr_saler'];//到货
				}

				//规格、型号、材质
				$goods_attr_sql = 'SELECT `attr_value` FROM ' . $goods_attr_table .' WHERE `goods_id` = ' . $order_info['goods_id'];
				$goods_attr = $GLOBALS['db']->getAll( $goods_attr_sql );
				if( empty( $goods_attr ) ){
					$order_goods['attr'] = '';
				}else{
					$attr_arr = array();
					foreach ($goods_attr as $value) {
						$attr_arr[] = $value['attr_value'];
					}
					$order_goods['attr'] = implode('/', $attr_arr);
				}

				$order_sn_str .= $order_info['order_sn'] . '-cg,';
				$order_id_str .= $order_info['order_id'] . ',';

				$order_total += $order_goods['total'];
				$json_goods[ $order_info['order_id'] ] = $order_goods;
			}

			$data = array();
			$data['user_id'] = $_SESSION['admin_id'];
			$data['order_id_str'] = substr( $order_id_str, 0, '-1' );
			$data['order_sn_str'] = substr( $order_sn_str, 0, '-1' );
			$data['order_total'] = $order_total;
			$data['goods_json'] = json_encode($json_goods);
			$data['suppliers_id'] = $suppliers_id;
			$data['suppliers_name'] = $this->getSuppliersName( $suppliers_id );
			$data['create_time'] = date('Y-m-d H:i:s', time());
			
			$insert_sql = 'INSERT INTO ' . $order_pay_table . ' (';
			$data_key_arr = array_keys( $data );
			foreach ($data_key_arr as $k) {
				$insert_sql .= '`' . $k . '`,';
			}

			$insert_sql = substr( $insert_sql, 0, -1 );
			$insert_sql .= ') VALUES (';
			foreach ($data as $v) {
				if( is_string( $v ) ){
					$insert_sql .= '\'' . $v . '\',';
				}else{
					$insert_sql .= $v . ',';
				}
			}

			$insert_sql = substr( $insert_sql, 0, -1 );
			$insert_sql .= ');';
			
			$insert_result = $GLOBALS['db']->query( $insert_sql );
			$order_pay_id = $GLOBALS['db']->insert_id();

			if( $insert_result ){
				//图片文件名字记录
				/*foreach ($file_0 as $k0=>$f0) {
					if( $file_0 ){
							
					}
				}*/




				/*$upload_data['order_pay_id'] = $order_pay_id;
				$upload_data['upload_type'] = ;
				$upload_data['order_pay_id'] = $order_pay_id;*/

				//生成状态记录
				$purchase_status_sql = 'UPDATE ' . $order_table . ' SET `purchase_pay_status` = 1 ' .
										' WHERE `order_id` IN(' . $data['order_id_str'] . ')';
				$update_ret = $GLOBALS['db']->query( $purchase_status_sql );

				if( $update_ret )
					make_json_response('', '0', '生成成功');
				else
					make_json_response('', '-1', '生成失败');
			}else{
				make_json_response('', '-1', '生成失败');
			}
		}

		/**
		 * 接口名称：应收单列表
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "orderPayList",
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *                  "status": 0,//订单状态 0 未确认
	     *                  "due_date1": "2015-01-01",//起始日期
	     *                  "due_date2": "2015-01-01"//结束日期
	     *              },
	     *              "limit": 0,//起始行号
	     *              "offset": 2//返回的行数
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "应收单查询成功",
		 *	    "content": 
		 *	    { 
		 *	    	"data":
		 *	    	[
		 *	    	{
		 *	    		"order_pay_id":1,//流水号
		 *	    		"create_time":"2016-02-23 17:21:33",//发起时间
		 *	    		"order_sn_str":"1234-1-CG,1234-2-CG,1236-1-CG",//订单号
		 *	    		"order_total":100,//发起金额
		 *	    		"pay_status":1//付款状态
		 *	    	}
		 *	    	],
		 *	    	"total":10
		 *	    }
		 *	}
		 */	
		public function orderPayListAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$order_pay_table = $GLOBALS['ecs']->table('order_pay');

			$suppliers_id = $this->getSuppliersId();

		    if( empty( $suppliers_id ) ){
		    	make_json_response('', '-1', '当前登录的必须是供应商账号');
		    }

		    $sql = 'SELECT `order_pay_id`, `order_sn_str`, `order_total`, `pay_status` FROM ' . $order_pay_table;

		    $total_sql = 'SELECT COUNT(*) AS `total` FROM ' . $order_pay_table;
		
			$where = array();	
			if( isset($params['where']) )
				$where = $params['where'];

			$where_str = '';
			
			if( isset( $where["status"] ) )
			{
				$where_status = intval( $where['status'] );
				if( $where_str )
					$where_str .= ' AND `pay_status` = ' . $where_status;
				else
					$where_str .= ' WHERE `pay_status` = ' . $where_status;

			}	

			if( isset( $where["due_date1"] ) && isset( $where["due_date2"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				$where['due_date2'] = strtotime( $where['due_date2'] );
				if( $where_str )
					$where_str .= " AND `create_time` >= '" . $where['due_date1'] . "' AND `create_time` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE `create_time` >= '" . $where['due_date1'] . "' AND `create_time` <= '" . $where['due_date2'] . "'";
				
			}
			else if( isset( $where["due_date1"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				if( $where_str )
					$where_str .= " AND `create_time` >= '" . $where['due_date1'] . "'";
				else
					$where_str .= " WHERE `create_time` >= '" . $where['due_date1'] . "'";
			}
			else if( isset( $where["due_date2"] ) )
			{
				$where['due_date2'] = strtotime( $where['due_date2'] );
				if( $where_str )
					$where_str .= " AND `create_time` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE `create_time` <= '" . $where['due_date2'] . "'";
			}

			$sql = $sql . $where_str . ' ORDER BY `create_time` DESC' .
				   ' LIMIT ' . $params['limit'].','.$params['offset'];//echo $sql;exit;
			$order_pay = $GLOBALS['db']->getAll( $sql );
			
			$total_sql = $total_sql . $where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);		

			if( $resultTotal ){
				$content = array();
				$content['data'] = $order_pay;
				$content['total'] = $resultTotal['total'];

				make_json_response($content, '0', '应收单查询成功');
			}else{
				make_json_response('', '-1', '应收单查询成功');
			}
		}

		/**
		 * 接口名称：应收单详情
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "orderPayDetail",
	     *      "parameters": {
	     *          "params": {
	     *              "order_pay_id": 2//应收单id
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "应收单详情查询成功",
		 *	    "content": 
		 *	    { 
		 *	    	"info":
		 *	    	{
		 *	    		"order_pay_id":1,//流水编号
		 *	    		"order_total":1500,//总金额
		 *	    		"order_count":3,//订单数量
		 *	    		"create_time":"2016-02-23 13:57:00",//发起时间
		 *	    	},
		 *	    	"orders"://订单信息
		 *	        [
		 *	        {
		 *	        	"order_id":1,//订单id
		 *	        	"order_sn":"os122311",//订单编号
		 *	        	"goods_name":"纹钢",//商品名称
		 *	        	"goods_sn":"s20000",//商品编号
		 *	        	"attr":"",//规格/牌号/材质
		 *	        	"goods_price":152,//单价
		 *          	"goods_number":251,//数量
		 *          	"shipping_fee":250.0//物流
		 *          	"total":250.0//小计
		 *	        }
		 *	        ],
		 *	        "file_0"://发票文件
		 *	        [
		 *	        	{
		 *	        		"upload_name":"20160224_s1.jpg",//文件名字
		 *	        		"upload_id":1,//文件id
		 *	        	}
		 *	        ],
		 *	        "file_1"://送货单文件
		 *	        [
		 *	        	{
		 *	        		"upload_name":"20160224_s1.jpg",//文件名字
		 *	        		"upload_id":1,//文件id
		 *	        	}
		 *	        ]
		 *	    }
		 *	}
		 */
		public function orderPayDetailAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$suppliers_id = $this->getSuppliersId();
			if( empty( $suppliers_id ) ){
				make_json_response('', '-1', '当前登录的必须是供应商账号');
			}

			$order_pay_id = $params['order_pay_id'];

			$order_pay_table = $GLOBALS['ecs']->table('order_pay');//生成单表
			$upload_table = $GLOBALS['ecs']->table('order_pay_upload');//生成单文件表

			$sql = 'SELECT * FROM ' . $order_pay_table . ' WHERE `order_pay_id` = ' . $order_pay_id;
			$order_pay = $GLOBALS['db']->getRow( $sql );

			if( $order_pay ){
				$content = array();

				$orders = json_decode( $order_pay['goods_json'] ,true );
				
				if( empty( $orders ) ){
					$orders = array();
				}

				$content['orders'] = array_values( $orders );
				$content['info'] = array();
				$content['info']['order_pay_id'] = $order_pay['order_pay_id'];
				
				$content['info']['order_total'] = $order_pay['order_total'];
				$content['info']['order_count'] = count( $content['orders'] );
				$content['info']['create_time'] = $order_pay['create_time'];
			
				//已上传文件
				$upload_sql = 'SELECT `upload_name`,`upload_type`, `upload_id` FROM ' . $upload_table;
				$files = $GLOBALS['db']->getAll( $upload_sql );

				$file_0 = $file_1 = array();

				foreach ($files as $f) {
					if( $f['upload_type'] == 0 )
						$file_0[] = array('upload_id' => $f['upload_id'], 'upload_name' => $f['upload_name']);
					else
						$file_1[] = array('upload_id' => $f['upload_id'], 'upload_name' => $f['upload_name']);
				}

				$content['file_0'] = $file_0;
				$content['file_1'] = $file_1;

				make_json_response($content, '0', '生成单查询成功');
			}else{
				make_json_response('', '-1', '生成单查询失败');
			}
		}

		/**
		 * 接口名称：订单详情
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "orderDetail",
	     *      "parameters": {
	     *          "params": {
	     *              "order_id": 2//订单id
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "订单详情查询成功",
		 *	    "content": 
		 *	    { 
		 *	    	"info"://订单信息
		 *	        {
		 *	        	"order_id":1,//订单id
		 *	        	"order_status":"",//订单状态
		 *	        	"order_sn":"os122311",//订单编号
		 *	        	"goods_name":"纹钢",//商品名称
		 *	        	"add_time":"2015-01-01:11",//订单时间
		 *	        	"goods_sn":"s20000",//商品编号
		 *	        	"attr":"",//规格/牌号/材质
		 *	        	"goods_price":152,//单价
		 *          	"goods_number":251,//数量
		 *          	"shipping_fee":250.0,//物流
		 *	        	"consignee":"aa",//收货人
		 *	        	"address":"xx地址",//收货地址
		 *	        	"receiver":"中交一局",//收货单位
		 *	        	"mobile":"13011111111",//手机号码
		 *	        	"best_time":""//希望到货时间
		 *	        },
		 *	        "shipping"://物流
		 *	        {
		 *	        	"company_name":"EMS",//物流公司
		 *	        	"shipping_num":"xxx",//物流单号
		 *	        	"tel":"1111111",//联系电话
		 *	        	"shipping_time":"2017-01-01",//发货时间
		 *	        	"log"://动态列表
		 *	        	[
		 *	        	{
		 *	        		"date":"2017/01/02",//日期
		 *	        		"content":"物流记录"//物流记录
		 *	        	}
		 *	        	]
		 *	        },
		 *	        "buttons"://平台可操作按钮
		 *	        [
		 *	        	"发货验签"
		 *	        ]
		 *      }
		 *	}
		 */
		public function orderDetailAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}
			$order_id = intval( $params['order_id'] );

			$order_info_table = $GLOBALS['ecs']->table('order_info');
			$user_table = $GLOBALS['ecs']->table('users');

			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//规格/型号/材质

			$sql = 'SELECT odr.`order_id`, odr.`child_order_status`, odr.`order_sn`, odr.`add_time`, ' .
				   ' odr.`shipping_fee_send_saler`, odr.`shipping_fee_arr_saler`, odr.`best_time`, odr.`suppers_id`,' .
				   ' og.`goods_sn`, og.`goods_name`, og.`goods_number_arr_saler`, og.`goods_number_send_saler`, og.`goods_price_send_saler`,og.`goods_price_arr_saler`,' .
				   ' og.`goods_id`, ' .
				   ' odr.`consignee`, odr.`address`, odr.`mobile`, odr.`sign_building`, odr.`shipping_info`, odr.`shipping_log`, ' .
				   ' u.`companyName` AS receiver ' .
				   'FROM ' . $order_info_table . ' AS odr LEFT JOIN ' .
				   $order_goods_table . ' AS og ON og.`order_id` = odr.`order_id` LEFT JOIN ' .
				   $user_table . ' AS u ON odr.`user_id` = u.`user_id` WHERE odr.`order_id` = ' . $order_id;

			$order_info = $GLOBALS['db']->getRow( $sql );
			if( empty( $order_info ) ){
				make_json_response('', '-1', '订单查询失败');
			}else{
				$this->orderPrivilege( $order_info['suppers_id'], $order_info['child_order_status'] );
				//规格、型号、材质
				$goods_attr_sql = 'SELECT `attr_value` FROM ' . $goods_attr_table .' WHERE `goods_id` = ' . $order_info['goods_id'];
				$goods_attr = $GLOBALS['db']->getAll( $goods_attr_sql );
				if( empty( $goods_attr ) ){
					$order_info['attr'] = '';
				}else{
					$attr_arr = array();
					foreach ($goods_attr as $value) {
						$attr_arr[] = $value['attr_value'];
					}
					$order_info['attr'] = implode('/', $attr_arr);
				}
				
				$order_info['add_time'] = date('Y-m-d H:i:s', $order_info['add_time']);
				//物流费
				if( $order_info['child_order_status'] <= SOS_SEND_PC2){
					$order_info['shipping_fee'] = $order_info['shipping_fee_send_saler'];//发货
					$order_info['goods_number'] = $order_info['goods_number_send_saler'];//发货
					$order_info['goods_price'] = $order_info['goods_price_send_saler'];//发货
				}else{
					$order_info['shipping_fee'] = $order_info['shipping_fee_arr_saler'];//到货
					$order_info['goods_number'] = $order_info['goods_number_arr_saler'];//到货
					$order_info['goods_price'] = $order_info['goods_price_arr_saler'];//到货
				}
				
				//物流
				$shipping = empty( $order_info['shipping_info'] ) ? array() : json_decode( $order_info['shipping_info'], true);
				$shipping['log'] = empty( $order_info['shipping_log'] ) ? array() : json_decode( $order_info['shipping_log'], true);

				unset( $order_info['shipping_info'] );
				unset( $order_info['shipping_log'] );

				$childer_map = C('purchase_to_childer_map');
				$purchase_status = C('purchase_status');
					
				$order_info['order_status'] = '';
				//订单状态转换
				foreach ($childer_map as $pk => $pv) {
					foreach ($pv as $s) {
						if( $s == $order_info['child_order_status'] ){

							$order_info['order_status'] = $purchase_status[ $pk ];
							break 2;
						}
					}
				}

				

				if( !empty( $order_info['best_time'] ) ){
					$order_info['best_time'] = date('Y-m-d H:i:s', $order_info['best_time']);
				}else{
					$order_info['best_time'] = '';
				}

				$buttons = array();
				if( $order_info['child_order_status'] == SOS_SEND_PP ){
					$buttons = array('发货验签');
				}else if( $order_info['child_order_status'] == SOS_ARR_PC ){
					$buttons = array('到货验签');
				}
				unset( $order_info['child_order_status'] );
			}

			$content = array();
			$content['shipping'] = $shipping;
			$content['info'] = $order_info;
			$content['buttons'] = $buttons;

			make_json_response($content, '0', '订单查询成功');
		}
				
		
		/**
		 * 接口名称: 订单详情- 操作按钮对应的接口("发货验签" "到货验签")
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "updateChilderStatus",
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":101//订单ID
	     *           	"button":""//按钮名称（可能的名称有: 发货验签 到货验签 ）
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "确认成功",
		 *	    "content": {}
		 *  }
		 * @return [type] [description]
		 */
		public function updateChilderStatusAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}
			$order_id = intval( $params['order_id'] );

			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$order_info_table = $GLOBALS['ecs']->table('order_info');

			//检查订单状态
			$order_info_sql = 'SELECT `child_order_status`, `suppers_id` FROM ' . $order_info_table .
							  ' WHERE `order_id` = ' . $order_id;
			$order_status = $GLOBALS['db']->getRow( $order_info_sql );
			if( !$order_status ){
				make_json_response('', '-1', '订单不存在');
			}

	    	$this->orderPrivilege( $order_status['suppers_id'], $order_status['child_order_status'] );	

			//子订单状态更改
			$childer_order_update_sql = 'UPDATE ' . $order_info_table . ' SET `child_order_status` = %d' . ' WHERE `order_id` = ' . $order_id;

			$buttons = trim($params['button']);
			switch ( $buttons ) {
				case '发货验签':
					if( $order_status['child_order_status'] == SOS_SEND_PP ){
						$childer_order_update_sql = sprintf($childer_order_update_sql, SOS_SEND_SC);

						$childer_order_update = $GLOBALS['db']->query( $childer_order_update_sql );

						if( $childer_order_update )
							make_json_response('', '0', '发货验签 成功');
						else
							make_json_response('', '-1', '发货验签 失败');
					}					
					break;
				
				case '到货验签':
					if( $order_status['child_order_status'] == SOS_ARR_PC ){
						$childer_order_update_sql = sprintf($childer_order_update_sql, SOS_ARR_SC);

						$childer_order_update = $GLOBALS['db']->query( $childer_order_update_sql );

						if( $childer_order_update )
							make_json_response('', '0', '到货验签 成功');
						else
							make_json_response('', '-1', '到货验签 失败');
					}					
					break;
				
				default:
					
					break;
			}

			// 状态提示
			$msg = '订单当前状态是 %s, 无法执行该操作';
			switch ( $order_status['child_order_status'] ) {
				case SOS_UNCONFIRMED:
					$msg = sprintf($msg, '未确认');
					break;
				case SOS_CONFIRMED:
					$msg = sprintf($msg, '已确认');
					break;
				case SOS_SEND_CC:
					$msg = sprintf($msg, '客户已验签(发货)');
					break;
				case SOS_SEND_PC:
					$msg = sprintf($msg, '平台已验签(发货)');
					break;
				case SOS_SEND_PP:
					$msg = sprintf($msg, '平台已推单(发货)');
					break;	
				case SOS_SEND_SC:
					$msg = sprintf($msg, '供应商已验签(发货)');
					break;
				case SOS_SEND_PC2:
					$msg = sprintf($msg, '平台已验签(发货)');
					break;

				case SOS_ARR_CC:
					$msg = sprintf($msg, '客户已验签(到货)');
					break;
				case SOS_ARR_PC:
					$msg = sprintf($msg, '平台已验签(到货)');
					break;		
				case SOS_ARR_SC:
					$msg = sprintf($msg, '供应商已验签(到货)');
					break;
				case SOS_ARR_PC2:
					$msg = sprintf($msg, '平台已验签(到货)');
					break;		
				case SOS_CANCEL:
					$msg = sprintf($msg, '订单已撤销');
					break;	
				default:
					$msg = sprintf($msg, '未知');
					break;
			}
			make_json_response('', '-1', $msg);
		}

		/**
		 * 接口名称:自订单详情-添加物流
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "addShippingInfo",
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":101//订单ID
	     *          	"company_name":"EMS",//物流公司
	     *          	"shipping_num":"e87694202100",//物流编号
	     *          	"tel":"021-62420011",//联系电话
	     *          	"shipping_time":"2017-01-01"//发货时间
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "物流添加成功",
		 *	    "content": {}
		 *	 }
		 */
		public function addShippingInfoAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}
			$order_id = intval( $params['order_id'] );

			$company_name = strval( trim( $params['company_name'] ) );
			$shipping_num = strval( trim( $params['shipping_num'] ) );
			$tel = strval( trim( $params['tel'] ) );

			$shipping_time = strval( trim( $params['shipping_time'] ) );

			$order_info_table = $GLOBALS['ecs']->table('order_info');
			
			$order_info_sql = 'SELECT `suppers_id`, `child_order_status` FROM ' . $order_info_table . ' WHERE `order_id` = ' . $order_id;
			$order_info = $GLOBALS['db']->getRow( $order_info_sql );

			$this->orderPrivilege( $order_info['suppers_id'], $order_info['child_order_status'] );

			$shipping_info['company_name'] = urlencode( $company_name );
			$shipping_info['shipping_num'] = $shipping_num;
			$shipping_info['tel'] = $tel;
			$shipping_info['shipping_time'] = $shipping_time;

			$shippinf_info_str = json_encode( $shipping_info );

			$add_shipping_info_sql = 'UPDATE ' . $order_info_table . ' SET `shipping_info` = \'' . $shippinf_info_str .
									 '\' WHERE `order_id` = ' . $order_id; 
			$add_shipping = $GLOBALS['db']->query( $add_shipping_info_sql );

			if( $add_shipping ){
				make_json_response('', '0', '物流添加成功');
			} else {
				make_json_response('', '-1', '物流添加失败');
			}

		}

		/**
		 * 接口名称:自订单详情-添加物流信息(物流动态)
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "addShippingLog",
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":101,//订单ID
	     *          	"log":"物流记录"//物流记录
	     *          	"date":"2017-02-06"//内容日期(不填则默认为当前时间)
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "物流信息动态添加成功",
		 *	    "content": {}
		 *	 }
		 */
		public function addShippingLogAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}
			$order_id = intval( $params['order_id'] );

			$order_info_table = $GLOBALS['ecs']->table('order_info');

			$log = strval( trim( $params['log'] ) );
			@$shipping_date = strval( trim( $params['date'] ) );
			if( empty( $shipping_date ) ){
				$shipping_date = date('Y-m-d H:i:s');
			}

			$order_info_sql = 'SELECT `suppers_id`,`shipping_log`, `child_order_status` FROM ' . $order_info_table . ' WHERE `order_id` = ' . $order_id;
			$order_info = $GLOBALS['db']->getRow( $order_info_sql );

			if( empty( $order_info ) ){
				make_json_response('', '-1', '订单不存在');
			}

			$this->orderPrivilege( $order_info['suppers_id'], $order_info['child_order_status'] );

			$shipping_log_old = array();
			$shipping_log_old['shipping_log'] = $order_info['shipping_log'];

			if( empty( $shipping_log_old['shipping_log'] ) ){
				$shipping_log = array();
			}else{
				$shipping_log = json_decode( $shipping_log_old['shipping_log'] );
			}
			$shipping_log_temp['content'] = urlencode( $log );
			$shipping_log_temp['date'] = $shipping_date;
			array_push( $shipping_log, $shipping_log_temp );
			
			$shipping_log_str = json_encode( $shipping_log );

			$add_shipping_log_sql = 'UPDATE ' . $order_info_table . ' SET `shipping_log` = \'' . $shipping_log_str .
									 '\' WHERE `order_id` = ' . $order_id; 
			$add_shipping_log = $GLOBALS['db']->query( $add_shipping_log_sql );

			if( $add_shipping_log ){
				make_json_response('', '0', '物流信息动态添加成功');
			} else {
				make_json_response('', '-1', '物流信息动态添加失败');
			}			
		}
		
		/**
		 * 接口名称: 物流费用设置
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "addCategoryShippingFee",
	     *      "parameters": {
	     *          "params": {
	     *          	"cat_id":101,//物料类别的id, category表的cat_id
	     *          	"shipping_fee":"100元/吨/公里",//物流费用
	     *          	"desc":""//说明
	     *          	
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "物流信息动态添加成功",
		 *	    "content": {}
		 *	 }
		 */	 
		public function addCategoryShippingFeeAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$shipping_table = $GLOBALS['ecs']->table('shipping_price');


			$suppliers_id = $this->getSuppliersId();

		    if( empty( $suppliers_id ) ){
		    	make_json_response('', '-1', '管理员账号id有误');
		    }

			$cat_id = intval( $params['cat_id'] );
			$shipping_fee = trim( strval( $params['shipping_fee'] ) );
			$desc = trim( strval( $params['desc'] ) );

			$shipping_sql = 'INSERT INTO ' . $shipping_table . ' (';

			$shipping['goods_category_id'] = $cat_id;
			$shipping['suppliers_id'] = $suppliers_id;
			$shipping['shipping_fee'] = $shipping_fee;
			$shipping['desc'] = $desc;

			foreach ($shipping as $k => $v) {
				$shipping_sql .= '`' . $k . '`,';
			}

			if( $cat_id ){//单个类别

				//检查是否已存在
				$check_sql = 'SELECT `shipping_fee_id` FROM ' . $shipping_table . ' WHERE `suppliers_id` = ' . $suppliers_id . ' AND `goods_category_id` = ' . $cat_id;
				$check = $GLOBALS['db']->getRow( $check_sql );
				if( $check ){//update
					$shipping_sql = 'UPDATE ' . $shipping_table . ' SET `shipping_fee` = \'' . $shipping_fee . '\', `desc` = \'' . $desc .
									'\' WHERE `shipping_fee_id` = ' . $check['shipping_fee_id'] ;
				}else{//insert

					$shipping_sql = substr($shipping_sql, 0, -1) . ") VALUES(";//values 一条

					foreach ($shipping as $v) {
						if ( is_string( $v ) ) {
							$v = '\'' . $v . '\'';
						}
						
						$shipping_sql .= $v . ',';
					}

					$shipping_sql = substr($shipping_sql, 0, -1) . ")";
				}
				
			}else{//所有类别

				$category_table = $GLOBALS['ecs']->table('category');
				$cat_id_sql = 'SELECT `cat_id` FROM ' . $category_table;
				$cat_id_arr = $GLOBALS['db']->getAll( $cat_id_sql );

				$cat_arr = array();
				foreach ($cat_id_arr as $v) {
					$cat_arr[] = $v['cat_id'];
				}

				$cat_str = implode(',', $cat_arr);
				//检查是否已存在
				$check_sql = 'SELECT `shipping_fee_id`,`goods_category_id` FROM ' . $shipping_table . ' WHERE `suppliers_id` = ' . $suppliers_id . ' AND `goods_category_id` IN(' . $cat_str . ')';
				$check = $GLOBALS['db']->getAll( $check_sql );
				
				if( !empty( $check ) ){//update
					$ids = array();
					$cat_old = array();
					foreach ($check as $v) {
						$ids[] = $v['shipping_fee_id'];
						$cat_old[] = $v['goods_category_id'];
					}

					$ids_str = implode(',', $ids);
					$update_sql = 'UPDATE ' . $shipping_table . ' SET `shipping_fee` = \'' . $shipping_fee . '\', `desc` = \'' . $desc .
								    '\' WHERE `shipping_fee_id` IN(' . $ids_str . ')';
					$update_shipping = $GLOBALS['db']->query( $update_sql );

					if( empty( $update_shipping )){
						make_json_response('', '-1', '设置物流费用失败');
					}

					$cat_arr = array_diff( $cat_arr, $cat_old );

				}

				if( !empty( $cat_arr ) ){//insert
					$shipping_sql = substr($shipping_sql, 0, -1) . ") VALUES";//values 多条

					foreach ($cat_arr as $v) {
						$shipping_sql .= '(';
						$shipping = array();

						$shipping['goods_category_id'] = $v['cat_id'];
						$shipping['suppliers_id'] = $suppliers_id;
						$shipping['shipping_fee'] = $shipping_fee;
						$shipping['desc'] = $desc;

						foreach ($shipping as $v) {
							if ( is_string( $v ) ) {
								$v = '\'' . $v . '\'';
							}
							
							$shipping_sql .= $v . ',';
						}

						$shipping_sql = substr($shipping_sql, 0, -1) . "),";
					}
					$shipping_sql = substr($shipping_sql, 0, -1);	
				}else{
					$shipping_sql = '';
					$add_shipping = true;
				}
				
			}

			if( $shipping_sql ){
				$add_shipping = $GLOBALS['db']->query( $shipping_sql );
			}

			if( $add_shipping ){
				make_json_response('', '0', '添加物流成功');
			}else{
				make_json_response('', '-1', '添加物流失败');
			}
		}

		/**
		 * 接口名称: 初始化物流费用设置
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "initcategoryShipping",
	     *      "parameters": {}
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "初始化物流成功",
		 *	    "content": 
		 *	    {
		 *	    	"cat":
		 *	    	[
		 *	    	{
		 *	    		"cat_id":1,//物料类别对应的ID
		 *	    		"cat_name":"钢材、钢绞线系列"//物料类别对应的名字
		 *	    	}
		 *	    	],
		 *	    	"data":
		 *	    	[	
		 *	    	{
		 *	    		"cat_name":"螺纹",//物料类别
		 *	    		"shipping_fee_id":1,//物料费用记录的ID
		 *	    		"shipping_fee":"100元/吨/公里",
		 *	    		"desc":"10000"//说明文字
		 *	    	}
		 *	    	],
		 *	    	"total":10
		 *	    }
		 *	 }
		 */	 
		public function initcategoryShippingAction()
		{
			$suppliers_id = $this->getSuppliersId();
			
			if( empty( $suppliers_id ) || $_SESSION['action_list'] != 'all' ){
		    	make_json_response('', '-1', '管理员账号id有误');
		    }
		    
		    $category_table = $GLOBALS['ecs']->table('category');
			$cat_sql = 'SELECT `cat_id`,`cat_name` FROM ' . $category_table;
			$cat_arr = $GLOBALS['db']->getAll( $cat_sql );

			$shipping_table = $GLOBALS['ecs']->table('shipping_price');

			$shipping_sql = 'SELECT sh.`shipping_fee_id`, sh.`shipping_fee`, sh.`desc`, c.`cat_name` FROM ' .
							$shipping_table . ' AS sh ' .
							'LEFT JOIN ' . $category_table . ' AS c ON c.`cat_id` = sh.`goods_category_id` WHERE sh.`suppliers_id` = ' .
							$suppliers_id;

			$shipping_total_sql = 'SELECT COUNT(*) AS `total` FROM ' . $shipping_table . ' WHERE `suppliers_id` = ' . $suppliers_id;
											
			$shipping_fee = $GLOBALS['db']->getAll( $shipping_sql );
			$shipping_total = $GLOBALS['db']->getRow( $shipping_total_sql );

			$content = array();
			$content['cat'] = empty( $cat_arr ) ? array() : $cat_arr;
			$content['data'] = empty( $shipping_fee ) ? array() : $shipping_fee;
			$content['total'] = $shipping_total['total'];
			make_json_response($content, '0', '初始化物流费用设置成功');
		}

		/**
		 * 接口名称：移除物料类别的物流费
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params)：
	     *  {
	     *      "entity": "order_info",
	     *      "command": "removeCategoryShipping",
	     *      "parameters": {
	     *      	"params":{
	     *      		"shipping_fee_id":10//`shipping_price` 的自增ID
	     *      	}
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "移除物流费用成功",
		 *	    "content": {}
		 *	 }
		 */	 
		public function removeCategoryShippingAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			$shipping_fee_id = $params['shipping_fee_id'];
			$suppliers_id = $this->getSuppliersId();

			if( empty( $suppliers_id ) ){
		    	make_json_response('', '-1', '管理员账号id有误');
		    }

			$shipping_table = $GLOBALS['ecs']->table('shipping_price');

			$remove_sql = 'DELETE FROM ' . $shipping_table . ' WHERE `shipping_fee_id` = ' . $shipping_fee_id . ' AND `suppliers_id` = ' .
						  $suppliers_id;
			$remove_shipping = $GLOBALS['db']->query( $remove_sql );

			if( !empty( $remove_shipping ) ){
				make_json_response('', '0', '移除物流费用成功');
			}else{
				make_json_response('', '-1', '移除物流费用失败');
			}
		}


		/**
		 * 接口名称：获取指定物料类别的物流费
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方式：POST
		 * 接口参数：
		 *  {
	     *      "entity": "shipping_price",
	     *      "command": "categoryShippingDetail",
	     *      "parameters": {
	     *      	"params":{
	     *      		"shipping_fee_id":10//`shipping_price` 的自增ID
	     *      	}
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "物流费用查询成功",
		 *	    "content": {
		 *	    	"shipping_fee_id":1,//id
		 *	     	"goods_category_id":1,//物料id
		 *	     	"goods_category":"圆钢",//物料类别
		 *	     	"shipping_fee":"300元/吨/公里",//物流费用
		 *	     	"desc":"23123"//说明
		 *	     	
		 *	    }
		 *	 }
		 *	 
		 * 
		 */
		public function categoryShippingDetailAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];

			$suppliers_id = $this->getSuppliersId();

			if( empty( $suppliers_id ) ){
		    	make_json_response('', '-1', '管理员账号id有误');
		    }

			$shipping_price_table = $GLOBALS['ecs']->table('shipping_price');
			$category_table = $GLOBALS['ecs']->table('category');

			$shipping_price_sql = 'SELECT s.*, c.`cat_name` FROM ' . $shipping_price_table .
								  ' AS s LEFT JOIN ' . $category_table . ' AS c ON c.`cat_id` = s.`goods_category_id` ' .
								  ' WHERE s.`shipping_fee_id` = ' . $params['shipping_fee_id'];

			$shipping_price = $GLOBALS['db']->getRow( $shipping_price_sql );
			if( !empty( $shipping_price ) ){
				make_json_response($shipping_price, '0', '物流费用查询成功');
			}else{
				make_json_response('', '-1', '物流费用查询失败');
			}
		}

		/**
		 * 接口名称：保存指定物料类别的物流费
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 接口地址：
		 * 请求方式：POST
		 * 接口参数：
		 *  {
	     *      "entity": "shipping_price",
	     *      "command": "saveCategorShipping",
	     *      "parameters": {
	     *      	"params":{
	     *      		"shipping_fee_id":10,//`shipping_price` 的自增ID
	     *      		"goods_category_id":1,//物料id
		 *	     	    "shipping_fee":"300元/吨/公里",//物流费用
		 *	     	    "desc":"23123"//说明
	     *      	}
	     *      }
	     *  }
	     *  
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "物流费用编辑成功",
		 *	    "content": {}
		 *	 }
		 *	 
		 * 
		 */
		public function saveCategorShippingAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];
				
			$suppliers_id = $this->getSuppliersId();

			if( empty( $suppliers_id ) ){
		    	make_json_response('', '-1', '管理员账号id有误');
		    }

		    if( empty( $params['shipping_fee'] ) ){
		    	$params['shipping_fee'] = 0;
		    }
		    if( empty( $params['desc'] )){
		    	$params['desc'] = '';
		    }
		    if( empty( $params['goods_category_id'] )){
		    	make_json_response('', '-1', '没有选择物料类别');
		    }

		    $shipping_price_table = $GLOBALS['ecs']->table('shipping_price');
		    
		    $old_sql = 'SELECT `shipping_fee_id` FROM ' . $shipping_price_table . ' WHERE `suppliers_id` = ' .
		    		   $suppliers_id . ' AND `goods_category_id` = ' . $params['goods_category_id'];
		    $old_id = $GLOBALS['db']->getOne( $old_sql );
		    if( $old_id ){
		    	if( $old_id != $params['shipping_fee_id'] ){
		    		$clean_sql = 'DELETE FROM ' . $shipping_price_table . ' WHERE `shipping_fee_id` = ' . $old_id . ' LIMIT 1';
		    		$clean = $GLOBALS['db']->query( $clean_sql );
		    		if( !$clean ){
		    			make_json_response('', '-1', '更新物流费用失败');
		    		}
		    	}
		    }

		    $shipping_price_sql = 'UPDATE ' . $shipping_price_table . ' SET `shipping_fee`  = \'' . trim( $params['shipping_fee'] ) .
		    					  '\', `desc` = \'' . trim( $params['desc'] ) .
		    					  '\' WHERE `shipping_fee_id` = ' . intval( $params['shipping_fee_id'] );
		    $shipping_price = $GLOBALS['db']->query( $shipping_price_sql );
		    if( $shipping_price ){
		    	make_json_response('', '0', '物流费用编辑成功');
		    }else{
		    	make_json_response('', '-1', '物流费用编辑失败');
		    }
		}

		/**
		 * 接口名称：上传文件
		 * 接口地址：http://admin.zj.dev/admin/SupplierModel.php
		 * 请求方式：POST
		 * 接口参数：
		 * {
         *      "command" : "upload", 
         *      "entity"  : "file_0", //file_0 发票 file_1物流
         *      "parameters" : {} 
         * }
	     *  
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "物流费用编辑成功",
		 *	    "content": {}
		 *	 }
		 */
		public function uploadAction()
		{
			$entity = $this->content['entity'];

			$suppliers_id = $this->getSuppliersId();

		    if( empty( $suppliers_id ) ){
		    	make_json_response('', '-1', '当前登录的必须是供应商账号');
		    }

			require('../includes/cls_image.php');
	        if (empty($_FILES))
	        	make_json_response('', '-1', '上传失败');
	        $file = pathinfo($_FILES[$entity]['name']);
	        // if ($file['extension'] != 'pdf') {
	        //     failed_json('只允许上传pdf格式的文件！');
	        // }
	        
	        
	        //upload
	        $upload = new cls_image();
	        $fileName = date('YmdHis') . '_s' . $suppliers_id . '.' . $file['extension'];
	        $res = $upload->upload_image($_FILES[$entity], PURCHASE_ORDER_DIR, $fileName);
	        if ($res === false) {
	        	make_json_response('', '-1', '保存文件失败');
	        } else {
	            make_json_response($fileName, '0', '保存文件成功');
	        }

		}

	}
	$content = jsonAction( 
				array( 'orderPage', 'orderDetail', 'updateChilderStatus', 'addShippingLog', 'addShippingInfo', 'initcategoryShipping',
						'addCategoryShippingFee', 'removeCategoryShipping', 'saveCategorShipping', 'categoryShippingDetail','createOrderPay',
						'completeList', 'orderPayDetail', 'orderPayList', 'initOrderPay', 'upload'
			 	) 
			);
	$supplierModel = new SupplierModel($content);
	$supplierModel->run();