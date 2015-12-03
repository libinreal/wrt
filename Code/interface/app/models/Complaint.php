<?php

class Complaint extends \PhpRudder\Mvc\ModelBase
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
    public $type_id;

    /**
     *
     * @var string
     */
    public $order_id;

    /**
     *
     * @var string
     */
    public $content;

    /**
     *
     * @var integer
     */
    public $state;

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
            'type_id' => 'type',
            'order_id' => 'orderNo',
            'content' => 'content',
            'state' => 'state',
            'c_id' => 'cId',
            'c_name' => 'cName',
            'createAt' => 'createAt',
            'user_id' => 'user_id',
            'remark' => 'remark'
        );
    }

    public function beforeCreate()
    {
    	$this->state = 0;
    	$this->createAt = time();
    }

}
