-- users 
alter table `users` add (`bill_amount_history` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '已分配票据采购额度(历次票据分配额度总和)',
`bill_amount_valid` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '现有票据采购额度(现有可用的票据采购额度 = 已分配票据采购额度 - 已经使用的票据采购额度)',
`cash_amount_history` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '已分配现金采购额度(历次现金分配额度总和)',
`cash_amount_valid` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '现有现金采购额度(现有可用的现金采购额度 = 已分配现金采购额度 - 已经使用的现金采购额度)',
`inv_payee` varchar(120) NOT NULL DEFAULT '' COMMENT '发票抬头，用户页面填写',
`inv_bank_name` varchar(120) NOT NULL DEFAULT '' COMMENT '开票资料，开户银行',
`inv_bank_account` varchar(50) NOT NULL DEFAULT '' COMMENT '开票资料，开户行帐号',
`inv_bank_address` varchar(120) NOT NULL DEFAULT '' COMMENT '开票资料，开户行地址',
`inv_tel` varchar(50) NOT NULL DEFAULT '' COMMENT '开票资料，联系电话',
`inv_fax` varchar(50) NOT NULL DEFAULT '' COMMENT '开票资料，传真号码')

-- order_goods 
alter table `order_info` add (`inv_bank_name` varchar(120) NOT NULL DEFAULT '' COMMENT '开票资料，开户银行',
`inv_bank_account` varchar(50) NOT NULL DEFAULT '' COMMENT '开票资料，开户行帐号',
`inv_bank_address` varchar(120) NOT NULL DEFAULT '' COMMENT '开票资料，开户行地址',
`inv_tel` varchar(50) NOT NULL DEFAULT '' COMMENT '开票资料，联系电话',
`inv_fax` varchar(50) NOT NULL DEFAULT '' COMMENT '开票资料，传真号码')

-- admin_action
/** 
INSERT INTO `zjwr`.`admin_action` (`parent_id`,`action_code`) VALUES ('0', '14_credit'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`,`action_code`) VALUES ('0', '15_bthPrice'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`,`action_code`) VALUES ('0', '16_applyCredit'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`,`action_code`) VALUES ('0', '17_bill_manage'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`,`action_code`) VALUES ('0', '18_sale_order'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('138', 'contract_insert'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('138', 'contract_list'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('138', 'contract_supplier_add'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('138', 'contract_supplier_list'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('139', 'credit_list'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('140', 'batch_price'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('140', 'price_list'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('141', 'applyCredit_list'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('141', 'applyCredit_rlist'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('142', 'bill_manage_insert'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('142', 'bill_manage_list'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('142', 'bill_manage_generate'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('142', 'bill_manage_order_list'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('142', 'bill_manage_repay_list'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('142', 'bill_manage_user_list'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('143', 'sale_order_list'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('143', 'sale_order_suborder_all'); 
INSERT INTO `zjwr`.`admin_action` (`parent_id`, `action_code`) VALUES ('1', 'goods_shipping_set'); 
*/