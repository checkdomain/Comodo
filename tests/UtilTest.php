<?php
namespace Checkdomain\Comodo\Tests;

use Checkdomain\Comodo\Model\Result\AutoApplyResult;
use Checkdomain\Comodo\Model\Result\AutoReplaceResult;
use Checkdomain\Comodo\Model\Result\GetDCVEMailAddressListResult;
use Checkdomain\Comodo\Model\Result\GetMDCDomainDetailsResult;

class UtilTest extends AbstractTest
{
    /**
     * test for applying SSL
     */
    public function testAutoApplySSL()
    {
        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode'            => 1,
            'totalCost'            => 12.98,
            'orderNumber'          => '123456789',
            'certificateID'        => 'abc123456',
            'expectedDeliveryTime' => '123456'
        ])));

        $result = $util->autoApplySSL([
            'test'                => 'Y',
            'product'             => 287,
            'serverSoftware'      => 2,
            'csr'                 => ('-----BEGIN CERTIFICATE REQUEST-----base64-----END CERTIFICATE REQUEST-----'),
            'isCustomerValidated' => 'Y',
            'showCertificateID'   => 'Y',
            'days'                => 365,
        ]);

        $this->assertInstanceOf(AutoApplyResult::class, $result);
        $this->assertEquals('12.98', $result->getTotalCost());
        $this->assertEquals('123456', $result->getExpectedDeliveryTime());
        $this->assertEquals('abc123456', $result->getCertificateID());
        $this->assertEquals('123456789', $result->getOrderNumber());
        $this->assertFalse($result->getPaid());
    }

    /**
     * test for getting dvc mail addresses
     */
    public function testGetDCVEMailAddressList()
    {
        // simulated response Text
        $responseText = '0'.chr(10)
            .'domain_name	www.test-domain.org'.chr(10)
            .'whois_email	 support@test-domain.org'.chr(10)
            .'level2_email	 admin@test-domain.org'.chr(10)
            .'level2_email	 postmaster@test-domain.org'.chr(10)
            .'level3_email	 admin@www.test-domain.org'.chr(10)
            .'level3_email	 postmaster@www.test-domain.org'.chr(10);

        $util = $this->createUtil($this->createGuzzleClient($responseText));

        $result = $util->getDCVEMailAddressList(['domainName' => 'www.test-domain.org']);

        $this->assertInstanceOf(GetDCVEMailAddressListResult::class, $result);
        $this->assertEquals(['support@test-domain.org'], $result->getWhoisEmail());
        $this->assertEquals('www.test-domain.org', $result->getDomainName());
        $this->assertEquals(['admin@test-domain.org', 'postmaster@test-domain.org'], $result->getLevel2Emails());
        $this->assertEquals(
            ['admin@www.test-domain.org', 'postmaster@www.test-domain.org'],
            $result->getLevel3Emails()
        );
    }

    /**
     * test for resending dcv mail
     */
    public function testResendDCVEMail()
    {
        $util = $this->createUtil($this->createGuzzleClient('errorCode=0'));

        $this->assertTrue($util->resendDCVEMail([
            'orderNumber'     => '1234567',
            'dcvEmailAddress' => 'webmaster@tobias-nitsche.de',
        ]));
    }

    /**
     * test for entering dcv code
     */
    public function testEnterDCVCode()
    {
        // simulated response Text
        $responseText = '<html><body><p>You have entered the correct Domain Control Validation code. '
            .'Your certificate will now be issued and emailed to you shortly. '
            .'Please close this window now.'
            .'</p></body></html>';

        $util = $this->createUtil($this->createGuzzleClient($responseText));

        $this->assertTrue($util->enterDcvCode([
            'orderNumber' => '1234567',
            'dcvCode'     => 'testtesttest',
        ]));
    }

    /**
     * test for revoke ssl
     */
    public function testAutoRevokeSSL()
    {
        $util = $this->createUtil($this->createGuzzleClient('errorCode=0'));

        $this->assertTrue($util->autoRevokeSSL(['orderNumber' => '1234567']));
    }

    /**
     * test for auto replacing ssl
     */
    public function testAutoReplaceSSL()
    {
        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode'            => 0,
            'expectedDeliveryTime' => 0,
            'certificateID'        => 'abc123456',
        ])));

        $result = $util->autoReplaceSSL(['orderNumber' => '1234567']);

        $this->assertInstanceOf(AutoReplaceResult::class, $result);
        $this->assertEquals('0', $result->getExpectedDeliveryTime());
        $this->assertEquals('abc123456', $result->getCertificateID());
    }

    /**
     * test for auto updating dcv method
     */
    public function testAutoUpdateDcv()
    {
        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode'            => 0,
            'expectedDeliveryTime' => 0,
            'certificateID'        => 'abc123456',
        ])));

        $this->assertTrue($util->autoUpdateDCV([
            'orderNumber'        => '1234567',
            'newDCVEmailAddress' => 'postmaster@test.de',
            'newMethod'          => 'EMAIL',
        ]));
    }

    /**
     * test for providing ev details
     */
    public function testProvideEvDetails()
    {
        $util = $this->createUtil($this->createGuzzleClient('errorCode=0'));

        $this->assertTrue($util->autoUpdateDCV([
            'orderNumber'     => '1234567',
            'certReqForename' => 'John',
            'certReqSurname'  => 'Test',
        ]));
    }

    /**
     * test for getting current dcv method
     */
    public function testGetMdcDomainDetails()
    {
        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode'    => 0,
            '1_domainName' => 'test.com',
            '1_dcvMethod'  => 'EMAIL',
            '1_dcvStatus'  => 'Validated',
        ])));

        $result = $util->getMDCDomainDetails(['orderNumber' => '1234567']);

        $this->assertInstanceOf(GetMDCDomainDetailsResult::class, $result);
        $this->assertEquals('test.com', $result->getDomainName());
        $this->assertEquals('EMAIL', $result->getDcvMethod());
        $this->assertEquals('Validated', $result->getDcvStatus());
    }

    /**
     * test, if request-string is correctly formatted
     */
    public function testCheckRequestString()
    {
        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode'            => 0,
            'certificateID'        => 'abc1231556',
            'expectedDeliveryTime' => 0,
            'orderNumber'          => 12345678,
            'totalCost'            => 12.45,
        ])));

        $result = $util->autoApplySSL([
            'orderNumber'     => '1234567',
            'dcvMethod'       => 'EMAIL',
        ]);

        $this->assertEquals(
            'orderNumber=1234567&dcvMethod=EMAIL&responseFormat=1&showCertificateID=Y',
            $result->getRequestQuery()
        );
    }

    /**
     * @expectedException \Checkdomain\Comodo\Model\Exception\RequestException
     */
    public function testRequestException()
    {
        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode'    => -1,
            'errorMessage' => 'Invalid Request',
        ])));

        $this->expectException(\Checkdomain\Comodo\Model\Exception\RequestException::class);
        $util->autoApplySSL([]);
    }

    /**
     * @expectedException \Checkdomain\Comodo\Model\Exception\ArgumentException
     */
    public function testArgumentException()
    {
        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode'    => -2,
            'errorItem'    => 'field',
            'errorMessage' => 'Invalid Request',
        ])));

        $this->expectException(\Checkdomain\Comodo\Model\Exception\ArgumentException::class);
        $util->autoApplySSL([]);
    }

    /**
     * @expectedException \Checkdomain\Comodo\Model\Exception\AccountException
     */
    public function testAccountException()
    {
        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode'    => -15,
            'errorMessage' => 'Invalid Request',
        ])));

        $this->expectException(\Checkdomain\Comodo\Model\Exception\AccountException::class);
        $util->autoApplySSL([]);
    }

    /**
     * @expectedException \Checkdomain\Comodo\Model\Exception\CsrException
     */
    public function testCsrException()
    {
        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode'    => -5,
            'errorMessage' => 'Invalid Request',
        ])));

        $this->expectException(\Checkdomain\Comodo\Model\Exception\CsrException::class);
        $util->autoApplySSL([]);
    }

    /**
     * @expectedException \Checkdomain\Comodo\Model\Exception\UnknownApiException
     */
    public function testUnknownApiException()
    {
        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode'    => -14,
            'errorMessage' => 'Invalid Request',
        ])));

        $this->expectException(\Checkdomain\Comodo\Model\Exception\UnknownApiException::class);
        $util->autoApplySSL([]);
    }

    /**
     * @expectedException \Checkdomain\Comodo\Model\Exception\UnknownException
     */
    public function testUnknownException()
    {
        $util = $this->createUtil($this->createGuzzleClient('Internal Server Error'));

        $this->expectException(\Checkdomain\Comodo\Model\Exception\UnknownException::class);
        $util->autoApplySSL([]);
    }

    /**
     * test, for getting status of certificate
     */
    public function testCollectSslStatus()
    {
        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode'         => 1,
            'orderNumber'       => 12345678,
            'certificateStatus' => 'Issued',
        ])));

        $result = $util->collectSsl(['showExtStatus' => 'Y']);

        $this->assertEquals('Issued', $result->getCertificateStatus());
    }


    /**
     * test, for getting status of certificate
     */
    public function testUpdateUserEvClickThrough()
    {
        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode' => 0,
            'status'    => 1,
        ])));

        $result = $util->updateUserEvClickThrough([]);

        $this->assertEquals(1, $result->getStatus());
    }

    /**
     * test, for getting period of certificate
     */
    public function testCollectSslPeriod()
    {
        $caCertificate = ['-----BEGIN CERTIFICATE-----'.chr(10).'123'.chr(10).'-----END CERTIFICATE-----'];
        $certificate = '-----BEGIN CERTIFICATE-----'.chr(10).'234'.chr(10). '-----END CERTIFICATE-----';

        $util = $this->createUtil($this->createGuzzleClient(http_build_query([
            'errorCode'         => 1,
            'orderNumber'       => 12345678,
            'notBefore'         => 1388576001,
            'notAfter'          => 1420112001,
            'csrStatus'         => 4,
            'certificateStatus' => 3,
            'validationStatus'  => 1,
            'certificate'       => 234,
            'caCertificate'     => 123,
            'ovCallBackStatus'  => 2,
        ])));

        $result = $util->collectSsl([]);

        $this->assertEquals($caCertificate, $result->getCaCertificate());
        $this->assertEquals($certificate, $result->getCertificate());
        $this->assertEquals('1', $result->getValidationStatus());
        $this->assertEquals('2', $result->getOvCallBackStatus());
        $this->assertEquals('3', $result->getCertificateStatus());
        $this->assertEquals('4', $result->getCsrStatus());
        $this->assertEquals('12345678', $result->getOrderNumber());
        $this->assertEquals('01.01.2014', $result->getNotBefore()->format('d.m.Y'));
        $this->assertEquals('01.01.2015', $result->getNotAfter()->format('d.m.Y'));
    }
}
