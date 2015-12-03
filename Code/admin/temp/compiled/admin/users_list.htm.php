<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<div class="form-div">
    <form action="javascript:searchUser()" name="searchForm">
        <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
        &nbsp;<?php echo $this->_var['lang']['label_user_name']; ?> &nbsp;<input type="text" name="keyword" />
        &nbsp;<?php echo $this->_var['lang']['label_parent_name']; ?> &nbsp;<input type="text" name="parnetName" />
        &nbsp;<?php echo $this->_var['lang']['label_company']; ?> &nbsp;<input type="text" name="company" />
        <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" />
    </form>
</div>

<form method="POST" action="" name="listForm" onsubmit="return confirm_bath()">
    <!-- start users list -->
    <div class="list-div" id="listDiv">
        <?php endif; ?>
        <!--用户列表部分-->
        <table cellpadding="3" cellspacing="1">
            <tr>
                <th>
                    <input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox">
                    <a href="javascript:listTable.sort('user_id'); "><?php echo $this->_var['lang']['record_id']; ?></a><?php echo $this->_var['sort_user_id']; ?>
                </th>
                <th><a href="javascript:listTable.sort('user_name'); "><?php echo $this->_var['lang']['username']; ?></a><?php echo $this->_var['sort_user_name']; ?></th>
                <th><?php echo $this->_var['lang']['customNo']; ?></th>
                <th>直属关系</th>
                <th><a href="javascript:listTable.sort('customLevel'); "><?php echo $this->_var['lang']['customLevel']; ?></a><?php echo $this->_var['sort_customLevel']; ?></th>
                <th><?php echo $this->_var['lang']['user_privilege']; ?></th>
                <!--
                <th><a href="javascript:listTable.sort('email'); "><?php echo $this->_var['lang']['email']; ?></a><?php echo $this->_var['sort_email']; ?></th>
                -->
                <th><a href="javascript:listTable.sort('contacts'); "><?php echo $this->_var['lang']['contacts']; ?></a><?php echo $this->_var['sort_contacts']; ?></th>
                <th><?php echo $this->_var['lang']['position']; ?></th>
                <th><a href="javascript:listTable.sort('contactsPhone'); "><?php echo $this->_var['lang']['contactsPhone']; ?></a><?php echo $this->_var['sort_contactsPhone']; ?></th>
                <th><?php echo $this->_var['lang']['companyName']; ?></th>
                <th><?php echo $this->_var['lang']['department']; ?></th>
                <th><?php echo $this->_var['lang']['credit_rank']; ?></th>
                <th><a href="javascript:listTable.sort('reg_time'); "><?php echo $this->_var['lang']['reg_date']; ?></a><?php echo $this->_var['sort_reg_time']; ?></th>
                <th><?php echo $this->_var['lang']['handler']; ?></th>
            <tr>
                <?php $_from = $this->_var['user_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'user');if (count($_from)):
    foreach ($_from AS $this->_var['user']):
?>
            <tr>
                <td><input type="checkbox" name="checkboxes[]" value="<?php echo $this->_var['user']['user_id']; ?>" notice="<?php if ($this->_var['user']['user_money'] != 0): ?>1<?php else: ?>0<?php endif; ?>"/><?php echo $this->_var['user']['user_id']; ?></td>
                <td class="first-cell"><?php echo htmlspecialchars($this->_var['user']['user_name']); ?></td>
                <td><?php if ($this->_var['user']['customNo']): ?><?php echo $this->_var['user']['customNo']; ?><?php else: ?>--<?php endif; ?></td>
                <td><?php echo $this->_var['user']['SubordinateLevel']; ?></td>
                <td>
                    <?php if ($this->_var['user']['customLevel'] == 0): ?>注册会员<?php endif; ?>
                    <?php if ($this->_var['user']['customLevel'] == 1): ?>VIP会员<?php endif; ?>
                    <?php if ($this->_var['user']['customLevel'] == 2): ?>VIP下单会员<?php endif; ?>
<!--
                    <?php $_from = $this->_var['user']['msn']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['list']):
?>
                        <?php if ($this->_var['list']['key'] == 1): ?> <?php echo $this->_var['lang']['privilege']['1']; ?><?php endif; ?>
                        <?php if ($this->_var['list']['key'] == 2): ?> <?php echo $this->_var['lang']['privilege']['2']; ?><?php endif; ?>
                        <?php if ($this->_var['list']['key'] == 3): ?> <?php echo $this->_var['lang']['privilege']['3']; ?><?php endif; ?>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    -->
                </td>
                <td>
                    <?php $_from = $this->_var['user']['msn']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['list']):
?>
                    <?php if ($this->_var['list']['key'] == 1): ?> <?php echo $this->_var['lang']['privilege']['1']; ?><?php endif; ?>
                    <?php if ($this->_var['list']['key'] == 2): ?> ,<?php echo $this->_var['lang']['privilege']['2']; ?><?php endif; ?>
                    <?php if ($this->_var['list']['key'] == 3): ?> ,<?php echo $this->_var['lang']['privilege']['3']; ?><?php endif; ?>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </td>
                <!--
                <td><?php echo $this->_var['user']['email']; ?></td>
                -->
                <td><?php echo $this->_var['user']['contacts']; ?></td>
                <td><?php echo $this->_var['user']['position']; ?></td>
                <td><?php echo $this->_var['user']['contactsPhone']; ?></td>
                <td><?php echo $this->_var['user']['companyName']; ?></td>
                <td><?php echo $this->_var['user']['department']; ?></td>
                <td><?php echo $this->_var['user']['credit_rank']; ?></td>
                <td align="center"><?php echo $this->_var['user']['reg_time']; ?></td>
                <td align="center">&nbsp;
                    <a href="users.php?act=edit&id=<?php echo $this->_var['user']['user_id']; ?>" title="<?php echo $this->_var['lang']['edit']; ?>"><img src="images/icon_edit.gif" border="0" height="16" width="16" /></a>
                    <!--
                    <a href="users.php?act=address_list&id=<?php echo $this->_var['user']['user_id']; ?>" title="<?php echo $this->_var['lang']['address_list']; ?>"><img src="images/book_open.gif" border="0" height="16" width="16" /></a>
                    <a href="order.php?act=list&user_id=<?php echo $this->_var['user']['user_id']; ?>" title="<?php echo $this->_var['lang']['view_order']; ?>"><img src="images/icon_view.gif" border="0" height="16" width="16" /></a>
                    <a href="account_log.php?act=list&user_id=<?php echo $this->_var['user']['user_id']; ?>" title="<?php echo $this->_var['lang']['view_deposit']; ?>"><img src="images/icon_account.gif" border="0" height="16" width="16" /></a>
                    -->
                    <a href="javascript:confirm_redirect('<?php if ($this->_var['user']['user_money'] != 0): ?><?php echo $this->_var['lang']['still_accounts']; ?><?php endif; ?><?php echo $this->_var['lang']['remove_confirm']; ?>', 'users.php?act=remove&id=<?php echo $this->_var['user']['user_id']; ?>')" title="<?php echo $this->_var['lang']['remove']; ?>">
                        <img src="images/icon_drop.gif" border="0" height="16" width="16" />
                    </a>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td class="no-records" colspan="11"><?php echo $this->_var['lang']['no_records']; ?></td></tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <tr>
                <td colspan="2">
                    <input type="hidden" name="act" value="batch_remove" />
                    <input type="submit" id="btnSubmit" value="<?php echo $this->_var['lang']['button_remove']; ?>" disabled="true" class="button" /></td>
                <td align="right" nowrap="true" colspan="12">
                    <?php echo $this->fetch('page.htm'); ?>
                </td>
            </tr>
        </table>

        <?php if ($this->_var['full_page']): ?>
    </div>
    <!-- end users list -->
</form>
<script type="text/javascript" language="JavaScript">
    <!--
    listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
    listTable.pageCount = <?php echo $this->_var['page_count']; ?>;

    <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
    listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

    
    onload = function()
    {
        document.forms['searchForm'].elements['keyword'].focus();
        // 开始检查订单
        startCheckOrder();
    }

    /**
     * 搜索用户
     */
    function searchUser()
    {
        listTable.filter['keywords'] = Utils.trim(document.forms['searchForm'].elements['keyword'].value);
        listTable.filter['parnetName'] = Utils.trim(document.forms['searchForm'].elements['parnetName'].value);
        listTable.filter['company'] = Utils.trim(document.forms['searchForm'].elements['company'].value);
        listTable.filter['page'] = 1;
        listTable.loadList();
    }

    function confirm_bath()
    {
        userItems = document.getElementsByName('checkboxes[]');

        cfm = '<?php echo $this->_var['lang']['list_remove_confirm']; ?>';

        for (i=0; userItems[i]; i++)
        {
            if (userItems[i].checked && userItems[i].notice == 1)
            {
                cfm = '<?php echo $this->_var['lang']['list_still_accounts']; ?>' + '<?php echo $this->_var['lang']['list_remove_confirm']; ?>';
                break;
            }
        }

        return confirm(cfm);
    }
    //-->
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>