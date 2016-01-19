<?php

class Contract1 extends \Phalcon\Mvc\Model
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
    public $cusFnum;

    /**
     *
     * @var string
     */
    public $conFnum;

    /**
     *
     * @var string
     */
    public $conName;

    /**
     *
     * @var integer
     */
    public $strDate;

    /**
     *
     * @var integer
     */
    public $endDate;

    /**
     *
     * @var string
     */
    public $conAmt;

    /**
     *
     * @var string
     */
    public $conState;

    /**
     *
     * @var string
     */
    public $conNo;

    /**
     *
     * @var string
     */
    public $Remark;

    /**
     *
     * @var string
     */
    public $Banks;

    /**
     *
     * @var string
     */
    public $Mats;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'cusFnum' => 'cusFnum',
        	'conFnum' => 'conFnum',
            'conName' => 'name',
            'strDate' => 'strDate',
            'endDate' => 'endDate',
            'conAmt' => 'conAmt',
            'conState' => 'conState',
            'conNo' => 'code',
            'Remark' => 'Remark',
            'Banks' => 'Banks',
            'Mats' => 'Mats',
        );
    }

}
