<!-- $Id: goods_list.htm 17126 2010-04-23 10:30:26Z liuhui $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<div class="form-div">
    <form action="javascript:search_brand()" name="searchForm">
        <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
        <select name="region" id="region">
            <option value=""><?php echo $this->_var['lang']['brand_select_region']; ?></option>
            <?php $_from = $this->_var['region']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'area');if (count($_from)):
    foreach ($_from AS $this->_var['area']):
?>
            <option value="<?php echo $this->_var['area']['region_id']; ?>" <?php if ($this->_var['brands']['area_id'] == $this->_var['area']['region_id']): ?>selected<?php endif; ?>><?php echo $this->_var['area']['region_name']; ?></option>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </select>

        <select name="category" id="category" onchange="get_category();">
            <option value="" level=""><?php echo $this->_var['lang']['brand_select_category']; ?></option>
            <?php echo $this->_var['category']; ?>
        </select>
        <select name="brand_id" id="brand">
            <option value=""><?php echo $this->_var['lang']['brand_select']; ?></option>
            <?php $_from = $this->_var['brand_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'brand');if (count($_from)):
    foreach ($_from AS $this->_var['brand']):
?>
            <option value="<?php echo $this->_var['brand']['brand_id']; ?>"<?php if ($this->_var['brands']['brand_id'] == $this->_var['brand']['brand_id']): ?>selected<?php endif; ?>><?php echo $this->_var['brand']['brand_name']; ?></option>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </select>

        <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" />
    </form>
</div>
<!-- 推荐商品列表 -->
<form method="post" action="brand_recommend.php?act=batch_remove" name="listForm" onsubmit="return confirmSubmit(this)">
    <div class="list-div" id="listDiv">
        <?php endif; ?>
        <table cellpadding="3" cellspacing="1">
            <tr>
                <th><?php echo $this->_var['lang']['brand_rid']; ?></th>
                <th><?php echo $this->_var['lang']['area_id']; ?></th>
                <th><?php echo $this->_var['lang']['cat_name']; ?></th>
                <th><?php echo $this->_var['lang']['brand_id']; ?></th>
                <th><?php echo $this->_var['lang']['sort_order']; ?></th>
                <th><?php echo $this->_var['lang']['handler']; ?></th>
            <tr>
            <?php $_from = $this->_var['brand_recommend_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'brand');if (count($_from)):
    foreach ($_from AS $this->_var['brand']):
?>
            <tr>
                <td style="text-align: center"><?php echo $this->_var['brand']['brand_rid']; ?></td>
                <td><?php echo $this->_var['brand']['region_name']; ?></td>
                <td><?php echo $this->_var['brand']['cat_name']; ?></td>
                <td><?php echo $this->_var['brand']['brand_name']; ?></td>
                <td style="text-align: center"><?php echo $this->_var['brand']['sort_order']; ?></td>
                <td style="text-align: center">
                    <a href="brand_recommend.php?act=edit&id=<?php echo $this->_var['brand']['brand_rid']; ?>" title="<?php echo $this->_var['lang']['edit']; ?>"><img src="images/icon_edit.gif" border="0" height="16" width="16" /></a>&nbsp;
                    <a href="javascript:;" onclick="listTable.remove(<?php echo $this->_var['brand']['brand_rid']; ?>, '<?php echo $this->_var['lang']['drop_confirm']; ?>')" title="<?php echo $this->_var['lang']['edit']; ?>"><img src="images/icon_trash.gif" width="16" height="16" border="0" /></a>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td class="no-records" colspan="10"><?php echo $this->_var['lang']['no_records']; ?></td></tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </table>
        <!-- 分页 -->
        <table id="page-table" cellspacing="0">
            <tr>
                <td align="right" nowrap="true">
                    <?php echo $this->fetch('page.htm'); ?>
                </td>
            </tr>
        </table>

        <?php if ($this->_var['full_page']): ?>
    </div>

</form>

<script type="text/javascript">
    listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
    listTable.pageCount = <?php echo $this->_var['page_count']; ?>;
    <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
    listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

    
    onload = function()
    {
        startCheckOrder(); // 开始检查订单
    }
    function get_category() {
        var obj = document.getElementById('category');
        var index=obj.selectedIndex;
        var level=obj.options[index].getAttribute("level");
        //if(level==4) {
        //    return true;
        //}else {
        //    obj.options[0].selected='selected';
        //    alert('不是最底层分类不可进行搜素');
        //   return false;
        //}
    }
    function search_brand()
    {
        listTable.filter['region'] = Utils.trim(document.forms['searchForm'].elements['region'].value);
        listTable.filter['category'] = Utils.trim(document.forms['searchForm'].elements['category'].value);
        listTable.filter['brand_id'] = Utils.trim(document.forms['searchForm'].elements['brand_id'].value);
        listTable.filter['page'] = 1;

        listTable.loadList();
    }
    
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>