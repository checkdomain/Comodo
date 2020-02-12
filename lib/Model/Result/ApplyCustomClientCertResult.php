<?php

namespace Checkdomain\Comodo\Model\Result;

class ApplyCustomClientCertResult extends AbstractResult
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
