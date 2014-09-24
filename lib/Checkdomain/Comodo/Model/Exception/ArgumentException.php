<?php
namespace Checkdomain\Comodo\Model\Exception;

/**
 * Exception thrown, when anything is wrong with argument
 */
class ArgumentException extends AbstractException
{
    protected $argumentName;

    /**
     * @param int    $errorCode
     * @param string $errorMessage
     * @param string $argumentName
     * @param string $responseString
     */
    public function __construct($errorCode, $errorMessage, $argumentName, $responseString)
    {
        $this->argumentName = $argumentName;

        parent::__construct($errorCode, $errorMessage, $responseString);
    }

    /**
     * @return string
     */
    public function getArgumentName()
    {
        return $this->argumentName;
    }
}