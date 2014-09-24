<?php
namespace Checkdomain\Comodo\Model\Result;

/**
 * Class Result
 *
 * Abstract class with result of a request to comodo API
 */
abstract class AbstractResult
{
    private $requestQuery;

    /**
     * Sets the request query
     *
     * @param string $requestQuery
     */
    public function setRequestQuery($requestQuery)
    {
        $this->requestQuery = $requestQuery;
    }

    /**
     * Return the request-query
     *
     * @return string
     */
    public function getRequestQuery()
    {
        return $this->requestQuery;
    }
}