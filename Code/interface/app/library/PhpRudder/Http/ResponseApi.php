<?php
/**
 * Created by PhpStorm.
 * User: wliu
 * Date: 14-8-25
 * Time: 上午9:44
 */
namespace PhpRudder\Http;

class ResponseApi {

    /**
     * 将后返回数据包裹成统一格式。
     *
     * @param $body
     * @param int $code
     */
    public static function send($body = null, $code = 0, $message = '') {
    	if(is_object($message) && method_exists($message, 'getMessage')) { //错误的时候，取出错误提示语
    		$message = $message->getMessage();
    	}
        $result = array();
        $result['body'] = $body;
        $result['code'] = $code;
        $result['message'] = $message;
        return $result;
    }
}