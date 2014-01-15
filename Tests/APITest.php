<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 09.12.13
 * Time: 18:20
 */
require_once __DIR__ . '/../Boomstarter/API.php';

class APITest extends PHPUnit_Framework_TestCase
{
    protected $api_url = 'http://localhost:8000/api/v1.1/partners';
    protected $shop_uuid = 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1';
    protected $shop_token = 'c50267d4-d08a-4fff-ad2b-87746088188a';

    /**
     * @param $shop_uuid
     * @param $shop_token
     * @param $expected
     * @return Boomstarter\API
     */
    private function getMockedApi($shop_uuid, $shop_token, $expected)
    {
        // override Transport::get() ::post() ::put() ::delete()
        $transport = $this->getMockBuilder('Boomstarter\Transport')
            ->setMethods(array('get', 'post', 'put', 'delete'))
            ->getMock();

        $transport->expects($this->any())
            ->method('get')
            ->will($this->returnValue($expected));

        $transport->expects($this->any())
            ->method('post')
            ->will($this->returnValue($expected));

        $transport->expects($this->any())
            ->method('put')
            ->will($this->returnValue($expected));

        $transport->expects($this->any())
            ->method('delete')
            ->will($this->returnValue($expected));

        // override API::getTransport()
        $api = $this->getMockBuilder('Boomstarter\API')
            ->setConstructorArgs(array($shop_uuid, $shop_token))
            ->setMethods(array('getTransport'))
            ->getMock();

        $api->expects($this->any())
            ->method('getTransport')
            ->will($this->returnValue($transport));

        return $api;
    }

    public function testGetGifts_NoGifts()
    {
        $expected = array(
            'gifts' => array(),
            '_metadata' => array(
                'total_count' => 0
            )
        );

        $api = $this->getMockedApi($this->shop_uuid, $this->shop_token, $expected);

        $result = $api->getGiftsAll();

        $this->assertTrue(is_array($result));
        $this->assertEquals(0, count($result));
    }

    public function testGetGifts()
    {
        $transport = new Boomstarter\Transport();
        $gift = new Boomstarter\Gift($transport);

        $expected = array(
            'gifts' => array(
                $gift
            ),
            '_metadata' => array(
                'total_count' => 1
            )
        );

        $api = $this->getMockedApi($this->shop_uuid, $this->shop_token, $expected);

        $result = $api->getGiftsAll();

        $this->assertTrue(is_array($result));
        $this->assertEquals(1, count($result));
        $this->assertInstanceOf('Boomstarter\Gift', $result[0]);
    }
}
