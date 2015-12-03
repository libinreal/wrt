<!-- $Id: article_list.htm 16783 2009-11-09 09:59:06Z liuhui $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<div class="form-div">
    <form action="javascript:searchBidding()" name="searchForm" >
        <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
        <select name="category" >
            <option value="0"><?php echo $this->_var['lang']['all_cat']; ?></option>
            <option value="20010000"><?php echo $this->_var['lang']['cat']['20010000']; ?></option>
            <option value="20020000"><?php echo $this->_var['lang']['cat']['20020000']; ?></option>
            <option value="10010000"><?php echo $this->_var['lang']['cat']['10010000']; ?></option>
            <option value="10020000"><?php echo $this->_var['lang']['cat']['10020000']; ?></option>
        </select>
        <?php echo $this->_var['lang']['goods_name']; ?> <input type="text" name="keyword" id="keyword" />
        <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" />
    </form>
</div>

<form method="POST" action="bidding.php?act=batch_remove" name="listForm">
    <!-- start cat list -->
    <div class="list-div" id="listDiv">
        <?php endif; ?>
        <table cellspacing='1' cellpadding='3' id='list-table'>
            <tr>
                <th>
                    <!--<input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox">-->
                    <a href="javascript:listTable.sort('id'); "><?php echo $this->_var['lang']['id']; ?></a><?php echo $this->_var['sort_id']; ?></th>
                <th><?php echo $this->_var['lang']['category']; ?></th>
                <th><?php echo $this->_var['lang']['goods_name']; ?></th>
                <th><?php echo $this->_var['lang']['brand_name']; ?></th>
                <th><a href="javascript:listTable.sort('createAt'); "><?php echo $this->_var['lang']['createAt']; ?></a><?php echo $this->_var['sort_createAt']; ?></th>
                <th><?php echo $this->_var['lang']['handler']; ?></th>
            </tr>
            <?php $_from = $this->_var['recommends_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
            <tr>
                <td align="center"><span><!--<input name="checkboxes[]" type="checkbox" value="<?php echo $this->_var['list']['id']; ?>" />--><?php echo $this->_var['list']['id']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['lang']['cat'][$this->_var['list']['cat_code']]; ?></span></td>
                <td align="left"><span><?php echo $this->_var['list']['goods_name']; ?></span></td>
                <td align="left"><span><?php echo $this->_var['list']['brand_id']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['createAt']; ?></span></td>
                <td align="center" nowrap="true"><span>
      <a href="recommend.php?act=edit&id=<?php echo $this->_var['list']['id']; ?>" title="<?php echo $this->_var['lang']['edit']; ?>"><img src="images/icon_edit.gif" border="0" height="16" width="16" /></a>&nbsp;
      <a href="javascript:;" onclick="listTable.remove(<?php echo $this->_var['list']['id']; ?>, '<?php echo $this->_var['lang']['drop_confirm']; ?>')" title="<?php echo $this->_var['lang']['remove']; ?>"><img src="images/icon_drop.gif" border="0" height="16" width="16"></a></span>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td class="no-records" colspan="10"><?php echo $this->_var['lang']['no_recommend']; ?></td></tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <tr>
                <td align="right" nowrap="true" colspan="8"><?php echo $this->fetch('page.htm'); ?></td>
            </tr>
        </table>

        <?php if ($this->_var['full_page']): ?>
    </div>

    <!--<div>
      <input type="hidden" name="act" value="batch" />
      <select name="type" id="selAction" onchange="changeAction()">
        <option value=""><?php echo $this->_var['lang']['select_please']; ?></option>
        <option value="button_remove"><?php echo $this->_var['lang']['button_remove']; ?></option>
       &lt;!&ndash; <option value="button_hide"><?php echo $this->_var['lang']['button_hide']; ?></option>
    <option value="button_show"><?php echo $this->_var['lang']['button_show']; ?></option>
    <option value="move_to"><?php echo $this->_var['lang']['move_to']; ?></option>&ndash;&gt;
  </select>
  <select name="target_cat" style="display:none" onchange="catChanged()">
    <option value="0"><?php echo $this->_var['lang']['select_please']; ?></option>
    <?php echo $this->_var['cat_select']; ?>
  </select>

  <input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" id="btnSubmit" name="btnSubmit" class="button" disabled="true" />
</div>-->
</form>
<!-- end cat list -->
<script type="text/javascript" language="JavaScript">
    /*listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
     listTable.pageCount = <?php echo $this->_var['page_count']; ?>;

     <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
     listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
     <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>*/
    

    onload = function()
    {
        // 开始检查订单
        startCheckOrder();
    }


    /* 搜索项目 */
    function searchBidding()
    {
        listTable.filter.keyword = Utils.trim(document.forms['searchForm'].elements['keyword'].value);
        listTable.filter.category = Utils.trim(document.forms['searchForm'].elements['category'].value);
        listTable.filter.page = 1;
        listTable.loadList();
    }

    
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>
