<?php
namespace Checkdomain\Comodo\Model\Result;

/**
 * Class SslCheckerResult
 */
class SslCheckerResult extends AbstractResult
{
    /**
     * @var string
     */
    protected $serverUrl;

    /**
     * @var string
     */
    protected $serverDomainIsIDN;

    /**
     * @var string
     */
    protected $serverDomainUtf8;

    /**
     * @var string
     */
    protected $serverDomainAce;

    /**
     * @var string
     */
    protected $serverIp;

    /**
     * @var integer
     */
    protected $serverPort;

    /**
     * @var string
     */
    protected $serverSoftware;

    /**
     * @var \DateTime
     */
    protected $certNotBefore;

    /**
     * @var \DateTime
     */
    protected $certNotAfter;

    /**
     * @var string
     */
    protected $certValidityNotBefore;

    /**
     * @var string
     */
    protected $certValidityNotAfter;

    /**
     * @var string
     */
    protected $certKeyAlgorithm;

    /**
     * @var string
     */
    protected $certKeySize;

    /**
     * @var string
     */
    protected $certSubjectDN;

    /**
     * @var string
     */
    protected $certSubjectCN;

    /**
     * @var string
     */
    protected $certSubjectOU;

    /**
     * @var string
     */
    protected $certSubjectOrganization;

    /**
     * @var string
     */
    protected $certSubjectStreetAddress1;

    /**
     * @var string
     */
    protected $certSubjectStreetAddress2;

    /**
     * @var string
     */
    protected $certSubjectStreetAddress3;

    /**
     * @var string
     */
    protected $certSubjectLocality;

    /**
     * @var string
     */
    protected $certSubjectState;

    /**
     * @var string
     */
    protected $certSubjectPostalCode ;

    /**
     * @var string
     */
    protected $certSubjectCountry ;

    /**
     * @var string
     */
    protected $certIsMultiDomain;

    /**
     * @var string
     */
    protected $certIsWildcard;

    /**
     * @var string
     */
    protected $certIssuerDN;

    /**
     * @var string
     */
    protected $certIssuerCN;

    /**
     * @var string
     */
    protected $certIssuerOrganization;

    /**
     * @var string
     */
    protected $certIssuerCountry;

    /**
     * @var string
     */
    protected $certIssuerBrand;

    /**
     * @var string
     */
    protected $certPolicyOID;

    /**
     * @var string
     */
    protected $certValidation;

    /**
     * @return string
     */
    public function getServerUrl()
    {
        return $this->serverUrl;
    }

    /**
     * @param string $serverUrl
     * @return SslCheckerResult
     */
    public function setServerUrl($serverUrl)
    {
        $this->serverUrl = $serverUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getServerDomainIsIDN()
    {
        return $this->serverDomainIsIDN;
    }

    /**
     * @param string $serverDomainIsIDN
     * @return SslCheckerResult
     */
    public function setServerDomainIsIDN($serverDomainIsIDN)
    {
        $this->serverDomainIsIDN = $serverDomainIsIDN;
        return $this;
    }

    /**
     * @return string
     */
    public function getServerDomainUtf8()
    {
        return $this->serverDomainUtf8;
    }

    /**
     * @param string $serverDomainUtf8
     * @return SslCheckerResult
     */
    public function setServerDomainUtf8($serverDomainUtf8)
    {
        $this->serverDomainUtf8 = $serverDomainUtf8;
        return $this;
    }

    /**
     * @return string
     */
    public function getServerDomainAce()
    {
        return $this->serverDomainAce;
    }

    /**
     * @param string $serverDomainAce
     * @return SslCheckerResult
     */
    public function setServerDomainAce($serverDomainAce)
    {
        $this->serverDomainAce = $serverDomainAce;
        return $this;
    }

    /**
     * @return string
     */
    public function getServerIp()
    {
        return $this->serverIp;
    }

    /**
     * @param string $serverIp
     * @return SslCheckerResult
     */
    public function setServerIp($serverIp)
    {
        $this->serverIp = $serverIp;
        return $this;
    }

    /**
     * @return int
     */
    public function getServerPort()
    {
        return $this->serverPort;
    }

    /**
     * @param int $serverPort
     * @return SslCheckerResult
     */
    public function setServerPort($serverPort)
    {
        $this->serverPort = $serverPort;
        return $this;
    }

    /**
     * @return string
     */
    public function getServerSoftware()
    {
        return $this->serverSoftware;
    }

    /**
     * @param string $serverSoftware
     * @return SslCheckerResult
     */
    public function setServerSoftware($serverSoftware)
    {
        $this->serverSoftware = $serverSoftware;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCertNotBefore()
    {
        return $this->certNotBefore;
    }

    /**
     * @param \DateTime $certNotBefore
     * @return SslCheckerResult
     */
    public function setCertNotBefore($certNotBefore)
    {
        $this->certNotBefore = $certNotBefore;
        return $this;
    }

    /**
     * @param integer $certNotBefore
     * @return SslCheckerResult
     */
    public function setCertNotBeforeFromUnixTimestamp($certNotBefore)
    {
        $this->certNotBefore = new \DateTime("@$certNotBefore");
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCertNotAfter()
    {
        return $this->certNotAfter;
    }

    /**
     * @param \DateTime $certNotAfter
     * @return SslCheckerResult
     */
    public function setCertNotAfter($certNotAfter)
    {
        $this->certNotAfter = $certNotAfter;
        return $this;
    }

    /**
     * @param integer $certNotAfter
     * @return SslCheckerResult
     */
    public function setCertNotAfterFromUnixTimestamp($certNotAfter)
    {
        $this->certNotAfter = new \DateTime("@$certNotAfter");;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCertValidityNotBefore()
    {
        return $this->certValidityNotBefore;
    }

    /**
     * @param \DateTime $certValidityNotBefore
     * @return SslCheckerResult
     */
    public function setCertValidityNotBefore($certValidityNotBefore)
    {
        $this->certValidityNotBefore = $certValidityNotBefore;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCertValidityNotAfter()
    {
        return $this->certValidityNotAfter;
    }

    /**
     * @param \DateTime $certValidityNotAfter
     * @return SslCheckerResult
     */
    public function setCertValidityNotAfter($certValidityNotAfter)
    {
        $this->certValidityNotAfter = $certValidityNotAfter;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertKeyAlgorithm()
    {
        return $this->certKeyAlgorithm;
    }

    /**
     * @param string $certKeyAlgorithm
     * @return SslCheckerResult
     */
    public function setCertKeyAlgorithm($certKeyAlgorithm)
    {
        $this->certKeyAlgorithm = $certKeyAlgorithm;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertKeySize()
    {
        return $this->certKeySize;
    }

    /**
     * @param string $certKeySize
     * @return SslCheckerResult
     */
    public function setCertKeySize($certKeySize)
    {
        $this->certKeySize = $certKeySize;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertSubjectDN()
    {
        return $this->certSubjectDN;
    }

    /**
     * @param string $certSubjectDN
     * @return SslCheckerResult
     */
    public function setCertSubjectDN($certSubjectDN)
    {
        $this->certSubjectDN = $certSubjectDN;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertSubjectCN()
    {
        return $this->certSubjectCN;
    }

    /**
     * @param string $certSubjectCN
     * @return SslCheckerResult
     */
    public function setCertSubjectCN($certSubjectCN)
    {
        $this->certSubjectCN = $certSubjectCN;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertSubjectOU()
    {
        return $this->certSubjectOU;
    }

    /**
     * @param string $certSubjectOU
     * @return SslCheckerResult
     */
    public function setCertSubjectOU($certSubjectOU)
    {
        $this->certSubjectOU = $certSubjectOU;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertSubjectOrganization()
    {
        return $this->certSubjectOrganization;
    }

    /**
     * @param string $certSubjectOrganization
     * @return SslCheckerResult
     */
    public function setCertSubjectOrganization($certSubjectOrganization)
    {
        $this->certSubjectOrganization = $certSubjectOrganization;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertSubjectStreetAddress1()
    {
        return $this->certSubjectStreetAddress1;
    }

    /**
     * @param string $certSubjectStreetAddress1
     * @return SslCheckerResult
     */
    public function setCertSubjectStreetAddress1($certSubjectStreetAddress1)
    {
        $this->certSubjectStreetAddress1 = $certSubjectStreetAddress1;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertSubjectStreetAddress2()
    {
        return $this->certSubjectStreetAddress2;
    }

    /**
     * @param string $certSubjectStreetAddress2
     * @return SslCheckerResult
     */
    public function setCertSubjectStreetAddress2($certSubjectStreetAddress2)
    {
        $this->certSubjectStreetAddress2 = $certSubjectStreetAddress2;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertSubjectStreetAddress3()
    {
        return $this->certSubjectStreetAddress3;
    }

    /**
     * @param string $certSubjectStreetAddress3
     * @return SslCheckerResult
     */
    public function setCertSubjectStreetAddress3($certSubjectStreetAddress3)
    {
        $this->certSubjectStreetAddress3 = $certSubjectStreetAddress3;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertSubjectLocality()
    {
        return $this->certSubjectLocality;
    }

    /**
     * @param string $certSubjectLocality
     * @return SslCheckerResult
     */
    public function setCertSubjectLocality($certSubjectLocality)
    {
        $this->certSubjectLocality = $certSubjectLocality;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertSubjectState()
    {
        return $this->certSubjectState;
    }

    /**
     * @param string $certSubjectState
     * @return SslCheckerResult
     */
    public function setCertSubjectState($certSubjectState)
    {
        $this->certSubjectState = $certSubjectState;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertSubjectPostalCode()
    {
        return $this->certSubjectPostalCode;
    }

    /**
     * @param string $certSubjectPostalCode
     * @return SslCheckerResult
     */
    public function setCertSubjectPostalCode($certSubjectPostalCode)
    {
        $this->certSubjectPostalCode = $certSubjectPostalCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertSubjectCountry()
    {
        return $this->certSubjectCountry;
    }

    /**
     * @param string $certSubjectCountry
     * @return SslCheckerResult
     */
    public function setCertSubjectCountry($certSubjectCountry)
    {
        $this->certSubjectCountry = $certSubjectCountry;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertIsMultiDomain()
    {
        return $this->certIsMultiDomain;
    }

    /**
     * @param string $certIsMultiDomain
     * @return SslCheckerResult
     */
    public function setCertIsMultiDomain($certIsMultiDomain)
    {
        $this->certIsMultiDomain = $certIsMultiDomain;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertIsWildcard()
    {
        return $this->certIsWildcard;
    }

    /**
     * @param string $certIsWildcard
     * @return SslCheckerResult
     */
    public function setCertIsWildcard($certIsWildcard)
    {
        $this->certIsWildcard = $certIsWildcard;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertIssuerDN()
    {
        return $this->certIssuerDN;
    }

    /**
     * @param string $certIssuerDN
     * @return SslCheckerResult
     */
    public function setCertIssuerDN($certIssuerDN)
    {
        $this->certIssuerDN = $certIssuerDN;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertIssuerCN()
    {
        return $this->certIssuerCN;
    }

    /**
     * @param string $certIssuerCN
     * @return SslCheckerResult
     */
    public function setCertIssuerCN($certIssuerCN)
    {
        $this->certIssuerCN = $certIssuerCN;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertIssuerOrganization()
    {
        return $this->certIssuerOrganization;
    }

    /**
     * @param string $certIssuerOrganization
     * @return SslCheckerResult
     */
    public function setCertIssuerOrganization($certIssuerOrganization)
    {
        $this->certIssuerOrganization = $certIssuerOrganization;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertIssuerCountry()
    {
        return $this->certIssuerCountry;
    }

    /**
     * @param string $certIssuerCountry
     * @return SslCheckerResult
     */
    public function setCertIssuerCountry($certIssuerCountry)
    {
        $this->certIssuerCountry = $certIssuerCountry;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertIssuerBrand()
    {
        return $this->certIssuerBrand;
    }

    /**
     * @param string $certIssuerBrand
     * @return SslCheckerResult
     */
    public function setCertIssuerBrand($certIssuerBrand)
    {
        $this->certIssuerBrand = $certIssuerBrand;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertPolicyOID()
    {
        return $this->certPolicyOID;
    }

    /**
     * @param string $certPolicyOID
     * @return SslCheckerResult
     */
    public function setCertPolicyOID($certPolicyOID)
    {
        $this->certPolicyOID = $certPolicyOID;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertValidation()
    {
        return $this->certValidation;
    }

    /**
     * @param string $certValidation
     * @return SslCheckerResult
     */
    public function setCertValidation($certValidation)
    {
        $this->certValidation = $certValidation;
        return $this;
    }

}
