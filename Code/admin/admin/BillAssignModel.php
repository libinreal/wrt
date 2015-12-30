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
	if ( $_REQUEST['act'] == 'list' ) {
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
			}elseif($this->command == 'createMulti'){
				//
				$this->createMultiAction();
			}else {
				//
				$this->pageAction();
			}
		}
				
		public function findAction(){
			
		}
		
		/**
		 * 分页显示
		 * 接口地址：http://admin.zj.dev/admin/BillAssignModel.php
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
			$resultContract = $GLOBALS['db']->getAll($contract_sql);

			if( empty( $resultContract ) ){
				$content = array();
				$content['data'] = array();
				$content['total'] = 0;
				make_json_response($content, '0', '客户分配单查询失败');
			}

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

			if( $resultTotal )
			{
				$content = array();
				$content['data'] = $bill_assigns ? $bill_assigns : array();
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
		 * 接口名称：采购额度分配单(单个)
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
			
			//客户当前可用额度
			$contract_user_sql = 'SELECT `customer_id` AS `user_id` FROM ' . $GLOBALS['ecs']->table('contract') . ' WHERE `contract_id` = ' .
								$data['contract_id'];
			$contract_user = $GLOBALS['db']->getRow( $contract_user_sql );
			if( empty( $contract_user ) ){
				make_json_response('', '-100', '合同id关联的客户有误');
			}
			$user_id = $contract_user['user_id'];

			$users_table = $GLOBALS['ecs']->table('users');			
			if( $data['type'] == 0 ) {//分配额度类型(0:票据额度，1:现金额度)
				
				$user_amount_sql = 'SELECT `bill_amount_valid` FROM ' . $users_table . ' WHERE `user_id` = ' . $user_id;
				$user_amount = $GLOBALS['db']->getRow( $user_amount_sql );
				if( $user_amount['bill_amount_valid'] < $data['assign_amount'] )
					make_json_response('', '-600', '分配额度超过当前可用额度');

			} else {
				
				$user_amount_sql = 'SELECT `cash_amount_valid` FROM ' . $users_table . ' WHERE `user_id` = ' . $user_id;
				$user_amount = $GLOBALS['db']->getRow( $user_amount_sql );
				if( $user_amount['cash_amount_valid'] < $data['assign_amount'] )
					make_json_response('', '-600', '分配额度超过当前可用额度');

			}


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
				if( $data['type'] == 0 )//分配额度类型(0:票据额度，1:现金额度)
					$contract_sql = 'UPDATE ' . $contract_table . ' SET `bill_amount_history` = `bill_amount_history` + ' . $data['assign_amount'] .
							',`bill_amount_valid` = `bill_amount_valid` + ' . $data['assign_amount'] . ' WHERE `contract_id` = ' . $data['contract_id'];
				else
					$contract_sql = 'UPDATE ' . $contract_table . ' SET `cash_amount_history` = `cash_amount_history` + ' . $data['assign_amount'] .
							',`cash_amount_valid` = `cash_amount_valid` + ' . $data['assign_amount'] . ' WHERE `contract_id` = ' . $data['contract_id'];
				$updateContract = $GLOBALS['db']->query($contract_sql);
				if( $updateContract )
				{
					$users_table = $GLOBALS['ecs']->table('users');
					if( $data['type'] == 0 )//分配额度类型(0:票据额度，1:现金额度)
						$users_sql = 'UPDATE ' . $users_table . ' SET `bill_amount_valid` = `bill_amount_valid` - ' . $data['assign_amount'] . ' WHERE `user_id` = ' . $user_id;
					else
						$users_sql = 'UPDATE ' . $users_table . ' SET `cash_amount_valid` = `cash_amount_valid` - ' . $data['assign_amount'] . ' WHERE `user_id` = ' . $user_id;
					
					$updateUsers = $GLOBALS['db']->query($users_sql);

					if( $updateUsers ) {

						$GLOBALS['db']->query("COMMIT");//事务提交
						make_json_response("", "0", "额度分配单添加成功");//暂不返回自增id
					}else{
						$GLOBALS['db']->query("ROLLBACK");//事务回滚
						make_json_response("", "-1", "额度生成单添加失败");//暂不返回自增id
					}
				}
				else
				{
					$GLOBALS['db']->query("ROLLBACK");//事务回滚
					make_json_response("", "-1", "额度分配单添加失败");//暂不返回自增id
				}
			}
			else
			{
				$GLOBALS['db']->query("ROLLBACK");//事务回滚
				make_json_response("", "-1", "额度分配单添加失败");
			}
		}
		

		/**
		 * 创建
		 * 接口名称：采购额度分配单(多个)
		 * 接口地址：http://admin.zjgit.dev/admin/BillAssignModel.php
		 * 传入的接口数据格式如下(字段以及对应的值 在parameters里)：* 
		 * 请求方法：POST
		 * {
		 *	    "command": "createMulti",
		 *	    "entity": "bill_assign_log",
		 *	    "parameters": {
		 *                  "type":  0,//0: 票据 1:现金
		 *                  "contract_id": "2,2,20" ,//合同id,多个id以","分割的字符串
		 *                  "assign_amount": "40,60,10"//分配金额,多个额度以","分割的字符串
		 *           }
		 * 返回数据格式如下 
		 * {
		 *	    "error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "额度分配单添加成功",
		 *	    "content": ""
		 *	}
		 */
		public	function createMultiAction(){
			/* 获得当前管理员数据信息 */
		    $sql = "SELECT `user_id`, `user_name` ".
		           "FROM " .$GLOBALS['ecs']->table('admin_user'). " WHERE `user_id` = '".$_SESSION['admin_id']."'";
		    $assign_admin_user = $GLOBALS['db']->getRow($sql);

			$content = $this->content;
			$params = $content['parameters'];

			$data['contract_id'] = explode(",", $params['contract_id']);
			$data['assign_amount'] = explode(",", $params['assign_amount']);
			
			if( empty( $data['contract_id'] ) )
				make_json_response('', '-1', '额度分配时合同ID错误');

			if( empty( $data['assign_amount'] ) )
				make_json_response('', '-1', '分配的额度数据错误');

			$count = count( $data['contract_id'] );

			$data['type'] = array_fill(0, $count, intval( $params['type'] ) ) ;
			$data['assign_time'] = array_fill(0, $count, date("Y-m-d H:i:s", time()) ) ;
			$data['assign_admin_user_id'] = array_fill(0, $count, $assign_admin_user['user_id'] ) ;


			foreach ($data['contract_id'] as &$c) {
				$c = intval($c);
			}
			unset( $c );

			foreach ($data['assign_amount'] as &$a) {
				$a = round( ( double )( $a ), 2 );
			}
			unset( $a );

			//客户当前可用额度
			$contract_user_sql = 'SELECT `customer_id` AS `user_id` FROM ' . $GLOBALS['ecs']->table('contract') . ' WHERE `contract_id` = ' .
								$data['contract_id'][0];
			$contract_user = $GLOBALS['db']->getRow( $contract_user_sql );
			if( empty( $contract_user ) ){
				make_json_response('', '-1', '合同id关联的客户有误');
			}
			$user_id = $contract_user['user_id'];

			$users_table = $GLOBALS['ecs']->table('users');			
			if( $data['type'][0] == 0 ) {//分配额度类型(0:票据额度，1:现金额度)
				
				$user_amount_sql = 'SELECT `bill_amount_valid` FROM ' . $users_table . ' WHERE `user_id` = ' . $user_id;
				$user_amount = $GLOBALS['db']->getRow( $user_amount_sql );
				if( $user_amount['bill_amount_valid'] < array_sum( $data['assign_amount'] ) )
					make_json_response('', '-600', '分配额度超过当前可用额度');

			} else {
				
				$user_amount_sql = 'SELECT `cash_amount_valid` FROM ' . $users_table . ' WHERE `user_id` = ' . $user_id;
				$user_amount = $GLOBALS['db']->getRow( $user_amount_sql );
				if( $user_amount['cash_amount_valid'] < array_sum( $data['assign_amount'] ) )
					make_json_response('', '-600', '分配额度超过当前可用额度');

			}


			$bill_assign_table = $GLOBALS['ecs']->table('bill_assign_log');
			$sql = 'INSERT INTO ' . $bill_assign_table .' (';

			$dataKey = array_keys( $data );
			foreach($dataKey as $k)
			{
				$sql = $sql . " `" . $k . "`,";
			}
			$sql = substr($sql, 0, -1) . ") VALUES";

			$multiData = array();

			for( $j = 0; $j < $count; $j++){

				$multiData[ $j ] = array();
				
				foreach($data as $k=>$col){
					
					$multiData[ $j ][ $k ] = $col[ $j ];
				}
			}

			foreach ($multiData as $m => $single) {
				$sql .= '(' ;
				foreach($single as $d){
					if( is_string( $d ) )
						$d = '\'' . $d . '\'';
					$sql = $sql . $d . ',';
				}
				$sql = substr($sql, 0, -1);
				$sql .= '),' ;
			}

			$sql = substr($sql, 0, -1);
			$GLOBALS['db']->query("START TRANSACTION");//开启事务
			$createAssign = $GLOBALS['db']->query($sql);
			
			if( $createAssign )
			{
				$contract_table = $GLOBALS['ecs']->table('contract');
				
				for($j = 0; $j < $count; $j++){
					if( $data['type'][0] == 0 ){//分配额度类型(0:票据额度，1:现金额度)

						$contract_sql = 'UPDATE ' . $contract_table . ' SET `bill_amount_history` = `bill_amount_history` + ' . $data['assign_amount'][ $j ] .
								',`bill_amount_valid` = `bill_amount_valid` + ' . $data['assign_amount'][ $j ] . ' WHERE `contract_id` = ' . $data['contract_id'][ $j ];
					}else{
						$contract_sql = 'UPDATE ' . $contract_table . ' SET `cash_amount_history` = `cash_amount_history` + ' . $data['assign_amount'][ $j ] .
								',`cash_amount_valid` = `cash_amount_valid` + ' . $data['assign_amount'][ $j ] . ' WHERE `contract_id` = ' . $data['contract_id'][ $j ];
					}

					$updateContract = $GLOBALS['db']->query($contract_sql);
					if( !$updateContract ){
						$GLOBALS['db']->query("ROLLBACK");
						make_json_response("", "-1", "额度生成单添加失败");
					}
				}
				
				$users_table = $GLOBALS['ecs']->table('users');	

				for($j = 0; $j < $count; $j++){
					
					if( $data['type'][0] == 0 )//分配额度类型(0:票据额度，1:现金额度)
						$users_sql = 'UPDATE ' . $users_table . ' SET `bill_amount_valid` = `bill_amount_valid` - ' . $data['assign_amount'][ $j ] . ' WHERE `user_id` = ' . $user_id;
					else
						$users_sql = 'UPDATE ' . $users_table . ' SET `cash_amount_valid` = `cash_amount_valid` - ' . $data['assign_amount'][ $j ] . ' WHERE `user_id` = ' . $user_id;
					
					$updateUsers = $GLOBALS['db']->query($users_sql);
					
					if( !$updateUsers ){
						$GLOBALS['db']->query("ROLLBACK");
						make_json_response("", "-1", "额度分配单添加失败");
					}
				}
				
				$GLOBALS['db']->query("COMMIT");//事务提交
				make_json_response("", "0", "额度分配单添加成功");//暂不返回自增id
			}
			else
			{
				$GLOBALS['db']->query("ROLLBACK");//事务回滚
				make_json_response("", "-1", "额度分配单添加失败");
			}
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
			$bill_assign_id = $content['parameters']['bill_assign_log_id'];
			
			$sql = "DELETE FROM " . $GLOBALS['ecs']->table("bill_assign_log") . " WHERE `bill_assign_log_id` = " . $bill_assign_id;
			$result = $GLOBALS['db']->query($sql);
			
			// update `bill` set `has_repay` = `has_repay` - 10
			#code....
			
			if( $result )
				make_json_response("", "0", "额度分配单删除成功");
			else
				make_json_response("", "-1", "额度分配单删除失败");
		}
		
		
	}
	$content = jsonAction( array( 'createMulti' ) );
	$billAssignModel = new BillAssignModel($content);
	$billAssignModel->run();