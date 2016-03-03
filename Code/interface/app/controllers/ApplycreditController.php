<?php
use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class ApplycreditController extends ControllerBase 
{
	public function indexAction() 
	{
		$this->persistent->parameters = null;
	}
	
	/**
	 * 上传申请附件
	 */
	public function uploadAttachAction() {
		if ($this->request->hasFiles() == true) {
			$dirAttach = getcwd().'/apply_attachment';
			if (!is_dir($dirAttach)) {
				if (!mkdir($dirAttach, 0777)) {
					return ResponseApi::send(null, -1, '`apply_attachment`目录创建失败');
				}
			}
			if (!is_writable($dirAttach)) {
				return ResponseApi::send(null, -1, '`apply_attachment`目录没有写的权限');
			}
			
			
			$opath = "";
			$filepath = date("Ym");
			$storedir = $dirAttach.'/'.$filepath;
			
			if(!is_dir($storedir)) {
				if(!mkdir($storedir, 0777)) {
					return ResponseApi::send(null, Message::$_ERROR_SYSTEM, '目录创建失败');
				}
			}
			
			if(!is_writable($storedir)) {
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, '目录没有写的权限');
			}
	
			$files = array();
			// Print the real file names and sizes
			foreach ($this->request->getUploadedFiles() as $file) {
				//Move the file into the application
				$fileType = '';
				if ($fileType == null) {
					$fileType= $file->getType();
				}
				$tmp = explode("/", $fileType);
				if (is_array($tmp)) {
					$fileType = $tmp[count($tmp)-1];
				}
	
				if ($fileType != null) {
					$filename = time().\PhpRudder\CommonUtil::random(2, '123456789').".".$fileType;
				} else {
					$filename = time().\PhpRudder\CommonUtil::random(8, '123456789');
				};
				array_push($files, $opath.$filename);
	
				$file->moveTo($storedir."/".$filename);
			}
			
			$files = array_map(function($file) {
				
				return $file;
			}, $files);
			foreach ($files as $k=>$v) {
				$files[$k] = $filepath.'/'.$v;
			}
				return ResponseApi::send($files);
		}
		return ResponseApi::send(null, Message::$_ERROR_CODING, "no upload file");
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
		if (!$userId) return ResponseApi::send(null, -1, '请登录');
		
		//分页参数
		$size = $this->request->get('size', 'int', 0);
		$applyId = $this->request->get('apply_id', 'int', 0);
		$forward = $this->request->get('forward', 'int');
		
		//搜索参数
		$applyUser = $this->request->get('apply_user');
		$contractId = $this->request->get('contract_id');
		$applyStatus = $this->request->get('apply_status');
		$startTime = $this->request->get('start');
		$endTime = $this->request->get('end');
		
		
		//总账号登录，显示总账号及其子帐号的申请授信信息
		$user = Users::find(array(
				'conditions' => 'parent_id = '.$userId.' or id='.$userId, 
				'columns' => 'id'
		))->toArray();
		foreach ($user as &$id) {
			$id = $id['id'];
		}
		
		if (!$user) return ResponseApi::send(null, -1, '未获取到用户信息');
		
		//数据顺序
		if ($forward or !$applyId) {
			$condition = 'CreditModel.apply_id>'.$applyId;
		} else {
			$condition = 'CreditModel.apply_id<'.$applyId;
		}
		
		//搜索条件
		if ($applyUser) $condition .= ' AND U.account LIKE "%'.$applyUser.'%"';
		if ($contractId) $condition .= ' AND CreditModel.contract_id='.$contractId;
		if ($applyStatus >= 0 && isset($applyStatus)) $condition .= ' AND CreditModel.status='.$applyStatus;
		if ($startTime) $condition .= ' AND CreditModel.create_date>="'.$startTime.'"';
		if ($endTime) $condition .= ' AND CreditModel.check_time<="'.$endTime.'"';
		
		//查询平台授信信息
		$data = CreditModel::query()
				->leftjoin('Users', 'U.id=CreditModel.user_id', 'U')
				->where($condition)
				->andWhere('CreditModel.status!=4')
				->andWhere('CreditModel.user_id IN ('.implode(',', $user).')')
				->columns('
						CreditModel.apply_id, 
						CreditModel.contract_id, 
						CreditModel.apply_amount, 
						CreditModel.apply_remark, 
						CreditModel.check_amount, 
						CreditModel.check_remark, 
						CreditModel.status, 
						DATE_FORMAT(CreditModel.create_date, "%Y-%m-%d") create_date, 
						DATE_FORMAT(CreditModel.check_time, "%Y-%m-%d") check_time, 
						U.id, 
						U.account
					')
				->order('CreditModel.apply_id DESC')
				->limit($size)
				->execute()
				->toArray();
		if ($data === false) {
			return ResponseApi::send(null, -1, '查询失败');
		}
		
		if (!$data) return ResponseApi::send(array());
		
		$status = array('审核中', '审批中', '审批通过', '审批失败');
		foreach ($data as &$credit) {
			$credit['status'] = $status[$credit['status']];
		}
		
		return ResponseApi::send($data);
	}
	
	
	
	/**
	 * 授信申请详情
	 */
	public function creditSingleAction() 
	{
		$applyId = $this->request->get('apply_id', 'int', 0);
		if (!$applyId) {
			return ResponseApi::send(null, -1, '未获取到`apply_id`');
		}
		
		//查询自有授信
		$applyCredit = CreditModel::findFirst($applyId)->toArray();
		if ($applyCredit === false) {
			return ResponseApi::send(null, -1, '查询失败');
		}
		if (!$applyCredit) return ResponseApi::send(array());
	
		//查询合同
		$contract = ContractModel::findFirst(array(
				'conditions' => 'id='.$applyCredit['contract_id'],
				'columns'    => 'id AS contract_id,name AS contract_name'
		))->toArray();
		if ($contract === false) {
			return ResponseApi::send(null, -1, '查询合同失败');
		}
	
		//查询用户信息
		$users = Users::findFirst(array(
				'conditions' => 'id='.$applyCredit['user_id'],
				'columns'    => 'id,account,companyName'
		))->toArray();
		if ($users === false) {
			return ResponseApi::send(null, -1, '查询用户失败');
		}
		$result = array_merge($applyCredit, $contract, $users);
		if ($result === false) {
			return ResponseApi::send(null, -1, '查询信息失败');
		}
	
		$action = ($result['status'] == 0) ? 1 : 0;
		$status = array('审核中', '审批中', '审批通过', '审批失败');
		$result['create_date'] = substr($result['create_date'], 0, 10);
		$result['status'] = $status[$result['status']];
		$result['action'] = $action;
		return ResponseApi::send($result);
	}
	
	
	
	/**
	 * 申请平台自有授信，或修改
	 */
	public function saveCreditAction() 
	{
		$userId = $this->get_user()->id;
		$action = $this->request->get('act', 'int', 0); //修改时传1
		$applyId = $this->request->get('apply_id', 'int', 0); //修改时传apply_id
		
		$applyAmount = $this->request->get('apply_amount', 'float', 0);
		$applyRemark = $this->request->get('apply_remark', 'string', 0);
		$contractId = $this->request->get('contract_id', 'int', 0);
		$applyImg = $this->request->get('apply_img', 'string', '');
		
		if (!$userId) return ResponseApi::send(null, -1, '请登录用户');
		
		if (!$applyAmount or !$contractId or !$applyImg) {
			return ResponseApi::send(null, -1, '参数不全');
		}
		
		$applyCredit = new CreditModel();
		
		if (!$action) {
			//添加操作
			$status = 0;
			//当前账号为主账号时，status=1
			$users = Users::findFirst(array(
					'conditions' => 'id='.$userId,
					'columns'    => 'parent_id'
			))->toArray();
			if ($users['parent_id'] == 0) {
				$status = 1;
			}
			
			//申请自有授信
			$result = $applyCredit->create(array(
					'user_id' => $userId,
					'contract_id' => $contractId,
					'apply_amount'=> $applyAmount,
					'apply_remark'=> $applyRemark,
					'img'         => $applyImg,
					'status'      => $status,
					'create_date' => date('Y-m-d H:i:s')
			));
			if ($result === false) {
				return ResponseApi::send(null, -1, '申请失败');
			}
			return ResponseApi::send($result);
		}
		
		//修改操作
		if (!$applyId) return ResponseApi::send(null, -1, '参数错误`apply_id`');
		$credit = $applyCredit::findFirst($applyId);
		
		$data = $credit->toArray();
		if ($data['status'] != 0) {
			return ResponseApi::send(null, -1, '审核通过，正在审批中，请勿修改信息');
		}
		$credit->contract_id = $contractId;
		$credit->apply_amount = $applyAmount;
		$credit->img = $applyImg;
		if ($applyRemark) $credit->apply_remark = $applyRemark;
		
		if ($credit->update() === false) {
			return ResponseApi::send(null, -1, '修改失败');
		}
		return ResponseApi::send(true);
	}
	
	
	
	/**
	 * 修改申请授信状态
	 */
	public function updateStatusAction() 
	{
		$applyId = $this->request->get('apply_id', 'int', 0);
		if (!$applyId) {
			return ResponseApi::send(null, -1, '错误传参`apply_id`');
		}
		
		$applyCredit = CreditModel::findFirst('apply_id='.$applyId);
		$applyCredit->status = 1;
		$result = $applyCredit->update();
		
		if ($result === false) {
			return ResponseApi::send(null, -1, '修改失败');
		}
		return ResponseApi::send($result);
	}
	
	
	
	
	/**
	 * 合同列表
	 */
	public function contractListAction() 
	{
		$userId = $this->get_user()->id;
		
		$data = ContractModel::query()
				->where('userId='.$userId)
				->orWhere('customerId='.$userId)
				->columns('id AS contract_id,name AS contract_name')
				->execute()
				->toArray();
		if ($data === false) return ResponseApi::send(null, -1, '查询失败');
		if (!$data) return ResponseApi::send(array());
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
	
		//查看当前用户是否为总账号
		$users = Users::findFirst(array(
				'conditions' => 'id='.$userId,
				'columns'    => 'parent_id'
		))->toArray();
		if ($users['parent_id'] == 0) {
			$status = 1;
		}
	
		//申请
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
}