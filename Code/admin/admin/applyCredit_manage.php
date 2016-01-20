<?php
/**
 * 平台自有授信
 * @author
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once('ManageModel.php');


$ApiList = array(
		'applyCreditSingle', 
		'applyCreditStatus'
);

class ApplyCredit extends ManageModel 
{
	protected static $_instance;
	
	protected $table;
	protected $db;
	protected $sql;
	
	
	/**
	 * 自有授信详情
	 * {
	 * 		command : 'applyCreditSingle', 
	 * 		entity  : 'apply_credit', 
	 * 		parameters : {
	 * 			"apply_id" : "(int)" // required
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
		//授信详情
		self::selectSql(array(
				'fields' => '*', 
				'where'  => 'apply_id='.$applyId.' AND status!=4'
		));
		$data = $this->db->getRow($this->sql);
		if ($data === false) 
			failed_json('获取列表失败');
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
		make_json_result($data);
	}
	
	
	/**
	 * 修改自有授信状态
	 * {
	 * 		command : 'applyCreditStatus', 
	 * 		entity  : 'apply_credit', 
	 * 		parameters : {
	 * 			"apply_id" : "(int or array)",  //required
	 * 			"flag"     : "(int)", //required 1 移入回收站 2 审批
	 * 			params     : {
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
	 * params : {
	 * 		//审批申请时传递的参数 required
	 *		"apply_id"     : "(int)",
	 * 		"check_remark" : "(string)",
	 *		"check_amount" : "(string)",
	 *		"status"       : "(int)" //1 yes 2 no
	 * }
	 */
	private function passApplyCredit($params) 
	{
		$applyId = $params['apply_id'];
		$remark  = $params['check_remark'];
		$amount  = $params['check_amount'];
		$status  = $params['status'];
		if (!$applyId or !$amount or !$status) 
			failed_json('传参错误');
		
		$status = ($status == 1) ? 2 : 3;
		
		$fields = 'check_amount="'.$amount.'",check_remark="'.$remark.'",status="'.$status.'"';
		$sql = 'UPDATE '.$this->table.' SET '.$fields.' WHERE apply_id='.$applyId;
		$result = $this->db->query($sql);
		if ($result === false) 
			failed_json('审批失败');
		make_json_result($result);
	}
}
$json = jsonAction($ApiList);
$cont = ApplyCredit::getIns();
$cont->run($json);