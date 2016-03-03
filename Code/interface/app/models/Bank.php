<?php

use PhpRudder\Mvc\ModelBase;

class Bank extends ModelBase 
{
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
		return 'bank';
	}
}