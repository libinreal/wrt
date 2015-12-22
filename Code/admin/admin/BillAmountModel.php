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
	     *              "like":{"user_name":"no11232"}//客户
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
		 *	    "content": [
		 *	        {
		 *                  "bill_amount_log_id":2 ,//生成单ID
		 *                  "amount_type": 0 ,//生成单类型
		 *                  "amount":  100.00 ,//金额
		 *                  "remark": "xxxx" ,//备注
		 *                  "update_time": "2015-12-12",//修改日期
		 *                  "customer_id": 4 ,//往来单位ID
		 *                  "create_time": "2015-12-12",//创建日期
		 *                  "create_by": "xxx" ,//创建人
		 *           }
		 *	    ]
		 *	}
		 */
		public	function pageAction(){
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$bill_amount_table = $GLOBALS["ecs"]->table("bill_amount_log");
			$sql = "SELECT * FROM $bill_amount_table";

			$where = array();	
			if( isset($params['where']) )
				$where = $params['where'];

			$where_str = '';

			if( isset( $where["like"] ) )
			{
				$like = $where["like"];
				if( isset( $like['user_name'] ) )
					$where_str = " WHERE `user_name` like '%" . $like["user_name"] . "%'";
			}

			if( isset( $where["due_date1"] ) && isset( $where["due_date2"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				$where['due_date2'] = strtotime( $where['due_date2'] );

				if( $where_str )
					$where_str .= " AND `due_date` >= '" . $where['due_date1'] . "' AND `due_date` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE `due_date` >= '" . $where['due_date1'] . "' AND `due_date` <= '" . $where['due_date2'] . "'";
			}
			else if( isset( $where["due_date1"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );

				if( $where_str )
					$where_str .= " AND `due_date` >= '" . $where['due_date1'] . "'";
				else
					$where_str .= " WHERE `due_date` >= '" . $where['due_date1'] . "'";
			}
			else if( isset( $where["due_date2"] ) )
			{
				$where['due_date2'] = strtotime( $where['due_date2'] );

				if( $where_str )
					$where_str .= " AND `due_date` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE `due_date` <= '" . $where['due_date2'] . "'";
			}

			$sql = $sql . $where_str . " LIMIT " . $params['limit'].",".$params['offset'];
			$bills = $GLOBALS['db']->getAll($sql);
			
			if( $bills )
			{
				foreach($bills as &$b)
				{
					$b['update_time'] = date("Y-m-d");
					$b['create_time'] = date("Y-m-d");
				}
				make_json_response( $bills, "0", "生成单列表查询成功");
			}
			else
			{
				make_json_response("", "-1", "生成列表查询失败");
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
			/* 获得当前管理员数据信息 */
		    $sql = "SELECT user_id, user_name ".
		           "FROM " .$GLOBALS['ecs']->table('admin_user'). " WHERE user_id = '".$_SESSION['admin_id']."'";
		    $create_by = $db->getRow($sql);

			$content = $this->content;
			$params = $content['parameters'];

			$data['amount_type'] = intval( $params['amount_type'] );
			$data['amount_rate'] = round( $params['amount_rate'], 4 );
			$data['amount'] = round( ( double )( $params['amount'] ), 2 );
			$data['remark'] = trim( $params['remark'] );
			$data['user_name'] = trim(  $params['user_name'] . '' );
			$data['user_id'] = intval( $params['user_id'] );
			$data['bill_id'] = intval( $params['bill_id'] );
			$data['create_time'] = time();
			$data['create_by'] = $create_by['user_name'];
			$data['create_user_id'] = $create_by['user_id'];
			$data['update_time'] = time();
			
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

			$createBill = $GLOBALS['db']->query($sql);
			
			if( $createBill )
			{
				make_json_response("", "0", "额度生成单添加成功");//暂不返回自增id
			}
			else
			{
				make_json_response("", "-1", "额度生成单添加失败");
			}
		}
		
		/**
		 * 更新
		 * 接口地址：http://admin.zjgit.dev/admin/BillAmountModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_id以及更新的字段 在parameters里)：
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

			foreach ($params as $p => &$pv) {
				if( is_null( $pv ) )
					$pv = 0;
				elseif( is_string( $pv ) )
					$pv = "'" . trim($pv) ."'";
			}	

			$sql = "UPDATE " . $GLOBALS['ecs']->table("bill_amount_log") . " SET";

			foreach ($params as $p => $pv) {
				if( $p == "bill_amount_log_id" )
					continue;
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
		 * 传入的接口数据格式如下(主键bill_id)：
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
		 * 传入的接口数据格式如下(主键bill_id)：
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
				$sql = 'SELECT a.`bill_num`, a.`customer_id` as `user_id`, b.`user_name`, a.`bill_amount`, a.`discount_rate` FROM ' . $bill_table .
				 		' AS a left join ' . $user_table . ' AS b on a.`customer_id` = b.`user_id` WHERE a.`bill_id` = ' . $bill_id;
				$bill = $GLOBALS['db']->getRow( $sql );

				$result['info'] = $bill; 		
			} elseif ( $type == 1) {//现金
				$sql = 'SELECT `user_id`, `user_name` FROM ' . $GLOBALS['ecs']->table('users');
				$users = $GLOBALS['db']->getAll( $sql );
				$init['customer'] = $users;

				$cash_amount_type = array_merge( array( "" => "所有" ), cash_bill_amount_types() );
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
		 * 接口地址：http://admin.zjgit.dev/admin/BillModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_id)：
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
				$bill_amount_table = $GLOBALS['ecs']->table( 'bill_amount_log' );
				$bill_amount_id = $content['parameters']['bill_amount_log_id'];

				$sql = 'SELECT `amount_type`, `amount_rate`, `amount`, `remark`, `user_name`, `user_id`,`bill_id` FROM ' . $bill_amount_table
					 . ' WHERE `bill_amount_log_id` = ' . $bill_amount_id;
				$bill_amount_log = $GLOBALS['db']->getRow( $sql );//额度生成单内容

				$bill_table = $GLOBALS['ecs']->table( 'bill' );
				$sql = 'SELECT `bill_num`,`bill_amount` FROM ' . $bill_table .
						' WHERE `bill_id` = ' . $bill_amount_log['bill_id'];
				$bill = $GLOBALS['db']->getRow( $sql );//票据内容(票据编号、票面金额)

				if( empty( $bill ) )
					make_json_response('', '-1', '生成单所用的票据不存在');

				$result['info'] = array_merge( $bill_amount_log, $bill ); 		
			} elseif ( $type == 1) {//现金
				$sql = 'SELECT `user_id`, `user_name` FROM ' . $GLOBALS['ecs']->table('users');
				$users = $GLOBALS['db']->getAll( $sql );
				$init['customer'] = $users;

				$cash_amount_type = array_merge( array( "" => "所有" ), cash_bill_amount_types() );
				$init['amount_type'] = $cash_amount_type;

				$bill_amount_table = $GLOBALS['ecs']->table( 'bill_amount_log' );
				$bill_amount_id = $content['parameters']['bill_amount_log_id'];

				$sql = 'SELECT `amount_type`, `amount`, `remark`, `user_name`, `user_id` FROM ' . $bill_amount_table
					 . ' WHERE `bill_amount_log_id` = ' . $bill_amount_id;
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
	$content = jsonAction( array( "editInit", "addInit", "listInit" ) );
	$billModel = new BillModel($content);
	$billModel->run();