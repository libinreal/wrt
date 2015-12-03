<!-- $Id: contract_list.htm 2014-09-13 xy $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js,../js/WebCalendar.js')); ?>

<!-- 商品搜索 -->

<div class="form-div">
    <form action="javascript:searchContract()" name="searchForm" >
        <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
        <?php echo $this->_var['lang']['cusName']; ?>：<input type="text" name="cusName" value="" />
        <?php echo $this->_var['lang']['conFnum']; ?>：<input type="text" name="conFnum" value="" />
        <?php echo $this->_var['lang']['conName']; ?>：<input type="text" name="conName" value="" />
        <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" />
    </form>
</div>

<form method="POST" action="" name="listForm">
    <!-- start cat list -->
    <div class="list-div" id="listDiv">
        <?php endif; ?>
        <table cellspacing='1' cellpadding='3' id='list-table'>
            <tr>
                <th><a href="javascript:listTable.sort('id'); "><?php echo $this->_var['lang']['id']; ?></a><?php echo $this->_var['sort_id']; ?></th>
                <th><?php echo $this->_var['lang']['cusName']; ?></th>
                <th><a href="javascript:listTable.sort('conFnum'); "><?php echo $this->_var['lang']['conFnum']; ?></a><?php echo $this->_var['sort_conFnum']; ?></th>
                <th><a href="javascript:listTable.sort('conName'); "><?php echo $this->_var['lang']['conName']; ?></a><?php echo $this->_var['sort_conName']; ?></th>
                <th><a href="javascript:listTable.sort('conAmt'); "><?php echo $this->_var['lang']['conAmt']; ?></a><?php echo $this->_var['sort_conName']; ?></th>
                <th><?php echo $this->_var['lang']['handler']; ?></th>
            </tr>
            <?php $_from = $this->_var['contract_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
            <tr>
                <td align="center"><span><?php echo $this->_var['list']['id']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['user_name']; ?></span></td>
                <td align="left"><span><?php echo $this->_var['list']['conFnum']; ?></span></td>
                <td align="left"><span><?php echo $this->_var['list']['conName']; ?></span></td>
                <td align="right"><span><?php echo $this->_var['list']['conAmt']; ?></span></td>
                <td align="center">
                    <span>
                        <a href="recommend_order_info.php?act=list&conFnum=<?php echo $this->_var['list']['conFnum']; ?>" title="<?php echo $this->_var['lang']['edit']; ?>"><?php echo $this->_var['lang']['detail']; ?></a>&nbsp;
                        <a href="recommendorder.php?act=add&conFnum=<?php echo $this->_var['list']['conFnum']; ?>" title="<?php echo $this->_var['lang']['add']; ?>"><?php echo $this->_var['lang']['neworder']; ?></a>&nbsp;
                    </span>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td class="no-records" colspan="11"><?php echo $this->_var['lang']['no_contract']; ?></td></tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <tr>
                <td align="right" nowrap="true" colspan="11"><?php echo $this->fetch('page.htm'); ?></td>
            </tr>
        </table>

        <?php if ($this->_var['full_page']): ?>
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


    function searchContract()
    {
        listTable.filter['cusName'] = document.forms['searchForm'].elements['cusName'].value;
        listTable.filter['conFnum'] = document.forms['searchForm'].elements['conFnum'].value;
        listTable.filter['conName'] = document.forms['searchForm'].elements['conName'].value;
        listTable.filter.page = 1;
        listTable.loadList();
    }

</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>
