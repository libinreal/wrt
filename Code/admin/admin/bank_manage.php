<?php
/**
 * 银行管理页面（登记机构）
 * API:
 * class Bank
 * @author <luolu@3ti.us>
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once('ManageModel.php');

/**
 * 登记机构列表
 */
if ( $_REQUEST['act'] == 'list' )
{
	$smarty->display('');
	exit;
}

/**
 * 登记机构详情
 */
elseif ( $_REQUEST['act'] == 'detail' ) {
	$smarty->display('');
	exit;
}

/**
 * 添加登记机构
 */
elseif ( $_REQUEST['act'] == 'add' ) {
	$smarty->display('');
	exit;
}

/**
 * 修改登记机构
 */
elseif ( $_REQUEST['act'] == 'update' ) {
	$smarty->display('');
	exit;
}


$ApiList = array(
		'bankList', 
		'bankDetail', 
		'bankAdd', 
		'deleteBank', 
);

/**
 * 登记机构管理API
 * @author <luolu@3ti.us>
 * @todo http://admin.zj.dev/admin/bank_manage.php
 * API Access:
 * @param string $entity api接口参数,数据表名
 * @param array|null $parameters api接口需要的参数，例如：搜索条件等...
 * @return json
 */
class Bank extends ManageModel 
{
	protected static $_instance;
	
	protected $table;
	protected $db;
	protected $sql;
	
	
	
	/**
	 * 登记机构列表（银行列表）
	 * {
	 *      "command" : "bankList",
	 *      "entity"  : "bank",
	 *      "parameters" : {}
	 * }
	 */
	public function bankList($entity, $parameters) 
	{
		self::init($entity, 'bank');
		self::selectSql(array(
				'fields' => '*', 
				'extend' => 'ORDER BY create_time DESC'
		));
		$res = $this->db->getAll($this->sql);
		make_json_result($res);
	}
	
	
	
	/**
	 * 登记机构详情（银行详情）
	 * {
	 *      "command" : "bankDetail",
	 *      "entity"  : "bank",
	 *      "parameters" : {
	 *      	"bank_id" : "(int)", //银行id
	 *      }
	 * }
	 */
	public function bankDetail($entity, $parameters) 
	{
		self::init($entity, 'bank');
		$bankId = intval($parameters['bank_id']);
		if (!$bankId) {
			failed_json('传参错误');
		}
		self::selectSql(array(
				'fields' => '*', 
				'where'  => 'bank_id='.$bankId
		));
		$res = $this->db->getRow($this->sql);
		make_json_result($res);
	}
	
	
	
	/**
	 * 登记机构添加、修改（银行添加、修改）
	 * {
	 *      "command" : "bankAdd",
	 *      "entity"  : "bank",
	 *      "parameters" : {
	 *      	"bank_id" : "(int)", //银行id，修改时传参
	 *      	"flag"    : "(int)", //修改时传，添加时传0
	 *      	"params"  : {
	 *      		"bank_name" : "(string)", //银行名称
	 *      		"bank_num"  : "(string)", //银行编号
	 *      		"bank_addr" : "(string)", //银行地址
	 *      		"bank_tel"  : "(string)", //银行电话
	 *      		"bank_fax"  : "(string)", //银行传真
	 *      	}
	 *      }
	 * }
	 */
	public function bankAdd($entity, $parameters) 
	{
		self::init($entity, 'bank');
		parent::checkParams(array(
				'flag', 
				'bank_name', 
				'bank_num', 
				'bank_addr', 
				'bank_tel', 
				'bank_fax'
		));
		$params   = $parameters['params'];
		
		$bankId = intval($parameters['bank_id']);
		$flag   = intval($parameters['flag']);
		$bankName = $params['bank_name'];
		$bankNum  = $params['bank_num'];
		$bankAddr = $params['bank_addr'];
		$bankTel  = $params['bank_tel'];
		$bankFax  = $params['bank_fax'];
		
		$where = '';
		//修改登记机构
		if ($flag && !$bankId) {
			return failed_json('没有传参`bank_id`');
		} elseif ($flag && $bankId) {
			$where .= ' AND bank_id!='.$bankId;
		}
		$where .= ' OR bank_name="'.$bankName.'"';
		
		//银行编号、银行名称是否重复
		$this->selectSql(array(
				'fields' => 'COUNT(*) AS num', 
				'where'  => 'bank_num="'.$bankNum.'"'.$where
		));
		$result = $this->db->getOne($this->sql);
		if ($result > 0) {
			return failed_json('该银行编号或名称已经存在');
		}
		
		//修改或添加
		if ($flag) {
			$sql = 'UPDATE '.$this->table.' SET bank_name="'.$bankName.'",bank_num="'.$bankNum.'",bank_addr="'.$bankAddr.'",bank_tel="'.$bankTel.'",bank_fax="'.$bankTel.'" WHERE bank_id='.$bankId;
		} else {
			$sql = 'INSERT '.$this->table.' (bank_name,bank_num,bank_addr,bank_tel,bank_fax)values("'.$bankName.'","'.$bankNum.'","'.$bankAddr.'","'.$bankTel.'","'.$bankFax.'")';
		}
		
		$res = $this->db->query($sql);
		if ( $res ) {
			make_json_result($res);
		} else {
			failed_json('失败');
		}
	}

	
	
	/**
	 * 删除登记机构
	 * {
	 *      "command" : "deleteBank",
	 *      "entity"  : "bank",
	 *      "parameters" : {
	 *      	"bank_id" : "(int)", //银行id
	 *      }
	 * }
	 */
	public function deleteBank($entity, $parameters) 
	{
		self::init($entity, 'bank');
		$bankId = intval($parameters['bank_id']);
		if (!$bankId) {
			failed_json('传参错误');
		}
		
		//查看该银行是否被使用
		$this->table = 'contract';
		self::selectSql(array(
				'fields' => 'COUNT(*) AS num', 
				'where'  => 'bank_id='.$bankId
		));
		$result = $this->db->getOne($this->sql);
		if ($result > 0) {
			failed_json('该登记机构已被使用，不能删除');
		}
		
		//删除登记机构
		$this->table = 'bank';
		$sql = 'DELETE FROM '.$this->table.' WHERE bank_id='.$bankId;
		$res = $this->db->query($sql);
		if ( $res ) {
			make_json_result($res);
		} else {
			failed_json('删除失败');
		}
	}


}
$json = jsonAction($ApiList);
$bank = Bank::getIns();
$bank->run($json);

