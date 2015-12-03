<?php

class Goods extends \PhpRudder\Mvc\ModelBase
{

    /**
     *
     * @var integer
     */
    public $goods_id;

    /**
     *
     * @var integer
     */
    public $cat_id;

    /**
     *
     * @var string
     */
    public $goods_sn;

    /**
     *
     * @var string
     */
    public $goods_name;

    /**
     *
     * @var string
     */
    public $goods_name_style;

    /**
     *
     * @var integer
     */
    public $click_count;

    /**
     *
     * @var integer
     */
    public $brand_id;

    /**
     *
     * @var string
     */
    public $provider_name;

    /**
     *
     * @var integer
     */
    public $goods_number;

    /**
     *
     * @var double
     */
    public $goods_weight;

    /**
     *
     * @var double
     */
    public $market_price;

    /**
     *
     * @var double
     */
    public $shop_price;

    /**
     *
     * @var double
     */
    public $promote_price;

    /**
     *
     * @var integer
     */
    public $promote_start_date;

    /**
     *
     * @var integer
     */
    public $promote_end_date;

    /**
     *
     * @var integer
     */
    public $warn_number;

    /**
     *
     * @var string
     */
    public $keywords;

    /**
     *
     * @var string
     */
    public $goods_brief;

    /**
     *
     * @var string
     */
    public $goods_desc;

    /**
     *
     * @var string
     */
    public $goods_thumb;

    /**
     *
     * @var string
     */
    public $goods_img;

    /**
     *
     * @var string
     */
    public $original_img;

    /**
     *
     * @var integer
     */
    public $is_real;

    /**
     *
     * @var string
     */
    public $extension_code;

    /**
     *
     * @var integer
     */
    public $is_on_sale;

    /**
     *
     * @var integer
     */
    public $is_alone_sale;

    /**
     *
     * @var integer
     */
    public $is_shipping;

    /**
     *
     * @var integer
     */
    public $integral;

    /**
     *
     * @var integer
     */
    public $add_time;

    /**
     *
     * @var integer
     */
    public $sort_order;

    /**
     *
     * @var integer
     */
    public $is_delete;

    /**
     *
     * @var integer
     */
    public $is_best;

    /**
     *
     * @var integer
     */
    public $is_new;

    /**
     *
     * @var integer
     */
    public $is_hot;

    /**
     *
     * @var integer
     */
    public $is_promote;

    /**
     *
     * @var integer
     */
    public $bonus_type_id;

    /**
     *
     * @var integer
     */
    public $last_update;

    /**
     *
     * @var integer
     */
    public $goods_type;

    /**
     *
     * @var string
     */
    public $seller_note;

    /**
     *
     * @var integer
     */
    public $give_integral;

    /**
     *
     * @var integer
     */
    public $rank_integral;

    /**
     *
     * @var integer
     */
    public $suppliers_id;

    /**
     *
     * @var integer
     */
    public $is_check;

    /**
     *
     * @var integer
     */
    public $area_id;

    /**
     *
     * @var string
     */
    public $sales;

    /**
     *
     * @var string
     */
    public $goods_explain;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $cat_code;

    /**
     *
     * @var string
     */
    public $wcode;

    /**
     *
     * @var string
     */
    public $shiplocal;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'goods_id' => 'id',
            'cat_id' => 'cat_id',
            'goods_sn' => 'goodsSn',
            'goods_name' => 'name',
            'goods_name_style' => 'goods_name_style',
            'click_count' => 'click_count',
            'brand_id' => 'factoryId',
            'provider_name' => 'provider_name',
            'goods_number' => 'storeNum',
            'goods_weight' => 'goods_weight',
            'market_price' => 'price',
            'shop_price' => 'vipPrice',
            'promote_price' => 'promote_price',
            'promote_start_date' => 'promote_start_date',
            'promote_end_date' => 'promote_end_date',
            'warn_number' => 'warn_number',
            'keywords' => 'keywords',
            'goods_brief' => 'spec',
            'goods_desc' => 'des',
            'goods_thumb' => 'thumb',
            'goods_img' => 'goods_img',
            'original_img' => 'original_img',
            'is_real' => 'is_real',
            'extension_code' => 'extension_code',
            'is_on_sale' => 'is_on_sale',
            'is_alone_sale' => 'is_alone_sale',
            'is_shipping' => 'is_shipping',
            'integral' => 'integral',
            'add_time' => 'createAt',
            'sort_order' => 'sort_order',
            'is_delete' => 'isDelete',
            'is_best' => 'isBest',
            'is_new' => 'is_new',
            'is_hot' => 'is_hot',
            'is_promote' => 'is_promote',
            'bonus_type_id' => 'bonus_type_id',
            'last_update' => 'last_update',
            'goods_type' => 'goods_type',
            'seller_note' => 'seller_note',
            'give_integral' => 'give_integral',
            'rank_integral' => 'rank_integral',
            'suppliers_id' => 'suppliersId',
            'is_check' => 'is_check',
            'area_id' => 'areaId',
            'sales' => 'afterSale',
            'goods_explain' => 'instr',
            'user_id' => 'userId',
            'cat_code' => 'code',
            'wcode' => 'wcode',
        	'shiplocal' => 'shiplocal'
        );
    }

    public function initialize() {
    	$attributes = array(
    			'id',
    			'cat_id',
    			'goodsSn',
    			'name',
    			'goods_name_style',
    			'click_count',
    			'factoryId',
    			'provider_name',
    			'goods_weight',
    			'price',
    			'vipPrice',
    			'promote_price',
    			'promote_start_date',
    			'promote_end_date',
    			'warn_number',
    			'keywords',
    			'goods_brief',
    			'des',
    			'thumb',
    			'goods_img',
    			'original_img',
    			'is_real',
    			'extension_code',
    			'is_on_sale',
    			'is_alone_sale',
    			'is_shipping',
    			'integral',
    			'createAt',
    			'sort_order',
    			'is_delete',
    			'isBest',
    			'is_new',
    			'is_hot',
    			'is_promote',
    			'bonus_type_id',
    			'last_update',
    			'goods_type',
    			'seller_note',
    			'give_integral',
    			'rank_integral',
    			'suppliersId',
    			'is_check',
    			'areaId',
    			'afterSale',
    			'instr',
    			'userId',
    			'code',
    			'wcode',
    			'shiplocal'
    	);
    	$this->skipAttributesOnUpdate($attributes);
    }

}
