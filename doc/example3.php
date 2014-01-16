<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 16.01.14
 * Time: 14:33
 */

require_once('../Boomstarter/API.php');

$shop_uuid = 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX';
$shop_token = 'XXXXXXXXXXXXXXXXXXXXXX-X-XXXXXXXXX-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

$api = new \Boomstarter\API($shop_uuid, $shop_token);

try {

    $gifts = $api->getGiftsAll(10);

} catch (\Boomstarter\Exception $e) {

    echo get_class($e) . ': ' . $e->getMessage() . "\n";
    exit(1);
}
