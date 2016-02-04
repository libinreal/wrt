<?php
/**
 * 我的合同
 * @author 
 */
use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class ContractController extends ControllerBase
{
    public function indexAction() 
    {
        $this->persistent->parameters = null;
    }
    
    
    /**
     * 我的合同
     * @return 
     */
    public function getListAction() 
    {
    	$customerId = $this->get_user()->id;
        $size = $this->request->get('size', 'int') ?: parent::SIZE;
        $currentId = $this->request->get('contract_id', 'int') ?: 0;
        $forward = $this->request->get('forward', 'int');
        
        if (!$customerId) {
        	return ResponseApi::send('', -1, '合同不存在！');
        }
        
        //如果是总账号 会选择旗下的所有子帐号
        $result = Users::find(array(
        		'conditions' => 'parent_id = '.$customerId.' or id='.$customerId,
        		'columns' => 'id'
        ));
        $user = array();
        if(is_object($result) && $result->count()) {
        	$user = $result->toArray();
        }
        
        //所有用户id
        $userId = array();
        foreach ($user as $v) {
        	$userId[] = $v['id']; 
        }
        if (!$userId) {
        	return ResponseApi::send(array());
        }
        
        //总账号显示 总账号与子帐号的所有合同
        $condition = 'userId in('.implode(',', $userId).')';
        
        if ($forward || !$currentId) {
        	//上一页操作 或 第一页操作
        	if (!empty($condition)) $condition .= ' AND ';
        	$condition .= 'id>"'.$currentId.'"';
        } else {
        	//下一页操作
        	if (!empty($condition)) $condition .= ' AND ';
        	$condition .= 'id<"'.$currentId.'"';
        }
        
        $data = ContractModel::query()
        		->where($condition)
        		->order('id DESC')
        		->limit($size)
        		->execute()
        		->toArray();
        
        return ResponseApi::send($data);
    }
    
    
    /**
     * 合同详情
     * @return array
     */
    public function getSingleAction() 
    {
    	error_reporting(E_ALL^E_NOTICE);
    	$contractId = $this->request->get('contract_id', 'int') ?: 0;
    	$userId = $this->get_user()->id;
    	if (!$contractId || !$userId) {
    		return ResponseApi::send(null, -1, '合同不存在！');
    	}
    	
    	$data = ContractModel::findFirst(array(
    			'conditions' => 'id='.$contractId.' AND userId='.$userId
    	));
    	if (!$data) {
    		return ResponseApi::send(null, -1, '该合同不存在！');
    	}
    	
    	$status = array('作废', '生效');
    	$type = array('', '销售合同', '采购合同');
    	$signType = array('平台到银行', '银行到平台');
    	
    	if ($data->endTime < time()) {
    		$data->status = '过期';
    	} else {
    		$data->status = $status[$data->status];
    	}
    	$data->type   = $type[$data->type];
    	$data->signType = $signType[$data->signType];
    	$data->startTime = date('Y-m-d', $data->startTime);
    	$data->endTime = date('Y-m-d', $data->endTime);
    	$data->createTime = date('Y-m-d', $data->createTime);
    	
    	return ResponseApi::send($data);
    }
}