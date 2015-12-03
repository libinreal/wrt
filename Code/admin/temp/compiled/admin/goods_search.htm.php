<div class="form-div">
    <form action="javascript:searchGoods()" name="searchForm">
        <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
        <?php if ($_GET['act'] != "trash"): ?>
        <!-- 分类 -->
        <select name="cat_id"><option value="0"><?php echo $this->_var['lang']['goods_cat']; ?></option><?php echo $this->_var['cat_list']; ?></select>
        <!-- 品牌 -->
        <select name="brand_id"><option value="0"><?php echo $this->_var['lang']['goods_brand']; ?></option><?php echo $this->html_options(array('options'=>$this->_var['brand_list'])); ?></select>
        <select name="area_id"><option value=""><?php echo $this->_var['lang']['area_id']; ?></option>
            <option value="1">全部区域</option>
            <?php $_from = $this->_var['regions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['list']):
?>
            <option value="<?php echo $this->_var['list']['region_id']; ?>"><?php echo $this->_var['list']['region_name']; ?></option>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </select>
        <!-- 推荐 -->
        <!--<select name="intro_type"><option value="0"><?php echo $this->_var['lang']['intro_type']; ?></option><?php echo $this->html_options(array('options'=>$this->_var['intro_list'],'selected'=>$_GET['intro_type'])); ?></select> -->
        <!--<?php if ($this->_var['suppliers_exists'] == 1): ?> -->
        <!-- 供货商 -->
        <!--<select name="suppliers_id"><option value="0"><?php echo $this->_var['lang']['intro_type']; ?></option><?php echo $this->html_options(array('options'=>$this->_var['suppliers_list_name'],'selected'=>$_GET['suppliers_id'])); ?></select> -->
        <!--<?php endif; ?> -->
        <!-- 上架 -->
        <!--<select name="is_on_sale"><option value=''><?php echo $this->_var['lang']['intro_type']; ?></option><option value="1"><?php echo $this->_var['lang']['on_sale']; ?></option><option value="0"><?php echo $this->_var['lang']['not_on_sale']; ?></option></select> -->
        <?php endif; ?>
        <!-- 关键字 -->
        <?php echo $this->_var['lang']['keyword']; ?> <input type="text" name="keyword" size="15" />
        <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" />
    </form>
</div>


<script language="JavaScript">
    function searchGoods()
    {
        
        <?php if ($_GET['act'] != "trash"): ?>
        listTable.filter['cat_id'] = document.forms['searchForm'].elements['cat_id'].value;
        listTable.filter['brand_id'] = document.forms['searchForm'].elements['brand_id'].value;
        listTable.filter['area_id'] = document.forms['searchForm'].elements['area_id'].value;
        //listTable.filter['intro_type'] = document.forms['searchForm'].elements['intro_type'].value;
        //<?php if ($this->_var['suppliers_exists'] == 1): ?>
        //listTable.filter['suppliers_id'] = document.forms['searchForm'].elements['suppliers_id'].value;
        //<?php endif; ?>
        //listTable.filter['is_on_sale'] = document.forms['searchForm'].elements['is_on_sale'].value;
        <?php endif; ?>
        
        listTable.filter['keyword'] = Utils.trim(document.forms['searchForm'].elements['keyword'].value);
        listTable.filter['page'] = 1;

        listTable.loadList();
    }
</script>
