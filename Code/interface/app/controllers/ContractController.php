<?php

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
     */
    public function getListAction() 
    {
    	$customerId = $this->request->get('customer_id', 'int');
        $size = $this->request->get('size', 'int') ?: parent::SIZE;
        $currentId = $this->request->get('contract_id', 'int') ?: time();
        $forward = $this->request->get('forward', 'int');
        
        if (!$customerId) {
        	return ResponseApi::send('', -1, 'doesn\'t give `customer_id`');
        }
        
        $condition = 'customer_id="'.$customerId.'"';
        if ($forward) {
        	//上一页操作
        	$condition .= ' AND (contract_id>"'.$currentId.'")';
        } else {
        	//下一页操作 或 第一页操作
        	$condition .= ' AND (contract_id<"'.$currentId.'")';
        }
        
        $data = ContractModel::query()
        		->where($condition)
        		->order('contract_id DESC')
        		->limit($size)
        		->execute()
        		->toArray();
        
        return ResponseApi::send($data);
    }
}