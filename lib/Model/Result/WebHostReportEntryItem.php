<?php
namespace Checkdomain\Comodo\Model\Result;

/**
 * Class WebHostReportEntryItem
 */
class WebHostReportEntryItem
{

    /**
     * @var int
     */
    protected $rowNumber;


    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $additionalDomain;

    /**
     * @var string
     */
    protected $mdcDomainNames;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var \DateTime
     */
    protected $lastStatusChange;

    /**
     * @var \DateTime
     */
    protected $notBefore;

    /**
     * @var \DateTime
     */
    protected $notAfter;

    /**
     * @var string
     */
    protected $serialNumber;

    /**
     * @var string
     */
    protected $signatureAlgorithm;

    /**
     * @var string
     */
    protected $keySize;

    /**
     * @var string
     */
    protected $webServerSoftware;

    /**
     * @var string
     */
    protected $certificateID;

    /**
     * @var string
     */
    protected $csrStatus;

    /**
     * @var string
     */
    protected $dcvStatus;

    /**
     * @var string
     */
    protected $ovCallBackStatus;

    /**
     * @var string
     */
    protected $organizationValidationStatus;

    /**
     * @var string
     */
    protected $countryName;

    /**
     * @var string
     */
    protected $freeDVUPStatus;

    /**
     * @var string
     */
    protected $evClickThroughStatus;

    /**
     * @return int
     */
    public function getRowNumber()
    {
        return $this->rowNumber;
    }

    /**
     * @param int $rowNumber
     * @return WebHostReportEntry
     */
    public function setRowNumber($rowNumber)
    {
        $this->rowNumber = $rowNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return WebHostReportEntry
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return WebHostReportEntry
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalDomain()
    {
        return $this->additionalDomain;
    }

    /**
     * @param string $additionalDomain
     * @return WebHostReportEntry
     */
    public function setAdditionalDomain($additionalDomain)
    {
        $this->additionalDomain = $additionalDomain;
        return $this;
    }

    /**
     * @return string
     */
    public function getMdcDomainNames()
    {
        return $this->mdcDomainNames;
    }

    /**
     * @param string $mdcDomainNames
     * @return WebHostReportEntry
     */
    public function setMdcDomainNames($mdcDomainNames)
    {
        $this->mdcDomainNames = $mdcDomainNames;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return WebHostReportEntry
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastStatusChange()
    {
        return $this->lastStatusChange;
    }

    /**
     * @param \DateTime $lastStatusChange
     * @return WebHostReportEntry
     */
    public function setLastStatusChange($lastStatusChange)
    {
        if (is_numeric($lastStatusChange)) {
            $this->lastStatusChange = new \DateTime("@$lastStatusChange");
        } else {
            $this->lastStatusChange = $dateTime;
        }
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getNotBefore()
    {
        return $this->notBefore;
    }

    /**
     * @param \DateTime $notBefore
     * @return WebHostReportEntry
     */
    public function setNotBefore($notBefore)
    {
        if (is_numeric($notBefore)) {
            $this->notBefore = new \DateTime("@$notBefore");
        } else {
            $this->notBefore = $notBefore;
        }
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getNotAfter()
    {
        return $this->notAfter;
    }

    /**
     * @param \DateTime $notAfter
     * @return WebHostReportEntry
     */
    public function setNotAfter($notAfter)
    {
        if (is_numeric($notBefore)) {
            $this->notAfter = new \DateTime("@$notAfter");
        } else {
            $this->notAfter = $notAfter;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * @param string $serialNumber
     * @return WebHostReportEntry
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getSignatureAlgorithm()
    {
        return $this->signatureAlgorithm;
    }

    /**
     * @param string $signatureAlgorithm
     * @return WebHostReportEntry
     */
    public function setSignatureAlgorithm($signatureAlgorithm)
    {
        $this->signatureAlgorithm = $signatureAlgorithm;
        return $this;
    }

    /**
     * @return string
     */
    public function getKeySize()
    {
        return $this->keySize;
    }

    /**
     * @param string $keySize
     * @return WebHostReportEntry
     */
    public function setKeySize($keySize)
    {
        $this->keySize = $keySize;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebServerSoftware()
    {
        return $this->webServerSoftware;
    }

    /**
     * @param string $webServerSoftware
     * @return WebHostReportEntry
     */
    public function setWebServerSoftware($webServerSoftware)
    {
        $this->webServerSoftware = $webServerSoftware;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertificateID()
    {
        return $this->certificateID;
    }

    /**
     * @param string $certificateID
     * @return WebHostReportEntry
     */
    public function setCertificateID($certificateID)
    {
        $this->certificateID = $certificateID;
        return $this;
    }

    /**
     * @return string
     */
    public function getCsrStatus()
    {
        return $this->csrStatus;
    }

    /**
     * @param string $csrStatus
     * @return WebHostReportEntry
     */
    public function setCsrStatus($csrStatus)
    {
        $this->csrStatus = $csrStatus;
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
     * @param string $dcvStatus
     * @return WebHostReportEntry
     */
    public function setDcvStatus($dcvStatus)
    {
        $this->dcvStatus = $dcvStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getOvCallBackStatus()
    {
        return $this->ovCallBackStatus;
    }

    /**
     * @param string $ovCallBackStatus
     * @return WebHostReportEntry
     */
    public function setOvCallBackStatus($ovCallBackStatus)
    {
        $this->ovCallBackStatus = $ovCallBackStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrganizationValidationStatus()
    {
        return $this->organizationValidationStatus;
    }

    /**
     * @param string $organizationValidationStatus
     * @return WebHostReportEntry
     */
    public function setOrganizationValidationStatus($organizationValidationStatus)
    {
        $this->organizationValidationStatus = $organizationValidationStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * @param string $countryName
     * @return WebHostReportEntry
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFreeDVUPStatus()
    {
        return $this->freeDVUPStatus;
    }

    /**
     * @param string $freeDVUPStatus
     * @return WebHostReportEntry
     */
    public function setFreeDVUPStatus($freeDVUPStatus)
    {
        $this->freeDVUPStatus = $freeDVUPStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getEvClickThroughStatus()
    {
        return $this->evClickThroughStatus;
    }

    /**
     * @param string $evClickThroughStatus
     * @return WebHostReportEntry
     */
    public function setEvClickThroughStatus($evClickThroughStatus)
    {
        $this->evClickThroughStatus = $evClickThroughStatus;
        return $this;
    }



}
