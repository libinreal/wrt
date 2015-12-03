<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'topbar.js,../js/utils.js,listtable.js,selectzone.js,../js/common.js,WebCalendar.js')); ?>
<form action="order.php?act=split_save" method="post" name="theForm" onsubmit="return fnValdate();">
    <div class="list-div" style="margin-bottom: 5px">
        <table width="100%" cellpadding="3" cellspacing="1">
            <tr>
                <th colspan="7"><?php echo $this->_var['lang']['base_info']; ?></th>
            </tr>
            <tr>
                <td><?php echo $this->_var['lang']['order_sn']; ?></td>
                <td><?php echo $this->_var['order']['order_sn']; ?></td>
                <td colspan="5"><?php echo $this->_var['lang']['goods_info']; ?></td>
            </tr>
            <tr>
                <th><?php echo $this->_var['lang']['goods_name']; ?></th>
                <th><?php echo $this->_var['lang']['goods_wcode']; ?></th>
                <th><?php echo $this->_var['lang']['goods_suppliers']; ?></th>
                <th><?php echo $this->_var['lang']['goods_price']; ?></th>
                <th><?php echo $this->_var['lang']['goods_number']; ?></th>
                <th><?php echo $this->_var['lang']['goods_measure_unit']; ?></th>
                <th><?php echo $this->_var['lang']['goods_delivery']; ?></th>
            </tr>
            <tr>
                <td><?php echo $this->_var['goods']['goods_name']; ?></td>
                <td class="goods-info"><?php echo $this->_var['goods']['wcode']; ?></td>
                <td class="goods-info"><?php echo $this->_var['goods']['suppliers_name']; ?></td>
                <td class="goods-info"><?php echo $this->_var['goods']['formated_goods_price']; ?></td>
                <td class="goods-info"><?php echo $this->_var['goods']['goods_number']; ?></td>
                <td class="goods-info"><?php echo $this->_var['goods']['measure_unit']; ?></td>
                <td class="goods-info"><?php echo $this->_var['goods']['send_number']; ?></td>
            </tr>
        </table>
        <table id="add_goods" class="add-list">
            <tr>
                <td><?php echo $this->_var['lang']['goods_split_add']; ?></td>
            </tr>
            <tr>
                <td>
                    <span class="td1" onclick="addRow(this);">[+]</span>
                    <span class="td2">订单编号：</span>
                    <span class="td3"><?php echo $this->_var['childer_order_sn']; ?><input type="hidden" name="order_sn[]" value="<?php echo $this->_var['childer_order_sn']; ?>"/></span>
                    <span class="td2">供货商：</span>
                    <span class="td4">
                        <select name="suppers_id[]" id="">
                            <?php $_from = $this->_var['suppliers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'supp');$this->_foreach['no'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['no']['total'] > 0):
    foreach ($_from AS $this->_var['supp']):
        $this->_foreach['no']['iteration']++;
?>
                            <option value="<?php echo $this->_var['supp']['suppliers_id']; ?>"><?php echo $this->_var['supp']['suppliers_name']; ?></option>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </select>
                    </span>
                    <span class="td2">单价：</span>
                    <span class="td5"><input type="text" value="<?php echo $this->_var['goods']['goods_price']; ?>" onblur="fnGoodsPrice(this);" name="goods_price[]"/></span>
                    <span class="td2">数量：</span>
                    <span class="td5"><input type="text" value="" onblur="fnNumber(this);" class="send_number" name="send_number[]"/></span>
                    <span class="td2">到货时间：</span>
                    <span class="td5"><input type="text" name="best_time[]" value="<?php echo $this->_var['dataTime']; ?>" onclick="SelectDate(this,'yyyy-MM-dd')" readonly="true" style="width:165px;cursor:pointer" name="send_number"/></span>
                    <span style="color: #ff0000;padding-left: 10px;">*</span>
                </td>
            </tr>
        </table>
        <div style="text-align: center;">
            <input name="order_id" type="hidden" value="<?php echo $this->_var['order']['order_id']; ?>"/>
            <input name="goods_id" type="hidden" value="<?php echo $this->_var['goods']['goods_id']; ?>"/>
            <input name="parent_order_sn" type="hidden" value="<?php echo $this->_var['order']['order_sn']; ?>"/>
            <input type="submit" value="保存" class="button"/>
        </div>
    </div>
</form>


<style type="text/css">
    .goods-info {
        text-align: center;
    }
    .add-list tr td span {
        display: block;float: left;
    }
    .td1 {
        width: 80px;text-align: center;color: #ff0000;
    }
    .td2 {margin: 0 10px;text-align: center;}
</style>
<script language="JavaScript">
    
    var oldAgencyId = <?php echo empty($this->_var['order']['agency_id']) ? '0' : $this->_var['order']['agency_id']; ?>;
    var goods_number = <?php echo $this->_var['goods']['goods_number']; ?>;
    var sendNumber = <?php echo $this->_var['goods']['send_number']; ?>;
    var goodsPrice = <?php echo $this->_var['goods']['goods_price']; ?>;
    
    var num = parseInt(goods_number) - parseInt(sendNumber);
    $(document).ready(function(){
        $('.send_number').val(num);
    });
    window.onload= function(){
    }
    function addRow(obj) {
        
        var childer_order_sn = '<?php echo $this->_var['childer_order_sn']; ?>'
        
        var order = childer_order_sn.split('-');
        var src  = obj.parentNode.parentNode;
        var idx  = rowindex(src);
        var tbl  = document.getElementById('add_goods');
        var len = tbl.getElementsByTagName('tr').length;
        var row  = tbl.insertRow(-1);
        var cell = row.insertCell(0);
        var orderNum = parseInt(order[1])+len-1;
        var newOrderSn = ""+order[0]+'-'+orderNum+"";
        var str = src.cells[0].innerHTML.replace(/(.*)(addRow)(.*)(\[)(\+)/i, "$1removeRow$3$4-");
        str = str.replace(/(\>|\")(\d+\-\d+)(\<|\")/g, "$1"+newOrderSn+"$3");
        cell.innerHTML = str;
        var splitNum = parseInt(num) / (len-1);
        splitNum = Math.floor(splitNum);
        $('.send_number').val(parseInt(splitNum));
    }
    function removeRow(obj)
    {
        var row = rowindex(obj.parentNode.parentNode);
        var tbl = document.getElementById('add_goods');
        var len = tbl.rows.length-1;
        var splitNum = parseInt(num) / (len-1);
        splitNum = Math.floor(splitNum);
        $('.send_number').val(parseInt(splitNum));
        if(len === row) {
            tbl.deleteRow(row);
        } else {
            alert('不是最后一行，不能删除');
        }
    }
    function fnGoodsPrice(obj) {
        var re=/^[0-9]+(.[0-9]{2})?$/;
        if(!re.test(obj.value)) {
            alert('只能输入价格格式的内容其中最多包含两位小数');
            obj.value = goodsPrice;
        }
    }
    function fnNumber(obj) {
        var reg = /^([1-9][0-9]*)$/;
        if(!reg.test(obj.value)) {
            alert('只能输入大于0的数字');
            obj.value = '1';
        }
    }
    function fnValdate() {
       var send_number = getByClass('send_number');
        var i=0;
        var sumSendNum = 0;
        for(i;i<send_number.length;i++) {
            sumSendNum = sumSendNum + parseInt(send_number[i].value);

        }
        var SUNSPLIT = goods_number - sendNumber;
        if(sumSendNum > SUNSPLIT) {
            alert('填写数量大于可拆分数量');
            return false;
        }
        return true;
    }
    function getByClass(sClass,oParent)
    {
        if(!oParent){
            oParent=document;
        }
        var aEle=oParent.getElementsByTagName('*');
        var aResult=[];
        var i=0;

        for(i=0;i<aEle.length;i++)
        {
            if(aEle[i].className==sClass)
            {
                aResult.push(aEle[i]);
            }
        }

        return aResult;
    }
    onload = function()
    {
        // 开始检查订单
        startCheckOrder();
    }
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>