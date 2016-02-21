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
		 *                  "customer_name": 'xxx' ,//客户名称
		 *                  "customer_id": "ad11223", //客户号
		 *                  "contract_sn": "2015-12-12" ,//合同号
		 *                  "contract_name": "薛某" ,//销售合同名称
		 *                  "add_time": "2015-12-12 16:41:00" ,  //下单时间
		 *                  "best_time": "2015-12-12 16:41:00" ,//希望到货时间
		 *                  "goods_amount": 200,//下单金额
		 *                  "status": 0 ,//订单状态
		 *                  "is_cancel":"yes"//是否可以取消("yes":可以取消 "no"：不能取消)
		 *           }
		 *	         ],
		 *	         "total":3
		 *	}
		 */
		public	function pageAction(){
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$contract_table = $GLOBALS["ecs"]->table("contract");
			$user_table = $GLOBALS["ecs"]->table("users");
			$order_table = $GLOBALS['ecs']->table('order_info');

			$sql = 'SELECT odr.`order_id` , odr.`order_sn`, odr.`contract_sn`, odr.`user_id` AS `customer_id`, odr.`add_time`, odr.`best_time`, odr.`goods_amount`, odr.`order_status`' .
				   ' ,usr.`companyName` as `customer_name`, IFNULL(crt.`contract_name`,\'\') as `contract_name` ' .
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
				if( isset( $like["user_name"] ) ){
					$user_name_sql = 'SELECT `user_id` FROM ' . $user_table . ' WHERE `companyName` like \'%' . $like['user_name'] . '%\' ORDER BY `user_id` ASC';
					$user_name_arr = $GLOBALS['db']->getAll( $user_name_sql );

					if( !empty( $user_name_arr ) ){
						$user_id_arr = array();
						foreach ($user_name_arr as $k => $v) {
							$user_id_arr[] = $v['user_id'];
						}
						$user_ids = implode(',', $user_id_arr);

						if( $where_str )
							$where_str .= ' AND usr.`user_id` IN(' . $user_ids . ')';
						else
							$where_str .= ' WHERE usr.`user_id` IN(' . $user_ids . ')';
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

			//过滤子订单
			if( $where_str ){
				$where_str .= ' AND ( odr.`parent_order_id` IS NULL OR odr.`parent_order_id` = 0 )';
			}else{
				$where_str .= ' WHERE ( odr.`parent_order_id` IS NULL OR odr.`parent_order_id` = 0 )';
			}

			$sql = $sql .
				   ' LEFT JOIN ' . $user_table . ' as usr ON odr.`user_id` = usr.`user_id` '.
				   ' LEFT JOIN ' . $contract_table . ' as crt ON odr.`contract_sn` = crt.`contract_num` ' .
				   $where_str . 'ORDER BY odr.`add_time` DESC' .
				   ' LIMIT ' . $params['limit'].','.$params['offset'];
			$orders = $GLOBALS['db']->getAll($sql);
			
			$total_sql = $total_sql . 
						' LEFT JOIN ' . $user_table . ' as usr ON odr.`user_id` = usr.`user_id` '.
				   		' LEFT JOIN ' . $contract_table . ' as crt ON odr.`contract_sn` = crt.`contract_num` ' .
						$where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);

			if( $resultTotal )
			{
				$orders = $orders ? $orders : array();
				//订单是否可取消
				foreach ($orders as &$v) {

					$child_total_sql = 'SELECT COUNT(*) AS `child_number` FROM ' . $order_table . ' WHERE `parent_order_id` = ' .
							 		   $v['order_id'] . ' AND `child_order_status` <> ' . SOS_UNCONFIRMED .
							   ' AND `child_order_status` <> ' . SOS_CANCEL;
					$child_total = $GLOBALS['db']->getRow( $child_total_sql );

					if( $child_total['child_number'] == 0 ){
						$v['is_cancel'] = 'yes';
					}else{
						$v['is_cancel'] = 'no';
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





	}

$content = jsonAction( array( "initPriceSend", "childerDetail", "initPriceSend", "updatePriceSend", "initPriceArr", "updatePriceArr", "updateChilderStatus",
							 
					 ) );
$orderModel = new PurchaseOrderModel($content);
$orderModel->run();