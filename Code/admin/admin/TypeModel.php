<?php
/**
 * 类型、用户 列表查询
 * 
 * @author libin@3ti.us
 * date 2015-12-28
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

	class TypeModel {
		
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
		 * 获取配置或数据
		 * {
		 *	    "command": "bill_type",
		 *	    "parameters": {}
		 *	    		
		 *	    //"command":"admin_user_banks"
		 *	    "parameters": {"user_id":4}
		 *	    
		 *	    //"command":"admin_user_bank_accounts"
		 *	    "parameters": {"user_id":4, "bank_id":1}
		 *	    
		 *	    //"command":"user_banks"
		 *	    "parameters": {"user_id":4}
		 *	    
		 *	    //"command":"user_bank_accounts"
		 *	    "parameters": {"user_id":4, "bank_id":1}
		 *
		 *		//"command":"users"
		 *  	"parameters": {}
		 *   	
		 *		//"command":"admin_users"
		 *  	"parameters": {}
		 *  	
		 *	    "entity": "type"
		 *	    
		 *	}
		 *
		 * 返回的数据格式:
		 * {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content":{} 
		 * }
		 */
		public function run(){
			$sql = '';
			switch ( $this->command ) {
				case 'bill_type':
					$content = array_merge( array(""=> "所有"), C('bill_type') );
					break;
				case 'bill_currency':
					$content = array_merge( array(""=> "所有" ), C('bill_currency') );//
					break;
				case 'users':
					$sql = 'SELECT `user_id`,`user_name`,`companyName` FROM ' . $GLOBALS['ecs']->table('users');
					break;
				case 'admin_users':
					$sql = 'SELECT `user_id`,`user_name` FROM ' . $GLOBALS['ecs']->table('admin_user');
					break;
				case 'admin_user_banks':
					$content = $this->content;
					$user_id = $content['parameters']['user_id'];
					$sql = 'SELECT b.`bank_id`, b.`bank_name` FROM ' . $GLOBALS['ecs']->table('user_bank_account') . ' AS a LEFT JOIN ' .
							$GLOBALS['ecs']->table('bank') . ' AS b ON a.`bank_id` = b.`bank_id` '. 'WHERE a.`type` = 1 AND a.`user_id` = ' . $user_id;
					break;
				case 'admin_user_bank_accounts':
					$content = $this->content;
					$user_id = $content['parameters']['user_id'];
					$bank_id = $content['parameters']['bank_id'];

					$sql = 'SELECT `account` FROM ' . $GLOBALS['ecs']->table('user_bank_account') . ' WHERE `type` = 1 AND `user_id` = ' . $user_id.
							' AND `bank_id` = ' . $bank_id;
					break;							
				case 'user_banks':
					$content = $this->content;
					$user_id = $content['parameters']['user_id'];
					$sql = 'SELECT b.`bank_id`, b.`bank_name` FROM ' . $GLOBALS['ecs']->table('user_bank_account') . ' AS a LEFT JOIN ' .
							$GLOBALS['ecs']->table('bank') . ' AS b ON a.`bank_id` = b.`bank_id` '. 'WHERE a.`type` = 0 AND a.`user_id` = ' . $user_id;
					break;							
				case 'user_bank_accounts':
					$content = $this->content;
					$user_id = $content['parameters']['user_id'];
					$bank_id = $content['parameters']['bank_id'];

					$sql = 'SELECT `account` FROM ' . $GLOBALS['ecs']->table('user_bank_account') . ' WHERE `type` = 0 AND `user_id` = ' . $user_id.
							' AND `bank_id` = ' . $bank_id;							
					break;							
				default:
					# code...
					break;
			}

			if( !empty( $sql ) )
				$content = $GLOBALS['db']->getAll($sql);

			make_json_response($content, '0', '显示列表成功');
		}
				
		public function findAction(){
			
		}
			
		
	}
	$command_arr = array('bill_type', 'bill_currency', 'users', 'admin_users', 
						'admin_user_banks', 'admin_user_bank_accounts', 'user_banks', 'user_bank_accounts'
					);
	$content = jsonAction( $command_arr );
	$typeModel = new TypeModel($content);
	$typeModel->run();