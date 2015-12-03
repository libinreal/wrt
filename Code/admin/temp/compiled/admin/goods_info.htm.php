<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,selectzone.js,colorselector.js')); ?>
<script type="text/javascript" src="../js/calendar.php?lang=<?php echo $this->_var['cfg_lang']; ?>"></script>
<link href="../js/calendar/calendar.css" rel="stylesheet" type="text/css" />
<?php if ($this->_var['warning']): ?>
<ul style="padding:0; margin: 0; list-style-type:none; color: #CC0000;">
    <li style="border: 1px solid #CC0000; background: #FFFFCC; padding: 10px; margin-bottom: 5px;" ><?php echo $this->_var['warning']; ?></li>
</ul>
<?php endif; ?>
<div class="tab-div">
<div id="tabbar-div">
    <p>
        <span class="tab-front" id="general-tab"><?php echo $this->_var['lang']['tab_general']; ?></span>
        <span class="tab-back" id="detail-tab"><?php echo $this->_var['lang']['tab_detail']; ?></span>
        <span class="tab-back" id="mix-tab"><?php echo $this->_var['lang']['tab_mix']; ?></span>
        <span class="tab-back" id="properties-tab"><?php echo $this->_var['lang']['tab_properties']; ?></span>
        <span class="tab-back" id="gallery-tab"><?php echo $this->_var['lang']['tab_gallery']; ?></span>
        <span class="tab-back" id="linkgoods-tab"><?php echo $this->_var['lang']['tab_explain']; ?></span>
        <span class="tab-back" id="groupgoods-tab"><?php echo $this->_var['lang']['tab_sales']; ?></span>
    </p>
</div>
<!-- tab body -->
<div id="tabbody-div">
<form enctype="multipart/form-data" action="goods.php" method="post" name="theForm" onsubmit="return fnGoodsform();">
    <!-- 最大文件限制 -->
    <input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
    <!-- 通用信息 -->
    <table width="90%" id="general-table" align="center">
        <tr>
            <td class="label"><?php echo $this->_var['lang']['lab_goods_name']; ?></td>
            <td>
                <input type="text" name="goods_name" id="goods_name" value="<?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?>" style="float:left;color:<?php echo $this->_var['goods_name_color']; ?>;" size="30" />
                <?php echo $this->_var['lang']['require_field']; ?>
            </td>
        </tr>
        <tr>
            <td class="label"><?php echo $this->_var['lang']['lab_goods_cat']; ?></td>
            <td><select name="cat_id" id="category_selected" onchange="get_categoryChange();">
                <option value="0"><?php echo $this->_var['lang']['select_please']; ?></option><?php echo $this->_var['cat_list']; ?></select>
                <?php echo $this->_var['lang']['require_field']; ?>
            </td>
        </tr>
        <tr>
            <td class="label"><?php echo $this->_var['lang']['lab_goods_brand']; ?></td>
            <td style="position: relative;">
                <span>
                    <input type="hidden" name="brand_id" id="send_id" value="<?php echo $this->_var['goods']['brand_id']; ?>"/>
                    <input style="width: 250px;" type="text" id="tt" autocomplete="off" placeholder="请输入拼音简码选择厂商" value="<?php echo $this->_var['goods']['brand_name']; ?>"/>
                    <?php echo $this->_var['lang']['require_field']; ?>
                </span>
                <div class="brandsInfo" style="">
                </div>
            </td>
        </tr>
        <tr>
            <td class="label"><?php echo $this->_var['lang']['lab_shop_price']; ?></td>
            <td><input type="text" name="shop_price" id="shop_price" value="<?php echo $this->_var['goods']['shop_price']; ?>" size="20" onblur="get_shop_price();" />
                <?php echo $this->_var['lang']['require_field']; ?></td>
        </tr>
        <tr>
            <td class="label"><?php echo $this->_var['lang']['lab_market_price']; ?></td>
            <td>
                <input type="text" name="market_price" id="market_price" value="<?php echo $this->_var['goods']['market_price']; ?>" size="20" onblur="get_market_price();" />
                <?php echo $this->_var['lang']['require_field']; ?>
            </td>
        </tr>
        <tr>
            <td class="label"><?php echo $this->_var['lang']['lab_picture']; ?></td>
            <td>
                <input type="file" name="goods_img" size="35" />
                <?php if ($this->_var['goods']['goods_img']): ?>
                <a href="goods.php?act=show_image&img_url=<?php echo $this->_var['goods']['goods_img']; ?>" target="_blank"><img src="images/yes.gif" border="0" /></a>
                <?php else: ?>
                <img src="images/no.gif" />
                <?php endif; ?>
                <br /><input type="text" size="40" value="<?php echo $this->_var['lang']['lab_picture_url']; ?>" style="color:#aaa;" onfocus="if (this.value == '<?php echo $this->_var['lang']['lab_picture_url']; ?>'){this.value='http://';this.style.color='#000';}" name="goods_img_url"/>
            </td>
        </tr>
        <tr id="auto_thumb_1">
            <td class="label"> <?php echo $this->_var['lang']['lab_thumb']; ?></td>
            <td id="auto_thumb_3">
                <input type="file" name="goods_thumb" size="35" />
                <?php if ($this->_var['goods']['goods_thumb']): ?>
                <a href="goods.php?act=show_image&img_url=<?php echo $this->_var['goods']['goods_thumb']; ?>" target="_blank"><img src="images/yes.gif" border="0" /></a>
                <?php else: ?>
                <img src="images/no.gif" />
                <?php endif; ?>
                <br /><input type="text" size="40" value="<?php echo $this->_var['lang']['lab_thumb_url']; ?>" style="color:#aaa;" onfocus="if (this.value == '<?php echo $this->_var['lang']['lab_thumb_url']; ?>'){this.value='http://';this.style.color='#000';}" name="goods_thumb_url"/>
                <?php if ($this->_var['gd'] > 0): ?>
                <br /><label for="auto_thumb"><input type="checkbox" id="auto_thumb" name="auto_thumb" checked="true" value="1" onclick="handleAutoThumb(this.checked)" /><?php echo $this->_var['lang']['auto_thumb']; ?></label><?php endif; ?>
            </td>
        </tr>
        <tr>
            <td id="tbody-goodsAttr" colspan="2" style="padding:0"><?php echo $this->_var['goods_attr_html']; ?></td>
        </tr>
    </table>

    <!-- 详细描述 -->
    <table width="90%" id="detail-table" style="display:none">
        <tr>
            <td><?php echo $this->_var['FCKeditor']; ?></td>
        </tr>
    </table>

    <!-- 其他信息 -->
    <table width="90%" id="mix-table" style="display:none" align="center">
        <?php if ($this->_var['cfg']['use_storage']): ?>
        <tr>
            <td class="label"><a href="javascript:showNotice('noticeStorage');" title="<?php echo $this->_var['lang']['form_notice']; ?>"><img src="images/notice.gif" width="16" height="16" border="0" alt="<?php echo $this->_var['lang']['form_notice']; ?>"></a> <?php echo $this->_var['lang']['lab_goods_number']; ?></td>
            <!--            <td><input type="text" name="goods_number" value="<?php echo $this->_var['goods']['goods_number']; ?>" size="20" <?php if ($this->_var['code'] != '' || $this->_var['goods']['_attribute'] != ''): ?>readonly="readonly"<?php endif; ?> /><br />-->
            <td><input type="text" name="goods_number" value="<?php echo $this->_var['goods']['goods_number']; ?>" size="20" /><br />
                <span class="notice-span" <?php if ($this->_var['help_open']): ?>style="display:block" <?php else: ?> style="display:none" <?php endif; ?> id="noticeStorage"><?php echo $this->_var['lang']['notice_storage']; ?></span></td>
        </tr>
        <tr>
            <td class="label"><?php echo $this->_var['lang']['lab_warn_number']; ?></td>
            <td><input type="text" name="warn_number" value="<?php echo $this->_var['goods']['warn_number']; ?>" size="20" /></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="label"><?php echo $this->_var['lang']['lab_area']; ?></td>
            <td>
                <select name="area" id="area_id">
                    <option value=""><?php echo $this->_var['lang']['selected_area']; ?></option>
                    <option value="1" <?php if ($this->_var['goods']['area_id'] == 1): ?>selected<?php endif; ?>><?php echo $this->_var['lang']['selected_All']; ?></option>
                    <?php $_from = $this->_var['areas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'area_region');if (count($_from)):
    foreach ($_from AS $this->_var['area_region']):
?>
                    <option value="<?php echo $this->_var['area_region']['region_id']; ?>" <?php if ($this->_var['goods']['area_id'] == $this->_var['area_region']['region_id']): ?>selected<?php endif; ?>><?php echo $this->_var['area_region']['region_name']; ?></option>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </select>
                <?php echo $this->_var['lang']['require_field']; ?>
            </td>
        </tr>
        <tr>
            <td class="label"><?php echo $this->_var['lang']['lab_shiplocal']; ?></td>
            <td>
                <input type="text" name="shiplocal" id="" value="<?php echo $this->_var['goods']['shiplocal']; ?>"/>
            </td>
        </tr>
        <tr id="alone_sale_1">
            <td class="label" id="alone_sale_2"><?php echo $this->_var['lang']['lab_is_on_sale']; ?></td>
            <td id="alone_sale_3"><input type="checkbox" name="is_on_sale" value="1" <?php if ($this->_var['goods']['is_on_sale']): ?>checked="checked"<?php endif; ?> /> <?php echo $this->_var['lang']['on_sale_desc']; ?></td>
        </tr>
    </table>

    <!-- 属性与规格 -->
    <table width="90%" id="properties-table" style="display:none">
        <tr>
            <td><?php echo $this->_var['goods_brief_FCKeditor']; ?></td>
        </tr>
    </table>
    <!-- 商品相册 -->
    <table width="90%" id="gallery-table" style="display:none" align="center">
        <!-- 图片列表 -->
        <tr>
            <td>
                <?php $_from = $this->_var['img_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('i', 'img');if (count($_from)):
    foreach ($_from AS $this->_var['i'] => $this->_var['img']):
?>
                <div id="gallery_<?php echo $this->_var['img']['img_id']; ?>" style="float:left; text-align:center; border: 1px solid #DADADA; margin: 4px; padding:2px;">
                    <a href="javascript:;" onclick="if (confirm('<?php echo $this->_var['lang']['drop_img_confirm']; ?>')) dropImg('<?php echo $this->_var['img']['img_id']; ?>')">[-]</a><br />
                    <a href="goods.php?act=show_image&img_url=<?php echo $this->_var['img']['img_url']; ?>" target="_blank">
                        <img src="../<?php if ($this->_var['img']['thumb_url']): ?><?php echo $this->_var['img']['thumb_url']; ?><?php else: ?><?php echo $this->_var['img']['img_url']; ?><?php endif; ?>" <?php if ($this->_var['thumb_width'] != 0): ?>width="<?php echo $this->_var['thumb_width']; ?>"<?php endif; ?> <?php if ($this->_var['thumb_height'] != 0): ?>height="<?php echo $this->_var['thumb_height']; ?>"<?php endif; ?> border="0" />
                    </a><br />
                    <input type="text" value="<?php echo htmlspecialchars($this->_var['img']['img_desc']); ?>" size="15" name="old_img_desc[<?php echo $this->_var['img']['img_id']; ?>]" />
                </div>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <!-- 上传图片 -->
        <tr>
            <td>
                <a href="javascript:;" onclick="addImg(this)">[+]</a>
                <?php echo $this->_var['lang']['img_desc']; ?> <input type="text" name="img_desc[]" size="20" />
                <?php echo $this->_var['lang']['img_url']; ?> <input type="file" name="img_url[]" />
                <input type="text" size="40" value="<?php echo $this->_var['lang']['img_file']; ?>" style="color:#aaa;" onfocus="if (this.value == '<?php echo $this->_var['lang']['img_file']; ?>'){this.value='http://';this.style.color='#000';}" name="img_file[]"/>
            </td>
        </tr>
    </table>

    <table width="90%" id="linkgoods-table" style="display:none">
        <tr>
            <td><?php echo $this->_var['explain_FCKeditor']; ?></td>
        </tr>
    </table>

    <!-- 售后保障 -->
    <table width="90%" id="groupgoods-table" style="display:none">
        <tr>
            <td><?php echo $this->_var['SalesFCKeditor']; ?></td>
        </tr>
    </table>
    <div class="button-div">
        <input type="hidden" id="goods_id" name="goods_id" value="<?php echo $this->_var['goods']['goods_id']; ?>" />
        <?php if ($this->_var['code'] != ''): ?>
        <input type="hidden" name="extension_code" value="<?php echo $this->_var['code']; ?>" />
        <?php endif; ?>
        <input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button" />
        <input type="reset" value="<?php echo $this->_var['lang']['button_reset']; ?>" class="button" />
        <input type="hidden" name="act" value="<?php echo $this->_var['form_act']; ?>" />
    </div>

</form>
</div>
</div>
<!-- end goods form -->
<?php echo $this->smarty_insert_scripts(array('files'=>'validator.js,tab.js')); ?>

<script type="text/javascript" charset="utf-8" language="JavaScript">
var categoryId = [];
<?php $_from = $this->_var['insertCategory']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'category');if (count($_from)):
    foreach ($_from AS $this->_var['category']):
?>

    categoryId.push(<?php echo $this->_var['category']['cat_id']; ?>);
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

var goodsId = '<?php echo $this->_var['goods']['goods_id']; ?>';
var elements = document.forms['theForm'].elements;
var sz1 = new SelectZone(1, elements['source_select1'], elements['target_select1']);
var sz2 = new SelectZone(2, elements['source_select2'], elements['target_select2'], elements['price2']);
var sz3 = new SelectZone(1, elements['source_select3'], elements['target_select3']);
var marketPriceRate = <?php echo empty($this->_var['cfg']['market_price_rate']) ? '1' : $this->_var['cfg']['market_price_rate']; ?>;
var integralPercent = <?php echo empty($this->_var['cfg']['integral_percent']) ? '0' : $this->_var['cfg']['integral_percent']; ?>;


onload = function()
{
    if (document.forms['theForm'].elements['auto_thumb'])
    {
        handleAutoThumb(document.forms['theForm'].elements['auto_thumb'].checked);
    }
    // 检查新订单
    startCheckOrder();
    document.forms['theForm'].reset();
}
function get_categoryChange() {
    //alert($('#goods_id').val());
    var catValue = $("#category_selected").val();
    var goodsId = $("#goods_id").val();
    var len = categoryId.length;
    var flag = true;
    for(var i=0;i<len;i++) {
        if(catValue == categoryId[i]) {
            flag = true;
            break;
        }else{
            flag = false;
        }
    }
    console.log(catValue);
    if(flag){
        Ajax.call('goods.php?act=goods_type', "id="+catValue+"&goodsId="+goodsId, goodsAttrResponse, "POST", "JSON");
    }else{
        alert('不可以添加');
        $("#category_selected").val('0');
    }
}

function goodsAttrResponse(msg) {
    $("#tbody-goodsAttr").html('');
    $("#tbody-goodsAttr").append(msg.content);
    //alert(msg.content);
}
function get_shop_price() {
    var shop_price = document.getElementById("shop_price").value;
    if(shop_price=='') {
        alert('Vip价格不能为空');
        document.getElementById("shop_price").value = '0.00';
    }
    if(shop_price<0) {
        alert('Vip价格不能小于0');
        document.getElementById("shop_price").value = '0.00';
    }
    if(!((/^[0-9]+(.[0-9]+)|[0-9]$/).test(shop_price))) {
        alert('Vip价格格式不正确');
        document.getElementById("shop_price").value = '0.00';
    }
}
function get_market_price() {
    var market_price = document.getElementById("market_price").value;
    if(market_price=='') {
        alert('出厂价格不能为空');
        document.getElementById("market_price").value = '0.00';
    }
    if(market_price<0) {
        alert('出厂价格不能小于0');
        document.getElementById("market_price").value = '0.00';
    }
    if(!((/^[0-9]+(.[0-9]+)|[0-9]$/).test(market_price))) {
        alert('出厂价格格式不正确');
        document.getElementById("market_price").value = '0.00';
    }
}

function fnGoodsform() {
    if($('#goods_name').val()=='') {
        alert(goods_name_not_null)
        return false;
    }
    if($('#category_selected').val()==0) {
        alert(goods_cat_not_null);
        return false;
    }
    if($('#tt').val()=='') {
        alert('物料厂商必须选择');
        return false;
    }
    if($("#shop_price").val()=='') {
        alert('Vip价格不能为空');
        $("#shop_price").val('0.00');
        return false;
    }
    if($("#shop_price").val()<0) {
        alert('Vip价格不能小于0');
        $("#shop_price").val('0.00');
        return false;
    }
    if(!((/^[0-9]+(.[0-9]+)|[0-9]$/).test($("#shop_price").val()))) {
        alert('Vip价格格式不正确');
        $("#shop_price").val('0.00');
        return false;
    }
    if($("#market_price").val()=='') {
        alert('出厂价格不能为空');
        $("#market_price").val('0.00');
        return false;
    }
    if($("#market_price").val()<0) {
        alert('出厂价格不能小于0');
        $("#market_price").val('0.00');
        return false;
    }
    if(!((/^[0-9]+(.[0-9]+)|[0-9]$/).test($("#market_price").val()))) {
        alert('出厂价格格式不正确');
        $("#market_price").val('0.00');
        return false;
    }
    if($('#area_id').val()=='') {
        alert(no_selected_area);
        return false;
    }
    if($('.goods_type').val()=='') {
        alert('商品规格属性必须选择');
        return false;
    }
    var doc = document.getElementById('attrTable').rows;
    var len = doc.length;
    var i = 0;
    var selectStr = '';
    for(i;i<len;i++) {
        var tdNodes = doc[i].getElementsByTagName('td')[1];
        var selectOption = tdNodes.getElementsByTagName('select')[0];
        var index=selectOption.options.selectedIndex;
        var OptionVal = selectOption.options[index].value;
        selectStr +=OptionVal;

    }
    if(selectStr.indexOf('default')!= -1) {
        alert('您有规格属性没有选择');
        return false;
    }
    return true;
}

/**
 * 切换商品类型
 */
function getAttrList(goodsId)
{
    var selGoodsType = document.forms['theForm'].elements['goods_type'];

    if (selGoodsType != undefined)
    {
        var goodsType = selGoodsType.options[selGoodsType.selectedIndex].value;

        Ajax.call('goods.php?is_ajax=1&act=get_attr', 'goods_id=' + goodsId + "&goods_type=" + goodsType, setAttrList, "GET", "JSON");
    }
}

function setAttrList(result, text_result)
{
    document.getElementById('tbody-goodsAttr').innerHTML = result.content;
}

function handleAutoThumb(checked)
{
    document.forms['theForm'].elements['goods_thumb'].disabled = checked;
    document.forms['theForm'].elements['goods_thumb_url'].disabled = checked;
}



/**
 * 新增一个图片
 */
function addImg(obj)
{
    var src  = obj.parentNode.parentNode;
    var idx  = rowindex(src);
    var tbl  = document.getElementById('gallery-table');
    var row  = tbl.insertRow(idx + 1);
    var cell = row.insertCell(-1);
    cell.innerHTML = src.cells[0].innerHTML.replace(/(.*)(addImg)(.*)(\[)(\+)/i, "$1removeImg$3$4-");
}

/**
 * 删除图片上传
 */
function removeImg(obj)
{
    var row = rowindex(obj.parentNode.parentNode);
    var tbl = document.getElementById('gallery-table');

    tbl.deleteRow(row);
}

/**
 * 删除图片
 */
function dropImg(imgId)
{
    Ajax.call('goods.php?is_ajax=1&act=drop_image', "img_id="+imgId, dropImgResponse, "GET", "JSON");
}

function dropImgResponse(result)
{
    if (result.error == 0)
    {
        document.getElementById('gallery_' + result.content).style.display = 'none';
    }
}

function fnBrandResponse(msg) {
    var i = 0;
    var brandInfos = '';
    var len = msg.length;
    var brandInfos = '';
    for(i;i<len;i++) {
        brandInfos +='<span data-id="'+msg[i].brand_id+'">'+msg[i].brand_name+'</span>'
    }
    $('.brandsInfo').html(brandInfos).show();
    $('.brandsInfo span').hover(function(){
        $(this).css({'background':'red','cursor':'pointer'});
    },function(){
        $(this).css({'background':''});
    });
}

var historyContainer = $('.brandsInfo'),
        indexLI,
        si = 9,
        wi = 0,
        upCount = 0,downCount = 0;
$("#tt").click(function(e){
    var pinyin = $("#tt").val().toUpperCase();
    //if(!pinyin) return;
    Ajax.call('goods.php?act=brands&code='+pinyin, "", fnBrandResponse, "GET", "JSON");
})
$("#tt").on('keyup', function(e) {
    if(e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40){
        var pinyin = $("#tt").val().toUpperCase();
        Ajax.call('goods.php?act=brands&code='+pinyin, "", fnBrandResponse, "GET", "JSON");
    }else{
        var max = historyContainer.children().length;
        historyContainer.find('span').removeClass('active');
        //down=40 up=38
        if (e.keyCode == 40) {
            if(typeof indexLI == 'undefined') indexLI = -1;
            indexLI++;
            downCount++;
            upCount = 0;
        } else if (e.keyCode == 38) {
            if(typeof indexLI == 'undefined') indexLI = 0;
            indexLI--;
            upCount ++;
            downCount = 0;
        }
        if (indexLI >= max - 1) {
            indexLI = max - 1;
        }
        if (indexLI < 0) {
            indexLI = 0;
        }
        var cur = historyContainer.find('span:eq(' + indexLI + ')');
        cur.addClass('active');
        $('#tt').val(cur.text());
        $("#send_id").val(cur.attr('data-id'));
    }
});
$('#shop_price').on('focusin', function(e) {
    closeHistoryContainer();
});
historyContainer.on('click', 'span', function() {
    $('#tt').val($(this).text());
    $("#send_id").val($(this).attr('data-id'));
    closeHistoryContainer();
});
$(document).on('click', function(event) {
    if ($(event.target).parent().hasClass('brandsInfo') || $(event.target).attr('id') == 'tt') return;
    closeHistoryContainer();
});

//关闭弹出下拉框，重置数据
function closeHistoryContainer(){
    historyContainer.hide();
    historyContainer.find('span').removeClass('active');
    indexLI = undefined;
}

</script>
<?php echo $this->fetch('pagefooter.htm'); ?>

<style type="text/css">
    .brandsInfo {overflow-y:auto;display: none;position: absolute;top:22px;z-index:555;background:#fff;height: 200px;width: 250px;border:1px solid #cccccc;}
    .brandsInfo span {display: block;line-height: 20px;padding-left: 3px;}
    .active{background: red;color: #ffffff;}
</style>

