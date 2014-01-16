gifts-api
=========

PHP-Library to work with Boomstarter Gifts API.

Tested with PHP 5.3


## Примеры использования

### 1. Вывод списка подарков

Код товара (product_id) - Наименование (name)

    require_once('Boomstarter/API.php');

    $shop_uuid = 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1';
    $shop_token = 'c50267d4-d08a-4fff-ad2b-87746088188a';
    
    $api = new \Boomstarter\API($shop_uuid, $shop_token);
    
    $gifts = $api->getGiftsAll();
    
    foreach($gifts as $gift) {
        echo "product_id: {$gift->product_id} - name: {$gift->name}\n";
    }
    
результат

	UUID: 741f2a44-c438-45e8-bfba-daba60609060
	product_id: 128298
	name: Люстра 1172-6U

	UUID: cc7cf13d-a12a-486d-aebb-272360a5f197
	product_id: 35727
	name: Люстра WL11401-6CH
	

### 2. Запрос десяти подарков

Вывод общего количества (доступных), количества запрошенных (десять), списка подарков

    $api = new API('fcfdfc62-7c05-4642-8d43-d26b0c05b9e1', 'c50267d4-d08a-4fff-ad2b-87746088188a');
    
    $gifts = $api->getGiftsAll(1);
    
    echo "TotalCount: " . $gifts->getTotalCount() . "\n";
    echo "Count: " . $gifts->count() . "\n";
    echo "Gifts:\n";
    
    /* @var $gift Gift */
    foreach($gifts as $gift) {
        echo "\t" . "UUID: " . $gift->uuid . "\n";
        echo "\t" . "product_id: " . $gift->product_id . "\n";
        echo "\t" . "name: " . $gift->name . "\n";
        echo "\n";
    }

результат

    TotalCount: 2
    Count: 1
    Gifts:
	    UUID: 741f2a44-c438-45e8-bfba-daba60609060
	    product_id: 128298
	    name: Люстра 1172-6U

	    UUID: cc7cf13d-a12a-486d-aebb-272360a5f197
	    product_id: 35727
	    name: Люстра WL11401-6CH

    
## Класс API

    class API
        function __construct($shop_uuid, $shop_token);
        function getGiftsAll();
        function getGiftsPending();
        function getGiftsShipping();
        function getGiftsDelivered();

## Класс Gift

(подарок)

    class Gift
        uuid
        product_id
        name
        pledged
        pledged_cents
        comments
        delivery_state
        payout_id
        order_id
        owner
        location
        state
        zipcode
        region
        district
        city
        street
        house
        building
        construction
        apartment
        
        function order($order_id);
        function schedule($delivery_date);
        function setStateDelivery();
        
## Структура

![scheme](https://raw2.github.com/boomstarterru/gifts-api/master/doc/scheme.jpg)

