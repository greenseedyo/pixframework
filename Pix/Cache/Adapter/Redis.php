<?php

/**
 * Pix_Cache_Adapter_Redis
 *
 * @uses Pix_Cache_Adapter
 * @package Cache
 * @version $id$
 * @copyright 2003-2012 PIXNET Digital Media Corporation
 * @license http://framework.pixnet.net/license BSD License
 */
class Pix_Cache_Adapter_Redis extends Pix_Cache_Adapter
{
    protected $_redis = null;
    protected $_server = null;
    protected $_default_expire = 3600;

    public function __construct($config)
    {
        $this->_server = $config['server'];

        if (isset($config['options'])) {
            if (isset($config['options']['expire'])) {
                $this->_default_expire = $config['options']['expire'];
            }
            if (isset($config['options']['ex'])) {
                $this->_default_expire = $config['options']['ex'];
            }
        }
    }

    public function getRedis()
    {
        if (is_null($this->_redis)) {
            $server = $this->_server;
            if (!is_array($server)) {
                throw new Pix_Exception('config error');
            }

            $this->_redis = new Redis;
            $this->_redis->connect(
                $server['host'],
                $server['port'],
                $server['timeout'] ?: 1,
                $server['reserved'] ?: null,
                $server['retry_interval'] ?: null
            );
        }
        return $this->_redis;
    }

    protected function _getOptions($options)
    {
        $ret = array();
        if (is_int($options)) {
            $expire = $options;
        } elseif (isset($options['expire'])) {
            $expire = $options['expire'];
        } elseif (isset($options['ex'])) {
            $expire = $options['ex'];
        } else {
            $expire = $this->_default_expire;
        }
        $ret['ex'] = $expire;
        return $ret;
    }

    public function add($key, $value, $options = array())
    {
        $redis = $this->getRedis();
        $options = $this->_getOptions($options);
        return $redis->setNx($key, $value, $options);
    }

    public function set($key, $value, $options = array())
    {
        $redis = $this->getRedis();
        $options = $this->_getOptions($options);
        return $redis->set($key, $value, $options);
    }

    public function delete($key)
    {
        $redis = $this->getRedis();
        return $redis->delete($key);
    }

    public function replace($key, $value, $options = array())
    {
        $redis = $this->getRedis();
        $options = $this->_getOptions($options);
        return $redis->setEx($key, $value, $options);
    }

    public function inc($key, $inc = 1)
    {
        $redis = $this->getRedis();
        return $redis->incrBy($key, $inc);
    }

    public function dec($key, $inc = 1)
    {
        $redis = $this->getRedis();
        return $redis->decrBy($key, $inc);
    }

    public function append($key, $data)
    {
        $redis = $this->getRedis();
        return $redis->append($key, $data);
    }

    public function get($key)
    {
        $redis = $this->getRedis();
        return $redis->get($key);
    }

    public function __call($name, $arguments)
    {
        $redis = $this->getRedis();
        call_user_func_array(array($redis, $name), $arguments);
    }
}
