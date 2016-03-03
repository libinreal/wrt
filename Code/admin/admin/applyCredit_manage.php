<?php
/**
 * 平台自有授信
 * @author
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once('ManageModel.php');

/**
 * 授信列表
 */
if ($_REQUEST['act'] == 'list') {
	$smarty->display('second/self_credit_list.html');
	exit;
} 

/**
 * 回收站列表
 */
elseif ($_REQUEST['act'] == 'recycle') {
	$smarty->display('second/self_credit_recycle.html');
	exit;
} 

/**
 * 授信详情
 */
elseif ($_REQUEST['act'] == 'detail') {
	$smarty->display('second/self_credit_check_detail.html');
	exit;
}


/**
 * API access
 * @var $ApiList
 */
$ApiList = array(
		'applyCreditSingle', 
		'applyCreditStatus', 
		'applyCreditList', 
		'applyCreditDelete'
);


/**
 * 平台自有授信管理API
 * @author Administrator
 * @todo http://admin.zj.dev/admin/applyCredit_manage.php
 * @param $entity	table name
 * @param $parameters	url params
 * @return json
 */
class ApplyCredit extends ManageModel 
{
	protected static $_instance;
	
	protected $table;
	protected $db;
	protected $sql;
	
	
	/**
	 * 自有授信列表
	 * {
	 * 		"command" : "applyCreditList", 
	 * 		"entity"  : "apply_credit", 
	 * 		"parameters" : {
	 * 			"status" : 4, //移入回收站列表
	 * 			"params" : {
	 * 				"where" : {
	 * 					"like" : {
	 * 						"user_name"     : "(string)", 
	 * 						"contract_name" : "(string)"
	 * 					}
	 * 					"start_time" : "(string)2016-01-20", 
	 * 					"end_time"   : "(string)2016-01-20"
	 * 				}, 
	 * 				"limit" : "(int)", 
	 * 				"offset": "(int)"
	 * 			}
	 * 		}
	 * }
	 */
	public function applyCreditList($entity, $parameters) 
	{
		self::init($entity, 'apply_credit');
		$status = $parameters['status'];
		$limit = intval($parameters['params']['limit']);
		$offset = intval($parameters['params']['offset']);

		//where conditions
		$userName = $parameters['params']['where']['like']['user_name'];
		$contractName = $parameters['params']['where']['like']['contract_name'];
		$startTime = $parameters['params']['where']['start_time'];
		$endTime = $parameters['params']['where']['end_time'];
		unset($parameters);
		if (!$offset) 
			make_json_result(array());
		
		//
		$limit = ($limit < 0) ? 0 : $limit; 
			
		//where conditions
		$where = '';
		if ($userName) {
			if (!empty($where)) $where .= ' AND ';
			$where .= 'u.user_name LIKE "%'.$userName.'%"';
		}
		if ($contractName) {
			if (!empty($where)) $where .= ' AND ';
			$where .= 'c.contract_name LIKE "%'.$contractName.'%"';
		}
		if ($startTime) {
			if (!empty($where)) $where .= ' AND ';
			$where .= 'DATE_FORMAT(ac.create_date, "%Y-%m-%d")>="'.$startTime.'"';
		}
		if ($endTime) {
			if (!empty($where)) $where .= ' AND ';
			$where .= 'DATE_FORMAT(ac.create_date, "%Y-%m-%d")<="'.$endTime.'"';
		}
		if ($status == 4) {
			if (!empty($where)) $where .= ' AND ';
			$where .= 'ac.status=4';
		} else {
			if (!empty($where)) $where .= ' AND ';
			$where .= 'ac.status!=4';
		}
		//获取数据
		self::selectSql(array(
				'fields' => array(
						'ac.*', 
						'DATE_FORMAT(ac.create_date, "%Y-%m-%d") AS create_date', 
						'u.user_name', 
						'c.contract_name'
				), 
				'as'     => 'ac', 
				'join'   => 'LEFT JOIN users AS u on ac.user_id=u.user_id'
							.' LEFT JOIN contract AS c on ac.contract_id=c.contract_id', 
				'where'  => $where, 
				'extend' => 'ORDER BY apply_id ASC limit '.$limit.','.$offset
		));
		$data = $this->db->getAll($this->sql);
		if ($result === false) 
			failed_json('获取列表失败');
		
		//总记录数
		self::selectSql(array(
				'fields' => 'count(ac.apply_id) AS num', 
				'as'     => 'ac', 
				'join'   => 'LEFT JOIN users AS u on ac.user_id=u.user_id'
							.' LEFT JOIN contract AS c on ac.contract_id=c.contract_id', 
				'where'  => $where
		));
		$total = $this->db->getOne($this->sql);
		if ($total === false) 
			failed_json('获取总记录数失败');
		
		$status = array('审核中', '已审核', '审批通过', '审批失败', '已删除');
		foreach ($data as $k=>$v) {
			$data[$k]['status'] = $status[$v['status']];
		}
		make_json_result(array('total' => $total, 'data'=>$data));
	}
	
	
	
	/**
	 * 删除自有授信记录 只删除回收站的数据
	 * {
	 * 		"command" : "applyCreditDelete", 
	 * 		"entity"  : "apply_credit", 
	 * 		"parameters" : {
	 * 			"apply_id" : "(int or array)" //required
	 * 		}
	 * }
	 */
	public function applyCreditDelete($entity, $parameters) 
	{
		self::init($entity, 'apply_credit');
		$applyId = $parameters['apply_id'];
		if (!$applyId) 
			failed_json('没有传参`apply_id`');
		
		if (is_array($applyId)) {
			$applyId = implode(',', $applyId);
			$where = 'apply_id in('.$applyId.')';
		} elseif (is_numeric($applyId)) {
			$where = 'apply_id='.$applyId;
		} else {
			failed_json('传参错误');
		}
		
		$sql = 'DELETE FROM '.$this->table.' WHERE status=4 AND '.$where;
		$result = $this->db->query($sql);
		if ($result === false) 
			failed_json('删除记录失败');
		make_json_result($result);
	}
	
	
	
	/**
	 * 自有授信详情
	 * {
	 * 		"command" : 'applyCreditSingle', 
	 * 		"entity"  : 'apply_credit', 
	 * 		"parameters" : {
	 * 			"apply_id" : "(int)", // required
	 * 			"flag"     : "1" //默认不填 若为1 则是已删除的状态的自有授信详情
	 * 		}
	 * }
	 */
	public function applyCreditSingle($entity, $parameters) 
	{
		self::init($entity, 'apply_credit');
		
		$applyId = $parameters['apply_id'];
		if (!$applyId) {
			failed_json('没有传参`apply_id`');
		}
		
		//是否查看已删除的数据
		$flag = $parameters['flag'];
		if (!$flag) {
			$where = ' AND status!=4';
		}
		
		//授信详情
		self::selectSql(array(
				'fields' => '*', 
				'where'  => 'apply_id='.$applyId.$where
		));
		$data = $this->db->getRow($this->sql);
		if ($data === false) 
			failed_json('没有此数据');
		if (empty($data)) 
			make_json_result(array());
		
		//申请人信息
		$this->table = 'users';
		self::selectSql(array(
				'fields' => array(
						'user_id', 
						'user_name', 
						'companyName'
				), 
				'where'  => 'user_id='.$data['user_id']
		));
		$users = $this->db->getRow($this->sql);
		if (!$users) 
			failed_json('获取申请人信息失败');
		
		//合同名称
		$this->table = 'contract';
		self::selectSql(array(
				'fields' => array(
						'contract_id', 
						'contract_name'
				), 
				'where'  => 'contract_id='.$data['contract_id']
		));
		$contracts = $this->db->getRow($this->sql);
		if (!$contracts) 
			failed_json('获取合同名称失败');
		
		$data['account']      = $users['user_name'];
		$data['companyName']  = $users['companyName'];
		$data['contractName'] = $contracts['contract_name'];
		$data['create_date']  = substr($data['create_date'], 0, 10);
		unset($users, $contracts);
		make_json_result($data);
	}
	
	
	/**
	 * 修改自有授信状态
	 * {
	 * 		"command" : 'applyCreditStatus', 
	 * 		"entity"  : 'apply_credit', 
	 * 		"parameters" : {
	 * 			"apply_id" : "(int or array)",  //required 移入回收站时传值
	 * 			"flag"     : "(int)", //required 1 移入回收站 2 审批
	 * 			"params"   : {
	 * 				//审批申请时传递的参数 required
	 * 				"apply_id"     : "(int)", 
	 * 				"check_remark" : "(string)", 
	 * 				"check_amount" : "(string)", 
	 * 				"status"       : "(int)" //1 yes 2 no 
	 * 			}
	 * 		}
	 * }
	 */
	public function applyCreditStatus($entity, $parameters) 
	{
		self::init($entity, 'apply_credit');
		$flag = $parameters['flag'];
		if (!$flag) 
			failed_json('没有传参`flag`');
		if ($flag == 2) {
			$this->passApplyCredit($parameters['params']);
			return ;
		}
		$applyId = $parameters['apply_id'];
		if (!$applyId) 
			failed_json('没有传参`apply_id`');
		
		//所有apply_id
		$apply = array();
		if (is_array($applyId)) {
			$apply = implode(',', $applyId);
		} elseif (is_numeric($applyId)) {
			$apply = intval($applyId);
		} else {
			failed_json('传参错误');
		}
		
		//移入回收站
		$sql = 'UPDATE '.$this->table.' SET status=4 WHERE apply_id in('.$apply.')';
		$result = $this->db->query($sql);
		
		if ($result === false) 
			failed_json('操作失败');
		make_json_result($result);
	}
	
	
	/**
	 * 审批
	 * "params" : {
	 * 		//审批申请时传递的参数 required
	 *		"apply_id"     : "(int)",
	 * 		"check_remark" : "(string)",
	 *		"check_amount" : "(string)",
	 *		"status"       : "(int)" //1 yes 0 no
	 * }
	 */
	private function passApplyCredit($params) 
	{
		$applyId = $params['apply_id'];
		$remark  = $params['check_remark'];
		$amount  = $params['check_amount'];
		$status  = $params['status'];
		if (!$applyId or !$amount) 
			failed_json('传参错误');
		
		$status = ($status == 1) ? 2 : 3;
		
		//审批
		$fields = 'check_amount="'.$amount.'",check_remark="'.$remark.'",status="'.$status.'",check_time="'.date('Y-m-d H:i:s').'"';
		$sql = 'UPDATE '.$this->table.' SET '.$fields.' WHERE apply_id='.$applyId;
		$result = $this->db->query($sql);
		if ($result === false) {
			$this->db->rollback();
			failed_json('审批失败');
		}
		
		/* //获取合同id
		$this->selectSql(array(
				'fields' => 'contract_id', 
				'where'  => 'apply_id='.$applyId
		));
		$contractId = $this->db->getOne($this->sql);
		if (!$contractId) {
			failed_json('获取合同失败');
		}
		
		//增加合同的现金额度
		$this->table = 'contract';
		$sql = 'UPDATE '.$this->table.' SET cash_amount_valid=cash_amount_valid+'.$amount.' WHERE contract_id='.$contractId;
		$result = $this->db->query($sql);
		if ($result === false) {
			failed_json('设置额度失败');
		}
		 */
		
		make_json_result($result);
	}
}
$json = jsonAction($ApiList);
$cont = ApplyCredit::getIns();
$cont->run($json);