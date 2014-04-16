<?php
namespace Checkdomain\Comodo\Model\Exception;

/**
 * Class AbstractException
 *
 * The mother of all exceptions
 */
abstract class AbstractException extends \Exception
{
    /**
     * @var string
     */
    protected $responseString;

    /**
     * @param int $errorCode
     * @param string $errorMessage
     * @param string $responseString
     */
    public function __construct($errorCode, $errorMessage, $responseString)
    {
        $this->responseString = $responseString;

        parent::__construct($errorMessage, $errorCode);
    }

    /**
     * @return string
     */
    public function getResponseString()
    {
        return $this->responseString;
    }
}