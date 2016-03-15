<?php

use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;
use PhpRudder\CommonUtil;

class CreditController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * 提交信用申请表单
     * XYC-01
     */
    public function createEvaluationAction()
    {
        if (!$this->request->isPost()) {
            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "只支持POST请求");
        }
        $userId = $this->get_user()->id;
        $credit = CreditEvaluationInfo::findFirst(array(
            'columns' => array('id','status'),
            'conditions' => "userId = :userId:
	            AND createAt>:createAt:",
            'bind' => array("userId" =>$userId, 'createAt' => strtotime('-1 month')),
        ));
        if ($credit) {
            if ($credit->status != CreditEvaluationInfo::STATUS_4 && $credit->status != CreditEvaluationInfo::STATUS_5) {
                return ResponseApi::send(null, Message::$_ERROR_SYSTEM,"您上次的申请尚未结束评测！");
            } else {
                return ResponseApi::send(null, Message::$_ERROR_SYSTEM,"您的申请距离上次申请不满一月，不能再次申请！");
            }
        }

        $credit_evaluation_info = new CreditEvaluationInfo();
        //注册资本
        $money = $this->request->getPost("money","int");
        if (!$money || $money<=0) {
            return ResponseApi::send("",Message::$_ERROR_LOGIC,"请输入注册资本。");
        }
        $credit_evaluation_info->money = $money;
        //公司成立时间
        $credit_evaluation_info->foundedDate = $this->request->getPost("foundedDate");
        //公司性质
        $credit_evaluation_info->nature = $this->request->getPost("nature");
        //拟授信额度
        $credit_evaluation_info->amountLimit = $this->request->getPost("amountLimit");
        //拟授信用途
        $credit_evaluation_info->use = $this->request->getPost("use");
        //营业执照编码
        $credit_evaluation_info->businessCode = $this->request->getPost("businessCode");
        //税务登记证编码
        $credit_evaluation_info->taxCode = $this->request->getPost("taxcode");
        //组织机构代码
        $credit_evaluation_info->orgCode = $this->request->getPost("orgcode");
        //营业执照副本
        $credit_evaluation_info->businessLicense = str_replace($this->get_url(), '', $this->request->getPost("businessLicense"));
        //税务登记证副本
        $credit_evaluation_info->taxcert = str_replace($this->get_url(), '', $this->request->getPost("taxcert"));
        //组织机构代码证副本
        $credit_evaluation_info->orgcert = str_replace($this->get_url(), '', $this->request->getPost("orgcert"));
        //状态
        $credit_evaluation_info->status = CreditEvaluationInfo::STATUS_1;
        //创建时间
        $credit_evaluation_info->createAt = time();
        $credit_evaluation_info->userId = $userId;

        if(!$credit_evaluation_info->save()) {
            foreach($credit_evaluation_info->getMessages() as $message) {
                return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $message);
            }
        } else {
	        $this->sendSmsNotice($this->get_user()->telephone);
            return ResponseApi::send(null, Message::$_OK, "您的资料已提交，预计7天后进行受理。");
        }
    }

    /**
     * XYC-02
     * 获得信用申请表单及处理状态
     */
    public function getEvaluationInfoAction()
    {
        $id = $this->request->get('id', 'int');
        if(!$id) {
            return ResponseApi::send(null, Message::$_ERROR_CODING, "数据格式不合法");
        }

        $userId = $this->get_user()->id;
        $result = CreditEvaluationInfo::findFirst(array(
            'conditions' => 'id = :id: AND userId = :userId:',
            'bind' => compact('id', 'userId'),
        	'columns' => array(
        			'money',
        			'foundedDate',
        			'nature',
        			'amountLimit',
        			'use',
        			'businessCode',
        			'taxCode',
        			'orgCode',
        			'IF(businessLicense LIKE "http://%", businessLicense, CONCAT("' . $this->get_url() . '", businessLicense)) businessLicense',
        			'IF(taxcert LIKE "http://%", taxcert, CONCAT("' . $this->get_url() . '", taxcert)) taxcert',
        			'IF(orgcert LIKE "http://%", orgcert, CONCAT("' . $this->get_url() . '", orgcert)) orgcert',
        			'status',
        			'remark'
        	)
        ));

        if(!$result) {
        	return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "资源请求错误，试图获取已经过期或则并不存在的资源.");
        }
        $resultArr = array();
        if ($result) {
            $resultArr = CommonUtil::object2Array($result);
        }
        unset($resultArr['id']);
        unset($resultArr['userId']);
        unset($resultArr['createAt']);
        return ResponseApi::send($resultArr);
    }

    /**
     * 查看信用申请表单记录 XYC-03
     */
    public function getEvaluationlistAction()
    {
        $userId = $this->get_user()->id;
        $result = CreditEvaluationInfo::find(array(
            'conditions' => 'userId=:userId:',
            'bind' => compact('userId'),
            'columns' => array('id', 'status', 'createAt'),
            'order' => 'createAt DESC',
        ));
        $credits = array();
        if(is_object($result) && $result) {
            $credits = $result->toArray();
        }
        return ResponseApi::send($credits);
    }

    /**
     * 我的信用B（需要调用金蝶接口) XYC-04
     */
    public function mycreamtAction() {
        $this->dispatcher->forward(array(
            "controller" => "api",
            "action" => "getcreamt"
        ));
    }
    
    
    /**
     * 个人中心第一部分信息
     */
    public function userinfoAction() 
    {
    	$userId = $this->get_user()->id;
    	$userId = $userId ? $userId : $this->request->getPost('user_id');
    	if (!$userId) {
    		return ResponseApi::send(null, -1, 'does not get `user_id`');
    	}
    	/* 
    	//用户合同
    	$valid = ContractModel::find(array(
    			'conditions' => 'userId='.$userId, 
    			'columns' => 'billValid,cashValid'
    	))->toArray();
    	
    	//用户合同的总采购额度和现金额度
    	$billValid = 0;$cashValid = 0;
    	foreach ($valid as $v) {
    		$billValid += $v['billValid'];
    		$cashValid += $v['cashValid'];
    	}
    	 */
    	//查询用户信息
    	$data = Users::findFirst(array(
    			'conditions' => 'id='.$userId, 
    			'columns' => 'id,account,companyName,companyAddress,billAmountValid,cashAmountValid'
    	))->toArray();
    	/* 
    	$data['billAmountValid'] = $billValid;
    	$data['cashAmountValid'] = $cashValid;
    	 */
    	return ResponseApi::send($data);
    }

    /**
     * 追加信用额度申请 XYC-05
     */
    public function applyxyedAction() {
        if (!$this->request->isPost()) {
            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, '只支持POST请求');
        }
        $userId = $this->get_user()->id;
        $apply = new Apply();

        $apply->contractNo = $this->request->getPost('contractNo');
        $apply->name = $this->request->getPost('name');
        $apply->phone = $this->request->getPost('phone');
        $apply->amount = $this->request->getPost('amount', 'int');
        if ($apply->amount <= 0) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "请输入申请的信用额度。");
        }
        $apply->reason = $this->request->getPost("reason");

        $api = new ApiController();

        $cmt = $api->getCreAmt();
        $apply->curamt = $cmt;

        $apply->type = Apply::type_0;
        $apply->userId = $userId;
        if(!$apply->save()) {
        	foreach($apply->getMessages() as $message) {
	            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $message);
        	}
        }
        return ResponseApi::send();
    }

    /**
     * 申请采购额度 XYC-06
     */
    public function applycgedAction() {
        if (!$this->request->isPost()) {
            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "只支持POST请求");
        }
        $user = $this->get_user();
        $apply = new Apply();

        $apply->contractNo = $this->request->getPost("contractNo");
        $apply->name = $this->request->getPost("name");
        $apply->phone = $this->request->getPost("phone");
        $apply->amount = $this->request->getPost("amount","int");
        if (!$apply->amount || $apply->amount <=0) {
            return ResponseApi::send("",Message::$_ERROR_LOGIC,"请输入申请的采购额度。");
        }
        $apply->reason = $this->request->getPost("reason");
        $apply->type = Apply::type_1;
        $apply->status = 0;


        $api = new ApiController();
        $cmt = $api->getPurchase();
        $apply->curamt = $cmt;

        //创建时间
        $apply->createAt = time();
        $apply->userId = $user->id;

        if(!$apply->save()) {
        	foreach($apply->getMessages() as $message) {
	            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $message);
        	}
        }
        return ResponseApi::send();
    }

    /**
     * XYC-07
     * 获取信用恢复历史记录
     * //TODO 是否要显示客户名称和所在公司。
     */
    public function getRestoreHistoryAction() {
        $user = $this->get_user();
        $chanKind = $this->request->get("chanKind");
        $timeflag = $this->request->get("chanDateEnum");

        if ($chanKind && ($chanKind>1 || $chanKind<0)) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
        }

        $conditions = "cusFnum = :cusFnum:";
        $bind = array("cusFnum" => $user->customNo);

        if ($chanKind != null &&  ($chanKind == 0 || $chanKind == 1)) {
            $conditions = $conditions." and chanKind=:chanKind:";
            $bind["chanKind"] = $chanKind;
        }

        //一个月
        if ($timeflag == 'm1') {
        	$bind['start'] = strtotime('-1 month');
        	$bind['end'] = time();
        	$conditions = $conditions." and UNIX_TIMESTAMP(chanDate)>=:start: and UNIX_TIMESTAMP(chanDate) <= :end:";
        }
        //三个月
        else if ($timeflag == 'm3') {
        	$bind['start'] = strtotime('-3 month');
        	$bind['end'] = time();
        	$conditions = $conditions." and UNIX_TIMESTAMP(chanDate)>=:start: and UNIX_TIMESTAMP(chanDate) <= :end:";
        }
        //半年
        else if ($timeflag == 'm6') {
        	$bind['start'] = strtotime('-6 month');
        	$bind['end'] = time();
        	$conditions = $conditions." and UNIX_TIMESTAMP(chanDate)>=:start: and UNIX_TIMESTAMP(chanDate) <= :end:";
        }
        //一年
        else if ($timeflag == 'y1') {
        	$bind['start'] = strtotime('-1 year');
        	$bind['end'] = time();
        	$conditions = $conditions." and UNIX_TIMESTAMP(chanDate)>=:start: and UNIX_TIMESTAMP(chanDate) <= :end:";
        }

        $result = CreditChangLog::find(array(
            'columns' => array('id', 'remark', 'chanAmt', 'chanKind','bankName', 'bankFnum', 'billNO', 'cusFnum', 'UNIX_TIMESTAMP(chanDate) chanDate'),
            'conditions' => $conditions,
            'bind' => $bind,
            'order' => 'chanDate DESC',
        ));
        $histrylogs = array();
        if(is_object($result) && $result) {
            $histrylogs = $result->toArray();
        }
        $bankNames = array('01' => '浙商银行',
			'02' => '工商银行',
			'03' => '招商银行',
			'04' => '浦发银行',
			'05' => '中信银行',
			'06' => '中交银行',
			'07' => '华夏银行'
        );
        foreach ($histrylogs as $key=>$value){
        	$histrylogs[$key]['bankName'] = $bankNames[$value['bankFnum']];
        	$histrylogs[$key]['cusName'] = $user->account ;
        	$histrylogs[$key]['company'] = $user->companyName ;
        }
        return ResponseApi::send($histrylogs);
    }

    /**
     * XYC-08
     * 得到票据到期提醒
     */
    public function getBillNoticeAction() {
        $user = $this->get_user();
        $timeflag = $this->request->get("time");

        $conditions = "cusFnum = :cusFnum:";
        $bind = array("cusFnum" =>$user->customNo);
        $datearr = array('d3'=>'3天后', 'd5'=>'5天后', 'd15'=>'15天后', 'inm'=>'1个月内', 'outm'=>'1个月后');
        $msg = '';
        //3天到期
        if ($timeflag == 'd3') {
            $bind['start'] = time();
            $bind['end'] = strtotime('3 day');
            $conditions = $conditions." AND billEndDate >= :start: AND billEndDate <= :end:";
            $msg = $datearr[$timeflag];
        }
        //5天到期
        else if ($timeflag == 'd5') {
            $bind['start'] = time();
            $bind['end'] = strtotime('5 day');
            $conditions = $conditions." AND billEndDate>=:start: AND billEndDate <= :end:";
            $msg = $datearr[$timeflag];
        }
        //15天到期
        else if ($timeflag == 'd15') {
            $bind['start'] = time();
            $bind['end'] = strtotime('15 day');
            $conditions = $conditions."AND billEndDate>=:start: AND billEndDate <= :end:";
            $msg = $datearr[$timeflag];
        }
        //一个月内到期
        else if ($timeflag == 'inm') {
            $bind['start'] = time();
            $bind['end'] = strtotime('1 month');
            $conditions = $conditions."AND billEndDate>=:start: and billEndDate <= :end:";
            $msg = $datearr[$timeflag];
        }
        //一个月后到期
        else if ($timeflag == 'outm') {
        	$bind['start'] = strtotime('1 month');
            $conditions = $conditions."AND billEndDate>=:start:";
            $msg = $datearr[$timeflag];
        }

        $result = BillNotice::find(array(
            'columns' => array('id', 'billNO', 'billAmt', 'billEndDate'),
            'conditions' => $conditions,
            'bind' => $bind,
            'order' => 'billEndDate DESC',
        ));
        $histrylogs = array();
        if(is_object($result) && $result) {
            $histrylogs = $result->toArray();
            $notice = array();
            $notice['noticelab'] = "您有".count($histrylogs)."张票据即将".$msg.'到期！';
            $notice['noticeList'] = $histrylogs;
        }
        return ResponseApi::send($notice);
    }

    /**
     * XYC-09
     * 查看采购额度（调用金蝶接口）//TODO
     */
    public function getcgedAction() {
		$this->dispatcher->forward(array(
			'controller' => 'api',
			'action' => 'getpurchase'
		));
    }

    /**
     * 在线申请 XYC-10
     */
    public function getAllApplyAction() {
    	$size = $this->request->get('size', 'int');
    	if(!$size) {
			$size = parent::SIZE;
    	}
		$apply = new Apply();
		$sqlStatement = 'SELECT
						    RPAD(SUBSTRING(u.customNo, 1, 3), CHAR_LENGTH(u.customNo), "*") customNo, t.amount addamt, t.curamt, t.type
						FROM
						    (SELECT
						        l . *, r.id rid
						    FROM
						        apply l
						    LEFT JOIN apply r ON l.type = r.type AND l.id < r.id
						    ORDER BY l.type ASC , l.createAt DESC) t
						        LEFT JOIN
						    users u ON t.user_id = u.user_id
						GROUP BY t.type , t.id
						HAVING COUNT(t.rid) < '.$size.'
						ORDER BY t.type , t.createAt DESC';
		$result = $apply->getReadConnection()->query($sqlStatement);
		if($result->numRows()) {
			$result->setFetchMode(PDO::FETCH_ASSOC);
			$applys = array();
			$applyType = array('creamt', 'buyamt');
			while(($r = $result->fetch()) != false) {
				$applys[$applyType[array_pop($r)]][] = $r;
			}
		}
		return ResponseApi::send($applys);
    }

    /**
     * XYC-11
     */
    public function getintrInfoAction() {
        $result = CreditIntrinfo::findFirst();
        if ($result) {
        	return ResponseApi::send(parent::replaceImgUrl($result->content),Message::$_OK);
        } else {
        	return ResponseApi::send();
        }
    }

    /**
     * XYC-12
     * 得到采购额度（调用金蝶接口)
     */
    public function getbuyamtAction() {
        $api = new ApiController();
        $cmt = $api->getPurchase();
        return ResponseApi::send($cmt);
    }

    /**
     * XYC-13
     * 信用等级详情
     */
    public function creditDetailAction() {
    	$result = CreditIntrinfo::findFirst(array("id = 2", "columns" => "content"));
    	$detail = array();
    	if(is_object($result) && $result) {
    		$detail = $result->toArray();
    		$detail['content'] = parent::replaceImgUrl($detail['content']);
    	}
    	return ResponseApi::send($detail);
    }

    private function sendSmsNotice($mobile) {
    	$smscfg = $this->config->smscfg;
    	$content = "尊敬的中交会员，您的信用申请已经提交";
    	$xmlUtil = new XmlUtil();
    	$message = '<?xml version="1.0" encoding="UTF-8"?>
				<message>
					<account>'.$smscfg->account.'</account>
					<password>'.strtolower(md5($smscfg->password)).'</password>
					<msgid></msgid>
					<phones>'.$mobile.'</phones>
					<content>'.$content.'</content>
					<sign></sign>
					<subcode></subcode>
					<sendtime></sendtime>
				</message>';
    	$response = \PhpRudder\Http\RequestUtil::requestPost(compact('message'), $smscfg->apiUrl);
    	$response = $xmlUtil->xmlToArray($response);
    	$this->get_logger()->debug($mobile."_".$content);
    	return;
    }


}
