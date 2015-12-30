<?php
/**
 * 销售订单的显示、详情
 */

define('IN_ECS', true);
//
require(dirname(__FILE__) . '/includes/init.php');
	
	/**
	 * 主要作用是：仅仅只做模板输出。具体数据需要POST调用 class里面的方法。
	 */
	if ($_REQUEST['act'] == 'edit' || $_REQUEST['act'] == 'add' ) {
		
		
		$smarty->display('admin_bill.html');
		exit;
	}

	class OrderInfoModel {
		
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
			}
		}
		
		/**
		 * 接口名称：订单详情
		 * 接口地址：http://admin.zjgit.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'order_info',
	     *      "command": 'page',
	     *      "parameters": {
	     *          "params": {
	     *          	"order_id":1//订单ID
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "",
		 *	    "content": { 
		 *	    	"info":
		 *	        {
		 *	        	
		 *	        },
		 *	        "invoice":
		 *	        {
		 *	        	
		 *	        },
		 *	        "goods":
		 *	        [
		 *	        {
		 *	        	
		 *	        }
		 *	        ]
		 *	    }
		 *	 }   
		 */
		public function findAction(){
			$content = $this->content;
			$params = $content['parameters']['params'];

			if( !isset( $params['order_id'] ) ){
				make_json_response('', '-1', '订单ID错误');
			}
			$order_id = intval( $params['order_id'] );

			$order_info_table = $GLOBALS['ecs']->table('order_info');
			$user_table = $GLOBALS['ecs']->table('users');
			$contract_table = $GLOBALS['ecs']->table('contract');


			//订单详情
			$order_sql = 'SELECT odr.`order_status`, odr.`order_sn`, odr.`user_id`, usr.`user_name`, usr.`companyName`, odr.`add_time`, odr.`contract_sn`, crt.`contract_name` FROM ' .
						 $order_info_table . ' AS odr LEFT JOIN ' . $user_table . '  AS usr ON odr.`user_id` = usr.`user_id` LEFT JOIN ' . $contract_table . ' AS crt ON odr.`contract_sn` = crt.`contract_num` ' . 
						 ' WHERE odr.`order_id` = ' . $order_id;
			$order_info = $GLOBALS['db']->getOne( $order_sql );

			//物料详情
			$order_goods_table = $GLOBALS['ecs']->table('order_goods');
			$order_goods_sql = 'SELECT * FROM ' . $order_goods_table . ' WHERE `order_id` = ' . $order_id;
			$order_goods_arr = $GLOBALS['db']->getAll($order_goods_sql);
							

		}
		
		/**
		 * 接口名称：订单列表  
		 * 接口地址：http://admin.zj.dev/admin/OrderInfoModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'order_info',
	     *      "command": 'page',
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              "like":{"order_sn":"no11232","user_name":"郭某某","contract_name":"xxxxx需求合同"},//订单编号 客户名称 合同名称
	     *                  "status": 0,//订单状态
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
		 *                  "status": 0 ,//订单状态 0:未偿还 1:已偿还
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
				   ' ,usr.`companyName` as `customer_name`, crt.`contract_name` ' .
				   ' FROM ' . $order_table .
				   ' AS odr';

			$total_sql = 'SELECT COUNT(*) as `total` FROM ' . $order_table; 	
		
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

						$where_str = ' WHERE `order_id` IN(' . $order_ids . ')';
					}	
				}
				if( isset( $like["user_name"] ) ){
					$user_name_sql = 'SELECT `user_id` FROM ' . $user_table . ' WHERE `user_name` like \'%' . $like['user_name'] . '%\' ORDER BY `user_id` ASC';
					$user_name_arr = $GLOBALS['db']->getAll( $user_name_sql );

					if( !empty( $user_name_arr ) ){
						$user_id_arr = array();
						foreach ($user_id_arr as $k => $v) {
							$user_id_arr[] = $v['user_id'];
						}
						$user_ids = implode(',', $user_id_arr);

						if( $where_str )
							$where_str .= ' AND `user_id` IN(' . $user_ids . ')';
						else
							$where_str .= ' WHERE `user_id` IN(' . $user_ids . ')';
					}

				}
				if( isset( $like["contract_name"] ) ){
					$contract_name_sql = 'SELECT `contract_id` FROM ' . $contract_table . ' WHERE `contract_name` like \'%' . $like['contract_name'] . '%\' ORDER BY `contract_id` ASC';
					$contract_name_arr = $GLOBALS['db']->getAll( $contract_name_sql );

					if( !empty( $contract_name_arr ) ){
						$contract_id = array();
						foreach ($contract_name_arr as $k => $v) {
							$contract_id[] = $v['contract_id'];
						}
						$contract_ids = implode(',', $contract_id);

						if( $where_str )
							$where_str .= ' AND `contract_id` IN(' . $contract_ids . ')';
						else
							$where_str .= ' WHERE `contract_id` IN(' . $contract_ids . ')';
					}
				}
			}
			
			if( isset( $where["status"] ) && $where['status'] !== '' )
			{
				if( $where_str )
					$where_str .= " AND `status` = " . $where['status'];
				else
					$where_str .= " WHERE `status` = " . $where['status'];
			}	

			if( isset( $where["due_date1"] ) && isset( $where["due_date2"] ) )
			{
				if( $where_str )
					$where_str .= " AND `add_time` >= '" . $where['due_date1'] . "' AND `add_time` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE `add_time` >= '" . $where['due_date1'] . "' AND `add_time` <= '" . $where['due_date2'] . "'";
			}
			else if( isset( $where["due_date1"] ) )
			{
				if( $where_str )
					$where_str .= " AND `add_time` >= '" . $where['due_date1'] . "'";
				else
					$where_str .= " WHERE `add_time` >= '" . $where['due_date1'] . "'";
			}
			else if( isset( $where["due_date2"] ) )
			{
				if( $where_str )
					$where_str .= " AND `add_time` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE `add_time` <= '" . $where['due_date2'] . "'";
			}

			$sql = $sql . $where_str . 
				   ' LEFT JOIN ' . $user_table . ' as usr ON odr.`user_id` = usr.`user_id` '.
				   ' LEFT JOIN ' . $contract_table . ' as crt ON odr.`contract_sn` = crt.`contract_num` ' .
				   ' LIMIT ' . $params['limit'].','.$params['offset'];
			$orders = $GLOBALS['db']->getAll($sql);
			
			$total_sql = $total_sql . $where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);

			if( $resultTotal )
			{
				$content = array();
				$content['data'] = $orders ? $orders : array();
				$content['total'] = $resultTotal['total'];
				make_json_response( $content, "0", "票据查询成功");
			}
			else
			{
				make_json_response("", "-1", "票据查询失败");
			}
		}
		
	
		
		public	function updateAction(){
			$content = $this->content;
			$params = $content['parameters'];

			
		}
		
		
		public function addInitAction()
		{
			

		}

		
		public function editInitAction()
		{
			$content = $this->content;
			$bill_id = $content['parameters']['bill_id'];
		}
			
		
	}
	$content = jsonAction( array( "editInit", "addInit" ) );
	$orderModel = new OrderInfoModel($content);
	$orderModel->run();