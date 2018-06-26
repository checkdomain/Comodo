<?php
namespace Checkdomain\Comodo\Model\Result;

/**
 * Class AutoReplaceResult
 */
class AutoReplaceResult extends AbstractResult
{
    protected $certificateID;
    protected $expectedDeliveryTime;
    protected $uniqueValue;

    /**
     * @param mixed $certificateID
     *
     * @return AutoReplaceResult
     */
    public function setCertificateID($certificateID)
    {
        $this->certificateID = $certificateID;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCertificateID()
    {
        return $this->certificateID;
    }

    /**
     * @param int $expectedDeliveryTime
     *
     * @return AutoReplaceResult
     */
    public function setExpectedDeliveryTime($expectedDeliveryTime)
    {
        $this->expectedDeliveryTime = $expectedDeliveryTime;

        return $this;
    }

    /**
     * @return int
     */
    public function getExpectedDeliveryTime()
    {
        return $this->expectedDeliveryTime;
    }

    /**
     * @return string
     */
    public function getUniqueValue()
    {
        return $this->uniqueValue;
    }

    /**
     * @param string $uniqueValue
     *
     * @return AutoReplaceResult
     */
    public function setUniqueValue($uniqueValue)
    {
        $this->uniqueValue = $uniqueValue;

        return $this;
    }
}
