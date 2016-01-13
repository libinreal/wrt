<?php

use PhpRudder\Mvc\ModelBase;


class ContractModel extends ModelBase
{
    /**
     * 
     * @var int
     */
    public $contract_id;
    
    
    /**
     * 
     * @var int
     */
    public $customer_id;
    
    public function initialize() 
    {
    	$attributes = array(
    			'contract_id' => 'contract_id', 
    			'customer_id' => 'customer_id'
    	);
    	$this->skipAttributesOnCreate($attributes);
    }
    
    
    public function getSource() 
    {
    	return 'contract';
    }
}