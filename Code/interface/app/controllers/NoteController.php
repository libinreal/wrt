<?php
/**
 * 我的票据
 * @author
 */
use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class NoteController extends ControllerBase 
{
	public function indexAction() 
	{
		$this->persistent->parameters = null;
	}
	
	
	/**
	 * 我的票据
	 * @return 
	 */
	public function getListAction() 
	{
		$userId = $this->get_user()->id;
		$size = $this->request->get('size', 'int') ?: parent::SIZE;
		$currentId = $this->request->get('bill_id', 'int') ?: 0;
		$forward = $this->request->get('forward', 'int');
		
		if (!$userId) {
			return ResponseApi::send('doesn\'t give `user_id`');
		}
		
		$condition = ''; //缺少user_id条件
		if ($forward || !$currentId) {
			//上一页 或 第一页操作
			if (!empty($condition)) $condition .= ' AND ';
			$condition .= 'bill_id>"'.$currentId.'"';
		} else {
			//下一页操作
			if (!empty($condition)) $condition .= ' AND ';
			$condition .= 'bill_id<"'.$currentId.'"';
		}
		
		$data = NoteModel::query()
				->where($condition)
				->order('bill_id DESC')
				->limit($size)
				->execute()
				->toArray();
		
		$billType = array('商业承兑汇票', '银行承兑汇票');
		$currency = array('人民币', '美元');
		foreach ($data as $k=>$v) 
		{
			if ($v['bill_type'] <= 1 || $v['currency'] <= 1) {
				$data[$k]['bill_type'] = $billType[$v['bill_type']];
				$data[$k]['currency']  = $currency[$v['currency']];
			}
			
		}
		
		return ResponseApi::send($data);
	}
	
	
	/**
	 * 票据详情
	 * @return 
	 */
	public function getSingleAction() 
	{
		error_reporting(E_ALL^E_NOTICE);
		$billId = $this->request->get('bill_id', 'int') ?: 0;
		$userId = $this->get_user()->id;
		
		if (!$billId || !$userId) {
			return ResponseApi::send('doesn\'t give `bill_id` or `user_id`');
		}
		
		$data = NoteModel::findFirst(array('bill_id='.$billId)); //缺少user_id条件
		if (!$data) {
			return ResponseApi::send(null, -1, '该票据不存在！');
		}
		
		$billType = array('商业承兑汇票', '银行承兑汇票');
		$currency = array('人民币', '美元');
		$status = array('已扣减', '已恢复');
		$isResource = array('否', '是');
		
		$data->bill_type   = $billType[$data->bill_type];
		$data->currency    = $currency[$data->currency];
		$data->status      = $status[$data->status];
		$data->is_recourse = $isResource[$data->is_recourse];
		
		$customerId = $data->customer_id;
		if ($customerId) {
			$user = Users::findFirst(array(
					'conditions' => 'id='.$customerId, 
					'columns'    => 'id,companyName'
			));
			$data->current_unit = $user->companyName;
		}
		
		if ($data->contract_id) {
			$contract = ContractModel::findFirst(array(
					'conditions' => 'contract_id='.$data->contract_id, 
					'columns'    => 'contract_id,contract_num'
			));
			$data->contract_num = $contract->contract_num;
		}
		
		
		return ResponseApi::send($data);
	}
}