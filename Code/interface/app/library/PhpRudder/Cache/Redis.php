<?php

namespace PhpRudder\Cache;

class Redis extends \Phalcon\Cache\Backend implements \Phalcon\Cache\BackendInterface {

	protected $_collection;
	private $_host;
	private $_port;
	private $_lifetime;
	public $_redis;
	private $CACHE_KEY = 'CACHE_KEY_';

	public function __construct($frontend, $config = null){
		$this->_lifetime = $config->lifetime;
		$this->_host = $config->host;
		$this->_port = $config->port;
		$this->_redis = new \Redis();
		$this->_redis->connect($this->_host, $this->_port);
	}

	protected function _getCollection(){}

	/**
	 * 获得缓存内容
	 *
	 * @param int|string $keyName
	 * @param null $lifetime
	 * @return bool mixed string
	 */
	public function get($keyName, $lifetime = null){
		try {
			return $this->_redis->get($this->CACHE_KEY . $keyName);
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * 保存数据到缓存
	 *
	 * @param null $keyName
	 * @param null $content
	 * @param null $lifetime
	 * @param null $stopBuffer
	 */
	public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = null){
		try {
			if ($lifetime != null) {
				return $this->_redis->setex($this->CACHE_KEY . $keyName, $lifetime, $content);
			} else {
				return $this->_redis->set($this->CACHE_KEY . $keyName, $content);
			}
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Deletes a value from the cache by its key
	 *
	 * @param int|string $keyName
	 * @return boolean
	 */
	public function delete($keyName){
		try {
			$this->_redis->del($this->CACHE_KEY . $keyName);
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Query the existing cached keys
	 *
	 * @param string $prefix
	 * @return array
	 */
	public function queryKeys($prefix = null){
		try {
			return $this->_redis->keys($this->CACHE_KEY);
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Checks if cache exists and it hasn't expired
	 *
	 * @param string $keyName
	 * @param long $lifetime
	 * @return boolean
	 */
	public function exists($keyName = null, $lifetime = null){
		try {
			return $this->_redis->exists($this->CACHE_KEY . $keyName);
		} catch (\Exception $e) {
			return false;
		}
	}
	public function gc(){
		try {
			$v = $this->_redis->keys($this->CACHE_KEY);
			return $this->_redis->del($v);
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Increment of a given key by $value
	 *
	 * @param int|string $keyName
	 * @param long $value
	 * @return mixed
	 */
	public function increment($key_name = null, $value = null){
		try {
			if ($value == null) {
				return $this->_redis->incr($this->CACHE_KEY . $key_name);
			} else {
				return $this->_redis->incrBy($this->CACHE_KEY . $key_name, $value);
			}
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Decrement of a given key by $value
	 *
	 * @param int|string $keyName
	 * @param long $value
	 * @return mixed
	 */
	public function decrement($key_name = null, $value = null){
		try {
			if ($value == null) {
				return $this->_redis->decr($this->CACHE_KEY . $key_name);
			} else {
				return $this->_redis->decrBy($this->CACHE_KEY . $key_name, $value);
			}
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Immediately invalidates all existing items.
	 *
	 * @return bool
	 */
	public function flush(){
		try {
			$this->_redis->flushAll();
		} catch (\Exception $e) {
			return false;
		}
	}

	public function hset($key, $field, $value){
		return $this->_redis->hset($this->CACHE_KEY . $key, $field, $value);
	}

	public function hGet($key, $field){
		return $this->_redis->hGet($this->CACHE_KEY . $key, $field);
	}

	public function hIncrBy($key, $field){
		return $this->_redis->hIncrBy($this->CACHE_KEY . $key, $field, 1);
	}
}
