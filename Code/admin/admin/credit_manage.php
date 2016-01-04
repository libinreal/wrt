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
    'test'
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
    
    
    /**
     * 导入银行xml文件
     * {
     *      "command" : "importCredit", 
     *      "entity"  : "(input name)", 
     *      "parameters" {}
     * }
     */
    public function importCredit($entity, $parameters) 
    {
        self::init('bank_credit', 'bank_credit');
        
        if (empty($entity) || empty($_FILES)) {
            failed_json('没有传参`entity`，或者上传错误');
        }
        
        //限制上传格式
        $file = pathinfo($_FILES[$entity]['name']);
        if ($file['extension'] != 'xml') {
            failed_json('只允许上传xml格式的文件！');
        }
        //upload
        require('../includes/cls_image.php');
        $upload = new cls_image();
        $fileName = date('YmdHis').'.xml';
        $res = $upload->upload_image($_FILES[$entity], 'credit', $fileName);
        if ($res === false) {
            failed_json('文件上传失败');
        }
        
        //load xml
        $registrationNum = 'CZB111';
        $path = '../data/credit/'.$fileName;
        $xml = simplexml_load_file($path);
        
        
    //所有的协议号
        $protocols = array();
        foreach ($xml->CZB2SINOT->Record as $v){
            $protocols[] = $v->Property->__toString();
        }
        
        //数据库已经存在的协议号
        self::selectSql(array(
            'fields' => 'credit_num', 
        ));
        $had = $this->db->getAll($this->sql);
        $exist = array();
        foreach ($had as $k=>$v) {
            $exist[] = $v['credit_num'];
        }
        unset($had);
        
        $intersect = array_intersect($protocols, $exist);   //交集
        $diff = array_diff($protocols, $exist);     //差集
        
        $update = array();  //更新数组
        $insert = array();  //添加数组
        $cl = '';$n = 0;$i = 0;
        foreach ($xml->CZB2SINOT->Record as $v) {
            $cl = $v->Property->__toString();
            //修改的数据
            if (in_array($cl, $intersect)) {
                $str = '';
                foreach ($v->Property as $pv) {
                    $str .= '"'.$pv->__toString().'",';
                    $update[$n][] = $pv->__toString();
                }
                $n++;
            }
            //添加的数据
            if (in_array($cl, $diff)) {
                $str = '';
                foreach ($v->Property as $pv) {
                    $str .= '"'.$pv->__toString().'",';
                    $insert[$i] = "(".$str.'"'.$registrationNum.'","'.time().'"'.")";
                }
                $i++;
            }
        }
        //修改操作
        
        //添加操作
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
        } else {
            @unlink($path);
            make_json_result($res);
        }
    }
    
    public function test()
    {
        self::init('bank_credit', 'bank_credit');
        $registrationNum = 'CZB1112';
        $path = '../data/credit/20160104181402.xml';
        $xml = simplexml_load_file($path);
        
        //所有的协议号
        $protocols = array();
        foreach ($xml->CZB2SINOT->Record as $v){
            $protocols[] = $v->Property->__toString();
        }
        
        //数据库已经存在的协议号
        self::selectSql(array(
            'fields' => 'credit_num', 
        ));
        $had = $this->db->getAll($this->sql);
        $exist = array();
        foreach ($had as $k=>$v) {
            $exist[] = $v['credit_num'];
        }
        unset($had);
        
        $intersect = array_intersect($protocols, $exist);   //已经存在的
        $diff = array_diff($protocols, $exist);     //需要添加的
        
        $update = array();  //更新数组
        $insert = array();  //添加数组
        $cl = '';$n = 0;$i = 0;
        foreach ($xml->CZB2SINOT->Record as $v) {
            $cl = $v->Property->__toString();
            if (in_array($cl, $intersect)) {
                $str = '';
                foreach ($v->Property as $pv) {
                    $str .= '"'.$pv->__toString().'",';
                    $update[$n][] = $pv->__toString();
                }
                $n++;
            }
            if (in_array($cl, $diff)) {
                $str = '';
                foreach ($v->Property as $pv) {
                    $str .= '"'.$pv->__toString().'",';
                    $insert[$i] = "(".$str.'"'.$registrationNum.'","'.time().'"'.")";
                }
                $i++;
            }
        }
        print_r($update);
        
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
            $offset = intval($params['offset']);
            $limit = 'limit '.($page * $offset).','.$offset;
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
                'create_type'
            ),
            'extend' => 'ORDER BY add_time ASC '.$limit
        ));
        $res = $this->db->getAll($this->sql);
        if ($res === false) {
            failed_json('获取列表失败');
        }
        foreach ($res as $k=>$v) {
            $res[$k]['credit_status'] = $config['creditStatus'][$v['credit_status']];
            $res[$k]['create_type']   = $config['creditType'][$v['create_type']];
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
            'fields' => array(
                'credit_id',
                'add_time',
                'customer_name',
                'amount_all',
                'credit_status',
                'registration_name',
                'end_time',
                'registration_num',
                'customer_bank_num',
                'remark'
            ),
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
    
        $sql = 'UPDATE '.$this->table.' SET remark='.$remark.' WHERE credit_id='.$creditId;
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
