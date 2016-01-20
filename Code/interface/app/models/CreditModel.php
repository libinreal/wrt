<?php
use PhpRudder\Mvc\ModelBase;

class CreditModel extends ModelBase 
{
	/**
	 * 
	 * @var int
	 */
	public $apply_id;

	
	public function initialize() 
	{
		$attributes = array(
				'apply_id' => 'apply_id', 
		);
		$this->skipAttributesOnCreate($attributes);
		$this->skipAttributesOnUpdate($attributes);
	}
	
	public function getSource() 
	{
		return 'apply_credit';
	}
}