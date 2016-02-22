<?php
/**
 * 额度调整创建
 * 
 * @author libin@3ti.us
 * date 2015-12-25
 */

define('IN_ECS', true);
//
require(dirname(__FILE__) . '/includes/init.php');
	
	/**
	 * 主要作用是：仅仅只做模板输出。具体数据需要POST调用 class里面的方法。
	 */
	if ($_REQUEST['act'] == 'edit' || $_REQUEST['act'] == 'add' ) {
		$smarty->display('admin_bill_adjust.html');
		exit;
	}

	class BillAdjustModel {
		
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
			}
		}
				
		public function findAction(){
			
		}
		
		/**
		 * 接口名称：额度分配调整列表
		 * 接口地址：http://admin.zjgit.dev/admin/BillAdjustModel.php
		 * 传入的接口数据格式如下(字段以及对应的值 在parameters里)：* 
		 * 请求方法：POST
		 *  {
	     *      "entity": 'bill_adjust',
	     *      "command": 'page',
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              "like":{"customer_name":"no11232","user_name":"v0001","mobile":"+8613830000000"}//客户名称 用户名 手机号码
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
		 *                  "user_id":2 ,//ID
		 *                  "custom_no": "s0001" ,//客户号
		 *                  "customer_name": "ad11223", //客户名称
		 *                  "user_name":  "v000001" ,//用户名
		 *                  "mobile": "+8613830000000" ,//手机
		 *                  "email": "sdasdas@3ti.us" //用户邮箱
		 *           }
		 *	         ],
		 *	         "total":3
		 *	}
		 */
		public function pageAction()
		{
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$bill_table = $GLOBALS["ecs"]->table("bill");
			$user_table = $GLOBALS["ecs"]->table("users");

			$sql = 'SELECT `user_id`, `email`, `customNo` AS `custom_no`, `companyName` AS `customer_name`, `user_name`, `contactsPhone` AS `mobile` ' .
						  'FROM ' . $GLOBALS['ecs']->table('users') . ' AS usr';

			$total_sql = 'SELECT COUNT(*) AS `total` FROM ' . $GLOBALS['ecs']->table('users') . ' AS usr'; 	
		
			$where = array();
			if( isset($params['where']) )
				$where = $params['where'];

			$where_str = ' WHERE `parent_id` = 0';//过滤掉子账号

			if( isset( $where["like"] ) )
			{
				$like = $where["like"];
				if( isset( $like["customer_name"] ) ){
					if( !$where_str )
						$where_str = " WHERE `companyName` like '%" . trim( $like["customer_name"] ) . "%'";
					else
						$where_str .= " AND `companyName` like '%" . trim( $like["customer_name"] ) . "%'";
				}
				if( isset( $like["user_name"] ) ){
					if( !$where_str )
						$where_str = " WHERE `user_name` like '%" . trim( $like["user_name"] ) . "%'";
					else
						$where_str .= ' AND `user_name` like \'%' . trim( $like['user_name'] ) . '%\'';
				}
				if( isset( $like["mobile"] ) ){
					if( !$where_str )
						$where_str = " WHERE `mobile_phone` like '%" . trim( $like["mobile"] ) . "%'";
					else
						$where_str .= " AND `mobile_phone` like '%" . trim( $like["mobile"] ) . "%'";
				}
			}

			$sql .= $where_str . ' LIMIT ' . $params['limit']. ','.$params['offset'];
			$users = $GLOBALS['db']->getAll( $sql );
			
			$total_sql = $total_sql . $where_str;
			$resultTotal = $GLOBALS['db']->getRow( $total_sql );

			if( $resultTotal )
			{
				$content = array();
				$content['data'] = $users ? $users : array();
				$content['total'] = $resultTotal['total'];
				make_json_response( $content, "0", "票据额度调整列表查询成功");
			}
			else
			{
				make_json_response("", "-1", "票据额度调整列表查询失败");
			}

		}				  

		/**
		 * 创建
		 * 接口地址：http://admin.zjgit.dev/admin/BillAdjustModel.php
		 * 传入的接口数据格式如下(字段以及对应的值 在parameters里)：* 
		 * 请求方法：POST
		 * {
		 *	    "command": "create",
		 *	    "entity": "bill_adjust",
		 *	    "parameters": {
		 *                  "type":  0,//0: 采购额度账户 1:现金账户
		 *                  "from_contract_id": 100 ,//调整的合同id
		 *                  "to_contract_id": 100 ,//被调整的目标合同id
		 *                  "adjust_amount": 4//调整的具体额度
		 *           }
		 * }
		 * 返回数据格式如下 
		 * {
		 *	    "error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "额度调整成功",
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

			$data['type'] = intval( $params['type'] );
			$data['from_contract_id'] = intval( $params['from_contract_id'] );
			$data['to_contract_id'] = intval( $params['to_contract_id'] );
			$data['create_time'] = time();
			$data['create_by'] = $create_by['user_id'];
			$data['adjust_amount'] = round( ( double )( $params['adjust_amount'] ), 2 );
			
			if( $data['from_contract_id'] == $data['to_contract_id'] )
				make_json_response('', '-1', '调整和分配的合同号相同');

			$bill_adjust_table = $GLOBALS['ecs']->table('bill_adjust');
			$sql = 'INSERT INTO ' . $bill_adjust_table .' (';

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
			// $GLOBALS['db']->query("START TRANSACTION");//开启事务
			$createAdjust = $GLOBALS['db']->query($sql);
			
			if( $createAdjust )
			{
				$contract_table = $GLOBALS['ecs']->table('contract');
				
				if( $data['type'] == 0){//调整的额度类型(0:采购额度，1:现金额度)
					$contract_from_sql = 'UPDATE ' . $contract_table . ' SET `bill_amount_valid` = `bill_amount_valid` - ' . $data['adjust_amount'] .
							' WHERE `contract_id` = ' . $data['from_contract_id'];
				}else{//现金
					$contract_from_sql = 'UPDATE ' . $contract_table . ' SET `cash_amount_valid` = `cash_amount_valid` - ' . $data['adjust_amount'] .
							' WHERE `contract_id` = ' . $data['from_contract_id'];
				}
				$updateFrom = $GLOBALS['db']->query($contract_from_sql);


				if( $updateFrom )
				{
					if( $data['type'] == 0){//调整的额度类型(0:采购额度，1:现金额度)
						$contract_to_sql = 'UPDATE ' . $contract_table . ' SET `bill_amount_valid` = `bill_amount_valid` + ' . $data['adjust_amount'] .
								' WHERE `contract_id` = ' . $data['to_contract_id'];
					}else{//现金
						$contract_to_sql = 'UPDATE ' . $contract_table . ' SET `cash_amount_valid` = `cash_amount_valid` + ' . $data['adjust_amount'] .
								' WHERE `contract_id` = ' . $data['to_contract_id'];
					}
					$updateTo = $GLOBALS['db']->query($contract_to_sql);

					if( $updateTo )
					{
						make_json_response("", "0", "额度调整单添加成功");//暂不返回自增id
					}
					else
					{
						if( $data['type'] == 0){//调整的额度类型(0:采购额度，1:现金额度)
							$contract_from_sql = 'UPDATE ' . $contract_table . ' SET `bill_amount_valid` = `bill_amount_valid` + ' . $data['adjust_amount'] .
									' WHERE `contract_id` = ' . $data['from_contract_id'];
						}else{//现金
							$contract_from_sql = 'UPDATE ' . $contract_table . ' SET `cash_amount_valid` = `cash_amount_valid` + ' . $data['adjust_amount'] .
									' WHERE `contract_id` = ' . $data['from_contract_id'];
						}
						$GLOBALS['db']->query($contract_from_sql);

						make_json_response("", "-1", "额度调整单添加失败");//暂不返回自增id		
					}
				}
				else
				{
					make_json_response("", "-1", "额度调整单添加失败");//暂不返回自增id
				}
			}
			else
			{
				make_json_response("", "-1", "额度调整单添加失败");
			}
		}
		
		
		/**
		 * 删除
		 * 接口地址：http://admin.zjgit.dev/admin/BillAdjustModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_adjust_id)：
		 *      {
		 *		    "command": "delete",
		 *		    "entity": "bill_adjust",
		 *		    "parameters": {
		 *                  "bill_adjust_id":2
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
			$bill_assign_id = $content['parameters']['bill_adjust_id'];
			
			$sql = "DELETE FROM " . $GLOBALS['ecs']->table("bill_adjust") . " WHERE `bill_adjust_id` = " . $bill_assign_id;
			$result = $GLOBALS['db']->query($sql);
			
			// update `bill` set `has_repay` = `has_repay` - 10
			#code....
			
			if( $result )
				make_json_response("", "0", "额度调整单删除成功");
			else
				make_json_response("", "-1", "额度调整单删除失败");
		}

		/**
		 * 创建初始化
		 * 接口地址：http://admin.zjgit.dev/admin/BillAdjustModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_amount_log_id)：
		 *      {
		 *		    "command": "addInit",
		 *		    "entity": "bill_adjust",
		 *		    "parameters": {
		 *                  "user_id":0//客户id
		 *                  }
		 *      }
		 *      
		 *  返回的数据格式:
		 * {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content":{ 
		 *	    	"init":{	
		 *	    		"contracts":[
		 *	    			{"contract_id":1, "contract_name":"合同1", "bill_amount_valid":1300, "cash_amount_valid":1100 }
		 *	    		],//合同列表
		 *	    		"type":{"0":"采购额度账户", "1":"现金账户"},//账户类型
		 *	    	}
		 *	    }
		 *	}
		 */
		public function addInitAction()
		{
			$content = $this->content;
			$params = $content['parameters'];
			$customer_id = $params['user_id'];

			if( empty( $customer_id ) )
				make_json_response('', '-1', '客户id错误');

			$type = C('bill_adjust_type');
			$contract_table = $GLOBALS['ecs']->table('contract');
			$sql = 'SELECT `contract_id`, `bill_amount_valid`, `contract_name`, `cash_amount_valid` FROM ' . $contract_table .
						' WHERE `customer_id` = ' . $customer_id . ' ORDER BY `contract_id` ASC';
			$resultContract = $GLOBALS['db']->getAll( $sql );

			if( $resultContract )
			{
				$content = array();
				$init['contracts'] = $resultContract;
				$init['type'] = $type;
				$content['init'] = $init;
				make_json_response($content, '0', '额度调整初始化成功');
			}
			else
			{
				make_json_response('', '-1', '额度调整初始化失败');
			}
		}
		
		
	}
	$content = jsonAction( array( 'addInit', 'editInit', 'create', 'page' ) );
	$billAdjustModel = new BillAdjustModel($content);
	$billAdjustModel->run();