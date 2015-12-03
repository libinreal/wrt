<!-- $Id: article_info.htm 2014-09-03 xy $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,selectzone.js,validator.js')); ?>
<!-- start goods form -->
<div class="tab-div">
    <div id="tabbar-div">
            <span class="tab-back" id="detail-tab"><?php echo $this->_var['lang']['tab_content']; ?></span>
    </div>

    <div id="tabbody-div">
        <form  action="credit_intrinfo.php" method="post" enctype="multipart/form-data" id="myform" name="theForm" onsubmit="return validate()">
            <table width="90%" id="detail-table">
                <tr><td><?php echo $this->_var['FCKeditor']; ?></td></tr>
            </table>

            <div class="button-div">
                <input type="hidden" name="act" value="<?php echo $this->_var['form_action']; ?>" />
                <input type="hidden" name="id" value="<?php echo $this->_var['credit']['id']; ?>" />
                <input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button" />
                <input type="reset" value="<?php echo $this->_var['lang']['button_reset']; ?>" class="button" />
            </div>
        </form>
    </div>

</div>
<!-- end goods form -->
<script language="JavaScript">


onload = function()
{
    // 开始检查订单
    startCheckOrder();
}

function validate()
{
    return true;
}
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>