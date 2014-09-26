<?php
namespace Checkdomain\Comodo\Tests;

use Checkdomain\Comodo\Model\Exception\AccountException;
use Checkdomain\Comodo\Model\Exception\ArgumentException;

class ExceptionTest extends AbstractTest
{
    /**
     * tests if the argument-exception object is correctly filled
     */
    public function testArgumentException()
    {
        $responseText = 'errorCode=-2&errorItem=field&errorMessage=Invalid Request';
        $util = $this->createUtil($this->createGuzzleClient($responseText));

        try {
            $util->autoApplySSL(array());
        } catch (ArgumentException $e){
            $this->assertEquals($e->getArgumentName(), 'field');
        }
    }

    /**
     * tests, if the common exception fields are filled
     */
    public function testCommonException()
    {
        $responseText = 'errorCode=-15&errorMessage=Invalid Request';

        $util = $this->createUtil($this->createGuzzleClient($responseText));

        try {
            $util->autoApplySSL(array());
        } catch (AccountException $e){
            $this->assertEquals($e->getMessage(), 'Invalid Request');
            $this->assertEquals($e->getCode(), '-15');
        }
    }
}
