<?php
/**
 * 定时任务
 * @author 
 */
use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class TaskController extends ControllerBase 
{
	public function indexAction() 
	{
		$this->persistent->parameters = null;
	}
	
	
	/**
	 * timing that update goods shop_price 
	 */
	public function setShopPriceAction() 
	{
		$data = Goods::query()
				->columns(array('id', 'priceNum', 'priceRate', 'vipPrice'))
				->where('priceNum!=0 OR priceRate!=0 AND priceRule!=0')
				->execute()
				->toArray();
		
		$arr = array();$i = 0;
		foreach ($data as $data) {
			$arr[$i]['id'] = $data['id'];
			if ($data['priceNum']) {
				$arr[$i]['vipPrice'] = $data['vipPrice'] + $data['priceNum'];
			} else {
				$arr[$i]['vipPrice'] = $data['vipPrice'] * (1+($data['priceRate']/100));
			}
			$i++;
		}
		
		foreach ($arr as $arr) {
			if ($arr['id'] && $arr['vipPrice']) {
				$goods = Goods::findFirst($arr['id']);
				$goods->vipPrice = $arr['vipPrice'];
				$goods->update();
			}
			
		}
		
		
		return ResponseApi::send(true);
	}
}
