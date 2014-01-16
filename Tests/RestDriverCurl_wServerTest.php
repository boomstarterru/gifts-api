<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 09.12.13
 * Time: 18:20
 */
require_once __DIR__ . '/../Boomstarter/API.php';
require_once __DIR__ . '/HTTPServer.php';

class RestDriverCurl_wServerTest extends PHPUnit_Framework_TestCase
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

    public function testGet()
    {
        $domain = 'http://localhost:8000';
        $url = '/api/v1.1/partners/gifts';

        $data = array(
            'shop_uuid' => 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1',
            'shop_token' => 'c50267d4-d08a-4fff-ad2b-87746088188a',
            'limit' => 100,
            'offset' => 0
        );

        $driver = Boomstarter\RestDriverFactory::getCurl();

        $response = $driver->get($domain . $url, $data);

        $decoded = json_decode($response, TRUE);

        $this->assertEquals($url, parse_url($decoded['_debug']['server']['REQUEST_URI'], PHP_URL_PATH));
        $this->assertEquals("GET", $decoded['_debug']['server']['REQUEST_METHOD']);
        $this->assertEquals($data['shop_uuid'], $decoded['_debug']['get']['shop_uuid']);
        $this->assertEquals($data['shop_token'], $decoded['_debug']['get']['shop_token']);
    }

    public function testPost()
    {
        $domain = 'http://localhost:8000';
        $url = '/api/v1.1/partners/gifts';

        $data = array(
            'shop_uuid' => 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1',
            'shop_token' => 'c50267d4-d08a-4fff-ad2b-87746088188a',
            'limit' => 100,
            'offset' => 0
        );

        $driver = Boomstarter\RestDriverFactory::getCurl();

        $response = $driver->post($domain . $url, $data);

        $decoded = json_decode($response, TRUE);
        $decoded_post = json_decode($decoded['_debug']['raw_post'], TRUE);

        $this->assertEquals($url, parse_url($decoded['_debug']['server']['REQUEST_URI'], PHP_URL_PATH));
        $this->assertEquals("POST", $decoded['_debug']['server']['REQUEST_METHOD']);
        $this->assertEquals($data['shop_uuid'], $decoded_post['shop_uuid']);
        $this->assertEquals($data['shop_token'], $decoded_post['shop_token']);
    }

    public function testPut()
    {
        $domain = 'http://localhost:8000';
        $url = '/api/v1.1/partners/gifts';

        $data = array(
            'shop_uuid' => 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1',
            'shop_token' => 'c50267d4-d08a-4fff-ad2b-87746088188a',
            'limit' => 100,
            'offset' => 0
        );

        $driver = Boomstarter\RestDriverFactory::getCurl();

        $response = $driver->put($domain . $url, $data);

        $decoded = json_decode($response, TRUE);
        $decoded_put = json_decode($decoded['_debug']['raw_put'], TRUE);

        $this->assertEquals($url, parse_url($decoded['_debug']['server']['REQUEST_URI'], PHP_URL_PATH));
        $this->assertEquals("PUT", $decoded['_debug']['server']['REQUEST_METHOD']);
        $this->assertEquals($data['shop_uuid'], $decoded_put['shop_uuid']);
        $this->assertEquals($data['shop_token'], $decoded_put['shop_token']);
    }

    public function testDelete()
    {
        $domain = 'http://localhost:8000';
        $url = '/api/v1.1/partners/gifts';

        $data = array(
            'shop_uuid' => 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1',
            'shop_token' => 'c50267d4-d08a-4fff-ad2b-87746088188a',
            'limit' => 100,
            'offset' => 0
        );

        $driver = Boomstarter\RestDriverFactory::getCurl();

        $response = $driver->delete($domain . $url, $data);

        $decoded = json_decode($response, TRUE);
        $decoded_delete = json_decode($decoded['_debug']['raw_delete'], TRUE);

        $this->assertEquals($url, parse_url($decoded['_debug']['server']['REQUEST_URI'], PHP_URL_PATH));
        $this->assertEquals("DELETE", $decoded['_debug']['server']['REQUEST_METHOD']);
        $this->assertEquals($data['shop_uuid'], $decoded_delete['shop_uuid']);
        $this->assertEquals($data['shop_token'], $decoded_delete['shop_token']);
    }
}
