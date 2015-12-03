<?php

class Apply extends \PhpRudder\Mvc\ModelBase
{
	/**
	 * 追加信用额度申请
	 */
	const type_0 = 0;

	/**
	 * 追加申请额度申请.
	 */
	const type_1 = 1;

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $contractNo;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $phone;

    /**
     *
     * @var string
     */
    public $amount;

    /**
     *
     * @var string
     */
    public $reason;

    /**
     *
     * @var integer
     */
    public $type;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     *
     * @var string
     */
    public $remark;

    /**
     *
     * @var integer
     */
    public $createAt;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var string
     */
    public $curamt;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'contractNo' => 'contractNo',
            'name' => 'name',
            'phone' => 'phone',
            'amount' => 'amount',
            'reason' => 'reason',
            'type' => 'type',
            'status' => 'status',
            'remark' => 'remark',
            'createAt' => 'createAt',
        	'user_id' => 'userId',
        	'curamt' => 'curamt'
        );
    }

    public function beforeCreate() {
    	$this->createAt = time();
    	$this->status = 0;
    }

}
