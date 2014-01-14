gifts-api
=========

PHP-Library to work with Boomstarter Gifts API.

![scheme](https://raw2.github.com/boomstarterru/gifts-api/master/doc/scheme.jpg)


Пример использования:

(вывод списка подарков: Код товара (product_id) - Наименование (name))

    include('Boomstarter/API.php');

    $shop_uuid = 'fcfdfc62-7c05-4642-8d43-d26b0c05b9e1';
    $shop_token = 'c50267d4-d08a-4fff-ad2b-87746088188a';
    
    $api = new \Boomstarter\API($shop_uuid, $shop_token);
    
    $gifts = $api->getGiftsAll();
    
    foreach($gifts as $gift) {
        echo "product_id: {$gift->product_id} - name: {$gift->name}\n";
    }
