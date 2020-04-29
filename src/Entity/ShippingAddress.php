<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 1/9/20
 * Time: 11:53 AM
 */

namespace App\Entity;


class ShippingAddress
{
    public $city;
    public $state;
    public $zipCode;
    public $country;
    public $apt;
    public $street;

    public static function fromArray(array $array) :self
    {
        $shippingAddress = new self();
        $shippingAddress->city = $array['city']??null;
        $shippingAddress->state = $array['state']??null;
        $shippingAddress->street = $array['street']??null;
        $shippingAddress->apt = $array['apt']??null;
        $shippingAddress->zipCode = $array['zipCode']??null;

        return $shippingAddress;
    }

}