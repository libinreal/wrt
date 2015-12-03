<?php

namespace PhpRudder\Http;

/**
 * 请求方法类
 * @author hhu
 *
 */
class RequestUtil {

	/**
	 * post请求
	 * @param array $post post的参数，必须是数组
	 * @param string $url 请求的url
	 * @return boolean|string
	 */
	
	const TIMEOUT = 10;
	
	public static function requestPost($post = array(), $url) {
		if(!is_array($post) || !$post || !$url) return false;
		$context = array ();
		ksort($post);
		$query = http_build_query($post);
		$length = strlen($query);
		$context ['http'] = array (
				'method' => 'POST',
				'header' => "Content-type: application/x-www-form-urlencoded \n".
							"Content-length: " . $length. " \n".
							"Pragma: no-cache",
				'content' => $query,
		);
		$pstr = stream_context_create($context);
		$fp = @fopen($url, 'rb', false, $pstr);
		if(is_resource($fp)) {
			$response = stream_get_contents($fp);
			return $response;
		} else {
			return false;
		}
	}

	public static function syncPost($url, $content = null, $options = array()) {
		if(function_exists("curl_init")) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($content));
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, self::TIMEOUT);
			$ret_data = curl_exec($curl);
			if (curl_errno($curl)) {
				printf("curl call error(%s): %s\n", curl_errno($curl), curl_error($curl));
				curl_close($curl);
				return false;
			} else {
				curl_close($curl);
				return $ret_data;
			}
		} else {
			throw new \Exception("[PHP] curl module is required");
		}
	}

	function post($url, $content = null) {
		if(function_exists("curl_init")) {
			$curl = curl_init();
			if (is_array($content)) {
				$data = http_build_query($content);
			}
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, self::TIMEOUT);
			$ret_data = curl_exec($curl);
			if (curl_errno($curl)) {
				printf("curl call error(%s): %s\n", curl_errno($curl), curl_error($curl));
				curl_close($curl);
				return false;
			} else {
				curl_close($curl);
				return $ret_data;
			}
		} else {
			throw new \Exception("[PHP] curl module is required");
		}
	}

	/**
	 * 调用银行接口
	 */
	public static function httpsPost($post_data, $url) {
	    ob_start();
	    $post_data = http_build_query($post_data);
	    $post_data = iconv('utf-8', 'gbk', $post_data);
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	    if(curl_exec($ch) === false) {
	        return curl_error($ch);
	    }
	    curl_close($ch);
	    $result = ob_get_contents();
	    ob_end_clean();
	    $result = iconv('gbk', 'utf-8', $result);
	    return $result;
	}

	/**
	 * get请求
	 * @param string $get get的参数，暂定为数组
	 * @param string $url 请求的url
	 * @return string
	 */
	public static function requestGet($get = '',  $url) {
		if(!is_array($get) || !$url) return false;
		$url = $url . '?' . http_build_query($get);
		$fp = @fopen($url, 'r');
		$response = stream_get_contents($fp);
		return $response;
	}

	public static function getUrl() {
		return strtolower(self::getSchema()) . '://' . self::getHost() . '/';
	}

	public static function getHost(){
		$host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
		if (!empty($host)) {
			return $host;
		}

		$scheme = self::getSchema();
		$name = self::getServerName();
		$port = self::getPort();
		if (($scheme == "HTTP" && $port == 80) || ($scheme == "HTTPS" && $port == 443)) {
			return $name;
		} else if ($port > 0) {
			return $name . ':' . $port . '/';
		} else {
			return $name . '/';
		}
	}

	public static function getServerName(){
		return $_SERVER['SERVER_NAME'];
	}

	public static function getSchema(){
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "HTTPS" : "HTTP";
	}

	public static function getPort(){
		return $_SERVER['SERVER_PORT'];
	}

	public static function dirname($dir){
		return substr($dir, 0, strrpos($dir, "/"));
	}
}
