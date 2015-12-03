<!-- $Id: appointment_list.htm 2014-09-13 xy $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js,../js/WebCalendar.js')); ?>

<!-- 商品搜索 -->
<div class="form-div">
    <form action="javascript:searchAppointment()" name="searchForm" >
        <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
        <select name="type_id" >
            <option value="0"><?php echo $this->_var['lang']['all_type']; ?></option>
            <?php $_from = $this->_var['type_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['list']):
?>
            <option value="<?php echo $this->_var['key']; ?>"><?php echo $this->_var['list']; ?></option>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </select>
        <?php echo $this->_var['lang']['starttime']; ?><input type="text" maxlength="100" name="starttime" size="18"  onclick="SelectDate(this,'yyyy-MM-dd hh:mm',0,-60)" readonly="true" style="cursor:pointer" />
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
                <th><?php echo $this->_var['lang']['type']; ?></th>
                <th><?php echo $this->_var['lang']['mobilephone']; ?></th>
                <th><a href="javascript:listTable.sort('callback_time'); "><?php echo $this->_var['lang']['callbacktime']; ?></a><?php echo $this->_var['sort_callback_time']; ?></th>
                <th><?php echo $this->_var['lang']['c_name']; ?></th>
                <th><a href="javascript:listTable.sort('createAt'); "><?php echo $this->_var['lang']['createAt']; ?></a><?php echo $this->_var['sort_createAt']; ?></th>
                <th><?php echo $this->_var['lang']['appointment_handler']; ?></a></th>
                <th><?php echo $this->_var['lang']['handler']; ?></th>
            </tr>
            <?php $_from = $this->_var['appointment_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
            <tr>
                <td align="center"><span><?php echo $this->_var['list']['id']; ?></span></td>
                <td align="center"><span><?php echo htmlspecialchars($this->_var['list']['type_id']); ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['mobile_phone']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['callback_time']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['c_name']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['createAt']; ?></span></td>
                <td align="center" id="state_<?php echo $this->_var['list']['id']; ?>">
                    <span>
                        <?php if ($this->_var['list']['state'] == 0): ?>
                            <a href="javascript:void(0);" class="state_a" listid="<?php echo $this->_var['list']['id']; ?>"><?php echo $this->_var['lang']['accept_appointment']; ?></a>
                        <?php elseif ($this->_var['list']['state'] == 1): ?>
                           <?php echo $this->_var['lang']['success_appointment']; ?>
                        <?php endif; ?>
                    </span>
                </td>
                <td align="center" nowrap="true">
                    <span>
                        <a href="appointment.php?act=info&comid=<?php echo $this->_var['list']['id']; ?>" title="<?php echo $this->_var['lang']['view']; ?>"><?php echo $this->_var['lang']['check_info']; ?></a>&nbsp;
                    </span>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td class="no-records" colspan="10"><?php echo $this->_var['lang']['no_appointment']; ?></td></tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <tr>
                <td align="right" nowrap="true" colspan="12"><?php echo $this->fetch('page.htm'); ?></td>
            </tr>
        </table>

        <?php if ($this->_var['full_page']): ?>
    </div>
</form>

<div class="modal" id="zj-modal" style="display: none;">
    <div class="modal-bg">
        <div class="modal-head">
            <a class="modal-return" href="javascript:;"><</a>
            <div class="modal-title">备注</div>
            <a class="modal-close" href="javascript:;">X</a>
        </div>
        <div class="modal-content">
            <div class="modal-tip"><?php echo $this->_var['lang']['addremark']; ?></div>
            <div class="modal-form"><textarea name="remark" id="remark" cols="40" rows="5" ></textarea></div>
        </div>
        <div class="modal-footer modal-buttons">
            <a href="#" class="button btn-confirm">确认</a>
            <a href="#" class="button btn-cancel">取消</a>
        </div>
    </div>
</div>
<div id="zj-modal-bg" class="panel-bg"></div>


<!-- end cat list -->
<script type="text/javascript" language="JavaScript">
    listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
    listTable.pageCount = <?php echo $this->_var['page_count']; ?>;

    <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
    listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

$(function(){
    var listid;
    $('.state_a').click(function(){
        listid = $(this).attr('listid');
        $('#zj-modal').show();
        $('#zj-modal-bg').show();
    });
    $('.btn-confirm').click(function(){
        $('#zj-modal').hide();
        $('#zj-modal-bg').hide();
        var remark = $('#remark').val();
        update_state(listid,remark);
    });
    $('.btn-cancel, .modal-close').click(function(){
        $('#remark').val('');
        $('#zj-modal').hide();
        $('#zj-modal-bg').hide();
    });
    function update_state(id,remark){
        $.ajax({
            'type':'post',
            'dataType':'text',
            'data':{comid:id,remark:remark},
            'url':'appointment.php?act=state',
            'success':function(msg){
                msg = $.parseJSON(msg);
                if(msg.state == 1){
                    $('#state_'+id).text('<?php echo $this->_var['lang']['success_appointment']; ?>');
                    $('#remark').val('');
                }
            }
        });
    }
});

function searchAppointment()
{
    listTable.filter.starttime = Utils.trim(document.forms['searchForm'].elements['starttime'].value);
    listTable.filter.endtime = Utils.trim(document.forms['searchForm'].elements['endtime'].value);
    listTable.filter.type_id = parseInt(document.forms['searchForm'].elements['type_id'].value);
    listTable.filter.page = 1;
    listTable.loadList();
}

</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>
