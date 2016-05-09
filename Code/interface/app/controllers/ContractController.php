<?php
/**
 * 我的合同
 * @author
 */
use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class ContractController extends ControllerBase
{
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }


    /**
     * 我的合同
     * @return
     */
    public function getListAction()
    {
    	$userinfo = $this->get_user();
    	if (!$userinfo) return ResponseApi::send(null, -1, '未登录');

    	$customerId = $this->get_user()->id;
        $size = $this->request->get('size', 'int') ?: parent::SIZE;
        $currentId = $this->request->get('contract_id', 'int') ?: 0;
        $forward = $this->request->get('forward', 'int');

        //
        $contractSN = $this->request->get('contract_sn');
        $contractName = $this->request->get('contract_name');
        $startTime = $this->request->get('start');
        $endTime = $this->request->get('end');

        if (!$customerId) {
        	return ResponseApi::send('', -1, '合同不存在！');
        }

        //如果是总账号 会选择旗下的所有子帐号
        $result = Users::find(array(
        		'conditions' => 'parent_id = '.$customerId.' or id='.$customerId,
        		'columns' => 'id, companyName'
        ));
        $user = array();
        if(is_object($result) && $result->count()) {
        	$user = $result->toArray();
        }

        //所有用户id
        $userId = array();
        $userIdName = array();
        foreach ($user as $v) {
        	$userId[] = $v['id'];
            $userIdName[$v['id']] = $v['companyName'];
        }
        if (!$userId) {
        	return ResponseApi::send(array());
        }

        //总账号显示 总账号与子帐号的所有合同
        $condition = 'userId in('.implode(',', $userId).')';
        $condition .= ' AND reviewStatus=1';//状态是审核通过
        if ($forward || !$currentId) {
        	//上一页操作 或 第一页操作
        	if (!empty($condition)) $condition .= ' AND ';
        	$condition .= 'id>"'.$currentId.'"';
        } else {
        	//下一页操作
        	if (!empty($condition)) $condition .= ' AND ';
        	$condition .= 'id<"'.$currentId.'"';
        }

        //搜索
        if ($contractSN) {
        	if (!empty($condition)) $condition .= ' AND ';
        	$condition .= 'num LIKE "%'.$contractSN.'%"';
        }
        if ($contractName) {
        	if (!empty($condition)) $condition .= ' AND ';
        	$condition .= 'name LIKE "%'.$contractName.'%"';
        }
        if ($startTime) {
        	if (!empty($condition)) $condition .= ' AND ';
        	$condition .= 'startTime >= '.strtotime($startTime);
        }
        if ($endTime) {
        	if (!empty($condition)) $condition .= ' AND ';
        	$condition .= 'endTime <= '.strtotime($endTime);
        }


        $data = ContractModel::query()
        		->where($condition)
        		->order('id DESC')
        		->limit($size)
        		->execute()
        		->toArray();
        if( $data ){
            foreach ($data as &$v) {
                $v['companyName'] = $userIdName[$v['userId']];
            }
            unset( $v );
        }
        return ResponseApi::send($data);
    }


    /**
     * 合同详情
     * @return array
     */
    public function getSingleAction()
    {
    	$userinfo = $this->get_user();
    	if (!$userinfo) return ResponseApi::send(null, -1, '未登录');

    	error_reporting(E_ALL^E_NOTICE);
    	$contractId = $this->request->get('contract_id', 'int') ?: 0;
    	$userId = $this->get_user()->id;
    	if (!$contractId || !$userId) {
    		return ResponseApi::send(null, -1, '合同不存在！');
    	}

    	//如果是总账号 会选择旗下的所有子帐号
    	$result = Users::find(array(
    			'conditions' => 'parent_id = '.$userId.' or id='.$userId,
    			'columns' => 'id'
    	));
    	$user = array();
    	if(is_object($result) && $result->count()) {
    		$user = $result->toArray();
    	}

    	//所有用户id
    	$userId = array();
    	foreach ($user as $v) {
    		$userId[] = $v['id'];
    	}

    	//查询合同
    	$data = ContractModel::findFirst(array(
    			'conditions' => 'id='.$contractId.' AND userId in('.implode(',', $userId).')'
    	));
    	if (!$data) {
    		return ResponseApi::send(null, -1, '该合同不存在！');
    	}

    	//合同下的物料类型
    	$category = ContractCategory::query();
    	$category->leftjoin('Category', 'C.id=ContractCategory.category_id', 'C');
    	$category->where('ContractCategory.contract_id='.$contractId);
    	$category->columns('
    			C.id cat_id,
    			C.name cat_name
    		');
    	$result = $category->execute();
    	$category = $result->toArray();


    	//解析数据
    	$status = array('作废', '生效');
    	$type = array('', '销售合同', '采购合同');
    	$signType = array('平台到银行', '银行到平台');

    	if ($data->endTime < time()) {
    		$data->status = '过期';
    	} else {
    		$data->status = $status[$data->status];
    	}
    	$data->type   = $type[$data->type];
    	$data->signType = $signType[$data->signType];
    	$data->startTime = date('Y-m-d', $data->startTime);
    	$data->endTime = date('Y-m-d', $data->endTime);
    	$data->createTime = date('Y-m-d', $data->createTime);
    	$data = $data->toArray();
    	$data['category'] = $category;

    	return ResponseApi::send($data);
    }



    /**
     * 合同详情附件下载
     */
    public function downloadPdfAction()
    {
    	$userinfo = $this->get_user();
    	if (!$userinfo) return ResponseApi::send(null, -1, '未登录');

    	$fileName = $this->request->get('url');
    	if (!$fileName) {
    		exit('抱歉，无法获取文件名！');
    	}

    	//文件路径
    	$baseDir = pathinfo(getcwd(), PATHINFO_DIRNAME);
    	$int = strpos($baseDir, 'interface');
    	$dir = substr($baseDir, 0, $int);
    	$dirName = $dir.'admin\data\contract\\'.$fileName;
    	if (!file_exists($dirName)) {
    		exit('抱歉，文件不存在！');
    	}

    	//下载文件
    	$file = fopen($dirName, 'r');
    	Header("Content-type: application/octet-stream");
    	Header("Accept-Ranges: bytes");
    	Header("Accept-Length: ".filesize($dirName));
    	Header("Content-Disposition: attachment; filename=" . $fileName);
    	echo fread($file,filesize($dirName));
    	fclose($file);
    	exit;
    }
}