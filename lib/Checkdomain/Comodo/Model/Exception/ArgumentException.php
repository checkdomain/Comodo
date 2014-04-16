<?php
namespace Checkdomain\Comodo\Model\Exception;

/*
 * Exception thrown, when anything is wrong with argument
 */
class ArgumentException extends AbstractException
{
    protected $argumentName;

    public function __construct($errorCode, $errorMessage, $argumentName, $responseString) {
        $this->argumentName = $argumentName;

        parent::__construct($errorCode, $errorMessage, $responseString);
    }

    public function getArgumentName() {
        return $this->argumentName;
    }
}