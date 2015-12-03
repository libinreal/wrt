<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<!-- 商品搜索 -->
<?php echo $this->fetch('mall_goods_search.htm'); ?>
<!-- 商品列表 -->
<form method="post" action="" name="listForm" onsubmit="return confirmSubmit(this)">
    <!-- start goods list -->
    <div class="list-div" id="listDiv">
        <?php endif; ?>
        <table cellpadding="3" cellspacing="1">
            <tr>
                <th>
                    <input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox" />
                    <a href="javascript:listTable.sort('goods_id'); "><?php echo $this->_var['lang']['record_id']; ?></a><?php echo $this->_var['sort_goods_id']; ?>
                </th>
                <th><a href="javascript:listTable.sort('goods_name'); "><?php echo $this->_var['lang']['goods_name']; ?></a><?php echo $this->_var['sort_goods_name']; ?></th>
                <th>销售区域</th>
                <th><a href="javascript:listTable.sort('shop_price'); "><?php echo $this->_var['lang']['shop_price']; ?></a><?php echo $this->_var['sort_shop_price']; ?></th>
                <th><a href="javascript:listTable.sort('market_price'); "><?php echo $this->_var['lang']['market_price']; ?></a><?php echo $this->_var['sort_market_price']; ?></th>
                <th><a href="javascript:listTable.sort('is_on_sale'); "><?php echo $this->_var['lang']['is_on_sale']; ?></a><?php echo $this->_var['sort_is_on_sale']; ?></th>
                <th><a href="javascript:listTable.sort('is_best'); "><?php echo $this->_var['lang']['is_best']; ?></a><?php echo $this->_var['sort_is_best']; ?></th>
                <th><a href="javascript:listTable.sort('sort_order'); "><?php echo $this->_var['lang']['sort_order']; ?></a><?php echo $this->_var['sort_sort_order']; ?></th>
                <th><a href="javascript:listTable.sort('goods_number'); "><?php echo $this->_var['lang']['goods_number']; ?></a><?php echo $this->_var['sort_goods_number']; ?></th>
            <tr>
                <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
            <tr>
                <td><input type="checkbox" name="checkboxes[]" value="<?php echo $this->_var['goods']['goods_id']; ?>" /><?php echo $this->_var['goods']['goods_id']; ?></td>
                <td class="first-cell"><?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?></td>
            <td><?php if ($this->_var['goods']['region_name'] == '中国'): ?>全部区域<?php else: ?><?php echo $this->_var['goods']['region_name']; ?><?php endif; ?></td>
               <td align="right"><?php echo $this->_var['goods']['shop_price']; ?></td>
                <td style="text-align: right;"><span><?php echo $this->_var['goods']['market_price']; ?></span></td>
                <td align="center"><img src="images/<?php if ($this->_var['goods']['is_on_sale']): ?>yes<?php else: ?>no<?php endif; ?>.gif" /></td>
                <td align="center"><img src="images/<?php if ($this->_var['goods']['is_best']): ?>yes<?php else: ?>no<?php endif; ?>.gif" onclick="listTable.toggle(this, 'toggle_best', <?php echo $this->_var['goods']['goods_id']; ?>)" /></td>
                <td align="center"><span><?php echo $this->_var['goods']['sort_order']; ?></span></td>
                <td align="right"><span><?php echo $this->_var['goods']['goods_number']; ?></span></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td class="no-records" colspan="8"><?php echo $this->_var['lang']['no_records']; ?></td></tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </table>
        <!-- end goods list -->
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

    <div>
        <input type="hidden" name="act" value="batch" />
        <select name="type" id="selAction" onchange="changeAction()">
            <option value=""><?php echo $this->_var['lang']['select_please']; ?></option>
            <option value="best"><?php echo $this->_var['lang']['best']; ?></option>
            <option value="not_best"><?php echo $this->_var['lang']['not_best']; ?></option>
        </select>
        <input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" id="btnSubmit" name="btnSubmit" class="button" disabled="true" />
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
        document.forms['listForm'].reset();
    }
    /**
     * @param: bool ext 其他条件：用于转移分类
     */
    function confirmSubmit(frm, ext)
    {
        if (frm.elements['type'].value == 'trash')
        {
            return confirm(batch_trash_confirm);
        }
        else if (frm.elements['type'].value == 'not_on_sale')
        {
            return confirm(batch_no_on_sale);
        }
        else if (frm.elements['type'].value == 'move_to')
        {
            ext = (ext == undefined) ? true : ext;
            return ext && frm.elements['target_cat'].value != 0;
        }
        else if (frm.elements['type'].value == '')
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function changeAction()
    {
        var frm = document.forms['listForm'];
        // 切换分类列表的显示
        frm.elements['target_cat'].style.display = frm.elements['type'].value == 'move_to' ? '' : 'none';
        if (!document.getElementById('btnSubmit').disabled &&confirmSubmit(frm, false))
        {
            frm.submit();
        }
    }
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>