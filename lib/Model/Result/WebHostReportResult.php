<?php
namespace Checkdomain\Comodo\Model\Result;

/**
 * Class WebHostReportResult
 */
class WebHostReportResult extends AbstractResult
{
    /**
     * @var string
     */
    protected $serverUrl;

    /**
     * @var int
     */
    protected $noOfResults;

    /**
     * @var int
     */
    protected $lastResultNo;

    /**
     * @var int
     */
    protected $firstResultNo;

    /**
     * @var int
     */
    protected $notBefore;

    /**
     * @var int
     */
    protected $notAfter;

    /**
     * @var WebHostReportEntryCollection
     */
    protected $entries;

    /**
     * @return int
     */
    public function getNoOfResults()
    {
        return $this->noOfResults;
    }

    /**
     * @param int $noOfResults
     * @return WebHostReportResult
     */
    public function setNoOfResults($noOfResults)
    {
        $this->noOfResults = $noOfResults;
        return $this;
    }

    /**
     * @return int
     */
    public function getLastResultNo()
    {
        return $this->lastResultNo;
    }

    /**
     * @param int $lastResultNo
     * @return WebHostReportResult
     */
    public function setLastResultNo($lastResultNo)
    {
        $this->lastResultNo = $lastResultNo;
        return $this;
    }

    /**
     * @return int
     */
    public function getFirstResultNo()
    {
        return $this->firstResultNo;
    }

    /**
     * @param int $firstResultNo
     * @return WebHostReportResult
     */
    public function setFirstResultNo($firstResultNo)
    {
        $this->firstResultNo = $firstResultNo;
        return $this;
    }

    /**
     * @return int
     */
    public function getNotBefore()
    {
        return $this->notBefore;
    }

    /**
     * @param int $notBefore
     * @return WebHostReportResult
     */
    public function setNotBefore($notBefore)
    {
        $this->notBefore = $notBefore;
        return $this;
    }

    /**
     * @return int
     */
    public function getNotAfter()
    {
        return $this->notAfter;
    }

    /**
     * @param int $notAfter
     * @return WebHostReportResult
     */
    public function setNotAfter($notAfter)
    {
        $this->notAfter = $notAfter;
        return $this;
    }

    /**
     * @param array $webHostReportRawResult
     * @return WebHostReportResult
     */
    public function importEntries($webHostReportRawResult) {

        $this->setNoOfResults($webHostReportRawResult['noOfResults']);
        $this->entries = new WebHostReportEntryCollection($webHostReportRawResult);

        return $this;
    }

    /**
     * @return WebHostReportEntryCollection
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param WebHostReportEntryCollection $entries
     * @return WebHostReportResult
     */
    public function setEntries($entries)
    {
        $this->entries = $entries;
        return $this;
    }

}
