<?php
/**
 * Created by PhpStorm.
 * User: wliu
 * Date: 14-10-14
 * Time: 上午11:48
 */

class OrderObj {
    public $cusFnum;////客户编号
    public $b2bFnum;//商城订单号1"
    public $erpFnum;//erp订单编号
    public $conFnum;//对应合同编号
    public $erpState;//订单状态（A保存,B提交,C审核）
    public $saleDate;//订单日期yyyy-MM-dd
    public $receiveFnum;//收货方编号
    public $chargeFnum;//收款方编号
    public $settleFnum;//结算方编号
    public $remark;//备注
    public $receiveAddress;//收货地址
    public $supplyNumber;//供应商
    public $fdeliverydate;//要货日期yyyy-MM-dd
    public $SAL_SaleOrder__FSaleOrderEntry;
}