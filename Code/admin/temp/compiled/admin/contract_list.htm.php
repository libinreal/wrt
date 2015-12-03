<!-- $Id: contract_list.htm 2014-09-13 xy $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js,../js/WebCalendar.js')); ?>

<!-- 商品搜索 -->
<div class="form-div">
    <form action="javascript:searchContract()" name="searchForm" >
        <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
        <select name="conState" >
            <option value="0"><?php echo $this->_var['lang']['all_state']; ?></option>
            <option value="1"><?php echo $this->_var['lang']['state_1']; ?></option>
            <option value="2"><?php echo $this->_var['lang']['state_2']; ?></option>
            <option value="3"><?php echo $this->_var['lang']['state_3']; ?></option>
        </select>
        <?php echo $this->_var['lang']['cusName']; ?>：<input type="text" name="keyword" id="keyword" />&nbsp;
        <?php echo $this->_var['lang']['cusFnum']; ?>：<input type="text" name="cusFnum" id="cusFnum" />
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
                <th><?php echo $this->_var['lang']['cusFnum']; ?></th>
                <th><?php echo $this->_var['lang']['conFnum']; ?></th>
                <th><?php echo $this->_var['lang']['conName']; ?></th>
                <th><?php echo $this->_var['lang']['conAmt']; ?></th>
                <th><?php echo $this->_var['lang']['conState']; ?></th>
                <th><?php echo $this->_var['lang']['conNo']; ?></th>
                <th><?php echo $this->_var['lang']['Remark']; ?></th>
                <th><?php echo $this->_var['lang']['bankName']; ?></th>
                <th><?php echo $this->_var['lang']['matGroupName']; ?></th>
                <th><a href="javascript:listTable.sort('strDate'); "><?php echo $this->_var['lang']['strDate']; ?></a><?php echo $this->_var['sort_strDate']; ?></th>
                <th><a href="javascript:listTable.sort('endDate'); "><?php echo $this->_var['lang']['endDate']; ?></a><?php echo $this->_var['sort_endDate']; ?></th>
            </tr>
            <?php $_from = $this->_var['contract_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
            <tr>
                <td style="text-align: center;"><?php echo $this->_var['list']['id']; ?></td>
                <td style="text-align: left;"><?php echo $this->_var['list']['user_name']; ?></td>
                <td style="text-align: left;"><?php echo $this->_var['list']['cusFnum']; ?></td>
                <td style="text-align: left;"><span><?php echo $this->_var['list']['conFnum']; ?></span></td>
                <td style="text-align: left;"><span><?php echo $this->_var['list']['conName']; ?></span></td>
                <td style="text-align: right;"><span><?php echo $this->_var['list']['conAmt']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['conState']; ?></span></td>
                <td style="text-align: left;"><span><?php echo $this->_var['list']['conNo']; ?></span></td>
                <td style="text-align: left;"><span><?php echo $this->_var['list']['Remark']; ?></span></td>
                <td style="text-align: left;">
                    <?php $_from = $this->_var['list']['Banks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'Banks');$this->_foreach['no'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['no']['total'] > 0):
    foreach ($_from AS $this->_var['Banks']):
        $this->_foreach['no']['iteration']++;
?>
                    <span><?php echo $this->_var['Banks']['bankName']; ?> <?php if (($this->_foreach['no']['iteration'] == $this->_foreach['no']['total']) != "1"): ?> ,<?php endif; ?></span>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </td>
                <td style="text-align: left;">
                    <?php $_from = $this->_var['list']['Mats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'Mats');$this->_foreach['no'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['no']['total'] > 0):
    foreach ($_from AS $this->_var['Mats']):
        $this->_foreach['no']['iteration']++;
?>
                    <span><?php echo $this->_var['Mats']['matGroupName']; ?><?php if (($this->_foreach['no']['iteration'] == $this->_foreach['no']['total']) != "1"): ?> ,<?php endif; ?></span>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </td>
                <td align="center"><span><?php echo $this->_var['list']['strDate']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['endDate']; ?></span></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td class="no-records" colspan="13"><?php echo $this->_var['lang']['no_contract']; ?></td></tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <tr>
                <td align="right" nowrap="true" colspan="13"><?php echo $this->fetch('page.htm'); ?></td>
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
    listTable.filter.keyword = Utils.trim(document.forms['searchForm'].elements['keyword'].value);
    listTable.filter.cusFnum = Utils.trim(document.forms['searchForm'].elements['cusFnum'].value);
    listTable.filter.conState = parseInt(document.forms['searchForm'].elements['conState'].value);
    listTable.filter.page = 1;
    listTable.loadList();
}

</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>
