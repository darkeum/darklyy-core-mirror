<?php

namespace Boot\System\Cache;

use Aws\DynamoDb\DynamoDbClient;
use Closure;
use Illuminate\Contracts\Cache\Factory as FactoryContract;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\Arr;
use Illuminate\Cache\CacheManager as LaravelCacheManager;
use InvalidArgumentException;

/**
 * @mixin \Illuminate\Contracts\Cache\Repository
 */
class CacheManager extends LaravelCacheManager
{
   
    /**
     * Get the cache connection configuration.
     *
     * @param  string  $name
     * @return array|null
     */
    protected function getConfig($name)
    {
        if (! is_null($name) && $name !== 'null') {
            return $this->app['config']->get('cache.stores')[$name];
        }

        return ['driver' => 'null'];
    }

    
}
