<?php

class BillNotice extends \Phalcon\Mvc\Model
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
    public $billFnum;

    /**
     *
     * @var string
     */
    public $cusFnum;

    /**
     *
     * @var string
     */
    public $billNO;

    /**
     *
     * @var string
     */
    public $billAmt;

    /**
     *
     * @var integer
     */
    public $billStrDate;

    /**
     *
     * @var integer
     */
    public $billEndDate;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'billFnum' => 'billFnum', 
            'cusFnum' => 'cusFnum', 
            'billNO' => 'billNO', 
            'billAmt' => 'billAmt', 
            'billStrDate' => 'billStrDate', 
            'billEndDate' => 'billEndDate'
        );
    }

}
