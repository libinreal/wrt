<!-- $Id: appointment_info.htm 16752 2014-09-14 xy $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<div class="main-div">
<form  action="appointment.php" method="post" enctype="multipart/form-data" id="myform" name="theForm">
<table width="100%" id="general-table">
  <tr>
    <td class="label"><?php echo $this->_var['lang']['c_name']; ?>：</td>
    <td><?php echo $this->_var['info']['c_name']; ?></td>
  </tr>
  <tr>
      <td class="label"><?php echo $this->_var['lang']['type']; ?>：</td>
      <td><?php echo $this->_var['info']['type_id']; ?></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['mobilephone']; ?>：</td>
    <td><?php echo $this->_var['info']['mobile_phone']; ?></td>
  </tr>
    <tr>
        <td class="label"><?php echo $this->_var['lang']['callbacktime']; ?>：</td>
        <td><?php echo $this->_var['info']['callback_time']; ?></td>
    </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['createAt']; ?>：</td>
    <td><?php echo $this->_var['info']['createAt']; ?></td>
  </tr>
    <tr>
        <td class="label"><?php echo $this->_var['lang']['state']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
        <td id="state">
            <span>
                <?php if ($this->_var['info']['state'] == 0): ?>
                    <?php echo $this->_var['lang']['not_deal']; ?>&nbsp;&nbsp;&nbsp;
                    <a href="javascript:void(0);" class="state_a" listid="<?php echo $this->_var['info']['id']; ?>" style="text-decoration: underline;color: red; "><?php echo $this->_var['lang']['deal']; ?></a>
                <?php elseif ($this->_var['info']['state'] == 1): ?>
                   <?php echo $this->_var['lang']['success_appointment']; ?>
                <?php endif; ?>
            </span>
        </td>
    </tr>
    <tr class="td-remark" <?php if ($this->_var['info']['state'] == 0): ?> style='display:none'<?php endif; ?>>
        <td class="label"><?php echo $this->_var['lang']['remark']; ?>：</td>
        <td class="tdremark">
            <textarea name="remark" id="remark" cols="60" rows="5" style="border: none;text-indent: none;" readonly><?php echo htmlspecialchars($this->_var['info']['remark']); ?></textarea>
        </td>
    </tr>
</table>
    <div class="button-div" <?php if ($this->_var['info']['state'] == 0): ?> style='display:none'<?php endif; ?>>
        <input type="hidden" name="act" value="edit_remark" />
        <input type="hidden" name="old_title" value="<?php echo $this->_var['article']['title']; ?>"/>
        <input type="hidden" name="id" value="<?php echo $this->_var['info']['id']; ?>" />
        <input type="button" value="<?php echo $this->_var['lang']['m_remark']; ?>" id="m_remark" class="button" />
        <input type="submit" value="<?php echo $this->_var['lang']['b_submit']; ?>" class="button" />
        <?php if ($this->_var['info']['state'] == 1): ?><input type="reset" value="<?php echo $this->_var['lang']['button_reset']; ?>" class="button" /><?php endif; ?>
    </div>
</form>
</div>
<div class="modal" id="zj-modal" style="display: none;">
    <div class="modal-bg">
        <div class="modal-head">
            <a class="modal-return" href="javascript:;"><</a>
            <div class="modal-title">备注</div>
            <a class="modal-close" href="javascript:;">X</a>
        </div>
        <div class="modal-content">
            <div class="modal-tip"><?php echo $this->_var['lang']['addremark']; ?></div>
            <div class="modal-form"><textarea name="remark" id="remarknew" cols="40" rows="5" ></textarea></div>
        </div>
        <div class="modal-footer modal-buttons">
            <a href="#" class="button btn-confirm">确认</a>
            <a href="#" class="button btn-cancel">取消</a>
        </div>
    </div>
</div>
<div id="zj-modal-bg" class="panel-bg"></div>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,validator.js')); ?>
<script src="../js/WebCalendar.js" type="text/javascript"></script>
<script language="JavaScript">
$(function(){
    $('.state_a').click(function(){
        $('#zj-modal').show();
        $('#zj-modal-bg').show();
    });
    $('.btn-confirm').click(function(){
        $('#zj-modal').hide();
        $('#zj-modal-bg').hide();
        var remark1 = $('#remarknew').val();
        update_state('<?php echo $this->_var['info']['id']; ?>',remark1);
    });
    $('.btn-cancel, .modal-close').click(function(){
        $('#remark').val('');
        $('#zj-modal').hide();
        $('#zj-modal-bg').hide();
    });
    $('#m_remark').click(function(){
        $('#remark').attr('style','');
        $('#remark').removeAttr('readonly');
    });
    function update_state(id,remark1){
        $.ajax({
            'type':'post',
            'dataType':'text',
            'data':{comid:id,remark:remark1},
            'url':'appointment.php?act=state',
            'success':function(msg){
                msg = $.parseJSON(msg);
                if(msg.state == 1){
                    $('#state').text('<?php echo $this->_var['lang']['success_appointment']; ?>');
                    $('.td-remark').show();
                    $('#remark').val(msg.remark);
                    $('.button-div').show();
                    $('#remarknew').val('');
                }
            }
        });
    }
});





onload = function()
{
    // 开始检查订单
    startCheckOrder();
}


</script>
<?php echo $this->fetch('pagefooter.htm'); ?>