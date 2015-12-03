<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<!-- 催单列表 -->
<form method="post" action="" name="listForm">
    <div class="list-div" id="listDiv">
        <?php endif; ?>
        <table cellpadding="3" cellspacing="1">
            <tr>
                <th><?php echo $this->_var['lang']['order_sn']; ?></th>
                <th><?php echo $this->_var['lang']['contract_sn']; ?></th>
                <th><?php echo $this->_var['lang']['order_time']; ?></th>
                <th><?php echo $this->_var['lang']['consignee']; ?></th>
                <th><?php echo $this->_var['lang']['total_fee']; ?></th>
                <th><?php echo $this->_var['lang']['all_status']; ?></th>
            <tr>
                <?php $_from = $this->_var['order_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('okey', 'order');if (count($_from)):
    foreach ($_from AS $this->_var['okey'] => $this->_var['order']):
?>
            <tr>
                <td valign="top" nowrap="nowrap">
                    <a href="order.php?act=info&order_id=<?php echo $this->_var['order']['order_id']; ?>" id="order_<?php echo $this->_var['okey']; ?>"><?php echo $this->_var['order']['order_sn']; ?></a></td>
                <td><?php echo empty($this->_var['order']['contract_sn']) ? 'N/A' : $this->_var['order']['contract_sn']; ?></td>
                <td><?php echo htmlspecialchars($this->_var['order']['buyer']); ?><br /><?php echo $this->_var['order']['short_order_time']; ?></td>
                <td align="left"><a href="mailto:<?php echo $this->_var['order']['email']; ?>"> <?php echo htmlspecialchars($this->_var['order']['consignee']); ?></a><?php if ($this->_var['order']['tel']): ?> [TEL: <?php echo htmlspecialchars($this->_var['order']['tel']); ?>]<?php endif; ?> <br /><?php echo htmlspecialchars($this->_var['order']['address']); ?></td>
                <td align="right" valign="top" nowrap="nowrap"><?php echo $this->_var['order']['formated_total_fee']; ?></td>
                <td align="center" valign="top" nowrap="nowrap"><?php echo $this->_var['lang']['os'][$this->_var['order']['order_status']]; ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td class="no-records" colspan="10"><?php echo $this->_var['lang']['no_orders']; ?></td></tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </table>
        <!-- 分页 -->
        <table id="page-table" cellspacing="0">
            <tr>
                <td align="right" nowrap="true">
                    <?php echo $this->fetch('page.htm'); ?>
                </td>
            </tr>
        </table>
        <?php if ($this->_var['full_page']): ?>
    </div>
</form>
<script language="JavaScript">
listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
listTable.pageCount = <?php echo $this->_var['page_count']; ?>;
<?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

onload = function()
{
    // 开始检查订单
    startCheckOrder();
}

/**
 * 搜索订单
 */
function searchOrder()
{
    listTable.filter['order_sn'] = Utils.trim(document.forms['searchForm'].elements['order_sn'].value);
    listTable.filter['consignee'] = Utils.trim(document.forms['searchForm'].elements['consignee'].value);
    listTable.filter['composite_status'] = document.forms['searchForm'].elements['status'].value;
    listTable.filter['page'] = 1;
    listTable.loadList();
}
/**
 * 显示订单商品及缩图
 */
var show_goods_layer = 'order_goods_layer';
var goods_hash_table = new Object;
var timer = new Object;

/**
 * 绑定订单号事件
 *
 * @return void
 */
function bind_order_event()
{
    var order_seq = 0;
    while(true)
    {
        var order_sn = Utils.$('order_'+order_seq);
        if (order_sn)
        {
            order_sn.onmouseover = function(e)
            {
                try
                {
                    window.clearTimeout(timer);
                }
                catch(e)
                {
                }
                var order_id = Utils.request(this.href, 'order_id');
                show_order_goods(e, order_id, show_goods_layer);
            }
            order_sn.onmouseout = function(e)
            {
                hide_order_goods(show_goods_layer)
            }
            order_seq++;
        }
        else
        {
            break;
        }
    }
}

listTable.listCallback = function(result, txt)
{
    if (result.error > 0)
    {
        alert(result.message);
    }
    else
    {
        try
        {
            document.getElementById('listDiv').innerHTML = result.content;
            bind_order_event();
            if (typeof result.filter == "object")
            {
                listTable.filter = result.filter;
            }
            listTable.pageCount = result.page_count;
        }
        catch(e)
        {
            alert(e.message);
        }
    }
}
/**
 * 浏览器兼容式绑定Onload事件
 *
 */
if (Browser.isIE)
{
    window.attachEvent("onload", bind_order_event);
}
else
{
    window.addEventListener("load", bind_order_event, false);
}

/**
 * 建立订单商品显示层
 *
 * @return void
 */
function create_goods_layer(id)
{
    if (!Utils.$(id))
    {
        var n_div = document.createElement('DIV');
        n_div.id = id;
        n_div.className = 'order-goods';
        document.body.appendChild(n_div);
        Utils.$(id).onmouseover = function()
        {
            window.clearTimeout(window.timer);
        }
        Utils.$(id).onmouseout = function()
        {
            hide_order_goods(id);
        }
    }
    else
    {
        Utils.$(id).style.display = '';
    }
}

/**
 * 显示订单商品数据
 *
 * @return void
 */
function show_order_goods(e, order_id, layer_id)
{
    create_goods_layer(layer_id);
    $layer_id = Utils.$(layer_id);
    $layer_id.style.top = (Utils.y(e) + 12) + 'px';
    $layer_id.style.left = (Utils.x(e) + 12) + 'px';
    if (typeof(goods_hash_table[order_id]) == 'object')
    {
        response_goods_info(goods_hash_table[order_id]);
    }
    else
    {
        $layer_id.innerHTML = loading;
        Ajax.call('order.php?is_ajax=1&act=get_goods_info&order_id='+order_id, '', response_goods_info , 'POST', 'JSON');
    }
}

/**
 * 隐藏订单商品
 *
 * @return void
 */
function hide_order_goods(layer_id)
{
    $layer_id = Utils.$(layer_id);
    window.timer = window.setTimeout('$layer_id.style.display = "none"', 500);
}

/**
 * 处理订单商品的Callback
 *
 * @return void
 */
function response_goods_info(result)
{
    if (result.error > 0)
    {
        alert(result.message);
        hide_order_goods(show_goods_layer);
        return;
    }
    if (typeof(goods_hash_table[result.content[0].order_id]) == 'undefined')
    {
        goods_hash_table[result.content[0].order_id] = result;
    }
    Utils.$(show_goods_layer).innerHTML = result.content[0].str;
}
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>