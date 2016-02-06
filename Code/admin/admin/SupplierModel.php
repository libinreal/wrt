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
			}elseif ($this->command == 'create'){
				//生成支付单信息
				$this->createOrderPayAction();
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

			$suppliers_id = $this->getSuppliersId();

		    if( empty( $suppliers_id ) ){
		    	make_json_response('', '-1', '当前登录的必须是供应商账号');
		    }

			$sql = 'SELECT odr.`order_id` , odr.`order_sn`, og.`goods_id`, og.`goods_name`, og.`goods_sn`, odr.`add_time`, og.`goods_number`, og.`goods_price`,' .
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
		 * 生成应付单/应收单数据。根据订单ID查询出来基础数据，然后把基础数据以json格式存入到 OrderPay 表
		 *  接口地址：http://admin.zjgit.dev/admin/SupplierModel.php
		 *  传入参数格式(主键id在parameters下级)：
		 *  {
		 *	    "command": "insert",
		 *	    "entity": "OrderPay",
		 *	    "parameters": {
		 *	        "order_id":"1,2,3"
		 *	    }
		 *	}
		 */
		public function createOrderPayAction(){
// 			print_r($_SESSION);die;
			$content = $this->content;
			$params = $content['parameters']['order_id'];
			
			if (!empty($params)) {
				$order_ids = explode(',',$params);
			}
// 			print_r($order_ids);die;
			$order_total = 0;
			foreach ($order_ids as $value){
				$order_info = $this->getOrderBaseInfo($value);
				$order_goods['goods_sn'] = $order_info['goods_sn'];//物料编码
				$order_goods['goods_name'] = $order_info['goods_name'];//物料名称
				$order_goods['goods_number'] = $order_info['goods_number'];//数量
				$order_goods['goods_price'] = $order_info['goods_price']; //单价
				$order_goods['shipping_fee_arr_saler'] = $order_info['shipping_fee_arr_saler'];//物流费用
				$order_goods['financial_arr'] = $order_info['financial_arr'];//金融费用
				$order_goods['total'] = $order_info['goods_number'] * $order_info['goods_price'] + $order_info['shipping_fee_arr_saler'] + $order_info['financial_arr'];
				
				$order_total += $order_goods['total'];
				$json_goods[] = $order_goods;
			}
			$data['count'] = count($json_goods);
			$data['order_total'] = $order_total;
			$data['goods_json'] = json_encode($json_goods);
			$data['suppliers_id'] = $_SESSION['suppliers_id'];
			$data['suppliers_name'] = '';
		}
		
/* 		public function insertOrderPayAction($data = array()){
			$sql = 'INSERT INTO order_pay (user_id,suppliers_id,order_id_str,order_sn_str,order_total,suppliers_name,goods_json) VALUES (1,.",".$data['suppliers_id'].",".'2,3','a-b,c-d',$data['order_total'],$data['suppliers_name'],$data['goods_json'] ) ";
						
		} */
		
		/**
		 * 获取订单商品基本信息
		 * @param unknown $order_id
		 */
		public function getOrderBaseInfo($order_id){
			$suppliers_id = $this->getSuppliersId();
// 			print_r($suppliers_id);die;
			$order_table = $GLOBALS['ecs']->table('order_info');
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$sql = 'SELECT odr.`order_id` , odr.`order_sn`, odr.`financial_arr`, og.`goods_id`, og.`goods_name`, og.`goods_sn`, odr.`add_time`, og.`goods_number`, og.`goods_price`,' .
					' odr.`shipping_fee_send_saler`,odr.`shipping_fee_arr_saler`, odr.`child_order_status` ' .
					' FROM ' . $order_table .
					' AS odr LEFT JOIN ' . $order_goods_table . ' AS og ON odr.`order_id` = og.`order_id`' .
					' WHERE odr.`suppers_id` = ' . $suppliers_id. ' AND og.order_id = '.$order_id;
			$order_goods = $GLOBALS['db']->getrow($sql);
// 			print_r($sql);die;
			return $order_goods;
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

			if( empty( $suppliers_id ) ){
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

	}
	$content = jsonAction( 
				array( 'orderPage', 'orderDetail', 'updateChilderStatus', 'addShippingLog', 'addShippingInfo', 'initcategoryShipping',
						'addCategoryShippingFee', 'removeCategoryShipping', 'saveCategorShipping', 'categoryShippingDetail','createOrderPay'
			 	) 
			);
	$supplierModel = new SupplierModel($content);
	$supplierModel->run();