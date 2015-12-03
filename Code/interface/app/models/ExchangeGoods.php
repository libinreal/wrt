<?php

class ExchangeGoods extends \PhpRudder\Mvc\ModelBase
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
     * @var string
     */
    public $sales;

    /**
     *
     * @var string
     */
    public $spec;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'goods_id' => 'id',
            'cat_id' => 'categoryId',
            'goods_sn' => 'itemNo',
            'goods_name' => 'name',
            'goods_name_style' => 'goods_name_style',
            'click_count' => 'click_count',
            'brand_id' => 'brand_id',
            'provider_name' => 'provider_name',
            'goods_number' => 'storeNum',
            'goods_weight' => 'weight',
            'market_price' => 'market_price',
            'shop_price' => 'credits',
            'promote_price' => 'promote_price',
            'promote_start_date' => 'promote_start_date',
            'promote_end_date' => 'promote_end_date',
            'warn_number' => 'warn_number',
            'keywords' => 'keywords',
            'goods_brief' => 'goods_brief',
            'goods_desc' => 'des',
            'goods_thumb' => 'goods_thumb',
            'goods_img' => 'imageUrl',
            'original_img' => 'originalImg',
            'is_real' => 'is_real',
            'extension_code' => 'extension_code',
            'is_on_sale' => 'isOnSale',
            'is_alone_sale' => 'is_alone_sale',
            'is_shipping' => 'is_shipping',
            'integral' => 'integral',
            'add_time' => 'add_time',
            'sort_order' => 'sort',
            'is_delete' => 'isDelete',
            'is_best' => 'is_best',
            'is_new' => 'is_new',
            'is_hot' => 'is_hot',
            'is_promote' => 'is_promote',
            'bonus_type_id' => 'bonus_type_id',
            'last_update' => 'updateAt',
            'goods_type' => 'goods_type',
            'seller_note' => 'seller_note',
            'give_integral' => 'give_integral',
            'rank_integral' => 'rank_integral',
            'suppliers_id' => 'suppliers_id',
            'is_check' => 'is_check',
            'sales' => 'sales',
            'spec' => 'spec',
            'user_id' => 'user_id'
        );
    }

    public function initialize() {
    	$attributes = array(
    			'id',
    			'categoryId',
    			'itemNo',
    			'name',
    			'goods_name_style',
    			'click_count',
    			'brand_id',
    			'provider_name',
    			'weight',
    			'market_price',
    			'credits',
    			'promote_price',
    			'promote_start_date',
    			'promote_end_date',
    			'warn_number',
    			'keywords',
    			'goods_brief',
    			'des',
    			'goods_thumb',
    			'imageUrl',
    			'originalImg',
    			'is_real',
    			'extension_code',
    			'isOnSale',
    			'is_alone_sale',
    			'is_shipping',
    			'integral',
    			'add_time',
    			'sort',
    			'isDelete',
    			'is_best',
    			'is_new',
    			'is_hot',
    			'is_promote',
    			'bonus_type_id',
    			'updateAt',
    			'goods_type',
    			'seller_note',
    			'give_integral',
    			'rank_integral',
    			'suppliers_id',
    			'is_check',
    			'sales',
    			'spec',
    			'user_id'
    	);
    	$this->skipAttributesOnUpdate($attributes);
    }

}
