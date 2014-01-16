<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 09.12.13
 * Time: 18:20
 */
require_once __DIR__ . '/../Boomstarter/API.php';

class TransportTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param $expected
     * @return Boomstarter\Transport
     */
    private function getMockedTransport($expected)
    {
        // override RestDriverCurl::get() ::post() ::put() ::delete()
        $driver = $this->getMockBuilder('Boomstarter\RestDriverCurl')
            ->setMethods(array('get', 'post', 'put', 'delete'))
            ->getMock();

        $driver->expects($this->any())
            ->method('get')
            ->will($this->returnValue($expected));

        $driver->expects($this->any())
            ->method('post')
            ->will($this->returnValue($expected));

        $driver->expects($this->any())
            ->method('put')
            ->will($this->returnValue($expected));

        $driver->expects($this->any())
            ->method('delete')
            ->will($this->returnValue($expected));

        // override Transport::getDriver()
        $transport = $this->getMockBuilder('Boomstarter\Transport')
            ->setMethods(array('getDriver'))
            ->getMock();

        $transport->expects($this->any())
            ->method('getDriver')
            ->will($this->returnValue($driver));

        return $transport;
    }

    public function testSetters()
    {
        $transport = new Boomstarter\Transport();

        $result = $transport->setShopUUID('test-shop-uuid');
        $this->assertEquals($transport, $result);

        $result = $transport->setShopToken('test-shop-token');
        $this->assertEquals($transport, $result);

        $result = $transport->setApiUrl('test-api-url');
        $this->assertEquals($transport, $result);
    }

    public function testGet()
    {
        $expected = '{"mocked": 1}';

        $transport = $this->getMockedTransport($expected);

        $url = '/gifts';

        $data = array(
            'shop_uuid' => 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1',
            'shop_token' => 'c50267d4-d08a-4fff-ad2b-87746088188a',
            'limit' => 100,
            'offset' => 0
        );

        $result = $transport->get($url, $data);

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('mocked', $result);
    }

    public function testPost()
    {
        $expected = '{"mocked": 1}';

        $transport = $this->getMockedTransport($expected);

        $url = '/gifts';

        $data = array(
            'shop_uuid' => 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1',
            'shop_token' => 'c50267d4-d08a-4fff-ad2b-87746088188a',
            'limit' => 100,
            'offset' => 0
        );

        $result = $transport->post($url, $data);

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('mocked', $result);
    }

    public function testPut()
    {
        $expected = '{"mocked": 1}';

        $transport = $this->getMockedTransport($expected);

        $url = '/gifts';

        $data = array(
            'shop_uuid' => 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1',
            'shop_token' => 'c50267d4-d08a-4fff-ad2b-87746088188a',
            'limit' => 100,
            'offset' => 0
        );

        $result = $transport->put($url, $data);

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('mocked', $result);
    }

    public function testDelete()
    {
        $expected = '{"mocked": 1}';

        $transport = $this->getMockedTransport($expected);

        $url = '/gifts';

        $data = array(
            'shop_uuid' => 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1',
            'shop_token' => 'c50267d4-d08a-4fff-ad2b-87746088188a',
            'limit' => 100,
            'offset' => 0
        );

        $result = $transport->delete($url, $data);

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('mocked', $result);
    }

    public function testUseCurl()
    {
        $transport = new Boomstarter\Transport();

        $result = $transport->useCurl();

        $driver = $transport->getDriver();

        $this->assertEquals($transport, $result);
        $this->assertInstanceOf('Boomstarter\RestDriverCurl', $driver);
    }

    public function testUseStream()
    {
        $transport = new Boomstarter\Transport();

        $result = $transport->useStream();

        $driver = $transport->getDriver();

        $this->assertEquals($transport, $result);
        $this->assertInstanceOf('Boomstarter\RestDriverStream', $driver);
    }

    /**
     * @covers Boomstarter\Transport::get()
     * @expectedException \Exception
     */
    public function testGetException()
    {
        $expected = '<wrong json>';

        $transport = $this->getMockedTransport($expected);

        $url = '/gifts';

        $data = array(
            'shop_uuid' => 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1',
            'shop_token' => 'c50267d4-d08a-4fff-ad2b-87746088188a',
            'limit' => 100,
            'offset' => 0
        );

        $transport->get($url, $data);
    }
}
