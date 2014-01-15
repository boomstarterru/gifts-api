<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 09.12.13
 * Time: 18:20
 */
require_once __DIR__ . '/../Boomstarter/API.php';

use Boomstarter\Boomstarter;
use Boomstarter\StreamDriver;
/*
class APITest extends PHPUnit_Framework_TestCase
{
    protected $api = NULL;
    protected static $server = NULL;
    protected $api_url = 'http://localhost:8000';
    protected $shop_uuid = 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1';
    protected $shop_token = 'c50267d4-d08a-4fff-ad2b-87746088188a';

    public function setUp()
    {
        $this->api = new \Boomstarter\API($this->shop_uuid, $this->shop_token);
        $this->api->setAPIUrl($this->api_url);

        parent::setUp();
    }

    public static function setUpBeforeClass()
    {
        // local web server
        self::$server = self::startServer();
    }

    public static function tearDownAfterClass()
    {
        self::stopServer(self::$server);
    }

    public function testGetGiftsAll()
    {
        $result = $this->api->getGiftsAll();

        //$this->assertEquals('/api/v1.1/partners/gifts', parse_url($result['debug']['server']['REQUEST_URI'], PHP_URL_PATH));
        $this->assertTrue(is_array($result));
        $this->assertTrue(is_object($result[0]));
        $this->assertInstanceOf("Boomstarter\Gift", $result[0]);
        $this->assertObjectHasAttribute("pledged", $result[0]);

        var_dump($result[0]);
        //$this->assertTrue(is_array($result['_metadata']));
        //$this->assertEquals($this->shop_uuid, $result['debug']['get']['shop_uuid']);
        //$this->assertEquals($this->shop_token, $result['debug']['get']['shop_token']);
    }

    public function testGetPendingGifts()
    {
        $result = $this->bs->getPendingGifts();

        $this->assertEquals('/api/v1.1/partners/gifts/pending', parse_url($result['debug']['server']['REQUEST_URI'], PHP_URL_PATH));
        $this->assertTrue(is_array($result['gifts']));
        $this->assertTrue(is_array($result['_metadata']));
        $this->assertEquals($this->shop_uuid, $result['debug']['get']['shop_uuid']);
        $this->assertEquals($this->shop_token, $result['debug']['get']['shop_token']);
    }

    public function testGetShippingGifts()
    {
        $result = $this->bs->getShippingGifts();

        $this->assertEquals('/api/v1.1/partners/gifts/shipping', parse_url($result['debug']['server']['REQUEST_URI'], PHP_URL_PATH));
        $this->assertTrue(is_array($result['gifts']));
        $this->assertTrue(is_array($result['_metadata']));
        $this->assertEquals($this->shop_uuid, $result['debug']['get']['shop_uuid']);
        $this->assertEquals($this->shop_token, $result['debug']['get']['shop_token']);
    }

    public function testGetDeliveredGifts()
    {
        $result = $this->bs->getDeliveredGifts();

        $this->assertEquals('/api/v1.1/partners/gifts/delivered', parse_url($result['debug']['server']['REQUEST_URI'], PHP_URL_PATH));
        $this->assertTrue(is_array($result['gifts']));
        $this->assertTrue(is_array($result['_metadata']));
        $this->assertEquals($this->shop_uuid, $result['debug']['get']['shop_uuid']);
        $this->assertEquals($this->shop_token, $result['debug']['get']['shop_token']);
    }

    public function testOrderGift()
    {
        $uuid = '5b6a38b7-b555-43e6-8b00-45ea924b283d';
        $order_id = '10002223321222';

        $result = $this->bs->orderGift($uuid, $order_id);

        $decoded_post = json_decode($result['debug']['raw_post'], TRUE);

        $this->assertTrue(is_array($result));
        $this->assertEquals("/api/v1.1/partners/gifts/$uuid/order", parse_url($result['debug']['server']['REQUEST_URI'], PHP_URL_PATH));
        $this->assertEquals($this->shop_uuid, $decoded_post['shop_uuid']);
        $this->assertEquals($this->shop_token, $decoded_post['shop_token']);
        $this->assertEquals($order_id, $decoded_post['order_id']);
        $this->assertEquals($order_id, $result['order_id']);
    }

    public function testScheduleGift()
    {
        $uuid = '5b6a38b7-b555-43e6-8b00-45ea924b283d';
        $delivery_date = '2013-07-29';

        $result = $this->bs->scheduleGift($uuid, $delivery_date);

        $decoded_post = json_decode($result['debug']['raw_post'], TRUE);

        $this->assertEquals("/api/v1.1/partners/gifts/$uuid/schedule", parse_url($result['debug']['server']['REQUEST_URI'], PHP_URL_PATH));
        $this->assertEquals($this->shop_uuid, $decoded_post['shop_uuid']);
        $this->assertEquals($this->shop_token, $decoded_post['shop_token']);
        $this->assertEquals($delivery_date, $decoded_post['delivery_date']);
        $this->assertEquals($delivery_date, \DateTime::createFromFormat(DATE_ATOM, $result['delivery_date'])->format("Y-m-d"));
    }

    public function testDeliveryStateGift()
    {
        $uuid = '5b6a38b7-b555-43e6-8b00-45ea924b283d';
        $delivery_state = 'delivery';

        $result = $this->bs->deliveryStateGift($uuid, $delivery_state);

        $decoded_put = json_decode($result['debug']['raw_put'], TRUE);

        $this->assertEquals("/api/v1.1/partners/gifts/$uuid/delivery_state", parse_url($result['debug']['server']['REQUEST_URI'], PHP_URL_PATH));
        $this->assertEquals($this->shop_uuid, $decoded_put['shop_uuid']);
        $this->assertEquals($this->shop_token, $decoded_put['shop_token']);
        $this->assertEquals($delivery_state, $decoded_put['delivery_state']);
        $this->assertEquals('delivered', $result['delivery_state']);
    }

    public function testStreamDriver()
    {
        $this->bs->setRESTDriver(new StreamDriver());

        $result = $this->bs->getAllGifts();

        $expected = file_get_contents(__DIR__ . '/api/v1.1/partners/gifts/response.json');
        $expected = json_decode($expected, TRUE);

        $this->assertTrue(array_key_exists('gifts', $expected));

        unset($result['debug']);
        $this->assertEquals($expected, $result);
    }

    public function testLimit()
    {
        $this->bs->setLimit(7);
        $result = $this->bs->getAllGifts();

        $this->assertEquals(7, (int)$result['debug']['request']['limit']);
    }

    public function testOffset()
    {
        $this->bs->setOffset(2);
        $result = $this->bs->getAllGifts();

        $this->assertEquals(2, (int)$result['debug']['request']['offset']);
    }

    private function startServer()
    {
        $descriptor = array();

        $server = proc_open('php -S localhost:8000 router.php', $descriptor, $pipes, __DIR__);
        sleep(2);

        return $server;
    }

    private function stopServer($server)
    {
        $status = proc_get_status($server);

        if ($status['running'] == true) {
            $ppid = $status['pid'];

            $pids = preg_split('/\s+/', `ps -o pid --no-heading --ppid $ppid`);

            foreach($pids as $pid) {
                if(is_numeric($pid)) {
                    posix_kill($pid, 9); //9 is the SIGKILL signal
                }
            }
        }

        proc_terminate($server);
        proc_close($server);
    }
}
*/