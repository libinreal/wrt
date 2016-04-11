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
	 * 			"flag" : "(int)", //0平台授信回收站 1平台授信列表
	 * 			"params" : {
	 * 				"where" : {
	 * 					"like" : {
	 * 						"user_name"     : "(string)", 
	 * 						"contract_name" : "(string)"
	 * 					},
	 * 					"state"     : "(int)",
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
		
		//0回收站列表 	1列表
		$flag = $parameters['flag'];
		
		//分页参数
		$params = $parameters['params'];
		$limit = $params['limit'] < 0 ? 0 : $params['limit'];
		$offset = $params['offset'] < 0 ? 0 : $params['offset'];
		if (!$offset) return make_json_result(array()); 
		
		//筛选条件
		$conditions = $params['where'];
		$state = $conditions['state'];
		$start = $conditions['start_time'];
		$end = $conditions['end_time'];
		
		//模糊查询条件
		$like = $conditions['like'];
		$username = $like['user_name'];
		$name = $like['contract_name'];
		
		//搜索
		if ($flag) {
			$where = 'ac.status!=4 ';
			if ($state) $where = 'ac.status='.$state.' ';
		} else {
			$where = 'ac.status=4 ';
		}
		
		if ($username) $where .= 'AND u.user_name LIKE "%'.$username.'%" ';
		if ($name) $where .= 'AND c.contract_name LIKE "%'.$name.'%" ';
		if ($start) $where .= 'AND ac.create_date>="'.date('Y-m-d H:i:s', strtotime($start)).'" ';
		if ($end) $where .= 'AND ac.create_date<="'.date('Y-m-d H:i:s', strtotime($end)).'" ';
		
		//分页
		$limit = 'LIMIT '.$limit.','.$offset;
		
		//查询
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
				'extend' => 'ORDER BY apply_id ASC '.$limit
		));
		$data = $this->db->getAll($this->sql);
		if ($data === false) return failed_json('获取列表失败');
		
		//总记录数
		self::selectSql(array(
				'fields' => 'count(ac.apply_id) AS num', 
				'as'     => 'ac', 
				'join'   => 'LEFT JOIN users AS u on ac.user_id=u.user_id'
							.' LEFT JOIN contract AS c on ac.contract_id=c.contract_id', 
				'where'  => $where
		));
		$total = $this->db->getOne($this->sql);
		if ($total === false) return failed_json('获取总记录数失败');
		
		$status = array('审核中', '已审核', '审批通过', '审批失败', '已删除');
		foreach ($data as $k=>$v) {
			$data[$k]['status'] = $status[$v['status']];

			$data[$k]['upload_url'] =  '/' . DATA_DIR . '/' . APPLY_CREDIT_DIR . '/' .  $f['upload_name'];
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
		
		$userId = $_SESSION['admin_id'];
		if (!$userId) failed_json('未获取到当前登录用户');
		$this->table = 'users';
		self::selectSql(array(
				'fields' => 'user_name', 
				'where'  => 'user_id='.$userId
		));
		$userName = $this->db->getOne($this->sql);
		if ($userName === false) return failed_json('获取用户名失败');
		if (!$userName) return failed_json('审批失败');
		
		//审批
		$this->table = 'apply_credit';
		$fields = 'check_amount="'.$amount.'",check_remark="'.$remark.'",status="'.$status.'",check_time="'.date('Y-m-d H:i:s').'",check_user="'.$userId.'",check_name="'.$userName.'"';
		$sql = 'UPDATE '.$this->table.' SET '.$fields.' WHERE apply_id='.$applyId;
		$result = $this->db->query($sql);
		if ($result === false) {
			$this->db->rollback();
			failed_json('审批失败');
		}
		
		//获取合同id
		$this->selectSql(array(
				'fields' => array( 'contract_id','user_id' ), 
				'where'  => 'apply_id='.$applyId
		));
		$contract_data = $this->db->getRow($this->sql);
		if (!$contract_data) {
			failed_json('获取合同失败');
		}

		$contractId = $contract_data['contract_id'];
		$user_id = $contract_data['user_id'];
		
		//增加合同的现金额度
		$sql = 'UPDATE '. $GLOBALS['ecs']->table('contract') . ' SET cash_amount_history=cash_amount_history+' . $amount . ',cash_amount_valid=cash_amount_valid+'.$amount.' WHERE contract_id='.$contractId;
		$result = $this->db->query($sql);
		if ($result === false) {
			failed_json('设置额度失败');
		}
		
		//总账号的id
		$sql ='SELECT `parent_id` FROM ' . $GLOBALS['ecs']->table('users') . ' WHERE `user_id` = ' . $user_id;
		$parentId = $this->db->getOne($sql);

		$uid_arr = array( $user_id );
		if($parentId){//总账号同步增加现金额度
			$uid_arr = array($user_id, $parentId);
		}
		$uids = implode(',', $uid_arr);

		//增加帐号的现金额度
		$sql = 'UPDATE '. $GLOBALS['ecs']->table('users') . ' SET cash_amount_history=cash_amount_history+' . $amount . ',cash_amount_valid=cash_amount_valid+'.$amount.' WHERE user_id IN('.$uids.') ' .
				' LIMIT 2';
		
		$result = $this->db->query($sql);
		if ($result === false) {
			failed_json('设置额度失败');
		}	
		
		make_json_response($result, 0, '审核成功');
	}
}
$json = jsonAction($ApiList);
$cont = ApplyCredit::getIns();
$cont->run($json);