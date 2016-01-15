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
        
        $condition = 'customer_id="'.$customerId.'"';
        if ($forward || !$currentId) {
        	//上一页操作 或 第一页操作
        	if (!empty($condition)) $condition .= ' AND ';
        	$condition .= 'contract_id>"'.$currentId.'"';
        } else {
        	//下一页操作
        	if (!empty($condition)) $condition .= ' AND ';
        	$condition .= 'contract_id<"'.$currentId.'"';
        }
        
        $data = ContractModel::query()
        		->where($condition)
        		->order('contract_id DESC')
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
    			'conditions' => 'contract_id='.$contractId.' AND customer_id='.$userId
    	));
    	if (!$data) {
    		return ResponseApi::send(null, -1, '该合同不存在！');
    	}
    	
    	$status = array('作废', '生效');
    	$type = array('', '销售合同', '采购合同');
    	$signType = array('平台到银行', '银行到平台');
    	
    	if ($data->end_time < time()) {
    		$data->contract_status = '过期';
    	} else {
    		$data->contract_status = $status[$data->contract_status];
    	}
    	$data->contract_type   = $type[$data->contract_type];
    	$data->contract_sign_type = $signType[$data->contract_sign_type];
    	$data->start_time = date('Y-m-d', $data->start_time);
    	$data->end_time = date('Y-m-d', $data->end_time);
    	$data->create_time = date('Y-m-d', $data->create_time);
    	
    	return ResponseApi::send($data);
    }
}