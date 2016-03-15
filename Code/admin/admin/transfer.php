<?php
header("Content-Type:text/html;charset=utf-8");
require_once 'common.php';

//过滤参数
if (!isset($_GET['act']) || !$_GET['act']) return Response::notFound('Error:act');
if (!isset($_GET['url']) || !$_GET['url']) return Response::notFound('Error:url');

//限定访问动作
$actList = array('attach');
if (!in_array($_GET['act'], $actList)) return Response::notFound('Unknown action');

//执行
$act = ucfirst($_GET['act']);
$transfer = new Transfer();
$transfer->$act($_GET['url']);

class Transfer 
{
	
	public function Attach($url) 
	{
		$n = strpos(getcwd(), 'Code');
		if (!$n) return Response::notFound();
		$str = substr(getcwd(), 0, $n+4);
		$filename = $str.'/interface/public/apply_attachment/'.$url;
		if (!is_file($filename)) return Response::notFound();
		
		header("Content-Type:image/jpg");
		echo file_get_contents($filename);
		die;
	}
}