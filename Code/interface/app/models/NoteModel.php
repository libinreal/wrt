<?php
/**
 * 我的票据 Model
 */
use PhpRudder\Mvc\ModelBase;

class NoteModel extends ModelBase 
{
	/**
	 * 
	 * @var int
	 */
	public $bill_id;
	
	
	/**
	 * init
	 * {@inheritDoc}
	 * @see \PhpRudder\Mvc\ModelBase::initialize()
	 */
	public function initialize() 
	{
		$attributes = array(
				'bill_id' => 'bill_id'
		);
		$this->skipAttributesOnCreate($attributes);
	}
	
	
	/**
	 * set table
	 * @return string
	 */
	public function getSource() 
	{
		return 'bill';
	}
}