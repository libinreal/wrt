<?php

use PhpRudder\Mvc\ModelBase;


class ContractModel extends ModelBase
{
    
    
    public function columnMap()
    {
        return array(
            'contract_id' => 'id',
            'contract_num' => 'num',
            'contract_name' => 'name',
            'contract_amount' => 'amount',
            'contract_status' => 'status',
            'contract_type' => 'type',
            'contract_sign_type' => 'signType',
            'customer_id' => 'customerId',
            'start_time' => 'startTime',
            'end_time' => 'endTime',
            'is_control' => 'isControl',
            'rate' => 'rate',
            'registration' => 'reg',
            'bank_id' => 'bankId',
            'attachment' => 'attach',
            'remark' => 'remark',
            'create_user' => 'createUser',
            'create_by' => 'createBy',
            'create_time' => 'createTime',
            'bill_amount_history' => 'billHistory',
            'bill_amount_valid' => 'billValid',
            'cash_amount_history' => 'cashHistory',
            'cash_amount_valid' => 'cashValid',
            'user_id' => 'userId',
            'user_name' => 'userName'
        );
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