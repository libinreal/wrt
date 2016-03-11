<?php
/**
 * 票据额度生成、编辑、列表
 * 
 * @author libin@3ti.us
 * date 2015-12-22
 */

define('IN_ECS', true);
//
require(dirname(__FILE__) . '/includes/init.php');
	
	/**
	 * 主要作用是：仅仅只做模板输出。具体数据需要POST调用 class里面的方法。
	 */
	if ($_REQUEST['act'] == 'edit' || $_REQUEST['act'] == 'add' ) {
		$smarty->display('admin_bill_amount.html');
		exit;
	} elseif ( $_REQUEST['act'] == 'list' ) {
		$smarty->display('admin_bill_amount_list.html');
		exit;
	}

	class BillAmountModel {
		
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
			}elseif ($this->command == 'create'){
				//
				$this->createAction();
			}elseif ($this->command == 'update'){
				//
				$this->updateAction();
			}elseif($this->command == 'delete'){
				//
				$this->deleteAction();
			}elseif($this->command == 'editInit'){
				//
				$this->editInitAction();
			}elseif($this->command == 'addInit'){
				//
				$this->addInitAction();
			}else {
				//
				$this->pageAction();
			}
		}
				
		public function findAction(){
			
		}
		
		/**
		 * 分页显示
		 * 接口地址：http://admin.zjgit.dev/admin/BillAmountModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'bill',
	     *      "command": 'page',
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              "like":{"user_name":"no11232"},//客户
	     *                  "due_date1": "2015-01-01",//起始日期
	     *                  "due_date2": "2015-01-01",//结束日期
	     *              },
	     *              "limit": 0,//起始行号
	     *              "offset": 2//返回的行数
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "额度生成单列表查询成功",
		 *	    "content":{ 
		 *	    	"data":[
		 *	        {
		 *                  "bill_amount_log_id":2 ,//生成单ID
		 *                  "amount_type": 0 ,//生成单类型
		 *                  "amount":  100.00 ,//金额
		 *                  "customer_id": 4 ,//往来单位ID
		 *                  "customer_name": "xxx",//客户
		 *                  "create_time": "2015-12-12",//创建日期
		 *                  "create_by": "xxx" ,//创建人
		 *                  "make_date"："2015-12-12"//单据日期
		 *           }
		 *	         ],
		 *	         "total":"3"
		 *	    }
		 *	}
		 */
		public	function pageAction(){
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$bill_amount_table = $GLOBALS["ecs"]->table("bill_amount_log");
			$users_table = $GLOBALS['ecs']->table( 'users' );
			$sql = 'SELECT IFNULL( b.`issuing_date`, \'\') AS `make_date`, ba.`bill_amount_log_id`, ba.`amount_type`, ba.`amount`, ba.`user_id` as `customer_id`, u.`user_name` as `customer_name`, ba.`create_time`, ba.`create_by`' .
			  	   ' FROM ' . $bill_amount_table . ' AS ba LEFT JOIN ' . $users_table .
				   ' AS u ON u.`user_id` = ba.`user_id` ' .
				   ' LEFT JOIN `bill` AS b ON b.`bill_id` = ba.`bill_id` ';

			$total_sql = "SELECT COUNT(*) as `total` FROM $bill_amount_table";

			$where = array();	
			if( isset($params['where']) )
				$where = $params['where'];

			$where_str = '';

			if( isset( $where["like"] ) )
			{
				$like = $where["like"];
				if ( isset( $like['user_name'] ) )//条件包含客户名的(先查users表，再根据结果中的user_id查找)
				{
					$user_table = $GLOBALS['ecs']->table('users');
					$users_sql = 'SELECT `user_id` FROM ' . $user_table . ' WHERE `companyName` like \'%' . $like["user_name"] . '%\'';
					$resultUsers = $GLOBALS['db']->getAll($users_sql);
					
					if( empty( $resultUsers ) )
					{
						$content = array();
						$content['data'] = array();
						$content['total'] = 0;
						make_json_response( $content , '0', '未找到符合条件的偿还记录');
					}
					else
					{

						$users = array();
						foreach($resultUsers as $u)
						{
							$users[] = $u['user_id'];
						}
						
						$users_ids = implode(',', $users);
						$where_str = ' WHERE `user_id` in(' . $users_ids . ')';
					}
				}
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

			if( isset( $where["amount_type"] ) ){
				if( $where_str )
					$where_str .= " AND `amount_type` = '" . intval( $where['amount_type'] ) . "'";
				else
					$where_str .= " WHERE `amount_type` = '" . intval( $where['amount_type'] ) . "'";
			}

			$sql = $sql . $where_str . " LIMIT " . $params['limit'].",".$params['offset'];
			$bills = $GLOBALS['db']->getAll( $sql );
			
			$total_sql = $total_sql . $where_str;
			$resultTotal = $GLOBALS['db']->getRow( $total_sql );
			
			if( $resultTotal )
			{
				if ( $bills ) {
					foreach($bills as &$b)
					{
						$b['create_time'] = date("Y-m-d", $b['create_time'] );
						if( !$b['make_date'] )
							$b['make_date'] = $b['create_time'];
					}
				} else {
					$bills = array();
				}

				$content = array();
				$content['data'] = $bills;
				$content['total'] = $resultTotal['total'];
				make_json_response( $content, "0", "额度生成单查询成功");
			}
			else
			{
				make_json_response("", "-1", "额度生成单查询失败");
			}
		}

		/**
		 * 是否可以生成票据采购额度
		 * @param $bill_id int 票据id
		 * @return [bool] [false 不能生成采购额度 true 可以生成采购额度]
		 */
		private function checkCreate( $bill_id ){
			$bill_amount_table = $GLOBALS['ecs']->table('bill_amount_log');
			$bill_amount_sql = 'SELECT `bill_id` FROM ' . $bill_amount_table . ' WHERE `bill_id` = ' . $bill_id;
			$bill_id = $GLOBALS['db']->getOne( $bill_amount_sql );

			if( intval( $bill_id ) != 0  ){
				return false;
			}else{
				return true;
			}
		}
		
		/**
		 * 创建
		 * 接口地址：http://admin.zjgit.dev/admin/BillAmountModel.php
		 * 传入的接口数据格式如下(字段以及对应的值 在parameters里)：* 
		 * 请求方法：POST
		 * {
		 *	    "command": "create",
		 *	    "entity": "bill_amount_log",
		 *	    "parameters": {
		 *                  "amount_type": 0 ,//额度生成类型
		 *                  "amount_rate": 1 ,//票据折算比率
		 *                  "amount": 100 ,//生成的额度
		 *                  "remark": "虚拟数据" ,
		 *                  "user_name": "钟某",//客户名称
		 *                  "user_id": 4 ,
		 * 	                "bill_id": 0 ,//票据ID
		 *           }
		 * 返回数据格式如下
		 * {
		 *	    "error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "生成单添加成功",
		 *	    "content": ""
		 *	}
		 */
		public	function createAction(){
			$content = $this->content;
			$params = $content['parameters'];

			if( !$this->checkCreate( intval( $params['bill_id'] ) ) )
				make_json_response('', '-1', '票据不能重复生成采购额度');
			/* 获得当前管理员数据信息 */
		    $sql = "SELECT user_id, user_name ".
		           "FROM " .$GLOBALS['ecs']->table('admin_user'). " WHERE user_id = '".$_SESSION['admin_id']."'";
		    $create_by = $GLOBALS['db']->getRow($sql);

			$data['amount_type'] = intval( $params['amount_type'] );
			$data['amount_rate'] = round( $params['amount_rate'], 4 );
			$data['amount'] = round( ( double )( $params['amount'] ), 2 );
			$data['remark'] = trim( $params['remark'] );
			$data['user_id'] = intval( $params['user_id'] );
			$data['bill_id'] = intval( $params['bill_id'] );
			$data['create_time'] = time();
			$data['create_by'] = $create_by['user_name'];
			
			if( empty( $data['user_id'] ) )
				make_json_response('', '-100', '客户id错误');

			if( $data['bill_id'] == '0' ){
				$data['amount_rate'] = 1;//票据折算比例最大为1，现金额度默认为1
			}

			$dataKey = array_keys( $data );
			$bill_amount_table = $GLOBALS['ecs']->table('bill_amount_log');
			$sql = "INSERT INTO $bill_amount_table (";
			foreach($dataKey as $k)
			{
				$sql = $sql . " `" . $k . "`,";
			}
			$sql = substr($sql, 0, -1) . ") VALUES(";

			foreach($data as $v)
			{
				if(is_string( $v ) )
					$v = "'" . $v . "'";

				$sql = $sql . $v . ",";
			}

			$sql = substr($sql, 0, -1) . ")";
			$GLOBALS['db']->query("START TRANSACTION");//开启事务
			$createBill = $GLOBALS['db']->query($sql);
			
			if( $createBill )
			{
				$users_table = $GLOBALS['ecs']->table('users');
				if( $data['bill_id'] != '0' && is_numeric( $data['bill_id'] ))//额度生成类型(0:商票,1:现金,2:承兑)
					$users_sql = 'UPDATE ' . $users_table . ' SET `bill_amount_history` = `bill_amount_history` + ' . $data['amount'] .
							',`bill_amount_valid` = `bill_amount_valid` + ' . $data['amount'] . ' WHERE `user_id` = ' . $data['user_id'];
				else
					$users_sql = 'UPDATE ' . $users_table . ' SET `cash_amount_history` = `cash_amount_history` + ' . $data['amount'] .
							',`cash_amount_valid` = `cash_amount_valid` + ' . $data['amount'] . ' WHERE `user_id` = ' . $data['user_id'];
				
				$updateUsers = $GLOBALS['db']->query($users_sql);

				if( $updateUsers ) {
					$GLOBALS['db']->query("COMMIT");//事务提交
					make_json_response("", "0", "额度生成单添加成功");//暂不返回自增id
				}else{
					$GLOBALS['db']->query("ROLLBACK");//事务回滚
					make_json_response("", "-1", "额度生成单添加失败");//暂不返回自增id
				}
			}
			else
			{
				$GLOBALS['db']->query("ROLLBACK");//事务回滚
				make_json_response("", "-1", "额度生成单添加失败");
			}
		}
		
		/**
		 * 更新
		 * 接口地址：http://admin.zjgit.dev/admin/BillAmountModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_amount_log_id以及更新的字段 在parameters里)：
		 *      {
		 *		    "command": "update",
		 *		    "entity": "bill_amount_log",
		 *		    "parameters": {
		 *                  "bill_amount_log_id":2 ,
		 *					"amount_type": 0 ,//额度生成类型
		 *                  "amount_rate": 1 ,//票据折算比率
		 *                  "amount": 100 ,//生成的额度
		 *                  "remark": "虚拟数据" ,
		 *                  "user_name": "钟某",//客户名称
		 *                  "user_id": 4 ,
		 * 	                "bill_id": 0 ,//票据ID
		 *           }
		 *      }
		 * 返回数据格式如下 
		 * {
		 *	    "error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "票据更新成功",
		 *	    "content": ""
		 * }
		 */
		public	function updateAction(){
			$content = $this->content;
			$params = $content['parameters'];

			if( !isset( $params['bill_amount_log_id'] ) )
				make_json_response('', "-1", "票据ID错误");

			$data['amount_type'] = intval( $params['amount_type'] );
			$data['amount_rate'] = round( $params['amount_rate'], 4 );
			$data['amount'] = round( ( double )( $params['amount'] ), 2 );
			$data['remark'] = trim( $params['remark'] );
			$data['user_id'] = intval( $params['user_id'] );
			$data['bill_id'] = intval( $params['bill_id'] );

			if( empty( $data['user_id'] ) )
				make_json_response('', '-100', '客户id错误');

			foreach ($data as $p => &$pv) {
				if( is_null( $pv ) )
					$pv = 0;
				elseif( is_string( $pv ) )
					$pv = "'" . trim($pv) ."'";
			}	

			$sql = "UPDATE " . $GLOBALS['ecs']->table("bill_amount_log") . " SET";

			foreach ($data as $p => $pv) {
				$sql = $sql . " `" . $p . "` = " . $pv . ",";
			}
			$sql = substr($sql, 0, -1) ." WHERE `bill_amount_log_id` = " . $params['bill_amount_log_id'];
			
			$result = $GLOBALS['db']->query($sql);

			if( $result )
				make_json_response("", "0", "额度生成单更新成功");
			else
				make_json_response("", "-1", "额度生成单更新失败");
		}
		
		/**
		 * 删除
		 * 接口地址：http://admin.zjgit.dev/admin/BillAmountModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_amount_log_id)：
		 *      {
		 *		    "command": "delete",
		 *		    "entity": "bill_amount_log",
		 *		    "parameters": {
		 *                  "bill_amount_log_id":2
		 *                  }
		 *      }
		 *  返回的数据格式:
		 * 
		 * {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content": ""
		 *	}
		 */
		public	function deleteAction(){
			$content = $this->content;
			$bill_amount_id = $content['parameters']['bill_amount_log_id'];
			
			$sql = "DELETE FROM " . $GLOBALS['ecs']->table("bill_amount_log") . " WHERE `bill_amount_log_id` = " . $bill_amount_id;
			$result = $GLOBALS['db']->query($sql);

			if( $result )
				make_json_response("", "0", "额度生成单删除成功");
			else
				make_json_response("", "-1", "额度生成单删除失败");
		}

		/**
		 * 创建初始化
		 * 接口地址：http://admin.zjgit.dev/admin/BillAmountModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_amount_log_id)：
		 *      {
		 *		    "command": "addInit",
		 *		    "entity": "bill_amount_log",
		 *		    "parameters": {
		 *                  "bill_id":0,//票据ID
		 *                  "type":0// 0:商票 1:现金
		 *                  }
		 *      }
		 *      
		 *  返回的数据格式:
		 * {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content":{ 
		 *	    	"init":{	
		 *	    		"customer":[
		 *	    			{"user_id":1, "user_name":"xxx" }
		 *	    		],//往来单位
		 *	    		"amount_type":{"0":"xxx", "1":"xxx"},//单据类型
		 *	    	},
		 *	    	"info":{
		 *	    		"bill_num":"ax134",//票据编号
		 *	    		"user_name":"库某",//往来单位名称
		 *	    		"user_id":1,//客户id（往来单位id）
		 *	    		"bill_amount":100.00,//票面金额
		 *	    		"discount_rate":1//折算比率
		 *	    	}
		 *	    }
		 *	}
		 */
		public function addInitAction()
		{
			$content = $this->content;
			$type = $content['parameters']['type'];

			if( $type == 0 )//商票
			{
				$user_table = $GLOBALS['ecs']->table( 'users' );//客户表
				$bill_table = $GLOBALS['ecs']->table( 'bill' );//票据表

				$bill_id = $content['parameters']['bill_id'];

				if( !$this->checkCreate( intval( $bill_id ) ) )
					make_json_response('', '-1', '票据不能重复生成采购额度');

				$sql = 'SELECT a.`bill_num`, a.`customer_id` as `user_id`, b.`companyName` AS `user_name`, a.`bill_amount`, a.`discount_rate` FROM ' . $bill_table .
				 		' AS a left join ' . $user_table . ' AS b on a.`customer_id` = b.`user_id` WHERE a.`bill_id` = ' . $bill_id;
				$bill = $GLOBALS['db']->getRow( $sql );

				$result['info'] = $bill; 		
			} elseif ( $type == 1) {//现金
				$sql = 'SELECT `user_id`, `companyName` AS `user_name` FROM ' . $GLOBALS['ecs']->table('users') . ' WHERE `companyName` IS NOT NULL GROUP BY `companyName`';
				$users = $GLOBALS['db']->getAll( $sql );
				$init['customer'] = $users;

				$cash_amount_type = C('cash_bill_amount_type');
				$init['amount_type'] = $cash_amount_type;

				$result['init'] = $init;
			}
			else
			{
				make_json_response('', '-1', '生成单类型为空');
			}

			make_json_response( $result, '0' );

		}

		/**
		 * 编辑初始化
		 * 接口地址：http://admin.zjgit.dev/admin/BillAmountModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_amount_log_id)：
		 *      {
		 *		    "command": "editInit",
		 *		    "entity": "bill_amount_log",
		 *		    "parameters": {
		 *		    		"type":0,//0: 商票 1: 现金
		 *                  "bill_amount_log_id":2//额度生成单ID
		 *                  }
		 *      }
		 *      
		 *  返回的数据格式:
		 * {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content":{ 
		 *	    	"init":{	
		 *	    		"customer":[
		 *	    			{"user_id":1, "user_name":"xxx" }
		 *	    		],//往来单位
		 *	    		"amount_type":{"0":"xxx", "1":"xxx"},//单据类型
		 *	    	},
		 *	       "info":{
		 *                  "amount_type": 0 ,//额度生成类型
		 *                  "amount_rate": 1 ,//票据折算比率
		 *                  "amount": 100 ,//生成的额度
		 *                  "remark": "虚拟数据" ,
		 *                  "user_name": "钟某",//客户名称
		 *                  "user_id": 4 ,
		 * 	                "bill_id": 0 ,//票据ID
		 * 	        }
		 *	}
		 */
		public function editInitAction()
		{
			$content = $this->content;
			$type = $content['parameters']['type'];

			if( $type == 0 )//商票
			{
				$users_table = $GLOBALS['ecs']->table( 'users' );
				$bill_amount_table = $GLOBALS['ecs']->table( 'bill_amount_log' );
				$bill_amount_id = $content['parameters']['bill_amount_log_id'];

				$sql = 'SELECT ba.`amount_type`, ba.`amount_rate`, ba.`amount`, ba.`remark`, u.`user_name`, ba.`user_id`, ba.`bill_id` FROM ' . $bill_amount_table .
					   ' AS ba LEFT JOIN ' . $users_table . ' AS u ON ba.`user_id` = u.`user_id`' .
					   ' WHERE `bill_amount_log_id` = ' . $bill_amount_id;
				$bill_amount_log = $GLOBALS['db']->getRow( $sql );//额度生成单内容

				$bill_table = $GLOBALS['ecs']->table( 'bill' );
				$sql = 'SELECT `bill_num`,`bill_amount` FROM ' . $bill_table .
						' WHERE `bill_id` = ' . $bill_amount_log['bill_id'];
				$bill = $GLOBALS['db']->getRow( $sql );//票据内容(票据编号、票面金额)

				if( empty( $bill ) )
					make_json_response('', '-1', '生成单所用的票据不存在');

				$result['info'] = array_merge( $bill_amount_log, $bill ); 		
			} elseif ( $type == 1) {//现金
				$users_table = $GLOBALS['ecs']->table( 'users' );
				$sql = 'SELECT `user_id`, `user_name` FROM ' . $users_table;
				$users = $GLOBALS['db']->getAll( $sql );
				$init['customer'] = $users;

				$cash_amount_type = array_merge( C('cash_bill_amount_type') );
				$init['amount_type'] = $cash_amount_type;

				$bill_amount_table = $GLOBALS['ecs']->table( 'bill_amount_log' );
				$bill_amount_id = $content['parameters']['bill_amount_log_id'];

				$sql = 'SELECT ba.`amount_type`, ba.`amount`, ba.`remark`, u.`user_name`, ba.`user_id` FROM ' . $bill_amount_table .
				       ' AS ba LEFT JOIN ' . $users_table . ' AS u on u.`user_id` = ba.`user_id`' .
					   ' WHERE `bill_amount_log_id` = ' . $bill_amount_id;
				$bill_amount_log = $GLOBALS['db']->getRow( $sql );//额度生成单内容

				$result['info'] = $bill_amount_log;
				$result['init'] = $init;
			}
			else
			{
				make_json_response('', '-1', '生成单类型为空');
			}

			make_json_response( $result, '0' );
		}
		
	}
	$content = jsonAction( array( "editInit", "addInit" ) );
	$billAmountModel = new BillAmountModel($content);
	$billAmountModel->run();