<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php endif; ?>
        
<div class="ArrList">
	<a href="evaluation.php?act=list">信用测评额度</a>
	<a href="credit_quota_add.php?act=list">信用额度追加</a>
	<a href="purchase_quota_add.php?act=list">采购额度追加</a>
	<a href="credit_intrinfo.php?act=edit&id=1">信用池介绍</a>
    <a href="recovery_history.php?act=list">信用恢复历史</a>
    <a href="bill_notice.php?act=list">票据到期提醒</a>
    <a href="contract.php?act=list">合同数据</a>
    <a href="credit_class.php?act=edit&id=2">信用等级介绍</a>
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