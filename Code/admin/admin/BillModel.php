<?php
/**
 * 票据创建、编辑、删除、更改、列表
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

	class BillModel {
		
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
		 * 接口地址：http://admin.zjgit.dev/admin/BillModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(具体参数在parameters下的params， "where"可以为空，有则 表示搜索条件，"limit"表示页面首条记录所在行数, "offset"表示要显示的数量)：
	     *  {
	     *      "entity": 'bill',
	     *      "command": 'page',
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              "like":{"bill_num":"no11232","drawer":"郭某某","acceptor":"张某某"},//票据编号 出票人 承票人
	     *                  "status": 0,//票据状态 0 未还 1 已还
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
		 *	    "message": "票据列表查询成功",
		 *	    "content": { 
		 *	    	"data":[
		 *	        {
		 *                  "bill_id":2 ,//ID
		 *                  "bill_type": 0 ,//票据类型
		 *                  "bill_num": "ad11223", //票据编号
		 *                  "bill_amount":  100.00 ,//票面金额
		 *                  "due_date": "2015-12-12" ,//到期日
		 *                  "drawer": "薛某" ,//出票人
		 *                  "acceptor": "李某" ,  //承兑人
		 *                  "customer_id": 4 ,//往来单位ID
		 *                  "customer_name": ,//往来单位
		 *                  "status": 0 ,//还票状态 0:未偿还 1:已偿还
		 *           }
		 *	         ],
		 *	         "total":3
		 *	}
		 */
		public	function pageAction(){
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$bill_table = $GLOBALS["ecs"]->table("bill");
			$user_table = $GLOBALS["ecs"]->table("users");

			$sql = "SELECT a.`bill_id`, a.`bill_type`, a.`due_date`, a.`bill_amount`, a.`customer_id`,b.`companyName` as `customer_name` FROM $bill_table as a " .
				" left join $user_table as b on a.`customer_id` = b.`user_id` ";

			$total_sql = "SELECT COUNT(*) as `total` FROM $bill_table as a " .
				" left join $user_table as b on a.`customer_id` = b.`user_id` "; 	
		
			$where = array();	
			if( isset($params['where']) )
				$where = $params['where'];

			$where_str = '';

			if( isset( $where["like"] ) )
			{
				$like = $where["like"];
				if( isset( $like["bill_num"] ) )
					$where_str = " WHERE `bill_num` like '%" . $like["bill_num"] . "%'";
				else if( isset( $like["drawer"] ) )
					$where_str = " WHERE `drawer` like '%" . $like["drawer"] . "%'";
				else if( isset( $like["acceptor"] ) )
					$where_str = " WHERE `acceptor` like '%" . $like["acceptor"] . "%'";
			}
			
			if( isset( $where["status"] ) )
			{
				if( $where_str )
					$where_str .= " AND `status` = " . $where['status'];
				else
					$where_str .= " WHERE `status` = " . $where['status'];
			}	

			if( isset( $where["due_date1"] ) && isset( $where["due_date2"] ) )
			{
				if( $where_str )
					$where_str .= " AND `due_date` >= '" . $where['due_date1'] . "' AND `due_date` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE `due_date` >= '" . $where['due_date1'] . "' AND `due_date` <= '" . $where['due_date2'] . "'";
			}
			else if( isset( $where["due_date1"] ) )
			{
				if( $where_str )
					$where_str .= " AND `due_date` >= '" . $where['due_date1'] . "'";
				else
					$where_str .= " WHERE `due_date` >= '" . $where['due_date1'] . "'";
			}
			else if( isset( $where["due_date2"] ) )
			{
				if( $where_str )
					$where_str .= " AND `due_date` <= '" . $where['due_date2'] . "'";
				else
					$where_str .= " WHERE `due_date` <= '" . $where['due_date2'] . "'";
			}

			$sql = $sql . $where_str . " LIMIT " . $params['limit'].",".$params['offset'];
			$bills = $GLOBALS['db']->getAll($sql);
			
			$total_sql = $total_sql . $where_str;
			$resultTotal = $GLOBALS['db']->getRow($total_sql);

			if( $bills )
			{
				$content = array();
				$content['data'] = $bills;
				$content['total'] = $resultTotal['total'];
				make_json_response( $content, "0", "票据查询成功");
			}
			else
			{
				make_json_response("", "-1", "票据查询失败");
			}
		}
		
		/**
		 * 创建
		 * 接口地址：http://admin.zjgit.dev/admin/BillModel.php
		 * 传入的接口数据格式如下(字段以及对应的值 在parameters里)：* 
		 * 请求方法：POST
		 * {
		 *	    "command": "create",
		 *	    "entity": "bill",
		 *	    "parameters": {
		 *                  "bill_type": 0 ,
		 *                  "bill_num": "ax1230" ,
		 *                  "currency": 0 ,
		 *                  "bill_amount":  100.00 ,
		 *                  "customer_num": "ZJ001" ,
		 *                  "contract_id": 6881 ,
		 *                  "payment_rate": 0.1120 ,
		 *                  "expire_amount": 10000.21 ,
		 *                  "issuing_date": "2015-12-12" ,
		 *                  "due_date": "2015-12-12" ,
		 *                  "prompt_day": 60 ,
		 *                  "drawer": "薛某" ,
		 *                  "acceptor": "李某" ,  
		 *                  "accept_num": "ZJ100" ,
		 *                  "accept_date": "2015-12-12" ,
		 *                  "remark": "虚拟数据" ,
		 *                  "customer_id": 4 ,
		 * 	                "receive_date": "2015-12-12" ,
		 *                  "trans_amount": 100.10 ,
		 *                  "saler": "乐某" ,
		 *                  "receiver": "华某" ,
		 *                  "balance": "中国建设银行" ,
		 *                  "discount_rate": 98.00 ,
		 *                  "status": 0 ,
		 *                  "discount_amount": 90.00 ,
		 *                  "is_recourse": 0 ,
		 *                  "pay_user_id": 24 ,
		 *                  "pay_bank_id": 2 ,
		 *                  "pay_account": "62200121122330011" ,
		 *                  "receive_user_id": 102 , 
		 *                  "receive_bank_id": 1 ,
		 *                  "receive_account": "62200121122330011"
		 *           }
		 * 返回数据格式如下 
		 * {
		 *	    "error": "0",("0": 成功 ,"-1": 失败)
		 *	    "message": "票据添加成功",
		 *	    "content": ""
		 *	}
		 */
		public	function createAction(){
			$content = $this->content;
			$params = $content['parameters'];

			$data['bill_type'] = intval( $params['bill_type'] );
			$data['bill_num'] = trim( $params['bill_num'] . '' );
			$data['currency'] = intval( $params['currency'] );
			$data['bill_amount'] = round( ( double )( $params['bill_amount'] ), 2 );
			$data['customer_num'] = trim(  $params['customer_num'] . '' );
			$data['contract_id'] = intval( $params['contract_id'] );
			$data['payment_rate'] = round( $params['payment_rate'], 4 );
			$data['expire_amount'] = round( ( double )( $params['expire_amount'] ), 2 );
			$data['issuing_date'] = trim( $params['issuing_date'] . '' );
			$data['due_date'] = trim( $params['due_date'] . '' );
			$data['prompt_day'] = intval( $params['prompt_day'] );
			$data['drawer'] = trim( $params['drawer'] . '' );
			$data['acceptor'] = trim( $params['acceptor'] . '' );
			$data['accept_num'] = trim( $params['accept_num'] . '' );
			$data['accept_date'] = trim( $params['accept_date'] . '' );
			$data['remark'] = trim( $params['remark'] . '' );
			$data['customer_id'] = intval( $params['customer_id'] );
			$data['receive_date'] = trim( $params['receive_date'] . '' );
			$data['trans_amount'] = round( ( double )( $params['trans_amount'] ), 2 );
			$data['saler'] = trim( $params['saler'] . '' );
			$data['receiver'] = trim( $params['receiver'] . '');
			$data['balance'] = trim( $params['balance'] . '' );
			$data['discount_rate'] = round( ( double )( $params['discount_rate'] ), 4);
			$data['status'] = intval( $params['status'] );
			$data['discount_amount'] = round( ( double )( $params['discount_amount'] ), 2);
			$data['is_recourse'] = intval( $params['is_recourse'] );
			$data['pay_user_id'] = intval( $params['pay_user_id'] );
			$data['pay_bank_id'] = intval( $params['pay_bank_id'] );
			$data['pay_account'] = trim( $params['pay_account'] . '');
			$data['receive_user_id'] = intval( $params['receive_user_id'] );
			$data['receive_bank_id'] = intval( $params['receive_bank_id'] );
			$data['receive_account'] = trim( $params['receive_account'] . '' );

			$dataKey = array_keys( $data );
			$bill_table = $GLOBALS['ecs']->table('bill');
			$sql = "INSERT INTO $bill_table (";
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
				make_json_response("", "0", "票据添加成功");//暂不返回自增id
			}
			else
			{
				make_json_response("", "-1", "票据添加失败");
			}
		}
		
		/**
		 * 更新
		 * 接口地址：http://admin.zjgit.dev/admin/BillModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_id以及更新的字段 在parameters里)：
		 *      {
		 *		    "command": "update",
		 *		    "entity": "bill",
		 *		    "parameters": {
		 *                  "bill_id":2 ,
		 *                  "bill_type": 0 ,
		 *                  "bill_num": "a000123" ,
		 *                  "currency": 0 ,
		 *                  "bill_amount":  100.00 ,
		 *                  "customer_num": "ZJ001" ,
		 *                  "contract_id": 6881 ,
		 *                  "payment_rate": 0.1120 ,
		 *                  "expire_amount": 10000.21 ,
		 *                  "issuing_date": "2015-12-12" ,
		 *                  "due_date": "2015-12-12" ,
		 *                  "prompt_day": 60 ,
		 *                  "drawer": "薛某" ,
		 *                  "acceptor": "李某" ,  
		 *                  "accept_num": "ZJ100" ,
		 *                  "accept_date": "2015-12-12" ,
		 *                  "remark": "虚拟数据" ,
		 *                  "customer_id": 4 ,
		 * 	                "receive_date": "2015-12-12" ,
		 *                  "trans_amount": 100.10 ,
		 *                  "saler": "乐某" ,
		 *                  "receiver": "华某" ,
		 *                  "balance": "中国建设银行" ,
		 *                  "discount_rate": 98.00 ,
		 *                  "status": 0 ,
		 *                  "discount_amount": 90.00 ,
		 *                  "is_recourse": 0 ,
		 *                  "pay_user_id": 24 ,
		 *                  "pay_bank_id": 2 ,
		 *                  "pay_account": "62200121122330011" ,
		 *                  "receive_user_id": 102 , 
		 *                  "receive_bank_id": 1 ,
		 *                  "receive_account": "62200121122330011"
		 *           }
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

			if( !isset( $params['bill_id'] ) )
				make_json_response('', "-1", "票据ID错误");

			$data['bill_type'] = intval( $params['bill_type'] );
			$data['bill_num'] = trim( $params['bill_num'] . '' );
			$data['currency'] = intval( $params['currency'] );
			$data['bill_amount'] = round( ( double )( $params['bill_amount'] ), 2 );
			$data['customer_num'] = trim(  $params['customer_num'] . '' );
			$data['contract_id'] = intval( $params['contract_id'] );
			$data['payment_rate'] = round( $params['payment_rate'], 4 );
			$data['expire_amount'] = round( ( double )( $params['expire_amount'] ), 2 );
			$data['issuing_date'] = trim( $params['issuing_date'] . '' );
			$data['due_date'] = trim( $params['due_date'] . '' );
			$data['prompt_day'] = intval( $params['prompt_day'] );
			$data['drawer'] = trim( $params['drawer'] . '' );
			$data['acceptor'] = trim( $params['acceptor'] . '' );
			$data['accept_num'] = trim( $params['accept_num'] . '' );
			$data['accept_date'] = trim( $params['accept_date'] . '' );
			$data['remark'] = trim( $params['remark'] . '' );
			$data['customer_id'] = intval( $params['customer_id'] );
			$data['receive_date'] = trim( $params['receive_date'] . '' );
			$data['trans_amount'] = round( ( double )( $params['trans_amount'] ), 2 );
			$data['saler'] = trim( $params['saler'] . '' );
			$data['receiver'] = trim( $params['receiver'] . '');
			$data['balance'] = trim( $params['balance'] . '' );
			$data['discount_rate'] = round( ( double )( $params['discount_rate'] ), 4);
			$data['status'] = intval( $params['status'] );
			$data['discount_amount'] = round( ( double )( $params['discount_amount'] ), 2);
			$data['is_recourse'] = intval( $params['is_recourse'] );
			$data['pay_user_id'] = intval( $params['pay_user_id'] );
			$data['pay_bank_id'] = intval( $params['pay_bank_id'] );
			$data['pay_account'] = trim( $params['pay_account'] . '');
			$data['receive_user_id'] = intval( $params['receive_user_id'] );
			$data['receive_bank_id'] = intval( $params['receive_bank_id'] );
			$data['receive_account'] = trim( $params['receive_account'] . '' );

			foreach ($data as $p => &$pv) {
				if( is_null( $pv ) )
					$pv = 0;
				elseif( is_string( $pv ) )
					$pv = "'" . trim($pv) ."'";
			}	

			$sql = "UPDATE " . $GLOBALS['ecs']->table("bill") . " SET";

			foreach ($data as $p => $pv) {
				$sql = $sql . " `" . $p . "` = " . $pv . ",";
			}
			$sql = substr($sql, 0, -1) ." WHERE `bill_id` = " . $params['bill_id'];
			
			$result = $GLOBALS['db']->query($sql);

			if( $result )
				make_json_response("", "0", "票据更新成功");
			else
				make_json_response("", "-1", "票据更新失败");
		}
		
		/**
		 * 删除
		 * 接口地址：http://admin.zjgit.dev/admin/BillModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_id)：
		 *      {
		 *		    "command": "delete",
		 *		    "entity": "bill",
		 *		    "parameters": {
		 *                  "bill_id":2
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
			$bill_id = $content['parameters']['bill_id'];
			
			$sql = "DELETE FROM " . $GLOBALS['ecs']->table("bill") . " WHERE `bill_id` = " . $bill_id;
			$result = $GLOBALS['db']->query($sql);

			if( $result )
				make_json_response("", "0", "票据删除成功");
			else
				make_json_response("", "-1", "票据删除失败");
		}

		/**
		 * 编辑初始化
		 * 接口地址：http://admin.zjgit.dev/admin/BillModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_id)：
		 *      {
		 *		    "command": "editInit",
		 *		    "entity": "bill",
		 *		    "parameters": {
		 *                  "bill_id":2
		 *                  }
		 *      }
		 *      
		 *  返回的数据格式:
		 * {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content":{ 
		 *	    	"init":{	
		 *	    		"bill_type":{"0":"xxx", "1":"xxx" },
		 *	    		"currency":{"0":"xxx", "1":"xxx"},
		 *	    		"status":{"0":"xxx", "1":"xxx"},
		 *	    		"is_recourse":{"0":"xxx", "1":"xxx"},
		 *	    		"payers":[
		 *	    			{"user_id":1,"user_name":"xxx"}
		 *	    		],
		 *	    		"receivers":[
		 *	    			{"user_id":1,"user_name":"xxx"}
		 *	    		]
		 *	    	},
		 *	       "info":{
		 *                  "bill_id":2 ,
		 *                  "bill_type": 0 ,
		 *                  "bill_num": "a000123" ,
		 *                  "currency": 0 ,
		 *                  "bill_amount":  100.00 ,
		 *                  "customer_num": "ZJ001" ,
		 *                  "contract_id": 6881 ,
		 *                  "payment_rate": 0.1120 ,
		 *                  "expire_amount": 10000.21 ,
		 *                  "issuing_date": "2015-12-12" ,
		 *                  "due_date": "2015-12-12" ,
		 *                  "prompt_day": 60 ,
		 *                  "drawer": "薛某" ,
		 *                  "acceptor": "李某" ,  
		 *                  "accept_num": "ZJ100" ,
		 *                  "accept_date": "2015-12-12" ,
		 *                  "remark": "虚拟数据" ,
		 *                  "customer_id": 4 ,
		 * 	                "receive_date": "2015-12-12" ,
		 *                  "trans_amount": 100.10 ,
		 *                  "saler": "乐某" ,
		 *                  "receiver": "华某" ,
		 *                  "balance": "中国建设银行" ,
		 *                  "discount_rate": 98.00 ,
		 *                  "serial_number": "a00112" ,
		 *                  "status": 0 ,
		 *                  "discount_amount": 90.00 ,
		 *                  "is_recourse": 0 ,
		 *                  "pay_user_id": 24 ,
		 *                  "pay_bank_id": 2 ,
		 *                  "pay_account": "62200121122330011" ,
		 *                  "receive_user_id": 102 , 
		 *                  "receive_bank_id": 1 ,
		 *                  "receive_account": "62200121122330011"
		 *          }
		 *	    }
		 
		 *	}
		 */
		public function addInitAction()
		{
			$bill_type = array_merge( array(""=> "所有"), bill_types() );//
			$currency = array_merge( array(""=> "所有" ), bill_currencys() );//
			$status = array(""=> "所有", "0" => "已扣减", "1" => "已恢复" );//

			$is_recourse = array( '0' =>"否", '1' => "是");//
			$sql = "SELECT `user_id`, `companyName` as `user_name` FROM " . $GLOBALS['ecs']->table('users');
			$payers = $GLOBALS['db']->getAll( $sql );//
			
			if ( empty( $payers ) )
				$payers = array();

			$sql = "SELECT `user_id`,`user_name` FROM " . $GLOBALS['ecs']->table('admin_user') . " where `role_id` = 2";
			$receivers = $GLOBALS['db']->getAll( $sql );//
			
			if ( empty( $receivers ) )
				$receivers = array();

			$content = array();

			$init['bill_type'] = $bill_type;
			$init['currency'] = $currency;
			$init['status'] = $status;
			
			$init['is_recourse'] = $is_recourse;
			$init['payers'] = $payers;
			$init['receivers'] = $receivers;

			$content['init'] = $init;
			make_json_response( $content, '0' );

		}

		/**
		 * 编辑初始化
		 * 接口地址：http://admin.zjgit.dev/admin/BillModel.php
		 * 请求方法：POST
		 * 传入的接口数据格式如下(主键bill_id)：
		 *      {
		 *		    "command": "editInit",
		 *		    "entity": "bill",
		 *		    "parameters": {
		 *                  "bill_id":2
		 *                  }
		 *      }
		 *      
		 *  返回的数据格式:
		 * {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content":{ 
		 *	    	"init":{	
		 *	    		"bill_type":{"0":"xxx", "1":"xxx" },
		 *	    		"currency":{"0":"xxx", "1":"xxx"},
		 *	    		"status":{"0":"xxx", "1":"xxx"},
		 *	    		"is_recourse":{"0":"xxx", "1":"xxx"},
		 *	    		"payers":[
		 *	    			{"user_id":1,"user_name":"xxx"}
		 *	    		],
		 *	    		"receivers":[
		 *	    			{"user_id":1,"user_name":"xxx"}
		 *	    		]
		 *	    	}
		 *	       "info":{
		 *                  "bill_id":2 ,
		 *                  "bill_type": 0 ,
		 *                  "bill_num": "a000123" ,
		 *                  "currency": 0 ,
		 *                  "bill_amount":  100.00 ,
		 *                  "customer_num": "ZJ001" ,
		 *                  "contract_id": 6881 ,
		 *                  "payment_rate": 0.1120 ,
		 *                  "expire_amount": 10000.21 ,
		 *                  "issuing_date": "2015-12-12" ,
		 *                  "due_date": "2015-12-12" ,
		 *                  "prompt_day": 60 ,
		 *                  "drawer": "薛某" ,
		 *                  "acceptor": "李某" ,  
		 *                  "accept_num": "ZJ100" ,
		 *                  "accept_date": "2015-12-12" ,
		 *                  "remark": "虚拟数据" ,
		 *                  "customer_id": 4 ,
		 * 	                "receive_date": "2015-12-12" ,
		 *                  "trans_amount": 100.10 ,
		 *                  "saler": "乐某" ,
		 *                  "receiver": "华某" ,
		 *                  "balance": "中国建设银行" ,
		 *                  "discount_rate": 98.00 ,
		 *                  "serial_number": "a00112" ,
		 *                  "status": 0 ,
		 *                  "discount_amount": 90.00 ,
		 *                  "is_recourse": 0 ,
		 *                  "pay_user_id": 24 ,
		 *                  "pay_bank_id": 2 ,
		 *                  "pay_account": "62200121122330011" ,
		 *                  "receive_user_id": 102 , 
		 *                  "receive_bank_id": 1 ,
		 *                  "receive_account": "62200121122330011"
		 *          }
		 
		 *	}
		 */
		public function editInitAction()
		{
			$content = $this->content;
			$bill_id = $content['parameters']['bill_id'];

			if( !$bill_id )
				make_json_response('', '-1', '票据ID为空');

			$bill_type = array_merge( array(""=> "所有"), bill_types() );//
			$currency = array_merge( array(""=> "所有" ), bill_currencys() );//
			$status = array(""=> "所有", "0" => "已扣减", "1" => "已恢复" );//

			$is_recourse = array( "0" =>"否", "1" => "是");//
			$sql = "SELECT `user_id`, `companyName` as `user_name` FROM " . $GLOBALS['ecs']->table('users');
			$payers = $GLOBALS['db']->getAll( $sql );//
			
			if ( empty( $payers ) )
				$payers = array();

			$sql = "SELECT `user_id`,`user_name` FROM " . $GLOBALS['ecs']->table('admin_user') . " where `role_id` = 2";
			$receivers = $GLOBALS['db']->getAll( $sql );//
			
			if ( empty( $receivers ) )
				$receivers = array();

			$content = array();

			$init['bill_type'] = $bill_type;
			$init['currency'] = $currency;
			$init['status'] = $status;
			
			$init['is_recourse'] = $is_recourse;
			$init['payers'] = $payers;
			$init['receivers'] = $receivers;

			$content['init'] = $init;

			$sql = "SELECT * FROM ". $GLOBALS['ecs']->table('bill') ." WHERE `bill_id` = $bill_id";
			$bill = $GLOBALS['db']->getRow($sql, true);
			
			if( empty( $bill ) )
				$bill = array();

			$content['info'] = $bill;
			make_json_response( $content, '0' , '');
		}
		
	}
	$content = jsonAction( array( "editInit", "addInit", "listInit" ) );
	$billModel = new BillModel($content);
	$billModel->run();