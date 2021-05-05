<?php

namespace App\Utils;

use Psr\Cache\CacheItemPoolInterface;

class BookCountCacheManager
{
    private const CACHE_EXPIRE_IN_SECONDS = 5 * 60; // 5 dk cache..

    private $cache;

    public function __construct(CacheItemPoolInterface $cacheMyRedis)
    {
        $this->cache = $cacheMyRedis;
    }

    public function get($categories)
    {
        if (!$this->cache->hasItem('books_count')) {
            $temp = [];

            foreach ($categories as $category) {
                $temp[$category->getId()] = $category->getBooksCount();
            }
            $books_count = $this->cache->getItem('books_count')->set(json_encode($temp));

            $books_count->expiresAfter(self::CACHE_EXPIRE_IN_SECONDS);

            $this->cache->save($books_count);
        }

        return $this->cache->getItem('books_count')->get();
    }
}
