<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @link http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
use Infuse\Application;
use Infuse\Services\Memcache;

class MemcacheTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('memcache')) {
            $this->markTestSkipped(
              'The Memcache extension is not available.'
            );
        }
    }

    public function testInvoke()
    {
        $config = [
            'memcache' => [
                'host' => 'localhost',
                'port' => 11211,
            ],
        ];
        $app = new Application($config);
        $service = new Memcache();
        $memcache = $service($app);

        $this->assertInstanceOf('Memcache', $memcache);
    }
}
