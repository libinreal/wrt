<?php
use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;
use PhpRudder\Http\RequestUtil;

class GlobalController extends ControllerBase
{

	/**
	 * app嗅探网络使用
	 */
    public function indexAction()
    {
    	return ResponseApi::send();
    }


   /**
    * 文件上传
    * @return multitype:int string
    */
    public function uploadAction() {
        if ($this->request->hasFiles() == true) {
            $opath = "";
            $filepath = date("Ym");
            if ($this->request->getPost("flag") == "pri") {
                $storedir = $this->config->storepath->pridir.$filepath;
            } else {
                $storedir = $this->config->storepath->pubdir.$filepath;
                $opath = "pics/" . $filepath . "/";
            }

            if(!is_dir($storedir)) {
            	if(!mkdir($storedir, 777, true)) {
            		return ResponseApi::send(null, Message::$_ERROR_SYSTEM, '目录创建失败，请稍后再试');
            	}
            } else {
            	if(!is_writable($storedir)) {
            		return ResponseApi::send(null, Message::$_ERROR_SYSTEM, '目录不可写，请稍后再试');
            	}
            }

            $files = array();
            // Print the real file names and sizes
            foreach ($this->request->getUploadedFiles() as $file) {
                //Move the file into the application
                $fileType = $file->getRealType();
                if ($fileType == null) {
                    $fileType= $file->getType();
                }
                $tmp = explode("/", $fileType);
                if (is_array($tmp)) {
                    $fileType = $tmp[count($tmp)-1];
                }

                if ($fileType != null) {
                    $filename = time().\PhpRudder\CommonUtil::random(2, '123456789').".".$fileType;
                } else {
                    $filename = time().\PhpRudder\CommonUtil::random(8, '123456789');
                };
                array_push($files, $opath.$filename);

                $file->moveTo($storedir."/".$filename);
            }
            $baseUrl = $this->get_url();

			$files = array_map(function($file) use ($baseUrl) {
				if(preg_match('/^http:\/\//', $file)) {
					return $file;
				}
				return $baseUrl . $file;
			}, $files);
           return ResponseApi::send($files);
        }
        return ResponseApi::send(null, Message::$_ERROR_CODING, "no upload file");
    }

    /**
     * 广告接口 GGJK-04
     * @return array
     */
    public function getAction(){
        $position = $this->request->get('position', 'int');
        if (!$position) {
            return ResponseApi::send(null, Message::$_ERROR_CODING, "输入的数据格式不合法");
        }
        $result = Ad::find(array(
            'conditions' => 'position_id = ?1',
            'bind' => array(1 => $position),
            'columns' => array('IF(adimg LIKE "http://%", adimg, CONCAT("'.$this->get_url().'", adimg)) adimg', 'adlink'),
        ));
        $ads = array();
		if(is_object($result) && $result) {
			$ads = $result->toArray();
		}
        return ResponseApi::send($ads);
    }


    /**
     * 获取全国省直辖市列表 GGJK-05
     */
    public function getProvinceListAction() {
    	$type = $this->request->get('type', 'int') ?: 0;
    	if(!in_array($type, array(0, 1))) {
    		return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法！");
    	}
    	if($type) {
    		$conditions = 'region_type IN (0, 1)';
    	} else {
    		$conditions = 'region_type = 1';
    	}
		$result = Region::find(array(
			'conditions' => $conditions,
			'columns' => array('id', 'name'),
			'order' => 'CHAR_LENGTH(name), id'
		));
		$provinceList = array();
		if(is_object($result) && $result->count()) {
			$provinceList = $result->toArray();
		}
		return ResponseApi::send($provinceList);
    }

    /**
     * 获取厂商列表 GGJK-06
     */
    public function getFactoryListAction() {
        $code = $this->request->get('code', 'int');
        if(!$code){
            return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
        }
        if(substr($code, 2, 4) == '00') {
        	$code = str_pad(substr($code, 0, 2), 8, '_');
        } else {
	        $code = str_pad(substr($code, 0, 4), 8, '_');
        }
		$builder = $this->modelsManager->createBuilder();
		$builder->from('Goods');
		$builder->leftJoin('Brand', 'Brand.id = Goods.factoryId');
		$builder->where("Goods.code LIKE '{$code}'");
		$builder->groupBy('Goods.factoryId');
		$builder->columns('Brand.id, Brand.factoryName');
		$result = $builder->getQuery()->execute();
		$factoryList = array();
		if(is_object($result) && $result->count()) {
			$factoryList = $result->toArray();
		}
        return ResponseApi::send($factoryList);
    }

    /**
     * 发送短信，只限后台调用 GGJK-07
     */
    public function sendSmsAction() {
    	if (!$this->request->isPost()) {
    		return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
    	}
		$mobiles = $this->request->getPost('mobiles');
		$mobiles = explode(',', trim($mobiles, ' ,'));
		$content = $this->request->getPost('content', 'trim');
		$mobiles = array_filter($mobiles, function($mobile) {
				if(preg_match('/^1\d{10}$/', $mobile)) {
					return true;
				}
		});
		$mobiles = array_unique($mobiles);
		$length = mb_strlen($content, 'UTF-8');
		if(count($mobiles) <= 0 || count($mobiles) > 500 || $length <= 0 || $length > 350) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式错误！");
		}
		$mobiles = implode(',', $mobiles);
		$smscfg = $this->config->smscfg;
		$xmlUtil = new XmlUtil();
		$message = '<?xml version="1.0" encoding="UTF-8"?>
				<message>
					<account>'.$smscfg->account.'</account>
					<password>'.strtolower(md5($smscfg->password)).'</password>
					<msgid></msgid>
					<phones>'.$mobiles.'</phones>
					<content>'.$content.'</content>
					<sign></sign>
					<subcode></subcode>
					<sendtime></sendtime>
				</message>';
		try {
			$response = RequestUtil::syncPost($smscfg->apiUrl, compact('message'));
		} catch (\Exception $e) {
			return ResponseApi::send(null, Message::$_ERROR_SYSTEM, $e->getMessage());
		}
		return ResponseApi::send();
    }


    /**
     * 根据code值取得code搜索范围
     */
    private static function getCodeConditions($code) {
        $codes = str_split($code, 2);
        $minCode = $maxCode = '';
        if($codes[count($codes) - 1] != '00') {
            $minCode = $maxCode = $code;
        } else {
            $minCode = $code;
            foreach($codes as $k => $v) {
                if($v == '00') {
                    break;
                }
                $maxCode .= $v;
            }
            $maxCode = str_pad($maxCode, 8, 9);
        }
        return compact('minCode', 'maxCode');
    }
}