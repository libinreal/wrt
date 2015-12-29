<?php
/**
 * 后台配置，键值形式
 * 在$admin_config添加对应的'key'=>'value'
 * 使用时调用C('key') 即可获取对应配置
 * 
 * @author libin@3ti.us
 * date 2015-12-25
 */

//可手动添加配置
$admin_config = array(
	'bill_currency' => array( 0 => "人民币", 1 => "美元" ),//票据币种
	'bill_type' => array( 0 => "商业承兑汇票", 1 => "银行承兑汇票"),//票据类型
	
    'bill_amount_type' => array(0 => "商票", 1 => "现金", 2 => "承兑"),//额度生成类型
	'cash_bill_amount_type' => array( 1 => "现金", 2 => "承兑", 3=> "国内信用证"),//额度生成类型（现金）
	
    'bill_repay_type' => array(0 => "现金", 1 => "支票"),//票据偿还类型
	
    'bill_adjust_type' => array(0 => "采购额度账户", 1 => "现金账户"),//账户调整类型

    'order_status' => array(0 => '新增', 1 => '处理中', 2 => '已完成', 3 => '已撤单' ),//订单状态
);

//加载配置
foreach ($admin_config as $n => $v) {
	C($n, $v);
}


/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function C($name=null, $value=null,$default=null) {
    static $_config = array();
    // 无参数时获取所有
    if (empty($name)) {
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtoupper($name);
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : $default;
            $_config[$name] = $value;
            return null;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
        $name[0]   =  strtoupper($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
        $_config[$name[0]][$name[1]] = $value;
        return null;
    }
    // 批量设置
    if (is_array($name)){
        $_config = array_merge($_config, array_change_key_case($name,CASE_UPPER));
        return null;
    }
    return null; // 避免非法参数
}
?>