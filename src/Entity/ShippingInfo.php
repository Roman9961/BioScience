<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Embeddable()*/
class ShippingInfo
{
    /** @ORM\Column(type = "string", nullable=true) */
    private $name;

    /** @ORM\Column(type = "string", nullable=true) */
    private $apt;

    /** @ORM\Column(type = "string", nullable=true) */
    private $street;

    /** @ORM\Column(type = "string", nullable=true) */
    private $city;

    /** @ORM\Column(type = "string", nullable=true) */
    private $state;

    /** @ORM\Column(type = "string", nullable=true) */
    private $country;

    /** @ORM\Column(type = "string", nullable=true) */
    private $zipCode;

    /**
     * @return mixed
     */
    public function getApt()
    {
        return $this->apt;
    }

    /**
     * @param mixed $apt
     */
    public function setApt($apt)
    {
        $this->apt = $apt;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param mixed $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


}