<!-- $Id: customize_list.htm 2014-09-02 xy $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<!-- 商品搜索 -->
<?php echo $this->fetch('customize_goods_search.htm'); ?>

<form method="POST" action="" name="listForm">
    <!-- start cat list -->
    <div class="list-div" id="listDiv">
        <?php endif; ?>

        <table cellspacing='1' cellpadding='3' id='list-table'>
            <tr>
                <th><a href="javascript:listTable.sort('id'); "><?php echo $this->_var['lang']['apply_id']; ?></a><?php echo $this->_var['sort_id']; ?></th>
                <th><?php echo $this->_var['lang']['goods_name']; ?></th>
                <th><?php echo $this->_var['lang']['goods_cat']; ?></th>
                <th><?php echo $this->_var['lang']['goods_price']; ?></th>
                <th><?php echo $this->_var['lang']['countNo']; ?></th>
                <th><?php echo $this->_var['lang']['goods_spec']; ?></th>
                <th><?php echo $this->_var['lang']['goods_model']; ?></th>
                <th><?php echo $this->_var['lang']['goods_unit']; ?></th>
                <th><?php echo $this->_var['lang']['area']; ?></th>
                <th><a href="javascript:listTable.sort('createAt'); "><?php echo $this->_var['lang']['createAt']; ?></a><?php echo $this->_var['sort_createAt']; ?></th>
                <th><a href="javascript:listTable.sort('expirationAt'); "><?php echo $this->_var['lang']['expirationAt']; ?></a><?php echo $this->_var['sort_expirationAt']; ?></th>
                <!--
                <th><?php echo $this->_var['lang']['apply_handler']; ?></a></th>
                -->
                <th><?php echo $this->_var['lang']['handler']; ?></th>
            </tr>
            <?php $_from = $this->_var['customize_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
            <tr>
                <td align="center"><span><?php echo $this->_var['list']['id']; ?></span></td>
                <td style="text-align: left;padding-left: 5px;" class="goodsname">
                    <span listid="<?php echo $this->_var['list']['id']; ?>"><?php echo htmlspecialchars($this->_var['list']['goodsName']); ?></span>
                    <div id="goodsimg_<?php echo $this->_var['list']['id']; ?>" class="goodsimg" style="display:none;">
                        <img src="<?php echo $this->_var['list']['thumb']; ?>" />
                    </div>
                </td>
                <td align="left"><span><?php echo htmlspecialchars($this->_var['list']['cat_name']); ?></span></td>
                <td style="text-align: right;padding-right: 5px;"><span><?php echo $this->_var['list']['goodsPrice']; ?></span></td>
                <td style="text-align: center;padding-right: 5px;"><?php echo $this->_var['list']['cusCount']; ?></td>
                <td align="left"><span><?php echo $this->_var['list']['goodsSpec']; ?></span></td>
                <td align="left"><span><?php echo $this->_var['list']['goodsModel']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['goodsUnit']; ?></span></td>
                <td align="left"><span><?php echo $this->_var['list']['region_name']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['createAt']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['expirationAt']; ?></span></td>
                <!--
                <td align="center" id="state_<?php echo $this->_var['list']['id']; ?>">
                    <span>
                        <?php if ($this->_var['list']['state'] == 0): ?>
                            <a href="javascript:void(0);" class="state_a" listid="<?php echo $this->_var['list']['id']; ?>"><?php echo $this->_var['lang']['accept_apply']; ?></a>
                        <?php elseif ($this->_var['list']['state'] == 1): ?>
                            <a href="javascript:void(0);" class="state_a" listid="<?php echo $this->_var['list']['id']; ?>"><?php echo $this->_var['lang']['success_apply']; ?></a>
                        <?php elseif ($this->_var['list']['state'] == 2): ?>
                            <?php echo $this->_var['lang']['apply_successed']; ?>
                        <?php endif; ?>
                    </span>
                </td>
                -->
                <td align="center" nowrap="true">
                    <span>
                        <a href="customize_info.php?act=info&applyId=<?php echo $this->_var['list']['id']; ?>" title="<?php echo $this->_var['lang']['view']; ?>"><?php echo $this->_var['lang']['applyinfo']; ?></a>&nbsp;
                    </span>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td class="no-records" colspan="12"><?php echo $this->_var['lang']['no_customize']; ?></td></tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <tr>
                <td align="right" nowrap="true" colspan="12"><?php echo $this->fetch('page.htm'); ?></td>
            </tr>
        </table>

        <?php if ($this->_var['full_page']): ?>
    </div>
</form>
<!-- end cat list -->
<script type="text/javascript" language="JavaScript">
    listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
    listTable.pageCount = <?php echo $this->_var['page_count']; ?>;

    <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
    listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

$(function(){
    $('.state_a').click(function(){
        var listid = $(this).attr('listid');
        update_state(listid);
    });
    function update_state(applyid){
        $.ajax({
            'type':'get',
            'dataType':'text',
            'url':'customize.php?act=state&applyid='+applyid,
            'success':function(msg){
                if(msg == 1){
                    $('#state_'+applyid+' a').text('<?php echo $this->_var['lang']['success_apply']; ?>');
                }else{
                    $('#state_'+applyid).text('<?php echo $this->_var['lang']['apply_successed']; ?>');
                }
            }
        });
    }

    $('#list-table tr td').hover(function(){
        $(this).css('cursor','pointer');
        $(this).find('.goodsimg').show();
    },function(){
        $(this).find('.goodsimg').hide();
    });
});


</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>
