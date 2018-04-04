<?php
namespace Checkdomain\Comodo\Model\Result;
use Checkdomain\Comodo\Model\Exception\UnknownException;

/**
 * Class WebHostReportEntryItemCollection
 */
class WebHostReportEntryItemCollection implements \Iterator
{

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var array
     */
    private $entries = [];

    /**
     *
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * @return mixed
     */
    public function current() {
        return $this->entries[$this->position];
    }

    /**
     * @return int
     */
    public function key() {
        return $this->position;
    }

    /**
     *
     */
    public function next() {
        ++$this->position;
    }

    /**
     * @param WebHostReportEntry $webHostReportEntry
     * @return WebHostReportEntryCollection
     */
    public function add($webHostReportEntry) {
        $this->entries[] = $webHostReportEntry;

        return $this;
    }

    /**
     * @return bool
     */
    public function valid() {
        return isset($this->entries[$this->position]);
    }

}
