<?php
/**
 * DEMO API
 */

define('IN_ECS', true);
//引入初始化文件
require(dirname(__FILE__) . '/includes/init.php');

	/**
	 * 主要作用是：仅仅只做模板输出。具体数据需要POST调用 class里面的方法。
	 */
	if ($_REQUEST['act'] == 'edit') {
		$test = array('a'=>array('id'=>1,'user_name'=>'kxq'),'b'=>array('id'=>2,'user_name'=>'heheh'),'c'=>array('id'=>3,'user_name'=>'test'));
		$smarty->assign('test',$test);
		$smarty->display('test.htm');
		exit;
	}

	/**
	 * 表操作类。包括基本基本的 CUSD(创建、更新、查询、删除)
	 * @author Administrator
	 *
	 * 1、单个查询
	 *	http://admin.zjgit.dev/admin/UserModel.php
	 *	{
	 *	    "command": "find",
	 *	    "entity": "users",
	 *      "parameters": {
	 *	        "user_id":"135"
	 *	    }
	 *	}
	 *
	 *2、列表查询
	 *http://admin.zjgit.dev/admin/UserModel.php
	 *{
		    "command": "page",
		    "entity": "users",
		    "parameters": {
		        "user_id":"135",
		         "params":{"limit": 2,"offset": 2}
		    }
		}
	 */
	class UserModel {
		
		//$_POST的所有未处理的数据
		protected $content = false;
		//操作命令
		protected $command = false;
		
		//构造方法，获取POST初始数据和接口名称。
		public function __construct($content){
			$this->content = $content;
			$this->command = $content['command'];
		}
		
		/**
		 * 类执行的第一方法。主要是根据实际情况定义接口名称，根据不同接口名称分别进入接口方法。
		 */
		public function run(){
			if ($this->command == 'find') {
				//查询接口(单个)
				$this->findAction();
			}elseif ($this->command == 'page'){
				//列表接口
				$this->pageAction();
			}elseif ($this->command == 'create'){
				//创建
				$this->createAction();
			}elseif ($this->command == 'update'){
				//更新
				$this->updateAction();
			}elseif($this->command == 'delete'){
				//删除
				$this->deleteAction();
			}else {
				//默认列表
				$this->pageAction();
			}
		}
		
		/**
		 *  查询单条数据
		 *  接口地址：http://admin.zjgit.dev/admin/UserModel.php
		 *  传入参数格式(主键id在parameters下级)：
		 *  {
		 *	    "command": "find",
		 *	    "entity": "users",
		 *	    "parameters": {
		 *	        "user_id":"135"
		 *	    }
		 *	}
		 *	 
		 *  返回
		 *	{
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content": {
		 *	        "user_id": "135",
		 *	        "email": "",
		 *	        "user_name": "hahaha"
		 * 	    }
		 *	}
		 */
		public function findAction(){
			$exc = new exchange($GLOBALS['ecs']->table("users"), $db, 'user_id', 'user_name');
			$sql = "SELECT * FROM users WHERE user_id  =135 ";
			$user = $GLOBALS['db']->getRow($sql);
			make_json_response($user,'0','success');
		}
		
		/**
		 * 查询列表（包括分页）
		 * 接口地址：http://admin.zjgit.dev/admin/UserModel.php
		 * 传入参数格式(分页参数limit,offset。查询条件在where中，模糊查询在like中):
	     *  {
	     *      "entity": 'user',
	     *      "command": 'page',
	     *      "parameters": {
	     *          "params": {
	     *              "where": {
	     *              	"like":{"user_name":"kxq","email":"3ti@us"}
	     *                  "password": "123456",
	     *              },
	     *              "limit": 0,
	     *              "offset": 2
	     *          }
	     *      }
	     *  }
	     *  返回
	     *  {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content": [
		 *	        {
		 *	            "user_id": "135",
		 *	            "email": "test@3ti.us",
		 *	            "user_name": "test"
		 *	        },
		 *	        {
		 *	            "user_id": "136",
		 *	            "email": "haha@3ti.us",
		 *	            "user_name": "kxq"
		 *	        }
		 *	    ]
		 *	}
		 */
		public	function pageAction(){
			$exc = new exchange($GLOBALS['ecs']->table("users"), $db, 'user_id', 'user_name');
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$sql = "SELECT * FROM users WHERE user_id in (136,135,133,132) limit ".$params['limit'].",".$params['offset'];
			$user = $GLOBALS['db']->getAll($sql);
			make_json_response($user,'0','success');
		}
		
		/**
		 * 添加
		 * 接口地址：http://admin.zjgit.dev/admin/UserModel.php
		 * 传入接口数据如下格式(主键在parameters中,添加的参数在params中)
		 * {
		 *	    "command": "create",
		 *	    "entity": "users",
		 *	    "parameters": {
		 *	        "user_id":"135",
		 *	         "params":{"username":"kxq","password":"123456","sex":1}
		 *	    }
		 *	}
		 * 返回
		 * {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content": true
		 *	}
		 */
		public	function createAction(){
			$exc = new exchange($GLOBALS['ecs']->table("users"), $db, 'user_id', 'user_name');
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$sql = "INSERT INTO users (user_name, password, sex) VALUES ('kxq','123456',1)";
			$result = $GLOBALS['db']->query($sql);
			make_json_response($result,'0','success');
		}
		
		/**
		 * 更新
		 * 接口地址：http://admin.zjgit.dev/admin/UserModel.php
		 * 传入的接口数据格式如下(主键id在parameters下级,具体更新的字段方在params里)：
		 *      {
		 *		    "command": "update",
		 *		    "entity": "users",
		 *		    "parameters": {
		 *		        "user_id":"135",
		 *		        "params":{
		 *						"username":"test",
		 *						"email":"xqku@3ti.us"
		 *				}
		 *		    }
		 *		}
		 * 返回
		 * {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content": true
		 *	}
		 */
		public	function updateAction(){
			$exc = new exchange($GLOBALS['ecs']->table("users"), $db, 'user_id', 'user_name');
			$content = $this->content;
			$params = $content['parameters']['params'];
			
			$sql = "UPDATE users SET user_name = '".$params['username']."' ,email = '".$params['email']. "' WHERE user_id = 135";
			$result = $GLOBALS['db']->query($sql);
			make_json_response($result,'0','success');
		}
		
		/**
		 * 删除
		 * 接口地址：http://admin.zjgit.dev/admin/UserModel.php
		 * 传入接口数据如下格式(主键id在parameters下级)
		 * 		{
		 *		    "command": "delete",
		 *		    "entity": "users",
		 *		    "parameters": {
		 *		        "user_id":"134",
		 *		    }
		 *		}
		 * 返回
		 * {
		 *	    "error": 0,
		 *	    "message": "",
		 *	    "content": true
		 *	}
		 */
		public	function deleteAction(){
			$exc = new exchange($GLOBALS['ecs']->table("users"), $db, 'user_id', 'user_name');
			$content = $this->content;
			$user_id = $content['parameters']['user_id'];
			
			$sql = "DELETE FROM users WHERE user_id = ".$user_id;
			$result = $GLOBALS['db']->query($sql);
			make_json_response($result,'0','success');
		}
		
	}
	$content = jsonAction();
	$userModel = new UserModel($content);
	$userModel->run();


	
	
	