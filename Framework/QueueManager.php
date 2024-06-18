<?php

namespace Framework;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class QueueManager
{
    private static $instance = null;
    private $queueAdapter;
    private $redis;

    private function __construct()
    {
        $this->initializeQueueAdapter();
    }

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initializeQueueAdapter()
    {
        $redisConnection = RedisConnection::instance();
        $this->redis = $redisConnection->getRedis();

        if ($this->redis) {
            $this->queueAdapter = $this->redis;
        } else {
            $this->queueAdapter = new FilesystemAdapter();
        }
    }

    public function add($queue, $data)
    {
        if ($this->redis) {
            $this->redis->rpush('queue_:'.$queue, json_encode($data));
        } else {
            // Fallback to filesystem queue
            $item = $this->queueAdapter->getItem($queue);
            $queueData = $item->get() ?? [];
            $queueData[] = $data;
            $item->set($queueData);
            $this->queueAdapter->save($item);
        }
    }

    public function remove($queue)
    {
        if ($this->redis) {
            $data = $this->redis->lpop('queue_:'.$queue);
            if ($data) {
                return json_decode($data, true);
            }
        } else {
            // Fallback to filesystem queue
            $item = $this->queueAdapter->getItem($queue);
            $queueData = $item->get() ?? [];
            if (!empty($queueData)) {
                $queue = array_shift($queueData);
                $item->set($queueData);
                $this->queueAdapter->save($item);
                return $queue;
            }
        }
        return null;
    }

    public static function clear($queue)
    {
        if (self::$instance !== null) {
            if (self::$instance->redis) {
                self::$instance->redis->del('queue_:'.$queue);
            } else {
                self::$instance->queueAdapter->deleteItem($queue);
            }
        }
    }

    public function logDebugBar($key, $data, $expiration)
    {
        if ($expiration > 0 && class_exists('Framework\DebugBar') && DebugBar::isSet()) {
            $debugbar = DebugBar::instance()->getDebugBar();
            if ($debugbar && isset($debugbar["cache"])) {
                $debugbar["cache"]->addCacheItem($key, json_encode($data));
            }
        }
    }
}