<?php
header('Content-Type:text/html; charset=utf-8');

define('IN_ECS', 1);
require(dirname(__FILE__) . '/includes/init.php');

$action = isset($_GET['action']) ? $_GET['action'] : '';
if (!$action) return NotFound('Unknown action');

$output = new OutputE();

switch ($action) {
	case 'goodslist':
		
		$output->opGoodsList();
		return ;
		break;
	default:
		return NotFound();
		break;
}


class OutputE 
{
	private $db;
	private static $excel;
	
	
	public function __construct() 
	{
		$this->db = $GLOBALS['db'];
		self::$excel = new PHPExcel();
	}
	
	
	/**
	 * @todo http://admin.zj.dev/admin/excel.php?action=goodslist
	 * 导出excel
	 */
	public function opGoodsList() 
	{
		$sql = $this->getSQL('OUTPUT_GOODS');
		if (!$sql) exit('准备导出失败');
		$data = $this->db->getAll($sql);
		if (!$data) exit('没有数据');
		
		$goodsId = array();
		foreach ($data as $k=>$v) {
			$goodsId[] = $v['goods_id'];
		}
		$goodsId = array_unique($goodsId);
		if (!$goodsId) exit('查询商品失败');
		
		//厂商编码、物料编码
		$sql = 'SELECT G.goods_id,C.code,B.brand_code,T.cat_id FROM goods AS G LEFT JOIN brand AS B ON B.brand_id=G.brand_id LEFT JOIN category AS C ON C.cat_id=G.cat_id LEFT JOIN goods_type AS T ON T.code=C.code WHERE goods_id IN('.implode(',', $goodsId).')';
		$info = $this->db->getAll($sql);
		
		//规格型号
		$sql = 'SELECT goods_id,attr_value FROM goods_attr WHERE goods_id IN('.implode(',', $goodsId).')';
		$goodsAttr = $this->db->getAll($sql);
		$attributes = array();
		foreach ($goodsAttr as $k=>$v) {
			$attributes[$v['goods_id']][] = $v;
		}
		
		foreach ($attributes as $k=>$v) {
			$string = '';
			foreach ($v as $vk=>$vv) {
				$string .= $vv['attr_value'].'/';
			}
			$attributes[$k] = substr($string, 0, -1);
		}
		
		
		foreach ($data as $k=>$v) {
			foreach ($info as $ik=>$iv) {
				if ($iv['goods_id'] == $v['goods_id']) {
					$data[$k]['brand_code'] = $iv['brand_code'];
					$data[$k]['code'] = $iv['code'];
				}
			}
			
			foreach ($attributes as $sk=>$sv) {
				if ($sk == $v['goods_id']) {
					$data[$k]['attributes'] = $sv;
				} elseif (!isset($data[$k]['attributes'])) {
					$data[$k]['attributes'] = '';
				}
			}
		}
		
		//导出excel
		error_reporting(E_ALL);
		date_default_timezone_set('Asia/Shanghai');
		
		$excel = self::$excel;
		$excel->getProperties()->setCreator('3tichina') //创建人
	        ->setLastModifiedBy('3tichina') //最后修改人
	        ->setTitle('物融通商品列表') //标题
	        ->setSubject('物融通商品列表') //题目
	        ->setDescription('物融通商品列表，导出excel') //描述
	        ->setKeywords('物融通商品导出') //关键字
	        ->setCategory('物融通商品列表导出'); //种类
		
		$excel->setActiveSheetIndex(0)
            ->setCellValue('A1','编号')
            ->setCellValue('B1','商品名称')
            ->setCellValue('C1','规格型号')
            ->setCellValue('D1','厂商编码')
            ->setCellValue('E1','物料编码')
            ->setCellValue('F1','库存数量')
            ->setCellValue('G1','商品报价');
		
        foreach ($data as $k=>$v) {
        	$k += 2;
        	$excel->setActiveSheetIndex(0)->setCellValue('A'.$k, $v['goods_id']);
        	$excel->setActiveSheetIndex(0)->setCellValue('B'.$k, $v['goods_name']);
        	$excel->setActiveSheetIndex(0)->setCellValue('C'.$k, $v['attributes']);
        	$excel->setActiveSheetIndex(0)->setCellValue('D'.$k, $v['brand_code']);
        	$excel->setActiveSheetIndex(0)->setCellValue('E'.$k, $v['code']);
        	$excel->setActiveSheetIndex(0)->setCellValue('F'.$k, $v['goods_number']);
        	$excel->setActiveSheetIndex(0)->setCellValue('G'.$k, $v['shop_price']);
        }
		
		$excel->getActiveSheet()->setTitle('物融通商品列表');
		$excel->setActiveSheetIndex(0);
		
		
		//excel数据
		$filename = urlencode('物融通商品列表').' '.date('Y-m-d H:i:s');
		
		$this->getxlsx($filename, $excel);
		exit();
	}
	
	
	
	/**
	 * 输出xlsx文件
	 * @param string $filename
	 */
	private function getxlsx($filename, $excel) 
	{
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');
		$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$writer->save('php://output');
		return ;
	}
	
	
	
	/**
	 * 输出xls文件
	 * @param string $filename
	 */
	private function getxls($filename, $excel) 
	{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');
		$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
		$writer->save('php://output');
	}
	
	
	
	/**
	 * 获得当前查询sql
	 * @param string $name
	 */
	private function getSQL($name)
	{
		if (!array_key_exists($name, $_COOKIE)) 
			return null;
		$sql = base64_decode($_COOKIE[$name]);
		if (!$sql) return false;
		return $sql;
	}
	
}

function NotFound($message = NULL) 
{
	header('HTTP/1.1 404 Not Found');
	header("status: 404 Not Found");
	if ($message) die($message);
	die('404 Not Found');
}