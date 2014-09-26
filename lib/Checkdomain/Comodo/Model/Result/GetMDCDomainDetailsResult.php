<?php
namespace Checkdomain\Comodo\Model\Result;

/**
 * Class GetMDCDomainDetailsResult
 * Offers the order Status, requested at comodo
 *
 * @package Checkdomain\Comodo\Model\Result
 */
class GetMDCDomainDetailsResult extends AbstractResult
{
    /**
     * @var string
     */
    protected $domainName;

    /**
     * @var string
     */
    protected $dcvMethod;

    /**
     * @var string
     */
    protected $dcvStatus;

    /**
     * @param string $dcvMethod
     *
     * @return GetMDCDomainDetailsResult
     */
    public function setDcvMethod($dcvMethod)
    {
        $this->dcvMethod = $dcvMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getDcvMethod()
    {
        return $this->dcvMethod;
    }

    /**
     * @param string $dcvStatus
     *
     * @return GetMDCDomainDetailsResult
     */
    public function setDcvStatus($dcvStatus)
    {
        $this->dcvStatus = $dcvStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getDcvStatus()
    {
        return $this->dcvStatus;
    }

    /**
     * @param string $domainName
     *
     * @return GetMDCDomainDetailsResult
     */
    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomainName()
    {
        return $this->domainName;
    }
}
