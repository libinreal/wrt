<?php
/**
 * 授信管理页面
 * API :
 * class Credit
 * @author 
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once('ManageModel.php');
/**
 * 授信单列表
 */
if ($_REQUEST['act'] == 'list') 
{
    $smarty->display('second/credit_list.html');
    exit;
}
/**
 * 授信单详情
 */
elseif ($_REQUEST['act'] == 'detail') 
{
    $smarty->display('second/credit_detail.html');
    exit;
}

/**
 * API Access
 */
//Api 接口列表
$ApiList = array(
    'creditList',
    'creditInfo',
    'creditRemark', 
    'importCredit', 
	'optionXml', 
	'access'
);

/**
 * 授信管理API
 * @author luolu<luolu@3ti.us>
 * API接口访问地址：http://admin.zj.dev/admin/credit_manage.php
 * API接口方法的参数及返回值：
 * @param string $entity api接口参数,数据表名
 * @param array|null $parameters api接口需要的参数，例如：搜索条件等...
 * @return json
 * ----------------------
 */
class Credit extends ManageModel 
{
    protected static $_instance;
    
    protected $table;
    protected $db;
    protected $sql;
    
    
    public function access($entity, $params) 
    {
    	$secret = 'luolu@3tichina.com';
    	
    	$count = count($params);
    	$key = array_keys($params);
    	$string = '';
    	foreach ($key as $v) {
    		$string .= $v;
    	}
    	$strlen = strlen($string);
    	
    	$number = $count*$strlen*41;
    	$token = md5($number);
    	$token .= md5($secret);
    	print_r($token);
    }
    
    
    /**
     * 获取SFTP目录下的xml文件
     * {
     *      "command" : "optionXml", 
     *      "entity"  : "xmlList", 
     *      "parameters" : {}
     * }
     */
    public function optionXml($entity, $params) 
    {
    	self::init($entity, 'xmlList');
    	
    	//SFTP 目录
    	$dir = C('CreditDir');
    	if (!is_dir($dir)) return failed_json('SFTP目录不存在');
    	
    	//读取SFTP目录文件
    	$files = scandir($dir);
    	if (!$files) return failed_json('获取文件列表失败');
    	$fileName = '';
    	foreach ($files as $k=>$v) {
    		$fileName = $dir.'/'.$v;
    		if (is_dir($fileName)) unset($files[$k]);
    		if (pathinfo($v, PATHINFO_EXTENSION) != 'xml') unset($files[$k]);
    	}
    	sort($files);
    	$data = array('dir'=>$dir, 'files'=>$files);
    	make_json_result($data);
    }
    
    
    /**
     * 导入银行xml文件
     * {
     *      "command" : "importCredit",
     *      "entity"  : "(file name)",
     *      "parameters" : {}
     * }
     */
    public function importCredit($entity, $parameters)
    {
    	self::init('bank_credit', 'bank_credit');
    	
    	if (!$entity) return failed_json('没有传参`entity`');
    	
    	$path = C('CreditDir').'/'.$entity;
    	/* 
    	if (!$_FILES) return failed_json('上传文件为空');
    
    	//限制文件格式 xml
    	$extension = pathinfo($_FILES[$entity]['name'], PATHINFO_EXTENSION);
    	if ($extension != 'xml') return failed_json('只允许上传xml格式的文件！');
    
    	//上传文件
    	require('../includes/cls_image.php');
    	$upload = new cls_image();
    	$fileName = date('YmdHis').'.xml';
    	$result = $upload->upload_image($_FILES[$entity], 'credit', $fileName);
    	if ($result === false) return failed_json('上传文件失败');
    
    	//new code
    	$path = '../'.$result;
    	 */
    	
    	return $this->creditXML($path, $fileName);
    }
    
    
    
    /**
     * 授信文件处理
     * @param string $path
     * @param string $filename
     */
    private function creditXML($oldname, $filename) 
    {
    	//$filename = 'CZB_NETCL-INFO_20141112_01.xml';
    	//$oldname = 'E:\project/'.$filename;
    	
    	//$oldname = $pathname;
    	if (!is_file($oldname)) return failed_json('文件错误');
    	
    	$data = $this->loadXML($oldname);
    	if (!$data) return failed_json('无数据');
    	if (!is_array($data)) return failed_json('数据错误');
    	
    	//数据库已经存在的授信记录
    	$this->table = 'bank_credit';
    	self::selectSql(array(
    			'fields' => 'credit_num credit,registration_name regist',
    	));
    	$credits = $this->db->getAll($this->sql);
    	
    	$users = array(); $save = array();
    	$i = 0;
    	foreach ($data as $k=>$v) {
    		//客户号 客户名称 登记机构
    		$users[$k] = array($v[1], $v[2], $v[10]);
    		if (!$credits) break;
    		foreach ($credits as $ck=>$cv) {
    			if ($v[0] == $cv['credit'] && $v[10] == $cv['regist']) {
    				$save[$i] = $v;
    				unset($data[$k]);
    				$i++;
    			}
    		}
    	}
    	
    	//新增授信记录
    	$registNum = '01234567890'; //授信银行号
    	if ($data) {
    		foreach ($data as $k=>$v) {
    			$data[$k] = '("'.implode('","', $v).'","'.$registNum.'","'.time().'")';
    		}
    	}
    	
    	if ($users) $this->addCreditUser($users); //授信文件用户信息
    	if ($save) $this->updateBankCredit($save); //授信文件修改信息
    	if ($data) $this->insertBankCredit($data); //授信文件新增信息
    	
    	
    	//移动导入后的授信文件
    	$usedDir = '../data/credit/used';
    	if (!is_dir($usedDir)) {
    		if (!@mkdir($usedDir, 0777)) return failed_json('创建目录失败');
    	}
    	$newname = $usedDir.'/'.$filename;
    	@rename($oldname, $newname);
    	
    	make_json_result(true);
    }
    
    
    
    /**
     * 读取银行授信xml文件
     * @param string $filename
     * @return array
     */
    private function loadXML($filename) 
    {
    	//$filename = 'E:\project\CZB_NETCL-INFO_20141112_011.xml';
    	
    	if (!is_file($filename)) return failed_json('文件不存在');
    	if (pathinfo($filename, PATHINFO_EXTENSION) != 'xml') return failed_json('文件不是xml文件');
    	
    	$objXML = simplexml_load_file($filename);
    	if ($objXML == false) return failed_json('文件读取失败');
    	$data = $this->toArray($objXML);
    	
    	if (!isset($data['CZB2SINOT'])) return failed_json('`CZB2SINOT`文件数据格式错误');
    	if (!isset($data['CZB2SINOT']['Record'])) return failed_json('`Record`文件数据格式错误');
    	
    	$record = $data['CZB2SINOT']['Record'];
    	foreach ($record as &$v) {
    		if (!isset($v['Property'])) return failed_json('`Property`文件数据格式错误');
    		$v = $v['Property'];
    	}
    	return $record;
    }
    
    
    /**
     * 对象转化成数组
     * @param object $object
     * @return array
     */
    private function toArray($object) 
    {
    	$arr = !is_object($object) ? $object : get_object_vars($object);
    	if (is_array($arr)) {
    		foreach ($arr as $k=>$v) {
    			$arr[$k] = $this->toArray($v);
    		}
    	}
    	return $arr;
    }
    
    
    
    /**
     * 根据授信文件的客户号和登记机构写入users表，创建用户，若存在则修改客户名称companyName
     * @param array $user
     */
    private function addCreditUser($users) 
    {
    	$this->table = 'users';
    	
    	//授信文件中客户号和登记机构的唯一值 查询
    	$condition = array();
    	foreach ($users as $k=>$v) {
    		$users[$k]['id'] = $v[0].$v[2];
    		$condition[] = 'customerNo="'.$v[0].'" AND bank_name="'.$v[2].'"';
    	}
    	$where = implode(' OR ', $condition);
    	
    	//查询存在的授信用户
    	self::selectSql(array(
    			'fields' => array(
    					'user_id', 
    					'customerNo', 
    					'bank_name'
    			), 
    			'where'  => $where
    	));
    	$result = $this->db->getAll($this->sql);
    	if ($result === false)
    		failed_json('查询用户失败');
    	
    	//更新数据$update 添加数据$users
    	$update = array();
    	if ($result) {
    		$id = '';$i = 0;
    		foreach ($result as $k=>$v) {
    			$id = $v['customerNo'].$v['bank_name'];
    			foreach ($users as $uk=>$uv) {
    				if ($id == $uv['id']) {
    					$update[$i]['user_id']     = $v['user_id'];
    					$update[$i]['companyName'] = $uv[1];
    					unset($users[$uk]);
    				}
    				$i++;
    			}
    		}
    	}
    	
    	
    	//update
    	if ($update) {
    		$sql = 'UPDATE '.$this->table.' SET companyName = CASE user_id';
    		$userId = array();
    		foreach ($update as $k=>$v) {
    			$userId[] =  $v['user_id'];
    			$sql .= ' WHEN '.$v['user_id'].' THEN "'.$v['companyName'].'" ';
    		}
    		$sql .= 'END WHERE user_id in('.implode(',', $userId).')';
    		$result = $this->db->query($sql);
    		if ($result === false)
    			failed_json('更新用户资料失败');
    	}
    	
    	
    	//insert
    	if ($users) {
    		$value = array();
    		foreach ($users as $v) {
    			$value[] = '("'.$v[0].'","'.$v[1].'","'.$v[2].'","'.time().'")';
    		}
    		$sql = 'INSERT '.$this->table.' (customerNo,companyName,bank_name,reg_time)values'.implode(',', $value);
    		$result = $this->db->query($sql);
    		if ($result === false)
    			failed_json('写入新用户失败');
    	}
    	
    }
    
    
    
    /**
     * 导入xml时，更新已经存在的协议号信息
     * @param array $update
     */
    private function updateBankCredit($update) 
    {
    	$this->table = 'bank_credit';
        $fields = array(
            'customer_num'      => '', 
            'customer_name'     => '', 
            'papers_type'       => '', 
            'papers_num'        => '', 
            'amount_all'        => '', 
            'amount_remain'     => '', 
            'credit_status'     => '', 
            'start_time'        => '', 
            'end_time'          => '', 
            'registration_name' => '', 
            'create_type'       => ''
        );
        $creditNum = array();
        foreach ($update as $k=>$v) {
            $creditNum[] = '"'.$v[0].'"';
            $fields['customer_num'][]      = 'WHEN "'.$v[0].'" THEN "'.$v[1].'"';
            $fields['customer_name'][]     = 'WHEN "'.$v[0].'" THEN "'.$v[2].'"';
            $fields['papers_type'][]       = 'WHEN "'.$v[0].'" THEN "'.$v[3].'"';
            $fields['papers_num'][]        = 'WHEN "'.$v[0].'" THEN "'.$v[4].'"';
            $fields['amount_all'][]        = 'WHEN "'.$v[0].'" THEN "'.$v[5].'"';
            $fields['amount_remain'][]     = 'WHEN "'.$v[0].'" THEN "'.$v[6].'"';
            $fields['credit_status'][]     = 'WHEN "'.$v[0].'" THEN "'.$v[7].'"';
            $fields['start_time'][]        = 'WHEN "'.$v[0].'" THEN "'.$v[8].'"';
            $fields['end_time'][]          = 'WHEN "'.$v[0].'" THEN "'.$v[9].'"';
            $fields['registration_name'][] = 'WHEN "'.$v[0].'" THEN "'.$v[10].'"';
            $fields['create_type'][]       = 'WHEN "'.$v[0].'" THEN "'.$v[11].'"';
        }
        $str = '';
        foreach ($fields as $k=>$v) {
            $str .= $k.'=CASE credit_num';
            foreach ($v as $vk=>$vv) {
                $str .= ' '.$vv.' ';
            }
            $str .= ' END, ';
        }
        $str = substr($str, 0, -2);
        $sql = 'UPDATE '.$this->table.' SET '.$str.' WHERE credit_num in('.implode(',', $creditNum).')';
        $res = $this->db->query($sql);
        if ($res === false) {
            failed_json('导入xml失败，更新已经存在的记录失败');
        }
    }
    
    
    
    /**
     * 导入xml时，添加不存在的协议号信息
     * @param array $insert
     */
    private function insertBankCredit($insert) 
    {
    	$this->table = 'bank_credit';
        $fields = array(
            'credit_num',
            'customer_num',
            'customer_name',
            'papers_type',
            'papers_num',
            'amount_all',
            'amount_remain',
            'credit_status',
            'start_time',
            'end_time',
            'registration_name',
            'create_type',
            'registration_num',
            'add_time'
        );
        $fields = '('.implode(',', $fields).')';
        $values = implode(',', $insert);
        $sql = 'INSERT '.$this->table.' '.$fields.'values'.$values;
        $res = $this->db->query($sql);
        if ($res === false) {
            failed_json('导入失败');
        }
    }
    
    
    
    /**
     * 银行授信列表
     * {
     *      "command" : "creditList",
     *      "entity"  : "bank_credit",
     *      "parameters" : {
     *          "params" : {
     *              "limit" : "(int)",
     *              "offset": "(int)"
     *          }
     *      }
     * }
     */
    public function creditList($entity, $parameters) 
    {
        self::init($entity, 'bank_credit');
    
        $config = $this->creditConf();
    
        //page
        $params = $parameters['params'];
        if (is_numeric($params['limit']) && is_numeric($params['offset'])) {
            $page = intval($params['limit']);
            if ($page < 0) $page = 0;
            $offset = intval($params['offset']);
            if ($offset < 0) $offset = 0;
            $limit = 'limit '.$page.','.$offset;
        }
    
        self::selectSql(array(
            'fields' => array(
                'credit_id', 
                'credit_num',
                'customer_num',
                'customer_name',
                'amount_all',
                'amount_remain',
                'credit_status',
                'start_time',
                'end_time',
                'registration_name',
               // 'create_type'
            ),
            'extend' => 'ORDER BY add_time DESC '.$limit
        ));
        $res = $this->db->getAll($this->sql);
        if ($res === false) {
            failed_json('获取列表失败');
        }
        foreach ($res as $k=>$v) {
            $res[$k]['credit_status'] = $config['creditStatus'][$v['credit_status']];
            //$res[$k]['create_type']   = $config['creditType'][$v['create_type']];
        }
        
        self::selectSql(array(
            'fields' => 'COUNT(*) AS num',
        ));
        $total = $this->db->getOne($this->sql);
        if ($total === false) {
            failed_json('获取总记录数失败');
        }
        make_json_result(array('total'=>$total, 'data'=>$res));
    }
    
    
    /**
     * 授信详细信息
     * {
     *      "command" : "creditInfo",
     *      "entity"  : "bank_credit",
     *      "parameters" : {
     *          "credit_id" : "(int)"
     *      }
     * }
     */
    public function creditInfo($entity, $parameters) 
    {
        self::init($entity, 'bank_credit');
    
        $creditId = intval($parameters['credit_id']);
        if (!$creditId) failed_json('没有传参`credit_id`');
    
        $config = $this->creditConf();
    
        self::selectSql(array(
            'fields' => '*',
            'where'  => 'credit_id='.$creditId,
        ));
        $res = $this->db->getRow($this->sql);
        if ($res === false) {
            failed_json('获取信息失败');
        }
        $res['credit_status'] = $config['creditStatus'][$res['credit_status']];
        $res['add_time'] = date('Y/m/d', strtotime($res['add_time']));
        $res['end_time'] = date('Y/m/d', $res['end_time']);
        make_json_result($res);
    }
    
    
    /**
     * 银行授信信息备注
     * {
     *      "command" : "creditRemark",
     *      "entity"  : "bank_credit",
     *      "parameters" : {
     *          "credit_id" : "(int)"
     *          "params" : {
     *              "remark" : "(string)"
     *          }
     *      }
     * }
     */
    public function creditRemark($entity, $parameters) 
    {
        self::init($entity, 'bank_credit');
    
        $creditId = intval($parameters['credit_id']);
        if (!$creditId) failed_json('没有传参`credit_id`');
    
        $remark = htmlspecialchars(trim($parameters['params']['remark']));
        if (!$remark) make_json_result(true);
    
        $sql = 'UPDATE '.$this->table.' SET remark="'.$remark.'" WHERE credit_id='.$creditId;
        $res = $this->db->query($sql);
        if ($res === false) {
            failed_json('备注失败');
        } else {
            make_json_result($res);
        }
    }
    
    
    /**
     * 银行授信配置
     * @return array $config
     */
    private function creditConf() 
    {
        $config = require_once('bankCredit_config.php');
        return $config;
    }
    
    
    /**
     * 导入授信文件副本
     */
    public function importXMLBAK() 
    {
    	self::init('bank_credit', 'bank_credit');
    	 
    	if (!$entity) return failed_json('没有传参`entity`');
    	if (!$_FILES) return failed_json('上传文件为空');
    	
    	//限制文件格式 xml
    	$extension = pathinfo($_FILES[$entity]['name'], PATHINFO_EXTENSION);
    	if ($extension != 'xml') return failed_json('只允许上传xml格式的文件！');
    	
    	//上传文件
    	require('../includes/cls_image.php');
    	$upload = new cls_image();
    	$fileName = date('YmdHis').'.xml';
    	$result = $upload->upload_image($_FILES[$entity], 'credit', $fileName);
    	if ($result === false) return failed_json('上传文件失败');
    	
    	//old code
    	$path = '../'.$result; //'../data/credit/'.$fileName;
    	@$xml = simplexml_load_file($path);
    	if ($xml == false) {
    		failed_json('文件格式错误');
    	}
    	
    	$registrationNum = '01234567890'; //授信银行号
    	//xml所有协议号
    	$protocols = array();
    	foreach ($xml->CZB2SINOT->Record as $v){
    		$protocols[] = $v->Property->__toString();
    	}
    	
    	if (empty($protocols)) {
    		make_json_result(true);
    	}
    	
    	//数据库所有协议号
    	self::selectSql(array(
    			'fields' => 'credit_num',
    	));
    	$had = $this->db->getAll($this->sql);
    	if (empty($had)) {
    		$diff = $protocols;
    	} else {
    		//获取一定格式的数组
    		$exist = array();
    		foreach ($had as $k=>$v) {
    			$exist[] = $v['credit_num'];
    		}
    		unset($had);
    	
    		$intersect = array_intersect($protocols, $exist);   //修改的交集数据
    		$diff = array_diff($protocols, $exist);     //添加的差集数据
    	
    		if (empty($intersect) && empty($diff)) {
    			make_json_result(true);
    		}
    	
    	}
    	
    	$update = array();  //需要修改的数据集合
    	$insert = array();  //需要添加的数据集合
    	$users  = array();  //需要创建或修改的用户集合
    	$allowUser = array(
    			'CUSTOMERID', 'CUSTOMERNAME', 'INPUTORG'
    	);
    	
    	//区分数据操作
    	$cl = '';$n = 0;$i = 0;$j = 0;
    	foreach ($xml->CZB2SINOT->Record as $v) {
    		$cl = $v->Property->__toString();
    	
    		foreach ($v->Property as $pv) {
    			if (in_array($pv->attributes()->name->__toString(), $allowUser)) {
    				$users[$j][] = $pv->__toString();
    			}
    		}
    		$j++;
    		//修改
    		if (!empty($intersect)) {
    			if (in_array($cl, $intersect)) {
    				$str = '';
    				foreach ($v->Property as $pv) {
    					$str .= '"'.$pv->__toString().'",';
    					$update[$n][] = $pv->__toString();
    				}
    				$n++;
    			}
    		}
    	
    		//添加
    		if (!empty($diff)) {
    			if (in_array($cl, $diff)) {
    				$str = '';
    				foreach ($v->Property as $pv) {
    					$str .= '"'.$pv->__toString().'",';
    					$insert[$i] = "(".$str.'"'.$registrationNum.'","'.time().'"'.")";
    				}
    				$i++;
    			}
    		}
    	}
    	
    	//写入用户表
    	if (!empty($users)) {
    		$this->addCreditUser($users);
    	}
    	
    	//修改操作
    	if (!empty($update)) {
    		$this->updateBankCredit($update);
    	}
    	
    	//添加操作
    	if (!empty($insert)) {
    		$this->insertBankCredit($insert);
    	}
    	
    	$newDir = '../data/credit/used/';
    	if (!file_exists($newDir) && mkdir($newDir)) {
    		@rename($path, $newDir.$fileName);
    	} elseif (file_exists($newDir)) {
    		@rename($path, $newDir.$fileName);
    	}
    	
    	make_json_result(true);
    }
    
    
}
if ($_POST['command'] == 'importCredit') {
    $credit = Credit::getIns();
    $credit->run(array(
        'command'=> $_POST['command'], 
        'entity' => $_POST['entity'], 
        'parameters' => $_POST['parameters']
    ));
} else {
    $json = jsonAction($ApiList);
    $credit = Credit::getIns();
    $credit->run($json);
}
