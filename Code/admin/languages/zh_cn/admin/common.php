<?php

//管理中心共用语言文件
$time = date('Y',time());
$_LANG['app_name'] = '中交物融集成服务平台有限公司';
$_LANG['cp_home'] = '中交物融平台 管理中心';
$_LANG['copyright'] = '版权所有 &copy; '.$time.' 中交物融集成服务平台有限公司，并保留所有权利。';
$_LANG['query_info'] = '共执行 %d 个查询，用时 %s 秒';
$_LANG['memory_info'] = '，内存占用 %0.3f MB';
$_LANG['gzip_enabled'] = '，Gzip 已启用';
$_LANG['gzip_disabled'] = '，Gzip 已禁用';
$_LANG['loading'] = '正在处理您的请求...';
$_LANG['js_languages']['process_request'] = '正在处理您的请求...';
$_LANG['js_languages']['todolist_caption'] = '记事本';
$_LANG['js_languages']['todolist_autosave'] = '自动保存';
$_LANG['js_languages']['todolist_save'] = '保存';
$_LANG['js_languages']['todolist_clear'] = '清除';
$_LANG['js_languages']['todolist_confirm_save'] = '是否将更改保存到记事本？';
$_LANG['js_languages']['todolist_confirm_clear'] = '是否清空内容？';
$_LANG['auto_redirection'] = '如果您不做出选择，将在 <span id="spanSeconds">3</span> 秒后跳转到第一个链接地址。';
$_LANG['password_rule'] = '密码应只包含英文字符、数字.长度在6--16位之间';
$_LANG['username_rule'] = '用户名应为汉字、英文字符、数字组合，3到15位';
$_LANG['plugins_not_found'] = '插件 %s 无法定位';
$_LANG['no_records'] = '没有找到任何记录';
$_LANG['role_describe'] = '角色描述';

$_LANG['require_field'] = '<span class="require-field">*</span>';
$_LANG['yes'] = '是';
$_LANG['no'] = '否';
$_LANG['record_id'] = '编号';
$_LANG['handler'] = '操作';
$_LANG['install'] = '安装';
$_LANG['uninstall'] = '卸载';
$_LANG['list'] = '列表';
$_LANG['add'] = '添加';
$_LANG['edit'] = '编辑';
$_LANG['view'] = '查看';
$_LANG['remove'] = '移除';
$_LANG['drop'] = '删除';
$_LANG['confirm_delete'] = '您确定要删除吗？';
$_LANG['disabled'] = '禁用';
$_LANG['enabled'] = '启用';
$_LANG['setup'] = '设置';
$_LANG['success'] = '成功';
$_LANG['sort_order'] = '排序';
$_LANG['trash'] = '回收站';
$_LANG['restore'] = '还原';
$_LANG['close_window'] = '关闭窗口';
$_LANG['btn_select'] = '选择';
$_LANG['operator'] = '操作人';
$_LANG['cancel'] = '取消';

$_LANG['empty'] = '不能为空';
$_LANG['repeat'] = '已存在';
$_LANG['is_int'] = '应该为整数';

$_LANG['button_submit'] = ' 确定 ';
$_LANG['button_save'] = ' 保存 ';
$_LANG['button_reset'] = ' 重置 ';
$_LANG['button_search'] = ' 搜索 ';

$_LANG['priv_error'] = '对不起,您没有执行此项操作的权限!';
$_LANG['drop_confirm'] = '您确认要删除这条记录吗?';
$_LANG['form_notice'] = '点击此处查看提示信息';
$_LANG['upfile_type_error'] = '上传文件的类型不正确!';
$_LANG['upfile_error'] = '上传文件失败!';
$_LANG['no_operation'] = '您没有选择任何操作';

$_LANG['go_back'] = '返回上一页';
$_LANG['back'] = '返回';
$_LANG['continue'] = '继续';
$_LANG['system_message'] = '系统信息';
$_LANG['check_all'] = '全选';
$_LANG['select_please'] = '请选择...';
$_LANG['all_category'] = '所有分类';
$_LANG['all_brand'] = '所有品牌';
$_LANG['refresh'] = '刷新';
$_LANG['update_sort'] = '更新排序';
$_LANG['modify_failure'] = '修改失败!';
$_LANG['attradd_succed'] = '操作成功!';
$_LANG['todolist'] = '记事本';
$_LANG['n_a'] = 'N/A';

/* 提示 */
$_LANG['sys']['wrong'] = '错误：';

/* 编码 */
$_LANG['charset']['utf8'] = '国际化编码（utf8）';
$_LANG['charset']['zh_cn'] = '简体中文';
$_LANG['charset']['zh_tw'] = '繁体中文';
$_LANG['charset']['en_us'] = '美国英语';
$_LANG['charset']['en_uk'] = '英文';

/* 新订单通知 */
$_LANG['order_notify'] = '新订单通知';
$_LANG['new_order_1'] = '您有 ';
$_LANG['new_order_2'] = ' 个新订单以及 ';
$_LANG['new_order_3'] = ' 个新付款的订单';
$_LANG['new_order_link'] = '点击查看新订单';

/*语言项*/
$_LANG['chinese_simplified'] = '简体中文';
$_LANG['english'] = '英文';

/* 分页 */
$_LANG['total_records'] = '总计 ';
$_LANG['total_pages'] = '个记录分为';
$_LANG['page_size'] = '页，每页';
$_LANG['page_current'] = '页当前第';
$_LANG['page_first'] = '第一页';
$_LANG['page_prev'] = '上一页';
$_LANG['page_next'] = '下一页';
$_LANG['page_last'] = '最末页';
$_LANG['admin_home'] = '起始页';

/* 重量 */
$_LANG['gram'] = '克';
$_LANG['kilogram'] = '千克';


/**
 *   a、将商城推荐下的推荐商品列表和推荐品牌放到基建商城下面。b、将物融新闻、商城公告、帮助中心和广告列表合并成内容管理
c、将工程推荐订单放到订单管理栏目d、调左边顺序。

基建商场—订单管理---积分商城—内容管理---基础数据管理 信用池管理--- 定制管理—
工程资讯—客服中心—会员管理—权限管理—报表统计—
 */
/* 菜单分类部分 */
$_LANG['01_cat_and_goods'] = '基建商城';
$_LANG['02_order'] = '订单管理';
$_LANG['03_exchange'] = "积分商城";
$_LANG['04_wrnews'] = '内容管理';
$_LANG['05_baseData'] = '基础数据管理';
$_LANG['06_credit'] = '信用池管理';
$_LANG['07_customize'] = '定制管理';
$_LANG['08_engineering'] = '工程资讯';
$_LANG['09_service'] = '客服中心';
$_LANG['10_members'] = '会员管理';
$_LANG['11_priv_admin'] = '权限管理';
$_LANG['12_stats'] = '报表统计';
$_LANG['13_contract'] = '合同管理';
$_LANG['14_credit'] = '授信管理';
$_LANG['15_bthPrice'] = '采购价格管理';
$_LANG['16_applyCredit'] = '平台授信管理';
$_LANG['17_bill_manage'] = '应收票据管理';
$_LANG['18_sale_order'] = '销售订单管理';
$_LANG['23_purchase_order_manage'] = '采购订单管理';
$_LANG['19_bank_manage'] = '银行管理';
$_LANG['20_supplier_order'] = '订单管理';//供货商
$_LANG['21_order_pay'] = '应付款管理';
$_LANG['22_order_recipient'] = '应收款管理';

//合同管理
$_LANG['01_contract_insert'] = '新增合同';
$_LANG['02_contract_list'] = '合同列表';
$_LANG['05_contract_supplier_add'] = '关联供应商、合同';
$_LANG['06_contract_supplier_list'] = '供应商、合同列表';

//授信管理
$_LANG['01_credit_list'] = '授信单列表';

//采购价格管理
$_LANG['01_batch_price'] = '批量加价';
$_LANG['02_price_list'] = '加价列表';

//平台授信管理
$_LANG['01_applyCredit_list'] = '平台授信列表';
$_LANG['02_applyCredit_rlist'] = '平台授信回收站';

//应收票据管理
$_LANG['01_bill_manage_insert'] = '票据添加';
$_LANG['02_bill_manage_list'] = '票据列表';
$_LANG['03_bill_manage_generate'] = '现金额度生成单';
$_LANG['04_bill_manage_order_list'] = '额度生成单列表';
$_LANG['05_bill_manage_repay_list'] = '票据偿还单列表';
$_LANG['06_bill_manage_user_list'] = '额度分配调整列表';

//销售订单管理
$_LANG['01_sale_order_list'] = '订单列表';
$_LANG['02_sale_order_suborder_all'] = '子订单列表';

// 采购订单管理
$_LANG['01_purchase_order_manage_list'] = '订单列表';

$_LANG['01_supplier_order_list'] = '订单列表';

//银行管理
$_LANG['01_bank_insert'] = '添加登记机构';
$_LANG['02_bank_list'] = '登记机构列表';

//应付款管理
$_LANG['01_order_pay_list'] = '应付单列表';

//应收款管理
$_LANG['01_order_recipient_comp'] = '已完成的订单';
$_LANG['02_order_recipient_list'] = '应收单列表';

// 积分商品管理
$_LANG['01_exchange_category'] = "分类列表";
$_LANG['02_exchange_category_add'] = "添加分类";
$_LANG['03_exchange_goods'] = '积分商品列表';
$_LANG['04_exchange_goods_add'] = '添加积分商品';
$_LANG['05_exchange_order_list'] = '积分订单列表';
$_LANG['06_exchange_delivery_order'] = '发货单列表';
$_LANG['07_exchange_goods_trash'] = '积分商品回收站';
//$_LANG['09_exchange_attribute_list'] = '积分商品属性';
//$_LANG['10_exchange_attribute_add'] = '添加属性';
//$_LANG['52_exchange_attribute_add'] = '编辑属性';
//$_LANG['08_exchange_goods_type'] = "积分商品类型";
// 积分商城订单
//$_LANG['03_exchange_order_query'] = '积分订单查询';
//$_LANG['04_exchange_merge_order'] = '合并积分订单';
//$_LANG['05_exchange_edit_order_print'] = '积分订单打印';
//$_LANG['06_exchange_undispose_booking'] = '缺货登记';
//$_LANG['08_exchange_add_order'] = '添加积分订单';
//$_LANG['10_exchange_back_order'] = '退货单列表';

//基础数据管理
$_LANG['01_category_list'] = '物料类别';
$_LANG['02_category_add'] = '添加类别';
$_LANG['03_brand_list'] = '厂商列表';
$_LANG['04_brand_add'] = '添加厂商';
$_LANG['05_goods_type'] = '物料类型';
$_LANG['09_attribute_list'] = '物料属性';
$_LANG['10_attribute_add'] = '添加属性';
//$_LANG['suppliers'] = '供货商列表';

/* 基建商城 */
$_LANG['12_mall_goods'] = '推荐商品';
$_LANG['01_goods_list'] = '商品列表';
$_LANG['02_goods_add'] = '添加新商品';
$_LANG['03_category_list'] = '商品分类';
$_LANG['04_category_add'] = '添加分类';
$_LANG['05_comment_manage'] = '用户评论';
$_LANG['07_brand_add'] = '添加品牌';
$_LANG['08_goods_type'] = '商品类型';
$_LANG['19_brand_recommend_list'] = '推荐品牌';
$_LANG['20_brand_recommend_add'] = '添加推荐品牌';
$_LANG['11_goods_trash'] = '商品回收站';
$_LANG['21_goods_shipping_set'] = '物流费用设置';
//$_LANG['09_attribute_list'] = '商品属性';
//$_LANG['10_attribute_add'] = '添加属性';
//$_LANG['12_batch_pic'] = '图片批量处理';
//$_LANG['06_goods_brand_list'] = '商品品牌';
//$_LANG['13_batch_add'] = '商品批量上传';
//$_LANG['15_batch_edit'] = '商品批量修改';
//$_LANG['16_goods_script'] = '生成商品代码';
//$_LANG['17_tag_manage'] = '标签管理';
//$_LANG['18_product_list'] = '货品列表';
//$_LANG['52_attribute_add'] = '编辑属性';
//$_LANG['53_suppliers_goods'] = '供货商商品管理';
//$_LANG['14_goods_export'] = '商品批量导出';
//$_LANG['50_virtual_card_list'] = '虚拟商品列表';
//$_LANG['51_virtual_card_add'] = '添加虚拟商品';
//$_LANG['52_virtual_card_change'] = '更改加密串';
//$_LANG['goods_auto'] = '商品自动上下架';
//$_LANG['article_auto'] = '文章自动发布';
//$_LANG['navigator'] = '自定义导航栏';


/* 促销管理 */
//$_LANG['02_snatch_list'] = '夺宝奇兵';
//$_LANG['snatch_add'] = '添加夺宝奇兵';
//$_LANG['04_bonustype_list'] = '红包类型';
//$_LANG['bonustype_add'] = '添加红包类型';
//$_LANG['05_bonus_list'] = '线下红包';
//$_LANG['bonus_add'] = '添加会员红包';
//$_LANG['06_pack_list'] = '商品包装';
//$_LANG['07_card_list'] = '祝福贺卡';
//$_LANG['pack_add'] = '添加新包装';
//$_LANG['card_add'] = '添加新贺卡';
//$_LANG['08_group_buy'] = '团购活动';
//$_LANG['09_topic'] = '专题管理';
//$_LANG['topic_add'] = '添加专题';
//$_LANG['topic_list'] = '专题列表';
//$_LANG['10_auction'] = '拍卖活动';
//$_LANG['12_favourable'] = '优惠活动';
//$_LANG['13_wholesale'] = '批发管理';
//$_LANG['ebao_commend'] = '易宝推荐';
//$_LANG['14_package_list'] = '超值礼包';
//$_LANG['package_add'] = '添加超值礼包';

/* 订单管理 */
$_LANG['02_order_list'] = '订单列表';
$_LANG['03_order_query'] = '订单查询';
$_LANG['04_merge_order'] = '合并订单';
$_LANG['05_edit_order_print'] = '订单打印';
$_LANG['06_undispose_booking'] = '缺货登记';
$_LANG['08_add_order'] = '添加订单';
$_LANG['09_delivery_order'] = '发货单列表';
$_LANG['10_back_order'] = '退货单列表';
$_LANG['11_order_reminder'] = '订单催单';

/* 广告管理 */
$_LANG['ad_position'] = '广告位置';
$_LANG['ad_list'] = '广告列表';

/* 报表统计 */
$_LANG['flow_stats'] = '流量分析';
$_LANG['searchengine_stats'] = '搜索引擎';
$_LANG['report_order'] = '订单统计';
$_LANG['report_sell'] = '销售概况';
$_LANG['sell_stats'] = '销售排行';
$_LANG['sale_list'] = '销售明细';
$_LANG['report_guest'] = '客户统计';
$_LANG['report_users'] = '会员排行';
$_LANG['visit_buy_per'] = '访问购买率';
$_LANG['z_clicks_stats'] = '站外投放JS';
$_LANG['credit_analysis'] = '信用分析';
$_LANG['sales_analysis'] = '销售分析';

$_LANG['13_custom_stats'] = '下游客户对账单';
$_LANG['14_suppliers_stats'] = '供应商对账单';
$_LANG['15_contract_stats'] = '项目内部对账单';

/* 文章管理 */
$_LANG['02_articlecat_list'] = '分类';
$_LANG['articlecat_add'] = '添加分类';
$_LANG['03_article_list'] = '列表';
$_LANG['article_add'] = '添加内容';
$_LANG['shop_article'] = '网店文章';
$_LANG['shop_info'] = '网店信息';
$_LANG['shop_help'] = '网店帮助';
$_LANG['vote_list'] = '在线调查';

/* 物融新闻管理 */
$_LANG['02_wrnewscat_list'] = '物融新闻分类';
$_LANG['wrnewscat_add'] = '添加新闻分类';
$_LANG['03_wrnews_list'] = '物融新闻内容列表';
$_LANG['wrnews_add'] = '添加新闻';

/* 商城公告管理 */
$_LANG['02_noticecat_list'] = '商城公告分类';
$_LANG['noticecat_add'] = '添加公告分类';
$_LANG['03_notice_list'] = '商城公告内容列表';
$_LANG['notice_add'] = '添加公告';

/* 帮助中心管理 */
$_LANG['02_helpcentercat_list'] = '帮助中心分类';
$_LANG['helpcentercat_add'] = '添加分类';
$_LANG['03_helpcenter_list'] = '帮助中心内容列表';
$_LANG['helpcenter_add'] = '添加文章';

/* 工程管理 */
$_LANG['03_recommendorder_list'] = '推荐订单';
$_LANG['recommendorder_add'] = '添加推荐订单';

/* 会员管理 */
$_LANG['08_unreply_msg'] = '会员留言';
$_LANG['03_users_list'] = '会员列表';
$_LANG['04_users_add'] = '添加会员';
$_LANG['05_user_rank_list'] = '会员等级';
$_LANG['06_list_integrate'] = '会员整合';
$_LANG['09_user_account'] = '充值和提现申请';
$_LANG['10_user_account_manage'] = '资金管理';

/* 权限管理 */
$_LANG['admin_list'] = '管理员列表';
$_LANG['admin_list_role'] = '角色列表';
$_LANG['admin_role'] = '角色管理';
$_LANG['admin_add'] = '添加管理员';
$_LANG['admin_add_role'] = '添加角色';
$_LANG['admin_edit_role'] = '修改角色';
$_LANG['admin_logs'] = '管理员日志';
$_LANG['agency_list'] = '办事处列表';
$_LANG['suppliers_list'] = '供货商列表';

/* 系统设置 */
$_LANG['01_shop_config'] = '基本设置';
$_LANG['shop_authorized'] = '授权证书';
$_LANG['02_payment_list'] = '支付方式';
$_LANG['03_shipping_list'] = '配送方式';
$_LANG['04_mail_settings'] = '邮件服务器设置';
$_LANG['05_area_list'] = '地区列表';
$_LANG['08_friendlink_list'] = '友情链接';
$_LANG['shipping_area_list'] = '配送区域';
$_LANG['sitemap'] = '站点地图';
$_LANG['check_file_priv'] = '文件权限检测';
$_LANG['captcha_manage'] = '验证码管理';



$_LANG['affiliate'] = '推荐设置';
$_LANG['affiliate_ck'] = '分成管理';
$_LANG['flashplay'] = '首页主广告管理';
$_LANG['search_log'] = '搜索关键字';
$_LANG['email_list'] = '邮件订阅管理';
$_LANG['magazine_list'] = '杂志管理';
$_LANG['attention_list'] = '关注管理';
$_LANG['view_sendlist'] = '邮件队列管理';

$_LANG['customize_list'] = '物资定制';
$_LANG['custom_list'] = '工程定制';

$_LANG['service_complaint'] = '投诉意见';
$_LANG['service_appointment'] = '预约电话';

$_LANG['01_credit_evaluation'] = '信用评测申请';
$_LANG['02_credit_quota_add'] = '信用额度追加申请';
$_LANG['03_purchase_quota_add'] = '采购额度追加申请';
$_LANG['04_credit_intrinfo'] = '信用池介绍';
$_LANG['05_credit_recovery_history'] = '信用恢复历史';
$_LANG['06_bill_notice'] = '票据到期提醒';
// $_LANG['07_contract'] = '合同数据'; //有bug
$_LANG['08_credit_class'] = '信用等级介绍';

$_LANG['engineering_bidding'] = '工程招标';
$_LANG['engineering_bidding_list'] = '工程招标列表';
$_LANG['engineering_bidding_add'] = '发布招标';
$_LANG['recommend_goods'] = '商品推荐';
$_LANG['recommend_goods_list'] = '商品推荐列表';
$_LANG['recommend_goods_add'] = '发布推荐';
$_LANG['engineering_links'] = '友情链接';
/* 积分兑换管理 */

/* cls_image类的语言项 */
$_LANG['directory_readonly'] = '目录 % 不存在或不可写';
$_LANG['invalid_upload_image_type'] = '不是允许的图片格式';
$_LANG['upload_failure'] = '文件 %s 上传失败。';
$_LANG['missing_gd'] = '没有安装GD库';
$_LANG['missing_orgin_image'] = '找不到原始图片 %s ';
$_LANG['nonsupport_type'] = '不支持该图像格式 %s ';
$_LANG['creating_failure'] = '创建图片失败';
$_LANG['writting_failure'] = '图片写入失败';
$_LANG['empty_watermark'] = '水印文件参数不能为空';
$_LANG['missing_watermark'] = '找不到水印文件%s';
$_LANG['create_watermark_res'] = '创建水印图片资源失败。水印图片类型为%s';
$_LANG['create_origin_image_res'] = '创建原始图片资源失败，原始图片类型%s';
$_LANG['invalid_image_type'] = '无法识别水印图片 %s ';
$_LANG['file_unavailable'] = '文件 %s 不存在或不可读';

/* 邮件发送错误信息 */
$_LANG['smtp_setting_error'] = '邮件服务器设置信息不完整';
$_LANG['smtp_connect_failure'] = '无法连接到邮件服务器 %s';
$_LANG['smtp_login_failure'] = '邮件服务器验证帐号或密码不正确';
$_LANG['sendemail_false'] = '邮件发送失败，请检查您的邮件服务器设置！';
$_LANG['smtp_refuse'] = '服务器拒绝发送该邮件';
$_LANG['disabled_fsockopen'] = '服务器已禁用 fsocketopen 函数。';

/* 虚拟卡 */
$_LANG['virtual_card_oos'] = '虚拟卡已缺货';

$_LANG['span_edit_help'] = '点击修改内容';
$_LANG['href_sort_help'] = '点击对列表排序';

$_LANG['catname_exist'] = '已存在相同的分类名称!';
$_LANG['brand_name_exist'] = '已存在相同的品牌名称!';

$_LANG['alipay_login'] = '<a href="https://www.alipay.com/user/login.htm?goto=https%3A%2F%2Fwww.alipay.com%2Fhimalayas%2Fpracticality_profile_edit.htm%3Fmarket_type%3Dfrom_agent_contract%26customer_external_id%3D%2BC4335319945672464113" target="_blank">立即免费申请支付接口权限</a>';
$_LANG['alipay_look'] = '<a href=\"https://www.alipay.com/himalayas/practicality.htm\" target=\"_blank\">请申请成功后登录支付宝账户查看</a>';
?>