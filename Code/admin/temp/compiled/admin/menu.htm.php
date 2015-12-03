<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>中交物融管理平台菜单</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="styles/general.css" rel="stylesheet" type="text/css" />
    <script language="JavaScript">
        <!--
        var noHelp   = "<p align='center' style='color: #666'><?php echo $this->_var['lang']['no_help']; ?></p>";
        var helpLang = "<?php echo $this->_var['help_lang']; ?>";
        //-->
    </script>
    
    <style type="text/css">
        body {
            background: #3D3D3D;margin: 0px;padding:0px;
        }
        dl,dd,dt,ul,ol,li {
            margin: 0px;padding:0px;
        }
        ul,ol,li {
            list-style-type: none;
        }
        #tabbar-div {
            background: #3D3D3D;
            padding-left: 10px;
            height: 21px;
            padding-top: 0px;
        }
        #tabbar-div p {
            margin: 1px 0 0 0;
        }
        .tab-front {
            background: #A5A5A5;
            line-height: 20px;
            font-weight: bold;
            padding: 4px 15px 4px 18px;
            border-right: 2px solid #335b64;
            cursor: hand;
            cursor: pointer;
        }
        .tab-back {
            color: #F4FAFB;
            line-height: 20px;
            padding: 4px 15px 4px 18px;
            cursor: hand;
            cursor: pointer;
        }
        .tab-hover {
            color: #F4FAFB;
            line-height: 20px;
            padding: 4px 15px 4px 18px;
            cursor: hand;
            cursor: pointer;
            background: #2F9DB5;
        }
        #top-div
        {
            padding: 3px 0 2px;
            background: #BBDDE5;
            margin: 5px;
            text-align: center;
        }
        #main-div {
            border: 1px solid #345C65;
            background: #FFF;
        }
        #menu-list {
            padding: 0;
            margin: 0;
        }
        #menu-list ul {
            padding: 0;
            margin: 0;
            list-style-type: none;
            color: #335B64;
        }
        #menu-list li {
            padding-left: 16px;
            line-height: 16px;
            cursor: hand;
            cursor: pointer;
        }
        #main-div a:visited, #menu-list a:link, #menu-list a:hover {
            color: #335B64
            text-decoration: none;
        }
        #menu-list a:active {
            color: #EB8A3D;
        }
        .explode {
            background: url(images/menu_minus.gif) no-repeat 0px 3px;
            font-weight: bold;
        }
        .collapse {
            background: url(images/menu_plus.gif) no-repeat 0px 3px;
            font-weight: bold;
        }
        .menu-item {
            background: url(images/menu_arrow.gif) no-repeat 0px 3px;
            font-weight: normal;
        }
        #help-title {
            font-size: 14px;
            color: #000080;
            margin: 5px 0;
            padding: 0px;
        }
        #help-content {
            margin: 0;
            padding: 0;
        }
        .tips {
            color: #CC0000;
        }
        .link {
            color: #000099;
        }

        #accordion {
        }

        .accordion {
            font: 12px Verdana, Arial;
            color: #333
        }

        .accordion dt {
            font-weight: bold;padding-left: 10px;
            cursor: pointer;
            background-color: #5E5E5E;
            background-image: url(images/arrow-d.png);
            background-position: right center;
            background-repeat: no-repeat;
            color: #fff;
            height: 25px;
            line-height: 25px;
            border-bottom: 1px solid #ffffff;
        }

        .accordion dt:hover {
            background-color: #A5A5A5;
        }

        .accordion .open {
            background-color: #A5A5A5;color: #000000;
            background-image: url(images/arrow-u.png)
        }

        .accordion dd {
            overflow: hidden;
            background: #fff;
        }
        .accordion dd ul li {margin-left: 20px;height: 20px;line-height: 20px;}

    </style>
    
</head>
<body>
<div id="tabbar-div">
    <p><span style="float:right; padding: 3px 5px;" ><a href="javascript:toggleCollapse();"></a></span>
        <span class="tab-front" id="menu-tab"><?php echo $this->_var['lang']['menu']; ?></span>
    </p>
</div>
<div id="main-div">
    <div id="accordion">
        <dl class="accordion" id="slider">
            <?php $_from = $this->_var['menus']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'menu');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['menu']):
?>
            <dt><?php echo $this->_var['menu']['label']; ?></dt>
            <dd>
                <?php if ($this->_var['menu']['children']): ?>
                <ul>
                    <?php $_from = $this->_var['menu']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child');if (count($_from)):
    foreach ($_from AS $this->_var['child']):
?>
                    <li key="<?php echo $this->_var['k']; ?>" name="menu"><a href="<?php echo $this->_var['child']['action']; ?>" target="main-frame"><?php echo $this->_var['child']['label']; ?></a></li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </ul>
                <?php endif; ?>
            </dd>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </dl>
    </div>
    <!--
    <div id="menu-list">
        <ul id="menu-ul">
            <?php $_from = $this->_var['menus']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'menu');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['menu']):
?>
            <?php if ($this->_var['menu']['action']): ?>
            <li class="explode"><a href="<?php echo $this->_var['menu']['action']; ?>" target="main-frame"><?php echo $this->_var['menu']['label']; ?></a></li>
            <?php else: ?>
            <li class="explode" key="<?php echo $this->_var['k']; ?>" name="menu">
                <?php echo $this->_var['menu']['label']; ?>
                <?php if ($this->_var['menu']['children']): ?>
                <ul>
                    <?php $_from = $this->_var['menu']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child');if (count($_from)):
    foreach ($_from AS $this->_var['child']):
?>
                    <li class="menu-item"><a href="<?php echo $this->_var['child']['action']; ?>" target="main-frame"><?php echo $this->_var['child']['label']; ?></a></li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </ul>
                <?php endif; ?>
            </li>
            <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
    </div>
    -->
</div>

<?php echo $this->smarty_insert_scripts(array('files'=>'accordion.js,../js/global.js,../js/utils.js,../js/transport.js,menu.js')); ?>
<script language="JavaScript">
<!--
var collapse_all = "<?php echo $this->_var['lang']['collapse_all']; ?>";
var expand_all = "<?php echo $this->_var['lang']['expand_all']; ?>";
var collapse = true;

accordion.slider("slider", 0, "open");
//function toggleCollapse()
//{
//    var items = document.getElementsByTagName('LI');
//    for (i = 0; i < items.length; i++)
//    {
//        if (collapse)
//        {
//            if (items[i].className == "explode")
//            {
//                toggleCollapseExpand(items[i], "collapse");
//            }
//        }
//        else
//        {
//            if ( items[i].className == "collapse")
//            {
//                toggleCollapseExpand(items[i], "explode");
//                ToggleHanlder.Reset();
//            }
//        }
//    }
//
//    collapse = !collapse;
//    document.getElementById('toggleImg').src = collapse ? 'images/menu_minus.gif' : 'images/menu_plus.gif';
//    document.getElementById('toggleImg').alt = collapse ? collapse_all : expand_all;
//}
//
//function toggleCollapseExpand(obj, status)
//{
//    if (obj.tagName.toLowerCase() == 'li' && obj.className != 'menu-item')
//    {
//        for (i = 0; i < obj.childNodes.length; i++)
//        {
//            if (obj.childNodes[i].tagName == "UL")
//            {
//                if (status == null)
//                {
//                    if (obj.childNodes[1].style.display != "none")
//                    {
//                        obj.childNodes[1].style.display = "none";
//                        ToggleHanlder.RecordState(obj.getAttribute("key"), "collapse");
//                        obj.className = "collapse";
//                    }
//                    else
//                    {
//                        obj.childNodes[1].style.display = "block";
//                        ToggleHanlder.RecordState(obj.getAttribute("key"), "explode");
//                        obj.className = "explode";
//                    }
//                    break;
//                }
//                else
//                {
//                    if( status == "collapse")
//                    {
//                        ToggleHanlder.RecordState(obj.getAttribute("key"), "collapse");
//                        obj.className = "collapse";
//                    }
//                    else
//                    {
//                        ToggleHanlder.RecordState(obj.getAttribute("key"), "explode");
//                        obj.className = "explode";
//                    }
//                    obj.childNodes[1].style.display = (status == "explode") ? "block" : "none";
//                }
//            }
//        }
//    }
//}

//document.getElementById('menu-list').onclick = function(e)
//{
//    var obj = Utils.srcElement(e);
//    toggleCollapseExpand(obj);
//}
//
//document.getElementById('tabbar-div').onmouseover=function(e)
//{
//    var obj = Utils.srcElement(e);
//
//    if (obj.className == "tab-back")
//    {
//        obj.className = "tab-hover";
//    }
//}
//
//document.getElementById('tabbar-div').onmouseout=function(e)
//{
//    var obj = Utils.srcElement(e);
//
//    if (obj.className == "tab-hover")
//    {
//        obj.className = "tab-back";
//    }
//}


/**
 * 创建XML对象
 */
function createDocument()
{
    var xmlDoc;
    // create a DOM object
    if (window.ActiveXObject)
    {
        try
        {
            xmlDoc = new ActiveXObject("Msxml2.DOMDocument.6.0");
        }
        catch (e)
        {
            try
            {
                xmlDoc = new ActiveXObject("Msxml2.DOMDocument.5.0");
            }
            catch (e)
            {
                try
                {
                    xmlDoc = new ActiveXObject("Msxml2.DOMDocument.4.0");
                }
                catch (e)
                {
                    try
                    {
                        xmlDoc = new ActiveXObject("Msxml2.DOMDocument.3.0");
                    }
                    catch (e)
                    {
                        alert(e.message);
                    }
                }
            }
        }
    }
    else
    {
        if (document.implementation && document.implementation.createDocument)
        {
            xmlDoc = document.implementation.createDocument("","doc",null);
        }
        else
        {
            alert("Create XML object is failed.");
        }
    }
    xmlDoc.async = false;

    return xmlDoc;
}

//菜单展合状态处理器
//var ToggleHanlder = new Object();
//
//Object.extend(ToggleHanlder ,{
//    SourceObject : new Object(),
//    CookieName   : 'Toggle_State',
//    RecordState : function(name,state)
//    {
//        if(state == "collapse")
//        {
//            this.SourceObject[name] = state;
//        }
//        else
//        {
//            if(this.SourceObject[name])
//            {
//                delete(this.SourceObject[name]);
//            }
//        }
//        var date = new Date();
//        date.setTime(date.getTime() + 99999999);
//        document.setCookie(this.CookieName, this.SourceObject.toJSONString(), date.toGMTString());
//    },
//
//    Reset :function()
//    {
//        var date = new Date();
//        date.setTime(date.getTime() + 99999999);
//        document.setCookie(this.CookieName, "{}" , date.toGMTString());
//    },
//
//    Load : function()
//    {
//        if (document.getCookie(this.CookieName) != null)
//        {
//            this.SourceObject = t_eval();
//            var items = document.getElementsByTagName('LI');
//            for (var i = 0; i < items.length; i++)
//            {
//                if ( items[0].getAttribute("name") == "menu" && items[0].getAttribute("id") != '20_yun')
//                {
//                    for (var k in this.SourceObject)
//                    {
//                        if ( typeof(items[i]) == "object")
//                        {
//                            if (items[i].getAttribute('key') == k)
//                            {
//                                toggleCollapseExpand(items[i], this.SourceObject[k]);
//                                collapse = false;
//                            }
//                        }
//                    }
//                }
//            }
//        }
//        document.getElementById('toggleImg').src = collapse ? 'images/menu_minus.gif' : 'images/menu_plus.gif';
//        document.getElementById('toggleImg').alt = collapse ? collapse_all : expand_all;
//    }
//});

//ToggleHanlder.CookieName += "_<?php echo $this->_var['admin_id']; ?>";
//初始化菜单状态
//ToggleHanlder.Load();

//-->
</script>

</body>
</html>