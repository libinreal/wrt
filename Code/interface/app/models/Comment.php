<?php

class Comment extends \PhpRudder\Mvc\ModelBase
{

    /**
     *
     * @var integer
     */
    public $comment_id;

    /**
     *
     * @var integer
     */
    public $comment_type;

    /**
     *
     * @var integer
     */
    public $id_value;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $user_name;

    /**
     *
     * @var string
     */
    public $content;

    /**
     *
     * @var integer
     */
    public $comment_rank;

    /**
     *
     * @var integer
     */
    public $add_time;

    /**
     *
     * @var string
     */
    public $ip_address;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     *
     * @var integer
     */
    public $parent_id;

    /**
     *
     * @var integer
     */
    public $goods_id;

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
            'comment_id' => 'id',
            'comment_type' => 'comment_type',
            'id_value' => 'id_value',
            'email' => 'email',
            'user_name' => 'username',
            'content' => 'content',
            'comment_rank' => 'comment_rank',
            'add_time' => 'createAt',
            'ip_address' => 'ip_address',
            'status' => 'status',
            'parent_id' => 'parent_id',
            'user_id' => 'userId',
        	'goods_id' => 'goodsId'
        );
    }

    public function initialize() {
    	$this->createAt = time();
    	$this->status = 0;
    	$this->skipAttributesOnCreate(array(
    			'comment_type',
    			'id_value',
    			'email',
    			'comment_rank',
    			'ip_address',
    			'parent_id',
    			'goodsId'
    	));
    }
}
