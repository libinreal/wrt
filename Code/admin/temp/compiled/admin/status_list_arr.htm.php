<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php endif; ?>
        
<div class="ArrList">
	<a href="flow_stats.php?act=view">流量分析</a>
	<a href="guest_stats.php?act=list">客户分析</a>
	<a href="order_stats.php?act=list">订单统计</a>
	<a href="sale_general.php?act=list">销售概况</a>
    <a href="sale_list.php?act=list">销售明细</a>
    <a href="credit_analysis.php?act=list">信用分析</a>
    <a href="sales_analysis.php?act=list">销售分析</a>
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