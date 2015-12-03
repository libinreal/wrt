<?php

use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;
use PhpRudder\CommonUtil;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
use Phalcon\Image\Adapter\GD as ImgGD;

class CustomizeController extends ControllerBase
{

	private static $_expire = array(
			'd3' => ' +3 day',
			'd5' => ' +5 day',
			'd15' => ' +15 day',
			'min' => ' +1 month',
			'mout' => '+1 month',
	);

	private static $_expireAt = array(
			'M1',
			'M5',
			'Y1',
			'Y2',
			'Y3',
			'Y4',
			'Y5'
	);

	private static $_expireType = array(
			1 => 'M1',
			5 => 'M5',
			12 => 'Y1',
			24 => 'Y2',
			36 => 'Y3',
			48 => 'Y4',
			60 => 'Y5'
	);

	private static $_timeType = array('M' => 'month', 'Y' => 'year');

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * 查询定制申请单 DZZQ-01
     */
    public function searchAction() {
        $categoryNo = $this->request->get('categoryNo', 'int');
        $createAt = $this->request->get('createAt', 'int') ?: time() + 10;
        $size = $this->request->get('size', 'int') ?: parent::SIZE;
        $forward = $this->request->get('forward', 'int');
        
        $builder = $this->modelsManager->createBuilder();
        $builder->from('CustomizeApply');
        $builder->leftJoin('CustomizeApplyInfo', 'info.applyId=CustomizeApply.id', 'info');
        $builder->leftJoin('Region', 'r.id=CustomizeApply.areaId', 'r');
        if($categoryNo) {
        	$minimum = $categoryNo;
        	$maximum = (floor($minimum / 10000000) + 1) * 10000000  - 1;
        	$builder->betweenWhere('CustomizeApply.categoryNo', $minimum, $maximum);
        }
        $orderBy = 'CustomizeApply.createAt';
        if($forward) {
        	$builder->andWhere('CustomizeApply.createAt > :createAt:', compact('createAt'));
        } else {
        	$builder->andWhere('CustomizeApply.createAt < :createAt:', compact('createAt'));
        	$orderBy .= ' DESC';
        }
        $builder->groupBy('CustomizeApply.id');
        $builder->orderBy($orderBy);
        $builder->limit($size);
        $builder->columns("CustomizeApply.id,
					CustomizeApply.goodsName,
					CustomizeApply.goodsSpec,
					CustomizeApply.goodsModel,
	                REPLACE(FORMAT(CustomizeApply.goodsPrice, 2), ', ', '') goodsPrice,
	                CustomizeApply.goodsUnit,
        			IF(thumb LIKE 'http://%', thumb, CONCAT('" . $this->get_url() . "', thumb)) thumb,
        			IF(originalImg LIKE 'http://%', originalImg, CONCAT('" . $this->get_url() . "', originalImg)) originalImg,
	                CustomizeApply.createAt,
	               	r.name area,
	                IF(UNIX_TIMESTAMP()>CustomizeApply.expirationAt, 'expire', 'unexpire') expire,
	                COUNT(info.applyId) applyTotal,
	                SUM(info.goodsNum) weightTotal");
        $result = $builder->getQuery()->execute();
        $customizeApply = array();
        if(is_object($result) && $result->count()) {
        	$customizeApply = $result->toArray();
        	if($forward) {
        		$customizeApply = array_reverse($customizeApply);
        	}
        }
        return ResponseApi::send($customizeApply);
    }


    /**
     * 提交定制申请单 DZZQ-02
     */
    public function createAction() {
    	if (!$this->request->isPost()) {
    		return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
    	}
        $manager = new TxManager();
        $transaction = $manager->get();
        $customize_apply = new CustomizeApply();
        $customize_apply->setTransaction($transaction);
        $customize_apply->areaId = $this->request->getPost("areaId", "int");
        $customize_apply->categoryNo = $this->request->getPost("categoryNo");
        $customize_apply->goodsName = $this->request->getPost("goodsName");
        $customize_apply->originalImg = $this->request->getPost("originalImg");
        $goodsImg = $this->request->getPost('goodsImg');
        if($goodsImg) {
			$goodsImg = str_replace($this->get_url(), '', $goodsImg);
			$customize_apply->thumb = $goodsImg;
			$customize_apply->originalImg = $goodsImg;
        } else {
	        if($customize_apply->originalImg != null) {
	        	$customize_apply->originalImg = str_replace($this->get_url(), '', $customize_apply->originalImg);
	            $temps = explode("/", $customize_apply->originalImg);
	            $filename = $temps[count($temps)-1];
	            $thumb = new ImgGD($this->config->storepath->pubdir.$temps[count($temps)-2].'/'.$filename);
	            $thumb->resize(100);
	            $filedir = $this->config->storepath->thumbdir.date("Ym")."/";
	            if(!is_dir($filedir)) {
					if(!mkdir($filedir, 777, true)) {
						return ResponseApi::send(null, Message::$_ERROR_SYSTEM, '目录创建失败，请稍后再试');
					}
	            } else {
	            	if(!is_writable($filedir)) {
	            		return ResponseApi::send(null, Message::$_ERROR_SYSTEM, '目录不可写，请稍后再试');
	            	}
	            }
	            $gd = $thumb->save($filedir . $filename, 100);

	            if (!$gd) {
	                return ResponseApi::send(null, Message::$_ERROR_LOGIC, "您上传的文件格式错误，请重新上传！");
	            }
	            $customize_apply->thumb = "thumbs/".date("Ym")."/".$filename;
	        }
        }

        $customize_apply->goodsModel = $this->request->getPost("goodsModel");
        $customize_apply->goodsUnit = $this->request->getPost("goodsUnit");
        $customize_apply->goodsPrice = $this->request->getPost("goodsPrice");
        $customize_apply->goodsSpec = $this->request->getPost("goodsSpec");

        //根据选择的枚举类型确定过期时间
        $validDateEnum = $this->request->getPost("validDateEnum");
        if ($validDateEnum) {
        	$expireAt = CommonUtil::object2Array($this->config->enums->expireAt);
        	if(!in_array($validDateEnum, $expireAt)) {
        		return ResponseApi::send(null, Message::$_ERROR_SYSTEM, "please check data");
        	}
        	$timeType = CommonUtil::object2Array($this->config->enums->timeType);
			$customize_apply->expirationAt = strtotime("+ {$validDateEnum[1]} {$timeType[$validDateEnum[0]]}");
        }
        $customize_apply->createAt = $this->request->getPost("createAt");
        $customize_apply->userId = $this->get_user()->id;
        try {
            if (!$customize_apply->save()) {
            	foreach($customize_apply->getMessages() as $message) {
	                return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
            	}
            }
        } catch (\Exception $ex) {
            $manager->rollback($transaction);
            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex);
        }
        //创建申请人的信息
        $customize_apply_info = new CustomizeApplyInfo();
        $customize_apply_info->setTransaction($transaction);
        $customize_apply_info->goodsNum = $this->request->getPost("goodsNum", 'int');
        $customize_apply_info->address = $this->request->getPost("address");
        $customize_apply_info->telephone = $this->request->getPost("telephone");
        $customize_apply_info->contacts = $this->request->getPost("contacts");
        $customize_apply_info->applyId = $customize_apply->id;
        $customize_apply_info->remark = $this->request->getPost("remark");
        $customize_apply_info->userId = $customize_apply->userId;
        try {
            if ($customize_apply_info->save() == null) {
            	foreach($customize_apply_info->getMessages() as $message) {
	                return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
            	}
            }
        } catch (\Exception $ex) {
            $manager->rollback($transaction);
            return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $ex->getMessage());
        }
        $manager->commit();
        //发短信通知
        $mobile = $customize_apply_info->telephone;
        $content = '感谢您的定制申请，已经提交成功！';
        $this->_sendNotice($mobile, $content);
        return ResponseApi::send(null, Message::$_OK, '您的定制申请表已提交，我们将在3天内给予答复！');
    }

    /**
     * 追加定制 DZZQ-03
     */
    public function appendAction()
    {
    	$applyId = $this->request->getPost('applyId');
    	if(!$applyId) {
    		return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法！");
    	}
        $customize_apply = CustomizeApply::findFirstByid($applyId);
        if(!is_object($customize_apply) || !$customize_apply) {
        	return ResponseApi::send(null, Message::$_ERROR_LOGIC, "定制申请不存在，不能追加！");
        }
        if ($customize_apply->expirationAt < time()) {
        	return ResponseApi::send(null, Message::$_ERROR_LOGIC, "定制申请已过期，不能追加！");
        }
		$userId = $this->get_user()->id;
        $customize_apply_info = new CustomizeApplyInfo();
        $customize_apply_info->goodsNum = $this->request->getPost("goodsNum");
        $customize_apply_info->address = $this->request->getPost("address");
        $customize_apply_info->telephone = $this->request->getPost("telephone");
        $customize_apply_info->contacts = $this->request->getPost("contacts");
        $customize_apply_info->applyId = $applyId;
        $customize_apply_info->userId = $userId;
        $customize_apply_info->remark = $this->request->getPost("remark");

        if (!$customize_apply_info->save()) {
            foreach ($customize_apply_info->getMessages() as $message) {
                return ResponseApi::send($message, Message::$_ERROR_SYSTEM);
            }
        }
        //发短信通知
        $mobile = $customize_apply_info->telephone;
        $content = '感谢您的定制申请，已经提交成功！';
        $this->_sendNotice($mobile, $content);
        return ResponseApi::send();
    }

    /**
     * 获得申请单详情--追加申请单调用 DZZQ-04
     */
    public function getAction() {
    	$id = $this->request->get('id', 'int');
    	if(!$id) {
    		return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不存在！");
    	}
		$criteria = CustomizeApply::query();
		$criteria->leftJoin('Region', 'r.id=CustomizeApply.areaId', 'r');
		$criteria->leftJoin('Category', 'c.code=CustomizeApply.categoryNo', 'c');
		$criteria->where('CustomizeApply.id='.$id);
		$criteria->columns(array(
				'c.name categoryName',
				'CustomizeApply.categoryNo',
				'r.name area',
				'CustomizeApply.goodsName',
				'IF(CustomizeApply.thumb LIKE "http://%", CustomizeApply.thumb, CONCAT("' . $this->get_url() . '", CustomizeApply.thumb)) thumb',
				'IF(CustomizeApply.originalImg LIKE "http://%", CustomizeApply.originalImg, CONCAT("' . $this->get_url() . '", CustomizeApply.originalImg)) originalImg',
				'CustomizeApply.goodsModel',
				'CustomizeApply.goodsSpec',
				'CustomizeApply.goodsPrice',
				'CustomizeApply.goodsUnit',
				'CustomizeApply.expirationAt validDateEnum',
				'CustomizeApply.id',
		));
		$result = $criteria->execute();
		$customizeApply = array();
        if (is_object($result) && $result) {
	        $customizeApply = $result->toArray();
	        $customizeApply = $customizeApply[0];
	       	$topCatNo = str_pad(substr($customizeApply['categoryNo'], 0, 2), 8, '0');
			$topCatName = Category::findFirst(array(
				'conditions' => 'code = :code:',
				'bind' => array('code' => $topCatNo),
				'columns' => 'name',
			))->toArray();
			$diffDate = $this->DiffDate(date('Y-m-d', $customizeApply['validDateEnum']), date('Y-m-d'));
			$month = $diffDate['year'] * 12 + $diffDate['month'] + ($diffDate['day'] ? 1 : 0);
			foreach(self::$_expireType as $k => $v) {
				if($k >= $month) {
					$customizeApply['validDateEnum'] = self::$_expireType[$k];
					break;
				}
			}
			$customizeApply['topcatName'] = $topCatName['name'];
        }
        return ResponseApi::send($customizeApply);
    }

    /**
     * 查看我的定制 DZZQ-05
     */
    public function myApplysAction() {
    	$expire = $this->request->get('expire');
    	$categoryNo = $this->request->get('categoryNo');
        $createAt = $this->request->get('createAt', 'int') ?: time() + 10;
    	$size = $this->request->get('size') ?: parent::SIZE;
        $forward = $this->request->get('forward', 'int');
        
        $userId = $this->get_user()->id;
        $builder = $this->modelsManager->createBuilder();
        $builder->from('CustomizeApplyInfo');
        $builder->leftJoin('CustomizeApply', 'CustomizeApply.id=CustomizeApplyInfo.applyId');
        $builder->where('CustomizeApplyInfo.userId = ' . $userId);
        if($categoryNo) {
        	$minimum = $categoryNo;
        	$maximum = (floor($minimum / 10000000) + 1) * 10000000  - 1;
        	$builder->betweenWhere('CustomizeApply.categoryNo', $minimum, $maximum);
        }
        if($expire) {
        	if(array_key_exists($expire, self::$_expire)) {
        		if($expire != 'mout') {
        			$builder->andWhere('expirationAt <= '.strtotime(self::$_expire[$expire]));
        		} else {
        			$builder->andWhere('expirationAt > '.strtotime(self::$_expire[$expire]));
        		}
        	} else {
        		return ResponseApi::send(null, Message::$_ERROR_CODING, "参数不合法！");
        	}
        }
        if($forward) {
        	$builder->andWhere('CustomizeApply.createAt > ' . $createAt);
        	$builder->orderBy('CustomizeApply.createAt');
        } else {
        	$builder->andWhere('CustomizeApply.createAt < ' . $createAt);
	        $builder->orderBy('CustomizeApply.createAt DESC');
        }
        $builder->limit($size);
        $builder->columns("
				CustomizeApply.goodsSpec,
				CustomizeApply.goodsModel,
				CustomizeApply.goodsName,
				CustomizeApplyInfo.goodsNum number,
				REPLACE(FORMAT(CustomizeApply.goodsPrice, 2), ',' , '') goodsPrice,
				CustomizeApply.id,
				CustomizeApplyInfo.state status,
				CustomizeApply.goodsUnit,
    			IF(CustomizeApply.thumb LIKE 'http://%', CustomizeApply.thumb, CONCAT('" . $this->get_url() . "', CustomizeApply.thumb)) thumb,
    			IF(CustomizeApply.originalImg LIKE 'http://%', CustomizeApply.originalImg, CONCAT('" . $this->get_url() . "', CustomizeApply.originalImg)) originalImg,
				CustomizeApply.createAt");
		$result = $builder->getQuery()->execute();
		$myCustomizeApply = array();
        if(is_object($result) && $result->count()) {
        	$myCustomizeApply = $result->toArray();
        	if($forward) {
        		$myCustomizeApply = array_reverse($myCustomizeApply);
        	}
        }
    	return ResponseApi::send($myCustomizeApply);
    }

    /**
     * 提交工程定制申请表 DZZQ-06 
     */
    public function createProjectAction() {
    	if (!$this->request->isPost()) {
    		return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
    	}
    	$userId = $this->get_user()->id;
    	$dataFields = array(
    			'areaId',
    			'name',
    			'address',
    			'period',
    			'amount',
    			'remark',
    			'contactPeople',
    			'position',
    			'contactTelephone',
    			'companyName',
    			'companyAddress'
    	);
    	$customizeProject = new CustomizeProject();
    	foreach($dataFields as $k => $fields) {
    		$$fields = $this->request->getPost($fields);
    		if($k < 9 && !$$fields) {
    			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法！");
    		}
    		$customizeProject->$fields = $$fields;
    	}
    	$customizeProject->userId = $userId;
    	if(!$customizeProject->save()) {
    		foreach($customizeProject->getMessages() as $message) {
    			return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
    		}
    	}
    	return ResponseApi::send();
    }

    /**
     * 查看我的工程定制 DZZQ-07
     */
    public function myProjectAction() {
    	$createAt = $this->request->get('createAt', 'int') ?: time() + 10;
    	$size = $this->request->get('size', 'int') ?: parent::SIZE;
    	$forward = $this->request->get('forward', 'int');
    	$userId = $this->get_user()->id;
    	if($forward) {
    		$conditions = 'CustomizeProject.createAt > :createAt:';
    		$orderBy = 'CustomizeProject.createAt';
    	} else {
    		$conditions = 'CustomizeProject.createAt < :createAt:';
    		$orderBy = 'CustomizeProject.createAt DESC';
    	}
    	
    	$result = CustomizeProject::query()
    	->leftJoin('Region', 'r.id = CustomizeProject.areaId', 'r')
    	->where('CustomizeProject.userId = :userId:', compact('userId'))
    	->andWhere($conditions, compact('createAt'))
    	->orderBy($orderBy)
    	->columns(array('CustomizeProject.id', 
					'r.name area', 
					'CustomizeProject.name', 
					'CustomizeProject.period', 
					'CustomizeProject.amount', 
					'CustomizeProject.contactPeople', 
					'CustomizeProject.contactTelephone', 
					'CustomizeProject.createAt',
    				'CustomizeProject.status'))
		->execute();
    	$myProj = array();
    	if(is_object($result) && $result->count()) {
    		$myProj = $result->toArray();
    		if($forward) {
    			$myProj = array_reverse($myProj);
    		}
    	}
    	return ResponseApi::send($myProj);
    }
    
    /**
     * 查看我的工程定制详情 DZZQ-08
     */
    public function myProjectDetailAction() {
    	$id = $this->request->get('id');
    	if(!$id) {
    		return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不存在！");
    	}
    	$userId = $this->get_user()->id;
    	$result = CustomizeProject::query()
    	->leftJoin('Region', 'r.id = CustomizeProject.areaId', 'r')
    	->where('CustomizeProject.id = :id:', compact('id'))
    	->andWhere('CustomizeProject.userId = :userId:', compact('userId'))
    	->columns(array('CustomizeProject.id',
    			'r.name area',
    			'CustomizeProject.name',
    			'CustomizeProject.address',
    			'CustomizeProject.period',
    			'CustomizeProject.amount',
    			'CustomizeProject.remark',
    			'CustomizeProject.contactPeople',
    			'CustomizeProject.position',
    			'CustomizeProject.contactTelephone',
    			'CustomizeProject.companyName',
    			'CustomizeProject.companyAddress',
    			'CustomizeProject.createAt'))
    	->limit(1)
    	->execute();
    	$projDetail = array();
    	if(is_object($result) && $result->count()) {
    		$projDetail = $result->getFirst()->toArray();
    	} else {
    		return ResponseApi::send(null, Message::$_ERROR_NOFOUND, '要查看的工程定制详情不存在，请稍后再试！');
    	}
    	return ResponseApi::send($projDetail);
    }
    
    /**
     * 计算两个日期时间差
     * @param unknown $date1
     * @param unknown $date2
     * @return multitype:
     */
   	private function DiffDate($date1, $date2) {
		if (strtotime($date1) > strtotime($date2)) {
			$ymd = $date2;
			$date2 = $date1;
			$date1 = $ymd;
		}
		list($y1, $m1, $d1) = explode('-', $date1);
		list($y2, $m2, $d2) = explode('-', $date2);
		$year = $month = $day = $_m = 0;
		$math = ($y2 - $y1) * 12 + $m2 - $m1;
		$year = round($math / 12);
		$month = intval($math % 12);
		$day = (mktime(0, 0, 0, $m2, $d2, $y2) - mktime(0, 0, 0, $m2, $d1, $y2)) / 86400;
		if ($day < 0) {
			$month -= 1;
			$day += date('j', mktime(0, 0, 0, $m2, 0, $y2));
		}
		if($month < 0) {
			$month += 12;
			$year -= 1;
		}
		return compact('year', 'month', 'day');
    }

    /**
     * 提交定制申请成功，发短信通知
     * @param unknown $mobile
     * @param unknown $vcode
     */
    private function _sendNotice($mobile, $content) {
    	$smscfg = $this->config->smscfg;
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
