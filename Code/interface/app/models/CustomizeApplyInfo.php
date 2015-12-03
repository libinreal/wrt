<?php

class CustomizeApplyInfo extends \PhpRudder\Mvc\ModelBase
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $number;

    /**
     *
     * @var string
     */
    public $address;

    /**
     *
     * @var string
     */
    public $telephone;

    /**
     *
     * @var string
     */
    public $contacts;

    /**
     *
     * @var integer
     */
    public $applyId;

    /**
     *
     * @var string
     */
    public $remark;

    /**
     *
     * @var integer
     */
    public $updateAt;

    /**
     *
     * @var integer
     */
    public $createAt;

    /**
     *
     * @var integer
     */
    public $userId;

    /**
     *
     * @var integer
     */
    public $state;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'number' => 'goodsNum',
            'address' => 'address',
            'telephone' => 'telephone',
            'contacts' => 'contacts',
            'applyId' => 'applyId',
            'remark' => 'remark',
            'updateAt' => 'updateAt',
            'createAt' => 'createAt',
            'userId' => 'userId',
        	'state' => 'state'
        );
    }

    /**
     * Validations and business logic
     */
    public function validation()
    {
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'goodsNum',
    			'message' => '数量不能为空！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'address',
    			'message' => '地址不能为空！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'telephone',
    			'message' => '联系电话不能为空！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'contacts',
    			'message' => '联系人不能为空！'
    	)));

    	if ($this->validationHasFailed() == true) {
    		return false;
    	}
    }

    public function beforeCreate()
    {
    	$this->createAt = time();
    	$this->updateAt = time();
    	$this->state = 0;
    }

    public function beforeUpdate()
    {
    	$this->updateAt = time();
    }

}
