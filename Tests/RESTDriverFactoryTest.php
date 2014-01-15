<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 09.12.13
 * Time: 18:20
 */
require_once __DIR__ . '/../Boomstarter/API.php';

class RESTDriverFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testGetAutomatic()
    {
        $driver = Boomstarter\RESTDriverFactory::getAutomatic();

        if (function_exists('curl_exec')) {
            $this->assertInstanceOf('Boomstarter\RESTDriverCurl', $driver);
        } else {
            $this->assertInstanceOf('Boomstarter\RESTDriverStream', $driver);
        }
    }

    public function testUseCurl()
    {
        $driver = Boomstarter\RESTDriverFactory::getCurl();
        $this->assertInstanceOf('Boomstarter\RESTDriverCurl', $driver);
    }

    public function testUseStream()
    {
        $driver = Boomstarter\RESTDriverFactory::getStream();
        $this->assertInstanceOf('Boomstarter\RESTDriverStream', $driver);
    }
}
