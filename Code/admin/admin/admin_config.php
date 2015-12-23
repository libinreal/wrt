<?php
/**
 * 后台表单类型相关配置，数组形式
 * 调用对应方法以获取配置
 */
//票据币种
$bill_currency = array( 0 => "人民币", 1 => "美元" );
//票据类型
$bill_type = array( 0 => "商业承兑汇票", 1 => "银行承兑汇票");
//额度生成类型
$bill_amount_type = array(0 => "商票", 1 => "现金", 2 => "承兑");
//额度生成类型（现金）
$cash_bill_amount_type = array( 1 => "现金", 2 => "承兑", 3=> "国内信用证");

//票据偿还类型
$bill_repay_type = array(0 => "现金", 1 => "支票");



/**
 * bill_types 票据类型
 * @return array 键对应类型ID, 值对应类型名字
 */
function bill_types()
{
	global $bill_type;
	return $bill_type;
}

/**
 * bill_amount_types 额度生成类型
 * @return array 键对应类型ID, 值对应类型名字
 */
function bill_amount_types()
{
	global $bill_amount_type;
	return $bill_amount_type;
}

/**
 * bill_currencys 票据币种
 * @return array 键对应类型ID, 值对应类型名字
 */
function bill_currencys()
{
	global $bill_currency;
	return $bill_currency;
}

/**
 * bill_repay_types 偿还单类型
 * @return array 键对应类型ID, 值对应类型名字
 */
function bill_repay_types()
{
	global $bill_repay_type;
	return $bill_repay_type;
}

/**
 * cash_bill_amount_types 现金偿还
 * @return array 键对应类型ID, 值对应类型名字 
 */
function cash_bill_amount_types()
{
	global $cash_bill_amount_type;
	return $cash_bill_amount_type;
}
?>