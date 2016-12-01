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
        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode'    => -2,
            'errorItem'    => 'field',
            'errorMessage' => 'InvalidRequest',
        ])));

        try {
            $util->autoApplySSL([]);
        } catch (ArgumentException $e) {
            $this->assertEquals('field', $e->getArgumentName());
        }
    }

    /**
     * tests, if the common exception fields are filled
     */
    public function testCommonException()
    {
        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode'    => -15,
            'errorMessage' => 'Invalid Request',
        ])));

        try {
            $util->autoApplySSL([]);
        } catch (AccountException $e) {
            $this->assertEquals($e->getMessage(), 'Invalid Request');
            $this->assertEquals(-15, $e->getCode());
        }
    }
}
