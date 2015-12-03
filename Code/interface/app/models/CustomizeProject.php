<?php

class CustomizeProject extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $proId;

    /**
     *zz
     * @var string
     */
    public $proName;

    /**
     *
     * @var string
     */
    public $proAddress;

    /**
     *
     * @var string
     */
    public $proTime;

    /**
     *
     * @var double
     */
    public $proMoney;

    /**
     *
     * @var integer
     */
    public $areaId;

    /**
     *
     * @var string
     */
    public $proRemark;

    /**
     *
     * @var string
     */
    public $contacts;

    /**
     *
     * @var string
     */
    public $position;

    /**
     *
     * @var string
     */
    public $telephone;

    /**
     *
     * @var string
     */
    public $company;

    /**
     *
     * @var string
     */
    public $companyAddress;

    /**
     *
     * @var integer
     */
    public $createAt;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     *
     * @var integer
     */
    public $userId;
    
    /**
     * Validations and business logic
     */
    public function validation()
    {
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'areaId',
    			'message' => '请选择所属区域！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'name',
    			'message' => '请填写项目名称！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'address',
    			'message' => '请填写项目地址！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'period',
    			'message' => '请填写项目周期！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'amount',
    			'message' => '请填写项目投资额度！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'remark',
    			'message' => '请填写项目筹资情况！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'contactPeople',
    			'message' => '请填写项目联系人！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'position',
    			'message' => '请填写您的职位信息！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'contactTelephone',
    			'message' => '请填写项目联系方式！'
    	)));
    	if ($this->validationHasFailed() == true) {
    		return false;
    	}
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'proId' => 'id', 
            'proName' => 'name', //项目名称
            'proAddress' => 'address', //项目地址
            'proTime' => 'period', //项目工期
            'proMoney' => 'amount', //项目投资额度
            'areaId' => 'areaId', //所属区域id
            'proRemark' => 'remark', //项目筹资情况
            'contacts' => 'contactPeople', 
            'position' => 'position', //职位
            'telephone' => 'contactTelephone', //联系电话
            'company' => 'companyName', //公司名称
            'companyAddress' => 'companyAddress', 
            'createAt' => 'createAt', 
            'status' => 'status', 
            'userId' => 'userId'
        );
    }
    
    public function initialize() {
    	$this->createAt = time();
    	$this->status = 0;
    }
    
}
