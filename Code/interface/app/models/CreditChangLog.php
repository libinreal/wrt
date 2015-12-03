<?php

class CreditChangLog extends \Phalcon\Mvc\Model
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
    public $chanFnum;

    /**
     *
     * @var string
     */
    public $cusFnum;

    /**
     *
     * @var string
     */
    public $billFnum;

    /**
     *
     * @var string
     */
    public $billNO;

    /**
     *
     * @var string
     */
    public $bankFnum;

    /**
     *
     * @var integer
     */
    public $chanKind;

    /**
     *
     * @var string
     */
    public $chanAmt;

    /**
     *
     * @var string
     */
    public $remark;

    /**
     *
     * @var integer
     */
    public $chanDate;

    /**
     *
     * @var string
     */
    public $bankName;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'chanFnum' => 'chanFnum', 
            'cusFnum' => 'cusFnum', 
            'billFnum' => 'billFnum', 
            'billNO' => 'billNO', 
            'bankFnum' => 'bankFnum', 
            'chanKind' => 'chanKind', 
            'chanAmt' => 'chanAmt', 
            'remark' => 'remark', 
            'chanDate' => 'chanDate', 
            'bankName' => 'bankName'
        );
    }

}
