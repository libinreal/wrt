<?php
/**
 *
 *	该文件用于测试模板
 *	by xiangfeng
 *
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/includes/lib_order.php');
$section = $_REQUEST['section'];
$act = $_REQUEST['act'];

if($section == "credit_manage"){
	switch ($act) {
		case 'list':
			$smarty->display('second/credit_list.html');
			break;

		case 'info':
			$smarty->display('second/credit_detail.html');
			break;
		
		default:
			exit;
			break;
	}
}


if($section == "bill_manage"){
	switch ($act) {
		case 'insert':
			$smarty->display('second/bill_insert.html');
			break;

		case 'list':
			$smarty->display('second/bill_list.html');
			break;

		case 'order_list':
			$smarty->display('second/bill_purchase_list.html');
			break;

		case 'repay_list':
			$smarty->display('second/bill_repay_list.html');
			break;

		case 'user_list':
			$smarty->display('second/user_list.html');
			break;

		case 'info':
			$smarty->display('second/bill_edit.html');
			break;

		case 'view':
			$smarty->display('second/bill_view.html');
			break;

		case 'generate':
			$smarty->display('second/bill_purchase_cash.html');
			break;

		case 'generate_edit':
			$smarty->display('second/bill_purchase_cash_edit.html');
			break;

		case 'generate_view':
			$smarty->display('second/bill_purchase_note_view.html');
			break;

		case 'generate_note':
			$smarty->display('second/bill_purchase_note.html');
			break;

		case 'generate_note_edit':
			$smarty->display('second/bill_purchase_note_edit.html');
			break;

		case 'repay':
			$smarty->display('second/bill_repay.html');
			break;

		case 'repay_view':
			$smarty->display('second/bill_repay_view.html');
			break;

		case 'assign_note':
			$smarty->display('second/bill_purchase_assign_note.html');
			break;

		case 'assign_cash':
			$smarty->display('second/bill_purchase_assign_cash.html');
			break;

		case 'adjust':
			$smarty->display('second/bill_purchase_adjust.html');
			break;
		
		default:
			exit;
			break;
	}
}

if($section == "purchase_price_manage"){
	switch ($act) {
		case 'batch':
			$smarty->display('second/purchase_price_increase_batch.html');
			break;

		case 'single':
			$smarty->display('second/purchase_price_increase_single.html');		
			break;

		case 'list':
			$smarty->display('second/purchase_price_increase_list.html');
			break;
		
		default:
			exit;
			break;
	}
}

if($section == "sale_order"){
	switch ($act) {
		case 'list':
			$smarty->display('second/sale_order_list.html');
			break;

		case 'detail':
			$smarty->display('second/sale_order_detail.html');		
			break;

		case 'change_send_price':
			$smarty->display('second/sale_order_send_change_price.html');
			break;

		case 'change_receive_price':
			$smarty->display('second/sale_order_receive_change_price.html');
			break;

		case 'split':
			$smarty->display('second/sale_order_split.html');
			break;

		case 'suborder_list':
			$smarty->display('second/sale_suborder_list.html');
			break;

		case 'suborder_all':
			$smarty->display('second/sale_suborder_all.html');
			break;

		case 'suborder_detail':
			$smarty->display('second/sale_suborder_detail.html');
			break;
		
		default:
			exit;
			break;
	}
}

if($section == "purchase_order"){
	switch ($act) {
		case 'check_first':
			$smarty->display('second/purchase_order_check_first_list.html');
			break;

		case 'suborder_check_first':
			$smarty->display('second/purchase_suborder_check_first.html');
			break;

		case 'check_second':
			$smarty->display('second/purchase_order_check_second_list.html');
			break;

		case 'suborder_check_second':
			$smarty->display('second/purchase_suborder_check_second.html');
			break;

		default:
			exit;
			break;
	}
}

if($section == "purchase_order_manage"){
	switch ($act) {
		case 'list':
			$smarty->display('second/purchase_order_list.html');
			break;

		case 'detail':
			$smarty->display('second/purchase_order_detail.html');		
			break;

		case 'send_price':
			$smarty->display('second/purchase_order_send_change_price.html');		
			break;

		case 'receive_price':
			$smarty->display('second/purchase_order_receive_change_price.html');		
			break;
		
		default:
			exit;
			break;
	}
}

if($section == "payment"){
	switch ($act) {
		case 'list':
			$smarty->display('second/payment_order_list.html');
			break;

		case 'detail':
			$smarty->display('second/payment_order_detail.html');
			break;

		case 'handle':
			$smarty->display('second/payment_order_handle.html');
			break;
		
		default:
			exit;
			break;
	}
}

if($section == "self_credit"){
	switch ($act) {
		case 'check_detail':
			$smarty->display('second/self_credit_check_detail.html');
			break;

		case 'list':
			$smarty->display('second/self_credit_list.html');
			break;

		case 'recycle':
			$smarty->display('second/self_credit_recycle.html');
			break;
		
		default:
			exit;
			break;
	}
}

if($section == "bank"){
	switch ($act) {
		case 'edit':
			$smarty->display('second/bank_edit.html');
			break;

		case 'list':
			$smarty->display('second/bank_list.html');
			break;

		case 'insert':
			$smarty->display('second/bank_insert.html');
			break;
		
		default:
			exit;
			break;
	}
}

if($section == "index_supplier"){
	$smarty->display('index_suppliers.htm');
}

if($section == "supplier"){
	switch ($act) {
		case 'goods_list':
			$smarty->display('supplier/goods_list.html');
			break;

		case 'goods_insert':
			$smarty->display('supplier/goods_insert.html');
			break;

		case 'goods_recycle':
			$smarty->display('supplier/goods_recycle.html');
			break;

		case 'goods_shipping_setting':
			$smarty->display('supplier/goods_shipping_setting.html');
			break;

		case 'order_detail':
			$smarty->display('supplier/order_detail.html');
			break;

		case 'order_list':
			$smarty->display('supplier/order_list.html');
			break;

		case 'recipient_complete':
			$smarty->display('supplier/recipient_complete.html');
			break;

		case 'recipient_detail':
			$smarty->display('supplier/recipient_detail.html');
			break;

		case 'recipient_edit':
			$smarty->display('supplier/recipient_edit.html');
			break;

		case 'recipient_list':
			$smarty->display('supplier/recipient_list.html');
			break;

		case 'recipient_render':
			$smarty->display('supplier/recipient_render.html');
			break;
		
		default:
			exit;
			break;
	}
}