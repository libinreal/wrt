<!-- $Id: article_list.htm 16783 2009-11-09 09:59:06Z liuhui $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<div class="form-div">
    <form action="javascript:searchBidding()" name="searchForm" >
        <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
        <select name="type_id" >
            <option value="0"><?php echo $this->_var['lang']['all_type']; ?></option>
            <?php $_from = $this->_var['type_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
            <option value="<?php echo $this->_var['list']; ?>"><?php echo $this->_var['lang'][$this->_var['list']]; ?></option>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </select>
        <?php echo $this->_var['lang']['name']; ?> <input type="text" name="keyword" id="keyword" />
        <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" />
    </form>
</div>

<form method="POST" action="bidding.php?act=batch_remove" name="listForm">
    <!-- start cat list -->
    <div class="list-div" id="listDiv">
        <?php endif; ?>

        <table cellspacing='1' cellpadding='3' id='list-table'>
            <tr>
                <th><input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox">
                    <a href="javascript:listTable.sort('id'); "><?php echo $this->_var['lang']['id']; ?></a><?php echo $this->_var['sort_id']; ?></th>
                <th><?php echo $this->_var['lang']['name']; ?></th>
                <th><?php echo $this->_var['lang']['type']; ?></th>
                <th><?php echo $this->_var['lang']['area']; ?></th>
                <th><a href="javascript:listTable.sort('biddingAt'); "><?php echo $this->_var['lang']['biddingAt']; ?></a><?php echo $this->_var['sort_biddingAt']; ?></th>
                <th><a href="javascript:listTable.sort('amount'); "><?php echo $this->_var['lang']['amount']; ?></a><?php echo $this->_var['sort_amount']; ?></th>
                <th><?php echo $this->_var['lang']['biddingman']; ?></th>
                <th><?php echo $this->_var['lang']['conditions']; ?></th>
                <th><a href="javascript:listTable.sort('createAt'); "><?php echo $this->_var['lang']['createAt']; ?></a><?php echo $this->_var['sort_createAt']; ?></th>
                <th><?php echo $this->_var['lang']['handler']; ?></th>
            </tr>
            <?php $_from = $this->_var['biddings_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
            <tr>
                <td><span><input name="checkboxes[]" type="checkbox" value="<?php echo $this->_var['list']['id']; ?>" /><?php echo $this->_var['list']['id']; ?></span></td>
                <td align="left" class="first-cell">
                    <span onclick="javascript:listTable.edit(this, 'edit_name', <?php echo $this->_var['list']['id']; ?>)"><?php echo htmlspecialchars($this->_var['list']['name']); ?></span></td>
                <td align="center"><span><?php echo $this->_var['lang'][$this->_var['list']['type']]; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['area_id']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['biddingAt']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['amount']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['biddingman']; ?></span></td>
                <td align="left"><span><?php echo $this->_var['list']['conditions']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['createAt']; ?></span></td>
                <td align="center" nowrap="true"><span>
      <a href="bidding.php?act=edit&id=<?php echo $this->_var['list']['id']; ?>" title="<?php echo $this->_var['lang']['edit']; ?>"><img src="images/icon_edit.gif" border="0" height="16" width="16" /></a>&nbsp;
      <a href="javascript:;" onclick="listTable.remove(<?php echo $this->_var['list']['id']; ?>, '<?php echo $this->_var['lang']['drop_confirm']; ?>')" title="<?php echo $this->_var['lang']['remove']; ?>"><img src="images/icon_drop.gif" border="0" height="16" width="16"></a></span>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td class="no-records" colspan="10"><?php echo $this->_var['lang']['no_bidding']; ?></td></tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <tr>
                <td align="right" nowrap="true" colspan="8"><?php echo $this->fetch('page.htm'); ?></td>
            </tr>
        </table>

        <?php if ($this->_var['full_page']): ?>
    </div>

    <div>
        <input type="hidden" name="act" value="batch" />
        <select name="type" id="selAction" onchange="changeAction()">
            <option value=""><?php echo $this->_var['lang']['select_please']; ?></option>
            <option value="button_remove"><?php echo $this->_var['lang']['button_remove']; ?></option>
            <!-- <option value="button_hide"><?php echo $this->_var['lang']['button_hide']; ?></option>
             <option value="button_show"><?php echo $this->_var['lang']['button_show']; ?></option>
             <option value="move_to"><?php echo $this->_var['lang']['move_to']; ?></option>-->
        </select>
        <select name="target_cat" style="display:none" onchange="catChanged()">
            <option value="0"><?php echo $this->_var['lang']['select_please']; ?></option>
            <?php echo $this->_var['cat_select']; ?>
        </select>

        <input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" id="btnSubmit" name="btnSubmit" class="button" disabled="true" />
    </div>
</form>
<!-- end cat list -->
<script type="text/javascript" language="JavaScript">
    listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
    listTable.pageCount = <?php echo $this->_var['page_count']; ?>;

    <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
    listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    

    onload = function()
    {
        // 开始检查订单
        startCheckOrder();
    }


    /* 搜索项目 */
    function searchBidding()
    {
        listTable.filter.keyword = Utils.trim(document.forms['searchForm'].elements['keyword'].value);
        listTable.filter.type_id = Utils.trim(document.forms['searchForm'].elements['type_id'].value);
        listTable.filter.page = 1;
        listTable.loadList();
    }

    
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>
