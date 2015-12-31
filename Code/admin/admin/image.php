<?php 
/**
 * 仿盗链
 */
$jump = 'http://www.baidu.com'; //如：http://xxx.xxx.xx

//允许访问的域名
$domainArr = array(
    'admin.zj.dev'
);

//域名信息
$host = $_SERVER['REQUEST_SCHEME'];
$domain = $_SERVER['SERVER_NAME'];

//允许访问
if (!in_array($domain, $domainArr) || !isset($_GET['act']) || !isset($_SERVER['HTTP_REFERER'])) {
    header('location:'.$jump);
}

//文件
$fileName = $_GET['url'];

if ($_GET['act'] == 'pdf') {
    
    //获取pdf信息
    $url = $host.'://'.$domain.'/data/contract/'.$fileName;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    header("Content-type: application/pdf");
    print( $data );
    exit;
}

/**
 * 下载
 */
elseif ($_GET['act'] == 'download') {
    
    $dirName = '../data/contract/'.$fileName;
    if (!file_exists($dirName)){
        exit('抱歉，文件不存在！');
    }
    $file = fopen($dirName, 'r');
    Header("Content-type: application/octet-stream");
    Header("Accept-Ranges: bytes");
    Header("Accept-Length: ".filesize($dirName));
    Header("Content-Disposition: attachment; filename=" . $fileName);
    echo fread($file,filesize($dirName));
    fclose($file);
    exit;
}