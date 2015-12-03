<?php

/**
 * 管理中心新闻处理程序文件
 * $Author: xy $
 * $Id: article.php 2014-09-03 xy $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
//require_once(ROOT_PATH . "includes/fckeditor/fckeditor.php");
require_once(ROOT_PATH . 'includes/cls_image.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("article"), $db, 'article_id', 'title');
//$image = new cls_image();

/* 允许上传的文件类型 */
$allow_file_types = '|GIF|JPG|PNG|';

/*------------------------------------------------------ */
//-- 文章列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('cat_select',  article_cat_list(0));
    $smarty->assign('ur_here',      $_LANG['03_article_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['article_add'], 'href' => 'article.php?act=add'));
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $article_list = get_articleslist();

    $smarty->assign('article_list',    $article_list['arr']);
    $smarty->assign('filter',          $article_list['filter']);
    $smarty->assign('record_count',    $article_list['record_count']);
    $smarty->assign('page_count',      $article_list['page_count']);

    $sort_flag  = sort_flag($article_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('article_list.htm');
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('article_manage');

    $article_list = get_articleslist();

    $smarty->assign('article_list',    $article_list['arr']);
    $smarty->assign('filter',          $article_list['filter']);
    $smarty->assign('record_count',    $article_list['record_count']);
    $smarty->assign('page_count',      $article_list['page_count']);

    $sort_flag  = sort_flag($article_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('article_list.htm'), '',
        array('filter' => $article_list['filter'], 'page_count' => $article_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加文章
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('article_manage');

    /* 创建 html editor */
    create_html_editor('FCKeditor1');

    /*初始化*/
    $article = array();
    $article['is_open'] = 1;

    /* 取得分类、品牌 */
    $smarty->assign('goods_cat_list', cat_list());
    $smarty->assign('brand_list',     get_brand_list());

    /* 清理关联商品 */
    $sql = "DELETE FROM " . $ecs->table('goods_article') . " WHERE article_id = 0";
    $db->query($sql);

    if (isset($_GET['id']))
    {
        $smarty->assign('cur_id',  $_GET['id']);
    }
    $smarty->assign('article',     $article);
    $smarty->assign('cat_select',  article_cat_list(0));
    $smarty->assign('ur_here',     $_LANG['article_add']);
    $smarty->assign('action_link', array('text' => $_LANG['03_article_list'], 'href' => 'article.php?act=list'));
    $smarty->assign('form_action', 'insert');

    assign_query_info();
    $smarty->display('article_info.htm');
}

/*------------------------------------------------------ */
//-- 添加文章
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
    /* 权限判断 */
    admin_priv('article_manage');

    /*检查是否重复*/
    $is_only = $exc->is_only('title', $_POST['title'],0, " cat_id ='$_POST[article_cat]'");

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['title_exist'], stripslashes($_POST['title'])), 1);
    }
    if($_POST['cat_type'] == 1000 || $_POST['cat_type'] == 2000 ){
        sys_msg($_LANG['not_allow_add'], 1);
    }
    if($_POST[cat_type] == 3000){
        $file_url = '';
        $imgurl = '';
    }else{
        /*检查新闻列表置顶是否重复*/
        if($_POST[article_type] == 1){
            $cat_id1 = empty($_POST[article_cat]) ? 0 : intval($_POST[article_cat]);
            $is_only = $exc->is_only('article_type', 1, 0, "cat_id=".$cat_id1);
            if (!$is_only)
            {
                sys_msg(sprintf($_LANG['not_allow_type']), 1);
            }
        }
        /* 取得文件地址 */
        $file_url = '';
        $imgurl = '';
        $img = array();
        for($i=1;$i<=4;$i++){
            if ((isset($_FILES['img'.$i]['error']) && $_FILES['img'.$i]['error'] == 0) || (!isset($_FILES['img'.$i]['error']) && isset($_FILES['img'.$i]['tmp_name']) && $_FILES['img'.$i]['tmp_name'] != 'none'))
            {
                // 检查文件格式
                if (!check_file_type($_FILES['img'.$i]['tmp_name'], $_FILES['img'.$i]['name'], $allow_file_types))
                {
                    sys_msg($_LANG['invalid_file'.$i]);
                }

                // 复制文件
                $res = upload_article_file($_FILES['img'.$i]);
                if ($res != false)
                {
                    if($i == 4){
                        $imgurl = $res;
                    }else{
                        $img['img'.$i] = $res;
                    }
                }
            }else{
                if($i == 4){
                    $imgurl = "";
                }else{
                    $img['img'.$i] = "";
                }

            }
        }
        $file_url = serialize($img);

        if ($file_url == '')
        {
            $file_url = $_POST['file_url'];
        }
    }

    /* 计算文章打开方式 */
    if ($file_url == '')
    {
        $open_type = 0;
    }
    else
    {
        $open_type = $_POST['FCKeditor1'] == '' ? 1 : 2;
    }

    /*插入数据*/
    $add_time = gmtime();
    $createat = $add_time;
    $userid = $_SESSION['admin_id'];
    if (empty($_POST['cat_id']))
    {
        $_POST['cat_id'] = 0;
    }
    $sql = "INSERT INTO ".$ecs->table('article')."(title, cat_id, article_type, is_open, author, ".
                "author_email, keywords, content, add_time, file_url, open_type, link, description,imgurl,cat_type,createAt,updateAt,user_id) ".
            "VALUES ('$_POST[title]', '$_POST[article_cat]', '$_POST[article_type]', '$_POST[is_open]', ".
                "'$_POST[author]', '$_POST[author_email]', '$_POST[keywords]', '$_POST[FCKeditor1]', ".
                "'$add_time', '$file_url', '$open_type', '$_POST[link_url]', '$_POST[description]', '$imgurl', '$_POST[cat_type]', '$createat', '$createat', '$userid')";
    $db->query($sql);

    /* 处理关联商品 */
    $article_id = $db->insert_id();
    $sql = "UPDATE " . $ecs->table('goods_article') . " SET article_id = '$article_id' WHERE article_id = 0";
    $db->query($sql);

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'article.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'article.php?act=list';

    admin_log($_POST['title'],'add','article');

    clear_cache_files(); // 清除相关的缓存文件

    sys_msg($_LANG['articleadd_succeed'],0, $link);
}

/*------------------------------------------------------ */
//-- 编辑
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('article_manage');

    /* 取文章数据 */
    $sql = "SELECT * FROM " .$ecs->table('article'). " WHERE article_id='$_REQUEST[id]'";
    $article = $db->GetRow($sql);

    /* 创建 html editor */
    create_html_editor('FCKeditor1',$article['content']);

    /* 取得分类、品牌 */
    $smarty->assign('goods_cat_list', cat_list());
    $smarty->assign('brand_list', get_brand_list());

    /* 取得关联商品 */
    $goods_list = get_article_goods($_REQUEST['id']);
    $smarty->assign('goods_list', $goods_list);
    $list_imgurl = unserialize($article['file_url']);
    $article['img_list'] = $list_imgurl;

    $smarty->assign('article',     $article);
    $smarty->assign('cat_select',  article_cat_list(0, $article['cat_id']));
    $smarty->assign('ur_here',     $_LANG['article_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['03_article_list'], 'href' => 'article.php?act=list&' . list_link_postfix()));
    $smarty->assign('form_action', 'update');

    assign_query_info();
    $smarty->display('article_info.htm');
}

if ($_REQUEST['act'] =='update')
{
    /* 权限判断 */
    admin_priv('article_manage');

    /*检查文章名是否相同*/
    $is_only = $exc->is_only('title', $_POST['title'], $_POST['id'], "cat_id = '$_POST[article_cat]'");

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['title_exist'], stripslashes($_POST['title'])), 1);
    }
    if($_POST['cat_type'] == 1000 || $_POST['cat_type'] == 2000 ){
        sys_msg($_LANG['not_allow_add'], 1);
    }
    if (empty($_POST['cat_id']))
    {
        $_POST['cat_id'] = 0;
    }
    if($_POST[cat_type] == 3000){
        $file_url = '';
        $imgurl = '';
        $open_type = 0;
    }else{
        /*检查新闻列表置顶是否重复*/
        if($_POST[article_type] == 1){
            $cat_id1 = empty($_POST[article_cat]) ? 0 : intval($_POST[article_cat]);
            $is_only = $exc->is_only('article_type', 1, $_POST['id'], "cat_id=".$cat_id1);
            if (!$is_only)
            {
                sys_msg(sprintf($_LANG['not_allow_type']), 1);
            }
        }
        /* 取得文件地址 */
        $file_url = '';
        $imgurl = '';
        $img = array();

        for($i=1;$i<=4;$i++){
            if ((isset($_FILES['img'.$i]['error']) && $_FILES['img'.$i]['error'] == 0) || (!isset($_FILES['img'.$i]['error']) && isset($_FILES['img'.$i]['tmp_name']) && $_FILES['img'.$i]['tmp_name'] != 'none'))
            {
                // 检查文件格式
                if (!check_file_type($_FILES['img'.$i]['tmp_name'], $_FILES['img'.$i]['name'], $allow_file_types))
                {
                    sys_msg($_LANG['invalid_file'.$i]);
                }

                // 复制文件
                $res = upload_article_file($_FILES['img'.$i]);
                if ($res != false)
                {
                    if($i == 4){
                        $imgurl = $res;
                    }else{
                        $img['img'.$i] = $res;
                    }
                }
            }else{
                if($i == 4){
                    $imgurl = "";
                }else{
                    $img['img'.$i] = "";
                }

            }
        }
        if(!empty($img)){
            $file_url = serialize($img);
        }

        if ($file_url == ''){
            $file_url = $_POST['file_url'];
        }
        /* 计算文章打开方式 */
        if ($file_url == ''){
            $open_type = 0;
        }
        else{
            $open_type = $_POST['FCKeditor1'] == '' ? 1 : 2;
        }

        /* 如果 file_url 跟以前不一样，且原来的文件是本地文件，删除原来的文件 */
        $sql = "SELECT file_url FROM " . $ecs->table('article') . " WHERE article_id = '$_POST[id]'";
        $old_url = $db->getOne($sql);
        if(!empty($old_url)){
            $file_url = unserialize($file_url);
            $old_url = unserialize($old_url);
            foreach($old_url as $k=>$v){
                if ($old_url[$k] != '' && $file_url[$k] != '' && $old_url[$k] != $file_url[$k] && strpos($old_url[$k], 'http://') === false && strpos($old_url[$k], 'https://') === false)
                {
                    @unlink(ROOT_PATH . $old_url[$k]);
                }elseif($old_url[$k] != '' && $file_url[$k] == ''){
                    $file_url[$k] = $old_url[$k];
                }

            }
            $file_url = serialize($file_url);
        }
        //标题图片
        $sql = "SELECT imgurl FROM " . $ecs->table('article') . " WHERE article_id = '$_POST[id]'";
        $old_url = $db->getOne($sql);
        if ($old_url != '' && $imgurl != '' && $old_url != $imgurl && strpos($old_url, 'http://') === false && strpos($old_url, 'https://') === false)
        {
            @unlink(ROOT_PATH . $old_url);
        }elseif($old_url != '' && $imgurl == ''){
            $imgurl = $old_url;
        }
    }

    $updateat = gmtime();
    if ($exc->edit("title='$_POST[title]', cat_id='$_POST[article_cat]', article_type='$_POST[article_type]',
    is_open='$_POST[is_open]', author='$_POST[author]', author_email='$_POST[author_email]',
    keywords ='$_POST[keywords]', file_url ='$file_url', open_type='$open_type', content='$_POST[FCKeditor1]',
    link='$_POST[link_url]', description = '$_POST[description]', updateAt='$updateat', imgurl = '$imgurl', cat_type = '$_POST[cat_type]'", $_POST['id']))
    {
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'article.php?act=list&' . list_link_postfix();

        $note = sprintf($_LANG['articleedit_succeed'], stripslashes($_POST['title']));
        admin_log($_POST['title'], 'edit', 'article');

        clear_cache_files();

        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }


}
/*------------------------------------------------------ */
//-- 新闻置顶判断
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'chang_type'){
    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $article_type = empty($_REQUEST['article_id']) ? 1 : intval($_REQUEST['article_type']);
    $is_only = $exc->is_only('article_type', $article_type, "", "cat_id=".$cat_id);

    if (!$is_only)
    {
        echo 'error';
    }else{
        echo 'ok';
    }

}
/*------------------------------------------------------ */
//-- 删除图片
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'drop_image'){

    check_authz_json('article_manage');

    $img_id = empty($_REQUEST['img_id']) ? 0 : $_REQUEST['img_id'];
    $article_id = empty($_REQUEST['article_id']) ? 0 : intval($_REQUEST['article_id']);

    /* 删除图片文件 */
    $sql = "SELECT file_url, imgurl " .
        " FROM " . $GLOBALS['ecs']->table('article') .
        " WHERE article_id = '$article_id'";
    $row = $GLOBALS['db']->getRow($sql);
    if($img_id == "img4"){
        if ($row['imgurl'] != '' && is_file('../' . $row['imgurl']))
        {
            @unlink('../' . $row['imgurl']);
        }
        $imgurl = "";
        if ($exc->edit("imgurl ='$imgurl'", $article_id)){
            clear_cache_files();
            make_json_result($img_id);
        }else{
            die($db->error());
        }
    }else{
        $file_url = unserialize($row['file_url']);
        if ($file_url[$img_id] != '' && is_file('../' . $file_url[$img_id]))
        {
            @unlink('../' . $file_url[$img_id]);
        }
        unset($file_url[$img_id]);
        $i=1;
        foreach($file_url as $v){
            $tmp['img'.$i] = $v;
            $i++;
        }
        $count=0;
        if(!empty($tmp)){
            foreach($tmp as $val){
                if($val == "")
                    $count++;
            }
            if($count != count($tmp)){
                $file_url = serialize($tmp);
            }else{
                $file_url="";
            }
        }else{
            $file_url="";
        }
        if ($exc->edit("file_url ='$file_url'", $article_id)){
            clear_cache_files();
            if($file_url == ""){
                echo 'pic_empty';
            }else{
                echo json_encode($tmp);
            }
        }else{
            die($db->error());
        }
    }
}
/*------------------------------------------------------ */
//-- 编辑文章主题
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_title')
{
    check_authz_json('article_manage');

    $id    = intval($_POST['id']);
    $title = json_str_iconv(trim($_POST['val']));

    /* 检查文章标题是否重复 */
    if ($exc->num("title", $title, $id) != 0)
    {
        make_json_error(sprintf($_LANG['title_exist'], $title));
    }
    else
    {
        if ($exc->edit("title = '$title'", $id))
        {
            clear_cache_files();
            admin_log($title, 'edit', 'article');
            make_json_result(stripslashes($title));
        }
        else
        {
            make_json_error($db->error());
        }
    }
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_show')
{
    check_authz_json('article_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("is_open = '$val'", $id);
    clear_cache_files();

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 切换文章重要性
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_type')
{
    check_authz_json('article_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("article_type = '$val'", $id);
    clear_cache_files();

    make_json_result($val);
}



/*------------------------------------------------------ */
//-- 删除文章主题
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('article_manage');

    $id = intval($_GET['id']);

    /* 删除原来的文件 */
    $sql = "SELECT imgurl,file_url FROM " . $ecs->table('article') . " WHERE article_id = '$id'";
    $old_url = $db->getRow($sql);

    if($old_url['imgurl'] != ''){
        if ($old_url['imgurl'] != '' && strpos($old_url['imgurl'], 'http://') === false && strpos($old_url['imgurl'], 'https://') === false)
        {
            @unlink(ROOT_PATH . $old_url['imgurl']);
        }
    }
    if($old_url['file_url'] != ''){
        $file_url = unserialize($old_url['file_url']);
        foreach($file_url as $val){
            if ($val != '' && strpos($val, 'http://') === false && strpos($val, 'https://') === false)
            {
                @unlink(ROOT_PATH . $val);
            }
        }
    }

    $name = $exc->get_name($id);
    if ($exc->drop($id))
    {
        //$db->query("DELETE FROM " . $ecs->table('comment') . " WHERE " . "comment_type = 1 AND id_value = $id");
        admin_log(addslashes($name),'remove','article');
        clear_cache_files();
    }

    $url = 'article.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 将商品加入关联
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_link_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('article_manage');

    $add_ids = $json->decode($_GET['add_ids']);
    $args = $json->decode($_GET['JSON']);
    $article_id = $args[0];

    if ($article_id == 0)
    {
        $article_id = $db->getOne('SELECT MAX(article_id)+1 AS article_id FROM ' .$ecs->table('article'));
    }

    foreach ($add_ids AS $key => $val)
    {
        $sql = 'INSERT INTO ' . $ecs->table('goods_article') . ' (goods_id, article_id) '.
               "VALUES ('$val', '$article_id')";
        $db->query($sql, 'SILENT') or make_json_error($db->error());
    }

    /* 重新载入 */
    $arr = get_article_goods($article_id);
    $opt = array();

    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value'  => $val['goods_id'],
                        'text'  => $val['goods_name'],
                        'data'  => '');
    }

    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 将商品删除关联
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_link_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('article_manage');

    $drop_goods     = $json->decode($_GET['drop_ids']);
    $arguments      = $json->decode($_GET['JSON']);
    $article_id     = $arguments[0];

    if ($article_id == 0)
    {
        $article_id = $db->getOne('SELECT MAX(article_id)+1 AS article_id FROM ' .$ecs->table('article'));
    }

    $sql = "DELETE FROM " . $ecs->table('goods_article').
            " WHERE article_id = '$article_id' AND goods_id " .db_create_in($drop_goods);
    $db->query($sql, 'SILENT') or make_json_error($db->error());

    /* 重新载入 */
    $arr = get_article_goods($article_id);
    $opt = array();

    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value'  => $val['goods_id'],
                        'text'  => $val['goods_name'],
                        'data'  => '');
    }

    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 搜索商品
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'get_goods_list')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $filters = $json->decode($_GET['JSON']);

    $arr = get_goods_list($filters);
    $opt = array();

    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value' => $val['goods_id'],
                        'text' => $val['goods_name'],
                        'data' => $val['shop_price']);
    }

    make_json_result($opt);
}
/*------------------------------------------------------ */
//-- 批量操作
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch')
{
    /* 批量删除 */
    if (isset($_POST['type']))
    {
        if ($_POST['type'] == 'button_remove')
        {
            admin_priv('article_manage');

            if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
            {
                sys_msg($_LANG['no_select_article'], 1);
            }

            /* 删除原来的文件 */
            $sql = "SELECT file_url,imgurl FROM " . $ecs->table('article') .
                    " WHERE article_id " . db_create_in(join(',', $_POST['checkboxes'])) .
                    " AND file_url <> ''";

            $res = $db->query($sql);
            while ($row = $db->fetchRow($res))
            {
                if($row['imgurl'] != ''){
                    if ($row['imgurl'] != '' && strpos($row['imgurl'], 'http://') === false && strpos($row['imgurl'], 'https://') === false)
                    {
                        @unlink(ROOT_PATH . $row['imgurl']);
                    }
                }
                if($row['file_url'] != ''){
                    $file_url = unserialize($row['file_url']);
                    foreach($file_url as $val){
                        if ($val != '' && strpos($val, 'http://') === false && strpos($val, 'https://') === false)
                        {
                            @unlink(ROOT_PATH . $val);
                        }
                    }
                }
            }

            foreach ($_POST['checkboxes'] AS $key => $id)
            {
                if ($exc->drop($id))
                {
                    $name = $exc->get_name($id);
                    admin_log(addslashes($name),'remove','article');
                }
            }

        }

        /* 批量隐藏 */
        if ($_POST['type'] == 'button_hide')
        {
            check_authz_json('article_manage');
            if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
            {
                sys_msg($_LANG['no_select_article'], 1);
            }

            foreach ($_POST['checkboxes'] AS $key => $id)
            {
              $exc->edit("is_open = '0'", $id);
            }
        }

        /* 批量显示 */
        if ($_POST['type'] == 'button_show')
        {
            check_authz_json('article_manage');
            if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
            {
                sys_msg($_LANG['no_select_article'], 1);
            }

            foreach ($_POST['checkboxes'] AS $key => $id)
            {
              $exc->edit("is_open = '1'", $id);
            }
        }

        /* 批量移动分类 */
        if ($_POST['type'] == 'move_to')
        {
            check_authz_json('article_manage');
            if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']) )
            {
                sys_msg($_LANG['no_select_article'], 1);
            }

            if(!$_POST['target_cat'])
            {
                sys_msg($_LANG['no_select_act'], 1);
            }

            foreach ($_POST['checkboxes'] AS $key => $id)
            {
              $sql = "SELECT cat_type FROM " . $ecs->table('article_cat') . " WHERE cat_id = '$_POST[target_cat]'";
              $cat_type = $db->getone($sql);
              if($cat_type == 1000 || $cat_type == 2000){
                  sys_msg($_LANG['not_allow_add'], 1);
              }else{
                  $exc->edit("cat_id = '".$_POST['target_cat']."', cat_type = '".$cat_type."'", $id);
              }
            }
        }
    }

    /* 清除缓存 */
    clear_cache_files();
    $lnk[] = array('text' => $_LANG['back_list'], 'href' => 'article.php?act=list');
    sys_msg($_LANG['batch_handle_ok'], 0, $lnk);
}

/* 把商品删除关联 */
function drop_link_goods($goods_id, $article_id)
{
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('goods_article') .
            " WHERE goods_id = '$goods_id' AND article_id = '$article_id' LIMIT 1";
    $GLOBALS['db']->query($sql);
    create_result(true, '', $goods_id);
}

/* 取得文章关联商品 */
function get_article_goods($article_id)
{
    $list = array();
    $sql  = 'SELECT g.goods_id, g.goods_name'.
            ' FROM ' . $GLOBALS['ecs']->table('goods_article') . ' AS ga'.
            ' LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = ga.goods_id'.
            " WHERE ga.article_id = '$article_id'";
    $list = $GLOBALS['db']->getAll($sql);

    return $list;
}

/* 获得文章列表 */
function get_articleslist()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['cat_id'] = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'a.article_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if (!empty($filter['keyword']))
        {
            $where = " AND a.title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }
        if ($filter['cat_id'])
        {
            $where .= " AND a." . get_article_children($filter['cat_id']);
        }

        /* 文章总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('article'). ' AS a '.
               'LEFT JOIN ' .$GLOBALS['ecs']->table('article_cat'). ' AS ac ON ac.cat_id = a.cat_id '.
               'WHERE 1 ' .$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获取文章数据 */
        $sql = 'SELECT a.* , ac.cat_name '.
               'FROM ' .$GLOBALS['ecs']->table('article'). ' AS a '.
               'LEFT JOIN ' .$GLOBALS['ecs']->table('article_cat'). ' AS ac ON ac.cat_id = a.cat_id '.
               'WHERE 1 ' .$where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];

        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $arr = array();
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['date'] = local_date($GLOBALS['_CFG']['time_format'], $rows['add_time']);
        $rows['createAt'] = local_date($GLOBALS['_CFG']['time_format'], $rows['createAt']);
        $arr[] = $rows;
    }
    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

/* 上传文件 */
function upload_article_file($upload)
{
    if (!make_dir("../" . DATA_DIR . "/article"))
    {
        /* 创建目录失败 */
        return false;
    }

    $filename = cls_image::random_filename() . substr($upload['name'], strpos($upload['name'], '.'));
    $path     = ROOT_PATH. DATA_DIR . "/article/" . $filename;

    if (move_upload_file($upload['tmp_name'], $path))
    {
        return DATA_DIR . "/article/" . $filename;
    }
    else
    {
        return false;
    }
}

?>