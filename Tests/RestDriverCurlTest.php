<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 09.12.13
 * Time: 18:20
 */
require_once __DIR__ . '/../Boomstarter/API.php';

class RestDriverCurlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param $url
     * @param $expected
     * @return Boomstarter\IRestDriver
     */
    private function getMockedDriver($url, $expected)
    {
        // override HttpRequestCurl::execute()
        $request = $this->getMockBuilder('Boomstarter\HttpRequestCurl')
            ->setConstructorArgs(array($url))
            ->setMethods(array('execute'))
            ->getMock();

        $request->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($expected));

        // override RestDriverCurl::getRequest()
        $driver = $this->getMockBuilder('Boomstarter\RestDriverCurl')
            ->setMethods(array('getRequest'))
            ->getMock();

        $driver->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        return $driver;
    }

    public function testGet()
    {
        $url = 'https://boomstarter.ru/api/v1.1/partners/gifts';

        $data = array(
            'shop_uuid' => 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1',
            'shop_token' => 'c50267d4-d08a-4fff-ad2b-87746088188a',
            'limit' => 100,
            'offset' => 0
        );

        $expected = 'mocked';

        $driver = $this->getMockedDriver($url, $expected);

        $response = $driver->get($url, $data);

        $this->assertEquals($expected, $response);
    }

    public function testPost()
    {
        $url = 'https://boomstarter.ru/api/v1.1/partners/gifts';

        $data = array(
            'shop_uuid' => 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1',
            'shop_token' => 'c50267d4-d08a-4fff-ad2b-87746088188a',
            'limit' => 100,
            'offset' => 0
        );

        $expected = 'mocked';

        $driver = $this->getMockedDriver($url, $expected);

        $response = $driver->post($url, $data);

        $this->assertEquals($expected, $response);
    }

    public function testPut()
    {
        $url = 'https://boomstarter.ru/api/v1.1/partners/gifts';

        $data = array(
            'shop_uuid' => 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1',
            'shop_token' => 'c50267d4-d08a-4fff-ad2b-87746088188a',
            'limit' => 100,
            'offset' => 0
        );

        $expected = 'mocked';

        $driver = $this->getMockedDriver($url, $expected);

        $response = $driver->put($url, $data);

        $this->assertEquals($expected, $response);
    }

    public function testDelete()
    {
        $url = 'https://boomstarter.ru/api/v1.1/partners/gifts';

        $data = array(
            'shop_uuid' => 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1',
            'shop_token' => 'c50267d4-d08a-4fff-ad2b-87746088188a',
            'limit' => 100,
            'offset' => 0
        );

        $expected = 'mocked';

        $driver = $this->getMockedDriver($url, $expected);

        $response = $driver->delete($url, $data);

        $this->assertEquals($expected, $response);
    }
}
