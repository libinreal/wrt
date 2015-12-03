<?php
/**
 * Created by PhpStorm.
 * User: wliu
 * Date: 14-8-29
 * Time: 上午10:44
 */
namespace PhpRudder;

 class CommonUtil {

 	/**
 	 * 获取随机字符串
 	 * @param number $length
 	 * @param string $chars
 	 * @return string
 	 */
    public static function random($length=6, $chars = '0123456789') {
        $hash = '';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++)	{
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }

    /**
     * 对象转数组
     * @param object $obj
     * @return array
     */
	public static function object2Array($obj){
		if(!is_object($obj)) {
			return $obj;
		}
		$json = json_encode($obj);
		if($json === false) {
			return $obj;
		}
		return json_decode($json, true);
	}

 }