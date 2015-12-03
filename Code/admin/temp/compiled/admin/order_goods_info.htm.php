<table width="100%" cellpadding="3" cellspacing="1">
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
        <td>
            <img src="<?php echo $this->_var['goods']['goods_thumb']; ?>" width="130" height="130" /><br />
            <span><?php echo $this->_var['goods']['goods_name']; ?><?php if ($this->_var['goods']['brand_name']): ?>[ <?php echo $this->_var['goods']['brand_name']; ?> ]<?php endif; ?></span>
        </td>
        <td class="order-goods-info"><?php echo $this->_var['goods']['formated_goods_price']; ?></td>
        <td class="order-goods-info"><?php echo $this->_var['goods']['goods_number']; ?></td>
        <td class="order-goods-info"><?php echo $this->_var['goods']['storage']; ?></td>
        <td class="order-goods-info"><?php echo $this->_var['goods']['formated_subtotal']; ?></td>
    </tr>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>