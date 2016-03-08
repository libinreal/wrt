<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once('ManageModel.php');

class Goods extends ManageModel 
{
	protected static $_instance;
	
	protected $table;
	protected $db;
	protected $sql;
	
	
	/***
	 * @todo http://admin.zj.dev/admin/goods_manage.php
	 * 商品导入excel
	 * {
     *      "command" : "importExcel", 
     *      "entity"  : "(input name)", 
     *      "parameters" : {}
     * }
	 */
	public function importExcel($entity, $params) 
	{
		if (empty($_FILES)) return failed_json('上传文件为空');
		
		//限制文件格式 xlsx
		$extension = pathinfo($_FILES[$entity]['name'], PATHINFO_EXTENSION);
		if ($extension != 'xlsx') return failed_json('只允许上传xlsx格式的文件！');
		
		//上传excel
		require('../includes/cls_image.php');
		$upload = new cls_image();
		$fileName = date('YmdHis').'.xlsx';
		$dirPath = $upload->upload_image($_FILES[$entity], 'goodsExcel', $fileName);
		if ($dirPath === false) return failed_json('文件上传失败');
		
		//读取excel
		$data = $this->readExcel('../'.$dirPath);
		//$data = $this->readExcel('../data/goodsExcel/20160304103136.xlsx');
		if (!$data) failed_json('读取 EXCEL 失败');
		
		
		self::init('', ''); //实例化类
		//供应商id
		$supplierId = $_SESSION['suppliers_id'];
		if (!$supplierId) {
			return failed_json('当前登录用户不是供应商');
		}
		
		/* 
		//管理员id
		$userId = $_SESSION['admin_id'];
		if (!$userId) return failed_json('请登录');
		 */
		
		//查询供应商下的商品
		$this->table = 'goods';
		self::selectSql(array(
				'fields' => array('goods_id'), 
				'where'  => 'suppliers_id='.$supplierId
		));
		$goods = $this->db->getAll($this->sql);
		if ($goods === false) return failed_json('查询失败');
		if (!$goods) return failed_json('当前登录供应商没有所属商品信息');
		foreach ($goods as &$id) {
			$id = $id['goods_id'];
		}
		
		//筛选可以进行修改商品信息
		$info = array();$i = 0;
		foreach ($data as $k=>$v) {
			foreach ($goods as $gk=>$gv) {
				if ($gv == $v[0]) {
					$info[$i] = $v;
					$i++;
				}
			}
		}
		
		if (empty($info)) return failed_json('没有找到符合该用户的商品信息');
		
		//商品导入修改信息
		$sql = 'UPDATE goods SET goods_name= CASE goods_id';
		$goodsNameSql = ''; $goodsNumberSql = ''; $goodsPriceSql = '';
		foreach ($info as $k=>$v) {
			$goodsNameSql .= ' WHEN '.$v[0].' THEN "'.$v[1].'" ';
			$goodsNumberSql .= ' WHEN '.$v[0].' THEN "'.$v[5].'"';
			$goodsPriceSql .= ' WHEN '.$v[0].' THEN "'.$v[6].'"';
		}
		
		$sql .= $goodsNameSql.' END, goods_number= CASE goods_id'.$goodsNumberSql.' END, shop_price= CASE goods_id'.$goodsPriceSql.' END ';
		$sql .= 'WHERE goods_id IN('.implode(',', $goods).')';
		$result = $this->db->query($sql);
		if (!$result) failed_json('更新数据失败');
		make_json_result($result);
	}
	
	
	
	/**
	 * 读取excel
	 * @param string $fileName
	 */
	private function readExcel($fileName) 
	{
		$xlsx = PHPExcel_IOFactory::load($fileName);
		
		$sheet              = $xlsx->getActiveSheet();
		$highestRow         = $sheet->getHighestRow();
		$highestColumn      = $sheet->getHighestColumn();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		$excelData = array();
		for ($row = 1; $row <= $highestRow; $row++) {
			for ($col = 0; $col < $highestColumnIndex; $col++) {
				$excelData[$row][] =(string)$sheet->getCellByColumnAndRow($col, $row)->getValue();
			}
		}
		unset($excelData[1]);
		return $excelData;
	}
	
}
$goods = Goods::getIns();
$goods->run(array(
        'command'=> $_POST['command'], 
        'entity' => $_POST['entity'], 
        'parameters' => $_POST['parameters']
    ));