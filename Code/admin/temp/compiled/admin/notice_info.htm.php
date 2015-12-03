<!-- $Id: article_info.htm 2014-09-03 xy $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,selectzone.js,validator.js')); ?>
<!-- start goods form -->
<div class="tab-div">
    <div id="tabbar-div">
        <p>
            <span class="tab-front" id="general-tab"><?php echo $this->_var['lang']['tab_general']; ?></span>
            <span class="tab-back" id="detail-tab"><?php echo $this->_var['lang']['tab_content']; ?></span>
            <!--<span class="tab-back" id="goods-tab"><?php echo $this->_var['lang']['tab_goods']; ?></span>-->
        </p>
    </div>

    <div id="tabbody-div">
        <form  action="notice.php" method="post" enctype="multipart/form-data" id="myform" name="theForm" onsubmit="return validate()">
            <table width="90%" id="general-table">
                <tr>
                    <td class="narrow-label"><?php echo $this->_var['lang']['title']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
                    <td><input type="text" name="title" id="notice_title" size ="40" maxlength="60" value="<?php echo htmlspecialchars($this->_var['article']['title']); ?>" /></td>
                </tr>
                <!-- <?php if ($this->_var['article']['cat_id'] >= 0): ?> -->
                <tr>
                    <td class="narrow-label"><?php echo $this->_var['lang']['cat']; ?><?php echo $this->_var['lang']['require_field']; ?> </td>
                    <td>
                        <select name="article_cat" id="article_cat" onchange="catChanged()">
                            <option value="0"><?php echo $this->_var['lang']['select_plz']; ?></option>
                            <?php echo $this->_var['cat_select']; ?>
                        </select>
                    </td>
                    <input type="hidden" id="cat_type" name="cat_type" value="<?php echo $this->_var['article']['cat_type']; ?>" />
                </tr>
                <!-- <?php else: ?> -->
                <input type="hidden" name="article_cat" value="-1" />
                <!-- <?php endif; ?> -->
                <?php if ($this->_var['article']['cat_id'] >= 0): ?>
                <tr class="nothelp">
                    <td class="narrow-label"><?php echo $this->_var['lang']['article_type']; ?><?php echo $this->_var['lang']['require_field']; ?></td>
                    <td><input type="radio" id="type_ge" name="article_type" value="0" <?php if ($this->_var['article']['article_type'] == 0): ?>checked<?php endif; ?>><?php echo $this->_var['lang']['common']; ?>
                        <input type="radio" id="type_top" name="article_type" value="1" onclick="typechange()" <?php if ($this->_var['article']['article_type'] == 1): ?>checked<?php endif; ?>><?php echo $this->_var['lang']['top']; ?>
                    </td>
                </tr>
                <!--<tr>
                  <td class="narrow-label"><?php echo $this->_var['lang']['is_open']; ?></td>
                  <td>
                  <input type="radio" name="is_open" value="1" <?php if ($this->_var['article']['is_open'] == 1): ?>checked<?php endif; ?>> <?php echo $this->_var['lang']['isopen']; ?>
                <input type="radio" name="is_open" value="0" <?php if ($this->_var['article']['is_open'] == 0): ?>checked<?php endif; ?>> <?php echo $this->_var['lang']['isclose']; ?><?php echo $this->_var['lang']['require_field']; ?>        </td>
                </tr>-->
                <?php else: ?>
                <tr style="display:none">
                    <td colspan="2">
                        <input type="hidden" name="article_type" value="0" />
                        <input type="hidden" name="is_open" value="1" />
                    </td>
                </tr>
                <?php endif; ?>
                <!--<tr>
                  <td class="narrow-label"><?php echo $this->_var['lang']['author']; ?></td>
                  <td><input type="text" name="author" maxlength="60" value="<?php echo htmlspecialchars($this->_var['article']['author']); ?>" /></td>
                </tr>
                <tr>
                  <td class="narrow-label"><?php echo $this->_var['lang']['email']; ?></td>
                  <td><input type="text" name="author_email" maxlength="60" value="<?php echo htmlspecialchars($this->_var['article']['author_email']); ?>" /></td>
                </tr>
                <tr>
                  <td class="narrow-label"><?php echo $this->_var['lang']['keywords']; ?></td>
                  <td><input type="text" name="keywords" maxlength="60" value="<?php echo htmlspecialchars($this->_var['article']['keywords']); ?>" /></td>
                </tr>-->
                <tr>
                    <td class="narrow-label"><?php echo $this->_var['lang']['source']; ?><span class="require-field"></span></td>
                    <td><input type="text" name="source" maxlength="32" value="<?php echo $this->_var['article']['source']; ?>"/></td>
                </tr>
                <tr class="nothelp">
                    <td class="narrow-label"><?php echo $this->_var['lang']['lable_description']; ?><span class="require-field"></span> </td>
                    <td><textarea name="description" id="description" cols="40" rows="5"><?php echo htmlspecialchars($this->_var['article']['description']); ?></textarea></td>
                </tr>
                <!--<tr>
                  <td class="narrow-label"><?php echo $this->_var['lang']['external_links']; ?></td>
                  <td><input name="link_url" type="text" id="link_url" value="<?php if ($this->_var['article']['link'] != ''): ?><?php echo htmlspecialchars($this->_var['article']['link']); ?><?php else: ?>http://<?php endif; ?>" maxlength="60" /></td>
                </tr>-->
                <tr class="nothelp">
                    <td class="narrow-label"><?php echo $this->_var['lang']['upload_titleimg']; ?><span class="require-field"></span></td>
                    <td>
                        <div class="up_tit">
                            <?php if ($this->_var['article']['imgurl'] != ''): ?>
                            <div class="pic_box" id="img4" style="float:left; text-align:center; border: 1px solid #DADADA; margin: 4px; padding:2px;">
                                <a href="javascript:;" onclick="del_pic('img4')">[-]</a><br />
                                <img style="vertical-align: middle;" src="../<?php echo $this->_var['article']['imgurl']; ?>" width="90" height="85" border="0" />
                                <br />
                            </div>
                            <?php endif; ?>
                        </div>
                        <div style="display: block;clear:both;">
                            <input type="file" name="img4" id="uimg4" imgvalue="<?php echo htmlspecialchars($this->_var['article']['imgurl']); ?>">
                        </div>
                    </td>
                </tr>
                <tr><td>&nbsp;</td></tr>
                <tr class="nothelp">
                    <td class="narrow-label"><?php echo $this->_var['lang']['upload_file']; ?><span class="require-field"></span> </td>
                    <td>
                        <div style="width:100%;" class="file_img">
                            <?php $_from = $this->_var['article']['img_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('i', 'img');if (count($_from)):
    foreach ($_from AS $this->_var['i'] => $this->_var['img']):
?>
                            <?php if ($this->_var['img'] != ''): ?>
                            <div class="pic_box" id="<?php echo $this->_var['i']; ?>" style="float:left; text-align:center; border: 1px solid #DADADA; margin: 4px; padding:2px;">
                                <a href="javascript:;" onclick="del_pic('<?php echo $this->_var['i']; ?>')">[-]</a><br />
                                <img style="vertical-align: middle;" src="../<?php echo $this->_var['img']; ?>" width="90" height="85" border="0" />
                                <br />
                            </div>
                            <?php endif; ?>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </div>
                        <div style="display: block;clear:both;">
                            <?php echo $this->_var['lang']['upload_file1']; ?>：<input type="file" name="img1" id="uimg1" value="<?php echo htmlspecialchars($this->_var['article']['img1']); ?>"><br />
                            <?php echo $this->_var['lang']['upload_file2']; ?>：<input type="file" name="img2" id="uimg2" value="<?php echo htmlspecialchars($this->_var['article']['img2']); ?>"><br />
                            <?php echo $this->_var['lang']['upload_file3']; ?>：<input type="file" name="img3" id="uimg3" value="<?php echo htmlspecialchars($this->_var['article']['img3']); ?>">
                        </div>

                    </td>
                </tr>
            </table>

            <table width="90%" id="detail-table" style="display:none">
                <tr><td><?php echo $this->_var['FCKeditor']; ?></td></tr>
            </table>

            <div class="button-div">
                <input type="hidden" name="act" value="<?php echo $this->_var['form_action']; ?>" />
                <input type="hidden" name="old_title" value="<?php echo $this->_var['article']['title']; ?>"/>
                <input type="hidden" name="id" value="<?php echo $this->_var['article']['article_id']; ?>" />
                <input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button" />
                <input type="reset" value="<?php echo $this->_var['lang']['button_reset']; ?>" class="button" />
            </div>
        </form>
    </div>

</div>
<!-- end goods form -->
<script language="JavaScript">

var articleId = <?php echo empty($this->_var['article']['article_id']) ? '0' : $this->_var['article']['article_id']; ?>;
var elements  = document.forms['theForm'].elements;
var sz        = new SelectZone(1, elements['source_select'], elements['target_select'], '');
var ArtType = <?php echo empty($this->_var['article']['cat_type']) ? '1001' : $this->_var['article']['cat_type']; ?>;

onload = function()
{
    if(ArtType == 3001 || ArtType == 3002 || ArtType == 3003 || ArtType == 3004){
        $('.nothelp').hide();
    }else{
        $('.nothelp').show();
    }
    // 开始检查订单
    startCheckOrder();
}

function validate()
{
    if($("#notice_title").val()=='') {
        alert(no_title);
        return false;
    }
    
    <?php if ($this->_var['article']['cat_id'] >= 0): ?>
    
        if($("#article_cat").val()=='') {
            alert(no_cat)
            return false;
        }
    
    <?php endif; ?>
    
        if($("input[name='article_type']:checked").val()==1) {
            var Img = $("#uimg4");
            if(Img.val()=='' && Img.attr('imgvalue') == "" ) {
                alert('置顶必须上传图片');
                return false;
            }else{
                if (!/\.(gif|jpg|jpeg|png|GIF|JPG|PNG)$/.test(Img.val())) {
                    alert('图片格式不正确');
                    return false;
                }
            }
        }
    return true;
    /*var notice_title = document.getElementById('notice_title').value;

    var validator = new Validator('theForm');
    validator.required('title', no_title);
    
    <?php if ($this->_var['article']['cat_id'] >= 0): ?>
    validator.isNullOption('article_cat',no_cat);
    <?php endif; ?>
    
    var obj = document.forms['theForm'].elements['article_cat'];
    cat_type = obj.options[obj.selectedIndex].getAttribute('cat_type');
    if(cat_type != 3001 && cat_type != 3002 && cat_type != 3003 &&  cat_type != 3004){
        $img_c = checkImgType();
        if($img_c === false){
            return false
        }
    }
    return validator.passed();*/
}

function typechange(){
    var obj = document.forms['theForm'].elements['article_cat'];
    var cat_id = obj.options[obj.selectedIndex].value;
    var r=true;
    if($('#type_top').val() != '<?php echo $this->_var['article']['article_type']; ?>'){
        $.ajax({
            'type':'get',
            'dataType':'text',
            'url':"notice.php?act=chang_type&article_type="+$('#type_top').val()+"&cat_id="+cat_id,
            'success':function(data){
                if(data == 'error'){
                    alert(not_allow_type);
                    $('#type_ge').attr("checked",true);
                    return false;
                }else if(data == 'ok'){
                    return true;
                }
            }
        });
    }

}

function checkImgType(){
    var img1 = $('#uimg1');
    var img2 = $('#uimg2');
    var img3 = $('#uimg3');
    var img4 = $('#uimg4');
    var imgi;
    if(img4.attr('imgvalue') == "" && img4.val() == ""){
        alert("标题图片不能为空！");
        return false;
    }else{
        for(var i=1;i<=4;i++){
            imgi = $('#uimg'+i);
            if(imgi.val() != ""){
                if (!/\.(gif|jpg|jpeg|png|GIF|JPG|PNG)$/.test(imgi.val())) {
                    if(i == 4){
                        alert("图片类型必须是.gif,jpeg,jpg,png中的一种,标题图片类型错误");
                    }else{
                        alert("图片类型必须是.gif,jpeg,jpg,png中的一种,第"+i+"张图片类型错误");
                    }
                    imgi.val('');
                    return false;
                }
            }
        }
    }
    return true;
}

function del_pic(id){
    if(confirm('<?php echo $this->_var['lang']['drop_img_confirm']; ?>')){
        if(id){
            $.ajax({
                'type':'get',
                'dataType':'text',
                'url':"notice.php?act=drop_image&img_id="+id+"&article_id=<?php echo $this->_var['article']['article_id']; ?>",
                'success':function(data){
                    if(id == 'img4'){
                        ajax_del_titlepic(data);
                    }else{
                        ajax_del_listpic(data);
                    }
                }
            });
        }
    }
}
function ajax_del_listpic(data){
    if(data == 'pic_empty'){
        $('.file_img').html('');
    }else{
        $('.file_img').html('');
        var r= $.parseJSON(data);
        if(r){
            var count=1;
            var html="";
            while(r['img'+count]){
                var i = 'img'+count;
                html += "<div class='pic_box' id='img"+count+"' style='float:left; text-align:center; border: 1px solid #DADADA; margin: 4px; padding:2px;'><a href='javascript:;' onclick=del_pic('"+i+"')>[-]</a><br /><img style='vertical-align: middle;' src='../"+r['img'+count]+"' width='90' height='85' border='0' /><br /></div>";
                count++;
            }
            $('.file_img').html(html);
        }
    }
}
function ajax_del_titlepic(data){
    $('.up_tit').html('');
    $('#uimg4').attr('imgvalue','')
}

document.getElementById("tabbar-div").onmouseover = function(e){
    var obj = Utils.srcElement(e);

    if (obj.className == "tab-back")
    {
        obj.className = "tab-hover";
    }
}

document.getElementById("tabbar-div").onmouseout = function(e){
    var obj = Utils.srcElement(e);

    if (obj.className == "tab-hover")
    {
        obj.className = "tab-back";
    }
}

document.getElementById("tabbar-div").onclick = function(e){
    var obj = Utils.srcElement(e);

    if (obj.className == "tab-front")
    {
        return;
    }
    else
    {
        objTable = obj.id.substring(0, obj.id.lastIndexOf("-")) + "-table";

        var tables = document.getElementsByTagName("table");
        var spans  = document.getElementsByTagName("span");

        for (i = 0; i < tables.length; i++)
        {
            if (tables[i].id == objTable)
            {
                tables[i].style.display = (Browser.isIE) ? "block" : "table";
            }
            else
            {
                tables[i].style.display = "none";
            }
        }
        for (i = 0; spans.length; i++)
        {
            if (spans[i].className == "tab-front")
            {
                spans[i].className = "tab-back";
                obj.className = "tab-front";
                break;
            }
        }
    }
}

function showNotice(objId){
    var obj = document.getElementById(objId);

    if (obj)
    {
        if (obj.style.display != "block")
        {
            obj.style.display = "block";
        }
        else
        {
            obj.style.display = "none";
        }
    }
}

function searchGoods(){
    var elements  = document.forms['theForm'].elements;
    var filters   = new Object;

    filters.cat_id = elements['cat_id'].value;
    filters.brand_id = elements['brand_id'].value;
    filters.keyword = Utils.trim(elements['keyword'].value);

    sz.loadOptions('get_goods_list', filters);
}


/**
 * 选取上级分类时判断选定的分类是不是底层分类
 */
function catChanged(){
    var obj = document.forms['theForm'].elements['article_cat'];
    cat_type = obj.options[obj.selectedIndex].getAttribute('cat_type');
    if ((obj.selectedIndex > 0) && (cat_type == 1000 || cat_type == 2000 || cat_type == 3000)){
        alert(not_allow_add);
        obj.selectedIndex = 0;
        return false;
    }

    $('#cat_type').val(cat_type);
    if(cat_type == 3001 || cat_type == 3002 || cat_type == 3003 || cat_type == 3004){
        $('.nothelp').hide();
    }else{
        $('.nothelp').show();
    }
    return true;
}

</script>

<?php echo $this->fetch('pagefooter.htm'); ?>