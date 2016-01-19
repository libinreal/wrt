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
     * 
     * @var varchar
     */
    public $contract_num;
    
    
    /**
     * 
     * @var varchar
     */
    public $contract_name;
    
    /**
     * init
     * {@inheritDoc}
     * @see \PhpRudder\Mvc\ModelBase::initialize()
     */
    public function initialize() 
    {
    	$attributes = array(
    			'contract_id'   => 'contract_id', 
    			'contract_num'  => 'contract_num', 
    			'contract_name' => 'contract_name', 
    			'customer_id'   => 'customer_id'
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