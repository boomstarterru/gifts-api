<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 14.01.14
 * Time: 21:58
 */

class Gift extends stdClass
{
    // Статусы подарков
    const STATUS_ALL = NULL;
    const STATUS_PENDING = 'pending';
    const STATUS_SHIPPING = 'shipping';
    const STATUS_DELIVERED = 'delivered';

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

    public function order()
    {
        //
    }

    public function schedule()
    {
        //
    }

    public function setState()
    {
        //
    }

    public function setTransport($transport)
    {
        $this->transport = $transport;
    }

    public function setProperties($properties)
    {
        foreach($properties as $name=>$value) {
            $this->$name = $value;
        }
        return $this;
    }
}

//$gift = new Gift();
//$gift->setProperties(array('order_id' => 123));
$properties = array('order_id' => 123);
// $gift = (Object)$properties;
var_dump($gift);
