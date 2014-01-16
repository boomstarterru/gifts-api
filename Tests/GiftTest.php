<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 09.12.13
 * Time: 18:20
 */
require_once __DIR__ . '/../Boomstarter/API.php';

class GiftTest extends PHPUnit_Framework_TestCase
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

    public function testOrder()
    {
        $expected = file_get_contents(__DIR__ . '/api/v1.1/partners/gifts/5b6a38b7-b555-43e6-8b00-45ea924b283d/order/response.json');
        $initial = json_decode($expected, TRUE);
        $initial["mocked"] = 1;
        $order_id = "10002223321222";

        $transport = $this->getMockedTransport($expected);
        $gift = Boomstarter\GiftFactory::getGift($transport, $initial);
        $gift->order_id = "<old_id>";

        $this->assertEquals("<old_id>", $gift->order_id);

        $result = $gift->order($order_id);

        $this->assertInstanceOf('Boomstarter\Gift', $result);
        $this->assertObjectHasAttribute('order_id', $result);
        $this->assertEquals($order_id, $result->order_id);
        $this->assertObjectHasAttribute('mocked', $result);
        $this->assertEquals(1, $result->mocked);
    }

    public function testSchedule()
    {
        $expected = '{"mocked": 1}';

        $transport = $this->getMockedTransport($expected);

        $gift = new Boomstarter\Gift($transport);

        $delivery_date = "2014-01-01";

        $result = $gift->schedule($delivery_date);

        $this->assertInstanceOf('Boomstarter\Gift', $result);
        $this->assertObjectHasAttribute('mocked', $result);
        $this->assertEquals(1, $result->mocked);
    }

    /**
     * @covers Boomstarter\Gift::schedule
     * @expectedException \Exception
     */
    public function testScheduleException()
    {
        $expected = '{"mocked": 1}';

        $transport = $this->getMockedTransport($expected);

        $gift = new Boomstarter\Gift($transport);

        $delivery_date = "The wrong date";

        $result = $gift->schedule($delivery_date);
    }

    public function testSetStateDelivery()
    {
        $expected = '{"mocked": 1}';

        $transport = $this->getMockedTransport($expected);

        $gift = new Boomstarter\Gift($transport);

        $result = $gift->setStateDelivery();

        $this->assertInstanceOf('Boomstarter\Gift', $result);
        $this->assertObjectHasAttribute('mocked', $result);
        $this->assertEquals(1, $result->mocked);
    }
}
