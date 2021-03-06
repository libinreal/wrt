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
			}elseif($this->command == 'review'){
				//
				$this->reviewAction();
			}else {
				//
				$this->pageAction();
			}
		}
		
		/**
		 * 分页显示
		 * 接口地址：http://admin.zjgit.dev/admin/BillAmountModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'bill_amount_log',
	     *      "command": 'find',
	     *      "parameters": {
	     *         	"bill_amount_log_id":10
	     *      }
	     *  }
	     *  返回的数据格式:
		 * {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content":{ 
		 *	    		  "bill_amount_log_id":1,//流水id
		 *	    		  "create_time":2015-10-11//创建日期
		 *	    		  "bill_create_time":2015-10-11//单据创建日期
		 *	    		  "bill_create_user":2015-10-11//单据创建人
		 *	    		  "bill_modify_user":2015-10-11//单据修改人
		 *	    		  "bill_modify_time":2015-10-11//单据修改日期
		 *	    		  
		 *	    		  "amount_type": 0 ,//额度生成类型
		 *	    		  
		 *                  "amount_rate": 1 ,//票据折算比率
		 *                  "amount": 100 ,//生成额度
		 *                  "bill_amount": 100 ,//票面金额
		 *                  "remark": "虚拟数据" ,
		 *                  "user_name": "钟某",//来往单位
		 *                  "user_id": 123,//来往单位id
		 * 	                "bill_id": 0 ,//票据ID
		 * 	                "bill_num": 'no1234',//票据编号
		 * 	                "review_user":'xxx',//审核人
		 * 	                "review_time":'2016-04-18',//审核日期
		 * 	                "review_status":0,//审核状态， 0:未审核 1：已通过 2：未通过
		 *	    }
		 *	}
		 */
		public function findAction(){
			$content = $this->content;
			$bill_amount_id = $content['parameters']['bill_amount_log_id'];

			$bill_table = $GLOBALS['ecs']->table( 'bill' );
			$bill_amount_table = $GLOBALS['ecs']->table( 'bill_amount_log' );
			$users_table = $GLOBALS['ecs']->table( 'users' );

			$bill_join_sql = 'SELECT b.`bill_amount`, b.`bill_num`, b.`create_time` AS `bill_create_time`, '.
								 ' b.`create_user_name` AS `bill_create_user`, b.`modify_user_name` AS `bill_modify_user`,b.`modify_time` AS `bill_modify_time`,' .
								 ' u.`user_id`, u.`companyName` as user_name, a.`bill_id`, a.`create_time`, a.`remark`, a.`amount_rate`, a.`amount`, ' .
								 ' a.`amount_type`, a.`review_user`, a.`review_status`, a.`review_time` '.
								 ' FROM ' . $bill_amount_table . ' AS a ' .
								 ' LEFT JOIN ' . $bill_table . ' AS b ON a.`bill_id` = b.`bill_id` '.
								 ' LEFT JOIN ' . $users_table . 'AS u ON a.`user_id` = u.`user_id` WHERE `bill_amount_log_id` = ' . $bill_amount_id;
				
			$bill_amount_log = $GLOBALS['db']->getRow( $bill_join_sql );//额度生成单内容

			if( empty( $bill_amount_log ) ) {
				make_json_response('', '-1', '票据额度查询失败');
			}

			if($bill_amount_log['review_time'])
				$bill_amount_log['review_time'] = date('Y-m-d H:i:s', $bill_amount_log['review_time']);
			else
				$bill_amount_log['review_time'] = '';
			$bill_amount_log['create_time'] = date('Y-m-d H:i:s', $bill_amount_log['create_time']);

			$bill_amount_log['bill_create_time'] = date('Y-m-d H:i:s', $bill_amount_log['bill_create_time']);
			$bill_amount_log['bill_modify_time'] = date('Y-m-d H:i:s', $bill_amount_log['bill_modify_time']);

			$priv = admin_priv('bill_amount_review', '', false);
			$bill_amount_log['is_review'] = $priv ? 1 : 0;

			make_json_response( $bill_amount_log, '0' );
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
		 *                  "review_status"：1//审核
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
			$sql = 'SELECT IFNULL( b.`issuing_date`, \'\') AS `make_date`, ba.`bill_amount_log_id`, ba.`review_status`, ba.`amount_type`, ba.`amount`, ba.`user_id` as `customer_id`, u.`companyName` as `customer_name`, ba.`create_time`, ba.`create_by`' .
			  	   ' FROM ' . $bill_amount_table . ' AS ba LEFT JOIN ' . $users_table .
				   ' AS u ON u.`user_id` = ba.`user_id` ' .
				   ' LEFT JOIN `bill` AS b ON b.`bill_id` = ba.`bill_id` ';

			$total_sql = "SELECT COUNT(*) as `total` FROM $bill_amount_table" . ' AS ba LEFT JOIN ' . $users_table .
				         ' AS u ON u.`user_id` = ba.`user_id` ' .
				         ' LEFT JOIN `bill` AS b ON b.`bill_id` = ba.`bill_id` ';

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
						$where_str = ' WHERE u.`user_id` in(' . $users_ids . ')';
					}
				}
			}

			if( isset( $where["due_date1"] ) && isset( $where["due_date2"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );
				$where['due_date2'] = strtotime( $where['due_date2'] );

				if( $where_str )
					$where_str .= " AND ba.`create_time` >= '" . $where['due_date1'] . "' AND ba.`create_time` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE ba.`create_time` >= '" . $where['due_date1'] . "' AND ba.`create_time` <= '" . $where['due_date2'] . "'";
			}
			else if( isset( $where["due_date1"] ) )
			{
				$where['due_date1'] = strtotime( $where['due_date1'] );

				if( $where_str )
					$where_str .= " AND ba.`create_time` >= '" . $where['due_date1'] . "'";
				else
					$where_str .= " WHERE ba.`create_time` >= '" . $where['due_date1'] . "'";
			}
			else if( isset( $where["due_date2"] ) )
			{
				$where['due_date2'] = strtotime( $where['due_date2'] );

				if( $where_str )
					$where_str .= " AND ba.`create_time` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE ba.`create_time` <= '" . $where['due_date2'] . "'";
			}

			if( isset( $where["amount_type"] ) ){
				if( $where_str )
					$where_str .= " AND ba.`amount_type` = '" . intval( $where['amount_type'] ) . "'";
				else
					$where_str .= " WHERE ba.`amount_type` = '" . intval( $where['amount_type'] ) . "'";
			}

			$sql = $sql . $where_str . " ORDER BY ba.`bill_amount_log_id` DESC  LIMIT " . $params['limit'].",".$params['offset'];
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
			$data['create_time'] = gmtime();
			$data['create_by'] = $create_by['user_name'];
			
			if( empty( $data['user_id'] ) )
				make_json_response('', '-100', '客户id错误');

			if( $data['bill_id'] == '0' ){//现金
				$data['amount_rate'] = 1;//票据折算比例最大为1，现金额度默认为1
			}else{//票据
				$bill_table = $GLOBALS['ecs']->table('bill');
				
				$bill_review_sql = 'SELECT `review_status` FROM ' . $bill_table .
									' WHERE `bill_id` = ' . $data['bill_id'];
				$bill_review = $GLOBALS['db']->getOne( $bill_review_sql );
				if( $bill_review != 1){
					make_json_response('', '-1', '票据未审核通过，不能生成额度');
				}
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
			
			$createBill = $GLOBALS['db']->query($sql);
			
			$parent_id_sql ='SELECT `parent_id` FROM ' . $GLOBALS['ecs']->table('users') . ' WHERE `user_id` = ' . $data['user_id'];
			$parent_id = $GLOBALS['db']->getOne( $parent_id_sql );

			if( $createBill )
			{
				// $users_table = $GLOBALS['ecs']->table('users');
				// if( $data['bill_id'] != '0' && is_numeric( $data['bill_id'] ))//额度生成类型(0:商票,1:现金,2:承兑)
				// 	$users_sql = 'UPDATE ' . $users_table . ' SET `bill_amount_history` = `bill_amount_history` + ' . $data['amount'] .
				// 			',`bill_amount_valid` = `bill_amount_valid` + ' . $data['amount'] . ' WHERE `user_id` = ' . $data['user_id'] . ' OR `user_id` = ' . $parent_id . ' LIMIT 2';
				// else
				// 	$users_sql = 'UPDATE ' . $users_table . ' SET `cash_amount_history` = `cash_amount_history` + ' . $data['amount'] .
				// 			',`cash_amount_valid` = `cash_amount_valid` + ' . $data['amount'] . ' WHERE `user_id` = ' . $data['user_id'] . ' OR `user_id` = ' . $parent_id . ' LIMIT 2';
				
				// $updateUsers = $GLOBALS['db']->query($users_sql);

				// if( $updateUsers ) {
					
				// 	make_json_response("", "0", "额度生成单添加成功");//暂不返回自增id
				// }else{
					
					make_json_response("", "0", "额度生成单添加成功");//暂不返回自增id
				// }
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
		 * 传入的接口数据格式如下(主键bill_amount_log_id以及更新的字段 在parameters里)：
		 *      {
		 *		    "command": "update",
		 *		    "entity": "bill_amount_log",
		 *		    "parameters": {
		 *                  "bill_amount_log_id":2 ,
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

			$bill_amount_table = $GLOBALS['ecs']->table("bill_amount_log");

			$review_status_sql = 'SELECT `review_status` FROM ' . $bill_amount_table . ' WHERE `bill_amount_log_id` = ' .
								 $params['bill_amount_log_id'];
			$review_status = $GLOBALS['db']->getOne( $review_status_sql );

			if( $review_status == 1 ){//审核已通过
				make_json_response('', '-1', '审核已通过，无法编辑');
			}

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

			$sql = "UPDATE " . $bill_amount_table . " SET";

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
				$sql = 'SELECT `user_id`, `companyName` AS `user_name` FROM ' . $GLOBALS['ecs']->table('users') . ' WHERE `parent_id` = 0 OR `parent_id` IS NULL';
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
		 *                  "amount": 100 ,//折后的额度
		 *                  "bill_amount": 100 ,//票面金额
		 *                  "remark": "虚拟数据" ,
		 *                  "user_name": "钟某",//来往单位
		 *                  "user_id": 123,//来往单位id
		 * 	                "bill_id": 0 ,//票据ID
		 * 	                "bill_num": 'no1234'//票据编号
		 * 	        }
		 *	}
		 */
		public function editInitAction()
		{
			$content = $this->content;
			$bill_amount_id = $content['parameters']['bill_amount_log_id'];

			$bill_table = $GLOBALS['ecs']->table( 'bill' );
			$bill_amount_table = $GLOBALS['ecs']->table( 'bill_amount_log' );
			$users_table = $GLOBALS['ecs']->table( 'users' );

			$bill_join_sql = 'SELECT b.`bill_amount`, b.`bill_num`, u.`user_id`, u.`companyName` as user_name, a.`bill_id`, a.`remark`, a.`amount_rate`, a.`amount`, ' .
								 ' a.`amount_type`'.
								 ' FROM ' . $bill_amount_table . ' AS a ' .
								 ' LEFT JOIN ' . $bill_table . ' AS b ON a.`bill_id` = b.`bill_id` '.
								 ' LEFT JOIN ' . $users_table . 'AS u ON a.`user_id` = u.`user_id` WHERE `bill_amount_log_id` = ' . $bill_amount_id;
				
			$bill_amount_log = $GLOBALS['db']->getRow( $bill_join_sql );//额度生成单内容

			if( empty( $bill_amount_log ) ) {
				make_json_response('', '-1', '票据额度查询失败');
			}

			$init = array();
			if( $bill_amount_log['amount_type'] != 0 )//现金
			{
				$sql = 'SELECT `user_id`, `companyName` AS `user_name` FROM ' . $users_table . ' WHERE `parent_id` = 0 OR `parent_id` IS NULL';
				$users = $GLOBALS['db']->getAll( $sql );
				$init['customer'] = $users;

				$cash_amount_type = array_merge( C('cash_bill_amount_type') );
				$init['amount_type'] = $cash_amount_type;				
			}

			$result['info'] = $bill_amount_log;
			$result['init'] = $init;

			make_json_response( $result, '0' );
		}

		/**
		 * 审核额度生成单
		 * 接口地址：http://admin.zjgit.dev/admin/BillAmountModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_amount_log_id)：
		 *      {
		 *		    "command": "review",
		 *		    "entity": "bill_amount_log",
		 *		    "parameters": {
		 *                  "bill_amount_log_id":2//额度生成单ID
		 *                  "review_status":1//审核状态
		 *                  }
		 *      }
		 *      
		 *  返回的数据格式:
		 * {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content":{ }
		 *  }
		 */
		public function reviewAction(){

			$priv = admin_priv('bill_amount_review', '', false);
			if( !$priv ){
				make_json_response('', '-1', '没有审核权限');
			}

			$content = $this->content;
			$parameters = $content['parameters'];

			$bill_log_id = intval( $parameters['bill_amount_log_id'] );
			$review_status = intval( $parameters['review_status'] );

			if( !$bill_log_id || !$review_status ){
				make_json_response('', '-1', '参数错误');
			}

			$bill_amount_table = $GLOBALS['ecs']->table('bill_amount_log');
			$bill_table = $GLOBALS['ecs']->table('bill');

			//bill_log
			$bill_log_sql = 'SELECT * FROM ' . $bill_amount_table . ' WHERE `bill_amount_log_id` = ' . $bill_log_id;
			$bill_log_data = $GLOBALS['db']->getRow( $bill_log_sql );

			$user_id = intval( $bill_log_data['user_id'] );//生成票据的关联客户
			$bill_id = intval( $bill_log_data['bill_id'] );//额度对应的票据id
			$amount = (double)( $bill_log_data['amount'] );

			/**
			 * 如果是票据，必须为审核过的状态
			 */

			$admin = admin_info();

			$review['review_user_id'] = $admin['user_id'];
			$review['review_user'] = $admin['user_name'];
			$review['review_status'] = $review_status;
			$review['review_time'] = gmtime();

			
			$review_sql = 'UPDATE ' . $bill_amount_table . ' SET ';

			foreach ($review as $key => $value) {
				if( is_string( $value ) )
					$review_sql .= '`' . $key . '` = \'' . $value .'\',';
				else
					$review_sql .= '`' . $key . '` = '  . $value . ',';
			}

			$review_sql  = substr($review_sql, 0, -1) . ' WHERE `bill_amount_log_id` = ' . $bill_log_id . ' LIMIT 1';
			$GLOBALS['db']->query( $review_sql );
			$review_ret = $GLOBALS['db']->affected_rows();

			if( $review_ret ){

				if( $bill_log_data['review_status'] != 1 && $review_status == 1 ){
					$users_table = $GLOBALS['ecs']->table('users');
					$child_id_sql ='SELECT `user_id` FROM ' . $users_table . ' WHERE `parent_id` = ' . $user_id;
					$child_ids = $GLOBALS['db']->getAll( $child_id_sql );

					$bill_user_id_arr = array( $user_id );
					if ( empty( $child_ids ) ){
						$child_ids = array();
					}else{
						foreach ($child_ids as $v) {
							$bill_user_id_arr[] = $v['user_id'];
						}
					}
					$bill_user_id_str = implode(',', $bill_user_id_arr);

					if( $bill_id != 0 )//额度生成类型(0:商票,1:现金,2:承兑)
						$users_sql = 'UPDATE ' . $users_table . ' SET `bill_amount_history` = `bill_amount_history` + ' . $amount .
								',`bill_amount_valid` = `bill_amount_valid` + ' . $amount . ' WHERE `user_id` IN (' . $bill_user_id_str . ')';
					else
						$users_sql = 'UPDATE ' . $users_table . ' SET `cash_amount_history` = `cash_amount_history` + ' . $amount .
								',`cash_amount_valid` = `cash_amount_valid` + ' . $amount . ' WHERE `user_id` IN (' . $bill_user_id_str . ')';
					
					$updateUsers = $GLOBALS['db']->query( $users_sql );
					
					if( $updateUsers ) {
						make_json_response("", "0", "票据生成额度审核成功");
					}

				}else{
					
					make_json_response("", "0", "票据生成额度审核成功");
				}

			}
					
			make_json_response('', '0', '票据生成额度审核失败');
			
		}


		
	}
	$content = jsonAction( array( "editInit", "addInit", 'review' ) );
	$billAmountModel = new BillAmountModel($content);
	$billAmountModel->run();