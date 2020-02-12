<?php

namespace Checkdomain\Comodo\Model\Result;

class PlaceOrderResult extends AbstractResult
{
    /** @var string|null */
    private $orderNumber;

    /** @var string|null */
    private $collectionCode;

    /**
     * @return string|null
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @param string $orderNumber
     * @return PlaceOrderResult
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCollectionCode()
    {
        return $this->collectionCode;
    }

    public function setCollectionCode($collectionCode)
    {
        $this->collectionCode = $collectionCode;
        return $this;
    }
}