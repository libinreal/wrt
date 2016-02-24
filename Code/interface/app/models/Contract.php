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
            'contract_id' => 'id',
            'customer_id' => 'cusFnum',//1期的客户编号这里用2期的id
        	'customer_id' => 'conFnum',
            'contract_name' => 'name',
            'start_time' => 'strDate',
            'end_time' => 'endDate',
            'contract_amount' => 'conAmt',
            'contract_status' => 'conState',
            'contract_num' => 'code',
            'remark' => 'Remark',
            // 'Banks' => 'Banks',
            // 'Mats' => 'Mats',
        );
    }

    public function initialize() {
        $this->Banks = '';
        $this->Mats = '';
        $attributes = array(
            'Banks',
            'Mats', 
        );
        $this->skipAttributesOnCreate($attributes);
        $this->skipAttributesOnUpdate($attributes);
    }

    public function getSource() 
    {
        return 'contract_old';
    }

}
