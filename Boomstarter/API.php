<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 14.01.14
 * Time: 20:41
 */

namespace Boomstarter;


class Exception extends \Exception {}
class DriverException extends Exception {}


interface IHttpRequest
{
    public function setOption($name, $value);
    public function execute();
    public function getInfo($name);
    public function close();
}


/**
 * Interface IRESTDriver Интерфейс для REST-драйверов
 * @package Boomstarter
 */
interface IRESTDriver
{
    function put($url, $data);
    function post($url, $data);
    function get($url, $data);
    function delete($url, $data);
}


class CurlRequest implements IHttpRequest
{
    /* @var mixed */
    private $handle = NULL;

    public function __construct($url)
    {
        $this->handle = curl_init($url);
    }

    public function setOption($name, $value)
    {
        curl_setopt($this->handle, $name, $value);
    }

    public function execute()
    {
        $response = curl_exec($this->handle);

        if ($response === FALSE) {
            $info = curl_getinfo($this->handle);
            throw new DriverException('Error occurred during curl exec. Additional info: ' . var_export($info));
        }

        return $response;
    }

    public function getInfo($name)
    {
        return curl_getinfo($this->handle, $name);
    }

    public function close()
    {
        curl_close($this->handle);
    }
}


class StreamRequest implements IHttpRequest
{
    /* @var mixed */
    private $handle = NULL;
    /* @var string */
    private $url = NULL;
    /* @var array */
    private $options = array();

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    public function execute()
    {
        $options = array('http' => $this->options);
        $context = stream_context_create($options);
        $this->handle = fopen($this->url, 'rb', FALSE, $context);

        if (!$this->handle) {
            throw new DriverException("Error occurred during open stream: {$this->url}");
        }

        $response = stream_get_contents($this->handle);

        $this->close();

        if ($response === FALSE) {
            throw new DriverException("Problem when reading data from: {$this->url}");
        }

        return $response;
    }

    public function getInfo($name)
    {
        return array();
    }

    public function close()
    {
        fclose($this->handle);
    }
}


/**
 * Class CurlDriver
 * Драйвер для работы с REST API через curl
 *
 * @package Boomstarter
 */
class RESTDriverCurl implements IRESTDriver
{
    public function getRequest($url)
    {
        return new CurlRequest($url);
    }

    /**
     * Метод GET
     *
     * @param $url string
     * @param $data array
     * @return string
     * @throws \Boomstarter\DriverException
     */
    public function get($url, $data)
    {
        $curl = $this->getRequest($url . '?' . http_build_query($data));
        $curl->setOption(CURLOPT_RETURNTRANSFER, TRUE);
        $curl->setOption(CURLOPT_HEADER, FALSE);
        $curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = $curl->execute();

        return $response;
    }

    /**
     * Метод POST
     *
     * @param $url string
     * @param $data array
     * @return string
     * @throws \Boomstarter\DriverException
     */
    public function post($url, $data)
    {
        $curl = $this->getRequest($url);
        $curl->setOption(CURLOPT_RETURNTRANSFER, TRUE);
        $curl->setOption(CURLOPT_HEADER, FALSE);
        $curl->setOption(CURLOPT_POST, TRUE);
        $curl->setOption(CURLOPT_POSTFIELDS, json_encode($data));
        $curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = $curl->execute();

        return $response;
    }

    /**
     * Метод PUT
     *
     * @param $url string
     * @param $data array
     * @return string
     * @throws \Boomstarter\DriverException
     */
    public function put($url, $data)
    {
        $curl = $this->getRequest($url);
        $curl->setOption(CURLOPT_RETURNTRANSFER, TRUE);
        $curl->setOption(CURLOPT_HEADER, FALSE);
        $curl->setOption(CURLOPT_POST, TRUE);
        $curl->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
        $curl->setOption(CURLOPT_POSTFIELDS, json_encode($data));
        $curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = $curl->execute();

        return $response;
    }

    /**
     * Метод DELETE
     *
     * @param $url string
     * @param $data array
     * @return string
     * @throws \Boomstarter\DriverException
     */
    public function delete($url, $data)
    {
        $curl = $this->getRequest($url);
        $curl->setOption(CURLOPT_RETURNTRANSFER, TRUE);
        $curl->setOption(CURLOPT_HEADER, FALSE);
        $curl->setOption(CURLOPT_POST, TRUE);
        $curl->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $curl->setOption(CURLOPT_POSTFIELDS, json_encode($data));
        $curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = $curl->execute();

        return $response;
    }
}


/**
 * Class StreamDriver
 * Драйвер для работы с REST API через stream_get_contents
 * Для некоторых провайдеров без поддержки curl.
 * @package Boomstarter
 */
class RESTDriverStream implements IRESTDriver
{
    public function getRequest($url)
    {
        return new CurlRequest($url);
    }

    /**
     * Метод GET
     *
     * @param $url string
     * @param $data array
     * @return string
     * @throws \Boomstarter\DriverException
     */
    public function get($url, $data)
    {
        $stream = $this->getRequest($url);;
        $stream->setOption('method', "GET");
        $stream->setOption('content', $data);
        $stream->setOption('header', 'Content-Type: application/json\r\n');
        $response = $stream->execute();

        return $response;
    }

    /**
     * Метод POST
     *
     * @param $url string
     * @param $data array
     * @return string
     * @throws \Boomstarter\DriverException
     */
    public function post($url, $data)
    {
        $stream = $this->getRequest($url);
        $stream->setOption('method', "POST");
        $stream->setOption('content', json_encode($data));
        $stream->setOption('header', 'Content-Type: application/json\r\n');
        $response = $stream->execute();

        return $response;
    }

    /**
     * Метод PUT
     *
     * @param $url string
     * @param $data array
     * @return string
     * @throws \Boomstarter\DriverException
     */
    public function put($url, $data)
    {
        $stream = $this->getRequest($url);
        $stream->setOption('method', "PUT");
        $stream->setOption('content', json_encode($data));
        $stream->setOption('header', 'Content-Type: application/json\r\n');
        $response = $stream->execute();

        return $response;
    }

    /**
     * Метод DELETE
     *
     * @param $url string
     * @param $data array
     * @return string
     * @throws \Boomstarter\DriverException
     */
    public function delete($url, $data)
    {
        $stream = $this->getRequest($url);
        $stream->setOption('method', "GET");
        $stream->setOption('content', json_encode($data));
        $stream->setOption('header', 'Content-Type: application/json\r\n');
        $response = $stream->execute();

        return $response;
    }
}


class RESTDriverFactory
{
    public static function getAutomatic()
    {
        if (function_exists('curl_exec')) {
            $driver = new RESTDriverCurl();
        } else {
            $driver = new RESTDriverStream();
        }

        return $driver;
    }

    public static function getCurl()
    {
        return new RESTDriverCurl();
    }

    public static function getStream()
    {
        return new RESTDriverStream();
    }
}


class Transport
{
    /* @var IRESTDriver */
    private $driver = NULL;
    /* @var string */
    private $shop_uuid = NULL;
    /* @var string */
    private $shop_token = NULL;
    /* @var string */
    private $api_url = 'https://boomstarter.ru/api/v1.1/partners';

    function __construct()
    {
        $this->driver = RESTDriverFactory::getAutomatic();
    }

    public function setShopUUID($shop_uuid)
    {
        $this->shop_uuid = $shop_uuid;
        return $this;
    }

    public function setShopToken($shop_token)
    {
        $this->shop_token = $shop_token;
        return $this;
    }

    public function setApiUrl($api_url)
    {
        $this->api_url = $api_url;
        return $this;
    }

    public function get($url, $data)
    {
        $data["shop_uuid"] = $this->shop_uuid;
        $data["shop_token"] = $this->shop_token;

        $response = $this->getDriver()->get($this->api_url . $url, $data);
        $result = json_decode($response, TRUE);

        return $result;
    }

    public function post($url, $data)
    {
        $data["shop_uuid"] = $this->shop_uuid;
        $data["shop_token"] = $this->shop_token;

        $response = $this->getDriver()->post($this->api_url . $url, $data);
        $result = json_decode($response, TRUE);

        return $result;
    }

    public function put($url, $data)
    {
        $data["shop_uuid"] = $this->shop_uuid;
        $data["shop_token"] = $this->shop_token;

        $response = $this->getDriver()->put($this->api_url . $url, $data);
        $result = json_decode($response, TRUE);

        return $result;
    }

    public function delete($url, $data)
    {
        $data["shop_uuid"] = $this->shop_uuid;
        $data["shop_token"] = $this->shop_token;

        $response = $this->getDriver()->delete($this->api_url . $url, $data);
        $result = json_decode($response, TRUE);

        return $result;
    }

    public function useCurl()
    {
        $this->driver = RESTDriverFactory::getCurl();
        return $this;
    }

    public function useStream()
    {
        $this->driver = RESTDriverFactory::getStream();
        return $this;
    }

    public function getDriver()
    {
        return $this->driver;
    }
}


class GiftFactory
{
    public static function getGift($transport, $properties)
    {
        $gift = new Gift($transport);
        
        foreach($properties as $name=>$value) {
            $gift->$name = $value;
        }
        
        return $gift;
    }
}


class Country
{
    /* @var int */
    public $id = NULL; // 20
    /* @var string */
    public $name = ""; // "Россия"
}


class City
{
    /* @var int */
    public $id = NULL; // 49849
    /* @var string */
    public $name = ""; // "Москва"
    /* @var string */
    public $slug = ""; // "moscow-ru"
}


class Location
{
    /* @var Country */
    public $country = NULL;
    /* @var City */
    public $city = NULL;
}


class Owner
{
    /* @var string */
    public $email = ""; // "boomstarter@boomstarter.ru"
    /* @var string */
    public $phone = ""; // "79853867016"
    /* @var string */
    public $first_name = ""; // "Ivan"
    /* @var string */
    public $last_name = ""; // "Ivanov"
}


class API
{
    /* @var Transport */
    private $transport = NULL;

    function __construct($shop_uuid, $shop_token)
    {
        $this->transport = new Transport();
        $this->transport->setShopUUID($shop_uuid);
        $this->transport->setShopToken($shop_token);
    }

    /**
     * Возвращает список подарков без фильтра по доставке,
     * все с сортировкой по дате завешения сбора средств.
     *
     * @param $status NULL|'pending'|'shipping'|'delivered' статус
     * @param $limit int Количество. Сколько подарков вернуть за раз
     * @param $offset int Отступ. Сколько подарков пропустить с начала
     * @return array(Gift)  Возвращает массив подарков
     */
    private function getGifts($status, $limit, $offset)
    {
        $result = array();
        $data = array();

        // limit, offset
        if ($limit) {
            $data['limit'] = $limit;
        }

        if ($offset) {
            $data['offset'] = $offset;
        }

        // url
        if ($status) {
            $url = '/gifts' . '/' . $status;
        } else {
            $url = '/gifts';
        }

        // request
        $array = $this->transport->get($url, $data);
        $items = $array['gifts'];

        foreach($items as $item) {
            $result[] = GiftFactory::getGift($this->transport, $item);
        }

        return $result;
    }

    public function getGiftsAll($limit=100, $offset=0)
    {
        return $this->getGifts(NULL, $limit, $offset);
    }

    public function getGiftsPending($limit=100, $offset=0)
    {
        return $this->getGifts('pending', $limit, $offset);
    }

    public function getGiftsShipping($limit=100, $offset=0)
    {
        return $this->getGifts('shipping', $limit, $offset);
    }

    public function getGiftsDelivered($limit=100, $offset=0)
    {
        return $this->getGifts('delivered', $limit, $offset);
    }

    public function useCurl()
    {
        $this->transport->useCurl();
        return $this;
    }

    public function useStream()
    {
        $this->transport->useStream();
        return $this;
    }

    public function setAPIUrl($url)
    {
        $this->transport->setApiUrl($url);
        return $this;
    }
}


class Gift
{
    /* @var int */
    public $pledged = NULL;    // 690.0
    /* @var string */
    public $product_id = NULL; // 25330
    /* @var Location */
    public $location = NULL; // Location
    /* @var Owner */
    public $owner = NULL; // Owner
    /* @var string */
    public $payout_id = NULL;
    /* @var string */
    public $state = ""; // "success_funded"
    /* @var string */
    public $zipcode = NULL;
    /* @var string */
    public $comments = "";
    /* @var string */
    public $uuid = ""; // "5b6a38b7-b555-43e6-8b00-45ea924b283d"
    /* @var string */
    public $name = ""; // "Чехол ArtWizz SeeJacket Alu Anthrazit для iPhone4/4S (AZ515AT)"
    /* @var int */
    public $pledged_cents = NULL; // 69000
    /* @var string */
    public $delivery_state = ""; // "none"
    /* @var string */
    public $region = NULL;
    /* @var string */
    public $district = NULL;
    /* @var string */
    public $city = NULL;
    /* @var string */
    public $street = ""; // "awdawd"
    /* @var string */
    public $house = NULL;
    /* @var string */
    public $building = NULL;
    /* @var string */
    public $construction = NULL;
    /* @var string */
    public $apartment = NULL;
    /* @var int */
    public $order_id = NULL;

    /* @var Transport */
    private $transport = NULL;

    function __construct($transport)
    {
        $this->transport = $transport;
    }

    public function order($order_id)
    {
        $url = "/gifts/{$this->uuid}/order";

        $data = array(
            "order_id" => $order_id
        );

        $result = $this->transport->post($url, $data);

        return $result;
    }

    public function schedule($delivery_date)
    {
        $url = "/gifts/{$this->uuid}/schedule";

        $data = array(
            "delivery_date" => $delivery_date
        );

        $result = $this->transport->post($url, $data);

        return $result;
    }

    public function setState($delivery_state)
    {
        $url = "/gifts/{$this->uuid}/delivery_state";

        $data = array(
            "delivery_state" => $delivery_state
        );

        $result = $this->transport->put($url, $data);

        return $result;
    }
}

// TODO что с Gift::location
// TODO что с Gift::owner
// TODO что с Gift::location:country
// TODO что с Gift::location:city
// TODO тесты транспорта
// TODO как отдавать _metadata.total_count
