<?php

class Appointment extends \PhpRudder\Mvc\ModelBase
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $mobile_phone;

    /**
     *
     * @var integer
     */
    public $type_id;

    /**
     *
     * @var integer
     */
    public $callback_time;

    /**
     *
     * @var integer
     */
    public $c_id;

    /**
     *
     * @var string
     */
    public $c_name;

    /**
     *
     * @var integer
     */
    public $state;

    /**
     *
     * @var string
     */
    public $remark;

    /**
     *
     * @var integer
     */
    public $user_id;

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
            'mobile_phone' => 'telephone',
            'type_id' => 'type',
            'callback_time' => 'time',
            'c_id' => 'cId',
            'c_name' => 'cName',
            'state' => 'state',
            'remark' => 'remark',
            'user_id' => 'user_id',
            'createAt' => 'createAt'
        );
    }

    public function beforeCreate()
    {
    	$this->state = 0;
    	$this->createAt = time();
    }

}
