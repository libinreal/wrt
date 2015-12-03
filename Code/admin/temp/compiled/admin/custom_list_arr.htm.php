<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php endif; ?>
        
<div class="ArrList">
	<a href="custom.php?act=list">工程定制</a>
	<a href="customize.php?act=list">物资定制</a>
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