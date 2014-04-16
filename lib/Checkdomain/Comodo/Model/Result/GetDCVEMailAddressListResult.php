<?php
namespace Checkdomain\Comodo\Model\Result;

/**
 * Class GetDCVEMailAddressListResult
 * Offers the email-addresses, requested at comodo
 *
 * @package Checkdomain\Comodo\Model\Result
 */
class GetDCVEMailAddressListResult extends AbstractResult
{
    /**
     * @var string
     */
    protected $domainName;

    /**
     * @var string
     */
    protected $whoisEmail;

    /**
     * @var array
     */
    protected $level2Email;

    /**
     * @var array
     */
    protected $level3Email;

    /**
     * @param string $domainName
     *
     * @return GetDCVEMailAddressListResult
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

    /**
     * @param array $level2Email
     *
     * @return GetDCVEMailAddressListResult
     */
    public function setLevel2Emails($level2Email)
    {
        $this->level2Email = $level2Email;

        return $this;
    }

    /**
     * @return array
     */
    public function getLevel2Emails()
    {
        return $this->level2Email;
    }

    /**
     * @param array $level3Email
     *
     * @return GetDCVEMailAddressListResult
     */
    public function setLevel3Emails($level3Email)
    {
        $this->level3Email = $level3Email;

        return $this;
    }

    /**
     * @return array
     */
    public function getLevel3Emails()
    {
        return $this->level3Email;
    }

    /**
     * @param string $whoisEmail
     *
     * @return GetDCVEMailAddressListResult
     */
    public function setWhoisEmail($whoisEmail)
    {
        $this->whoisEmail = $whoisEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getWhoisEmail()
    {
        return $this->whoisEmail;
    }
}