<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php endif; ?>
        
<div class="ArrList">
	<a href="exchange_category.php?act=list">分类列表</a>
	<a href="exchange_goods.php?act=list">积分商品列表</a>
	<a href="exchange_goods.php?act=add">积分商品添加</a>
    <a href="exchange_order.php?act=list">积分订单列表</a>
    <a href="exchange_order.php?act=delivery_list">发货单列表</a>
    <a href="exchange_goods.php?act=trash">积分商品回收站</a>
</div>

<?php if ($this->_var['full_page']): ?> 
<script language="JavaScript">

onload = function()
{
    // 开始检查订单
    startCheckOrder();
}

</script>


<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>