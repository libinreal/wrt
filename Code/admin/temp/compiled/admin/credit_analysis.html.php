<!-- $Id: sale_list.htm 15848 2009-04-24 07:07:13Z liubo $ -->
<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<script type="text/javascript" src="../js/calendar.php?lang=<?php echo $this->_var['cfg_lang']; ?>"></script>
<link href="../js/calendar/calendar.css" rel="stylesheet" type="text/css" />
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<div class="form-div">
    <form name="TimeInterval"  action="javascript:getList()" style="margin:0px">
        <?php echo $this->_var['lang']['start_date']; ?>&nbsp;
        <input name="start_date" type="text" id="start_date" size="15" value='<?php echo $this->_var['start_date']; ?>' readonly="readonly" /><input name="selbtn1" type="button" id="selbtn1" onclick="return showCalendar('start_date', '%Y-%m-%d', false, false, 'selbtn1');" value="<?php echo $this->_var['lang']['btn_select']; ?>" class="button"/>&nbsp;&nbsp;
        <?php echo $this->_var['lang']['end_date']; ?>&nbsp;
        <input name="end_date" type="text" id="end_date" size="15" value='<?php echo $this->_var['end_date']; ?>' readonly="readonly" /><input name="selbtn2" type="button" id="selbtn2" onclick="return showCalendar('end_date', '%Y-%m-%d', false, false, 'selbtn2');" value="<?php echo $this->_var['lang']['btn_select']; ?>" class="button"/>&nbsp;&nbsp;
        <select name="credit_type" id="">
            <option value="" selected><?php echo $this->_var['lang']['credit_type']; ?></option>
            <option value="0"><?php echo $this->_var['lang']['credit']; ?></option>
            <option value="1"><?php echo $this->_var['lang']['purchase']; ?></option>
        </select>
        <input type="submit" name="submit" value="<?php echo $this->_var['lang']['query']; ?>" class="button" />
    </form>
</div>
<form method="POST" action="" name="listForm">
    <div class="list-div" id="listDiv">
        <?php endif; ?>
        <table width="100%" cellspacing="1" cellpadding="3">
            <tr>
                <th><?php echo $this->_var['lang']['customNo']; ?></th>
                <th><?php echo $this->_var['lang']['name']; ?></th>
                <th><?php echo $this->_var['lang']['type']; ?></th>
                <th><?php echo $this->_var['lang']['curamt']; ?></th>
                <th><?php echo $this->_var['lang']['amount']; ?></th>
                <th><?php echo $this->_var['lang']['createAt']; ?></th>
            </tr>
            <?php $_from = $this->_var['credit_analysis']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
            <tr>
                <td style="text-align: center;"><?php echo $this->_var['list']['customNo']; ?></td>
                <td style="text-align: center;"><?php echo $this->_var['list']['name']; ?></td>
                <td style="text-align: center;"><?php if ($this->_var['list']['type'] == 0): ?><?php echo $this->_var['lang']['credit']; ?><?php else: ?><?php echo $this->_var['lang']['purchase']; ?><?php endif; ?></td>
                <td style="text-align: left;padding-left: 10px;"><?php echo $this->_var['list']['curamt']; ?></td>
                <td align="left"><span class="active-span"><?php echo $this->_var['list']['amount']; ?></span></td>
                <td style="text-align: center;"><?php echo $this->_var['list']['createAt']; ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td class="no-records" colspan="10"><?php echo $this->_var['lang']['no_records']; ?></td></tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </table>
        <table id="page-table" cellspacing="0">
            <tr>
                <td>&nbsp;</td>
                <td align="right" nowrap="true">
                    <?php echo $this->fetch('page.htm'); ?>
                </td>
            </tr>
        </table>
        <?php if ($this->_var['full_page']): ?>
    </div>
</form>

<script type="Text/Javascript" language="JavaScript">
    listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
    listTable.pageCount = <?php echo $this->_var['page_count']; ?>;
    <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
    listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    
    <!--
            onload = function()
    {
        // 开始检查订单
        startCheckOrder();
    }

    function getList()
    {
        var frm =  document.forms['TimeInterval'];
        listTable.filter['start_date'] = frm.elements['start_date'].value;
        listTable.filter['end_date'] = frm.elements['end_date'].value;
        listTable.filter['credit_type'] = frm.elements['credit_type'].value;
        listTable.filter['page'] = 1;
        listTable.loadList();
    }
    //-->
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>