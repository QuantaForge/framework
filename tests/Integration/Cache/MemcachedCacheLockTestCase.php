<?php

namespace QuantaForge\Tests\Integration\Cache;

use QuantaForge\Contracts\Cache\LockTimeoutException;
use QuantaForge\Support\Facades\Cache;

/**
 * @requires extension memcached
 */
class MemcachedCacheLockTestCase extends MemcachedIntegrationTestCase
{
    public function testMemcachedLocksCanBeAcquiredAndReleased()
    {
        Cache::store('memcached')->lock('foo')->forceRelease();
        $this->assertTrue(Cache::store('memcached')->lock('foo', 10)->get());
        $this->assertFalse(Cache::store('memcached')->lock('foo', 10)->get());
        Cache::store('memcached')->lock('foo')->forceRelease();
        $this->assertTrue(Cache::store('memcached')->lock('foo', 10)->get());
        $this->assertFalse(Cache::store('memcached')->lock('foo', 10)->get());
        Cache::store('memcached')->lock('foo')->forceRelease();
    }

    public function testMemcachedLocksCanBlockForSeconds()
    {
        Cache::store('memcached')->lock('foo')->forceRelease();
        $this->assertSame('taylor', Cache::store('memcached')->lock('foo', 10)->block(1, function () {
            return 'taylor';
        }));

        Cache::store('memcached')->lock('foo')->release();
        $this->assertTrue(Cache::store('memcached')->lock('foo', 10)->block(1));
    }

    public function testLocksCanRunCallbacks()
    {
        Cache::store('memcached')->lock('foo')->forceRelease();
        $this->assertSame('taylor', Cache::store('memcached')->lock('foo', 10)->get(function () {
            return 'taylor';
        }));
    }

    public function testLocksThrowTimeoutIfBlockExpires()
    {
        $this->expectException(LockTimeoutException::class);

        Cache::store('memcached')->lock('foo')->release();
        Cache::store('memcached')->lock('foo', 5)->get();
        $this->assertSame('taylor', Cache::store('memcached')->lock('foo', 10)->block(1, function () {
            return 'taylor';
        }));
    }

    public function testConcurrentMemcachedLocksAreReleasedSafely()
    {
        Cache::store('memcached')->lock('bar')->forceRelease();

        $firstLock = Cache::store('memcached')->lock('bar', 1);
        $this->assertTrue($firstLock->acquire());
        sleep(2);

        $secondLock = Cache::store('memcached')->lock('bar', 10);
        $this->assertTrue($secondLock->acquire());

        $firstLock->release();

        $this->assertTrue(Cache::store('memcached')->has('bar'));
    }

    public function testMemcachedLocksCanBeReleasedUsingOwnerToken()
    {
        Cache::store('memcached')->lock('foo')->forceRelease();

        $firstLock = Cache::store('memcached')->lock('foo', 10);
        $this->assertTrue($firstLock->get());
        $owner = $firstLock->owner();

        $secondLock = Cache::store('memcached')->restoreLock('foo', $owner);
        $secondLock->release();

        $this->assertTrue(Cache::store('memcached')->lock('foo')->get());
    }
}
