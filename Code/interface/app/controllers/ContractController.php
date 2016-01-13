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
    	echo date('Y-m-d H:i:s', '1414206639');
    	die;
    	$customerId = $this->request->get('customer_id', 'int');
        $limit = $this->request->get('limit', 'int') ?: parent::SIZE;
        $offset = $this->request->get('offset', 'int') ?: 0;
        
        if (!$customerId) {
        	return ResponseApi::send('', -1, 'doesn\'t give `customer_id`');
        }
        
        $where = 'customer_id="'.$customerId.'"';
        
        $data = ContractModel::query()
        		->where($where)
        		->execute();
        		$total = $data->count();
        $data = ContractModel::query()
        		->where($where)
        		->order('contract_id ASC')
        		->limit($limit)
        		->execute()
        		->toArray();
        
        return ResponseApi::send($data);
    }
}