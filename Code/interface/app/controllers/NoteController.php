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
		$userinfo = $this->get_user();
		if (!$userinfo) return ResponseApi::send(null, -1, '未登录');
		
		$userId = $this->get_user()->id;
		$size = $this->request->get('size', 'int') ?: parent::SIZE;
		$currentId = $this->request->get('bill_id', 'int') ?: 0;
		$forward = $this->request->get('forward', 'int');
		
		if (!$userId) return ResponseApi::send(null, -1, '请登录用户');
		
		//搜索条件
		$billNum = $this->request->get('bill_num');
		$drawer = $this->request->get('drawer');
		$acceptor = $this->request->get('acceptor');
		$billStatus = $this->request->get('bill_status');
		$startTime = $this->request->get('start');
		$endTime = $this->request->get('end');
		
		//总账号及子帐号都显示总账号下的票据
		$user = Users::findFirst(array(
				'conditions' => 'id='.$userId,
				'columns' => 'id,parent_id'
		))->toArray();
		if ($user === false) return ResponseApi::send(null, -1, '查询用户失败');
		if (!$user) return ResponseApi::send(null, -1, '未获取到用户信息');
		if ($user['parent_id'] != 0) {
			$userId = $user['parent_id'];
		}
		
		if ($user) {
			$condition = 'NoteModel.customer_id='.$userId;
		} else {
			$condition = '';
		}
		
		if ($forward || !$currentId) {
			//上一页 或 第一页操作
			if (!empty($condition)) $condition .= ' AND ';
			$condition .= 'NoteModel.bill_id>"'.$currentId.'"';
		} else {
			//下一页操作
			if (!empty($condition)) $condition .= ' AND ';
			$condition .= 'NoteModel.bill_id<"'.$currentId.'"';
		}
		
		//搜索条件
		if ($billNum) {
			$condition .= ' AND NoteModel.bill_num LIKE "%'.$billNum.'%"';
		}
		if ($drawer) {
			$condition .= ' AND NoteModel.drawer LIKE "%'.$drawer.'%"';
		}
		if ($acceptor) {
			$condition .= ' AND NoteModel.acceptor LIKE "%'.$acceptor.'%"';
		}
		if ($billStatus >= 0 && isset($billStatus)) {
			$condition .= ' AND NoteModel.status='.$billStatus;
		}
		if ($startTime) {
			$condition .= ' AND UNIX_TIMESTAMP(NoteModel.issuing_date)>='.strtotime(substr($startTime, 0, 10));
		}
		if ($endTime) {
			$condition .= ' AND UNIX_TIMESTAMP(NoteModel.due_date)<='.strtotime(substr($endTime, 0, 10));
		}
		
		$data = NoteModel::query()
				->leftjoin('Users', 'U.id=NoteModel.customer_id', 'U')
				->where($condition)
				->columns('
						NoteModel.bill_id, 
						NoteModel.bill_num, 
						NoteModel.issuing_date, 
						NoteModel.due_date, 
						NoteModel.bill_amount, 
						U.companyName, 
						NoteModel.drawer, 
						NoteModel.acceptor, 
						IF(NoteModel.status=0,"未还","已还") status
					')
				->order('NoteModel.bill_id DESC')
				->limit($size)
				->execute()
				->toArray();
		
		return ResponseApi::send($data);
	}
	
	
	
	/**
	 * 票据详情 - 前台接口
	 */
	public function noteinfoAction() 
	{
		$userinfo = $this->get_user();
		if (!$userinfo) return ResponseApi::send(null, -1, '未登录');
		
		$userId = $this->get_user()->id;
		$billId = $this->request->get('bill_id', 'int') ?: 0;
		
		if (!$userId) return ResponseApi::send(null, -1, '请登录用户');
		
		if (!$billId) return ResponseApi::send(null, -1, '未获取到`bill_id`');
		
		
		//查询票据
		$data = NoteModel::findFirst(array(
				'conditions' => 'bill_id='.$billId, 
				'columns' => '
					bill_id, 
					bill_num, 
					IF(bill_type=0, "商业承兑汇票", "银行承兑汇票") bill_type, 
					IF(currency=0, "人民币", "美元") currency, 
					bill_amount, 
					customer_num, 
					contract_id, 
					payment_rate, 
					expire_amount, 
					issuing_date, 
					due_date, 
					prompt_day, 
					drawer, 
					acceptor, 
					accept_num, 
					accept_date, 
					remark, 
					customer_id, 
					receive_date, 
					trans_amount, 
					saler, 
					receiver, 
					balance, 
					discount_rate, 
					IF(status=0, "未还", "已还") status, 
					discount_amount, 
					IF(is_recourse=0, "否", "是") is_recourse, 
					pay_user_id, 
					pay_bank_id, 
					pay_account, 
					receive_user_id, 
					receive_bank_id, 
					receive_account
				'
		));
		if ($data === false) 
			return ResponseApi::send(null, -1, '查询失败');
		$data = $data->toArray();
		
		if (!$data) return ResponseApi::send(null, -1, '没有相关数据');
		
		//查询用户
		$users = array();
		
		if (!$data['customer_id']) $users[] = $data['customer_id'];
		if (!$data['pay_user_id']) $users[] = $data['pay_user_id'];
		if (!$data['receive_user_id']) $users[] = $data['receive_user_id'];
		
		if (!$users) 
		{
			$data['customer'] = '';
			$data['pay_user'] = '';
			$data['receive_user'] = '';
		} 
		else {
			$users = array_unique($users);
			$users = Users::find(array(
					'conditions' => 'id IN('.implode(',', $users).')',
					'columns' => 'id,account'
			));
			if ($users === false) return ResponseApi::send(null, -1, '查询用户失败');
			$users = $users->toArray();
			if (!$users) return ResponseApi::send(null, -1, '用户数据不正确');
			
			foreach ($users as $v) {
				if ($data['customer_id'] == $v['id']) {
					$data['customer'] = $v['account'];
				} elseif(!isset($data['customer'])) {
					$data['customer'] = '';
				}
			
				if ($data['pay_user_id'] == $v['id']) {
					$data['pay_user'] = $v['account'];
				} elseif (!isset($data['pay_user'])) {
					$data['pay_user'] = '';
				}
			
				if ($data['receive_user_id'] == $v['id']) {
					$data['receive_user'] = $v['account'];
				} elseif (!isset($data['receive_user'])) {
					$data['receive_user'] = '';
				}
			}
		}
		
		$banks = array();
		
		if ($data['pay_bank_id']) $banks[] = $data['pay_bank_id'];
		if ($data['receive_bank_id']) $banks[] = $data['receive_bank_id'];
		
		//票据信息不关联银行
		if (!$banks) 
		{
			$data['pay_bank'] = '';
			$data['receive_bank'] = '';
			return ResponseApi::send($data);
		}
		
		//查询银行
		$banks = array_unique($banks);
		$banks = Bank::find(array(
				'conditions' => 'bank_id IN('.implode(',', $banks).')',
				'columns' => 'bank_id,bank_name'
		));
		
		if ($banks === false) return ResponseApi::send(null, -1, '查询银行失败');
		$banks = $banks->toArray();
		if (!$banks) return ResponseApi::send(null, -1, '银行数据不正确');
		
		//绑定银行名称
		foreach ($banks as $v) {
			if ($data['pay_bank_id'] == $v['bank_id']) {
				$data['pay_bank'] = $v['bank_name'];
			} elseif (!isset($data['pay_bank'])) {
				$data['pay_bank'] = '';
			}
			
			if ($data['receive_bank_id'] == $v['bank_id']) {
				$data['receive_bank'] = $v['bank_name'];
			} elseif (!isset($data['receive_bank'])) {
				$data['receive_bank'] = '';
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
			return ResponseApi::send(null, -1, '票据不存在！');
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
					'conditions' => 'id='.$data->contract_id, 
					'columns'    => 'id,name'
			));
			$data->contract_num = $contract->contract_num;
		}
		
		
		return ResponseApi::send($data);
	}
}