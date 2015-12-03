<?php

namespace PhpRudder\Mvc;

use Phalcon\Mvc\Controller;
use PhpRudder\Http\ResponseApi;
use PhpRudder\Serviceable;
use Phalcon\Logger\Adapter\File as Logger;
use PhpRudder\Http\RequestUtil;

class ControllerBase extends Controller implements Serviceable
{

	const SIZE = 10;

	/**
	 * 替换富文本图片为绝对地址
	 */
	public function replaceImgUrl(&$content) {
		$imgBaseUrl = $this->get_url();
		return preg_replace_callback('/(\<img[^>]*?src\s*\=\s*[\'|\"])(.*?)([\'|\"])/', function($img) use ($imgBaseUrl) {
			if(!preg_match('/^http:\/\//', $img[2])) {
				$img[2] = $imgBaseUrl . ltrim($img[2], '/\\');
			}
			array_shift($img);
			return implode('', $img); 
		}, $content);
	}

    /**
     * @return object|Logger
     */
    public function get_logger() {
        return $this->getDI()->get("log");
    }

    /**
     * @return object|\PhpRudder\Cache\Redis
     */
    public function get_cache() {
        return $this->getDI()->get("cache");
    }

    /**
     * @return object|\PhpRudder\Http\RedisSession
     */
    public function get_session() {
        return $this->session;
    }

    /**
     * @return object|\Phalcon\Db\Adapter\Pdo\Mysql
     */
    public function get_db() {
        return $this->getDI()->get("db");
    }

    /**
     * @return object|\PhpRudder\Security\Auth
     */
    public function get_user() {
        return $this->get_session()->get("auth");
    }

    /**
     * @return object|\Phalcon\Mvc\Model\Transaction\Manager
     */
    public function get_tx() {
        return $this->getDI()->getShared("transactions");
    }
    
    public function get_url() {
    	return RequestUtil::getUrl();
    }

    public function afterExecuteRoute($dispatcher) {
        $v = $dispatcher->getReturnedValue();
		if(is_array($v)) {
			array_walk_recursive($v, function(&$item) {
				if($item === null) {
					$item = '';
				}
			});
		}
        $this->response->setHeader('Content-Type', 'application/json;charset=UTF-8');
        if ($this->request->isGet()) {
            if ($v != null && is_array($v)) {
                if ($v['body'] != null && $v['code'] == 0) {//空值或错误不需要做摘要
                    $jsonstr = json_encode($v);
                    /*$neweTag = md5($jsonstr);
                    $this->response->setHeader('ETag', $neweTag);
	                $this->response->setHeader('cache-control', 'max-age=1');
                    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $neweTag) {
                        header("HTTP/1.1 304 Not Modified");
                        $this->response->send();
                        exit;
                    }*/
                }
            } else if (!is_array($v) || !isset($v['body']) || !isset($v['code'])) {
                echo json_encode(ResponseApi::send("the protocol format is error:".$v, -1));
            }
        }
        if (isset($jsonstr)) {
            echo $jsonstr;
        } else {
            echo json_encode($v);
        }
        $this->response->send();
    }
}
