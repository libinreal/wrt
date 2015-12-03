<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php endif; ?>
        
<div class="ArrList">
	<a href="order.php?act=list">订单列表</a>
	<a href="order.php?act=order_query">订单查询</a>
	<a href="recommendorder.php?act=list">推荐订单</a>
	<a href="order.php?act=reminder">订单催单</a>
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