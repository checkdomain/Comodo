<?php
namespace Checkdomain\Comodo\Model\Result;

/**
 * Class AutoReplaceResult
 */
class AutoReplaceResult extends AbstractResult
{
    protected $certificateID;
    protected $expectedDeliveryTime;

    /**
     * @param mixed $certificateID
     *
     * @return AutoApplyResult
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
     * @return AutoApplyResult
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
}
