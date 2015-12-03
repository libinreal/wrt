<?php

class Links extends \Phalcon\Mvc\Model
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
    public $url;

    /**
     *
     * @var string
     */
    public $icon;

    /**
     *
     * @var string
     */
    public $title;

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
    public $userid;

    /**
     *
     * @var string
     */
    public $type;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'url' => 'url', 
            'icon' => 'icon', 
            'title' => 'title', 
            'createAt' => 'createAt', 
            'updateAt' => 'updateAt', 
            'userid' => 'userid', 
            'type' => 'type'
        );
    }

}
