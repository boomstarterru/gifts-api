<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 16.01.14
 * Time: 14:33
 */

require_once('../Boomstarter/API.php');

// settings
$shop_uuid = 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX';
$shop_token = 'XXXXXXXXXXXXXXXXXXXXXX-X-XXXXXXXXX-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

// init
$api = new \Boomstarter\API($shop_uuid, $shop_token);

// query
$gifts = $api->getGiftsAll(10);

// result
echo "TotalCount: " . $gifts->getTotalCount() . "\n";
echo "Count: " . $gifts->count() . "\n";
echo "Gifts:\n";

/* @var $gift Boomstarter\Gift */
foreach($gifts as $gift) {
    echo "\t" . "UUID: " . $gift->uuid . "\n";
    echo "\t" . "product_id: " . $gift->product_id . "\n";
    echo "\t" . "name: " . $gift->name . "\n";
    echo "\n";
}
