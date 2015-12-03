<?php

use Phalcon\Db\Column as Column;
use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class AddressController extends ControllerBase{
	/**
	 * Index action
	 */
	public function indexAction()
	{
		$this->persistent->parameters = null;
	}

	/**
	 * 获取收货地址列表 GRZX-3
	 */
	public function getlistAction(){
		$userId = $this->get_user()->id;
		$criteria = UserAddress::query();
		$criteria->leftJoin('Users', 'u.addressId=UserAddress.id', 'u');
		$criteria->where('UserAddress.userId=:userId:', compact('userId'), array (
				Column::BIND_PARAM_INT
		));
		$criteria->columns(array (
				'UserAddress.id',
				'UserAddress.tag',
				'UserAddress.name',
				'UserAddress.phone',
				'UserAddress.address', 'UserAddress.tag', 'IF(u.addressId > 0, 1, 0) default'));
		$result = $criteria->execute();
		$addresses = array();
		if(is_object($result) && $result) {
			$addresses = $result->toArray();
		}
		return ResponseApi::send($addresses);
	}
	/**
	 * 保存收货地址信息 GRZX-4
	 */
	public function saveAction() {
		if (!$this->request->isPost()) {
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "只支持POST请求");
		}
		$id = $this->request->getPost('id', 'int');
		$name = $this->request->getPost('name');
		$phone = $this->request->getPost('phone');
		$address = $this->request->getPost('address');
		$tag = $this->request->getPost('tag');
		if(!$name || !$phone || !$tag) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$userAddress = new UserAddress();
		if($id) {
			$userAddress = UserAddress::findFirst($id);
		}
		$userId = $this->get_user()->id;
		foreach(array('name', 'phone', 'address', 'tag', 'userId') as $v) {
			$userAddress->$v = $$v;
		}
		if(!$userAddress->save()) {
			foreach($userAddress->getMessages() as $message) {
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $message);
			}
		} else {
			$addressId = $userAddress->id;
			return ResponseApi::send(compact('addressId'));
		}
	}

	/**
	 * 删除收货地址信息 GRZX-5
	 */
	public function deleteAction() {
		if (!$this->request->isPost()) {
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "只支持POST请求");
		}
		$id = $this->request->getPost('id', 'int');
		if(!$id) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$userAddress = UserAddress::findFirst($id);
		if(!$userAddress->delete()) {
			foreach($userAddress->getMessages() as $message) {
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $message);
			}
		} else {
			return ResponseApi::send();
		}
	}

	/**
	 * 设置默认收货地址信息 GRZX-8
	 */
	public function setDefaultAction() {
		if (!$this->request->isPost()) {
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "只支持POST请求");
		}
		$addressId = $this->request->getPost('id');
		if(!$addressId) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$user = $this->get_user();
		$user = Users::findFirst($user->id);
		$user->addressId = $addressId;
		if(!$user->save()) {
			foreach($user->getMessages() as $message) {
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $message);
			}
		}
		return ResponseApi::send();
	}

	/**
	 * 获取收货地址详情GRZX-23
	 */
	public function detailAction() {
		$id = $this->request->get('id', 'int');
		$userId = $this->get_user()->id;
		$result = UserAddress::findFirst(array(
			'conditions' => 'id = :id: AND userId = :userId:',
			'bind' => compact('id', 'userId'),
			'columns' => array('id', 'name', 'phone', 'address', 'tag')
		));
		$addressDetail = array();
		if(is_object($result) && $result) {
			$addressDetail = $result->toArray();
		}
		return ResponseApi::send($addressDetail);
	}
}