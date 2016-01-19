<?php
use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class ApplyCreditController extends ControllerBase 
{
	public function indexAction() 
	{
		$this->persistent->parameters = null;
	}
	
	
	public function creditListAction() 
	{
		
	}
	
	
	
	/**
	 * 
	 */
	public function creditAddAction() 
	{
		$userId = $this->get_user()->id;
		$contractId = $this->request->get('contract_id');
		$applyAmount = $this->request->get('apply_amount');
		$applyRemark = $this->request->get('apply_remark');
		$applyImg    = $this->request->get('img');
		$createDate = date('Y-m-d H:i:s');
		
		$applyCreadit = new ApplyCreditController();
		$result = $applyCreadit->create = array(
				'user_id' => $userId, 
				'contract_id' => $contractId, 
				'apply_amount'=> $applyAmount, 
				'apply_remark'=> $applyRemark, 
				'img'         => $applyImg, 
				'status'      => 0, 
				'create_date' => $createDate
		);
		return ResponseApi::send($result);
	}
}