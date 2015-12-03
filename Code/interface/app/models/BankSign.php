<?php

class BankSign extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $sign_id;

    /**
     *
     * @var string
     */
    public $order_sn;

    /**
     *
     * @var string
     */
    public $submit_data;

    /**
     *
     * @var string
     */
    public $sign_data;

    /**
     *
     * @var string
     */
    public $buyer_sign;

    /**
     *
     * @var string
     */
    public $saler_sign;

    /**
     *
     * @var integer
     */
    public $buyer_sign_time;

    /**
     *
     * @var integer
     */
    public $saler_sign_time;

    /**
     *
     * @var integer
     */
    public $create_at;

    /**
     *
     * @var string
     */
    public $sign_type;

    /**
     *
     * @var string
     */
    public $sign_result;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'sign_id' => 'signId',
            'order_sn' => 'orderSn',
            'submit_data' => 'submitData',
            'sign_data' => 'signData',
            'buyer_sign' => 'buyerSign',
            'saler_sign' => 'salerSign',
            'buyer_sign_time' => 'buyerSignTime',
            'saler_sign_time' => 'salerSignTime',
            'create_at' => 'createAt',
            'sign_type' => 'signType',
            'sign_result' => 'signResult',
        );
    }

    public function initialize() {
        $this->createAt = time();
        $this->skipAttributesOnCreate(array(
            'sign_id',
            'buyerSign',
            'salerSign',
            'buyerSignTime',
            'salerSignTime',
            'signResult'
        ));
    }

}
