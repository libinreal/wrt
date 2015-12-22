<?php
/**
 * 后台表单类型相关配置，数组形式
 * 调用对应方法以获取配置
 */

//票据类型
$bill_type = array("商业承兑汇票", "银行承兑汇票");
//额度生成类型
$bill_amount_type = array("商票", "现金", "承兑");

/**
 * [bill_types 票据类型]
 * @return [array] [键对应类型ID, 值对应类型名字]
 */
function bill_types()
{
	global $bill_type;
	return $bill_type;
}

/**
 * [bill_amount_types 额度生成类型]
 * @return [array] [键对应类型ID, 值对应类型名字]
 */
function bill_amount_types()
{
	global $bill_amount_type;
	return $bill_amount_type;
}

?>