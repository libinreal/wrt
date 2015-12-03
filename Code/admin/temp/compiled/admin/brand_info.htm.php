<!-- $Id: brand_info.htm 14216 2008-03-10 02:27:21Z testyang $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<div class="main-div">
    <form method="post" action="brand.php" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
        <table cellspacing="1" cellpadding="3" width="100%">
            <tr>
                <td class="label"><?php echo $this->_var['lang']['label_brand_name']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
                <td><input type="text" id="brand_name" name="brand_name" maxlength="60" value="<?php echo $this->_var['brand']['brand_name']; ?>" /></td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['label_brand_pinyin']; ?><span class="require-field">&nbsp;</span></td>
                <td><input type="text" id="brand_pinyin" name="brand_pinyin" value="<?php echo $this->_var['brand']['brand_pinyin']; ?>" readonly="readonly" /></td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['label_site_url']; ?><span class="require-field">&nbsp;</span></td>
                <td><input type="text" name="site_url" maxlength="60" size="40" value="<?php echo $this->_var['brand']['site_url']; ?>" /></td>
            </tr>
            <tr>
                <td class="label">
                    <a href="javascript:showNotice('warn_brandlogo');" title="<?php echo $this->_var['lang']['form_notice']; ?>">
                    <img src="images/notice.gif" width="16" height="16" border="0" alt="<?php echo $this->_var['lang']['form_notice']; ?>"></a><?php echo $this->_var['lang']['label_brand_logo']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
                <td>
                    <input type="file" name="brand_logo" id="logo" size="45">
                    <?php if ($this->_var['brand']['brand_logo'] != ""): ?>
                    <input type="button" value="<?php echo $this->_var['lang']['drop_brand_logo']; ?>" onclick="if (confirm('<?php echo $this->_var['lang']['confirm_drop_logo']; ?>'))location.href='brand.php?act=drop_logo&id=<?php echo $this->_var['brand']['brand_id']; ?>'"><?php endif; ?>
                    <br /><span class="notice-span" <?php if ($this->_var['help_open']): ?>style="display:block" <?php else: ?> style="display:none" <?php endif; ?> id="warn_brandlogo">
                    <?php if ($this->_var['brand']['brand_logo'] == ''): ?>
                    <?php echo $this->_var['lang']['up_brandlogo']; ?>
                    <?php else: ?>
                    <?php echo $this->_var['lang']['warn_brandlogo']; ?>
                    <?php endif; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['label_brand_code']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
                <td><input type="text" name="brand_code" value="<?php echo $this->_var['brand']['brand_code']; ?>" id="brand_code" /></td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['label_brand_desc']; ?><span class="require-field"></span></td>
                <td><textarea  name="brand_desc" cols="60" rows="4"  ><?php echo $this->_var['brand']['brand_desc']; ?></textarea></td>
            </tr>
            <!--
            <tr>
                <td class="label"><?php echo $this->_var['lang']['sort_order']; ?></td>
                <td><input type="text" name="sort_order" maxlength="40" size="15" value="<?php echo $this->_var['brand']['sort_order']; ?>" /></td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['is_show']; ?></td>
                <td><input type="radio" name="is_show" value="1" <?php if ($this->_var['brand']['is_show'] == 1): ?>checked="checked"<?php endif; ?> /> <?php echo $this->_var['lang']['yes']; ?>
                    <input type="radio" name="is_show" value="0" <?php if ($this->_var['brand']['is_show'] == 0): ?>checked="checked"<?php endif; ?> /> <?php echo $this->_var['lang']['no']; ?>
                    (<?php echo $this->_var['lang']['visibility_notes']; ?>)
                </td>
            </tr>
            -->
            <tr>
                <td colspan="2" align="center"><br />
                    <input type="submit" class="button" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
                    <input type="reset" class="button" value="<?php echo $this->_var['lang']['button_reset']; ?>" />
                    <input type="hidden" name="act" value="<?php echo $this->_var['form_action']; ?>" />
                    <input type="hidden" name="old_brandname" value="<?php echo $this->_var['brand']['brand_name']; ?>" />
                    <input type="hidden" name="id" value="<?php echo $this->_var['brand']['brand_id']; ?>" />
                    <input type="hidden" name="old_brandlogo" value="<?php echo $this->_var['brand']['brand_logo']; ?>">
                </td>
            </tr>
        </table>
    </form>
</div>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,validator.js,pinyin.js')); ?>

<script language="JavaScript">
    <!--
    document.forms['theForm'].elements['brand_name'].focus();

    $("#brand_name").blur(function(){
        var str = document.getElementById("brand_name").value.trim();
        if(str == "") return false;
        var arrRslt = makePy(str);
        if(arrRslt.length>1) {
            arrRslt = arrRslt[0];
        }
        var div = document.getElementById("brand_pinyin");
        div.value = "" + arrRslt +"";
    });
    onload = function()
    {
        // 开始检查订单
        startCheckOrder();
    }
    /**
     * 检查表单输入的数据
     */
    function validate()
    {
        var brandName = document.getElementById('brand_name').value;
        if(brandName=='') {
            alert(no_brandname);
            return false;
        }
        
        <?php if ($this->_var['form_action'] == 'insert'): ?>
        
        var brandImg = document.getElementById('logo').value;
        if(brandImg=='') {
            alert('Logo必须上传');
            return false

        }else {
            if (!/\.(gif|jpg|jpeg|png|GIF|JPG|PNG)$/.test(brandImg)) {
                alert("图片类型必须是.gif,jpeg,jpg,png中的一种");
                brandImg = "";
                return false;
            }
        }
        
        <?php endif; ?>
        <?php if ($this->_var['form_action'] == 'updata'): ?>
            <?php if ($this->_var['brand']['brand_logo'] == ''): ?>
            
            var brandImg = document.getElementById('logo').value;
            if(brandImg=='') {
                alert('Logo必须上传');
                return false

            }else {

                if (!/\.(gif|jpg|jpeg|png|GIF|JPG|PNG)$/.test(brandImg)) {
                    alert("图片类型必须是.gif,jpeg,jpg,png中的一种");
                    brandImg = "";
                    return false;
                }
            }
            
            <?php endif; ?>
        <?php endif; ?>
        var brandCode = document.getElementById('brand_code').value;
        if(brandCode=='') {
            alert(no_brand_code)
            return false;
        }
        return true;
    }
    //-->
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>