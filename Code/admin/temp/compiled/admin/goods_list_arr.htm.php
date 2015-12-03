<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php endif; ?>
        
<div class="ArrList">
	<a href="goods.php?act=list">商品列表</a>
	<a href="goods.php?act=add">商品添加</a>
    <a href="comment_manage.php?act=list">用户评论</a>
    <a href="goods.php?act=trash">商品回收站</a>
    <a href="mall_goods.php?act=list">推荐商品</a>
    <a href="brand_recommend.php?act=list">推荐品牌</a>
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