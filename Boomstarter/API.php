<?php
/**
 * Библиотека для работы с подарками Boomstarter
 * Boomstarter Gifts API
 *
 * @docs https://boomstarter.ru/gifts/
 * @api http://docs.boomstarter.apiary.io/
 * @url https://github.com/boomstarterru/gifts-api
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
 * Interface IRestDriver Интерфейс для REST-драйверов
 * @package Boomstarter
 */
interface IRestDriver
{
    function put($url, $data);
    function post($url, $data);
    function get($url, $data);
    function delete($url, $data);
}


/**
 * Class CurlRequest
 * Драйвер для HTTP-запросов
 *
 * @package Boomstarter
 */
class HttpRequestCurl implements IHttpRequest
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


/**
 * Class CurlRequest
 * Драйвер для HTTP-запросов
 *
 * @package Boomstarter
 */
class HttpRequestStream implements IHttpRequest
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
class RestDriverCurl implements IRestDriver
{
    const USER_AGENT = 'Boomstarter Gifts PHP library; Curl';

    public function getRequest($url)
    {
        return new HttpRequestCurl($url);
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
        $curl->setOption(CURLOPT_USERAGENT, self::USER_AGENT);
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
        $curl->setOption(CURLOPT_USERAGENT, self::USER_AGENT);
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
        $curl->setOption(CURLOPT_USERAGENT, self::USER_AGENT);
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
        $curl->setOption(CURLOPT_USERAGENT, self::USER_AGENT);
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
class RestDriverStream implements IRestDriver
{
    const USER_AGENT = 'Boomstarter Gifts PHP library; Stream';

    public function getRequest($url)
    {
        return new HttpRequestStream($url);
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
        $stream = $this->getRequest($url . '?' . http_build_query($data));
        $stream->setOption('method', "GET");
        $stream->setOption('user_agent', self::USER_AGENT);
        $stream->setOption('header', 'Content-Type: application/json');
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
        $stream->setOption('user_agent', self::USER_AGENT);
        $stream->setOption('header', 'Content-Type: application/json');
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
        $stream->setOption('user_agent', self::USER_AGENT);
        $stream->setOption('header', 'Content-Type: application/json');
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
        $stream->setOption('method', "DELETE");
        $stream->setOption('content', json_encode($data));
        $stream->setOption('user_agent', self::USER_AGENT);
        $stream->setOption('header', 'Content-Type: application/json');
        $response = $stream->execute();

        return $response;
    }
}


/**
 * Class RESTDriverFactory
 * Фабрика драйверов
 *
 * @package Boomstarter
 */
class RestDriverFactory
{
    /**
     * Автоматически выбирает подходящий драйвер
     *
     * @return RestDriverCurl|RestDriverStream
     */
    public static function getAutomatic()
    {
        if (function_exists('curl_exec')) {
            $driver = new RestDriverCurl();
        } else {
            $driver = new RestDriverStream();
        }

        return $driver;
    }

    /**
     * @return RestDriverCurl
     */
    public static function getCurl()
    {
        return new RestDriverCurl();
    }

    /**
     * @return RestDriverStream
     */
    public static function getStream()
    {
        return new RestDriverStream();
    }
}


/**
 * Class Transport
 * Вызов REST методов
 * @none Использует несколько драйверов
 *
 * @package Boomstarter
 */
class Transport
{
    /* @var IRestDriver */
    private $driver = NULL;
    /* @var string */
    private $shop_uuid = NULL;
    /* @var string */
    private $shop_token = NULL;
    /* @var string */
    private $api_url = 'https://boomstarter.ru/api/v1.1/partners';

    function __construct()
    {
        $this->driver = RestDriverFactory::getAutomatic();
    }

    /**
     * @param $shop_uuid string
     * @return $this
     */
    public function setShopUUID($shop_uuid)
    {
        $this->shop_uuid = $shop_uuid;
        return $this;
    }

    /**
     * @param $shop_token string
     * @return $this
     */
    public function setShopToken($shop_token)
    {
        $this->shop_token = $shop_token;
        return $this;
    }

    /**
     * @param $api_url string
     * @return $this
     */
    public function setApiUrl($api_url)
    {
        $this->api_url = $api_url;
        return $this;
    }

    /**
     * REST-метод GET
     *
     * @param $url string URL
     * @param $data array параметры
     * @return array
     */
    public function get($url, $data)
    {
        $data["shop_uuid"] = $this->shop_uuid;
        $data["shop_token"] = $this->shop_token;

        $response = $this->getDriver()->get($this->api_url . $url, $data);
        $result = json_decode($response, TRUE);

        return $result;
    }

    /**
     * REST-метод POST
     *
     * @param $url string URL
     * @param $data array параметры
     * @return array
     */
    public function post($url, $data)
    {
        $data["shop_uuid"] = $this->shop_uuid;
        $data["shop_token"] = $this->shop_token;

        $response = $this->getDriver()->post($this->api_url . $url, $data);
        $result = json_decode($response, TRUE);

        return $result;
    }

    /**
     * REST-метод PUT
     *
     * @param $url string URL
     * @param $data array параметры
     * @return array
     */
    public function put($url, $data)
    {
        $data["shop_uuid"] = $this->shop_uuid;
        $data["shop_token"] = $this->shop_token;

        $response = $this->getDriver()->put($this->api_url . $url, $data);
        $result = json_decode($response, TRUE);

        return $result;
    }

    /**
     * REST-метод DELETE
     *
     * @param $url string URL
     * @param $data array параметры
     * @return array
     */
    public function delete($url, $data)
    {
        $data["shop_uuid"] = $this->shop_uuid;
        $data["shop_token"] = $this->shop_token;

        $response = $this->getDriver()->delete($this->api_url . $url, $data);
        $result = json_decode($response, TRUE);

        return $result;
    }

    /**
     * Использовать Curl драйвер
     *
     * @return $this
     */
    public function useCurl()
    {
        $this->driver = RestDriverFactory::getCurl();
        return $this;
    }

    /**
     * Использовать Stream драйвер
     *
     * @return $this
     */
    public function useStream()
    {
        $this->driver = RestDriverFactory::getStream();
        return $this;
    }

    /**
     * @reserved
     * @return IRestDriver|RestDriverCurl|RestDriverStream
     */
    public function getDriver()
    {
        return $this->driver;
    }
}


/**
 * Class GiftIterator
 * Список подарков
 *
 * @package Boomstarter
 */
class GiftIterator extends \ArrayIterator
{
    /* @var int */
    private $total_count = 0;

    /**
     * @return int количество подарков всего на сеовере
     */
    public function getTotalCount()
    {
        return $this->total_count;
    }

    /**
     * @param $total_count Количество подарков всего на сеовере.
     * @none Используется при инициализации списка.
     * @return $this
     */
    public function setTotalCount($total_count)
    {
        $this->total_count = $total_count;
        return $this;
    }
}


/**
 * Class GiftFactory
 * Фабрика подарков
 *
 * @package Boomstarter
 */
class GiftFactory
{
    /**
     * Возвращает объект подарка.
     * Устанавливает свойства $properties.
     * Устанавливает транспорт $transport.
     *
     * @param $transport Transport Транспорт для вызова REST API
     * @param $properties array Массив свойств. Ключ=>значение
     * @return Gift
     */
    public static function getGift($transport, $properties)
    {
        $gift = new Gift($transport);

        $gift->setProperties($properties);

        return $gift;
    }
}


class Country
{
    /* @property int */
    public $id = NULL; // 20
    /* @property string */
    public $name = ""; // "Россия"

    public function setProperties($properties)
    {
        if (!$properties) {
            return $this;
        }

        foreach($properties as $name=>$value) {
            $this->$name = $value;
        }

        return $this;
    }
}


class City
{
    /* @property int */
    public $id = NULL; // 49849
    /* @property string */
    public $name = ""; // "Москва"
    /* @property string */
    public $slug = ""; // "moscow-ru"

    public function setProperties($properties)
    {
        if (!$properties) {
            return $this;
        }

        foreach($properties as $name=>$value) {
            $this->$name = $value;
        }

        return $this;
    }
}


class Location
{
    /* @property Country */
    public $country = NULL;
    /* @property City */
    public $city = NULL;

    public function setProperties($properties)
    {
        if (!$properties) {
            return $this;
        }

        foreach($properties as $name=>$value) {

            if (strcmp($name, 'city') == 0) {

                $city = new City();
                $city->setProperties($value);
                $this->city = $city;

            } elseif (strcmp($name, 'country') == 0) {

                $country = new Country();
                $country->setProperties($value);
                $this->country = $country;

            } else {
                $this->$name = $value;
            }
        }

        return $this;
    }
}


class Owner
{
    /* @property string */
    public $email = ""; // "boomstarter@boomstarter.ru"
    /* @property string */
    public $phone = ""; // "79853867016"
    /* @property string */
    public $first_name = ""; // "Ivan"
    /* @property string */
    public $last_name = ""; // "Ivanov"

    public function setProperties($properties)
    {
        if (!$properties) {
            return $this;
        }

        foreach($properties as $name=>$value) {
            $this->$name = $value;
        }

        return $this;
    }
}


/**
 * Class API
 * API подарков
 *
 * @package Boomstarter
 */
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
     * Возвращает список подарков
     * все с сортировкой по дате завешения сбора средств.
     *
     * @param $status NULL|'pending'|'shipping'|'delivered' Фильтр по статусу. NULL-все.
     * @param $limit int Количество. Сколько подарков вернуть за раз
     * @param $offset int Отступ. Сколько подарков пропустить с начала
     * @return GiftIterator  Возвращает массив подарков
     */
    private function getGifts($status, $limit, $offset)
    {
        $result = new GiftIterator();
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
        $response = $this->getTransport()->get($url, $data);
        $items = $response['gifts'];

        foreach($items as $item) {
            $result[] = GiftFactory::getGift($this->getTransport(), $item);
        }

        $result->setTotalCount($response['_metadata']['total_count']);

        return $result;
    }

    /**
     * Возвращает список подарков без фильтра по доставке. Т.е. все.
     *
     * @param int $limit int Количество. Сколько подарков вернуть за раз
     * @param int $offset int Отступ. Сколько подарков пропустить с начала
     * @return GiftIterator Возвращает массив подарков
     */
    public function getGiftsAll($limit=100, $offset=0)
    {
        return $this->getGifts(NULL, $limit, $offset);
    }

    /**
     * Возвращает список подарков в ожидании доставки.
     *
     * @param int $limit int Количество. Сколько подарков вернуть за раз
     * @param int $offset int Отступ. Сколько подарков пропустить с начала
     * @return GiftIterator Возвращает массив подарков
     */
    public function getGiftsPending($limit=100, $offset=0)
    {
        return $this->getGifts('pending', $limit, $offset);
    }

    /**
     * Возвращает список подарков со статусом "в доставке".
     *
     * @param int $limit int Количество. Сколько подарков вернуть за раз
     * @param int $offset int Отступ. Сколько подарков пропустить с начала
     * @return GiftIterator Возвращает массив подарков
     */
    public function getGiftsShipping($limit=100, $offset=0)
    {
        return $this->getGifts('shipping', $limit, $offset);
    }

    /**
     * Возвращает список доставленных подарков.
     *
     * @param int $limit int Количество. Сколько подарков вернуть за раз
     * @param int $offset int Отступ. Сколько подарков пропустить с начала
     * @return GiftIterator Возвращает массив подарков
     */
    public function getGiftsDelivered($limit=100, $offset=0)
    {
        return $this->getGifts('delivered', $limit, $offset);
    }

    /**
     * Переключиться на использование curl для HTTP-запросов.
     *
     * @return $this
     */
    public function useCurl()
    {
        $this->getTransport()->useCurl();
        return $this;
    }

    /**
     * Переключиться на использование stream_get_contents() для HTTP-запросов.
     *
     * @return $this
     */
    public function useStream()
    {
        $this->getTransport()->useStream();
        return $this;
    }

    /**
     * @reserved
     * @return Transport
     */
    public function getTransport()
    {
        return $this->transport;
    }
}

/**
 * Class Gift
 * Подарок
 *
 * @package Boomstarter
 */
class Gift
{
    /* @property int */
    public $pledged = NULL;    // 690.0
    /* @property string */
    public $product_id = NULL; // 25330
    /* @property Location */
    public $location = NULL; // Location
    /* @property Owner */
    public $owner = NULL; // Owner
    /* @property string */
    public $payout_id = NULL;
    /* @property string */
    public $state = ""; // "success_funded"
    /* @property string */
    public $zipcode = NULL;
    /* @property string */
    public $comments = "";
    /* @property string */
    public $uuid = ""; // "5b6a38b7-b555-43e6-8b00-45ea924b283d"
    /* @property string */
    public $name = ""; // "Чехол ArtWizz SeeJacket Alu Anthrazit для iPhone4/4S (AZ515AT)"
    /* @property int */
    public $pledged_cents = NULL; // 69000
    /* @property string */
    public $delivery_state = ""; // "none"
    /* @property string */
    public $region = NULL;
    /* @property string */
    public $district = NULL;
    /* @property string */
    public $city = NULL;
    /* @property string */
    public $street = ""; // "awdawd"
    /* @property string */
    public $house = NULL;
    /* @property string */
    public $building = NULL;
    /* @property string */
    public $construction = NULL;
    /* @property string */
    public $apartment = NULL;
    /* @property int */
    public $order_id = NULL;
    /* @property string */
    public $delivery_date = NULL;

    /* @var Transport */
    private $transport = NULL;

    function __construct($transport)
    {
        $this->transport = $transport;
    }

    public function setProperties($properties)
    {
        foreach($properties as $name=>$value) {

            if (strcmp($name, 'owner') == 0) {

                $owner = new Owner();
                $owner->setProperties($value);
                $this->owner = $owner;

            } elseif (strcmp($name, 'location') == 0) {

                $location = new Location();
                $location->setProperties($value);
                $this->location = $location;

            } else {
                $this->$name = $value;
            }
        }

        return $this;
    }

    /**
     * Подтверждение подарка с передачей ID-заказа магазина.
     *
     * @param $order_id string номер заказа
     * @return Gift
     */
    public function order($order_id)
    {
        $url = "/gifts/{$this->uuid}/order";

        $data = array(
            "order_id" => $order_id
        );

        $result = $this->transport->post($url, $data);

        $this->setProperties($result);

        return $this;
    }

    /**
     * Передача времени или даты доставки подарка.
     *
     * @param $delivery_date string|\DateTime Дата доставки. В любом формате поддерживаемом DateTime()
     * @return Gift
     */
    public function schedule($delivery_date)
    {
        // validate
        $datetime = $delivery_date instanceof \DateTime ? $delivery_date : new \DateTime($delivery_date);

        $url = "/gifts/{$this->uuid}/schedule";

        $data = array(
            //  ISO 8601
            "delivery_date" => $datetime->format(\DateTime::ISO8601)
        );

        $result = $this->transport->post($url, $data);

        $this->setProperties($result);

        return $this;
    }

    /**
     * Завершение доставки, клиенту вручили подарок.
     *
     * @param $delivery_state 'delivery' Параметр состояния доставки. (delivery - подарок доставлен)
     * @return Gift
     * @throws Exception при некорректном $delivery_state
     */
    private function setState($delivery_state)
    {
        // validate
        if ($delivery_state != 'delivery') {
            throw new Exception("Unsupported delivery state: '{$delivery_state}'. Expected 'delivery'");
        }

        $url = "/gifts/{$this->uuid}/delivery_state";

        $data = array(
            "delivery_state" => $delivery_state
        );

        $result = $this->transport->put($url, $data);

        $this->setProperties($result);

        return $this;
    }

    /**
     * Завершение доставки, клиенту вручили подарок.
     *
     * @return mixed
     */
    public function setStateDelivery()
    {
        return $this->setState('delivery');
    }
}

// TODO названия методов Gift
