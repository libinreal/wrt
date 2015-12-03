<?php

class Bidding extends \Phalcon\Mvc\Model
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
    public $name;

    /**
     *
     * @var integer
     */
    public $biddingAt;

    /**
     *
     * @var integer
     */
    public $createAt;

    /**
     *
     * @var string
     */
    public $type;

    /**
     *
     * @var integer
     */
    public $userid;

    /**
     *
     * @var integer
     */
    public $area_id;

    /**
     *
     * @var string
     */
    public $amount;

    /**
     *
     * @var integer
     */
    public $updateAt;

    /**
     *
     * @var string
     */
    public $prjdesc;

    /**
     *
     * @var string
     */
    public $content;

    /**
     *
     * @var string
     */
    public $conditions;

    /**
     *
     * @var string
     */
    public $biddingman;

    /**
     *
     * @var string
     */
    public $prjaddress;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'name' => 'name',
            'biddingAt' => 'biddingAt',
            'createAt' => 'createAt',
            'type' => 'type',
            'userid' => 'userid',
            'area_id' => 'areaId',
            'amount' => 'amount',
            'updateAt' => 'updateAt',
            'prjdesc' => 'prjdesc',
            'content' => 'content',
            'conditions' => 'conditions',
            'biddingman' => 'biddingman',
            'prjaddress' => 'prjaddress'
        );
    }
}
