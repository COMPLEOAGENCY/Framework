<?php

namespace Framework;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class SessionHandler
{
    private static $instance = null;
    private $session;

    private function __construct()
    {
        $this->session = null;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function startSession()
    {
        if (empty($this->session)) {

                try {
                    $redisConnection = RedisConnection::instance();
                    $redis = $redisConnection->getRedis();      
                    if($redis){
                        $redisHandler = new RedisSessionHandler($redis, ['prefix' => 'session_:']);
                        $storage = new NativeSessionStorage([], $redisHandler);
                        $session = new Session($storage);
                        $session->start();
                        $this->session = $session;  
                    } else {
                        Throw new \Exception("Redis connection not found");
                    }           
                } catch (\Exception $e) {
                    $session = new Session();
                    $session->start();
                    $this->session = $session;
                }

        }
        return $this->session;
    }

    public function getSession()
    {
        return $this->session;
    }
}
