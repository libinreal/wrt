<!-- $Id: recovery_history_list.htm 2014-09-13 xy $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js,../js/WebCalendar.js')); ?>

<!-- 商品搜索 -->
<div class="form-div">
    <form action="javascript:searchRecovery_history()" name="searchForm" >
        <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
        <select name="chanKind" >
            <option value="0"><?php echo $this->_var['lang']['koujian']; ?></option>
            <option value="1"><?php echo $this->_var['lang']['huifu']; ?></option>
        </select>
        <?php echo $this->_var['lang']['cusName']; ?>：<input type="text" name="keyword" id="keyword" />
        <?php echo $this->_var['lang']['chanDate']; ?>：<?php echo $this->_var['lang']['starttime']; ?><input type="text" maxlength="100" name="starttime" size="18"  onclick="SelectDate(this,'yyyy-MM-dd hh:mm',0,-60)" readonly="true" style="cursor:pointer" />
        <?php echo $this->_var['lang']['endtime']; ?><input type="text" maxlength="100" name="endtime" size="18"  onclick="SelectDate(this,'yyyy-MM-dd hh:mm',0,-60)" readonly="true" style="cursor:pointer" />
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
                <th><?php echo $this->_var['lang']['chanFnum']; ?></th>
                <th><?php echo $this->_var['lang']['chanKind']; ?></th>
                <th><?php echo $this->_var['lang']['billNo']; ?></th>
                <th><?php echo $this->_var['lang']['billFnum']; ?></th>
                <th><a href="javascript:listTable.sort('chanDate'); "><?php echo $this->_var['lang']['chanDate']; ?></a><?php echo $this->_var['sort_chanDate']; ?></th>
                <th><?php echo $this->_var['lang']['Company']; ?></th>
                <th><?php echo $this->_var['lang']['chanAmt']; ?></th>
                <th><?php echo $this->_var['lang']['bankFnum']; ?></th>
                <th><?php echo $this->_var['lang']['remark']; ?></th>
            </tr>
            <?php $_from = $this->_var['recovery_history_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
            <tr>
                <td align="center"><span><?php echo $this->_var['list']['id']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['user_name']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['chanFnum']; ?></span></td>
                <td align="center"><span><?php echo htmlspecialchars($this->_var['list']['chanKind']); ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['billNO']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['billFnum']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['chanDate']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['companyName']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['chanAmt']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['bankFnum']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['remark']; ?></span></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td class="no-records" colspan="11"><?php echo $this->_var['lang']['no_recovery_history']; ?></td></tr>
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


function searchRecovery_history()
{
    listTable.filter.keyword = Utils.trim(document.forms['searchForm'].elements['keyword'].value);
    listTable.filter.chanKind = parseInt(document.forms['searchForm'].elements['chanKind'].value);
    listTable.filter.starttime = Utils.trim(document.forms['searchForm'].elements['starttime'].value);
    listTable.filter.endtime = Utils.trim(document.forms['searchForm'].elements['endtime'].value);
    listTable.filter.page = 1;
    listTable.loadList();
}

</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>
