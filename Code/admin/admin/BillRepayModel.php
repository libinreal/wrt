<?php
/**
 * 票据生成、编辑、列表
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
		 * 接口地址：http://admin.zjgit.dev/admin/BillRepayModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'bill_repay_log',
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
		 *                  "remark": "xxxx" ,//备注
		 *                  "update_time": "2015-12-12",//修改日期
		 *                  "customer_id": 4 ,//往来单位ID
		 *                  "create_time": "2015-12-12",//创建日期
		 *                  "create_by": "xxx" ,//创建人
		 *           }
		 *	         ],
		 *	         "total":"3"
		 *	    }
		 *	}
		 */
		public	function pageAction(){
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$bill_repay_table = $GLOBALS["ecs"]->table("bill_repay_log");
			$sql = "SELECT * FROM $bill_repay_table";
			$total_sql = "SELECT COUNT(*) as `total` FROM $bill_repay_table";

			$where = array();	
			if( isset($params['where']) )
				$where = $params['where'];

			$where_str = '';
			//搜索客户
			if( isset( $where["like"] ) )
			{
				$like = $where["like"];
				if( isset( $like['user_name'] ) && strlen( $like['user_name'] ) > 0 )//条件包含客户名的(先查users表，再根据结果中的user_id查找)
				{

					$user_table = $GLOBALS['ecs']->table('users');
					$users_sql = 'SELECT `user_id` FROM ' . $user_table . ' WHERE `companyName` like \'%' . $like["user_name"] . '%\'';
					$resultUsers = $GLOBALS['db']->getAll($users_sql);
					
					if( empty( $resultUsers ) )
					{
						make_json_response('', '-1', '未找到符合条件的偿还记录');
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
			//搜索日期
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

			$sql = $sql . $where_str . " LIMIT " . $params['limit'].",".$params['offset'];
			$bill_repays = $GLOBALS['db']->getAll($sql);
			
			$total_sql = $total_sql . $where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);

			if( $bill_repays )
			{
				foreach($bill_repays as &$b)
				{
					$b['create_time'] = date("Y-m-d");
				}

				$content = array();
				$content['data'] = $bill_repays;
				$content['total'] = $resultTotal['total'];

				make_json_response( $content, "0", "生成单列表查询成功");
			}
			else
			{
				make_json_response("", "-1", "生成列表查询失败");
			}
		}
		
		/**
		 * 创建
		 * 接口地址：http://admin.zjgit.dev/admin/BillRepayModel.php
		 * 传入的接口数据格式如下(字段以及对应的值 在parameters里)：* 
		 * 请求方法：POST
		 * {
		 *	    "command": "create",
		 *	    "entity": "bill_repay_log",
		 *	    "parameters": {
		 *                  "bill_id": 0 ,//额度生成类型
		 *                  "user_id": 1 ,//客户id
		 *                  "repay_amount": 100 ,//偿还金额
		 *                  "remark": "虚拟数据" ,
		 *           }
		 * 返回数据格式如下 
		 * {
		 *	    "error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "偿还记录添加成功",
		 *	    "content": ""
		 *	}
		 */
		public	function createAction(){


			/* 获得当前管理员数据信息 */

		    $sql = "SELECT `user_id`, `user_name` ".
		           "FROM " .$GLOBALS['ecs']->table('admin_user'). " WHERE `user_id` = '".$_SESSION['admin_id']."'";
		    $create_by = $GLOBALS['db']->getRow($sql);

			$content = $this->content;
			$params = $content['parameters'];

			$data['repay_amount'] = round( ( double )( $params['repay_amount'] ), 2 );
			$data['remark'] = trim( $params['remark'] );
			$data['user_id'] = intval( $params['user_id'] );
			$data['bill_id'] = intval( $params['bill_id'] );
			$data['create_time'] = time();
			$data['create_by'] = $create_by['user_name'];
			
			$bill_table = $GLOBALS['ecs']->table('bill');
			$discount_sql = "SELECT `discount_amount`,`has_repay` FROM " . $bill_table . " WHERE `bill_id` = " . $data['bill_id'];
			$resultDiscount = $GLOBALS['db']->getRow($discount_sql);//折后金额、累计偿还金额

			$dataKey = array_keys( $data );
			$bill_repay_table = $GLOBALS['ecs']->table('bill_repay_log');
			$sql = "INSERT INTO $bill_repay_table (";
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
			$createRepay = $GLOBALS['db']->query($sql);
			
			if( $createRepay )
			{
				if( $resultDiscount['has_repay'] + $data['repay_amount'] >= $resultDiscount['discount_amount'] )
					$bill_sql = 'UPDATE ' . $bill_table . ' SET `has_repay` = `has_repay` + ' . $data['repay_amount'] . ' ,`status` = 1' .
							' WHERE `bill_id` = ' . $data['bill_id'] . ' AND `status` = 0';//未还状态的，更新为已还`status`=1
				else
					$bill_sql = 'UPDATE ' . $bill_table . ' SET `has_repay` = `has_repay` + ' . $data['repay_amount'] .
							' WHERE `bill_id` = ' . $data['bill_id'] . ' AND `status` = 0';//未还状态的

				$updateBill = $GLOBALS['db']->query($bill_sql);
				if( $updateBill )
				{
					$GLOBALS['db']->query("COMMIT");//事务提交
					make_json_response("", "0", "偿还单添加成功");//暂不返回自增id
				}
				else
				{
					$GLOBALS['db']->query("ROLLBACK");//事务回滚
					make_json_response("", "0", "偿还单添加失败");//暂不返回自增id
				}
			}
			else
			{
				make_json_response("", "-1", "偿还单添加失败");
			}
		}
		
		/**
		 * 更新
		 * 接口地址：http://admin.zjgit.dev/admin/BillRepayModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_repay_log_id以及更新的字段 在parameters里)：
		 *      {
		 *		    "command": "update",
		 *		    "entity": "bill_repay_log",
		 *		    "parameters": {
		 *                  "bill_repay_log_id":1 ,
		 *                  "repay_amount": 100 ,//偿还额度
		 *                  "remark": "虚拟数据" ,
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

			if( !isset( $params['bill_repay_log_id'] ) )
				make_json_response('', "-1", "偿还单ID错误");

			$data['repay_amount'] = round( ( double )( $params['repay_amount'] ), 2 );
			$data['remark'] = trim( $params['remark'] );
			$data['user_id'] = intval( $params['user_id'] );
			$data['bill_id'] = intval( $params['bill_id'] );

			foreach ($data as $p => &$pv) {
				if( is_null( $pv ) )
					$pv = 0;
				elseif( is_string( $pv ) )
					$pv = "'" . trim($pv) ."'";
			}	

			$sql = "UPDATE " . $GLOBALS['ecs']->table("bill_repay_log") . " SET";

			foreach ($data as $p => $pv) {
				$sql = $sql . " `" . $p . "` = " . $pv . ",";
			}
			$sql = substr($sql, 0, -1) ." WHERE `bill_repay_log_id` = " . $params['bill_repay_log_id'];
			
			$result = $GLOBALS['db']->query($sql);

			if( $result )
				make_json_response("", "0", "偿还单更新成功");
			else
				make_json_response("", "-1", "偿还单更新失败");
		}
		
		/**
		 * 删除
		 * 接口地址：http://admin.zjgit.dev/admin/BillAmountModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_repay_log_id)：
		 *      {
		 *		    "command": "delete",
		 *		    "entity": "bill_repay_log",
		 *		    "parameters": {
		 *                  "bill_repay_log_id":2
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
			$bill_repay_id = $content['parameters']['bill_repay_log_id'];
			
			$sql = "DELETE FROM " . $GLOBALS['ecs']->table("bill_repay_log") . " WHERE `bill_repay_log_id` = " . $bill_amount_id;
			$result = $GLOBALS['db']->query($sql);
			
			// update `bill` set `has_repay` = `has_repay` - 10
			#code....
			
			if( $result )
				make_json_response("", "0", "偿还单删除成功");
			else
				make_json_response("", "-1", "偿还单删除失败");
		}

		/**
		 * 创建初始化
		 * 接口地址：http://admin.zjgit.dev/admin/BillRepayModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下:
		 *      {
		 *		    "command": "addInit",
		 *		    "entity": "bill_repay_log",
		 *		    "parameters": {
		 *                  "bill_id":1,//票据ID
		 *                  }
		 *      }
		 *      
		 *  返回的数据格式:
		 * {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content":{ 
		 *	    	"info":{
		 *	    		"bill_num":"ax134",//票据编号
		 *	    		"user_name":"库某",//往来单位名称
		 *	    		"user_id":1,//客户id（往来单位id）
		 *	    		"issuing_date":"2015-12-01",//签发日
		 *	    		"due_date":"2015-12-01"//到期日
		 *	    	}
		 *	    }
		 *	}
		 */
		public function addInitAction()
		{
			$content = $this->content;
			$bill_id = $content['parameters']['bill_id'];

			if( $bill_id )
			{
				$bill_table = $GLOBALS['ecs']->table( 'bill' );//票据表
				$user_table = $GLOBALS['ecs']->table( 'users' );//用户表
				$sql = 'SELECT a.`bill_num`, a.`customer_id` as `user_id`, b.`companyName` as `user_name`, a.`issuing_date`, a.`due_date`' .  
						' FROM ' . $bill_table . ' as a LEFT JOIN ' . $user_table . ' as b on a.`customer_id` = b.`user_id` ' . 
						' WHERE a.`bill_id` = ' . $bill_id;
				$bill = $GLOBALS['db']->getRow( $sql );
				if( empty( $bill ) )
					make_json_response('', '-1', '票据未找到');

				$content = array();
				$content['info'] = $bill; 		

				make_json_response( $content, '0', '偿还初始化成功');
			}
			else
			{
				make_json_response('', '-1', '票据ID错误');
			}

		}

		/**
		 * 编辑初始化
		 * 接口地址：http://admin.zjgit.dev/admin/BillRepayModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_repay_log_id)：
		 *      {
		 *		    "command": "editInit",
		 *		    "entity": "bill_repay_log",
		 *		    "parameters": {
		 *                  "bill_repay_log_id":2//偿还单ID
		 *                  }
		 *      }
		 *      
		 *  返回的数据格式:
		 * {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content":{ 
		 *	       "info":{
		 *                  "user_id": 1 ,//客户id(往来单位id)
		 *                  "repay_amount": 100 ,//生成的额度
		 *                  "remark": "虚拟数据" ,
		 *                  "user_name": "钟某",//客户名称
		 * 	                "bill_id": 0 ,//票据ID
		 * 	        }
		 *	}
		 */
		public function editInitAction()
		{
			$content = $this->content;
			$repay_id = intval( $content['parameters']['bill_repay_log_id'] );

			if( $repay_id )
			{
				$repay_table = $GLOBALS['ecs']->table('bill_repay_log');
				$user_table = $GLOBALS['ecs']->table('users');				
				$sql = 'SELECT a.`bill_id`, a.`user_id`, b.`companyName` as `user_name`, a.`repay_amount`, a.`remark` FROM ' . $repay_table .
					 	' as a LEFT JOIN ' . $user_table . ' as b on a.`user_id` = b.`user_id` WHERE `bill_repay_log_id` = ' . 
						$repay_id;

				$repay = $GLOBALS['db']->getRow($sql);
				if( !empty( $repay ) )
				{
					$content = array();
					$content['info'] = $repay;
					make_json_response( $content, '0', '偿还单编辑初始化成功');
				}
				else
				{
					make_json_response('', '-1', '偿还单编辑初始化失败');
				}
			}
			else
			{
				make_json_response('', '-1', '偿还单id为空');
			}

			
		}
		
	}
	$content = jsonAction( array( "editInit", "addInit" ) );
	$billAmountModel = new BillAmountModel($content);
	$billAmountModel->run();