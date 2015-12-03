<!-- $Id: bidding_info.htm 16752 2014-09-11 xy $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<div class="main-div">
<form action="bidding.php" method="post" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
<table width="100%" id="general-table">
  <tr>
    <td class="label"><?php echo $this->_var['lang']['name']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
    <td>
      <input type="text" id="bidding_name" name="name" onblur="fnStringlen();" value="<?php echo htmlspecialchars($this->_var['bidding']['name']); ?>" size="30" maxlength="40"/>
    </td>
  </tr>
  <tr>
      <td class="label"><?php echo $this->_var['lang']['type']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
      <td>
          <select name="type_id" style="width: 216px">
              <!--<option value="0"><?php echo $this->_var['lang']['all_type']; ?></option>-->
              <?php $_from = $this->_var['type_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
              <option value="<?php echo $this->_var['list']; ?>"<?php if ($this->_var['bidding']['type'] == $this->_var['list']): ?> selected<?php endif; ?>><?php echo $this->_var['lang'][$this->_var['list']]; ?></option>
              <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
          </select>
      </td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['area']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
    <td>
        <select name="area_id" style="width: 216px">
            <!--<option value="0"><?php echo $this->_var['lang']['all_area']; ?></option>-->
            <?php $_from = $this->_var['area_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
            <option value="<?php echo $this->_var['list']['region_id']; ?>"<?php if ($this->_var['bidding']['area_id'] == $this->_var['list']['region_id']): ?> selected<?php endif; ?>><?php echo $this->_var['list']['region_name']; ?></option>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </select>
    </td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['biddingAt']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
    <td>
        <input type="text" value="<?php echo $this->_var['bidding']['biddingAt']; ?>" maxlength="100" name="biddingAt" size="30"  onclick="SelectDate(this,'yyyy-MM-dd',0,-150)" readonly="true" style="cursor:pointer" /><br />
    </td>
  </tr>
    <tr>
        <td class="label"><?php echo $this->_var['lang']['amount']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
        <td>
            <input type='text' name='amount'  value="<?php echo $this->_var['bidding']['amount']; ?>" size="30"  />
        </td>
    </tr>
    <tr>
        <td class="label"><?php echo $this->_var['lang']['biddingman']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
        <td>
            <input type='text' name='biddingman'  value="<?php echo $this->_var['bidding']['biddingman']; ?>" size="30"  />
        </td>
    </tr>
    <tr>
        <td class="label"><?php echo $this->_var['lang']['prjaddress']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
        <td>
            <input type='text' name='prjaddress'  value="<?php echo $this->_var['bidding']['prjaddress']; ?>" size="30"  />
        </td>
    </tr>
    <tr>
        <td class="label"><?php echo $this->_var['lang']['prjdesc']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
        <td><textarea name="prjdesc" id="prjdesc" style="width: 400px;height: 150px;" cols="28" rows="3"><?php echo htmlspecialchars($this->_var['bidding']['prjdesc']); ?></textarea></td>
    </tr>
    <tr>
        <td class="label"><?php echo $this->_var['lang']['content']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
        <td><textarea name="content" id="Content" style="width: 400px;height: 150px;" cols="28" rows="3"><?php echo htmlspecialchars($this->_var['bidding']['content']); ?></textarea></td>
    </tr>
    <tr>
        <td class="label"><?php echo $this->_var['lang']['conditions']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
        <td><textarea name="conditions" id="conditions" style="width: 400px;height: 150px;" cols="28" rows="3"><?php echo htmlspecialchars($this->_var['bidding']['conditions']); ?></textarea></td>
    </tr>
  <tr>
    <td class="label">&nbsp;</td>
    <td>
      <input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button" />
      <input type="reset" value="<?php echo $this->_var['lang']['button_reset']; ?>" class="button" />
      <input type="hidden" name="act" value="<?php echo $this->_var['form_action']; ?>" />
      <input type="hidden" name="id" value="<?php echo $this->_var['bidding']['id']; ?>" />
    </td>
  </tr>
</table>
</form>
</div>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,validator.js')); ?>
<script src="../js/WebCalendar.js" type="text/javascript"></script>
<script language="JavaScript">
<!--


/**
 * 检查表单输入的数据
 */
function fnStringlen() {
    var doc = document.getElementById('bidding_name').value;
    var len = doc.length;
    if(len>20) {
        alert('项目名称太长');
        document.getElementById('bidding_name').value = doc.substr(0,20);
    }
}
function validate()
{
    validator = new Validator("theForm");
    validator.required("name",      name_empty);
    
    <?php if ($this->_var['bidding']['type'] != 0): ?>
        validator.isNullOption('type',no_type);
        validator.isNullOption('area_id',no_area);
    <?php endif; ?>
    
    validator.required("biddingAt",      biddingAt_empty);
    validator.required("amount",      amount_empty);
    validator.isNumber("amount",     amount_type);
    validator.required("biddingman",      biddingman_empty);
    validator.required("prjaddress",      prjaddress_empty);
    validator.required("prjdesc",      prjdesc_empty);
    validator.required("content",      content_empty);
    validator.required("conditions",      conditions_empty);
        return validator.passed();
}

onload = function()
{
    // 开始检查订单
    startCheckOrder();
}

//-->
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>