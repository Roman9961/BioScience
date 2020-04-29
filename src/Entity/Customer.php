<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomerRepository")
 * @Table(name="customer",indexes={@Index(name="search_idx", columns={"order_id", "email", "message_id"})})
 */
class Customer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customer_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $messageId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $orderId;

    /**
     * @ORM\Embedded(class="App\Entity\ShippingInfo")
     */
    private $shipping_info;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $grade;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $feedback;

    /**
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    private $isRequested;

    /**
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    private $isSended;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $orderDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $feedbackDate;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product")
     */
    private $product;

    public function __construct()
    {
        $this->product = new ArrayCollection();
        $this->shipping_info = new ShippingInfo();
        $this->isRequested = false;
        $this->isSended = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerId(): ?string
    {
        return $this->customer_id;
    }

    public function setCustomerId(string $customer_id): self
    {
        $this->customer_id = $customer_id;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(string $order_id): self
    {
        $this->orderId = $order_id;

        return $this;
    }

    public function getShippingInfo()
    {
        return $this->shipping_info;
    }

    public function setShippingInfo(ShippingAddress $shippingAddress): self
    {
        $this->shipping_info->setApt($shippingAddress->apt);
        $this->shipping_info->setCity($shippingAddress->city);
        $this->shipping_info->setState($shippingAddress->state);
        $this->shipping_info->setCountry($shippingAddress->country);
        $this->shipping_info->setZipCode($shippingAddress->zipCode);
        $this->shipping_info->setStreet($shippingAddress->street);

        return $this;
    }

    public function getGrade(): ?int
    {
        return $this->grade;
    }

    public function setGrade(int $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @param mixed $message_id
     */
    public function setMessageId($message_id): void
    {
        $this->messageId = $message_id;
    }

    /**
     * @return mixed
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * @param mixed $feedback
     */
    public function setFeedback($feedback): void
    {
        $this->feedback = $feedback;
    }

    /**
     * @return mixed
     */
    public function getIsRequested()
    {
        return $this->isRequested;
    }

    /**
     * @param mixed $isRequested
     */
    public function setIsRequested($isRequested): void
    {
        $this->isRequested = $isRequested;
    }

    /**
     * @return Product[]
     */
    public function getProduct(): array
    {
        return $this->product->toArray();
    }

    /**
     * @return boolean
     */
    public function hasProduct(Product $product = null)
    {
        return $this->product->contains($product);
    }

    public function addProduct(Product $product): self
    {
        if (!$this->product->contains($product)) {
            $this->product[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->product->contains($product)) {
            $this->product->removeElement($product);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }

    /**
     * @param mixed $orderDate
     */
    public function setOrderDate($orderDate): void
    {
        $this->orderDate = $orderDate;
    }

    /**
     * @return mixed
     */
    public function getFeedbackDate()
    {
        return $this->feedbackDate;
    }

    /**
     * @param mixed $feedbackDate
     */
    public function setFeedbackDate($feedbackDate): void
    {
        $this->feedbackDate = $feedbackDate;
    }

    /**
     * @return mixed
     */
    public function getIsSended()
    {
        return $this->isSended;
    }

    /**
     * @param mixed $isSended
     */
    public function setIsSended($isSended)
    {
        $this->isSended = $isSended;
    }

}
