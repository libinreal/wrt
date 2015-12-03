<?php

use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class HelpcenterController extends ControllerBase
{
	/**
	 * Index action
	 */
	public function indexAction()
	{
		$this->persistent->parameters = null;
	}

	/**
	 * 帮助中心列表 BZZX-01
	 */
	public function getHelpsAction() {
		$catType = $this->request->get('catType', 'int');
		if(!$catType) {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "帮助信息不存在！");
		}
		$result = Article::find(array(
			"conditions" => "catType=:catType:",
			"bind" => compact('catType'),
			'columns' => array('id', 'title', 'createAt'),
			'order' => 'createAt DESC',
		));
		$helps = array();
		if(is_object($result) && $result) {
			$helps = $result->toArray();
		} else {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "帮助信息不存在！");
		}
		return ResponseApi::send($helps);

	}

	/**
	 * 查看帮助中心内容BZZX-02
	 */
	public function viewHelpAction() {
		$id = $this->request->get('id', 'int');
		if(!$id) {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "要查看的帮助信息不存在！");
		}
		$result = Article::findFirst(array(
			'conditions' => 'id = :id:',
			'bind' => compact('id'),
			'columns' => array('title', 'content', 'createAt'),
		));
		if(is_object($result) && $result) {
			$help = $result->toArray();
			$help['content'] = parent::replaceImgUrl($help['content']);
			return ResponseApi::send($help);
		} else {
			return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "要查看的帮助信息不存在！");
		}
	}

	/**
	 * 电话预约BZZX-03
	 */
	public function appointmentAction() {
		if (!$this->request->isPost()) {
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "只支持POST请求");
		}
		$type = $this->request->getPost('type', 'int');
		$telephone = $this->request->getPost('telephone');
		$time = $this->request->getPost('time', 'int');
		if(!$type || !in_array($type, array(1, 2, 3)) || !$telephone || $time <= time()) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$user = $this->get_user();
		$cId = $user->id;
		$cName = $user->account;
		$appointment = new Appointment();
		foreach(array('type', 'telephone', 'time', 'cId', 'cName') as $v) {
			$appointment->$v = $$v;
		}
		if(!$appointment->save()) {
			foreach($appointment->getMessages() as $message) {
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $message);
			}
		} else {
			return ResponseApi::send();
		}
	}

	/**
	 * 投诉建议BZZX-4
	 */
	public function omplaintAction() {
		if (!$this->request->isPost()) {
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "只支持POST请求");
		}
		$type = $this->request->getPost('type', 'int');
		$content = trim($this->request->getPost('content'));
		$orderNo = $this->request->getPost('orderNo');
		if(!$type || !in_array($type, array(1, 2, 3, 4)) || mb_strlen($content, 'utf-8') < 5 || mb_strlen($content, 'utf-8') > 200 || !$orderNo) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$user = $this->get_user();
		$cId = $user->id;
		$cName = $user->account;
		$complaint = new Complaint();
		foreach(array('type', 'content', 'orderNo', 'cId', 'cName') as $v) {
			$complaint->$v = $$v;
		}
		if(!$complaint->save()) {
			foreach ($complaint->getMessages() as $message) {
				return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $message);
			}
		} else {
			return ResponseApi::send();
		}
	}

}