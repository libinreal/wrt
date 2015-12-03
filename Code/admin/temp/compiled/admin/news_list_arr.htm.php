<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php endif; ?>
        
<div class="ArrList">
    <a href="helpcentercat.php?act=list">帮助中心分类</a>
    <a href="noticecat.php?act=list">商城公告分类</a>
    <a href="wrnewscat.php?act=list">物融新闻分类</a>
    <a href="helpcenter.php?act=list">帮助中心列表</a>
    <a href="notice.php?act=list">商城公告列表</a>
    <a href="wrnews.php?act=list">物融新闻列表</a>
    <a href="ads.php?act=list">广告列表</a>
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