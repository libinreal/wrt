<?php

use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class AccountController extends ControllerBase
{
    /**
     * 注册校验码标志
     * @var string
     */
    const VCODE_REGISTER = "vcode_reister";

    /**
     * 忘记密码标志
     * @var string
     */
    const VCODE_FORGETPWD = "vcode_forgetpwd";

    /**
     * 更换手机号码标志
     * @var string
     */
    const VCODE_UPHONE = "vcode_uphone";

    /**
     * 校验码有效时长
     */
    const VCODE_TIME= 600;


    /**
     * 得到验证码，用以注册专用。
     * DLZC-01
     */
    public function createVcodeAction() {
    	if (!$this->request->isPost()) {
    		return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
    	}
        $mobile = $this->request->getPost("mobile" , "int");
        if(!$mobile) {
        	return ResponseApi::send(null, Message::$_ERROR_LOGIC, "请输入手机号！");
        }
        $result = Users::findFirst(array(
        		'conditions' => 'telephone = :mobile:',
        		'bind' => compact('mobile'),
        ));
        if(is_object($result) && $result) {
			$user = $result->toArray();
        	return ResponseApi::send(null, Message::$_ERROR_LOGIC, "手机号码已经被其他用户使用，请重新再试！");
        }
        //发送校验码
        $this->sendValidateCode($mobile, $this::VCODE_REGISTER);
        return ResponseApi::send();
    }

    /**
     * 注册
     * DLZC-02
     */
    public function registerAction() {
        if (!$this->request->isPost()) {
            return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
        }
        //TODO 敏感词过滤，系统保留账号过滤。
        $vcode = $this->request->getPost("vcode");
        if (empty($vcode)) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "手机验证码不能为空");
        }
        $svcode = $this->get_session()->get($this::VCODE_REGISTER);
        if(!$svcode) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "您还没有获取手机验证码。");
        }
        if ($vcode != $svcode) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "您输入的验证码错误！");
        }
        //账户信息
        $user = new Users();
        $user->account = $this->request->getPost("account");

        $password = $this->request->getPost("password", "trim");
        if (!$password || strlen($password) < 6) {
        	return ResponseApi::send(null, Message::$_ERROR_LOGIC, "密码格式不正确！");
        } else {
            $user->password = sha1($password); //密码加密存放
        }
        //个人信息
        $user->gender = $this->request->getPost("gender");
        $user->email = $this->request->getPost("email");
        $user->qq = $this->request->getPost("qq");
        $user->weixin = $this->request->getPost("weixin");
        //单位信息
        $user->companyName = $this->request->getPost("companyName");
        $user->companyAddress = $this->request->getPost("companyAddress");
        $user->department = $this->request->getPost('department');
        $user->officePhone = $this->request->getPost("officePhone");
        $user->fax = $this->request->getPost("fax");
        $user->position = $this->request->getPost("position");

        //联系人信息
        $user->contacts = $this->request->getPost("contacts");
        $user->telephone = $this->request->getPost("telephone");
        $user->secondContacts = $this->request->getPost("secondContacts");
        $user->secondPhone = $this->request->getPost("secondPhone");
        if (!$user->save()) {
            foreach($user->getMessages() as $message) {
                return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
            }
        }
        return ResponseApi::send();
    }

    /**
     * 登录
     * DLZC-03
     */
    public function loginAction() {
        if (!$this->request->isPost()) {
            return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
        }
        $account = $this->request->getPost('account');
        $password = $this->request->getPost('password');
        $password = sha1($password);
        $user = Users::findFirst(array(
            	'conditions' => "account=:account: AND password=:password:",
            	'bind' => compact('account', 'password'),
				'columns' => array (
						"email",
						"account",
						"gender",
						"qq",
						"weixin",
						"companyName",
						"companyAddress",
						"officePhone",
						"fax",
						"position",
						"contacts",
						"telephone",
						"secondContacts",
						"icon",
						"secondPhone",
						"customNo",
						"customLevel",
						"id",
						"creditLevel",
						"department",
						"credits",
				        "customerAccount",
				        "customerNo",
                        "billAmountHistory",
                        "billAmountValid",
                        "cashAmountHistory",
                        "cashAmountValid",
				),
        ));
        if (is_object($user) && $user) {
			$user->icon = preg_match('/^pics.*/', $user->icon) ? $this->get_url() . $user->icon : $user->icon;
            $this->_registerSession($user);
            $user = $user->toArray();
            //TODO 暂时手动加积分
            unset($user['id']);
            $user['superiors'] = '采购工程师上';
            $user['subordinate'] = '采购工程师下';
            return ResponseApi::send($user);
        } else {
            return ResponseApi::send(null, Message::$_ERROR_UNLOGIN, "您输入的用户名或密码错误，请重新再试！");
        }
    }

    /**
     * 找回密码
     * DLZC-04
     */
    public function findpwdAction() {
        $step = $this->request->getPost("step");
        if(!$step) {
        	return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法！");
        }
        $mobile = $this->request->getPost("mobile");
        if (!$mobile) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "请输入您的手机号码！");
        }
        $user = Users::findFirst(array(
        	'conditions' => 'telephone = :mobile:',
        	'bind' => compact('mobile'),
        ));
        if(!$user) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "您输入的手机号码错误");
        }
        if ($step == 1) {
            //生成随机验证码
            $this->sendValidateCode($mobile, $this::VCODE_FORGETPWD);
            return ResponseApi::send();
        } else if ($step == 2) {
            $vcode = $this->request->get("vcode");
            if (!$vcode) {
                return ResponseApi::send(null, Message::$_ERROR_LOGIC, "请输入验证码!");
            }
            $svcode = $this->get_session()->get($this::VCODE_FORGETPWD);
            if(!$svcode) {
                return ResponseApi::send(null, Message::$_ERROR_LOGIC, "您还没有获取手机验证码。");
            }
            $this->get_logger()->debug("找回密码:".$mobile."_".$svcode);
            if ($vcode!=$svcode) {
                return ResponseApi::send(null, Message::$_ERROR_LOGIC, "您输入的验证码错误！");
            }
            $newpas = \PhpRudder\CommonUtil::random(8);
            //更新密码
            $this->get_logger()->debug("新密码是：".$newpas);
            $user->password = sha1($newpas);
           	$user->save();
            //发送新密码给用户
            $this->_sendValidateCode($mobile, $newpas);
        }
        return ResponseApi::send();
    }

    /**
     * 检查账户名同名
     * DLZC-05
     */
    public function checkNameAction() {
        $account = $this->request->get("name");
        $user = Users::findFirst(array(
        		'conditions' => 'account = :account:',
        		'bind' => compact('account'),
        ));
        if ($user) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "您输入的用户名已被其他用户使用！");
        }
        return ResponseApi::send();
    }

    /**
     * 检查手机号码是否存在
     * DLZC-06
     */
    public function checkMobileAction() {
        $mobile = $this->request->get("mobile");
        if(empty($mobile)) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "手机号码不能为空！");
        }
        $user = Users::findFirst(array(
        		'conditions' => 'telephone = :mobile:',
        		'bind' => compact('mobile'),
        ));
        if ($user) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "您输入的手机号码已被其他用户使用！");
        }
        return ResponseApi::send();
    }

    /**
     * 修改账号信息
     * DLZC-07
     */
    public function uinfoAction() {
        if (!$this->request->isPost()) {
            return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
        }
        $userId = $this->get_user()->id;
        $user = Users::findFirst(array(
            'conditions' => 'id = :userId:',
            'bind' => compact('userId'),
        ));
        if (!$user) {
            return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "请重新登录后再试！");
        }

        $user->email = $this->request->getPost("email");
        $user->gender = $this->request->getPost("gender");
        $user->qq = $this->request->getPost("qq");
        $user->officePhone = $this->request->getPost("officePhone");
        $user->weixin = $this->request->getPost("weixin");
        $user->companyName = $this->request->getPost("companyName");
        $user->companyAddress = $this->request->getPost("companyAddress");
        $user->fax = $this->request->getPost("fax");
        $user->position = $this->request->getPost("position");
        $user->contacts = $this->request->getPost("contacts");
        $user->secondContacts = $this->request->getPost("secondContacts");
        $user->secondPhone = $this->request->getPost("secondPhone");
        $user->department = $this->request->getPost("department");

        if (!$user->save()) {
            foreach($user->getMessages() as $message) {
                return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
            }
        }
        return ResponseApi::send();
    }

    /**
     * 注销 DLZC-07
     */
    public function logoutAction() {
    	$this->get_session()->destroy('auth');
    	return ResponseApi::send();
    }

    /**
     * 修改密码
     * DLZC-08
     */
    public function upwdAction() {
        $oldpwd = $this->request->getPost("oldpwd");
        $newpwd = $this->request->getPost("newpwd");
        if (!$oldpwd) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "请输入您的密码！");
        }
        if (!$newpwd) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "请输入新的密码！");
        }
        $oldpwd = sha1($oldpwd);

        $userId = $this->get_user()->id;
        $user = Users::findFirst(array(
            'conditions' => 'id = :userId: and password = :oldpwd:',
            'bind' => compact('userId', 'oldpwd'),
        ));

        if (!$user) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "您的密码输入错误，请重新再试！");
        }
        $user->password = sha1($newpwd);
        $user->save();
        return ResponseApi::send();
    }

    /**
     * 修改电话号码
     * DLZC-09
     */
    public function uphoneAction() {
        $userId = $this->get_user()->id;
        $step = $this->request->getPost("step","int");
        if (!$step) {
            return ResponseApi::send(null, Message::$_ERROR_CODING, "输入的数据格式不合法，必须是1和2！");
        }
        $mobile = $this->request->getPost("mobile");
        if (!$mobile) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "请输入您的手机号码！");
        }
        $user = Users::findFirst(array(
            'conditions' => 'telephone = :mobile:',
            'bind' => compact('mobile'),
        ));
        if($user && $user->id != $userId) {
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "您输入的手机号码已被使用，请重新再试！");
        } else if($user && $user->id == $userId){
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "请输入新的手机号码！");
        }
        if ($step == 1) {
            //生成随机验证码
            $this->sendValidateCode($mobile, $this::VCODE_UPHONE);
            return ResponseApi::send();
        } else if ($step == 2) {
            $vcode = $this->request->get("vcode");
            if (!$vcode) {
                return ResponseApi::send(null, Message::$_ERROR_LOGIC, "请输入验证码!");
            }
            $svcode = $this->get_session()->get($this::VCODE_UPHONE);
            if ($vcode != $svcode) {
                return ResponseApi::send(null, Message::$_ERROR_LOGIC, "您输入的验证码错误！");
            }
            $user = Users::findFirst(array(
                'conditions' => 'id = :userId:',
                'bind' => compact('userId'),
            ));
            if (!$user) {
                return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "请重新登录后再试！");
            }
            //更新手机号码
            $user->telephone = $mobile;
            $user->save();
        }
        return ResponseApi::send();
    }

    /**
     * 修改用户头像
     * DLZC-10
     */
    public function uiconAction() {
        if (!$this->request->isPost()) {
            return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
        }
        $userId = $this->get_user()->id;
        $user = Users::findFirst(array(
            'conditions' => 'id = :userId:',
            'bind' => compact('userId'),
        ));
        if (!$user) {
            return ResponseApi::send(null, Message::$_ERROR_NOFOUND, "请重新登录后再试！");
        }
        $icon = $this->request->getPost("icon");
        if (!$icon) {
            return ResponseApi::send(null, Message::$_ERROR_CODING, "上传的图像必须存在");
        }
        $user->icon = $icon;
        if (!$user->save()) {
            foreach($user->getMessages() as $message) {
                return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
            }
        }
        return ResponseApi::send();
    }

    /**
     * 获取用户信息 DLZC-11
     */
    public function getAction() {
		$userId = $this->get_user()->id;
		$userInfo = Users::findFirst(array(
				'conditions' => 'id = :userId:',
				'bind' => compact('userId'),
				'columns' => 'account,
							gender,
							email,
							qq,
							weixin,
							companyName,
							companyAddress,
							officePhone,
							fax,
							position,
							projectName,
							projectBrief,
							contacts,
							telephone,
							secondContacts,
							secondPhone,
							customLevel,
							customNo,
							credits,
							department,
							creditLevel,
							billAmountValid,
                        cashAmountValid,
							icon',


		))->toArray();
		$userInfo['icon'] = preg_match('/^pics.*/', $userInfo['icon']) ? ($this->get_url() . $userInfo['icon']) : $userInfo['icon'];
		//TODO 逻辑处理
		$userInfo['superiors'] = '采购工程师上';
		$userInfo['subordinate'] = '采购工程师下';
		return ResponseApi::send($userInfo);
    }

    /**
     * 注册用户到session
     * @param $user
     */
    private function _registerSession($user) {
        $this->get_session()->set('auth', $user);
    }

    /**
     *
     * 发送校验码
     * @param $mobile
     * @param $flag
     */
    private function sendValidateCode($mobile, $flag) {
        $vcode = $this->session->get($flag);
        if (!$vcode) {
            //生成随机验证码
            $vcode = \PhpRudder\CommonUtil::random();
            $this->get_session()->set($flag, $vcode, $this::VCODE_TIME);
        }
        $this->_sendValidateCode($mobile, $vcode);
    }

    /**
     * 发送验证码到手机
     *
     * @param $mobile
     * @param $vcode
     */
    private function _sendValidateCode($mobile, $vcode) {
        $smscfg = $this->config->smscfg;
        $content = "验证码是：{$vcode}如非本人操作，请忽略此消息。";
        $xmlUtil = new XmlUtil();
        $message = '<?xml version="1.0" encoding="UTF-8"?>
				<message>
					<account>'.$smscfg->account.'</account>
					<password>'.strtolower(md5($smscfg->password)).'</password>
					<msgid></msgid>
					<phones>'.$mobile.'</phones>
					<content>'.$content.'</content>
					<sign></sign>
					<subcode></subcode>
					<sendtime></sendtime>
				</message>';
        $response = \PhpRudder\Http\RequestUtil::requestPost(compact('message'), $smscfg->apiUrl);
        $response = $xmlUtil->xmlToArray($response);
        $this->get_logger()->debug($mobile."_".$content);
        return;
    }

}
