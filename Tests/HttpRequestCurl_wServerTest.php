<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 09.12.13
 * Time: 18:20
 */
require_once __DIR__ . '/../Boomstarter/API.php';
require_once __DIR__ . '/HTTPServer.php';

class HttpRequestCurlTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        // local web server
        HTTPServer::startServer();
    }

    public static function tearDownAfterClass()
    {
        HTTPServer::stopServer();
    }

    public function testExecute()
    {
        $url = 'http://localhost:8000/api/v1.1/partners/gifts';

        $request = new Boomstarter\HttpRequestCurl($url);

        $request->setOption(CURLOPT_RETURNTRANSFER, TRUE);
        $request->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $result = $request->execute();

        $this->assertTrue(is_string($result));
        $this->assertJson($result);

        $decoded = json_decode($result, TRUE);

        $this->assertEquals('/api/v1.1/partners/gifts', parse_url($decoded['_debug']['server']['REQUEST_URI'], PHP_URL_PATH));
    }
}

