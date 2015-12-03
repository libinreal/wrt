<?php

class UserAddress extends \PhpRudder\Mvc\ModelBase
{

    /**
     *
     * @var integer
     */
    public $address_id;

    /**
     *
     * @var string
     */
    public $address_name;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var string
     */
    public $consignee;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var integer
     */
    public $country;

    /**
     *
     * @var integer
     */
    public $province;

    /**
     *
     * @var integer
     */
    public $city;

    /**
     *
     * @var integer
     */
    public $district;

    /**
     *
     * @var string
     */
    public $address;

    /**
     *
     * @var string
     */
    public $zipcode;

    /**
     *
     * @var string
     */
    public $tel;

    /**
     *
     * @var string
     */
    public $mobile;

    /**
     *
     * @var string
     */
    public $sign_building;

    /**
     *
     * @var string
     */
    public $best_time;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'address_id' => 'id',
            'address_name' => 'tag',
            'user_id' => 'userId',
            'consignee' => 'name',
            'email' => 'email',
            'country' => 'country',
            'province' => 'province',
            'city' => 'city',
            'district' => 'district',
            'address' => 'address',
            'zipcode' => 'zipcode',
            'tel' => 'tel',
            'mobile' => 'phone',
            'sign_building' => 'sign_building',
            'best_time' => 'best_time'
        );
    }

}
