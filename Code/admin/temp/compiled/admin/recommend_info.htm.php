<!-- $Id: recommend_info.htm 16752 2014-09-11 xy $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<div class="main-div">
<form action="recommend.php" method="post" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
<table width="100%" id="general-table">
    <tr>
        <td class="label"><?php echo $this->_var['lang']['category']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
        <td>
            <select id="category" name="category" style="width: 216px" >
                <option value=""><?php echo $this->_var['lang']['all_cat']; ?></option>
                <option value="20010000" <?php if ($this->_var['recommend']['cat_code'] == "20010000"): ?> selected<?php endif; ?>><?php echo $this->_var['lang']['cat']['20010000']; ?></option>
                <option value="20020000" <?php if ($this->_var['recommend']['cat_code'] == "20020000"): ?> selected<?php endif; ?>><?php echo $this->_var['lang']['cat']['20020000']; ?></option>
                <option value="10010000" <?php if ($this->_var['recommend']['cat_code'] == "10010000"): ?> selected<?php endif; ?>><?php echo $this->_var['lang']['cat']['10010000']; ?></option>
                <option value="10020000" <?php if ($this->_var['recommend']['cat_code'] == "10020000"): ?> selected<?php endif; ?>><?php echo $this->_var['lang']['cat']['10020000']; ?></option>
            </select>
        </td>
    </tr>
  <tr>
      <td class="label"><?php echo $this->_var['lang']['goods_name']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
      <td>
          <select id="goods_id" name="goods_id" style="width: 216px">
              <option gname="" wcode="" value=""><?php echo $this->_var['lang']['goods_name']; ?></option>
              <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
                <option gname="<?php echo $this->_var['list']['goods_name']; ?>" wcode="<?php echo $this->_var['list']['wcode']; ?>" value="<?php echo $this->_var['list']['goods_id']; ?>" <?php if ($this->_var['recommend']['goods_id'] == $this->_var['list']['goods_id']): ?> selected<?php endif; ?>><?php echo $this->_var['list']['goods_name']; ?></option>
              <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
          </select>
          <input type="hidden" id="gname" name="gname" value="<?php echo $this->_var['gname']; ?>" />
          <input type="hidden" id="wcode" name="wcode" value="<?php echo $this->_var['wcode']; ?>" />
      </td>
  </tr>
  <tr>
    <td class="label"><span id="brand_td" style="display: <?php if ($this->_var['form_action'] == 'insert'): ?>none<?php else: ?>block<?php endif; ?>"><?php echo $this->_var['lang']['brand_name']; ?><?php echo $this->_var['lang']['require_field']; ?></span></td>
    <td>
        <div id="r_brand" class="r_brand">
            <?php $_from = $this->_var['brands_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
                <label><input type="checkbox" name="brand_name[]" value="<?php echo $this->_var['list']['brand_id']; ?>" /><?php echo $this->_var['list']['brand_name']; ?></label>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </div>
    </td>
  </tr>
  <tr>
    <td class="label">&nbsp;</td>
    <td>
      <input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button" />
      <input type="reset" value="<?php echo $this->_var['lang']['button_reset']; ?>" class="button" />
      <input type="hidden" name="act" value="<?php echo $this->_var['form_action']; ?>" />
      <input type="hidden" name="id" value="<?php echo $this->_var['recommend']['id']; ?>" />
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
function validate()
{
    var docCategory = document.getElementById('category');
    var catIndex = docCategory.options.selectedIndex;
    var catObj = docCategory.options[catIndex].value;
    if(catObj=='') {
        alert(no_category);
        return false;
    }
    var goodsId = document.getElementById('goods_id');
    var goodsIndex = goodsId.options.selectedIndex;
    var goodsObj = goodsId.options[goodsIndex].value;
    if(goodsObj == ""){
        alert(no_goods_name);
        return false;
    }else{

    }
    var brandName = document.getElementById('r_brand').getElementsByTagName('input');
    var len = brandName.length;
    var i= 0,count=false;
    for(i;i<len;i++) {
        if(document.getElementById('r_brand').getElementsByTagName('input')[i].checked){
            count = true;
            break;
        }
    }
    if(!count){
        alert(no_brand_name);
        return false;
    }
    return true;
}
function matchbrand(){
    var brandlist = <?php echo empty($this->_var['recommend']['brand_id']) ? '""' : $this->_var['recommend']['brand_id']; ?>;
    if(brandlist){
        var len = brandlist.length;
        var brandName = document.getElementById('r_brand').getElementsByTagName('input');
        var leninput = brandName.length;
        for(var i=0;i<len;i++){
            for(var j=0;j<leninput;j++){
                if(brandName[j].value == brandlist[i]){
                    brandName[j].checked = "checked";
                }
            }
        }
    }
}


$(function(){
    matchbrand();
    $('#category').change(function(){
        $.ajax({
            'type':'get',
            'dataType':'text',
            'url':'recommend.php?act=get_goods&category=' + $(this).val(),
            'success':function(data){
                data = $.parseJSON(data);
                $('#goods_id').empty();
                $('#brand_td').attr('style','display:none');
                $('#r_brand').html('');
                $('#goods_id').append('<option value="" gname="" wcode=""><?php echo $this->_var['lang']['goods_name']; ?></option>');
                $(data).each(function(i,val){
                    $('#goods_id').append("<option wcode=" + val.wcode + " gname=" + val.goods_name + " value=" + val.goods_id + ">" + val.goods_name + "</option>");
                });

            }
        });
    });
    $('#goods_id').change(function(){
        var id,name;
        var goodsId = document.getElementById('goods_id');
        var goodsIndex = goodsId.options.selectedIndex;
        var gname = goodsId.options[goodsIndex].getAttribute("gname");
        var wcode = goodsId.options[goodsIndex].getAttribute("wcode");
        $.ajax({
            'type':'get',
            'dataType':'text',
            'url':'recommend.php?act=get_brands&goods_id=' + $(this).val(),
            'success':function(data){
                data = $.parseJSON(data);
                $('#gname').val(gname);
                $('#wcode').val(wcode);
                $('#r_brand').html('');
                $('#brand_td').attr('style','display:block');
                $(data).each(function(i,val){
                    $('#r_brand').append('<label><input type="checkbox" name="brand_name[]" value=' + val.brand_id + ' />' + val.brand_name + '</label>');
                });
            }
        });
    });
});

onload = function()
{
    // 开始检查订单
    startCheckOrder();
}

//-->
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>