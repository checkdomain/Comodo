<?php
namespace Checkdomain\Comodo\Model\Result;
use Checkdomain\Comodo\Model\Exception\UnknownException;

/**
 * Class WebHostReportEntryCollection
 */
class WebHostReportEntryCollection implements \Iterator
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

    /**
     * WebHostReportEntryCollection constructor.
     * @param $webHostReportRawResult
     *
     * @return WebHostReportEntryCollection
     */
    public function __construct($webHostReportRawResult)
    {
        $pos = 0;
        foreach ($webHostReportRawResult as $key => $line) {
            $keyArray = explode('_', $key);

            if (is_numeric($keyArray[0])) {
                if ($pos < $keyArray[0]) {
                    $pos = $keyArray[0];
                    $entry = new WebHostReportEntry();
                    $this->add($entry);
                    $entry->setRowNumber($pos);
                }

                switch (count($keyArray)) {
                    case 2:
                        $setter = 'set' . ucwords($keyArray[1]);
                        $itempos = 0;
                        break;
                    case 3:
                        if ($itempos < $keyArray[1]) {
                            $item = new WebHostReportEntryItem();
                            $entry->getItems()->add($item);
                            $itempos = $keyArray[1];
                        }
                        $itemsetter = 'set' . ucwords($keyArray[2]);
                        if (method_exists($item, $itemsetter)) {
                            $item->$itemsetter($webHostReportRawResult[$key]);
                        }
                        break;
                    default:
                        throw new UnknownException(99, 'UnknownException', 'Malformed result from api call.');
                }
                if (method_exists($entry, $setter)) {
                    $entry->$setter($webHostReportRawResult[$key]);
                }
            }
        }

        return $this;
    }

}
