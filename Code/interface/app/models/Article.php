<?php

class Article extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $article_id;

    /**
     *
     * @var integer
     */
    public $cat_id;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $content;

    /**
     *
     * @var string
     */
    public $author;

    /**
     *
     * @var string
     */
    public $author_email;

    /**
     *
     * @var string
     */
    public $keywords;

    /**
     *
     * @var integer
     */
    public $article_type;

    /**
     *
     * @var integer
     */
    public $is_open;

    /**
     *
     * @var integer
     */
    public $add_time;

    /**
     *
     * @var string
     */
    public $file_url;

    /**
     *
     * @var integer
     */
    public $open_type;

    /**
     *
     * @var string
     */
    public $link;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var string
     */
    public $imgurl;

    /**
     *
     * @var integer
     */
    public $cat_type;

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
     *
     * @var string
     */
    public $source;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'article_id' => 'id',
            'cat_id' => 'catId',
            'title' => 'title',
            'content' => 'content',
            'author' => 'author',
            'author_email' => 'author_email',
            'keywords' => 'keywords',
            'article_type' => 'articleType',
            'is_open' => 'is_open',
            'add_time' => 'add_time',
            'file_url' => 'fileUrl',
            'open_type' => 'open_type',
            'link' => 'link',
            'description' => 'brief',
            'imgurl' => 'imgurl',
            'cat_type' => 'catType',
            'createAt' => 'createAt',
            'updateAt' => 'updateAt',
            'user_id' => 'user_id',
        	'source' => 'source'
        );
    }

}
