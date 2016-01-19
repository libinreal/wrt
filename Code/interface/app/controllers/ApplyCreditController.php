<?php
use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class ApplyCreditController extends ControllerBase 
{
	public function indexAction() 
	{
		$this->persistent->parameters = null;
	}
	
	
	/**
	 * 授信列表
	 * parameters: 
	 * 		size     int required
	 * 		apply_id int required
	 * 		forward  int required
	 */
	public function creditListAction() 
	{
		$userId = $this->get_user()->id;
		
		$size = $this->request->get('size', 'int', 0);
		$applyId = $this->request->get('apply_id', 'int', 0);
		$forward = $this->request->get('forward', 'int');
		if (!$userId) {
			return ResponseApi::send(null, -1, 'login please');
		}
		
		$condition = 'status!=4';
		if ($forward or !$applyId) {
			if (!empty($condition)) $condition .= ' AND ';
			$condition .= 'apply_id>'.$applyId;
		} else {
			if (!empty($condition)) $condition .= ' AND ';
			$condition .= 'apply_id<'.$applyId;
		}
		
		$data = CreditModel::query()
				->where($condition)
				->order('apply_id DESC')
				->limit($size)
				->execute()
				->toArray();
		if ($data === false) {
			return ResponseApi::send(null, -1, 'error:select false');
		}
		$userId = array();
		$status = array('审核中', '审核通过', '审批通过', '审批失败');
		foreach ($data as $k=>$v) {
			$userId[] = $v['user_id'];
			$data[$k]['create_date'] = substr($v['create_date'], 0, 10);
			$data[$k]['status'] = $status[$v['status']];
		}
		$userId = array_unique($userId);
		$users = Users::query()
				->inWhere('id', $userId)
				->columns('id,account')
				->execute()
				->toArray();
		foreach ($users as $k=>$v) {
			foreach ($data as $dk=>$dv) {
				if ($dv['user_id'] == $v['id']) 
					$data[$dk]['account'] = $v['account'];
			}
		}
		return ResponseApi::send($data);
	}
	
	
	
	/**
	 * 申请平台授信
	 * parameters: 
	 * 		contract_id  int required
	 * 		apply_amount double required
	 * 		apply_remark varchar required
	 * 		img          varchar optional
	 * 		status 		 int optional
	 */
	public function creditAddAction() 
	{
		$userId      = $this->get_user()->id;
		$contractId  = $this->request->get('contract_id', 'int', 0);
		$applyAmount = $this->request->get('apply_amount', 'float', 0);
		$applyRemark = $this->request->get('apply_remark', 'string', 0);
		$applyImg    = $this->request->get('img', 'string', '');
		$status      = $this->request->get('status', 'int', 0);
		$createDate  = date('Y-m-d H:i:s');
		if (!$contractId or !$applyAmount or !$applyRemark) {
			return ResponseApi::send(null, -1, 'wrong parameter');
		}

		if ($status != 0 and $status != 1) {
			return ResponseApi::send(null, -1, 'wrong parameter `status` value');
		}
		
		$applyCredit = new CreditModel();
		$result = $applyCredit->create(array(
				'user_id' => $userId, 
				'contract_id' => $contractId, 
				'apply_amount'=> $applyAmount, 
				'apply_remark'=> $applyRemark, 
				'img'         => $applyImg, 
				'status'      => $status, 
				'create_date' => $createDate
		));
		if ($result === false) {
			return ResponseApi::send(null, -1, 'error:insert record');
		}
		return ResponseApi::send($result);
	}
	
	
	
	/**
	 * 修改申请授信状态
	 * parameters:
	 * 		apply_id int required
	 */
	public function updateStatusAction() 
	{
		$applyId = $this->request->get('apply_id', 'int', 0);
		if (!$applyId) {
			return ResponseApi::send(null, -1, 'wrong parameter');
		}
		
		$applyCredit = CreditModel::findFirst('apply_id='.$applyId);
		$applyCredit->status = 1;
		$result = $applyCredit->update();
		
		if ($result === false) {
			return ResponseApi::send(null, -1, 'error:update status');
		}
		return ResponseApi::send($result);
	}
	
	
	/**
	 * 授信申请详情
	 * parameters:
	 * 		apply_id int required
	 */
	public function creditSingleAction() 
	{
		$applyId = $this->request->get('apply_id', 'int', 0);
		if (!$applyId) {
			return ResponseApi::send(null, -1, 'wrong parameter');
		}
		
		$applyCredit = CreditModel::findFirst($applyId)->toArray();
		if ($applyCredit === false) {
			return ResponseApi::send(null, -1, 'error:select apply credit false');
		}
		
		$contract = ContractModel::findFirst(array(
				'conditions' => 'contract_id='.$applyCredit['contract_id'], 
				'columns'    => 'contract_id,contract_name'
		))->toArray();
		if ($contract === false) {
			return ResponseApi::send(null, -1, 'error:select contract false');
		}
		
		$users = Users::findFirst(array(
				'conditions' => 'id='.$applyCredit['user_id'], 
				'columns'    => 'id,account,companyName'
		))->toArray();
		if ($users === false) {
			return ResponseApi::send(null, -1, 'error:select user false');
		}
		$result = array_merge($applyCredit, $contract, $users);
		if ($result === false) {
			return ResponseApi::send(null, -1, 'error:wrong data');
		}
		$status = array('审核中', '审核通过', '审批通过', '审批失败');
		$result['create_date'] = substr($result['create_date'], 0, 10);
		$result['status'] = $status[$result['status']];
		return ResponseApi::send($result);
	}
	
	/**
	 * 合同列表
	 */
	public function contractListAction() 
	{
		$userId = $this->get_user()->id;
		
		$data = ContractModel::query()
				->where('customer_id='.$userId)
				->columns('contract_id,contract_name')
				->execute()
				->toArray();
		if ($data === false) {
			return ResponseApi::send(null, -1, 'error:select contract false');
		}
		return ResponseApi::send($data);
	}
}