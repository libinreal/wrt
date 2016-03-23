<?php
header('Content-Type:text/html; charset=utf-8');

class MyExcel 
{
	private $_excel;

	public function __construct() 
	{
		$this->_excel = new PHPExcel();
	}
	
	
	/**
	 * @param $content 对账单数据
	 * 导出客户对账单excel
	 */
	public function customerStatements( $statements ) 
	{

	}


	/**
	 * @param $content 对账单数据
	 * 导出供货商对账单excel
	 */
	public function customerStatements( $statements ) 
	{

	}

	/**
	 * @param $content 对账单数据
	 * 导出项目内部对账单excel
	 */
	public function customerStatements( $statements ) 
	{

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
}