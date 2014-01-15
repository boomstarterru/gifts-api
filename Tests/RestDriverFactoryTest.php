<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 09.12.13
 * Time: 18:20
 */
require_once __DIR__ . '/../Boomstarter/API.php';

class RestDriverFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testGetAutomatic()
    {
        $driver = Boomstarter\RestDriverFactory::getAutomatic();

        if (function_exists('curl_exec')) {
            $this->assertInstanceOf('Boomstarter\RestDriverCurl', $driver);
        } else {
            $this->assertInstanceOf('Boomstarter\RestDriverStream', $driver);
        }
    }

    public function testUseCurl()
    {
        $driver = Boomstarter\RestDriverFactory::getCurl();
        $this->assertInstanceOf('Boomstarter\RestDriverCurl', $driver);
    }

    public function testUseStream()
    {
        $driver = Boomstarter\RestDriverFactory::getStream();
        $this->assertInstanceOf('Boomstarter\RestDriverStream', $driver);
    }
}
