<?php
/**
 * 额度分配单创建、编辑、列表
 * 
 * @author libin@3ti.us
 * date 2015-12-24
 */

define('IN_ECS', true);
//
require(dirname(__FILE__) . '/includes/init.php');
	
	/**
	 * 主要作用是：仅仅只做模板输出。具体数据需要POST调用 class里面的方法。
	 */
	if ($_REQUEST['act'] == 'edit' || $_REQUEST['act'] == 'add' ) {
		$smarty->display('admin_bill_assign.html');
		exit;
	} elseif ( $_REQUEST['act'] == 'list' ) {
		$smarty->display('admin_bill_assign_list.html');
		exit;
	}

	class BillAssignModel {
		
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
		 * 接口地址：http://admin.zjgit.dev/admin/BillAssignModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'bill_assign_log',
	     *      "command": 'page',
	     *      "parameters": {
	     *          "params": {
	     *          	"where": { "customer_id":100,//客户id
	     *          				"type":0// 0:票据 1:现金
	     *          	  },
	     *              "limit": 0,//起始行号
	     *              "offset": 2//返回的行数
	     *          }
	     *      }
	     *  }
	     * 返回数据格式如下 :
	     *  {
		 *		"error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "额度分配单列表查询成功",
		 *	    "content":{
		 *	    	"info":{
		 *	    		"user_name":"xxxx", //客户名
		 *	    		"amount_available":10000//可分配额度
		 *	    	},
		 *	    	"data":[
		 *	        {
		 *                  "bill_amount_history":22 ,//已分配票据采购额度(type 为0)
		 *                  "bill_amount_valid": 100 ,//现有票据采购额度(type 为0)
		 *                  "cash_amount_history":  20,//已分配现金采购额度(type 为1)
		 *                  "cash_amount_valid": 100 ,//现有现金采购额度(type 为1)
		 *                  "contract_id": "2015-12-12"//合同id
		 *           }
		 *	         ],
		 *	         "total":"3"
		 *	    }
		 *	}
		 */
		public	function pageAction(){
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			if( isset( $params["where"] ) && isset( $params["where"]['customer_id'] ) )
				$customer_id = trim( $params["where"]['customer_id'] );

			if( empty( $customer_id ) )
				make_json_response('', '-1', '客户id错误');

			$contract_table = $GLOBALS['ecs']->table('contract');

			if( $type == 0 )//现金
				$contract_sql = 'SELECT `bill_amount_history`, `bill_amount_valid`, `contract_id` FROM ' . $contract_table .
				 				' WHERE `customer_id` = ' . $customer_id . ' ORDER BY `contract_id` ASC';
			else//票据
				$contract_sql = 'SELECT `cash_amount_history`, `cash_amount_valid`, `contract_id` FROM ' . $contract_table .
				 				' WHERE `customer_id` = ' . $customer_id . ' ORDER BY `contract_id` ASC';
			$resultContract = $GLOBALS['db']->qeury($contract_sql);

			if( empty( $resultContract ) )
				make_json_response('', '-1', '客户分配单查询失败');

			$contract_id_arr = array();
			foreach($resultContract as $c)
			{
				$contract_id_arr[] = $c['contract_id'];
			}
			$contract_ids = implode(',', $contract_id_arr);

			$bill_assign_table = $GLOBALS["ecs"]->table("bill_assign_log");
			$sql = 'SELECT * FROM ' . $bill_assign_table;
			$total_sql = 'SELECT COUNT(*) as `total` FROM ' . $bill_assign_table;

			$where_str = ' WHERE `contract_id` in (' . $contract_ids . ')';

			$sql = $sql . $where_str . " LIMIT " . $params['limit'].",".$params['offset'];
			$bill_assigns = $GLOBALS['db']->getAll($sql);
			
			$total_sql = $total_sql . $where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);

			if( $bill_assigns )
			{
				$content = array();
				$content['data'] = $bill_assigns;
				$content['total'] = $resultTotal['total'];

				make_json_response( $content, "0", "分配单查询成功");
			}
			else
			{
				make_json_response("", "-1", "分配单查询失败");
			}
		}
		
		/**
		 * 创建
		 * 接口地址：http://admin.zjgit.dev/admin/BillAssignModel.php
		 * 传入的接口数据格式如下(字段以及对应的值 在parameters里)：* 
		 * 请求方法：POST
		 * {
		 *	    "command": "create",
		 *	    "entity": "bill_assign_log",
		 *	    "parameters": {
		 *                  "type":  0,//0: 票据 1:现金
		 *                  "contract_id": 100 ,//合同id
		 *                  "assign_amount": 4//分配金额
		 *           }
		 * 返回数据格式如下 
		 * {
		 *	    "error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "额度分配单添加成功",
		 *	    "content": ""
		 *	}
		 */
		public	function createAction(){
			/* 获得当前管理员数据信息 */
		    $sql = "SELECT `user_id`, `user_name` ".
		           "FROM " .$GLOBALS['ecs']->table('admin_user'). " WHERE `user_id` = '".$_SESSION['admin_id']."'";
		    $assign_admin_user = $GLOBALS['db']->getRow($sql);

			$content = $this->content;
			$params = $content['parameters'];

			$data['type'] = intval( $params['type'] );
			$data['contract_id'] = intval( $params['contract_id'] );
			$data['assign_time'] = date("Y-m-d H:i:s", time());
			$data['assign_admin_user_id'] = $assign_admin_user['user_id'];
			$data['assign_amount'] = round( ( double )( $params['assign_amount'] ), 2 );
			
			$bill_assign_table = $GLOBALS['ecs']->table('bill_assign_log');
			$sql = 'INSERT INTO ' . $bill_assign_table .' (';

			$dataKey = array_keys( $data );
			foreach($dataKey as $k)
			{
				$sql = $sql . " `" . $k . "`,";
			}
			$sql = substr($sql, 0, -1) . ") VALUES(";

			foreach($data as $d)
			{
				if( is_string( $d ) )
					$d = '\'' . $d . '\'';
				$sql = $sql . $d . ',';
			}

			$sql = substr($sql, 0, -1) . ')';
			$GLOBALS['db']->query("START TRANSACTION");//开启事务
			$createAssign = $GLOBALS['db']->query($sql);
			
			if( $createAssign )
			{
				$contract_table = $GLOBALS['ecs']->table('contract');
				if( $type == 0 )
					$contract_sql = 'UPDATE ' . $contract_table . ' SET `bill_amount_history` = `bill_amount_history` + ' . $data['assign_amount'] .
							' WHERE `contract_id` = ' . $data['contract_id'];
				else
					$contract_sql = 'UPDATE ' . $contract_table . ' SET `cash_amount_history` = `cash_amount_history` + ' . $data['assign_amount'] .
							' WHERE `contract_id` = ' . $data['contract_id'];
				$updateContract = $GLOBALS['db']->query($contract_sql);
				if( $updateContract )
				{
					$GLOBALS['db']->query("COMMIT");//事务提交
					make_json_response("", "0", "额度分配单添加成功");//暂不返回自增id
				}
				else
				{
					$GLOBALS['db']->query("ROLLBACK");//事务回滚
					make_json_response("", "-1", "额度分配单添加失败");//暂不返回自增id
				}
			}
			else
			{
				make_json_response("", "-1", "额度分配单添加失败");
			}
		}
		
		/**
		 * 更新
		 * 接口地址：http://admin.zjgit.dev/admin/BillAssignModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_assign_log_id以及更新的字段 在parameters里)：
		 *      {
		 *		    "command": "update",
		 *		    "entity": "bill_assign_log",
		 *		    "parameters": {
		 *                  "bill_assign_log_id":1 ,
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

			if( !isset( $params['bill_assign_log_id'] ) )
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

			$sql = "UPDATE " . $GLOBALS['ecs']->table("bill_assign_log") . " SET";

			foreach ($data as $p => $pv) {
				$sql = $sql . " `" . $p . "` = " . $pv . ",";
			}
			$sql = substr($sql, 0, -1) ." WHERE `bill_assign_log_id` = " . $params['bill_assign_log_id'];
			
			$result = $GLOBALS['db']->query($sql);

			if( $result )
				make_json_response("", "0", "偿还单更新成功");
			else
				make_json_response("", "-1", "偿还单更新失败");
		}
		
		/**
		 * 删除
		 * 接口地址：http://admin.zjgit.dev/admin/BillAssignModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_assign_log_id)：
		 *      {
		 *		    "command": "delete",
		 *		    "entity": "bill_assign_log",
		 *		    "parameters": {
		 *                  "bill_assign_log_id":2
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
			$bill_repay_id = $content['parameters']['bill_assign_log_id'];
			
			$sql = "DELETE FROM " . $GLOBALS['ecs']->table("bill_assign_log") . " WHERE `bill_assign_log_id` = " . $bill_amount_id;
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
		 * 接口地址：http://admin.zjgit.dev/admin/BillAssignModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下:
		 *      {
		 *		    "command": "addInit",
		 *		    "entity": "bill_assign_log",
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
		 * 接口地址：http://admin.zjgit.dev/admin/BillAssignModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_assign_log_id)：
		 *      {
		 *		    "command": "editInit",
		 *		    "entity": "bill_assign_log",
		 *		    "parameters": {
		 *                  "bill_assign_log_id":2//偿还单ID
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
			$repay_id = intval( $content['parameters']['bill_assign_log_id'] );

			if( $repay_id )
			{
				$repay_table = $GLOBALS['ecs']->table('bill_assign_log');
				$user_table = $GLOBALS['ecs']->table('users');				
				$sql = 'SELECT a.`bill_id`, a.`user_id`, b.`companyName` as `user_name`, a.`repay_amount`, a.`remark` FROM ' . $repay_table .
					 	' as a LEFT JOIN ' . $user_table . ' as b on a.`user_id` = b.`user_id` WHERE `bill_assign_log_id` = ' . 
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
	$billAssignModel = new BillAssignModel($content);
	$billAssignModel->run();