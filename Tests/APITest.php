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

        $this->assertInstanceOf('Boomstarter\GiftIterator', $result);
        $this->assertEquals(0, count($result));
    }

    public function testGetGiftsAll()
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

        $this->assertInstanceOf('Boomstarter\GiftIterator', $result);
        $this->assertEquals(1, count($result));
        $this->assertEquals($expected['_metadata']['total_count'], $result->getTotalCount());
        $this->assertInstanceOf('Boomstarter\Gift', $result[0]);
    }

    public function testGetGiftsPending()
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

        $result = $api->getGiftsPending();

        $this->assertInstanceOf('Boomstarter\GiftIterator', $result);
        $this->assertEquals(1, count($result));
        $this->assertInstanceOf('Boomstarter\Gift', $result[0]);
    }

    public function testGetGiftsDelivered()
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

        $result = $api->getGiftsDelivered();

        $this->assertInstanceOf('Boomstarter\GiftIterator', $result);
        $this->assertEquals(1, count($result));
        $this->assertInstanceOf('Boomstarter\Gift', $result[0]);
    }

    public function testGetGiftsShipping()
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

        $result = $api->getGiftsShipping();

        $this->assertInstanceOf('Boomstarter\GiftIterator', $result);
        $this->assertEquals(1, count($result));
        $this->assertInstanceOf('Boomstarter\Gift', $result[0]);
    }

    public function testUseCurl()
    {
        $api = new Boomstarter\API($this->shop_uuid, $this->shop_token);

        $api->useCurl();

        $this->assertInstanceOf('Boomstarter\RESTDriverCurl', $api->getTransport()->getDriver());
    }

    public function testUseStream()
    {
        $api = new Boomstarter\API($this->shop_uuid, $this->shop_token);

        $api->useStream();

        $this->assertInstanceOf('Boomstarter\RESTDriverStream', $api->getTransport()->getDriver());
    }

    public function testGetGiftsTotalCount()
    {
        $file_data = json_decode(file_get_contents(__DIR__ . '/api/v1.1/partners/gifts/response.json'), TRUE);
        $gifts = $file_data['gifts'];

        $expected = array(
            'gifts' => $gifts,
            '_metadata' => array(
                'total_count' => 100
            )
        );

        $api = $this->getMockedApi($this->shop_uuid, $this->shop_token, $expected);

        $result = $api->getGiftsAll();

        $this->assertInstanceOf('Boomstarter\GiftIterator', $result);
        $this->assertEquals(1, count($result));
        $this->assertInstanceOf('Boomstarter\Gift', $result[0]);
        $this->assertEquals($expected['_metadata']['total_count'], $result->getTotalCount());
    }

    public function testGetGiftsComplex()
    {
        $json = json_decode(file_get_contents(__DIR__ . '/api/v1.1/partners/gifts/response.json'), TRUE);
        $gifts = $json['gifts'];

        $expected = array(
            'gifts' => $gifts,
            '_metadata' => array(
                'total_count' => 100
            )
        );

        $api = $this->getMockedApi($this->shop_uuid, $this->shop_token, $expected);

        /* @var Boomstarter\GiftIterator */
        $result = $api->getGiftsAll();

        $this->assertInstanceOf('Boomstarter\GiftIterator', $result);
        $this->assertEquals(1, count($result));
        $this->assertEquals($expected['_metadata']['total_count'], $result->getTotalCount());

        /* @var $gift Boomstarter\Gift */
        foreach($result as $gift) {
            $this->assertInstanceOf('Boomstarter\Gift', $gift);
            $this->assertInstanceOf('Boomstarter\Location', $gift->location);
            $this->assertInstanceOf('Boomstarter\City', $gift->location->city);
            $this->assertInstanceOf('Boomstarter\Country', $gift->location->country);
            $this->assertEquals($json['gifts'][0]['uuid'], $gift->uuid);
            $this->assertEquals($json['gifts'][0]['name'], $gift->name);
            $this->assertEquals($json['gifts'][0]['pledged'], $gift->pledged);
            $this->assertEquals($json['gifts'][0]['location']['country']['id'], $gift->location->country->id);
            $this->assertEquals($json['gifts'][0]['location']['city']['id'], $gift->location->city->id);
            $this->assertEquals($json['gifts'][0]['location']['city']['name'], $gift->location->city->name);
            $this->assertEquals($json['gifts'][0]['location']['city']['slug'], $gift->location->city->slug);
            $this->assertEquals($json['gifts'][0]['owner']['email'], $gift->owner->email);
            $this->assertEquals('boomstarter@boomstarter.ru', $gift->owner->email);
        }
    }
}
