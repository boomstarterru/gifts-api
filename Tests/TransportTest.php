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

    public function _testGet()
    {
        $transport = new Boomstarter\Transport();

        $url = 'https://boomstarter.ru/api/v1.1/partners/gifts';

        $data = array(
            'shop_uuid' => 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1',
            'shop_token' => 'c50267d4-d08a-4fff-ad2b-87746088188a',
            'limit' => 100,
            'offset' => 0
        );

        $result = $transport->get($url, $data);

        $this->assertTrue(is_array($result));
    }
}
