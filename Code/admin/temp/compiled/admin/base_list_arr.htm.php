<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php endif; ?>
        
<div class="ArrList">
	<a href="category.php?act=list">物料类别</a>
	<a href="goods_type.php?act=manage">物料类型</a>
	<a href="brand.php?act=list">厂商列表</a>
	<a href="suppliers.php?act=list">供货商列表</a>
    <a href="area_manage.php?act=list">地区列表</a>
    <a href="shop_config.php?act=list_edit">积分设置</a>
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