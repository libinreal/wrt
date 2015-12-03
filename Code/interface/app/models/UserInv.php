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
            'inv_company' => 'invPayee',
            'inv_address' => 'invAddress',
            'updateAt' => 'updateAt'
        );
    }

    public function beforeCreate() {
    	$this->updateAt = time();
    }

}
