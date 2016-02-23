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
	'bill_status' => array( 0 => "已扣减", 1 => "已恢复" ),//票据状态
	
    'bill_amount_type' => array(0 => "商票", 1 => "现金", 2 => "承兑"),//额度生成类型
	'cash_bill_amount_type' => array( 1 => "现金", 2 => "承兑", 3=> "国内信用证"),//额度生成类型（现金）
	
    'bill_repay_type' => array(0 => "现金", 1 => "支票"),//票据偿还类型
	
    'bill_adjust_type' => array(0 => "采购额度账户", 1 => "现金账户"),//账户调整类型

    'order_status' => array(
                        POS_SUBMIT => '已下单',
                        POS_HANDLE => '处理中',
                        /* 前台  */
                        POS_CONFIRM => '确认中',
                        POS_CHECK => '验收中',
                        POS_BALANCE => '对账中',
                        /* 前台  */
                        POS_COMPLETE => '已完成',
                        POS_CANCEL => '订单取消'


                        ),//大订单状态

    'childer_order_status' => array( 
                                SOS_UNCONFIRMED => '未确认',
                                SOS_CONFIRMED => '已确认',
                                SOS_SEND_CC => '客户已验签(发货)',
                                SOS_SEND_PC => '平台已验签(发货)',
                                SOS_SEND_PP => '平台已推单(发货)',
                                SOS_SEND_SC => '供应商已验签(发货)', 
                                SOS_SEND_PC2 => '平台已验签(发货)',
                                SOS_ARR_CC => '客户已验签(到货)',
                                SOS_ARR_PC => '平台已验签(到货)',
                                SOS_ARR_SC => '供应商已验签(到货)',
                                SOS_ARR_PC2 => '平台已验签(到货)',
                                SOS_CANCEL => '订单已取消'
                            ),//(子)订单状态，也是验签状态

    'sale_status' => array(
                                SALE_ORDER_UNCONFIRMED => '未确认',
                                SALE_ORDER_CONFIRMED => '已确认',
                                SALE_ORDER_UNRECEIVE => '待收货',
                                SALE_ORDER_COMPLETE => '已完成',
                                SALE_ORDER_CANCEL => '订单取消'
                            ),//销售订单状态

    'purchase_status' => array(
                                PURCHASE_ORDER_UNCONFIRMED => '未确认',
                                PURCHASE_ORDER_CONFIRMED => '已确认',
                                PURCHASE_ORDER_UNCOMPLETE => '待完成',
                                PURCHASE_ORDER_COMPLETE => '已完成',
                                PURCHASE_ORDER_CANCEL => '订单取消'
                            ),//采购订单状态

    'purchase_to_childer_map' => array(
                                PURCHASE_ORDER_UNCONFIRMED =>array(
                                   
                                ),
                                PURCHASE_ORDER_CONFIRMED => array(
                                    SOS_UNCONFIRMED,
                                    SOS_CONFIRMED,
                                    SOS_SEND_CC,
                                    SOS_SEND_PC, 
                                    SOS_SEND_PP,
                                    SOS_SEND_SC,
                                    SOS_SEND_PC2,
                                    SOS_ARR_CC,
                                    SOS_ARR_PC
                                ),
                                PURCHASE_ORDER_UNCOMPLETE => array(
                                    SOS_ARR_SC
                                ),
                                PURCHASE_ORDER_COMPLETE => array(
                                    SOS_ARR_PC2     
                                ),
                                PURCHASE_ORDER_CANCEL => array(
                                    SOS_CANCEL
                                ),

                            ),//采购订单 和 子订单 映射关系

    'pay_status' => array(0 => '未付款', 1 => '付款中', 2 => '已付款'),//付款状态
    'purchase_pay_status' => array(0 => '未生成', 1 => '已生成', 2 => '已付款'),//采购订单的收款状态
    'shipping_status' => array( 0 => '未发货', 1 => '已发货', 2 => '收货中', 3 => '已收货', 4 => '确认收货'),//发货状态
    'purchase_order_pay_status' => array( 0 => '已申请', 1 => '审核不通过', 2 => '审核通过', 3 => '已付款'),//供应商的生成单付款状态
    'payment' => array( 1 => '现金', 2 => '支票'),//付款方式
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