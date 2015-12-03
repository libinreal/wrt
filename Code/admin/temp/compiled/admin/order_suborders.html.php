<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<!-- 子订单列表 -->
<form method="post" action="" name="listForm">
    <div class="list-div" id="listDiv" style="position: relative">
        <?php endif; ?>
        <table cellpadding="3" cellspacing="1">
            <tr>
                <th><?php echo $this->_var['lang']['order_sn']; ?></th>
                <th><?php echo $this->_var['lang']['contract_sn']; ?></th>
                <th><?php echo $this->_var['lang']['order_time']; ?></th>
                <th><?php echo $this->_var['lang']['consignee']; ?></th>
                <th><?php echo $this->_var['lang']['total_fee']; ?></th>
                <th><?php echo $this->_var['lang']['all_status']; ?></th>
            <tr>
            <?php $_from = $this->_var['suborders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('okey', 'order');if (count($_from)):
    foreach ($_from AS $this->_var['okey'] => $this->_var['order']):
?>
            <tr style="position: relative;">
                <td valign="top" nowrap="nowrap">
                    <a class="stateOrder" style="display: block;width:240px;height: 30px;" data-order="<?php echo $this->_var['order']['order_id']; ?>" href="order.php?act=info&order_id=<?php echo $this->_var['order']['order_id']; ?>" id="order_<?php echo $this->_var['okey']; ?>"><?php echo $this->_var['order']['order_sn']; ?></a>
                    <div class="order-goods" order_id="<?php echo $this->_var['order']['order_id']; ?>" style="width: 80%;border:1px solid #ccc;height: auto;background: #fff;position: absolute;display:none;left: 5%;top: 30;z-index: 9999;"></div>
                </td>
                <td><?php echo empty($this->_var['order']['contract_sn']) ? 'N/A' : $this->_var['order']['contract_sn']; ?></td>
                <td><?php echo htmlspecialchars($this->_var['order']['buyer']); ?><br /><?php echo $this->_var['order']['short_order_time']; ?></td>
                <td align="left"><a href="mailto:<?php echo $this->_var['order']['email']; ?>"> <?php echo htmlspecialchars($this->_var['order']['consignee']); ?></a><?php if ($this->_var['order']['tel']): ?> [TEL: <?php echo htmlspecialchars($this->_var['order']['tel']); ?>]<?php endif; ?> <br /><?php echo htmlspecialchars($this->_var['order']['address']); ?></td>
                <td align="right" valign="top" nowrap="nowrap"><?php echo $this->_var['order']['formated_total_fee']; ?></td>
            <td align="center" valign="top" nowrap="nowrap">
                <?php if ($this->_var['order']['order_status'] != '-1'): ?>  <?php echo $this->_var['lang']['cos'][$this->_var['order']['order_status']]; ?><?php else: ?>
                    <a href="order.php?act=push&order_id=<?php echo $this->_var['order']['order_id']; ?>">重新推送</a>
                  <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td class="no-records" colspan="10"><?php echo $this->_var['lang']['no_suborders']; ?></td></tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </table>
        <?php if ($this->_var['full_page']): ?>
    </div>
</form>
<script language="JavaScript">
    <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
    listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    
    onload = function()
    {
        // 开始检查订单
        startCheckOrder();
    }

    $(document).ready(function(){
        $(document).mouseover(function(){
            $('.order-goods').hide();
        });
        $('.list-div tr').each(function(){
            $(this).find('td').eq(0).find('a').mouseover(function(){
                $('.order-goods').hide();
            var order_id=$(this).attr('data-order');
            ajax_get_goods($(this).parent().find('.order-goods'),order_id);
            });
            $(this).find('td').eq(0).find('a').mouseout(function(){
                $(this).parent().find('.order-goods').hide();
            });
        });
    });
    function ajax_get_goods(obj,order_id){
        $.ajax({
            'type':'post',
            'dataType':'text',
            'data':{'order_id':order_id},
            'url':'order.php?is_ajax=1&act=get_goods_info',
            'success':function(msg){
                msg = msg.replace(/\\r\\n/g,' ');
                msg = JSON.parse(msg);
                obj.html(msg.content[0].str);
                $('.order-goods').hide();
                obj.show();
            }
        });
    }
    
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>