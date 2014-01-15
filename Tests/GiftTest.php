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
        // override RESTDriverCurl::get() ::post() ::put() ::delete()
        $driver = $this->getMockBuilder('Boomstarter\RESTDriverCurl')
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
        $expected = '{"mocked": 1}';

        $transport = $this->getMockedTransport($expected);

        $gift = new Boomstarter\Gift($transport);

        $order_id = "1";

        $result = $gift->order($order_id);

        $this->assertInstanceOf('Boomstarter\Gift', $result);
    }

    public function testSchedule()
    {
        $expected = '{"mocked": 1}';

        $transport = $this->getMockedTransport($expected);

        $gift = new Boomstarter\Gift($transport);

        $delivery_date = "2014-01-01";

        $result = $gift->schedule($delivery_date);

        $this->assertInstanceOf('Boomstarter\Gift', $result);
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
    }
}
