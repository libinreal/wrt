<?php
/**
 * Created by PhpStorm.
 * User: wliu
 * Date: 14-8-30
 * Time: 下午7:43
 */

namespace PhpRudder\Http;


class RedisSession extends \Phalcon\Session\Adapter implements \ArrayAccess, \Traversable, \IteratorAggregate, \Countable, \Phalcon\Session\AdapterInterface {

    private $_host;

    private $_port;

    private $_lifetime;

    private $_redis;

    private $HTTP_SESSION_KEY = 'HTTP_SESSION_KEY_';

    public function __construct($config) {
        $this->_lifetime = $config->lifetime;
        $this->_host = $config->host;
        $this->_port = $config->port;

        $this->_redis = new \Redis();
        $this->_redis->connect($this->_host, $this->_port);

        ini_set("session.save_handler", "redis");
        ini_set("session.save_path", "tcp://.".$this->_host.":".$this->_port);

        if ($this->_lifetime == null) {
            $this->_lifetime= get_cfg_var("session.gc_maxlifetime");
        }
    }


    public function __destruct(){ }


    function start() {
        session_set_save_handler(
            array($this, 'open'),
            array($this, 'close'),
            array($this, 'read'),
            array($this, 'write'),
            array($this, 'destroy'),
            array($this, 'gc')
        );
        session_start();
    }

    function open()
    {
        return true;
    }

    function close()
    {
        return true;
    }

    function read($key)
    {
        return $this->_redis->get($this->HTTP_SESSION_KEY.session_id()."_".$key);
    }

    function write($key, $value, $expiry = 0)
    {
        if ($expiry == 0 && $this->_lifetime > 0 ) {
            $expiry = $this->_lifetime;
        }
        if ($expiry <= 0) {//永不过期
            return $this->_redis->set($this->HTTP_SESSION_KEY.session_id()."_".$key, $value);
        } else {
            return $this->_redis->setex($this->HTTP_SESSION_KEY.session_id()."_".$key,$expiry, $value);
        }
    }

    function destroy($key = null)
    {
        if ($key==null) return;
        return $this->_redis->delete($this->HTTP_SESSION_KEY.session_id()."_".$key);
    }

    function gc()
    {
        return true;
    }

    /**
     * Returns active session id
     *
     * @return string
     */
    public function getId() {

    }
    /**
     * Gets a session variable from an application context
     *
     * @param string $index
     * @param mixed $defaultValue
     * @param bool $remove
     * @return mixed
     */
    public function get($index, $defaultValue = null, $remove = NULL) {
	    $value = unserialize($this->read($index));
        if ($value == null) return $defaultValue;
        return $value;
    }


    /**
     * Sets a session variable in an application context
     *
     *<code>
     *	$session->set('auth', 'yes');
     *</code>
     *
     * @param string $index
     * @param string $value
     */
    public function set($index, $value, $expiry = 0){
        return $this->write($index, serialize($value), $expiry);
    }

}
