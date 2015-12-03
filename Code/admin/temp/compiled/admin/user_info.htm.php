<?php echo $this->fetch('pageheader.htm'); ?>
<div class="main-div">
    <form method="post" action="users.php" name="theForm" onsubmit="return validate()">
        <input type="hidden" id="Action" name="act" value="<?php echo $this->_var['form_action']; ?>" />
        <input type="hidden" name="id" value="<?php echo $this->_var['user']['user_id']; ?>" />
        <table width="100%" >
            <tr>
                <td class="label"><?php echo $this->_var['lang']['username']; ?>:</td>
                <td>
                    <?php if ($this->_var['form_action'] == "update"): ?><?php echo $this->_var['user']['user_name']; ?><input type="hidden" name="username" value="<?php echo $this->_var['user']['user_name']; ?>" />
                    <?php else: ?><input type="text" name="username" id="username" onblur="fnUserName();" maxlength="60" value="<?php echo $this->_var['user']['user_name']; ?>" />
                    <span class="require-field" id="field-name">*</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if ($this->_var['form_action'] == "insert"): ?>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['password']; ?>:</td>
                <td>
                    <input type="password" id="password" name="password" maxlength="20" size="20" onblur="fnPassword();" />
                    <span class="require-field" id="field-password">*</span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['confirm_password']; ?>:</td>
                <td><input type="password" id="confirm-password" onblur="fnPassTo();" name="confirm_password" maxlength="20" size="20" />
                    <span class="require-field" id="field-confirm-password">*</span></td>
            </tr>
            <?php elseif ($this->_var['form_action'] == "update"): ?>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['newpass']; ?>:</td>
                <td>
                    <input type="password" id="password" name="password" maxlength="20" size="20" />
                    <span class="require-field" id="field-password"></span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['confirm_password']; ?>:</td>
                <td>
                    <input type="password" id="confirm-password" name="confirm_password" maxlength="20" size="20" />
                    <span class="require-field" id="field-confirm-password"></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['gender']; ?>:</td>
                <td><?php echo $this->html_radios(array('name'=>'sex','options'=>$this->_var['lang']['sex'],'checked'=>$this->_var['user']['sex'])); ?></td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['qq']; ?>:</td>
                <td>
                    <input name="qq" type="text" size="40" class="inputBg" value="<?php echo $this->_var['user']['qq']; ?>"/>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['weixin']; ?>:</td>
                <td>
                    <input name="weixin" type="text" size="40" class="inputBg" value="<?php echo $this->_var['user']['weixin']; ?>"/>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['companyName']; ?>:</td>
                <td>
                    <input name="companyName" id="companyName" onblur="fncompanyName();" type="text" size="40" class="inputBg" value="<?php echo $this->_var['user']['companyName']; ?>"/>
                    <span class="require-field" id="field-companyName">*</span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['department']; ?>:</td>
                <td>
                    <input name="department" id="department" onblur="fndepartment();" type="text" size="40" class="inputBg" value="<?php echo $this->_var['user']['department']; ?>"/>
                    <span class="require-field" id="field-department">*</span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['position']; ?>:</td>
                <td>
                    <input name="position" id="position" onblur="fnposition();" type="text" size="40" class="inputBg" value="<?php echo $this->_var['user']['position']; ?>"/>
                    <span class="require-field" id="field-position">*</span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['companyAddress']; ?>:</td>
                <td>
                    <input name="companyAddress" id="companyAddress" onblur="fncompanyAddress();" type="text" size="40" class="inputBg" value="<?php echo $this->_var['user']['companyAddress']; ?>"/>
                    <span class="require-field" id="field-companyAddress">*</span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['officePhone']; ?>:</td>
                <td>
                    <input name="officePhone" id="officePhone" onblur="fnofficePhone();" type="text" size="40" class="inputBg" value="<?php echo $this->_var['user']['officePhone']; ?>"/>
                    <span class="require-field" id="field-officePhone">*</span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['fax']; ?>:</td>
                <td>
                    <input name="fax" type="text" id="fax" onblur="fnfax();" size="40" class="inputBg" value="<?php echo $this->_var['user']['fax']; ?>"/>
                    <span class="require-field" id="field-fax">*</span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['credit_rank']; ?></td>
                <td>
                    <select id="rank" name="credit_rank" onchange="fnRank();">
                        <?php echo $this->html_options(array('name'=>'credit_rank','options'=>$this->_var['lang']['rank'],'selected'=>$this->_var['user']['credit_rank'])); ?>
                    </select><span class="require-field" id="field-rank">*</span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['contacts']; ?>:</td>
                <td>
                    <input name="contacts" id="contacts" onblur="fncontacts();" type="text" size="40" class="inputBg" value="<?php echo $this->_var['user']['contacts']; ?>"/>
                    <span class="require-field" id="field-contacts">*</span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['contactsPhone']; ?>:</td>
                <td>
                    <input name="contactsPhone" id="contactsPhone" onblur="fncontactsPhone();" type="text" size="40" class="inputBg" value="<?php echo $this->_var['user']['contactsPhone']; ?>"/>
                    <span class="require-field" id="field-contactsPhone">*</span>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['email']; ?>:</td>
                <td>
                    <input type="text" name="email" maxlength="60" size="40" value="<?php echo $this->_var['user']['email']; ?>" />
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['secondContacts']; ?>:</td>
                <td>
                    <input name="secondContacts" type="text" size="40" class="inputBg" value="<?php echo $this->_var['user']['secondContacts']; ?>"/>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['secondPhone']; ?>:</td>
                <td>
                    <input name="secondPhone" type="text" size="40" class="inputBg" value="<?php echo $this->_var['user']['secondPhone']; ?>"/>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['customLevel']; ?>:</td>
                <td id="customLevel">
                    <?php echo $this->html_radios(array('name'=>'customLevel','options'=>$this->_var['lang']['customLevelNo'],'checked'=>$this->_var['user']['customLevel'])); ?>
                </td>
            </tr>
            <tr id="user_privilege" style="display: none;">
                <td class="label"><?php echo $this->_var['lang']['user_privilege']; ?>:</td>
                <td>
                    <input type="checkbox" name="privilege[]" value="1" <?php if ($this->_var['user']['msn'] == '1'): ?> checked<?php endif; ?>/><?php echo $this->_var['lang']['privilege']['1']; ?>
                    <input type="checkbox" name="privilege[]" value="2" <?php if ($this->_var['user']['msn'] == '2'): ?> checked<?php endif; ?>/><?php echo $this->_var['lang']['privilege']['2']; ?>
                    <input type="checkbox" name="privilege[]" value="3" <?php if ($this->_var['user']['msn'] == '3'): ?> checked<?php endif; ?>/><?php echo $this->_var['lang']['privilege']['3']; ?>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['customNo']; ?>:</td>
                <td>
                    <input name="customNo" type="text" size="40" class="inputBg" value="<?php echo $this->_var['user']['customNo']; ?>"/>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['customerNo']; ?>:</td>
                <td>
                    <input name="customerNo" type="text" size="40" class="inputBg" value="<?php echo $this->_var['user']['customerNo']; ?>"/>
                </td>
            </tr>
            <tr>
                <td class="label"><?php echo $this->_var['lang']['customerAccount']; ?>:</td>
                <td>
                    <input name="customerAccount" type="text" size="40" class="inputBg" value="<?php echo $this->_var['user']['customerAccount']; ?>"/>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button" />
                    <input type="reset" value="<?php echo $this->_var['lang']['button_reset']; ?>" class="button" />    </td>
            </tr>
        </table>
    </form>
</div>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,validator.js')); ?>
<script language="JavaScript">
    
    $(document).ready(function(){
        var custVal = $("#customLevel").find("input:checked").val();
        $("#customLevel input").each(function(){
            $(this).click(function(){
                var Val = $(this).val();
                if(Val>0) {
                    $("#user_privilege").css('display',"");
                }else {
                    $("#user_privilege").css('display',"none");
                }
            });
        });
        if(custVal>0) {
            $("#user_privilege").css('display',"");
        }else {
            $("#user_privilege").css('display',"none");
        }
    });

    function fnUserName() {
        if($("#username").val()=='') {
            $('#field-name').html('用户名必须填写');
        }else {
            $('#field-name').html('*');
        }
    }

    function fnPassword() {
        if($("#password").val()=='') {
            $('#field-password').html('密码必须填写');
        }else if(/ /.test($("#password").val()) == true) {
            $('#field-password').html('密码中不能包含空格');
        } else if($("#password").val().length<6){
            $('#field-password').html('密码长度不能小于6位');
        }else {
            $('#field-password').html('*');
        }
    }
    function fnPassTo() {
        if($("#confirm-password").val()=='') {
            $("#field-confirm-password").html('确认密码必须填写');
        }else if($('#confirm-password').val()!=$("#password").val()){
            $("#field-confirm-password").html('密码和确认密码必须相同');
        }else {
            $('#field-confirm-password').html('*');
        }
    }
    function fncompanyName() {
        if($("#companyName").val()=='') {
            $("#field-companyName").html("单位名称必须填写");
        }else {
            $("#field-companyName").html("*");
        }

    }
    function fndepartment() {
        if($("#department").val()=='') {
            $("#field-department").html("部门名称必须填写");
        }else {
            $("#field-department").html("*");
        }
    }
    function fnposition() {
        if($("#position").val()=='') {
            $("#field-position").html("职位名称必须填写");
        } else {
            $("#field-position").html("*");
        }
    }
    function fncompanyAddress() {
        if($("#companyAddress").val()=='') {
            $("#field-companyAddress").html("单位地址必须填写");
        } else {
            $("#field-companyAddress").html("*");
        }
    }
    function fnofficePhone() {
        if($("#officePhone").val()=='') {
            $("#field-officePhone").html("办公电话必须填写");
        }else {
            $("#field-officePhone").html("*");
        }
    }
    function fnfax() {
        if($("#fax").val()=='') {
            $("#field-fax").html("办公传真必须填写");
        }else {
            $("#field-fax").html("*");
        }
    }
    function fncontacts() {
        if($("#contacts").val()=='') {
            $("#field-contacts").html("联系人必须填写");
        } else {
            $("#field-contacts").html("*");
        }
    }
    function fnRank() {
        var docRank = document.getElementById("rank");
        var objIndex = docRank.options.selectedIndex;
        var objVal = docRank.options[objIndex].value;
        if(objVal=='') {
            $("#field-rank").html("信用等级必须选择");
        } else {
            $("#field-rank").html("*");
        }
    }
    function fncontactsPhone() {
        if($("#contactsPhone").val()=='') {
            $("#field-contactsPhone").html("联系人电话必须填写");
        } else {
            $("#field-contactsPhone").html("*");
        }
    }

    <!--
    if (document.forms['theForm'].elements['act'].value == "insert")
    {
        document.forms['theForm'].elements['username'].focus();
    }
    else
    {
        document.forms['theForm'].elements['email'].focus();
    }
    onload = function()
    {
        // 开始检查订单
        startCheckOrder();
    }
    /**
     * 检查表单输入的数据
     */
    function validate()
    {
        
        <?php if ($this->_var['form_action'] == "insert"): ?>
        
        if($("#username").val()=='') {
            $('#field-name').html('用户名必须填写');
            return false;
        }
        if($("#password").val()=='') {
            $('#field-password').html('密码必须填写');
            return false;
        }else if(/ /.test($("#password").val()) == true) {
            $('#field-password').html('密码中不能包含空格');
            return false
        } else if($("#password").val().length<6){
            $('#field-password').html('密码长度不能小于6位');
            return false;
        }
        if($("#confirm-password").val()=='') {
            $("#field-confirm-password").html('确认密码必须填写');
            return false;
        }else if($('#confirm-password').val()!=$("#password").val()){
            $("#field-confirm-password").html('密码和确认密码必须相同');
            return false;
        }
        
        <?php endif; ?>
        <?php if ($this->_var['form_action'] == "update"): ?>
        
        if($("#password").val().length > 0) {
            if(/ /.test($("#password").val()) == true) {
                $('#field-password').html('密码中不能包含空格');
                return false
            } else if($("#password").val().length<6){
                $('#field-password').html('密码长度不能小于6位');
                return false;
            }
            //确认密码
            if($('#confirm-password').val()!=$("#password").val()){
                $("#field-confirm-password").html('密码和确认密码必须相同');
                return false;
            }
        }
        
        <?php endif; ?>
        
        if($("#companyName").val()=='') {
            $("#field-companyName").html("单位名称必须填写");
            return false;
        }
        if($("#department").val()=='') {
            $("#field-department").html("部门名称必须填写");
            return false;
        }
        if($("#position").val()=='') {
            $("#field-position").html("职位名称必须填写");
            return false;
        }
        if($("#companyAddress").val()=='') {
            $("#field-companyAddress").html("单位地址必须填写");
            return false;
        }
        if($("#officePhone").val()=='') {
            $("#field-officePhone").html("办公电话必须填写");
            return false;
        }
        if($("#fax").val()=='') {
            $("#field-fax").html("办公传真必须填写");
            return false;
        }
        var docRank = document.getElementById("rank");
        var objIndex = docRank.options.selectedIndex;
        var objVal = docRank.options[objIndex].value;
        if(objVal=='') {
            $("#field-rank").html("信用等级必须选择");
            return false;
        }
        if($("#contacts").val()=='') {
            $("#field-contacts").html("联系人必须填写");
            return false;
        }
        if($("#contactsPhone").val()=='') {
            $("#field-contactsPhone").html("联系人电话必须填写");
            return false;
        }
        return true;
    }
    
    //-->
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
