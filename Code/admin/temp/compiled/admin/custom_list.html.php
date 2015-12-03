<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<div class="form-div">
    <form action="javascript:searchCustom()" name="searchForm">
        <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
        <?php echo $this->_var['lang']['proName']; ?> <input type="text" name="keyword" size="15" />
        <?php echo $this->_var['lang']['contacts']; ?> <input type="text" name="contacts" id="" />
        <?php echo $this->_var['lang']['telephone']; ?> <input type="text" name="telephone" id="" />
        <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" />
    </form>
</div>
<?php endif; ?>
<div class="list-div" id="listDiv">
    <table cellspacing='1' cellpadding='3'>
        <tr>
            <th><a href="javascript:listTable.sort('proId'); "><?php echo $this->_var['lang']['proId']; ?></a><?php echo $this->_var['sort_proId']; ?></th>
            <th><?php echo $this->_var['lang']['proName']; ?></th>
            <th><?php echo $this->_var['lang']['proTime']; ?></th>
            <th><?php echo $this->_var['lang']['proMoney']; ?></th>
            <th><?php echo $this->_var['lang']['areaId']; ?></th>
            <th><?php echo $this->_var['lang']['contacts']; ?></th>
            <th><?php echo $this->_var['lang']['position']; ?></th>
            <th><?php echo $this->_var['lang']['telephone']; ?></th>
            <th><?php echo $this->_var['lang']['company']; ?></th>
            <th><?php echo $this->_var['lang']['cusStatus']; ?></th>
            <th><?php echo $this->_var['lang']['createAt']; ?></th>
            <th><?php echo $this->_var['lang']['handler']; ?></th>
        </tr>
        <?php $_from = $this->_var['custom']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');$this->_foreach['no'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['no']['total'] > 0):
    foreach ($_from AS $this->_var['list']):
        $this->_foreach['no']['iteration']++;
?>
        <tr>
            <td style="text-align: center;"><?php echo $this->_var['list']['proId']; ?></td>
            <td><?php echo $this->_var['list']['proName']; ?></td>
            <td style="text-align: center;"><?php echo $this->_var['list']['proTime']; ?></td>
            <td style="text-align: right;"><?php echo $this->_var['list']['proMoney']; ?></td>
            <td style="text-align: center;"><?php echo $this->_var['list']['region_name']; ?></td>
            <td style="text-align: center;"><?php echo $this->_var['list']['contacts']; ?></td>
            <td style="text-align: center;"><?php echo $this->_var['list']['position']; ?></td>
            <td style="text-align: center;"><?php echo $this->_var['list']['telephone']; ?></td>
            <td><?php echo $this->_var['list']['company']; ?></td>
            <td style="text-align: center;" id="status_<?php echo $this->_var['list']['proId']; ?>">
                <!--<?php if ($this->_var['list']['cusStatus'] != 2): ?>-->
                <a class="cusStatus"  data-proId="<?php echo $this->_var['list']['proId']; ?>" href="javascript:void(0);"><?php echo $this->_var['lang']['cus'][$this->_var['list']['cusStatus']]; ?></a>
                <!--<?php else: ?>-->
                <?php echo $this->_var['lang']['cus'][$this->_var['list']['cusStatus']]; ?>
                <!--<?php endif; ?>-->
            </td>
            <td style="text-align: center;"><?php echo $this->_var['list']['createAt']; ?></td>
            <td style="text-align: center;"><a href="custom.php?act=info&id=<?php echo $this->_var['list']['proId']; ?>">查看详情</a></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td class="no-records" colspan="12"><?php echo $this->_var['lang']['no_custom']; ?></td></tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
        <tr>
            <td align="right" nowrap="true" colspan="12"><?php echo $this->fetch('page.htm'); ?></td>
        </tr>
    </table>
</div>

<script language="JavaScript">
    listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
    listTable.pageCount = <?php echo $this->_var['page_count']; ?>;
    <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
    listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    
    $(function(){
        $('.cusStatus').click(function(){
            var proId = $(this).attr('data-proId');
            $.ajax({
                'type':'post',
                'dataType':'text',
                'url':'custom.php?act=status&id='+proId,
                'success':function(msg){
                    msg = parseInt(msg)
                    if (msg == 1) {
                        $("#status_"+proId+" a").text("<?php echo $this->_var['lang']['cus']['1']; ?>");
                    } else if(msg == 2) {
                        $("#status_"+proId+"").text("<?php echo $this->_var['lang']['cus']['2']; ?>");
                    }
                }
            });
        });
    });
    function searchCustom()
    {
        listTable.filter['contacts'] = document.forms['searchForm'].elements['contacts'].value;
        listTable.filter['telephone'] = document.forms['searchForm'].elements['telephone'].value;
        listTable.filter['keyword'] = Utils.trim(document.forms['searchForm'].elements['keyword'].value);
        listTable.filter['page'] = 1;
        listTable.loadList();
    }
    
</script>
<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>