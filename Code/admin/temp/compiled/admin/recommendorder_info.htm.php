<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'topbar.js,../js/utils.js,listtable.js,selectzone.js,../js/common.js,WebCalendar.js')); ?>
<div class="main-div">
    <form action="recommendorder.php" method="post" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
        <table width="100%" id="general-table">
            <tr>
                <td class="label"><?php echo $this->_var['lang']['conFnum']; ?><span class="require-field">&nbsp;</span></td>
                <td>
                    <?php echo $this->_var['contract_info']['conFnum']; ?>
                    <input type="hidden" name="conFnum" value="<?php echo $this->_var['contract_info']['conFnum']; ?>" />
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['m_user']; ?><span class="require-field">*</span></td>
                <td>
                    <select name="cusFnum" class="cusfnum" style="width: 215px">
                        <option value="" ><?php echo $this->_var['lang']['user']; ?></option>
                        <!--<?php $_from = $this->_var['userInfo']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>-->
                        <option value="<?php echo $this->_var['list']['customNo']; ?>" ><?php echo $this->_var['list']['contacts']; ?></option>
                        <!--<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>-->
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['prjNum']; ?><span class="require-field">*</span></td>
                <td>
                    <input type='text' name='prjNum' class="prjNum" value='' size="30"/>
                    <span><?php echo $this->_var['lang']['prjnumnotice']; ?></span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['conAmt']; ?><span class="require-field">*</span></td>
                <td>
                    <input type='text' name='conAmt' class="conAmt" value="<?php echo $this->_var['contract_info']['conAmt']; ?>" size="30"  />
                    <span><?php echo $this->_var['lang']['Amtnotice']; ?></span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['prjName']; ?><span class="require-field">*</span></td>
                <td>
                    <input type='text' name='prjName' class="prjName" size="30" />
                    <span><?php echo $this->_var['lang']['prjNamenotice']; ?></span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['enddate']; ?><span class="require-field">*</span></td>
                <td>
                    <input type="text" name="endDate" value="<?php echo $this->_var['contract_info']['endDate']; ?>" onclick="SelectDate(this,'yyyy-MM-dd')" readonly="true" style="width:165px;cursor:pointer" name="send_number" />
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['label_category']; ?><span class="require-field">*</span></td>
                <td>
                    <select name="category" id="category" onchange="get_category_goods();">
                        <?php $_from = $this->_var['contract_info']['Mats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
                        <option value="<?php echo $this->_var['list']['matGroupFnum']; ?>"><?php echo $this->_var['list']['matGroupName']; ?></option>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['goodssn']; ?><span class="require-field">*</span></td>
                <td id="r_goodssn" style="width: 100%">

                    <table class="tab-info">
                        <tr>
                            <td>操作</td>
                            <td>商品名称</td>
                            <td>Vip价格</td>
                            <td>库存</td>
                            <td>厂商名称</td>
                            <td>销售区域</td>
                            <td>供货商</td>
                        </tr>
                        <!-- <?php $_from = $this->_var['goodsInfo']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?> <?php if ($this->_var['list']['goods_id']): ?> -->
                        <tr>
                            <td><input type="checkbox" class="send" name="goodsId[]" data-goods="<?php echo $this->_var['list']['wcode']; ?>" value="<?php echo $this->_var['list']['goods_id']; ?>" /><?php echo $this->_var['list']['goods_id']; ?></td>
                            <td><?php echo $this->_var['list']['goods_name']; ?></td>
                            <td><?php echo $this->_var['list']['shop_price']; ?></td>
                            <td><?php echo empty($this->_var['list']['goods_number']) ? 'N/A' : $this->_var['list']['goods_number']; ?></td>
                            <td><?php echo empty($this->_var['list']['brand_name']) ? '--' : $this->_var['list']['brand_name']; ?></td>
                            <td><?php echo empty($this->_var['list']['region_name']) ? '--' : $this->_var['list']['region_name']; ?></td>
                            <td><?php echo empty($this->_var['list']['suppliers_name']) ? '--' : $this->_var['list']['suppliers_name']; ?></td>
                        </tr>
                        <!-- <?php endif; ?><?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> -->
                    </table>
                    <span style="display: block;height: 25px;line-height: 25px;width: 30%;float: left;overflow: hidden;">
                    </span>

                </td>
            </tr>
            <tr>
                <td class="label">&nbsp;</td>
                <td>
                    <input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button send" />
                    <input type="reset" value="<?php echo $this->_var['lang']['button_reset']; ?>" class="button" />
                    <input type="hidden" name="act" value="<?php echo $this->_var['form_act']; ?>" />
                </td>
            </tr>
        </table>
    </form>
</div>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,validator.js')); ?>
<script language="JavaScript">
    <!--
    
    $(document).ready(function() {
        $("#category option").eq(0).attr('selected',true);
    });

    function get_category_goods() {
        var msg = $("#category").val();
        Ajax.call('recommendorder.php?act=recommendgoods&cat_code=' + msg, '', GoodsResponse, 'POST', 'JSON');
    }
    function GoodsResponse(msg) {
        var i = 0;
        var len = msg.length;
        var html = "<table class='tab-info'><tr><td>操作</td><td>商品名称</td><td>Vip价格</td><td>库存</td><td>厂商名称</td><td>销售区域</td><td>供货商</td></tr>";
        for(i;i<len;i++) {
            if(msg[i].brand_name == null) msg[i].brand_name = '--';
            if(msg[i].suppliers_name == null) msg[i].suppliers_name = '--';
            html += '<tr>';
            html += '<td><input type="checkbox" class="send" name="goodsId[]" value="'+msg[i].goods_id+'" />'+msg[i].goods_id+'</td>'
            html += '<td>'+msg[i].goods_name+'</td><td>'+msg[i].shop_price+'</td>';
            html += '<td>'+msg[i].goods_number+'</td><td>'+msg[i].brand_name+'</td><td>'+msg[i].region_name+'</td><td>'+msg[i].suppliers_name+'</td>'
            html += '</tr>'
            //alert(msg[i].goods_id+'--'+msg[i].goods_number);
        }
        html +="</table>";
        document.getElementById('r_goodssn').innerHTML = html;
    }
    function validate()
    {
        if ($('.cusfnum').val() == '') {
            alert(cusFnum_empty);
            return false;
        }
        if ($('.prjNum').val() == '') {
            alert(prjNum_empty);
            return false;
        }
        if ($('.conAmt').val() == '') {
            alert(conAmt_empty);
            return false;
        }
        if ($('.prjName').val() == '') {
            alert(prjName_empty);
            return false;
        }
        var goodssn = document.getElementById('r_goodssn').getElementsByTagName('input');
        var len = goodssn.length;
        var i= 0,count=false;
        for(i;i<len;i++) {
            if(document.getElementById('r_goodssn').getElementsByTagName('input')[i].checked){
                count = true;
                break;
            }
        }
        if(!count){
            alert(no_goodssn);
            return false;
        }
        return true;
    }
    /**
     * 检查表单输入的数据
     */
    onload = function()
    {
        // 开始检查订单
        startCheckOrder();
    }
    
    //-->
</script>
        <style type="text/css">
            .tab-info {
                white-space: normal;border: none; border-collapse: collapse;overflow: hidden;
            }
            .tab-info tr td {
                line-height: 15px;border-bottom: 1px solid #cccccc;
            }
        </style>
<?php echo $this->fetch('pagefooter.htm'); ?>