<?php
namespace Checkdomain\Comodo\Model\Result;

/**
 * Class WebHostReportEntry
 */
class WebHostReportEntry
{

    /**
     * @var int
     */
    protected $rowNumber;


    /**
     * @var int
     */
    protected $orderNumber;

    /**
     * @var string
     */
    protected $orderStatus;

    /**
     * @var \DateTime
     */
    protected $dateTime;

    /**
     * @var string
     */
    protected $organizationName;

    /**
     * @var string
     */
    protected $organizationalUnitName;

    /**
     * @var string
     */
    protected $postOfficeBox;

    /**
     * @var string
     */
    protected $streetAddress1;

    /**
     * @var string
     */
    protected $streetAddress2;

    /**
     * @var string
     */
    protected $streetAddress3;

    /**
     * @var string
     */
    protected $localityName;

    /**
     * @var string
     */
    protected $stateOrProvinceName;

    /**
     * @var string
     */
    protected $postalCode;

    /**
     * @var string
     */
    protected $countryName;

    /**
     * @var string
     */
    protected $validationStatus;

    /**
     * @var WebHostReportEntryItemCollection;
     */
    protected $items;

    /**
     * WebHostReportEntry constructor.
     * @throws \Checkdomain\Comodo\Model\Exception\UnknownException
     */
    public function __construct()
    {
        $this->items = new WebHostReportEntryItemCollection();
    }

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
     * @return int
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @param int $orderNumber
     * @return WebHostReportEntry
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }

    /**
     * @param int $orderStatus
     * @return WebHostReportEntry
     */
    public function setOrderStatus($orderStatus)
    {
        $this->orderStatus = $orderStatus;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param \DateTime $dateTime
     * @return WebHostReportEntry
     */
    public function setDateTime($dateTime)
    {
        if (is_numeric($dateTime)) {
            $this->dateTime = new \DateTime("@$dateTime");
        } else {
            $this->dateTime = $dateTime;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    /**
     * @param string $organizationName
     * @return WebHostReportEntry
     */
    public function setOrganizationName($organizationName)
    {
        $this->organizationName = $organizationName;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrganizationalUnitName()
    {
        return $this->organizationalUnitName;
    }

    /**
     * @param string $organizationalUnitName
     * @return WebHostReportEntry
     */
    public function setOrganizationalUnitName($organizationalUnitName)
    {
        $this->organizationalUnitName = $organizationalUnitName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostOfficeBox()
    {
        return $this->postOfficeBox;
    }

    /**
     * @param string $postOfficeBox
     * @return WebHostReportEntry
     */
    public function setPostOfficeBox($postOfficeBox)
    {
        $this->postOfficeBox = $postOfficeBox;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreetAddress1()
    {
        return $this->streetAddress1;
    }

    /**
     * @param string $streetAddress1
     * @return WebHostReportEntry
     */
    public function setStreetAddress1($streetAddress1)
    {
        $this->streetAddress1 = $streetAddress1;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreetAddress2()
    {
        return $this->streetAddress2;
    }

    /**
     * @param string $streetAddress2
     * @return WebHostReportEntry
     */
    public function setStreetAddress2($streetAddress2)
    {
        $this->streetAddress2 = $streetAddress2;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreetAddress3()
    {
        return $this->streetAddress3;
    }

    /**
     * @param string $streetAddress3
     * @return WebHostReportEntry
     */
    public function setStreetAddress3($streetAddress3)
    {
        $this->streetAddress3 = $streetAddress3;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocalityName()
    {
        return $this->localityName;
    }

    /**
     * @param string $localityName
     * @return WebHostReportEntry
     */
    public function setLocalityName($localityName)
    {
        $this->localityName = $localityName;
        return $this;
    }

    /**
     * @return string
     */
    public function getStateOrProvinceName()
    {
        return $this->stateOrProvinceName;
    }

    /**
     * @param string $stateOrProvinceName
     * @return WebHostReportEntry
     */
    public function setStateOrProvinceName($stateOrProvinceName)
    {
        $this->stateOrProvinceName = $stateOrProvinceName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     * @return WebHostReportEntry
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
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
    public function getValidationStatus()
    {
        return $this->validationStatus;
    }

    /**
     * @param string $validationStatus
     * @return WebHostReportEntry
     */
    public function setValidationStatus($validationStatus)
    {
        $this->validationStatus = $validationStatus;
        return $this;
    }

    /**
     * @return WebHostReportEntryItemCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param WebHostReportEntryItemCollection $items
     * @return WebHostReportEntry
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }


}
