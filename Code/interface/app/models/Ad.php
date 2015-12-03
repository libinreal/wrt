<?php

class Ad extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $ad_id;

    /**
     *
     * @var integer
     */
    public $position_id;

    /**
     *
     * @var integer
     */
    public $media_type;

    /**
     *
     * @var string
     */
    public $ad_name;

    /**
     *
     * @var string
     */
    public $ad_link;

    /**
     *
     * @var string
     */
    public $ad_code;

    /**
     *
     * @var integer
     */
    public $start_time;

    /**
     *
     * @var integer
     */
    public $end_time;

    /**
     *
     * @var string
     */
    public $link_man;

    /**
     *
     * @var string
     */
    public $link_email;

    /**
     *
     * @var string
     */
    public $link_phone;

    /**
     *
     * @var integer
     */
    public $click_count;

    /**
     *
     * @var integer
     */
    public $enabled;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'ad_id' => 'ad_id',
            'position_id' => 'position_id',
            'media_type' => 'media_type',
            'ad_name' => 'ad_name',
            'ad_link' => 'adlink',
            'ad_code' => 'adimg',
            'start_time' => 'start_time',
            'end_time' => 'end_time',
            'link_man' => 'link_man',
            'link_email' => 'link_email',
            'link_phone' => 'link_phone',
            'click_count' => 'click_count',
            'enabled' => 'enabled'
        );
    }

}
