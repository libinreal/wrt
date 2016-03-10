<?php 
header('content-type:text/html;charset=utf-8');

//来源判断
if (!isset($_SERVER['HTTP_REFERER'])) return NotFound('Refused access');

$origin = pathinfo($_SERVER['HTTP_REFERER'], PATHINFO_FILENAME);
if ($origin != 'contract_manage') return NotFound('Wrong origin');


//可操作类型
$actionList = array(
		'view', 
		'download'
);

//参数判断
if (!isset($_GET['act']) or !$_GET['act'] or !in_array($_GET['act'], $actionList)) return NotFound('Unknown action');
if (!isset($_GET['url']) or !$_GET['url']) return NotFound('None filename');

//文件类型处理
$extension = pathinfo($_GET['url'], PATHINFO_EXTENSION);
if (!$extension or $extension != 'pdf') return NotFound('Wrong extension');

//文件真假
$filename = '../data/contract/'.$_GET['url'];
if (!is_file($filename)) return NotFound();

switch ($_GET['act']) {
	case 'view':
		header("Content-type: application/pdf");
		echo file_get_contents($filename);
		die;
		break;
		
	case 'download':
		$file = fopen($filename, 'r');
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length: ".filesize($filename));
		Header("Content-Disposition: attachment; filename=" . $_GET['url']);
		echo fread($file, filesize($filename));
		fclose($file);
		die;
		
		break;
		
	default:
		return NotFound('Error');
		break;
}

function NotFound($string = NULL) 
{
	header('HTTP/1.1 404 Not Found');
	header("status: 404 Not Found");
	if ($string) die($string);
	die('404 Not Found');
}