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
	
	
	public function importExcel($entity, $params) 
	{
		if (empty($_FILES)) {
			failed_json('上传文件为空');
		}
		
		//限制上传格式
		$extension = pathinfo($_FILES[$entity]['name'], PATHINFO_EXTENSION);
		if ($extension != 'xlsx') {
			failed_json('只允许上传xlsx格式的文件！');
		}
		
		//上传excel
		require('../includes/cls_image.php');
		$upload = new cls_image();
		$fileName = date('YmdHis').'.xlsx';
		$dirPath = $upload->upload_image($_FILES[$entity], 'goodsExcel', $fileName);
		if ($dirPath === false) {
			failed_json('文件上传失败');
		}
		
		//读取excel
		$data = $this->readExcel('../'.$dirPath);
		if (!$data) failed_json('读取 EXCEL 失败');
		print_r($data);
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