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
	 */
	public function getListAction() 
	{
		$id = $this->request->get('id', 'int');
		$size = $this->request->get('size', 'int') ?: parent::SIZE;
		$currentId = $this->request->get('bill_id', 'int') ?: 0;
		$forward = $this->request->get('forward', 'int');
		
		if (!$id) {
			return ResponseApi::send('doesn\'t give `id`');
		}
		
		$condition = 'bill_type='.$id;
		if ($forward) {
			$condition .= ' AND (bill_id>"'.$currentId.'")';
		} else {
			$condition .= ' AND (bill_id<"'.$currentId.'")';
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
	 */
	public function findOne() 
	{
		$billId = $this->request->get('bill_id', 'int') ?: 0;
		
		if (!$billId) {
			return ResponseApi::send('doesn\'t give `bill_id`');
		}
		
		$data = NoteModel::query()
				->where('bill_id='.$billId)
				->execute()
				->toArray();
		
		return ResponseApi::send($data);
	}
}