<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'topbar.js,../js/utils.js,listtable.js,selectzone.js,../js/common.js,sign.js')); ?>
<?php if ($this->_var['user']): ?>
<div id="topbar">
    <div align="right"><a href="" onclick="closebar(); return false"><img src="images/close.gif" border="0" /></a></div>
    <table width="100%" border="0">
        <caption><strong> <?php echo $this->_var['lang']['buyer_info']; ?> </strong></caption>
        <tr>
            <td> <?php echo $this->_var['lang']['email']; ?> </td>
            <td> <a href="mailto:<?php echo $this->_var['user']['email']; ?>"><?php echo $this->_var['user']['email']; ?></a> </td>
        </tr>
        <tr>
            <td> <?php echo $this->_var['lang']['user_money']; ?> </td>
            <td> <?php echo $this->_var['user']['formated_user_money']; ?> </td>
        </tr>
        <tr>
            <td> <?php echo $this->_var['lang']['pay_points']; ?> </td>
            <td> <?php echo $this->_var['user']['pay_points']; ?> </td>
        </tr>
        <tr>
            <td> <?php echo $this->_var['lang']['rank_points']; ?> </td>
            <td> <?php echo $this->_var['user']['rank_points']; ?> </td>
        </tr>
        <tr>
            <td> <?php echo $this->_var['lang']['rank_name']; ?> </td>
            <td> <?php echo $this->_var['user']['rank_name']; ?> </td>
        </tr>
        <tr>
            <td> <?php echo $this->_var['lang']['bonus_count']; ?> </td>
            <td> <?php echo $this->_var['user']['bonus_count']; ?> </td>
        </tr>
    </table>

    <?php $_from = $this->_var['address_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'address');if (count($_from)):
    foreach ($_from AS $this->_var['address']):
?>
    <table width="100%" border="0">
        <caption><strong> <?php echo $this->_var['lang']['consignee']; ?> : <?php echo htmlspecialchars($this->_var['address']['consignee']); ?> </strong></caption>
        <tr>
            <td> <?php echo $this->_var['lang']['email']; ?> </td>
            <td> <a href="mailto:<?php echo htmlspecialchars($this->_var['address']['email']); ?>"><?php echo htmlspecialchars($this->_var['address']['email']); ?></a> </td>
        </tr>
        <tr>
            <td> <?php echo $this->_var['lang']['address']; ?> </td>
            <td> <?php echo htmlspecialchars($this->_var['address']['address']); ?> </td>
        </tr>
        <tr>
            <td> <?php echo $this->_var['lang']['zipcode']; ?> </td>
            <td> <?php echo htmlspecialchars($this->_var['address']['zipcode']); ?> </td>
        </tr>
        <tr>
            <td> <?php echo $this->_var['lang']['tel']; ?> </td>
            <td> <?php echo htmlspecialchars($this->_var['address']['tel']); ?> </td>
        </tr>
        <tr>
            <td> <?php echo $this->_var['lang']['mobile']; ?> </td>
            <td> <?php echo htmlspecialchars($this->_var['address']['mobile']); ?> </td>
        </tr>
    </table>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>
<?php endif; ?>

<form action="order.php?act=operate" method="post" name="theForm">
<div class="list-div" style="margin-bottom: 5px">
    <table width="100%" cellpadding="3" cellspacing="1">
        <tr>
            <td colspan="4">
                <div align="center">
                    <input name="prev" type="button" class="button" onClick="location.href='order.php?act=info&order_id=<?php echo $this->_var['prev_id']; ?>';" value="<?php echo $this->_var['lang']['prev']; ?>" <?php if (! $this->_var['prev_id']): ?>disabled<?php endif; ?> />
                    <input name="next" type="button" class="button" onClick="location.href='order.php?act=info&order_id=<?php echo $this->_var['next_id']; ?>';" value="<?php echo $this->_var['lang']['next']; ?>" <?php if (! $this->_var['next_id']): ?>disabled<?php endif; ?> />
                    <!--
                    <input type="button" onclick="window.open('order.php?act=info&order_id=<?php echo $this->_var['order']['order_id']; ?>&print=1')" class="button" value="<?php echo $this->_var['lang']['print_order']; ?>" />
                    -->
                </div>
            </td>
        </tr>
        <!-- 订单基本信息 -->
        <tr>
            <th colspan="4"><?php echo $this->_var['lang']['base_info']; ?></th>
        </tr>
        <tr>
            <td width="18%"><div align="right"><strong><?php echo $this->_var['lang']['label_order_sn']; ?></strong></div></td>
            <td width="34%"><?php echo $this->_var['order']['order_sn']; ?><?php if ($this->_var['order']['extension_code'] == "group_buy"): ?><a href="group_buy.php?act=edit&id=<?php echo $this->_var['order']['extension_id']; ?>"><?php echo $this->_var['lang']['group_buy']; ?></a><?php elseif ($this->_var['order']['extension_code'] == "exchange_goods"): ?><a href="exchange_goods.php?act=edit&id=<?php echo $this->_var['order']['extension_id']; ?>"><?php echo $this->_var['lang']['exchange_goods']; ?></a><?php endif; ?></td>
            <td width="15%"><div align="right"><strong><?php echo $this->_var['lang']['label_order_status']; ?></strong></div></td>
            <td><?php echo $this->_var['order']['status']; ?></td>
        </tr>
        <tr>
            <td><div align="right"><strong><?php echo $this->_var['lang']['label_user_name']; ?></strong></div></td>
            <td><?php echo empty($this->_var['order']['user_name']) ? $this->_var['lang']['anonymous'] : $this->_var['order']['user_name']; ?></td>
            <td><div align="right"><strong><?php echo $this->_var['lang']['label_order_time']; ?></strong></div></td>
            <td><?php echo $this->_var['order']['formated_add_time']; ?></td>
        </tr>
        <!--
        <tr>
            <td><div align="right"><strong><?php echo $this->_var['lang']['label_payment']; ?></strong></div></td>
            <td>
                <?php if ($this->_var['order']['pay_id'] > 0): ?><?php echo $this->_var['order']['pay_name']; ?><?php else: ?><?php echo $this->_var['lang']['require_field']; ?><?php endif; ?>
            </td>
            <td><div align="right"><strong><?php echo $this->_var['lang']['label_pay_time']; ?></strong></div></td>
            <td><?php echo $this->_var['order']['pay_time']; ?></td>
        </tr>
        -->
        <!-- 其他信息 -->
        <tr>
            <th colspan="4"><?php echo $this->_var['lang']['other_info']; ?></th>
        </tr>
        <tr>
            <td><div align="right"><strong><?php echo $this->_var['lang']['label_inv_type']; ?></strong></div></td>
            <td><?php echo $this->_var['lang']['invType'][$this->_var['order']['inv_type']]; ?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><div align="right"><strong><?php echo $this->_var['lang']['label_inv_payee']; ?></strong></div></td>
            <td><?php echo $this->_var['order']['inv_payee']; ?></td>
            <td><div align="right"><strong><?php echo $this->_var['lang']['label_inv_content']; ?></strong></div></td>
            <td><?php echo $this->_var['order']['inv_content']; ?></td>
        </tr>
        <tr>
            <td><div align="right"><strong><?php echo $this->_var['lang']['label_postscript']; ?></strong></div></td>
            <td colspan="3"><?php echo $this->_var['order']['postscript']; ?></td>
        </tr>
        <!-- 收货人信息-->
        <tr>
            <th colspan="4"><?php echo $this->_var['lang']['consignee_info']; ?></th>
        </tr>
        <tr>
            <td><div align="right"><strong><?php echo $this->_var['lang']['label_consignee']; ?></strong></div></td>
            <td><?php echo htmlspecialchars($this->_var['order']['consignee']); ?></td>
            <td><div align="right"><strong><?php echo $this->_var['lang']['label_address']; ?></strong></div></td>
            <td><?php echo htmlspecialchars($this->_var['order']['address']); ?></td>
        </tr>
        <tr>
            <td><div align="right"><strong><?php echo $this->_var['lang']['label_mobile']; ?></strong></div></td>
            <td><?php echo htmlspecialchars($this->_var['order']['mobile']); ?></td>
            <td><div align="right"><strong><?php echo $this->_var['lang']['label_sign_building']; ?></strong></div></td>
            <td><?php echo htmlspecialchars($this->_var['order']['sign_building']); ?></td>
        </tr>
    </table>
</div>

<div class="list-div" style="margin-bottom: 5px">
    <table width="100%" cellpadding="3" cellspacing="1">
        <tr>
            <th colspan="8" scope="col"><?php echo $this->_var['lang']['goods_info']; ?></th>
        </tr>
        <tr>
            <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_name_brand']; ?></strong></div></td>
            <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_price']; ?></strong></div></td>
            <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_number']; ?></strong></div></td>

            <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['storage']; ?></strong></div></td>
            <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['subtotal']; ?></strong></div></td>
        </tr>
        <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
        <tr>
            <td><?php echo $this->_var['goods']['goods_name']; ?> <?php if ($this->_var['goods']['brand_name']): ?>[ <?php echo $this->_var['goods']['brand_name']; ?> ]<?php endif; ?></td>
            <td><div align="right"><?php echo $this->_var['goods']['formated_goods_price']; ?></div></td>
            <td><div align="right"><?php echo $this->_var['goods']['goods_number']; ?></div></td>
            <td><div align="right"><?php echo $this->_var['goods']['storage']; ?></div></td>
            <td><div align="right"><?php echo $this->_var['goods']['formated_subtotal']; ?></div></td>
        </tr>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        <tr>
            <td></td>
            <td><?php if ($this->_var['order']['total_weight']): ?><div align="right"><strong><?php echo $this->_var['lang']['label_total_weight']; ?>
            </strong></div><?php endif; ?></td>
            <td><?php if ($this->_var['order']['total_weight']): ?><div align="right"><?php echo $this->_var['order']['total_weight']; ?>
            </div><?php endif; ?></td>
            <td><div align="right"><strong><?php echo $this->_var['lang']['label_total']; ?></strong></div></td>
            <td><div align="right"><?php echo $this->_var['order']['formated_goods_amount']; ?></div></td>
        </tr>
    </table>
</div>

<!--
<div class="list-div" style="margin-bottom: 5px">
    <table width="100%" cellpadding="3" cellspacing="1">
        <tr>
            <th><?php echo $this->_var['lang']['fee_info']; ?><a href="order.php?act=edit&order_id=<?php echo $this->_var['order']['order_id']; ?>&step=money" class="special"><?php echo $this->_var['lang']['edit']; ?></a></th>
        </tr>
        <tr>
            <td><div align="right"><?php echo $this->_var['lang']['label_goods_amount']; ?><strong><?php echo $this->_var['order']['formated_goods_amount']; ?></strong>
                - <?php echo $this->_var['lang']['label_discount']; ?><strong><?php echo $this->_var['order']['formated_discount']; ?></strong>     + <?php echo $this->_var['lang']['label_tax']; ?><strong><?php echo $this->_var['order']['formated_tax']; ?></strong>
                + <?php echo $this->_var['lang']['label_shipping_fee']; ?><strong><?php echo $this->_var['order']['formated_shipping_fee']; ?></strong>
                + <?php echo $this->_var['lang']['label_insure_fee']; ?><strong><?php echo $this->_var['order']['formated_insure_fee']; ?></strong>
                + <?php echo $this->_var['lang']['label_pay_fee']; ?><strong><?php echo $this->_var['order']['formated_pay_fee']; ?></strong>
                + <?php echo $this->_var['lang']['label_pack_fee']; ?><strong><?php echo $this->_var['order']['formated_pack_fee']; ?></strong>
                + <?php echo $this->_var['lang']['label_card_fee']; ?><strong><?php echo $this->_var['order']['formated_card_fee']; ?></strong></div></td>
        <tr>
            <td><div align="right"> = <?php echo $this->_var['lang']['label_order_amount']; ?><strong><?php echo $this->_var['order']['formated_total_fee']; ?></strong></div></td>
        </tr>
        <tr>
            <td><div align="right">
                - <?php echo $this->_var['lang']['label_money_paid']; ?><strong><?php echo $this->_var['order']['formated_money_paid']; ?></strong> - <?php echo $this->_var['lang']['label_surplus']; ?> <strong><?php echo $this->_var['order']['formated_surplus']; ?></strong>
                - <?php echo $this->_var['lang']['label_integral']; ?> <strong><?php echo $this->_var['order']['formated_integral_money']; ?></strong>
                - <?php echo $this->_var['lang']['label_bonus']; ?> <strong><?php echo $this->_var['order']['formated_bonus']; ?></strong>
            </div></td>
        <tr>
            <td><div align="right"> = <?php if ($this->_var['order']['order_amount'] >= 0): ?><?php echo $this->_var['lang']['label_money_dues']; ?><strong><?php echo $this->_var['order']['formated_order_amount']; ?></strong>
                <?php else: ?><?php echo $this->_var['lang']['label_money_refund']; ?><strong><?php echo $this->_var['order']['formated_money_refund']; ?></strong>
                <input name="refund" type="button" value="<?php echo $this->_var['lang']['refund']; ?>" onclick="location.href='order.php?act=process&func=load_refund&anonymous=<?php if ($this->_var['order']['user_id'] <= 0): ?>1<?php else: ?>0<?php endif; ?>&order_id=<?php echo $this->_var['order']['order_id']; ?>&refund_amount=<?php echo $this->_var['order']['money_refund']; ?>'" />
                <?php endif; ?><?php if ($this->_var['order']['extension_code'] == "group_buy"): ?><br /><?php echo $this->_var['lang']['notice_gb_order_amount']; ?><?php endif; ?></div></td>
        </tr>
    </table>
</div>
-->

<div class="list-div" style="margin-bottom: 5px;<?php if ($this->_var['order']['parent_order_id'] != 0): ?>display:none;<?php endif; ?>">
    <table cellpadding="3" cellspacing="1">
        <tr>
            <th colspan="6"><?php echo $this->_var['lang']['action_info']; ?></th>
        </tr>
        <tr>
            <td><div align="right"><strong><?php echo $this->_var['lang']['label_action_note']; ?></strong></div></td>
            <td colspan="5"><textarea name="action_note" cols="80" rows="3"></textarea></td>
        </tr>
        <tr>
            <td>
                <div align="right"></div>
                <div align="right"><strong><?php echo $this->_var['lang']['label_operable_act']; ?></strong> </div>
            </td>
            <td colspan="5">
                <?php if ($this->_var['operable_list']['confirm']): ?>
                <input name="confirm" type="submit" value="<?php echo $this->_var['lang']['op_confirm']; ?>" class="button" />
				<script>
					var basepath = "";
					document.write('<div id="kpsign" style="display:none">');
					if((navigator.userAgent.indexOf("MSIE") > -1) || ((navigator.userAgent.indexOf("rv:11") > -1 && navigator.userAgent.indexOf("Firefox")<= -1)))
					{
					    //alert("IE");
					    if((navigator.platform =="Win32")) {
					        //alert("windows 32 bit IE");
					        document.write('<OBJECT id="doit" height=40 width=200 classid="CLSID:D54C3C5F-6CA8-440B-A4E5-8B43186D5DFF" codebase='+basepath+'npkoaliiCZB_x86.CAB VIEWASTEXT>')
					    } else if(navigator.platform =="Win64") {
					        //alert("windows 64 bit IE");
					        document.write('<OBJECT id="doit" height=40 width=200 classid="CLSID:40BEE6CE-DC27-45DB-A07E-42A625547B45" codebase='+basepath+'npkoaliiCZB_x64.CAB VIEWASTEXT>')
					    }
					}
					document.write('<param name="DigitalSignature" value="1">');
					document.write('<param name="SignMethod" value="2">');
					document.write('</OBJECT>');
					document.write('</div>');
					document.write('<input type="hidden" name="step" value="1" />');
				</script>
                <?php endif; ?> <?php if ($this->_var['operable_list']['pay']): ?>
                <input name="pay" type="submit" value="<?php echo $this->_var['lang']['op_pay']; ?>" class="button" />
                <?php endif; ?> <?php if ($this->_var['operable_list']['unpay']): ?>
                <input name="unpay" type="submit" class="button" value="<?php echo $this->_var['lang']['op_unpay']; ?>" />
                <?php endif; ?> <?php if ($this->_var['operable_list']['prepare']): ?>
                <input name="prepare" type="submit" value="<?php echo $this->_var['lang']['op_prepare']; ?>" class="button" />
                <?php endif; ?> <?php if ($this->_var['operable_list']['split']): ?>
                <input name="ship" type="submit" value="<?php echo $this->_var['lang']['op_split']; ?>" class="button" />
                <?php endif; ?> <?php if ($this->_var['operable_list']['unship']): ?>
                <input name="unship" type="submit" value="<?php echo $this->_var['lang']['op_unship']; ?>" class="button" />
                <?php endif; ?> <?php if ($this->_var['operable_list']['receive']): ?>
                <input name="receive" type="submit" value="<?php echo $this->_var['lang']['op_receive']; ?>" class="button" />
                <?php endif; ?> <?php if ($this->_var['operable_list']['cancel']): ?>
                <input name="cancel" type="submit" value="<?php echo $this->_var['lang']['op_cancel']; ?>" class="button" />
                <?php endif; ?> <?php if ($this->_var['operable_list']['invalid']): ?>
                <input name="invalid" type="submit" value="<?php echo $this->_var['lang']['op_invalid']; ?>" class="button" />
                <?php endif; ?> <?php if ($this->_var['operable_list']['return']): ?>
                <input name="return" type="submit" value="<?php echo $this->_var['lang']['op_return']; ?>" class="button" />
                <?php endif; ?> <?php if ($this->_var['operable_list']['to_delivery']): ?>
                <input name="to_delivery" type="submit" value="<?php echo $this->_var['lang']['op_to_delivery']; ?>" class="button"/>
                <input name="order_sn" type="hidden" value="<?php echo $this->_var['order']['order_sn']; ?>" />
                <?php endif; ?>
                <!--
                <input name="after_service" type="submit" value="<?php echo $this->_var['lang']['op_after_service']; ?>" class="button" />
                -->
                <?php if ($this->_var['operable_list']['remove']): ?>
                <input name="remove" type="submit" value="<?php echo $this->_var['lang']['remove']; ?>" class="button" onClick="return window.confirm('<?php echo $this->_var['lang']['js_languages']['remove_confirm']; ?>');" />
                <?php endif; ?>
                <?php if ($this->_var['order']['extension_code'] == "group_buy"): ?><?php echo $this->_var['lang']['notice_gb_ship']; ?><?php endif; ?>
                <!--
                <?php if ($this->_var['agency_list']): ?>
                <input name="assign" type="submit" value="<?php echo $this->_var['lang']['op_assign']; ?>" class="button" onclick="return assignTo(document.forms['theForm'].elements['agency_id'].value)" />
                <select name="agency_id"><option value="0"><?php echo $this->_var['lang']['select_please']; ?></option>
                    <?php $_from = $this->_var['agency_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'agency');if (count($_from)):
    foreach ($_from AS $this->_var['agency']):
?>
                    <option value="<?php echo $this->_var['agency']['agency_id']; ?>" <?php if ($this->_var['agency']['agency_id'] == $this->_var['order']['agency_id']): ?>selected<?php endif; ?>><?php echo $this->_var['agency']['agency_name']; ?></option>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </select>
                <?php endif; ?>
                -->
                <input name="order_id" type="hidden" value="<?php echo $_REQUEST['order_id']; ?>">
                <?php if ($this->_var['order']['order_status'] == 1): ?>
                <input type="button" class="button" value="<?php echo $this->_var['lang']['split_orders_info']; ?>" onclick="window.location.href='order.php?act=split&order_id=<?php echo $this->_var['order']['order_id']; ?>'"/>
                <input type="button" class="button" value="取消订单" name="confirm" />
                <script>
					var basepath = "";
					document.write('<div id="kpsign" style="display:none">');
					if((navigator.userAgent.indexOf("MSIE") > -1) || ((navigator.userAgent.indexOf("rv:11") > -1 && navigator.userAgent.indexOf("Firefox")<= -1)))
					{
					    //alert("IE");
					    if((navigator.platform =="Win32")) {
					        //alert("windows 32 bit IE");
					        document.write('<OBJECT id="doit" height=40 width=200 classid="CLSID:D54C3C5F-6CA8-440B-A4E5-8B43186D5DFF" codebase='+basepath+'npkoaliiCZB_x86.CAB VIEWASTEXT>')
					    } else if(navigator.platform =="Win64") {
					        //alert("windows 64 bit IE");
					        document.write('<OBJECT id="doit" height=40 width=200 classid="CLSID:40BEE6CE-DC27-45DB-A07E-42A625547B45"  codebase='+basepath+'npkoaliiCZB_x64.CAB VIEWASTEXT>')
					    }
					}
					document.write('<param name="DigitalSignature" value="1">');
					document.write('<param name="SignMethod" value="2">');
					document.write('</OBJECT>');
					document.write('</div>');
					document.write('<input type="hidden" name="step" value="2" />');
				</script>
                <?php endif; ?>
                <?php if ($this->_var['order']['order_status'] == 3): ?>
                <input type="button" class="button" value="对账验签" name="confirm">
                <script>
					var basepath = "";
					document.write('<div id="kpsign" style="display:none">');
					if((navigator.userAgent.indexOf("MSIE") > -1) || ((navigator.userAgent.indexOf("rv:11") > -1 && navigator.userAgent.indexOf("Firefox")<= -1)))
					{
					    //alert("IE");
					    if((navigator.platform =="Win32")) {
					        //alert("windows 32 bit IE");
					        document.write('<OBJECT id="doit" height=40 width=200 classid="CLSID:D54C3C5F-6CA8-440B-A4E5-8B43186D5DFF" codebase='+basepath+'npkoaliiCZB_x86.CAB VIEWASTEXT>')
					    } else if(navigator.platform =="Win64") {
					        //alert("windows 64 bit IE");
					        document.write('<OBJECT id="doit" height=40 width=200 classid="CLSID:40BEE6CE-DC27-45DB-A07E-42A625547B45"  codebase='+basepath+'npkoaliiCZB_x64.CAB VIEWASTEXT>')
					    }
					}
					document.write('<param name="DigitalSignature" value="1">');
					document.write('<param name="SignMethod" value="2">');
					document.write('</OBJECT>');
					document.write('</div>');
					document.write('<input type="hidden" name="step" value="1" />');
				</script>
                <?php endif; ?>
            </td>
        </tr>
        <!--
        <tr>
            <th><?php echo $this->_var['lang']['action_user']; ?></th>
            <th><?php echo $this->_var['lang']['action_time']; ?></th>
            <th><?php echo $this->_var['lang']['order_status']; ?></th>
            <th><?php echo $this->_var['lang']['pay_status']; ?></th>
            <th><?php echo $this->_var['lang']['shipping_status']; ?></th>
            <th><?php echo $this->_var['lang']['action_note']; ?></th>
        </tr>
        <?php $_from = $this->_var['action_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'action');if (count($_from)):
    foreach ($_from AS $this->_var['action']):
?>
        <tr>
            <td><div align="center"><?php echo $this->_var['action']['action_user']; ?></div></td>
            <td><div align="center"><?php echo $this->_var['action']['action_time']; ?></div></td>
            <td><div align="center"><?php echo $this->_var['action']['order_status']; ?></div></td>
            <td><div align="center"><?php echo $this->_var['action']['pay_status']; ?></div></td>
            <td><div align="center"><?php echo $this->_var['action']['shipping_status']; ?></div></td>
            <td><?php echo nl2br($this->_var['action']['action_note']); ?></td>
        </tr>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        -->
    </table>
</div>

</form>
<script type="text/javascript">
    var oldAgencyId = <?php echo empty($this->_var['order']['agency_id']) ? '0' : $this->_var['order']['agency_id']; ?>;
    
    onload = function()
    {
        // 开始检查订单
        startCheckOrder();
    }

    /**
     * 把订单指派给某办事处
     * @param int agencyId
     */
    function assignTo(agencyId)
    {
        if (agencyId == 0)
        {
            alert(pls_select_agency);
            return false;
        }
        if (oldAgencyId != 0 && agencyId == oldAgencyId)
        {
            alert(pls_select_other_agency);
            return false;
        }
        return true;
    }

    
    
    //获取签名数据url
    var signUrl = <?php if ($this->_var['operable_list']['confirm']): ?>'/bank/getSubmitData'<?php elseif ($this->_var['order']['order_status'] == 3): ?>'/bank/getconfirmdata'<?php else: ?>'/bank/getCancelData'<?php endif; ?>;

  	//合同验签
    $('body').on('click', 'input[name="confirm"]', function(e) {
        e.preventDefault();

        var that = $(this);
        if(that.hasClass('disabled')){
            return;
        }

        //检测浏览器，当前只在ie可用
        var msie = /msie/.test(navigator.userAgent.toLowerCase());
        var msie11 = /rv:11/.test(navigator.userAgent.toLowerCase());//ie11
        if(!msie && !msie11){
        	alert('验签只能在IE中使用');
        	return;
        }

        //获取订单签名数据
        $.ajax({
            url: signUrl,
            data: {
                orderId: <?php echo $this->_var['order']['order_id']; ?>//当前订单id
            },
            dataType: 'text',
            type: 'POST',
	        success: function(response){
	        	try{
		            tempResponse = JSON.parse(response);
	        	}catch(e){
	        		tempResponse = {};
	        	}

	            if(tempResponse.code != 0 || !tempResponse.body){
	                alert('获取订单签名数据失败');
	                return;
	            }

	            var step = $('input[name="step"]').val();
	            //生成签名数据
	            var signData = getSignData(step, tempResponse.body.signRawData);
	            
	            if(!signData.success){
	            	alert('生成签名数据失败！' + signData.errorInfo);
	                return;
	            }

				var submitSignUrl = <?php if ($this->_var['operable_list']['confirm']): ?>'/bank/submitContractAdmin'<?php elseif ($this->_var['order']['order_status'] == 3): ?>'/bank/confirmcontractadmin'<?php else: ?>'/bank/cancelContractAdmin'<?php endif; ?>;

	            $.ajax({
	                url: submitSignUrl,
	                data: {
	                    'orderSn': tempResponse.body.orderSn,
	                    'signId': tempResponse.body.signId,
	                    'salerSign': signData.data
	                },
	                dataType: 'text',
	                type: 'POST',
		            success: function(response){
			        	try{
			        		response = JSON.parse(response);
			        	}catch(e){
			        		response = {};
			        	}
		                if (response.code != 0) {
		                	alert(response.message);
		                    return;
		                }
		                alert('签名成功！');
		            },
		            error: function(jqXHR, textStatus, errorThrown){
		            	alert('签名失败！');
		            }
	            });
	        },
	        error:function(xhr, textStatus, errorThrown){
	        	alert('获取签名数据失败！');
	        }
        });
    });
  	

	//生成签名数据
	function getSignData(step, data) {
		var result = {};
		if(typeof doit == 'undefined'){
			doit = document.getElementById('doit');
		}
		if(typeof doit == 'undefined'){
			result.success = false;
			result.errorInfo = '请插入object标签';
			return result;
		}
		var signData;
		switch (step) {
			case "1": //提交合同签名 flag：0买方，1卖方
				signData = koalSign4submitContract(1, data);
			break;
			case "2": //取消合同签名 flag：0买方，1卖方
				signData = koalSign4cancelContract(1, data);
			break;
			case "3": // 内部数据签名
				signData = koalSign4zjwcCheck(data);
			break;
			default:
				
			break;
		}
		
		if(!signData.success) {
			result.success = false;
			result.errorInfo = signData.msg;
			result.data = "";
		} else {
			result.success = true,
			result.errorInfo = "";
			result.data = signData.data;
		}

		return result;
	}

</script>

<?php echo $this->fetch('pagefooter.htm'); ?>