<?php

class UserInv extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $inv_id;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $inv_type;

    /**
     *
     * @var string
     */
    public $inv_company;

    /**
     *
     * @var string
     */
    public $inv_address;

    /**
     *
     * @var integer
     */
    public $updateAt;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'inv_id' => 'invId',
            'user_id' => 'userId',
            'inv_type' => 'invType',
            'inv_payee' => 'invPayee',
            'inv_address' => 'invAddress',
        	'inv_remark' => 'inv_remark',
           //增值税发票
            'inv_payee' =>'invPayee',
            'inv_company' => 'invCompany',
            'inv_bank_name' => 'invBankName',
            'inv_bank_account' => 'invBankAccount',
            'inv_license' => 'invLicense',
            'inv_company_addr' => 'invCompanyAddr',
            'inv_bank_address' =>'invBankAddress',
            'inv_tel' => 'invTel',
            'inv_fax' => 'invFax',

            'updateAt' => 'updateAt'
        );
    }

    public function beforeCreate() {
    	$this->updateAt = time();
    }

}
