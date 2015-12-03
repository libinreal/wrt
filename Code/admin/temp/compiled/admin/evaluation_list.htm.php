<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js,../js/WebCalendar.js')); ?>
<!-- 商品搜索 -->
<div class="form-div">
    <form action="javascript:searchEvaluation()" name="searchForm" >
        <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
        <select name="status" >
            <option value="0"><?php echo $this->_var['lang']['all_state']; ?></option>
            <option value="1"><?php echo $this->_var['lang']['info_submitted']; ?></option>
            <option value="2"><?php echo $this->_var['lang']['info_accepted']; ?></option>
            <option value="3"><?php echo $this->_var['lang']['info_passed']; ?></option>
            <option value="4"><?php echo $this->_var['lang']['successed_evaluation']; ?></option>
            <option value="5"><?php echo $this->_var['lang']['failed_evaluation']; ?></option>
        </select>
        <?php echo $this->_var['lang']['username']; ?><input type="text" name="keyword" id="keyword" />
        <?php echo $this->_var['lang']['starttime']; ?><input type="text" maxlength="100" name="starttime" size="18"  onclick="SelectDate(this,'yyyy-MM-dd hh:mm',0,-60)" readonly="true" style="cursor:pointer" />
        <?php echo $this->_var['lang']['endtime']; ?><input type="text" maxlength="100" name="endtime" size="18"  onclick="SelectDate(this,'yyyy-MM-dd hh:mm',0,-60)" readonly="true" style="cursor:pointer" />
        <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" />
    </form>
</div>
<?php endif; ?>
<form method="POST" action="" name="listForm">
    <div class="list-div" id="listDiv">
        <table cellspacing='1' cellpadding='3' id='list-table'>
            <tr>
                <th><a href="javascript:listTable.sort('id'); "><?php echo $this->_var['lang']['id']; ?></a><?php echo $this->_var['sort_id']; ?></th>
                <th><?php echo $this->_var['lang']['username']; ?></th>
                <th><?php echo $this->_var['lang']['nature']; ?></th>
                <th><?php echo $this->_var['lang']['money']; ?></th>
                <th><a href="javascript:listTable.sort('foundedDate'); "><?php echo $this->_var['lang']['foundedDate']; ?></a><?php echo $this->_var['sort_foundedDate']; ?></th>
                <th><?php echo $this->_var['lang']['amountLimit']; ?></th>
                <th><?php echo $this->_var['lang']['use']; ?></th>
                <th><?php echo $this->_var['lang']['businessCode']; ?></th>
                <th><?php echo $this->_var['lang']['taxcode']; ?></th>
                <th><?php echo $this->_var['lang']['orgcode']; ?></th>
                <th><a href="javascript:listTable.sort('createAt'); "><?php echo $this->_var['lang']['createAt']; ?></a><?php echo $this->_var['sort_createAt']; ?></th>
                <th><?php echo $this->_var['lang']['evaluation_handler']; ?></a></th>
                <th><?php echo $this->_var['lang']['handler']; ?></th>
            </tr>
            <?php $_from = $this->_var['evaluation_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
            <tr>
                <td align="center"><span><?php echo $this->_var['list']['id']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['user_name']; ?></span></td>
                <td align="center"><span><?php echo htmlspecialchars($this->_var['list']['nature']); ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['money']; ?></span></td>
                <td align="center"><span><?php echo $this->_var['list']['foundedDate']; ?></span></td>
                <td align="left" style="padding-left: 5px;"><span><?php echo $this->_var['list']['amountLimit']; ?></span></td>
                <td align="left" style="padding-left: 5px;"><span><?php echo $this->_var['list']['use']; ?></span></td>
                <td style="text-align: center" class="txtCodeImg">
                    <span><?php echo $this->_var['list']['businessCode']; ?></span>
                    <div class="businessCode" style="display: none;">
                        <img src="<?php echo $this->_var['list']['businessLicense']; ?>" width="200px" height="200px"  alt=""/>
                    </div>
                </td>
                <td style="text-align: center" class="txtCodeImg">
                    <span><?php echo $this->_var['list']['taxCode']; ?></span>
                    <div class="taxCode" style="display: none;">
                        <img src="<?php echo $this->_var['list']['taxcert']; ?>" width="200px" height="200px" alt=""/>
                    </div>
                </td>
                <td align="center" class="txtCodeImg">
                    <span><?php echo $this->_var['list']['orgCode']; ?></span>
                    <div class="orgCode" style="display: none;"><img src="<?php echo $this->_var['list']['orgcert']; ?>" width="200px" height="200px"  alt=""/></div>
                </td>
                <td align="center" style="width:100px;"><span><?php echo $this->_var['list']['createAt']; ?></span></td>
                <td align="center" id="state_<?php echo $this->_var['list']['id']; ?>">
                    <span>
                        <?php if ($this->_var['list']['status'] == 1): ?>
                            <a href="javascript:void(0);" id="state3_success" class="state_a" old_state="<?php echo $this->_var['list']['status']; ?>" listid="<?php echo $this->_var['list']['id']; ?>" final=""><?php echo $this->_var['lang']['accept_evaluation']; ?></a>
                            <a href="javascript:void(0);" id="state3_fail"  class="state_a" old_state="<?php echo $this->_var['list']['status']; ?>" listid="<?php echo $this->_var['list']['id']; ?>" final=""></a>
                        <?php elseif ($this->_var['list']['status'] == 2): ?>
                            <a href="javascript:void(0);" id="state3_success" class="state_a" old_state="<?php echo $this->_var['list']['status']; ?>" listid="<?php echo $this->_var['list']['id']; ?>" final=""><?php echo $this->_var['lang']['passed_evaluation']; ?></a>
                            <a href="javascript:void(0);" id="state3_fail" class="state_a" old_state="<?php echo $this->_var['list']['status']; ?>" listid="<?php echo $this->_var['list']['id']; ?>" final=""></a>
                        <?php elseif ($this->_var['list']['status'] == 3): ?>
                            <a href="javascript:void(0);" class="state_a" old_state="<?php echo $this->_var['list']['status']; ?>" listid="<?php echo $this->_var['list']['id']; ?>" final="success"><?php echo $this->_var['lang']['success_evaluation']; ?></a>&nbsp;
                            <a href="javascript:void(0);" class="state_a" old_state="<?php echo $this->_var['list']['status']; ?>" listid="<?php echo $this->_var['list']['id']; ?>" final="fail"><?php echo $this->_var['lang']['fail_evaluation']; ?></a>
                        <?php elseif ($this->_var['list']['status'] == 4): ?>
                            <?php echo $this->_var['lang']['successed_evaluation']; ?>
                        <?php elseif ($this->_var['list']['status'] == 5): ?>
                            <?php echo $this->_var['lang']['failed_evaluation']; ?>
                        <?php endif; ?>
                    </span>
                </td>
                <td align="center" nowrap="true">
                    <span>
                        <a href="evaluation.php?act=info&id=<?php echo $this->_var['list']['id']; ?>" title="<?php echo $this->_var['lang']['view']; ?>"><?php echo $this->_var['lang']['check_info']; ?></a>&nbsp;
                    </span>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td class="no-records" colspan="13"><?php echo $this->_var['lang']['no_evaluation']; ?></td></tr>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <tr>
                <td align="right" nowrap="true" colspan="13"><?php echo $this->fetch('page.htm'); ?></td>
            </tr>
        </table>
        <?php if ($this->_var['full_page']): ?>
    </div>
</form>
<div class="modal" id="zj-modal" style="display: none;">
    <div class="modal-bg">
        <div class="modal-head">
            <a class="modal-return" href="javascript:;"><</a>
            <div class="modal-title">备注</div>
            <a class="modal-close" href="javascript:;">X</a>
        </div>
        <div class="modal-content">
            <div class="modal-tip"><?php echo $this->_var['lang']['addremark']; ?></div>
            <div class="modal-form"><textarea name="remark" id="remark" cols="40" rows="5" ></textarea></div>
        </div>
        <div class="modal-footer modal-buttons">
            <a href="#" class="button btn-confirm">确认</a>
            <a href="#" class="button btn-cancel">取消</a>
        </div>
    </div>
</div>
<div id="zj-modal-bg" class="panel-bg"></div>
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
        var listid,old_state,remark='',final='';
        $('.state_a').click(function(){
            listid = $(this).attr('listid');
            old_state = $(this).attr('old_state');
            final = $(this).attr('final');
            if(old_state == 1 || old_state == 2){
                update_state(listid,remark);
            }
            else if(old_state == 3){
                $('#zj-modal').show();
                $('#zj-modal-bg').show();
            }
        });
        $('.btn-confirm').click(function(){
            $('#zj-modal').hide();
            $('#zj-modal-bg').hide();
            var remark = $('#remark').val();
            update_state(listid,remark);
        });
        $('.btn-cancel, .modal-close').click(function(){
            $('#remark').val('');
            $('#zj-modal').hide();
            $('#zj-modal-bg').hide();
        });
        function update_state(id,remark){
            $.ajax({
                'type':'post',
                'dataType':'text',
                'data':{id:id,remark:remark,old_state:old_state,final:final},
                'url':'evaluation.php?act=state',
                'success':function(msg){
                    msg = $.parseJSON(msg);
                    switch(msg.status){
                        case '2':
                            $('#state_'+id+' a[id="state3_success"]' ).text('<?php echo $this->_var['lang']['passed_evaluation']; ?>');
                            $('#state_'+id+' a[id="state3_success"]' ).attr('old_state',msg.status);
                            $('#state_'+id+' a[id="state3_fail"]' ).attr('old_state',msg.status);
                            break;
                        case '3':
                            $('#state_'+id+' a[id="state3_success"]' ).text('<?php echo $this->_var['lang']['success_evaluation']; ?>');
                            $('#state_'+id+' a[id="state3_fail"]' ).text('<?php echo $this->_var['lang']['fail_evaluation']; ?>');
                            $('#state_'+id+' a[id="state3_success"]').attr('old_state',msg.status).attr('final','success');
                            $('#state_'+id+' a[id="state3_fail"]' ).attr('old_state',msg.status).attr('final','fail');
                            break;
                        case '4':
                            $('#state_'+id).text('<?php echo $this->_var['lang']['successed_evaluation']; ?>');
                            $('#remark').val('');
                            break;
                        case '5':
                            $('#state_'+id).text('<?php echo $this->_var['lang']['failed_evaluation']; ?>');
                            $('#remark').val('');
                            break;
                    }
                }
            });
        }
    });

    $(document).ready(function(){
        $('#list-table tr td').hover(function(){
            $(this).css('cursor','pointer');
            $(this).find('.businessCode').show();
        },function(){
            $(this).find('.businessCode').hide();
        });
        $('#list-table tr td').hover(function(){
            $(this).css('cursor','pointer');
            $(this).find('.taxCode').show();
        },function(){
            $(this).find('.taxCode').hide();
        });
        $('#list-table tr td').hover(function(){
            $(this).css('cursor','pointer');
            $(this).find('.orgCode').show();
        },function(){
            $(this).find('.orgCode').hide();
        });
    });
    
    function searchEvaluation()
    {
        listTable.filter.keyword = Utils.trim(document.forms['searchForm'].elements['keyword'].value);
        listTable.filter.starttime = Utils.trim(document.forms['searchForm'].elements['starttime'].value);
        listTable.filter.endtime = Utils.trim(document.forms['searchForm'].elements['endtime'].value);
        listTable.filter.status = parseInt(document.forms['searchForm'].elements['status'].value);
        listTable.filter.page = 1;
        listTable.loadList();
    }

</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>
