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
	public function customerStatements( $statements ) {
		$this->_excel->getProperties()->setCreator('3tichina') //创建人
	        ->setLastModifiedBy('3tichina') //最后修改人
	        ->setTitle('物融通下游客户对账单') //标题
	        ->setSubject('物融通下游客户对账单') //题目
	        ->setDescription('物融通下游客户对账单，导出excel') //描述
	        ->setKeywords('物融通下游客户对账单导出') //关键字
	        ->setCategory('物融通下游客户对账单导出'); //种类
		
	    $data_row = $statements['total'] + 2;
	    $data_col = 'M';

	    $objWorkSheet = $this->_excel->setActiveSheetIndex(0);
	    //宽高
		$objWorkSheet->getColumnDimension('A')->setWidth(21);
		$objWorkSheet->getColumnDimension('B')->setWidth(21);
		$objWorkSheet->getColumnDimension('C')->setWidth(21);
		$objWorkSheet->getColumnDimension('D')->setWidth(21);
		$objWorkSheet->getColumnDimension('E')->setWidth(21);
		$objWorkSheet->getColumnDimension('F')->setWidth(21);
		$objWorkSheet->getColumnDimension('G')->setWidth(21);
		$objWorkSheet->getColumnDimension('H')->setWidth(21);
		$objWorkSheet->getColumnDimension('I')->setWidth(21);
		$objWorkSheet->getColumnDimension('J')->setWidth(21);
		$objWorkSheet->getColumnDimension('K')->setWidth(21);
		$objWorkSheet->getColumnDimension('L')->setWidth(21);
		$objWorkSheet->getColumnDimension('M')->setWidth(21);
		$objWorkSheet->getColumnDimension('N')->setWidth(21);

		$objWorkSheet->getRowDimension('1')->setRowHeight(48);
		$objWorkSheet->getRowDimension('2')->setRowHeight(30.7);
		// $objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A1'), 'B1:J'.$data_row );
		
		$objWorkSheet->mergeCells('A1:M1');
		
		//对齐方式

		//标题 、表头、数据 对齐
		$objWorkSheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  		$objWorkSheet->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  		$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A1'), 'B1:N1' );

  		$objWorkSheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  		$objWorkSheet->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A2'), 'B2:N2' );

		$objWorkSheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  		$objWorkSheet->getStyle('A3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A3'), 'A3:N'.($data_row + 2) );

		//字体
		$objWorkSheet->getStyle('A1')->getFont()->setName('宋体');
		$objWorkSheet->getStyle('A1')->getFont()->setSize(14);
		$objWorkSheet->getStyle('A1')->getFont()->setBold(true);

		$objWorkSheet->getStyle('A2')->getFont()->setName('宋体');
		$objWorkSheet->getStyle('A2')->getFont()->setSize(11);
		$objWorkSheet->getStyle('A2')->getFont()->setBold(true);		
		$objWorkSheet->getStyle('A2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
		
		//设置边框
		$styleThinBlackBorderOutline = array(
		       'borders' => array (
		             'bottom' => array (
		                   'style' => PHPExcel_Style_Border::BORDER_THIN,   //设置border样式
		                   'color' => array ( PHPExcel_Style_Color::COLOR_BLACK ),          //设置border颜色
		            ),
		      ),
		);



		$objWorkSheet->getStyle( 'A2:N2' )->applyFromArray($styleThinBlackBorderOutline);

		//背景填充
		$objWorkSheet->getStyle( 'A2:N2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objWorkSheet->getStyle( 'A2:N2')->getFill()->getStartColor()->setARGB('00C0C0C0');
		$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A2'), 'B2:N2');

		$objWorkSheet->setCellValue('A1',"\"对账单" . $statements['contract_name'] ."—".$statements['contract_sn']."\r\n对账日期".$statements['dates'] . "\"");

		//文本格式
		$objWorkSheet->getStyle('C3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$objWorkSheet->getStyle('D3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$objWorkSheet->getStyle('G3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		
		$objWorkSheet->getStyle('I3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		$objWorkSheet->getStyle('J3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		$objWorkSheet->getStyle('K3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		$objWorkSheet->getStyle('L3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		$objWorkSheet->getStyle('M3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

		$objWorkSheet->getStyle('N3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('C3'), 'C3:C'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('D3'), 'D3:D'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('G3'), 'G3:G'.$data_row );

		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('H3'), 'H3:H'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('I3'), 'I3:I'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('J3'), 'J3:J'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('K3'), 'K3:K'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('L3'), 'L3:L'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('M3'), 'M3:M'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('N3'), 'N3:N'.$data_row );

        $objWorkSheet->setCellValue('A2','下单日期')
        	->setCellValue('B2','订单编号')
        	->setCellValue('C2','客户名称')
            ->setCellValue('D2','产品编号')
            ->setCellValue('E2','产品名称')
            ->setCellValue('F2','型号')
            ->setCellValue('G2','发货数量')
            ->setCellValue('H2','计量单位')
            ->setCellValue('I2','单价(人民币：元)')
            ->setCellValue('J2','票据占用天数')
            ->setCellValue('K2','金融费(人民币：元)')
            ->setCellValue('L2','物流费(人民币：元)')
            ->setCellValue('M2','货款额(人民币：元)')
            ->setCellValue('N2','对应操作帐号');

        

		foreach ($statements['data'] as $k=>$row) {
			$k += 3;
			$objWorkSheet->getRowDimension($k)->setRowHeight(24);
			$objWorkSheet->setCellValue('A'.$k, $row['add_date']);	
			$objWorkSheet->setCellValue('B'.$k, $row['order_sn']);	
			$objWorkSheet->setCellValue('C'.$k, $row['customer_name']);	
			$objWorkSheet->setCellValueExplicit('D'.$k, $row['goods_sn'], PHPExcel_Cell_DataType::TYPE_STRING);	
			$objWorkSheet->setCellValue('E'.$k, $row['goods_name']);	
			$objWorkSheet->setCellValue('F'.$k, $row['attr']);	
			$objWorkSheet->setCellValue('G'.$k, $row['goods_number_arr_buyer']);	
			$objWorkSheet->setCellValue('H'.$k, $row['unit']);	
			$objWorkSheet->setCellValue('I'.$k, $row['goods_price_arr_buyer']);	
			$objWorkSheet->setCellValue('J'.$k, $row['bill_used_days']);	
			$objWorkSheet->setCellValue('K'.$k, $row['financial_arr']);	
			$objWorkSheet->setCellValue('L'.$k, $row['shipping_fee_arr_buyer']);	
			$objWorkSheet->setCellValue('M'.$k, $row['order_amount_arr_buyer']);	
			$objWorkSheet->setCellValue('N'.$k, $row['user_account']);	
		}
		
		$k++;
		$objWorkSheet->mergeCells('A'.$k.':L'.$k);	
		$objWorkSheet->setCellValue('A'.$k, '合计发货数量');
		$objWorkSheet->setCellValue('M'.$k, '发货数量');
		$objWorkSheet->setCellValue('N'.$k, $statements['count_total']);
		$objWorkSheet->getRowDimension($k)->setRowHeight(24);

		$k++;
		$objWorkSheet->mergeCells('A'.$k.':L'.$k);
		$objWorkSheet->setCellValue('A'.$k, '合计发货金额');
		$objWorkSheet->setCellValue('M'.$k, '¥');
		$objWorkSheet->setCellValue('N'.$k, $statements['amount_total']);
		$objWorkSheet->getRowDimension($k)->setRowHeight(24);

		$this->_excel->setActiveSheetIndex(0);
		$objWorkSheet->setTitle('物融通下游客户对账单');
		//excel数据
		$filename = $statements['dates'].'_customer_stat';
		
		$this->getxlsx($filename, $this->_excel);
	}


	/**
	 * @param $content 对账单数据
	 * 导出供货商对账单excel
	 */
	public function suppliersStatements( $statements ) {
		$this->_excel->getProperties()->setCreator('3tichina') //创建人
	        ->setLastModifiedBy('3tichina') //最后修改人
	        ->setTitle('物融通供应商对账单') //标题
	        ->setSubject('物融通供应商对账单') //题目
	        ->setDescription('物融通供应商对账单，导出excel') //描述
	        ->setKeywords('物融通供应商对账单导出') //关键字
	        ->setCategory('物融通供应商对账单导出'); //种类
		
	    $data_row = $statements['total'] + 2;
	    $data_col = 'I';

	    $objWorkSheet = $this->_excel->setActiveSheetIndex(0);
	    //宽高
		$objWorkSheet->getColumnDimension('A')->setWidth(21);
		$objWorkSheet->getColumnDimension('B')->setWidth(21);
		$objWorkSheet->getColumnDimension('C')->setWidth(21);
		$objWorkSheet->getColumnDimension('D')->setWidth(21);
		$objWorkSheet->getColumnDimension('E')->setWidth(21);
		$objWorkSheet->getColumnDimension('F')->setWidth(21);
		$objWorkSheet->getColumnDimension('G')->setWidth(21);
		$objWorkSheet->getColumnDimension('H')->setWidth(21);
		$objWorkSheet->getColumnDimension('I')->setWidth(21);
		$objWorkSheet->getColumnDimension('J')->setWidth(21);
		$objWorkSheet->getColumnDimension('K')->setWidth(21);
		$objWorkSheet->getColumnDimension('L')->setWidth(21);

		$objWorkSheet->getRowDimension('1')->setRowHeight(48);
		$objWorkSheet->getRowDimension('2')->setRowHeight(30.7);
		// $objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A1'), 'B1:J'.$data_row );
		
		$objWorkSheet->mergeCells('A1:L1');
		
		//对齐方式

		//标题 、表头、数据 对齐
		$objWorkSheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  		$objWorkSheet->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  		$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A1'), 'B1:L1' );

  		$objWorkSheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  		$objWorkSheet->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A2'), 'B2:L2' );

		$objWorkSheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  		$objWorkSheet->getStyle('A3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A3'), 'A3:L'.($data_row + 2) );

		//字体
		$objWorkSheet->getStyle('A1')->getFont()->setName('宋体');
		$objWorkSheet->getStyle('A1')->getFont()->setSize(14);
		$objWorkSheet->getStyle('A1')->getFont()->setBold(true);

		$objWorkSheet->getStyle('A2')->getFont()->setName('宋体');
		$objWorkSheet->getStyle('A2')->getFont()->setSize(11);
		$objWorkSheet->getStyle('A2')->getFont()->setBold(true);		
		$objWorkSheet->getStyle('A2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
		
		//设置边框
		$styleThinBlackBorderOutline = array(
		       'borders' => array (
		             'bottom' => array (
		                   'style' => PHPExcel_Style_Border::BORDER_THIN,   //设置border样式
		                   'color' => array ( PHPExcel_Style_Color::COLOR_BLACK ),          //设置border颜色
		            ),
		      ),
		);



		$objWorkSheet->getStyle( 'A2:L2' )->applyFromArray($styleThinBlackBorderOutline);

		//背景填充
		$objWorkSheet->getStyle( 'A2:L2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objWorkSheet->getStyle( 'A2:L2')->getFill()->getStartColor()->setARGB('00C0C0C0');
		$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A2'), 'B2:L2');

		$objWorkSheet->setCellValue('A1',"\"" . $statements['data'][0]['customer_name'] ."对账单\r\n对账日期".$statements['dates'] . "\"");

		//文本格式
		$objWorkSheet->getStyle('C3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$objWorkSheet->getStyle('D3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$objWorkSheet->getStyle('G3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$objWorkSheet->getStyle('H3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		
		$objWorkSheet->getStyle('I3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		$objWorkSheet->getStyle('J3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		$objWorkSheet->getStyle('K3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

		$objWorkSheet->getStyle('L3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		

		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('C3'), 'C3:C'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('D3'), 'D3:D'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('G3'), 'G3:G'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('H3'), 'H3:H'.$data_row );

		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('I3'), 'I3:I'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('J3'), 'J3:J'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('K3'), 'K3:K'.$data_row );

		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('L3'), 'L3:L'.$data_row );

        $objWorkSheet->setCellValue('A2','发货日期')
        	->setCellValue('B2','订单编号')
        	->setCellValue('C2','客户名称')
            ->setCellValue('D2','产品编号')
            ->setCellValue('E2','产品名称')
            ->setCellValue('F2','型号')
            ->setCellValue('G2','发货数量')
            ->setCellValue('H2','计量单位')
            ->setCellValue('I2','单价(人民币：元)')
            ->setCellValue('J2','物流费(人民币：元)')
            ->setCellValue('K2','货款额(人民币：元)')
            ->setCellValue('L2','备注');

        

		foreach ($statements['data'] as $k=>$row) {
			$k += 3;
			$objWorkSheet->getRowDimension($k)->setRowHeight(24);
			$objWorkSheet->setCellValue('A'.$k, $row['shipping_time']);	
			$objWorkSheet->setCellValue('B'.$k, $row['order_sn']);	
			$objWorkSheet->setCellValue('C'.$k, $row['customer_name']);	
			$objWorkSheet->setCellValueExplicit('D'.$k, $row['goods_sn'], PHPExcel_Cell_DataType::TYPE_STRING);	
			$objWorkSheet->setCellValue('E'.$k, $row['goods_name']);	
			$objWorkSheet->setCellValue('F'.$k, $row['attr']);	
			$objWorkSheet->setCellValue('G'.$k, $row['goods_number_arr_saler']);	
			$objWorkSheet->setCellValue('H'.$k, $row['unit']);	
			$objWorkSheet->setCellValue('I'.$k, $row['goods_price_arr_saler']);	
			$objWorkSheet->setCellValue('J'.$k, $row['shipping_fee_arr_saler']);	
			$objWorkSheet->setCellValue('K'.$k, $row['order_amount_arr_saler']);	
			$objWorkSheet->setCellValue('L'.$k, $row['remark']);	
		}
		
		$k++;
		$objWorkSheet->mergeCells('A'.$k.':J'.$k);	
		$objWorkSheet->setCellValue('A'.$k, '合计发货数量');
		$objWorkSheet->setCellValue('K'.$k, '发货数量');
		$objWorkSheet->setCellValue('L'.$k, $statements['count_total']);
		$objWorkSheet->getRowDimension($k)->setRowHeight(24);

		$k++;
		$objWorkSheet->mergeCells('A'.$k.':J'.$k);
		$objWorkSheet->setCellValue('A'.$k, '合计发货金额');
		$objWorkSheet->setCellValue('K'.$k, '¥');
		$objWorkSheet->setCellValue('L'.$k, $statements['amount_total']);
		$objWorkSheet->getRowDimension($k)->setRowHeight(24);

		$this->_excel->setActiveSheetIndex(0);
		$objWorkSheet->setTitle('物融通供应商对账单');
		//excel数据
		$filename = $statements['dates'].'_supplier_stat';
		
		$this->getxlsx($filename, $this->_excel);
	}

	/**
	 * @param $content 对账单数据
	 * 导出项目内部对账单excel
	 */
	public function contractStatements( $statements ) {
		$this->_excel->getProperties()->setCreator('3tichina') //创建人
	        ->setLastModifiedBy('3tichina') //最后修改人
	        ->setTitle('物融通项目内部对账单') //标题
	        ->setSubject('物融通项目内部对账单') //题目
	        ->setDescription('物融通项目内部对账单，导出excel') //描述
	        ->setKeywords('物融通项目内部对账单导出') //关键字
	        ->setCategory('物融通项目内部对账单导出'); //种类
		
	    $data_row = $statements['total'] + 2;
	    $data_col = 'O';

	    $objWorkSheet = $this->_excel->setActiveSheetIndex(0);
	    //宽高
		$objWorkSheet->getColumnDimension('A')->setWidth(21);
		$objWorkSheet->getColumnDimension('B')->setWidth(21);
		$objWorkSheet->getColumnDimension('C')->setWidth(21);
		$objWorkSheet->getColumnDimension('D')->setWidth(21);
		$objWorkSheet->getColumnDimension('E')->setWidth(21);
		$objWorkSheet->getColumnDimension('F')->setWidth(21);
		$objWorkSheet->getColumnDimension('G')->setWidth(21);
		$objWorkSheet->getColumnDimension('H')->setWidth(21);
		$objWorkSheet->getColumnDimension('I')->setWidth(21);
		$objWorkSheet->getColumnDimension('J')->setWidth(21);
		$objWorkSheet->getColumnDimension('K')->setWidth(21);
		$objWorkSheet->getColumnDimension('L')->setWidth(21);
		$objWorkSheet->getColumnDimension('M')->setWidth(21);
		$objWorkSheet->getColumnDimension('N')->setWidth(21);
		$objWorkSheet->getColumnDimension('O')->setWidth(21);
		$objWorkSheet->getColumnDimension('P')->setWidth(21);
		$objWorkSheet->getColumnDimension('Q')->setWidth(21);
		$objWorkSheet->getColumnDimension('R')->setWidth(21);

		$objWorkSheet->getRowDimension('1')->setRowHeight(48);
		$objWorkSheet->getRowDimension('2')->setRowHeight(30.7);
		// $objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A1'), 'B1:J'.$data_row );
		
		$objWorkSheet->mergeCells('A1:R1');
		
		//对齐方式

		//标题 、表头、数据 对齐
		$objWorkSheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  		$objWorkSheet->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  		$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A1'), 'B1:R1' );

  		$objWorkSheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  		$objWorkSheet->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A2'), 'B2:R2' );

		$objWorkSheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  		$objWorkSheet->getStyle('A3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A3'), 'A3:R'.($data_row + 3) );

		//字体
		$objWorkSheet->getStyle('A1')->getFont()->setName('宋体');
		$objWorkSheet->getStyle('A1')->getFont()->setSize(14);
		$objWorkSheet->getStyle('A1')->getFont()->setBold(true);

		$objWorkSheet->getStyle('A2')->getFont()->setName('宋体');
		$objWorkSheet->getStyle('A2')->getFont()->setSize(11);
		$objWorkSheet->getStyle('A2')->getFont()->setBold(true);		
		$objWorkSheet->getStyle('A2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
		
		//设置边框
		$styleThinBlackBorderOutline = array(
		       'borders' => array (
		             'bottom' => array (
		                   'style' => PHPExcel_Style_Border::BORDER_THIN,   //设置border样式
		                   'color' => array ( PHPExcel_Style_Color::COLOR_BLACK ),          //设置border颜色
		            ),
		      ),
		);



		$objWorkSheet->getStyle( 'A2:R2' )->applyFromArray($styleThinBlackBorderOutline);

		//背景填充
		$objWorkSheet->getStyle( 'A2:R2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objWorkSheet->getStyle( 'A2:R2')->getFill()->getStartColor()->setARGB('00C0C0C0');
		$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A2'), 'B2:R2');

		$objWorkSheet->setCellValue('A1',"\"" . $statements['data'][0]['contract_name'] ."对账单\r\n对账日期".$statements['dates'] . "\"");

		//文本格式
		$objWorkSheet->getStyle('A3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

		$objWorkSheet->getStyle('C3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$objWorkSheet->getStyle('F3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		
		
		$objWorkSheet->getStyle('E3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		$objWorkSheet->getStyle('G3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		$objWorkSheet->getStyle('H3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		$objWorkSheet->getStyle('I3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

		$objWorkSheet->getStyle('M3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$objWorkSheet->getStyle('N3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		$objWorkSheet->getStyle('O3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		$objWorkSheet->getStyle('P3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		$objWorkSheet->getStyle('Q3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('A3'), 'A3:A'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('B3'), 'B3:B'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('C3'), 'C3:C'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('E3'), 'E3:E'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('F3'), 'F3:F'.$data_row );

		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('G3'), 'G3:G'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('H3'), 'H3:H'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('I3'), 'I3:I'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('K3'), 'K3:K'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('M3'), 'M3:M'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('N3'), 'N3:N'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('O3'), 'O3:O'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('P3'), 'P3:P'.$data_row );
		if( $data_row > 3 )
			$objWorkSheet->duplicateStyle( $objWorkSheet->getStyle('Q3'), 'Q3:Q'.$data_row );

        $objWorkSheet->setCellValue('A2','发货时间')
        	->setCellValue('B2','客户名称')
        	->setCellValue('C2','产品名称')
            ->setCellValue('D2','产品编号')
            ->setCellValue('E2','销售订单号')
            ->setCellValue('F2','销售价格')
            ->setCellValue('G2','销售数量')
            ->setCellValue('H2','销售订单金融费')
            ->setCellValue('I2','销售订单物流费')
            ->setCellValue('J2','销售金额')
            ->setCellValue('K2','到货日期')
            ->setCellValue('L2','采购订单编号')
            ->setCellValue('M2','供应商名称')
            ->setCellValue('N2','采购价格')
            ->setCellValue('O2','采购数量')
            ->setCellValue('P2','采购订单物流费')
            ->setCellValue('Q2','采购金额')
            ->setCellValue('R2','交易差价');

        

		foreach ($statements['data'] as $k=>$row) {
			$k += 3;
			$objWorkSheet->getRowDimension($k)->setRowHeight(24);
			$objWorkSheet->setCellValue('A'.$k, $row['shipping_time']);
			$objWorkSheet->setCellValue('B'.$k, $row['customer_name']);
			$objWorkSheet->setCellValue('C'.$k, $row['goods_name']);
			$objWorkSheet->setCellValueExplicit('D'.$k, $row['goods_sn'], PHPExcel_Cell_DataType::TYPE_STRING);
			$objWorkSheet->setCellValue('E'.$k, $row['order_sn']);
			$objWorkSheet->setCellValue('F'.$k, $row['goods_price_arr_buyer']);	
			$objWorkSheet->setCellValue('G'.$k, $row['goods_number_arr_buyer']);	
			$objWorkSheet->setCellValue('H'.$k, $row['financial_arr']);	
			$objWorkSheet->setCellValue('I'.$k, $row['shipping_fee_arr_buyer']);	
			$objWorkSheet->setCellValue('J'.$k, $row['order_amount_arr_buyer']);

			$objWorkSheet->setCellValue('K'.$k, $row['contract_effect_time']);
			$objWorkSheet->setCellValue('L'.$k, $row['purchase_sn']);
			$objWorkSheet->setCellValue('M'.$k, $row['suppliers_name']);
			$objWorkSheet->setCellValue('N'.$k, $row['goods_price_arr_saler']);
			$objWorkSheet->setCellValue('O'.$k, $row['goods_number_arr_saler']);	
			$objWorkSheet->setCellValue('P'.$k, $row['shipping_fee_arr_saler']);	
			$objWorkSheet->setCellValue('Q'.$k, $row['order_amount_arr_saler']);	
			$objWorkSheet->setCellValue('R'.$k, $row['differential']);	
			
		}
		
		$k++;
		$objWorkSheet->mergeCells('A'.$k.':H'.$k);	
		$objWorkSheet->mergeCells('I'.$k.':J'.$k);	
		$objWorkSheet->setCellValue('A'.$k, '总销售数量');
		$objWorkSheet->setCellValue('I'.$k, $statements['buyer_count_total']);
		$objWorkSheet->getRowDimension($k)->setRowHeight(24);

		
		$objWorkSheet->mergeCells('K'.$k.':P'.$k);
		$objWorkSheet->mergeCells('Q'.$k.':R'.$k);
		$objWorkSheet->setCellValue('K'.$k, '总采购数量');
		$objWorkSheet->setCellValue('Q'.$k, $statements['saler_count_total']);
		$objWorkSheet->getRowDimension($k)->setRowHeight(24);

		$k++;
		$objWorkSheet->mergeCells('A'.$k.':H'.$k);	
		$objWorkSheet->mergeCells('I'.$k.':J'.$k);	
		$objWorkSheet->setCellValue('A'.$k, '总销售金额');
		$objWorkSheet->setCellValue('I'.$k, '¥'.$statements['buyer_amount_total']);
		$objWorkSheet->getRowDimension($k)->setRowHeight(24);

		
		$objWorkSheet->mergeCells('K'.$k.':P'.$k);
		$objWorkSheet->mergeCells('Q'.$k.':R'.$k);
		$objWorkSheet->setCellValue('K'.$k, '总采购金额');
		$objWorkSheet->setCellValue('Q'.$k, '¥'.$statements['saler_amount_total']);
		$objWorkSheet->getRowDimension($k)->setRowHeight(24);

		$k++;
		$objWorkSheet->mergeCells('A'.$k.':H'.$k);	
		$objWorkSheet->mergeCells('I'.$k.':J'.$k);	
		$objWorkSheet->setCellValue('A'.$k, '总交易差价');
		$objWorkSheet->setCellValue('I'.$k, '¥'.$statements['differential_total']);
		$objWorkSheet->getRowDimension($k)->setRowHeight(24);

		$objWorkSheet->mergeCells('K'.$k.':R'.$k);
		$objWorkSheet->setCellValue('K'.$k, '对账日期'.$statements['dates']);
		$objWorkSheet->getRowDimension($k)->setRowHeight(24);

		$this->_excel->setActiveSheetIndex(0);
		$objWorkSheet->setTitle('物融通项目内部对账单');
		//excel数据
		$filename = $statements['dates'].'_contract_stat';
		
		$this->getxlsx($filename, $this->_excel);
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