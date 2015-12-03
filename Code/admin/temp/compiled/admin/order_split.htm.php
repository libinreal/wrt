<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'topbar.js,../js/utils.js,listtable.js,selectzone.js,../js/common.js')); ?>
<form action="order.php?act=operate" method="post" name="theForm">
<div class="list-div" style="margin-bottom: 5px">
    <table width="100%" cellpadding="3" cellspacing="1">
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
            <th colspan="9" scope="col"><?php echo $this->_var['lang']['goods_info']; ?></th>
        </tr>
        <tr>
            <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_name_brand']; ?></strong></div></td>
            <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_wcode']; ?></strong></div></td>
            <td scope="col"><div style="text-align: center"><?php echo $this->_var['lang']['goods_suppliers']; ?></div> </td>
            <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_price']; ?></strong></div></td>
            <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_number']; ?></strong></div></td>
            <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_delivery']; ?></strong></div></td>
            <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_measure_unit']; ?></strong></div></td>
            <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['storage']; ?></strong></div></td>
            <td scope="col"><div style="text-align: center"><strong><?php echo $this->_var['lang']['handler']; ?></strong></div></td>

            <!--<td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['subtotal']; ?></strong></div></td>-->
        </tr>
        <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
        <tr>
            <td><?php echo $this->_var['goods']['goods_name']; ?> <?php if ($this->_var['goods']['brand_name']): ?>[ <?php echo $this->_var['goods']['brand_name']; ?> ]<?php endif; ?></td>
            <td class="goods-info"><?php echo $this->_var['goods']['wcode']; ?></td>
            <td class="goods-info"><?php echo $this->_var['goods']['suppliers_name']; ?></td>
            <td class="goods-info"><?php echo $this->_var['goods']['formated_goods_price']; ?></td>
            <td class="goods-info"><?php echo $this->_var['goods']['goods_number']; ?></td>
            <td class="goods-info"><?php echo $this->_var['goods']['send_number']; ?></td>
            <td class="goods-info"><?php echo $this->_var['goods']['measure_unit']; ?></td>
            <td class="goods-info"><?php echo $this->_var['goods']['storage']; ?></td>
            <td class="goods-info" style="text-align:center;">
                <?php if ($this->_var['goods']['no_split'] == 0): ?>
                <?php echo $this->_var['lang']['no_split']; ?>
                <?php else: ?>
                <a style="display: block;width: 60px;text-align: center;line-height: 25px;height: 25px;background: #BBCADE;" href="order.php?act=goods_split&order_id=<?php echo $this->_var['order']['order_id']; ?>&goods_id=<?php echo $this->_var['goods']['goods_id']; ?>"><?php echo $this->_var['lang']['split_orders']; ?></a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
</div>
</form>

<style type="text/css">
    .goods-info {text-align: center;}
</style>
<script language="JavaScript">
    
    var oldAgencyId = <?php echo empty($this->_var['order']['agency_id']) ? '0' : $this->_var['order']['agency_id']; ?>;
    
    onload = function()
    {
        // 开始检查订单
        startCheckOrder();
    }

</script>

<?php echo $this->fetch('pagefooter.htm'); ?>