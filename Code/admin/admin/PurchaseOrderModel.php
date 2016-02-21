<?php
/**
 * 采购订单的显示、详情
 */

define('IN_ECS', true);
//
require(dirname(__FILE__) . '/includes/init.php');
	
	/**
	 * 主要作用是：仅仅只做模板输出。具体数据需要POST调用 class里面的方法。
	 */
	class PurchaseOrderModel {
		
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
			if ($this->command == 'page'){
				//
				$this->pageAction();
			}elseif ($this->command == 'initPriceSend'){
				//
				$this->initPriceSendAction();
			}elseif ($this->command == 'updatePriceSend'){
				//
				$this->updatePriceSendAction();
			}elseif ($this->command == 'childerDetail'){
				//
				$this->childerDetailAction();
			}elseif ($this->command == 'initPriceArr'){
				//
				$this->initPriceArrAction();
			}elseif ($this->command == 'updatePriceArr'){
				//
				$this->updatePriceArrAction();
			}elseif ($this->command == 'updateChilderStatus'){
				//
				$this->updateChilderStatusAction();
			}
		}


		/**
		 * 接口名称：订单列表  
		 * 接口地址：http://admin.zj.dev/admin/PurchaseOrderModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'order_info',
	     *      "command": 'page',
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              "like":{"order_sn":"no11232","suppliers_name":"郭某某","contract_name":"xxxxx需求合同"},//订单编号 客户名称 合同名称
	     *                  "status": 0,//订单状态
	     *                  "due_date1": 2015-01-01,//起始日期
	     *                  "due_date2": 2015-01-01//结束日期
	     *              },
	     *              "limit": 0,//起始行号
	     *              "offset": 2//返回的行数
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "订单查询成功",
		 *	    "content": { 
		 *	    	"data":[
		 *	        {
		 *	        		"order_id":1, //订单ID
		 *                  "order_sn":2 ,//订单号
		 *                  "goods_name": 'xxx' ,//商品名称
		 *                  "goods_sn": "ad11223", //商品编号
		 *                  "attr": "2015-12-12" ,//规格属性
		 *                  "price": "薛某" ,//单价
		 *                  "number": "薛某" ,//数量
		 *                  "shipping": "薛某" ,//物流
		 *                  "add_time": "2015-12-12 16:41:00" ,  //下单时间
		 *                  "status": 0 //订单状态
		 *           }
		 *	         ],
		 *	         "total":3
		 *	}
		 */
		public	function pageAction(){
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$contract_table = $GLOBALS["ecs"]->table("contract");
			$suppliers_table = $GLOBALS["ecs"]->table("suppliers");
			$order_table = $GLOBALS['ecs']->table('order_info');
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');//订单商品
			$goods_attr_table = $GLOBALS['ecs']->table('goods_attr');//规格/型号/材质

			$sql = 'SELECT odr.`order_id` , odr.`order_sn`, odr.`add_time`, odr.`order_status`, odr.`child_order_status`,' .
				   ' odr.`shipping_fee_send_saler`,odr.`shipping_fee_arr_saler`,' .		
				   ' og.`goods_id`,og.`goods_name`,og.`goods_sn`, og.`goods_number_send_saler`,og.`goods_price_send_saler`,og.`goods_number_arr_saler`,og.`goods_price_arr_saler`' .
				   ' FROM ' . $order_table .
				   ' AS odr';

			$total_sql = 'SELECT COUNT(*) as `total` FROM ' . $order_table .' AS odr'; 	
		
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
				if( isset( $like["order_sn"] ) ){
					$order_sn_sql =  'SELECT `order_id` FROM ' . $order_table . ' WHERE `order_sn` like \'%' . $like['order_sn'] . '%\' ORDER BY `order_id` ASC';
					$order_sn_arr = $GLOBALS['db']->getAll( $order_sn_sql );

					if( !empty( $order_sn_arr ) ){
						$order_sn_id = array();
						foreach ($order_sn_arr as $k => $v) {
							$order_sn_id[] = $v['order_id'];
						}
						$order_ids = implode(',', $order_sn_id);

						$where_str = ' WHERE odr.`order_id` IN(' . $order_ids . ')';
					}	
				}
				if( isset( $like["suppliers_name"] ) ){
					$suppliers_name_sql = 'SELECT `suppliers_id` FROM ' . $suppliers_table . ' WHERE `suppliers_name` like \'%' . $like['suppliers_name'] . '%\' ORDER BY `suppliers_id` ASC';
					$suppliers_name_arr = $GLOBALS['db']->getAll( $suppliers_name_sql );

					if( !empty( $suppliers_name_arr ) ){
						$suppliers_id_arr = array();
						foreach ($suppliers_name_arr as $k => $v) {
							$suppliers_id_arr[] = $v['suppliers_id'];
						}
						$suppliers_ids = implode(',', $suppliers_id_arr);

						if( $where_str )
							$where_str .= ' AND spl.`suppliers_id` IN(' . $suppliers_ids . ')';
						else
							$where_str .= ' WHERE spl.`suppliers_id` IN(' . $user_ids . ')';
					}

				}
				if( isset( $like["contract_name"] ) && $like['contract_name'] ){
					$contract_name_sql = 'SELECT `contract_id` FROM ' . $contract_table . ' WHERE `contract_name` like \'%' . $like['contract_name'] . '%\' ORDER BY `contract_id` ASC';
					$contract_name_arr = $GLOBALS['db']->getAll( $contract_name_sql );
					
					if( !empty( $contract_name_arr ) ){
						$contract_id = array();
						foreach ($contract_name_arr as $k => $v) {
							$contract_id[] = $v['contract_id'];
						}
						$contract_ids = implode(',', $contract_id);

						if( $where_str )
							$where_str .= ' AND crt.`contract_id` IN(' . $contract_ids . ')';
						else
							$where_str .= ' WHERE crt.`contract_id` IN(' . $contract_ids . ')';
					}
				}
			}
			
			if( isset( $where["status"] ) )
			{
				if( $where_str )
					$where_str .= " AND odr.`order_status` = " . $where['status'];
				else
					$where_str .= " WHERE odr.`order_status` = " . $where['status'];
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

			//过滤原始订单
			if( $where_str ){
				$where_str .= ' AND odr.`parent_order_id` IS NOT NULL AND odr.`parent_order_id` <> 0 AND odr.`child_order_status` >= ' . SOS_SEND_PP;
			}else{
				$where_str .= ' WHERE odr.`parent_order_id` IS NOT NULL AND odr.`parent_order_id` <> 0 AND odr.`child_order_status` >= ' . SOS_SEND_PP;
			}

			$sql = $sql .
				   ' LEFT JOIN ' . $suppliers_table . ' as spl ON odr.`suppers_id` = spl.`suppliers_id` '.
				   ' LEFT JOIN ' . $contract_table . ' as crt ON odr.`contract_sn` = crt.`contract_num` ' .
				   ' LEFT JOIN ' . $order_goods_table . ' as og ON og.`order_id` = odr.`order_id` ' .
				   $where_str . ' ORDER BY odr.`add_time` DESC' .
				   ' LIMIT ' . $params['limit'].','.$params['offset'];
			$orders = $GLOBALS['db']->getAll($sql);
			
			$total_sql = $total_sql . 
						' LEFT JOIN ' . $suppliers_table . ' as spl ON odr.`suppers_id` = spl.`suppliers_id` '.
				   		' LEFT JOIN ' . $contract_table . ' as crt ON odr.`contract_sn` = crt.`contract_num` ' .
				   		' LEFT JOIN ' . $order_goods_table . ' as og ON og.`order_id` = odr.`order_id` ' .
						$where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);

			if( $resultTotal )
			{
				$orders = $orders ? $orders : array();
				foreach($orders as &$v){

					if( $v['child_order_status'] < SOS_ARR_CC ){//发货
						$v['number'] = $v['goods_number_send_saler'];//单价
						$v['price'] = $v['goods_price_send_saler'];//数量
						$v['shipping'] = $v['shipping_fee_send_saler'];//物流
					}else{//到货
						$v['number'] = $v['goods_number_arr_saler'];
						$v['price'] = $v['goods_price_arr_saler'];
						$v['shipping'] = $v['shipping_fee_arr_saler'];
					}
					$v['order_sn'] = $v['order_sn'] ? $v['order_sn'] . '-cg' : '';//订单编号
					$v['status'] = $v['order_status'];//状态
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

					unset( $v['goods_number_send_saler'] );
					unset( $v['goods_price_send_saler'] );
					unset( $v['shipping_fee_send_saler'] );
					unset( $v['goods_number_arr_saler'] );
					unset( $v['goods_price_arr_saler'] );
					unset( $v['shipping_fee_arr_saler'] );
					unset( $v['goods_id']);
					unset( $v['order_status'] );
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





	}

$content = jsonAction( array( "initPriceSend", "childerDetail", "initPriceSend", "updatePriceSend", "initPriceArr", "updatePriceArr", "updateChilderStatus",
							 
					 ) );
$orderModel = new PurchaseOrderModel($content);
$orderModel->run();