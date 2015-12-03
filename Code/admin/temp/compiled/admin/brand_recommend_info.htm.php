<!-- $Id: category_info.htm gxyang 2014-09-11 -->
<?php echo $this->fetch('pageheader.htm'); ?>
<!-- start add new category form -->
<div class="main-div">
    <form action="" method="post" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
        <table width="100%" id="general-table">
            <tr>
                <td class="label"><?php echo $this->_var['lang']['label_area']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
                <td>
                    <select name="region" id="region">
                        <option value=""><?php echo $this->_var['lang']['brand_select_region']; ?></option>
                        <option value="1" <?php if ($this->_var['brands']['area_id'] == 1): ?>selected<?php endif; ?>><?php echo $this->_var['lang']['china']; ?></option>
                        <?php $_from = $this->_var['region']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'area');if (count($_from)):
    foreach ($_from AS $this->_var['area']):
?>
                        <option value="<?php echo $this->_var['area']['region_id']; ?>" <?php if ($this->_var['brands']['area_id'] == $this->_var['area']['region_id']): ?>selected<?php endif; ?>><?php echo $this->_var['area']['region_name']; ?></option>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['label_cat_name']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
                <td>
                    <select name="category" id="category" onchange="get_category();">
                        <option value="" level=""><?php echo $this->_var['lang']['brand_select_category']; ?></option>
                        <?php echo $this->_var['category']; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['label_brand']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
                <td>
                    <select name="brand_id" id="brand">
                        <option value=""><?php echo $this->_var['lang']['brand_select']; ?></option>
                        <?php $_from = $this->_var['brand_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'brand');if (count($_from)):
    foreach ($_from AS $this->_var['brand']):
?>
                        <option value="<?php echo $this->_var['brand']['brand_id']; ?>"<?php if ($this->_var['brands']['brand_id'] == $this->_var['brand']['brand_id']): ?>selected<?php endif; ?>><?php echo $this->_var['brand']['brand_name']; ?></option>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['label_sort_order']; ?></td>
                <td><input type="text" name="sort_order" id="" value="<?php echo $this->_var['brands']['sort_order']; ?>" /></td>
            </tr>
        </table>
        <div class="button-div">
            <input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
            <input type="reset" value="<?php echo $this->_var['lang']['button_reset']; ?>" />
        </div>
        <input type="hidden" name="act" value="<?php echo $this->_var['form_action']; ?>" />
        <input type="hidden" name="brand_rid" value="<?php echo $this->_var['brands']['brand_rid']; ?>" />
    </form>
</div>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,validator.js')); ?>

<script type="text/javascript" language="JavaScript">
    <!--
function validate()
{
    if($("#region").val() == '') {
        alert('请选择区域')
        return false;
    }
    if($("#category").val() == '') {
        alert('请选择物料类别');
        return false;
    }
    if($("#brand").val() == '') {
        alert('请选择品牌');
        return false;
    }
    return true;
}
function get_category() {
    var obj = document.getElementById('category');
    var index=obj.selectedIndex;
    var level=obj.options[index].getAttribute("level");
    if(level>=3) {
        return true;
    }else {
        obj.options[0].selected='selected';
        alert('只有三级或者四级分类可以添加推荐品牌');
        return false;
    }
}
    onload = function()
    {
        // 开始检查订单
        startCheckOrder();
    }


    //-->
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>