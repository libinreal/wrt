<?php

class ArticleCat extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $cat_id;

    /**
     *
     * @var string
     */
    public $cat_name;

    /**
     *
     * @var integer
     */
    public $cat_type;

    /**
     *
     * @var string
     */
    public $keywords;

    /**
     *
     * @var string
     */
    public $cat_desc;

    /**
     *
     * @var integer
     */
    public $sort_order;

    /**
     *
     * @var integer
     */
    public $show_in_nav;

    /**
     *
     * @var integer
     */
    public $parent_id;

    /**
     *
     * @var integer
     */
    public $createAt;

    /**
     *
     * @var integer
     */
    public $updateAt;

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
            'cat_id' => 'cat_id',
            'cat_name' => 'cat_name',
            'cat_type' => 'cat_type',
            'keywords' => 'keywords',
            'cat_desc' => 'cat_desc',
            'sort_order' => 'sort_order',
            'show_in_nav' => 'show_in_nav',
            'parent_id' => 'parent_id',
            'createAt' => 'createAt',
            'updateAt' => 'updateAt',
            'user_id' => 'user_id'
        );
    }

}
