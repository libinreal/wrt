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
    
    
    /**
     * init
     * {@inheritDoc}
     * @see \PhpRudder\Mvc\ModelBase::initialize()
     */
    public function initialize() 
    {
    	$attributes = array(
    			'contract_id' => 'contract_id', 
    			'customer_id' => 'customer_id'
    	);
    	$this->skipAttributesOnCreate($attributes);
    }
    
    
    /**
     * set table
     * @return string
     */
    public function getSource() 
    {
    	return 'contract';
    }
}