<?php

class CreditEvaluationInfo extends \Phalcon\Mvc\Model
{


	/**
	 * 1.资料已提交
	 */
	const STATUS_1 = 1;

	/**，
	 * 2.受理成功，
	*/
	const STATUS_2 = 2;

	/**
	 * 3.审核通过
	 */
	const STATUS_3 = 3;

	/**
	 * 4.成功（评测结果）
	 */
	const STATUS_4 = 4;

	/**
	 * 5.失败（评测结果）
	 */
	const STATUS_5 = 5;

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $money;

    /**
     *
     * @var string
     */
    public $foundedDate;

    /**
     *
     * @var integer
     */
    public $nature;

    /**
     *
     * @var string
     */
    public $amountLimit;

    /**
     *
     * @var string
     */
    public $use;

    /**
     *
     * @var string
     */
    public $businessCode;

    /**
     *
     * @var string
     */
    public $taxCode;

    /**
     *
     * @var string
     */
    public $orgCode;

    /**
     *
     * @var string
     */
    public $businessLicense;

    /**
     *
     * @var string
     */
    public $taxcert;

    /**
     *
     * @var string
     */
    public $orgcert;

    /**
     *
     * @var integer
     */
    public $status;

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
    public $remark;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'money' => 'money',
            'foundedDate' => 'foundedDate',
            'nature' => 'nature',
            'amountLimit' => 'amountLimit',
            'use' => 'use',
            'businessCode' => 'businessCode',
            'taxCode' => 'taxCode',
            'orgCode' => 'orgCode',
            'businessLicense' => 'businessLicense',
            'taxcert' => 'taxcert',
            'orgcert' => 'orgcert',
            'status' => 'status',
            'createAt' => 'createAt',
            'user_id' => 'userId',
            'remark' => 'remark'
        );
    }

}
