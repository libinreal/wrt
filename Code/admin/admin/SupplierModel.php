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
			}elseif($this->command == 'updateChilderStatus'){
				//
				$this->updateChilderStatusAction();
			}elseif($this->command == 'addShippingInfo'){
				//
				$this->addShippingInfoAction();
			}elseif($this->command == 'addShippingLog'){
				//
				$this->addShippingLogAction();			
			}
		}
				
		public function findAction(){
			
		}

		/**
		 * @param suppers_id int 订单关联的供应商id
		 * @return [type] [description]
		 */
		private function orderPrivilege( $suppers_id = '' )
		{
			//验证是否有权限
			$admin_sql = "SELECT `user_id`, `user_name`, `suppliers_id` ".
	           "FROM " .$GLOBALS['ecs']->table('admin_user'). " WHERE `user_id` = '".$_SESSION['admin_id']."'";
	    	$admin_user = $GLOBALS['db']->getRow($admin_sql);

	    	if( empty( $admin_user['suppliers_id'] ) || $admin_user['suppliers_id'] != $suppers_id ){
	    		make_json_response('', '-1', '权限不足，无法执行该操作');
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
		 *	    "message": "票据列表查询成功",
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

			$sql = "SELECT `user_id`, `user_name`, `suppliers_id` ".
		           "FROM " .$GLOBALS['ecs']->table('admin_user'). " WHERE `user_id` = '".$_SESSION['admin_id']."'";
		    $admin_user = $GLOBALS['db']->getRow($sql);

		    if( empty( $admin_user['suppliers_id'] ) ){
		    	make_json_response('', '-1', '管理员账号id有误');
		    }

		    $suppliers_id = $admin_user['suppliers_id'];

			$sql = 'SELECT odr.`order_id` , odr.`order_sn`, og.`goods_id`, og.`goods_name`, og.`goods_sn`, odr.`add_time`, og.`goods_number`, og.`goods_price`,' .
				   ' odr.`shipping_fee_send_saler`,odr.`shipping_fee_arr_saler`, odr.`child_order_status` ' .
				   ' FROM ' . $order_table .
				   ' AS odr LEFT JOIN ' . $order_goods_table . ' AS og ON odr.`order_id` = og.`order_id`' .
				   ' WHERE odr.`suppers_id` = ' . $suppliers_id;

			$total_sql = 'SELECT COUNT(*) AS `total`' .
				   		 ' FROM ' . $order_table .
				   		 ' AS odr LEFT JOIN ' . $order_goods_table . ' AS og ON odr.`order_id` = og.`order_id`' .
				   		 ' WHERE odr.`suppers_id` = ' . $suppliers_id;	
		
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
				$childer_map = C('purchase_to_childer_map');
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
					}else{
						$v['shipping_fee'] = $v['shipping_fee_arr_saler'];//到货
					}

					$v['order_status'] = '';
					//订单状态转换
					foreach ($childer_map as $pk => $pv) {
						foreach ($pv as $s) {
							if( $s == $v['child_order_status'] ){
								$v['order_status'] = $purchase_status[ $pk ];
								break 2;
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
				   ' og.`goods_sn`, og.`goods_name`, og.`goods_price`, og.`goods_number`, og.`goods_id`, ' .
				   ' odr.`consignee`, odr.`address`, odr.`mobile`, odr.`sign_building`, odr.`shipping_info`, odr.`shipping_log`, ' .
				   ' u.`companyName` AS receiver ' .
				   'FROM ' . $order_info_table . ' AS odr LEFT JOIN ' .
				   $order_goods_table . ' AS og ON og.`order_id` = odr.`order_id` LEFT JOIN ' .
				   $user_table . ' AS u ON odr.`user_id` = u.`user_id` WHERE odr.`order_id` = ' . $order_id;

			$order_info = $GLOBALS['db']->getRow( $sql );
			if( empty( $order_info ) ){
				make_json_response('', '-1', '订单查询失败');
			}else{
				$this->orderPrivilege( $order_info['suppers_id'] );
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
				}else{
					$order_info['shipping_fee'] = $order_info['shipping_fee_arr_saler'];//到货
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

				unset( $order_info['child_order_status'] );

				if( !empty( $order_info['best_time'] ) ){
					$order_info['best_time'] = date('Y-m-d H:i:s', $order_info['best_time']);
				}else{
					$order_info['best_time'] = '';
				}

				$buttons = array();
				if( $order_info['child_order_status'] == SOS_SEND_PC ){
					$buttons = array('发货验签');
				}else if( $order_info['child_order_status'] == SOS_ARR_PC ){
					$buttons = array('到货验签');
				}

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

	    	$this->orderPrivilege( $order_status['suppers_id'] );	

			//子订单状态更改
			$childer_order_update_sql = 'UPDATE ' . $order_info_table . ' SET `child_order_status` = %d' . ' WHERE `order_id` = ' . $order_id;

			$buttons = trim($params['button']);
			switch ( $buttons ) {
				case '发货验签':
					if( $order_status['child_order_status'] == SOS_SEND_PC ){
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
			
			$order_info_sql = 'SELECT `suppers_id` FROM ' . $order_info_table . ' WHERE `order_id` = ' . $order_id;
			$order_info = $GLOBALS['db']->getRow( $order_info_sql );

			$this->orderPrivilege( $order_info['suppers_id'] );
			
			$shipping_info['company_name'] = $company_name;
			$shipping_info['shipping_num'] = $shipping_num;
			$shipping_info['tel'] = $tel;
			$shipping_info['shipping_time'] = $shipping_time;

			$shippinf_info_str = json_encode( $shipping_info, JSON_UNESCAPED_UNICODE );

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

			$order_info_sql = 'SELECT `suppers_id`,`shipping_log` FROM ' . $order_info_table . ' WHERE `order_id` = ' . $order_id;
			$order_info = $GLOBALS['db']->getRow( $order_info_sql );

			if( empty( $order_info ) ){
				make_json_response('', '-1', '订单不存在');
			}

			$this->orderPrivilege( $order_info['suppers_id'] );

			$shipping_log_old = array();
			$shipping_log_old['shipping_log'] = $order_info['shipping_log'];

			if( empty( $shipping_log_old['shipping_log'] ) ){
				$shipping_log = array();
			}else{
				$shipping_log = json_decode( $shipping_log_old['shipping_log'] );
			}
			$shipping_log_temp['content'] = $log;
			$shipping_log_temp['date'] = $shipping_date;
			array_push( $shipping_log, $shipping_log_temp );
			$shipping_log_str = json_encode( $shipping_log, JSON_UNESCAPED_UNICODE );

			$add_shipping_log_sql = 'UPDATE ' . $order_info_table . ' SET `shipping_log` = \'' . $shipping_log_str .
									 '\' WHERE `order_id` = ' . $order_id; 
			$add_shipping_log = $GLOBALS['db']->query( $add_shipping_log_sql );

			if( $add_shipping_log ){
				make_json_response('', '0', '物流信息动态添加成功');
			} else {
				make_json_response('', '-1', '物流信息动态添加失败');
			}			
		}
		
	}
	$content = jsonAction( array( "orderPage", "orderDetail", "updateChilderStatus", 'addShippingLog', 'addShippingInfo' ) );
	$supplierModel = new SupplierModel($content);
	$supplierModel->run();