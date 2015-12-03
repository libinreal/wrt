<?php

class CustomizeApply extends \PhpRudder\Mvc\ModelBase
{

	const M1 = "M1";//五个月内
	const M5 = "M5";//五个月内
	const Y1 = "Y1";  //一年内
	const Y2 = "Y2";  //二年内
	const Y3 = "Y2";// 三年内
	const Y4 = "Y2";//四年内
	const Y5 = "Y2";//五年内

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $areaId;

    /**
     *
     * @var integer
     */
    public $categoryNo;

    /**
     *
     * @var string
     */
    public $goodsName;

    /**
     *
     * @var string
     */
    public $thumb;

    /**
     *
     * @var string
     */
    public $originalImg;

    /**
     *
     * @var string
     */
    public $goodsModel;

    /**
     *
     * @var string
     */
    public $goodsUnit;

    /**
     *
     * @var string
     */
    public $goodsPrice;

    /**
     *
     * @var string
     */
    public $goodsSpec;

    /**
     *
     * @var integer
     */
    public $expirationAt;

    /**
     *
     * @var integer
     */
    public $createAt;

    /**
     *
     * @var integer
     */
    public $updateAt;

    /**
     *
     * @var integer
     */
    public $userId;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'areaId' => 'areaId',
            'categoryNo' => 'categoryNo',
            'goodsName' => 'goodsName',
            'thumb' => 'thumb',
            'originalImg' => 'originalImg',
            'goodsModel' => 'goodsModel',
            'goodsUnit' => 'goodsUnit',
            'goodsPrice' => 'goodsPrice',
            'goodsSpec' => 'goodsSpec',
            'expirationAt' => 'expirationAt',
            'createAt' => 'createAt',
            'updateAt' => 'updateAt',
            'userId' => 'userId'
        );
    }

    /**
     * Validations and business logic
     */
    public function validation()
    {
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'areaId',
    			'message' => '销售区域不能为空！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'categoryNo',
    			'message' => '分类不能为空！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'goodsName',
    			'message' => '商品名称不能为空！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'originalImg',
    			'message' => '必须上传商品图片！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'goodsPrice',
    			'message' => '价格不能为空！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'goodsUnit',
    			'message' => '单位不能为空！'
    	)));
    	$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
    			'field' => 'expirationAt',
    			'message' => '有效期不能为空！'
    	)));

    	if ($this->validationHasFailed() == true) {
    		return false;
    	}
    }

    public function beforeCreate()
    {
    	$this->createAt = time();
    	$this->updateAt = time();
    }

    public function beforeUpdate()
    {
    	$this->updateAt = time();
    }

}
