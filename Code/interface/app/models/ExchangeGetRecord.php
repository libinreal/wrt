<?php

class ExchangeGetRecord extends \PhpRudder\Mvc\ModelBase
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
    public $user_id;

    /**
     *
     * @var integer
     */
    public $credits;

    /**
     *
     * @var string
     */
    public $order_sn;

    /**
     *
     * @var integer
     */
    public $createAt;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'user_id' => 'userId',
            'credits' => 'credits',
            'order_sn' => 'orderNo',
            'createAt' => 'createAt'
        );
    }

    public function beforeCreate() {
    	$this->createAt = time();
    }

}
